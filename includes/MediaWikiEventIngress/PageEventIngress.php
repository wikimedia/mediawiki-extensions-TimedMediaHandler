<?php

namespace MediaWiki\TimedMediaHandler\MediaWikiEventIngress;

use MediaWiki\Config\Config;
use MediaWiki\DomainEvent\DomainEventIngress;
use MediaWiki\FileRepo\RepoGroup;
use MediaWiki\JobQueue\JobQueueGroup;
use MediaWiki\JobQueue\Jobs\HTMLCacheUpdateJob;
use MediaWiki\Page\Event\PageDeletedEvent;
use MediaWiki\Page\Event\PageDeletedListener;
use MediaWiki\Page\Event\PageLatestRevisionChangedEvent;
use MediaWiki\Page\Event\PageLatestRevisionChangedListener;
use MediaWiki\Page\Event\PageMovedEvent;
use MediaWiki\Page\Event\PageMovedListener;
use MediaWiki\Page\PageIdentity;
use MediaWiki\TimedMediaHandler\TimedTextPage;
use MediaWiki\TimedMediaHandler\TranscodableChecker;
use MediaWiki\TimedMediaHandler\WebVideoTranscode\WebVideoTranscode;
use Mediawiki\Title\Title;
use MediaWiki\User\UserIdentity;

class PageEventIngress extends DomainEventIngress implements
	PageDeletedListener,
	PageMovedListener,
	PageLatestRevisionChangedListener
{

	public function __construct(
		private readonly Config $config,
		private readonly JobQueueGroup $jobQueueGroup,
		private readonly RepoGroup $repoGroup,
		private readonly TranscodableChecker $transcodableChecker
	) {
	}

	/** @inheritDoc */
	public function handlePageLatestRevisionChangedEvent( PageLatestRevisionChangedEvent $event ): void {
		if ( $event->isCreation() ) {
			$this->purgeDependingPages( $event->getPage(), $event->getPerformer() );
		}
	}

	/** @inheritDoc */
	public function handlePageDeletedEvent( PageDeletedEvent $event ): void {
		$this->purgeDependingPages( $event->getDeletedPage(), $event->getPerformer() );
	}

	/** @inheritDoc */
	public function handlePageMovedEvent( PageMovedEvent $event ): void {
		$pageRecordOld = $event->getPageRecordBefore();
		$pageRecordNew = $event->getPageRecordAfter();

		$this->purgeDependingPages( $pageRecordOld, $event->getPerformer() );
		$this->purgeDependingPages( $pageRecordNew, $event->getPerformer() );

		// Add transcode jobs for new file name
		if ( $this->transcodableChecker->isTranscodableTitle( $pageRecordNew ) ) {
			$newFile = $this->repoGroup->findFile(
				$pageRecordNew,
				[ 'ignoreRedirect' => true, 'latest' => true ]
			);
			WebVideoTranscode::startJobQueue( $newFile );
		}
	}

	/**
	 * When timedtext files are added or removed we need to update the cached html
	 * of all pages using those files.
	 *
	 * @param PageIdentity $timedTextTitle
	 * @param UserIdentity $user performing the action that led to cache invalidation being required
	 * @return void
	 */
	protected function purgeDependingPages( PageIdentity $timedTextTitle, UserIdentity $user ): void {
		$timedTextNS = $this->config->get( 'TimedTextNS' );
		if ( $timedTextTitle->getNamespace() === $timedTextNS ) {
			$timedTextPage = $this->createTimedTextPage( $timedTextTitle );
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

	/**
	 * Create and return instance of TimedTextPage
	 */
	protected function createTimedTextPage( PageIdentity $timedTextTitle ): TimedTextPage {
		return new TimedTextPage( Title::newFromPageIdentity( $timedTextTitle ) );
	}
}
