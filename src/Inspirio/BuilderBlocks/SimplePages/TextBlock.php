<?php
namespace Inspirio\BuilderBlocks\SimplePages;

use Inspirio\Database\cDBFeederBase;
use WebBuilder\WebBlock;

class TextBlock extends WebBlock
{
	public static function requires()
	{
		return array(
			'textBlockID' => 'ID'
		);
	}

	public static function provides()
	{
		return array(
			'text' => 'cTextBlock'
		);
	}

	public function setupData( $textID )
	{
		$textBlockFeeder = new cDBFeederBase( '\\Inspirio\\cTextBlock', $this->database );
		$textBlock       = $textBlockFeeder->whereID( $textID )->getOne();

		return array(
			'text' => $textBlock
		);
	}

	public static function config()
	{
		return array(
			'textBlockID' => array(
				'required' => true,
				'type'     => 'WebBuilder.widget.config.ForeignDataField',

				'module'       => '\\Inspirio\\Administration\\TextBlockManager\\TextBlockList',
				'displayField' => 'title'
			)
		);
	}
}