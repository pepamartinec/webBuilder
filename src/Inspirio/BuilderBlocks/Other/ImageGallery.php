<?php
namespace Inspirio\BuilderBlocks\Other;

use Inspirio\cWebPage;
use Inspirio\Database\cDBFeederBase;
use WebBuilder\WebBlock;

class ImageGallery extends WebBlock
{
	public static function requires()
	{
		return array(
			'webPage' => 'cWebPage',
		);
	}

	public static function provides()
	{
		return array(
			'images'     => 'array[ cImage ]',
			'dimensions' => 'array'
		);
	}

	public function setupData( cWebPage $webPage )
	{
		$imageFeeder = new cDBFeederBase( '\\Inspirio\\cImage', $this->database );
		$images      = $imageFeeder->whereColumnEq( 'web_page_ID', $webPage->getID() )->get();

		if( $images === null ) {
			$images = array();
		}

		$dimensions = array();

		$pathPrefix = PATH_TO_WEBSERVER_ROOT . PATH_FROM_ROOT_TO_BASE;
		foreach( $images as $image ) {
			$dim = array();

			list( $width, $height ) = getimagesize( $pathPrefix . $image->getFilenameFull() );
			$dim['full'] = array(
				'width'  => $width,
				'height' => $height
			);

			list( $width, $height ) = getimagesize( $pathPrefix . $image->getFilenameThumb() );
			$dim['thumb'] = array(
				'width'  => $width,
				'height' => $height
			);

			$dimensions[ $image->getID() ] = $dim;
		}

		return array(
			'images'     => $images,
			'dimensions' => $dimensions,
		);
	}
}