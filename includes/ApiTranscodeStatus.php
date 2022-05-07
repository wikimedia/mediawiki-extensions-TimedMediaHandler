<?php
/**
 * Allows for api queries to get detailed information about the transcode state of a particular
 * media asset. ( basically directly returns the transcode status table )
 *
 * This information can be used to generate status tables similar to the one seen
 * on the image page.
 */

namespace MediaWiki\TimedMediaHandler;

use ApiQueryBase;
use File;
use MediaWiki\MediaWikiServices;
use MediaWiki\TimedMediaHandler\WebVideoTranscode\WebVideoTranscode;

class ApiTranscodeStatus extends ApiQueryBase {
	public function execute() {
		$pageIds = $this->getPageSet()->getAllTitlesByNamespace();
		// Make sure we have files in the title set:
		if ( !empty( $pageIds[NS_FILE] ) ) {
			$titles = array_keys( $pageIds[NS_FILE] );
			// Ensure the order is always the same
			asort( $titles );

			$result = $this->getResult();
			$images = MediaWikiServices::getInstance()->getRepoGroup()->findFiles( $titles );
			/**
			 * @var $img File
			 */
			foreach ( $images as $img ) {
				// if its a "transcode" add the transcode status table output
				if ( Hooks::isTranscodableTitle( $img->getTitle() ) ) {
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
