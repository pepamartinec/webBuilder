<?php
namespace DemoCMS\BuilderBlocks\SimplePages;

use DemoCMS\cWebPage;
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
		$imageFeeder = new cDBFeederBase( '\\DemoCMS\\cImage', $this->database );
		$images      = $imageFeeder->whereColumnEq( 'web_page_ID', $webPage->getID() )->get();

		if( $images === null ) {
			$images = array();
		}

		$dimensions = array();

		$pathPrefix = PATH_TO_ROOT;
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