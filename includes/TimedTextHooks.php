<?php

namespace MediaWiki\TimedMediaHandler;

use MediaWiki\Hook\PageMoveCompleteHook;
use MediaWiki\JobQueue\JobQueueGroup;
use MediaWiki\JobQueue\Jobs\HTMLCacheUpdateJob;
use MediaWiki\Linker\LinkTarget;
use MediaWiki\Logging\ManualLogEntry;
use MediaWiki\Page\Hook\PageDeleteCompleteHook;
use MediaWiki\Page\ProperPageIdentity;
use MediaWiki\Permissions\Authority;
use MediaWiki\Revision\RevisionRecord;
use MediaWiki\Storage\Hook\PageSaveCompleteHook;
use MediaWiki\Title\Title;
use MediaWiki\User\UserIdentity;

class TimedTextHooks implements
	PageDeleteCompleteHook,
	PageMoveCompleteHook,
	PageSaveCompleteHook
{

	public function __construct(
		private readonly JobQueueGroup $jobQueueGroup,
	) {
	}

	/** @inheritDoc */
	public function onPageDeleteComplete(
		ProperPageIdentity $page,
		Authority $deleter,
		string $reason,
		int $pageID,
		RevisionRecord $deletedRev,
		ManualLogEntry $logEntry,
		int $archivedRevisionCount
	) {
		$title = Title::newFromPageIdentity( $page );
		$this->purgeDependingPages( $title, $deleter->getUser() );
	}

	/** @inheritDoc */
	public function onPageMoveComplete( $old, $new, $user, $pageid, $redirid, $reason, $revision ) {
		$this->purgeDependingPages( $old, $user );
		$this->purgeDependingPages( $new, $user );
	}

	/** @inheritDoc */
	public function onPageSaveComplete( $wikiPage, $user, $summary, $flags, $revisionRecord, $editResult ) {
		if ( $flags & EDIT_NEW ) {
			$this->purgeDependingPages( $wikiPage->getTitle(), $user );
		}
	}

	/**
	 * When timedtext files are added or removed we need to update the cached html
	 * of all pages using those files.
	 *
	 * @param LinkTarget $timedTextTitle
	 * @param UserIdentity $user performing the action that led to cache invalidation being required
	 * @return void
	 */
	protected function purgeDependingPages( LinkTarget $timedTextTitle, UserIdentity $user ) {
		if ( $timedTextTitle->getNamespace() === NS_TIMEDTEXT ) {
			$timedTextPage = new TimedTextPage( Title::newFromLinkTarget( $timedTextTitle ) );
			$correspondingFileTitle = $timedTextPage->getCorrespondingFileTitle();

			if ( !$correspondingFileTitle ) {
				return;
			}

			// Invalidate cache for all pages using this file
			$cacheUpdateJob = HTMLCacheUpdateJob::newForBacklinks(
				$correspondingFileTitle,
				'imagelinks',
				[ 'causeAction' => 'tmh-timedtext', 'causeAgent' => $user->getName() ]
			);
			$this->jobQueueGroup->lazyPush( $cacheUpdateJob );

			// TODO handle GlobalUsage
		}
	}
}
