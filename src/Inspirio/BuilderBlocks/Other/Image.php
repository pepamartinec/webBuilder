<?php
namespace Inspirio\BuilderBlocks\Other;

use Inspirio\Database\cDBFeederBase;

use WebBuilder\WebBlock;

class Image extends WebBlock
{
	public static function requires()
	{
		return array(
			'imageID' => 'int',
		);
	}

	public static function provides()
	{
		return array(
			'image' => 'cImage',
		);
	}

	public function setupData( $imageID )
	{
		$imageFeeder = new cDBFeederBase( '\\Inspirio\\cImage', $this->database );
		$image       = $imageFeeder->whereID( $imageID )->getOne();

		return array(
			'image' => $image,
		);
	}
}