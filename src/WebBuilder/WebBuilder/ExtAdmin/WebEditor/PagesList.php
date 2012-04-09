<?php
namespace WebBuilder\WebBuilder\ExtAdmin\WebEditor;

use WebBuilder\WebBuilder\DataObjects\BlocksSet;
use ExtAdmin\Response\DataBrowserResponse;
use Inspirio\Database\cDBFeederBase;
use ExtAdmin\Request\DataRequestDecorator;
use ExtAdmin\RequestInterface;
use Inspirio\Database\cDatabase;
use ExtAdmin\Module\DataBrowser\GridList;

class PagesList extends GridList
{
	/**
	 * @var cDatabase
	 */
	protected $database;

	/**
	 * Module constructor
	 *
	 * @param cDatabase $database
	 * @param \SimpleXMLElement $labels
	 */
	public function __construct( cDatabase $database, \SimpleXMLElement $labels )
	{
		$this->database = $database;
	}

	/**
	 * Returns module actions definition
	 *
	 * Used for defining actions within concrete modules implementations
	 *
	 * @return array
	 */
	public function actions()
	{
		return array(
			'loadListData' => true,

			'create' => array(
				'title'  => 'Vytvořit',
				'type'   => 'create',
				'params' => array(
					'editor'     => 'PageEditor',
					'loadAction' => 'loadData_new',
				),
			),

// 			'edit' => array(
// 				'title'   => 'Upravit',
// 				'type'    => 'form',
// 				'dataDep' => true,
// 				'params'  => array(
// 					'form' => 'WebBuilder.module.TemplatesManager.TemplateEditor',
// 					'mode' => 'inline',
// 					'data' => 'record'
// 				),
// 				'enabled' => true
// 			),

// 			'delete' => array(
// 				'title'   => 'Smazat',
// 				'type'    => 'delete',
// 				'enabled' => true
// 			),
		);
	}

	/**
	 * Module UI viewConfiguration
	 *
	 * @return array
	 */
	public function viewConfiguration()
	{
		return array(
			'barActions' => array( 'create', 'edit', 'delete' ),

			'fields' => array(
				'name' => array(
					'title' => 'Název'
				),

				'actions' => array(
					'type'  => 'actioncolumn',
					'items' => array( 'edit', 'delete' )
				)
			),
		);
	}

	/**
	 * Loads data for dataList
	 *
	 * @param  RequestInterface $request
	 * @return DataBrowserResponse
	 */
	public function loadListData( RequestInterface $request )
	{
		$request = new DataRequestDecorator( $request );

		return new DataBrowserResponse( true, array(), 0 );
	}

	/**
	 * Removes selected items
	 *
	 * @param  RequestInterface $request
	 * @return Response
	 */
	public function remove( RequestInterface $request )
	{

	}
}