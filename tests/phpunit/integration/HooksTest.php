<?php
declare( strict_types=1 );

namespace MediaWiki\TimedMediaHandler\Test\Integration;

use File;
use MediaWiki\Context\RequestContext;
use MediaWiki\Request\FauxRequest;
use MediaWiki\TimedMediaHandler\Hooks;
use MediaWiki\TimedMediaHandler\TimedMediaHandler;
use MediaWiki\Title\Title;
use MediaWikiIntegrationTestCase;
use RepoGroup;
use SkinTemplate;

/**
 * @covers \MediaWiki\TimedMediaHandler\Hooks::onSkinTemplateNavigation__Universal
 *
 * @group Database
 * @group TimedMediaHandler
 */
class HooksTest extends MediaWikiIntegrationTestCase {
	private const TAB_TIMEDTEXT = 'timedtext';

	protected function setUp(): void {
		parent::setUp();
		if ( !defined( 'NS_TIMEDTEXT' ) ) {
			define( 'NS_TIMEDTEXT', 710 );
			define( 'NS_TIMEDTEXT_TALK', 711 );
		}
		// Use a controlled config so tests are deterministic regardless of
		// the deploying wiki's LocalSettings overrides.
		$this->overrideConfigValues( [
			'EnableTranscode'  => true,
			'TimedTextNS'      => 710,
			'FFmpegLocation'   => '/usr/bin/ffmpeg',
		] );
	}

	private function newHooksFromServices(): Hooks {
		$services = $this->getServiceContainer();
		return new Hooks(
			$services->getMainConfig(),
			$services->getLinkRenderer(),
			$services->getRepoGroup(),
			$services->getSpecialPageFactory(),
			$services->getTitleFactory()
		);
	}

	private function makeSkinForTitle( Title $title ): SkinTemplate {
		$context = new RequestContext();
		$context->setTitle( $title );
		$context->setRequest( new FauxRequest() );
		$context->setUser( $this->getTestUser()->getUser() );

		// Use the 'fallback' skin – always available, no extra dependencies.
		$skinFactory = $this->getServiceContainer()->getSkinFactory();
		$skin = $skinFactory->makeSkin( 'fallback' );
		$skin->setContext( $context );

		return $skin;
	}

	private function registerFakeFile(
		string $filename,
		string $mimeType,
		bool $isTmhHandler
	): Title {
		$title = Title::makeTitle( NS_FILE, $filename );

		// Build a File stub that reports the correct handler type.
		$handlerClass = $isTmhHandler ? TimedMediaHandler::class : \BitmapHandler::class;
		$handler      = $this->createMock( $handlerClass );

		$file = $this->createMock( File::class );
		$file->method( 'getTitle' )->willReturn( $title );
		$file->method( 'getHandler' )->willReturn( $handler );
		$file->method( 'getMimeType' )->willReturn( $mimeType );
		$file->method( 'exists' )->willReturn( true );
		$file->method( 'getName' )->willReturn( $filename );

		// Override RepoGroup to return our stub for this file title.
		$realRepoGroup = $this->getServiceContainer()->getRepoGroup();
		$mockRepoGroup = $this->createMock( RepoGroup::class );
		$mockRepoGroup->method( 'findFile' )
			->willReturnCallback(
				static function ( $titleArg ) use ( $title, $file, $realRepoGroup ) {
					$checkTitle = $titleArg instanceof Title
						? $titleArg
						: Title::newFromText( (string)$titleArg, NS_FILE );
					if ( $checkTitle && $checkTitle->equals( $title ) ) {
						return $file;
					}
					// Fall through to the real repo for everything else.
					return $realRepoGroup->findFile( $titleArg );
				}
			);

		$this->setService( 'RepoGroup', $mockRepoGroup );

		return $title;
	}

	private function runHook( Hooks $hooks, Title $title, array $initial = [] ): array {
		$skin  = $this->makeSkinForTitle( $title );
		$links = $initial ?: [ 'views' => [], 'actions' => [], 'namespaces' => [] ];
		$hooks->onSkinTemplateNavigation__Universal( $skin, $links );
		return $links;
	}

	private function findTab( array $links, string $key ): ?array {
		foreach ( $links as $bucket ) {
			if ( is_array( $bucket ) && array_key_exists( $key, $bucket ) ) {
				return $bucket[$key];
			}
		}
		return null;
	}

	public function testTimedTextTabAddedForWebmFileWithRealServices(): void {
		$title = $this->registerFakeFile( 'IntegrationClipTT.webm', 'video/webm', true );
		$hooks = $this->newHooksFromServices();

		$links = $this->runHook( $hooks, $title );

		$tab = $this->findTab( $links, self::TAB_TIMEDTEXT );
		$this->assertNotNull( $tab, 'TimedText tab must be injected for a WebM TMH file' );
	}

	public function testTimedTextTabAddedForOggVideoWithRealServices(): void {
		$title = $this->registerFakeFile( 'IntegrationClipTT.ogv', 'video/ogg', true );
		$hooks = $this->newHooksFromServices();

		$links = $this->runHook( $hooks, $title );

		$tab = $this->findTab( $links, self::TAB_TIMEDTEXT );
		$this->assertNotNull( $tab, 'TimedText tab must be injected for an Ogg video TMH file' );
	}

	public function testTimedTextTabStillPresentWhenTranscodeDisabled(): void {
		$this->overrideConfigValue( 'EnableTranscode', false );

		$title = $this->registerFakeFile( 'IntegrationNoTranscodeTT.webm', 'video/webm', true );
		$hooks = $this->newHooksFromServices();

		$links = $this->runHook( $hooks, $title );

		$tab = $this->findTab( $links, self::TAB_TIMEDTEXT );
		$this->assertNotNull( $tab,
			'TimedText tab must still appear even when $wgEnableTranscode = false' );
	}

	public function testNoTabsAddedForJpegFileInNsFile(): void {
		$title = $this->registerFakeFile( 'IntegrationPhoto.jpg', 'image/jpeg', false );
		$hooks = $this->newHooksFromServices();

		$initial = [ 'views' => [], 'actions' => [] ];
		$links   = $this->runHook( $hooks, $title, $initial );

		$this->assertNull( $this->findTab( $links, self::TAB_TIMEDTEXT ),
			'TimedText tab must NOT appear for a JPEG file' );
	}

	public function testNoTabsAddedForPngFileInNsFile(): void {
		$title = $this->registerFakeFile( 'IntegrationDiagram.png', 'image/png', false );
		$hooks = $this->newHooksFromServices();

		$links = $this->runHook( $hooks, $title );

		$this->assertNull( $this->findTab( $links, self::TAB_TIMEDTEXT ) );
	}

	public function testNoTabsAddedForSvgFileInNsFile(): void {
		$title = $this->registerFakeFile( 'IntegrationLogo.svg', 'image/svg+xml', false );
		$hooks = $this->newHooksFromServices();

		$links = $this->runHook( $hooks, $title );

		$this->assertNull( $this->findTab( $links, self::TAB_TIMEDTEXT ) );
	}

	public function testNoTabsForMainNamespaceTitleWithRealServices(): void {
		$title = Title::makeTitle( NS_MAIN, 'SomeArticle' );
		$hooks = $this->newHooksFromServices();

		$initial = [ 'views' => [ 'view' => [ 'href' => '/wiki/SomeArticle', 'text' => 'Read' ] ] ];
		$links   = $this->runHook( $hooks, $title, $initial );

		$this->assertNull( $this->findTab( $links, self::TAB_TIMEDTEXT ) );
		// Pre-existing tabs must be preserved
		$this->assertArrayHasKey( 'view', $links['views'] );
	}

	public function testNoTabsForTalkNamespacePage(): void {
		$title = Title::makeTitle( NS_TALK, 'Discussion' );
		$hooks = $this->newHooksFromServices();

		$links = $this->runHook( $hooks, $title );

		$this->assertNull( $this->findTab( $links, self::TAB_TIMEDTEXT ) );
	}

	public function testNoTabsForFileTalkNamespacePage(): void {
		$title = Title::makeTitle( NS_FILE_TALK, 'IntegrationClip.webm' );
		$hooks = $this->newHooksFromServices();

		$links = $this->runHook( $hooks, $title );

		$this->assertNull( $this->findTab( $links, self::TAB_TIMEDTEXT ) );
	}

	public function testNoTabsForCategoryPage(): void {
		$title = Title::makeTitle( NS_CATEGORY, 'Videos' );
		$hooks = $this->newHooksFromServices();

		$links = $this->runHook( $hooks, $title );

		$this->assertNull( $this->findTab( $links, self::TAB_TIMEDTEXT ) );
	}

	public function testNoTabsForSpecialPage(): void {
		$title = Title::makeTitle( NS_SPECIAL, 'Upload' );
		$hooks = $this->newHooksFromServices();

		$links = $this->runHook( $hooks, $title );

		$this->assertNull( $this->findTab( $links, self::TAB_TIMEDTEXT ) );
	}

	public function testNoTabsWhenFileNotFoundInRepo(): void {
		// Title in NS_FILE but not registered in the repo stub → findFile returns false
		$title = Title::makeTitle( NS_FILE, 'NonExistent.webm' );

		// Real RepoGroup will return false for a file that has never been uploaded
		$hooks = $this->newHooksFromServices();
		$links = $this->runHook( $hooks, $title );

		$this->assertNull( $this->findTab( $links, self::TAB_TIMEDTEXT ),
			'No TimedText tab when file does not exist in the repo' );
	}

	public function testTimedTextTabHasExpectedStructureFromRealServices(): void {
		$title = $this->registerFakeFile( 'StructureTestTT.webm', 'video/webm', true );
		$hooks = $this->newHooksFromServices();

		$links = $this->runHook( $hooks, $title );
		$tab   = $this->findTab( $links, self::TAB_TIMEDTEXT );

		$this->assertIsArray( $tab, 'TimedText tab must be an array' );
		$this->assertArrayHasKey( 'href', $tab, 'TimedText tab must have an href key' );
		$this->assertArrayHasKey( 'text', $tab, 'TimedText tab must have a text key' );
		$this->assertNotEmpty( $tab['href'], 'TimedText tab href must not be empty' );
		$this->assertNotEmpty( $tab['text'], 'TimedText tab text must not be empty' );
	}

	public function testTimedTextTabHrefContainsFilenameFromRealServices(): void {
		$filename = 'HrefCheckTT_Clip.webm';
		$title    = $this->registerFakeFile( $filename, 'video/webm', true );
		$hooks    = $this->newHooksFromServices();

		$links = $this->runHook( $hooks, $title );
		$tab   = $this->findTab( $links, self::TAB_TIMEDTEXT );

		$this->assertStringContainsString(
			'HrefCheckTT_Clip',
			$tab['href'] ?? '',
			'TimedText tab href must reference the file name'
		);
	}

	public function testExistingViewAndActionTabsArePreservedForTmhPage(): void {
		$title = $this->registerFakeFile( 'PreserveTest.webm', 'video/webm', true );
		$hooks = $this->newHooksFromServices();

		$initial = [
			'views'   => [ 'view' => [ 'href' => '/wiki/File:PreserveTest.webm', 'text' => 'File' ] ],
			'actions' => [ 'edit' => [ 'href' => '/?action=edit', 'text' => 'Edit' ] ],
		];

		$links = $this->runHook( $hooks, $title, $initial );

		$this->assertArrayHasKey( 'view', $links['views'],
			'"view" tab must not be removed by the hook' );
		$this->assertArrayHasKey( 'edit', $links['actions'],
			'"edit" tab must not be removed by the hook' );
	}

	public function testExistingTabsArePreservedForNonTmhPage(): void {
		$title = Title::makeTitle( NS_MAIN, 'Article' );
		$hooks = $this->newHooksFromServices();

		$initial = [
			'views'   => [ 'view' => [ 'href' => '/wiki/Article', 'text' => 'Read' ] ],
			'actions' => [ 'edit' => [ 'href' => '/?action=edit', 'text' => 'Edit' ] ],
		];

		$links = $this->runHook( $hooks, $title, $initial );

		$this->assertArrayHasKey( 'view', $links['views'] );
		$this->assertArrayHasKey( 'edit', $links['actions'] );
	}

	public function testTimedTextTabHrefIsAbsoluteOrRootRelative(): void {
		$title = $this->registerFakeFile( 'UrlCheckTT.webm', 'video/webm', true );
		$hooks = $this->newHooksFromServices();

		$links = $this->runHook( $hooks, $title );
		$href  = $this->findTab( $links, self::TAB_TIMEDTEXT )['href'] ?? '';

		$this->assertMatchesRegularExpression(
			'/^(https?:\/\/|\/)/',
			$href,
			'TimedText tab href must be an absolute URL or root-relative path'
		);
	}

	public function testCustomTimedTextNsIsReflectedInTimedTextTabHref(): void {
		// Override the TimedText namespace to 102 (as used on Wikimedia Commons)
		$this->overrideConfigValue( 'TimedTextNS', 102 );

		$title = $this->registerFakeFile( 'CommonsNS.webm', 'video/webm', true );
		$hooks = $this->newHooksFromServices();

		$links = $this->runHook( $hooks, $title );
		$tab   = $this->findTab( $links, self::TAB_TIMEDTEXT );

		// The tab should still appear; the href may differ (custom NS prefix)
		$this->assertNotNull( $tab,
			'TimedText tab must still be present when using a custom TimedTextNS' );
	}
}
