<?php
/**
 * Fake handler for Ogg videos.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @file
 * @ingroup Media
 */

use MediaWiki\TimedMediaHandler\Handlers\OggHandler\OggHandler;
use MediaWiki\TimedMediaHandler\TimedMediaTransformOutput;

class MockOggHandler extends OggHandler {
	/** @inheritDoc */
	public function doTransform( $file, $dstPath, $dstUrl, $params, $flags = 0 ) {
		# Important or height handling is wrong.
		if ( !$this->normaliseParams( $file, $params ) ) {
			return new TransformParameterError( $params );
		}

		$srcWidth = $file->getWidth();
		$srcHeight = $file->getHeight();

		// Audio should not be transformed by size, give it a default width and height
		if ( $this->isAudio( $file ) ) {
			$srcWidth = 220;
			$srcHeight = 23;
		}

		$params['width'] = $params['width'] ?? $srcWidth;

		// if height overtakes width use height as max:
		$targetWidth = $params['width'];
		$targetHeight = $srcWidth ? round( $params['width'] * $srcHeight / $srcWidth ) : $srcHeight;
		if ( isset( $params['height'] ) && $targetHeight > $params['height'] ) {
			$targetHeight = $params['height'];
			$targetWidth = round( $params['height'] * $srcWidth / $srcHeight );
		}
		$options = [
			'file' => $file,
			'length' => $this->getLength( $file ),
			'offset' => $this->getOffset( $file ),
			'width' => $targetWidth,
			'height' => $targetHeight,
			'isVideo' => !$this->isAudio( $file ),
			'thumbtime' => $params['thumbtime'] ?? (int)( $file->getLength() / 2 ),
			'start' => $params['start'] ?? false,
			'end' => $params['end'] ?? false,
			'fillwindow' => $params['fillwindow'] ?? false,
			'disablecontrols' => $params['disablecontrols'] ?? false,
			'loop' => $params['loop'] ?? false,
			'muted' => $params['muted'] ?? false,
		];

		// No thumbs for audio
		if ( !$options['isVideo'] ) {
			return new TimedMediaTransformOutput( $options );
		}

		// Setup pointer to thumb arguments
		$options[ 'thumbUrl' ] = $dstUrl;
		$options[ 'dstPath' ] = $dstPath;
		$options[ 'path' ] = $dstPath;

		return new TimedMediaTransformOutput( $options );
	}

	/** @inheritDoc */
	public function getLength( $file ) {
		if ( $this->isAudio( $file ) ) {
			return 0.99875;
		}
		return 4.3666666666667;
	}

	/** @inheritDoc */
	public function getBitRate( $file ) {
		if ( $this->isAudio( $file ) ) {
			return 41107;
		}
		return 590013;
	}

	/** @inheritDoc */
	public function getWebType( $file ) {
		if ( $this->isAudio( $file ) ) {
			return "audio/ogg; codecs=\"vorbis\"";
		}
		return "video/ogg; codecs=\"theora\"";
	}

	/** @inheritDoc */
	public function getFramerate( $file ) {
		if ( $this->isAudio( $file ) ) {
			return 0;
		}
		return 30;
	}
}
