<?php
/**
 * Allows for api queries to get detailed information about the transcode state of a particular
 * media asset. ( basically directly returns the transcode status table )
 *
 * This information can be used to generate status tables similar to the one seen
 * on the image page.
 */

class ApiTranscodeStatus extends ApiQueryBase {
	public function execute() {
		$pageIds = $this->getPageSet()->getAllTitlesByNamespace();
		// Make sure we have files in the title set:
		if ( !empty( $pageIds[NS_FILE] ) ) {
			$titles = array_keys( $pageIds[NS_FILE] );
			asort( $titles ); // Ensure the order is always the same

			$result = $this->getResult();
			$images = RepoGroup::singleton()->findFiles( $titles );
			/**
			 * @var $img File
			 */
			foreach ( $images as $img ) {
				// if its a "transcode" add the transcode status table output
				if ( TimedMediaHandlerHooks::isTranscodableTitle( $img->getTitle() ) ) {
					$transcodeStatus = WebVideoTranscode::getTranscodeState( $img );
					// remove useless properties
					foreach ( $transcodeStatus as $key => &$val ) {
						unset( $val['id'] );
						unset( $val['image_name'] );
						unset( $val['key'] );
					}
					$result->addValue( [
						'query', 'pages', $img->getTitle()->getArticleID() ], 'transcodestatus', $transcodeStatus
					);
				}
			}
		}
	}

	public function getCacheMode( $params ) {
		return 'public';
	}

	public function getAllowedParams() {
		return [];
	}

	/**
	 * @deprecated since MediaWiki core 1.25
	 */
	public function getDescription() {
		return [
			'Get transcode status for a given file page'
		];
	}

	/**
	 * @deprecated since MediaWiki core 1.25
	 */
	protected function getExamples() {
		return [
			'api.php?action=query&prop=transcodestatus&titles=File:Clip.webm',
		];
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
