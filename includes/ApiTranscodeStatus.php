<?php
/**
 * Allows for api queries to get detailed information about the transcode state of a particular
 * media asset. ( basically directly returns the transcode status table )
 *
 * This information can be used to generate status tables similar to the one seen
 * on the image page.
 */

namespace MediaWiki\TimedMediaHandler;

use File;
use MediaWiki\Api\ApiQuery;
use MediaWiki\Api\ApiQueryBase;
use MediaWiki\TimedMediaHandler\WebVideoTranscode\WebVideoTranscode;
use RepoGroup;

class ApiTranscodeStatus extends ApiQueryBase {
	/** @var RepoGroup */
	private $repoGroup;

	/** @var TranscodableChecker */
	private $transcodableChecker;

	/**
	 * @param ApiQuery $queryModule
	 * @param string $moduleName
	 * @param RepoGroup $repoGroup
	 */
	public function __construct(
		ApiQuery $queryModule,
		$moduleName,
		RepoGroup $repoGroup
	) {
		parent::__construct( $queryModule, $moduleName );
		$this->repoGroup = $repoGroup;
		$this->transcodableChecker = new TranscodableChecker(
			$this->getConfig(),
			$repoGroup
		);
	}

	public function execute() {
		$pageIds = $this->getPageSet()->getAllTitlesByNamespace();
		// Make sure we have files in the title set:
		if ( !empty( $pageIds[NS_FILE] ) ) {
			$titles = array_keys( $pageIds[NS_FILE] );
			// Ensure the order is always the same
			asort( $titles );

			$result = $this->getResult();
			$images = $this->repoGroup->findFiles( $titles );
			/**
			 * @var $img File
			 */
			foreach ( $images as $img ) {
				// if its a "transcode" add the transcode status table output
				if ( $this->transcodableChecker->isTranscodableTitle( $img->getTitle() ) ) {
					$transcodeStatus = WebVideoTranscode::getTranscodeState( $img );
					// remove useless properties
					foreach ( $transcodeStatus as &$val ) {
						unset( $val['id'], $val['image_name'], $val['key'] );
					}
					unset( $val );
					$result->addValue( [
						'query', 'pages', $img->getTitle()->getArticleID() ], 'transcodestatus', $transcodeStatus
					);
				}
			}
		}
	}

	/** @inheritDoc */
	public function getCacheMode( $params ) {
		return 'public';
	}

	/** @inheritDoc */
	public function getAllowedParams() {
		return [];
	}

	/**
	 * @see ApiBase::getExamplesMessages()
	 * @return array
	 */
	protected function getExamplesMessages() {
		return [
			'action=query&prop=transcodestatus&titles=File:Clip.webm'
				=> 'apihelp-query+transcodestatus-example-1',
		];
	}
}
