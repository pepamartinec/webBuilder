<?php
namespace DemoCMS\BuilderBlocks\SimplePages;

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
		$imageFeeder = new cDBFeederBase( '\\DemoCMS\\cImage', $this->database );
		$image       = $imageFeeder->whereID( $imageID )->getOne();

		return array(
			'image' => $image,
		);
	}

	public static function config()
	{
		return array(
			'textBlockID' => array(
				'required' => true,
				'type'     => 'WebBuilder.widget.config.ForeignDataField',

				'module'       => '\\DemoCMS\\Administration\\TextBlockManager\\ImageList',
				'displayField' => 'title'
			)
		);
	}
}