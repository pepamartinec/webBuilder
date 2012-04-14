<?php
namespace Inspirio\BuilderBlocks\Pages;

use Inspirio\Database\cDBFeederBase;

use WebBuilder\WebBlock;

class ItemsList extends WebBlock
{
	public static function requires()
	{
		return array(
			'parentID' => 'int'
		);
	}

	public static function provides()
	{
		return array(
			'pages' => 'array[ cPage ]'
		);
	}

	public function setupData( $parentID )
	{
		$pagesFeeder = new cDBFeederBase( '\Inspirio\cPage', $this->database );
		$pages = $pagesFeeder->whereColumnEq( 'parent_ID', $parentID );

		return array(
			'pages' => $pages
		);
	}
}