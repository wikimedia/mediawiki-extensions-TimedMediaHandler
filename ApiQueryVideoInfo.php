<?php
/**
 * Extends imageinfo with support for videoinfo sources property.
 *
 * Alternatively core ApiQueryImageInfo could support being extended in some straightforward ways.
 * see: https://www.mediawiki.org/wiki/User:Catrope/Extension_review/TimedMediaHandler#ApiQueryVideoInfo.php
 *
 */

class ApiQueryVideoInfo extends ApiQueryImageInfo {

	public function __construct( $query, $moduleName, $prefix = 'vi' ) {
		// We allow a subclass to override the prefix, to create a related API module.
		// Some other parts of MediaWiki construct this with a null $prefix,
		// which used to be ignored when this only took two arguments
		if ( is_null( $prefix ) ) {
			$prefix = 'vi';
		}
		parent::__construct( $query, $moduleName, $prefix );
	}

	public static function getInfo( $file, $prop, $result, $thumbParams = null, $version = 'latest' ) {
		$vals = parent::getInfo( $file, $prop, $result, $thumbParams );
		if ( isset( $prop['derivatives'] ) ) {
			if ( $file->getHandler() && $file->getHandler() instanceof TimedMediaHandler ) {
				$vals['derivatives'] = WebVideoTranscode::getSources( $file, [ 'fullurl' ] );
				$result->setIndexedTagName( $vals['derivatives'], "derivative" );
			} else {
				// Non-TMH file, no derivatives.
				$vals['derivatives'] = [];
			}
		}
		if ( isset( $prop['timedtext'] ) ) {
			if ( $file->getHandler() && $file->getHandler() instanceof TimedMediaHandler ) {
				$handler = new TextHandler( $file );
				$vals['timedtext'] = $handler->getTracks();
				foreach ( $vals['timedtext'] as &$track ) {
					$track['src'] = wfExpandUrl( $track['src'], PROTO_CURRENT );
				}
				unset( $track );
				$result->setIndexedTagName( $vals['timedtext'], "timedtext" );
			} else {
				// Non-TMH file, no timedtext.
				$vals['timedtext'] = [];
			}
		}
		return $vals;
	}

	public static function getPropertyMessages( $filter = [] ) {
		$pm = parent::getPropertyMessages( $filter );
		$pm['derivatives'] = 'apihelp-query+videoinfo-paramvalue-prop-derivatives';
		$pm['timedtext'] = 'apihelp-query+videoinfo-paramvalue-prop-timedtext';
		return array_diff_key( $pm, array_flip( $filter ) );
	}

	/**
	 * @see ApiBase::getExamplesMessages()
	 * @return array
	 */
	protected function getExamplesMessages() {
		return [
			'action=query&titles=File:Folgers.ogv&prop=videoinfo&viprop=derivatives'
				=> 'apihelp-query+videoinfo-example-1',
		];
	}

	public function getHelpUrls() {
		return 'https://www.mediawiki.org/wiki/API:Videoinfo';
	}

	public function getAllowedParams() {
		$params = parent::getAllowedParams();
		foreach ( $params as $k => $v ) {
			if ( !isset( $params[$k][ApiBase::PARAM_HELP_MSG] ) ) {
				$params[$k][ApiBase::PARAM_HELP_MSG] = "apihelp-query+imageinfo-param-$k";
			}
		}

		return $params;
	}
}
