<?php
/**
 * Implements Special:OrphanedTimedText
 *
 * @author Brian Wolff
 * @file
 * @ingroup SpecialPage
 */

namespace MediaWiki\TimedMediaHandler;

use HtmlArmor;
use MediaWiki\Html\Html;
use MediaWiki\Languages\LanguageConverterFactory;
use MediaWiki\Linker\Linker;
use MediaWiki\SpecialPage\PageQueryPage;
use MediaWiki\Title\Title;
use RepoGroup;
use Skin;
use stdClass;
use Wikimedia\Rdbms\IConnectionProvider;
use Wikimedia\Rdbms\IDatabase;
use Wikimedia\Rdbms\IResultWrapper;

/**
 * Lists TimedText pages that don't have a corresponding video.
 *
 * @ingroup SpecialPage
 */
class SpecialOrphanedTimedText extends PageQueryPage {

	/** @var array with keys being names of valid files */
	private $existingFiles;

	/** @var IConnectionProvider */
	private $dbProvider;

	/** @var LanguageConverterFactory */
	private $languageConverterFactory;

	/** @var RepoGroup */
	private $repoGroup;

	/**
	 * @param IConnectionProvider $dbProvider
	 * @param LanguageConverterFactory $languageConverterFactory
	 * @param RepoGroup $repoGroup
	 */
	public function __construct(
		IConnectionProvider $dbProvider,
		LanguageConverterFactory $languageConverterFactory,
		RepoGroup $repoGroup
	) {
		parent::__construct( 'OrphanedTimedText' );
		$this->dbProvider = $dbProvider;
		$this->languageConverterFactory = $languageConverterFactory;
		$this->repoGroup = $repoGroup;
	}

	/**
	 * This is alphabetical, so sort ascending.
	 * @return bool
	 */
	public function sortDescending() {
		return false;
	}

	/**
	 * Should this be cached?
	 *
	 * This query is actually almost cheap given the current
	 * number of things in TimedText namespace.
	 * @return bool
	 */
	public function isExpensive() {
		return $this->canExecute();
	}

	/**
	 * Main execution function
	 *
	 * @param string $par subpage
	 */
	public function execute( $par ) {
		$this->addHelpLink( 'https://commons.wikimedia.org/wiki/Commons:Timed_Text', true );

		if ( !$this->canExecuteQuery() ) {
			$this->setHeaders();
			$this->outputHeader();
			$this->getOutput()->addWikiMsg( 'orphanedtimedtext-unsupported' );
			return;
		}
		parent::execute( $par );
	}

	/**
	 * Can we cache the results of this query?
	 *
	 * Only if we support the query.
	 * @return bool
	 */
	public function isCacheable() {
		return $this->canExecute();
	}

	/**
	 * List in Special:SpecialPages?
	 *
	 * @return bool
	 */
	public function isListed() {
		return $this->canExecute();
	}

	/**
	 * Can we execute this special page?
	 *
	 * The query uses a mysql specific feature (substring_index), so disable on non mysql dbs.
	 *
	 * @return bool
	 */
	private function canExecuteQuery() {
		$dbr = $this->dbProvider->getReplicaDatabase();
		return $dbr->getType() === 'mysql';
	}

	/**
	 * Can we execute this special page
	 *
	 * That is, db is mysql, and TimedText namespace enabled.
	 * @return bool
	 */
	private function canExecute(): bool {
		return $this->canExecuteQuery();
	}

	/**
	 * Get query info
	 *
	 * The query here is meant to retrieve all pages in the TimedText namespace,
	 * such that if you strip the last two extensions (e.g. Foo.bar.baz.en.srt -> Foo.bar.baz)
	 * there is no corresponding img_name in image table. So if there is a page in TimedText
	 * namespace named TimedText:My.Dog.webm.ceb.srt, it will include it in the list provided
	 * that File:My.Dog.webm is not uploaded.
	 *
	 * TimedText does not support file redirects or foreign files, so we don't have
	 * to worry about those.
	 *
	 * Potentially this should maybe also include pages not ending in
	 * .<valid lang code>.srt . However, determining what a valid lang code
	 * is, is pretty hard (although perhaps it could check if its [a-z]{2,3}
	 * however then we've got things like roa-tara, cbk-zam, etc)
	 * and TimedText throws away the final .srt extension and will work with
	 * any extension, so things not ending in .srt arguably aren't oprhaned.
	 *
	 * @note This uses "substring_index" which is a mysql extension.
	 * @return array Standard query info values.
	 */
	public function getQueryInfo() {
		$tables = [ 'page', 'image' ];
		$fields = [
			'namespace' => 'page_namespace',
			'title' => 'page_title',
			'value' => 0,
		];
		$conds = [
			'img_name' => null,
			'page_namespace' => $this->getConfig()->get( 'TimedTextNS' ),
			'page_is_redirect' => 0,
		];

		// Now for the complicated bit
		// Note: This bit is mysql specific. Probably could do something
		// equivalent in postgres via split_part or regex substr,
		// but my sql-fu is not good enough to figure out how to do
		// this in standard sql, or in sqlite.
		$baseCond = 'substr( page_title, 1, length( page_title ) - '
			. "length( substring_index( page_title, '.' ,-2 ) ) - 1 )";
		$joinConds = [
			'image' => [
				'LEFT OUTER JOIN',
				$baseCond . ' = img_name'
			]
		];
		return [
			'tables' => $tables,
			'fields' => $fields,
			'conds' => $conds,
			'join_conds' => $joinConds
		];
	}

	/** @inheritDoc */
	public function getOrderFields() {
		return [ 'namespace', 'title' ];
	}

	/**
	 * Is the TimedText page really orphaned?
	 *
	 * Given a title like "TimedText:Some bit here.webm.en.srt"
	 * check to see if "File:Some bit here.webm" really exists (locally).
	 * @param Title $title
	 * @return bool True if we should cross out the line.
	 */
	protected function existenceCheck( Title $title ) {
		$fileTitle = $this->getCorrespondingFile( $title );
		if ( !$fileTitle ) {
			return !$title->isKnown();
		}
		return !$title->isKnown() ||
			( isset( $this->existingFiles[ $fileTitle->getDBKey() ] )
			&& $this->existingFiles[$fileTitle->getDBKey()]->getHandler()
			&& $this->existingFiles[$fileTitle->getDBKey()]->getHandler() instanceof TimedMediaHandler );
	}

	/**
	 * Given a TimedText title, get the File title
	 *
	 * @param Title $timedText
	 * @return Title|null Title in File namespace. null on error.
	 */
	private function getCorrespondingFile( Title $timedText ) {
		$titleParts = explode( '.', $timedText->getDBkey() );
		$baseParts = array_slice( $titleParts, 0, -2 );
		return Title::makeTitleSafe( NS_FILE, implode( '.', $baseParts ) );
	}

	/**
	 * What group to include this page in on Special:SpecialPages
	 * @return string
	 */
	protected function getGroupName() {
		return 'media';
	}

	/**
	 * Preprocess result to do existence checks all at once.
	 *
	 * @param IDatabase $db
	 * @param IResultWrapper $res
	 */
	public function preprocessResults( $db, $res ) {
		parent::preprocessResults( $db, $res );

		if ( !$res->numRows() ) {
			return;
		}

		$filesToLookFor = [];
		foreach ( $res as $row ) {
			$title = Title::makeTitle( $row->namespace, $row->title );
			$fileTitle = $this->getCorrespondingFile( $title );
			if ( !$fileTitle ) {
				continue;
			}
			$filesToLookFor[] = [ 'title' => $fileTitle, 'ignoreRedirect' => true ];
		}
		$this->existingFiles = $this->repoGroup->getLocalRepo()
			->findFiles( $filesToLookFor );
		$res->seek( 0 );
	}

	/**
	 * Format the result as a simple link to the page
	 *
	 * Based on parent class but with an existence check added.
	 *
	 * @param Skin $skin
	 * @param stdClass $row Result row
	 * @return string
	 */
	public function formatResult( $skin, $row ) {
		$title = Title::makeTitleSafe( $row->namespace, $row->title );

		if ( $title instanceof Title ) {
			$contLangConv = $this->languageConverterFactory->getLanguageConverter();
			$text = $contLangConv->convert(
				htmlspecialchars( $title->getPrefixedText() )
			);
			$link = $this->getLinkRenderer()->makeLink( $title, new HtmlArmor( $text ) );
			if ( $this->existenceCheck( $title ) ) {
				// File got uploaded since this page was cached
				$link = '<del>' . $link . '</del>';
			}
			return $link;
		}

		return Html::element( 'span', [ 'class' => 'mw-invalidtitle' ],
			Linker::getInvalidTitleDescription( $this->getContext(), $row->namespace, $row->title ) );
	}
}
