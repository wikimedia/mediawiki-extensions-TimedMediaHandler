<?php

declare( strict_types=1 );

namespace MediaWiki\TimedMediaHandler\Test\Unit;

use File;
use MediaHandler;
use MediaWiki\Config\HashConfig;
use MediaWiki\Linker\LinkRenderer;
use MediaWiki\SpecialPage\SpecialPageFactory;
use MediaWiki\TimedMediaHandler\Hooks;
use MediaWiki\TimedMediaHandler\TimedMediaHandler;
use MediaWiki\Title\Title;
use MediaWiki\Title\TitleFactory;
use MediaWikiUnitTestCase;
use RepoGroup;
use SkinTemplate;

/**
 * @covers \MediaWiki\TimedMediaHandler\Hooks::onSkinTemplateNavigation__Universal
 * @group TimedMediaHandler
 */
class HooksTest extends MediaWikiUnitTestCase {
	private const TAB_TIMEDTEXT = 'timedtext';

	private function newHooks(
		RepoGroup $repoGroup,
		bool $enableTranscode = true,
		?SpecialPageFactory $spFactory = null
	): Hooks {
		if ( !defined( 'NS_TIMEDTEXT' ) ) {
			define( 'NS_TIMEDTEXT', 710 );
			define( 'NS_TIMEDTEXT_TALK', 711 );
		}
		$config = new HashConfig( [
			'EnableTranscode'    => $enableTranscode,
			'TimedTextNS'        => 710,
			'FFmpegLocation'     => '/usr/bin/ffmpeg',
			'TMHSupportedCodecs' => [],
		] );

		$linkRenderer = $this->createMock( LinkRenderer::class );

		if ( $spFactory === null ) {
			$spFactory = $this->makeSpecialPageFactory();
		}

		$titleFactory = $this->createMock( TitleFactory::class );
		$titleFactory
			->method( 'makeTitleSafe' )
			->willReturnCallback( static function ( $ns, $title ) {
				return Title::makeTitle( $ns, $title );
			} );

		return new Hooks( $config, $linkRenderer, $repoGroup, $spFactory, $titleFactory );
	}

	private function makeSpecialPageFactory(): SpecialPageFactory {
		$spFactory = $this->createMock( SpecialPageFactory::class );
		$spFactory->method( 'getTitleForAlias' )
			->willReturnCallback( function ( string $alias ) {
				$t = $this->createMock( Title::class );
				$t->method( 'getLocalURL' )->willReturn( "/wiki/Special:$alias" );
				$t->method( 'getLinkURL' )->willReturn( "/wiki/Special:$alias" );
				$t->method( 'getFullURL' )->willReturn( "https://example.org/wiki/Special:$alias" );
				return $t;
			} );
		return $spFactory;
	}

	private function makeFileTitle( string $text = 'Example.webm' ): Title {
		$title = $this->createMock( Title::class );
		$title->method( 'getNamespace' )->willReturn( NS_FILE );
		$title->method( 'inNamespace' )->with( NS_FILE )->willReturn( true );
		$title->method( 'getText' )->willReturn( $text );
		$title->method( 'getDBkey' )->willReturn( str_replace( ' ', '_', $text ) );
		$title->method( 'getPrefixedText' )->willReturn( "File:$text" );
		$title->method( 'getLocalURL' )->willReturn( '/wiki/File:' . urlencode( $text ) );
		return $title;
	}

	private function makeNonFileTitle( int $ns = NS_MAIN, string $text = 'Article' ): Title {
		$title = $this->createMock( Title::class );
		$title->method( 'getNamespace' )->willReturn( $ns );
		$title->method( 'inNamespace' )->with( NS_FILE )->willReturn( false );
		$title->method( 'getText' )->willReturn( $text );
		$title->method( 'getDBkey' )->willReturn( str_replace( ' ', '_', $text ) );
		return $title;
	}

	private function makeSkin( Title $title ): SkinTemplate {
		$skin = $this->createMock( SkinTemplate::class );
		$skin->method( 'getTitle' )->willReturn( $title );
		return $skin;
	}

	private function makeTmhFile( string $mime = 'video/webm' ): File {
		$handler = $this->createMock( TimedMediaHandler::class );
		$file = $this->createMock( File::class );
		$file->method( 'getHandler' )->willReturn( $handler );
		$file->method( 'getMimeType' )->willReturn( $mime );
		$file->method( 'exists' )->willReturn( true );
		return $file;
	}

	private function makeNonTmhFile( string $mime = 'image/jpeg' ): File {
		$handler = $this->createMock( MediaHandler::class );
		$file = $this->createMock( File::class );
		$file->method( 'getHandler' )->willReturn( $handler );
		$file->method( 'getMimeType' )->willReturn( $mime );
		$file->method( 'exists' )->willReturn( true );
		return $file;
	}

	private function makeRepoGroupWith( $file ): RepoGroup {
		$rg = $this->createMock( RepoGroup::class );
		$rg->method( 'findFile' )->willReturn( $file );
		return $rg;
	}

	private function invokeHook(
		Hooks $hooks,
		Title $title,
		array $initialLinks = []
	): array {
		$skin  = $this->makeSkin( $title );
		$links = $initialLinks ?: [ 'views' => [], 'actions' => [] ];
		$hooks->onSkinTemplateNavigation__Universal( $skin, $links );
		return $links;
	}

	public function testNoTabsAddedForMainNamespacePage(): void {
		$title   = $this->makeNonFileTitle( NS_MAIN, 'SomeArticle' );
		$repoGrp = $this->createMock( RepoGroup::class );
		$repoGrp->expects( $this->never() )->method( 'findFile' );
		$hooks = $this->newHooks( $repoGrp );

		$before = [ 'views' => [], 'actions' => [] ];
		$links  = $this->invokeHook( $hooks, $title, $before );

		$this->assertSame( $before, $links, 'Non-File page must leave $links untouched' );
	}

	public function testNoTabsAddedForFileTalkNamespacePage(): void {
		$title = $this->makeNonFileTitle( NS_FILE_TALK, 'Example.webm' );
		$hooks = $this->newHooks( $this->createMock( RepoGroup::class ) );

		$before = [ 'views' => [], 'actions' => [] ];
		$links  = $this->invokeHook( $hooks, $title, $before );

		$this->assertSame( $before, $links );
	}

	public function testNoTabsAddedForImageFileWithNonTmhHandler(): void {
		$title = $this->makeFileTitle( 'Photo.jpg' );
		$hooks = $this->newHooks( $this->makeRepoGroupWith( $this->makeNonTmhFile( 'image/jpeg' ) ) );

		$before = [ 'views' => [], 'actions' => [] ];
		$links  = $this->invokeHook( $hooks, $title, $before );

		$this->assertTimedTextTabAbsent( $links );
	}

	public function testNoTabsAddedWhenFileNotFoundInRepo(): void {
		$title = $this->makeFileTitle( 'Missing.webm' );
		$hooks = $this->newHooks( $this->makeRepoGroupWith( false ) );

		$before = [ 'views' => [], 'actions' => [] ];
		$links  = $this->invokeHook( $hooks, $title, $before );

		$this->assertTimedTextTabAbsent( $links );
	}

	public function testExistingTabsArePreservedWhenTabsAdded(): void {
		$title = $this->makeFileTitle( 'Clip.webm' );
		$hooks = $this->newHooks( $this->makeRepoGroupWith( $this->makeTmhFile( 'video/webm' ) ) );

		$initial = [
			'views'   => [ 'view' => [ 'href' => '/wiki/File:Clip.webm', 'text' => 'Read' ] ],
			'actions' => [ 'edit' => [ 'href' => '/w/index.php?action=edit', 'text' => 'Edit' ] ],
		];

		$links = $this->invokeHook( $hooks, $title, $initial );

		$this->assertArrayHasKey( 'view', $links['views'], '"view" tab must be preserved' );
		$this->assertArrayHasKey( 'edit', $links['actions'], '"edit" tab must be preserved' );
	}

	public function testHookDoesNotClearExistingTabsForNonTmhPage(): void {
		$title = $this->makeNonFileTitle( NS_MAIN, 'Article' );
		$hooks = $this->newHooks( $this->createMock( RepoGroup::class ) );

		$initial = [
			'views'   => [ 'view' => [ 'href' => '/wiki/Article', 'text' => 'Read' ] ],
			'actions' => [ 'edit' => [ 'href' => '/?action=edit', 'text' => 'Edit' ] ],
		];

		$links = $this->invokeHook( $hooks, $title, $initial );

		$this->assertArrayHasKey( 'view', $links['views'] );
		$this->assertArrayHasKey( 'edit', $links['actions'] );
	}

	private function findTab( array $links, string $key ): ?array {
		foreach ( $links as $bucket ) {
			if ( is_array( $bucket ) && isset( $bucket[$key] ) ) {
				return $bucket[$key];
			}
		}
		return null;
	}

	private function assertTimedTextTabAbsent( array $links ): void {
		$tab = $this->findTab( $links, self::TAB_TIMEDTEXT );
		$this->assertNull(
			$tab,
			'Expected timed text tab to be absent from navigation links'
		);
	}
}
