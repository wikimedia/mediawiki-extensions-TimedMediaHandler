<?php

use MediaWiki\Config\Config;
use MediaWiki\FileRepo\RepoGroup;
use MediaWiki\JobQueue\JobQueueGroup;
use MediaWiki\Page\Event\PageDeletedEvent;
use MediaWiki\Page\Event\PageLatestRevisionChangedEvent;
use MediaWiki\Page\Event\PageMovedEvent;
use MediaWiki\Page\ExistingPageRecord;
use MediaWiki\Revision\RevisionRecord;
use MediaWiki\Storage\EditResult;
use MediaWiki\Storage\RevisionSlotsUpdate;
use MediaWiki\TimedMediaHandler\MediaWikiEventIngress\PageEventIngress;
use MediaWiki\TimedMediaHandler\TranscodableChecker;
use MediaWiki\User\UserIdentityValue;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @covers \MediaWiki\TimedMediaHandler\MediaWikiEventIngress\PageEventIngress
 */
class PageIngressTest extends MediaWikiIntegrationTestCase {

	private function getPageEventIngress( array $mockedMethods = [] ): PageEventIngress&MockObject {
		return $this->getMockBuilder( PageEventIngress::class )
			->setConstructorArgs( [
				$this->createMock( Config::class ),
				$this->createMock( JobQueueGroup::class ),
				$this->createMock( RepoGroup::class ),
				$this->createMock( TranscodableChecker::class ),
			] )
			->onlyMethods( $mockedMethods )
			->getMock();
	}

	public function testDeletedPage() {
		$pageRecordBefore = $this->createMock( ExistingPageRecord::class );
		$pageRecordBefore->method( 'exists' )->willReturn( true );
		$latestRevisionBefore = $this->createMock( RevisionRecord::class );
		$user = new UserIdentityValue( 0, "User" );
		$event = new PageDeletedEvent(
			$pageRecordBefore,
			$latestRevisionBefore,
			$user,
			[], [], "", "", 1
		);
		$pageEventIngress = $this->getPageEventIngress( [ 'purgeDependingPages' ] );
		$pageEventIngress->expects( $this->once() )
			->method( 'purgeDependingPages' )
			->with( $pageRecordBefore, $user );
		$pageEventIngress->handlePageDeletedEvent( $event );
	}

	public function testMovedPage() {
		$pageRecordBefore = $this->createMock( ExistingPageRecord::class );
		$pageRecordBefore->method( 'exists' )->willReturn( true );
		$pageRecordAfter = $this->createMock( ExistingPageRecord::class );
		$pageRecordAfter->method( 'exists' )->willReturn( true );
		$user = new UserIdentityValue( 0, "User" );
		$event = new PageMovedEvent(
			$pageRecordBefore,
			$pageRecordAfter,
			$user,
			""
		);
		$pageRecordBefore->method( 'exists' )->willReturn( true );
		$pageEventIngress = $this->getPageEventIngress( [ 'purgeDependingPages' ] );
		$pageEventIngress->expects( $this->exactly( 2 ) )
			->method( 'purgeDependingPages' )
			->withConsecutive(
				[ $pageRecordBefore, $user ],
				[ $pageRecordAfter, $user ],
			);
		$pageEventIngress->handlePageMovedEvent( $event );
	}

	public function testLatestRevisionChangedPageIsCreation() {
		$pageRecordAfter = $this->createMock( ExistingPageRecord::class );
		$pageRecordAfter->method( 'exists' )->willReturn( true );
		$pageRecordAfter->method( 'isSamePageAs' )->willReturn( true );
		$latestRevisionAfter = $this->createMock( RevisionRecord::class );
		$slotsUpdate = $this->createMock( RevisionSlotsUpdate::class );
		$editResult = $this->createMock( EditResult::class );
		$user = new UserIdentityValue( 0, "User" );
		$event = new PageLatestRevisionChangedEvent(
			"",
			null,
			$pageRecordAfter,
			null,
			$latestRevisionAfter,
			$slotsUpdate,
			$editResult,
			$user
		);
		$pageEventIngress = $this->getPageEventIngress( [ 'purgeDependingPages' ] );
		$pageEventIngress->expects( $this->once() )
			->method( 'purgeDependingPages' )
			->with( $pageRecordAfter, $user );
		$pageEventIngress->handlePageLatestRevisionChangedEvent( $event );
	}

	public function testLatestRevisionChangedPageIsNotCreation() {
		$pageRecordBefore = $this->createMock( ExistingPageRecord::class );
		$pageRecordBefore->method( 'exists' )->willReturn( true );
		$pageRecordBefore->method( 'isSamePageAs' )->willReturn( true );
		$pageRecordAfter = $this->createMock( ExistingPageRecord::class );
		$pageRecordAfter->method( 'exists' )->willReturn( true );
		$pageRecordAfter->method( 'isSamePageAs' )->willReturn( true );
		$latestRevisionBefore = $this->createMock( RevisionRecord::class );
		$latestRevisionAfter = $this->createMock( RevisionRecord::class );
		$slotsUpdate = $this->createMock( RevisionSlotsUpdate::class );
		$editResult = $this->createMock( EditResult::class );
		$user = new UserIdentityValue( 0, "User" );
		$event = new PageLatestRevisionChangedEvent(
			"",
			$pageRecordBefore,
			$pageRecordAfter,
			$latestRevisionBefore,
			$latestRevisionAfter,
			$slotsUpdate,
			$editResult,
			$user
		);
		$pageEventIngress = $this->getPageEventIngress( [ 'purgeDependingPages' ] );
		$pageEventIngress->expects( $this->never() )
			->method( 'purgeDependingPages' );
		$pageEventIngress->handlePageLatestRevisionChangedEvent( $event );
	}

	public function testPurgeDependingPagesWithoutCorrespondingFileTitle() {
		$pageRecordBefore = $this->createMock( ExistingPageRecord::class );
		$pageRecordBefore->method( 'exists' )->willReturn( true );
		$user = new UserIdentityValue( 0, "User" );
		$pageEventIngressReflector = new ReflectionClass( PageEventIngress::class );
		$purgeDependingPages = $pageEventIngressReflector->getMethod( 'purgeDependingPages' );
		$jobQueueGroup = $this->createMock( JobQueueGroup::class );
		$jobQueueGroup->expects( $this->never() )
			->method( 'lazyPush' );
		$config = $this->createMock( Config::class );
		$repoGroup = $this->createMock( RepoGroup::class );
		$purgeDependingPages->invoke(
			new PageEventIngress(
				$config, $jobQueueGroup, $repoGroup, $this->createMock( TranscodableChecker::class )
			),
			$pageRecordBefore, $user
		);
	}

	// TODO: testPurgeDependingPagesWithCorrespondingFileTitle
}
