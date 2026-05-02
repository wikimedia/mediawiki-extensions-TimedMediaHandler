<?php

namespace MediaWiki\TimedMediaHandler;

use MediaWiki\Hook\MediaWikiServicesHook;

/**
 * Registers the TimedText namespace with localized names early in the MediaWiki
 * bootstrap, before the CanonicalNamespaces hook fires. This mirrors the approach
 * used by the ProofreadPage extension (ProofreadPageInit).
 */
class TimedMediaHandlerInit implements MediaWikiServicesHook {

	/** @inheritDoc */
	public function onMediaWikiServices( $services ): void {
		$id = $services->getMainConfig()->get( 'TimedTextNS' );
		self::initNamespace( $id );
	}

	private static function initNamespace( int $id ): void {
		global $wgExtraNamespaces, $wgCanonicalNamespaceNames, $wgNamespaceAliases, $wgLanguageCode;
		global $wgTimedMediaHandlerNamespaceNames, $wgTimedMediaHandlerNamespaceAliases;

		if ( isset( $wgExtraNamespaces[$id] ) ) {
			return;
		}

		if ( !defined( 'NS_TIMEDTEXT' ) ) {
			define( 'NS_TIMEDTEXT', $id );
			define( 'NS_TIMEDTEXT_TALK', $id + 1 );
		}

		$wgExtraNamespaces[$id] = $wgTimedMediaHandlerNamespaceNames[$wgLanguageCode]['timedtext']
			?? $wgTimedMediaHandlerNamespaceNames['en']['timedtext'];
		$wgExtraNamespaces[$id + 1] = $wgTimedMediaHandlerNamespaceNames[$wgLanguageCode]['timedtext_talk']
			?? $wgTimedMediaHandlerNamespaceNames['en']['timedtext_talk'];

		// $wgCanonicalNamespaceNames is seeded before MediaWikiServices fires, so we
		// must update it directly here (same pattern as ProofreadPageInit)
		$wgCanonicalNamespaceNames[$id] = $wgExtraNamespaces[$id];
		$wgCanonicalNamespaceNames[$id + 1] = $wgExtraNamespaces[$id + 1];

		foreach ( $wgTimedMediaHandlerNamespaceAliases[$wgLanguageCode]['timedtext'] ?? [] as $alias ) {
			$wgNamespaceAliases[$alias] = $id;
		}
		foreach ( $wgTimedMediaHandlerNamespaceAliases[$wgLanguageCode]['timedtext_talk'] ?? [] as $alias ) {
			$wgNamespaceAliases[$alias] = $id + 1;
		}
	}
}
