<?php
namespace WebBuilder\WebBuilder\ExtAdmin\TemplatesManager;

use ExtAdmin\Module\DataBrowser\GridList;
use WebBuilder\WebBuilder\DataObjects\BlocksSet;
use ExtAdmin\Request\DataRequest;
use ExtAdmin\Response\DataBrowserResponse;
use ExtAdmin\RequestInterface;
use Inspirio\Database\cDBFeederBase;
use Inspirio\Database\cDatabase;

class TemplatesList extends GridList
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

			'createEmpty' => array(
				'title'   => 'Vytvořit prázdnou',
				'type'    => 'edit',
				'dataDep' => false,
				'params'  => array(
					'editor'     => 'TemplateEditor',
					'loadAction' => array( 'createTemplate' )
				),
			),


// 			array(
// 				'title'   => 'Vytvořit prázdnou',
// 				'type'    => 'form',
// 				'dataDep' => false,
// 				'params'  => array(
// 					'form' => 'WebBuilder.module.TemplatesManager.TemplateEditor',
// 					'mode' => 'inline',
// 					'data' => 'empty'
// 				),
// 				'enabled' => true
// 			),

// 			'createCopy' => array(
// 				'title'   => 'Vytvořit kopii',
// 				'type'    => 'form',
// 				'dataDep' => true,
// 				'params'  => array(
// 					'form' => 'WebBuilder.module.TemplatesManager.TemplateEditor',
// 					'mode' => 'inline',
// 					'data' => 'copy'
// 				),
// 				'enabled' => true
// 			),

// 			'createInherited' => array(
// 				'title'   => 'Vytvořit poděděnou',
// 				'type'    => 'form',
// 				'dataDep' => true,
// 				'params'  => array(
// 					'form' => 'WebBuilder.module.TemplatesManager.TemplateEditor',
// 					'mode' => 'inline',
// 					'data' => 'inherited'
// 				),
// 				'enabled' => true
// 			),

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

			'delete' => array(
				'title'   => 'Smazat',
				'type'    => 'delete',
				'enabled' => true,
// 				'enabled' => function( BlocksSet $record ) {
// 					return ( $record->getID() % 2 ) == 0;
// 				}
			),
		);
	}

	/**
	 * Module viewConfiguration
	 *
	 * @return array
	 */
	public function viewConfiguration()
	{
		return array(
			'barActions' => array(
				array(
					'type'  => 'splitButton',
					'title' => 'Založit novou',
					'items' => array( 'createEmpty', 'createCopy', 'createInherited' )
				),
				'edit',
				'delete'
			),

			'fields' => array(
				'name' => array(
					'title' => 'Název'
				),

				'algo' => array(
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
		$request = new DataRequest( $request );

		$dataFeeder = new cDBFeederBase( 'WebBuilder\\WebBuilder\\DataObjects\\BlocksSet', $this->database );
		$data       = $dataFeeder->get();
		$count      = $dataFeeder->getCount();

		return new DataBrowserResponse( true, $data, $count, function( BlocksSet $record ) {
			return array(
				'ID'    => $record->getID(),
				'name'  => $record->getName(),
				'image' => 'images/templateThumb.png',
			);
		} );
	}

	/**
	 * Removes selected items
	 *
	 * @param  RequestInterface $request
	 * @return Response
	 */
	public function delete( RequestInterface $request )
	{
		var_dump( $request->getParameter( 'recordID', 'int' ) );
exit;
// 		$dataFeeder = new cDBFeederBase( 'WebBuilder\\WebBuilder\\DataObjects\\BlocksSet', $this->database );
// 		$data       = $dataFeeder->get();
// 		$count      = $dataFeeder->getCount();
	}
}