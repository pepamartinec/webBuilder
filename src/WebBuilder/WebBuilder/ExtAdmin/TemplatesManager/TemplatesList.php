<?php
namespace WebBuilder\WebBuilder\ExtAdmin\TemplatesManager;

use ExtAdmin\ModuleInterface;

use ExtAdmin\Request\DataRequest;

use WebBuilder\WebBuilder\DataObjects\BlocksSet;
use ExtAdmin\Response\DataListResponse;
use ExtAdmin\Module\DataList\DataList;
use ExtAdmin\RequestInterface;
use Inspirio\Database\cDBFeederBase;
use Inspirio\Database\cDatabase;

class TemplatesList implements ModuleInterface
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
	public function getActions()
	{
		return array(
			'loadListData' => true,

			'createEmpty' => array(
				'title'   => 'Vytvořit prázdnou',
				'type'    => 'form',
				'dataDep' => false,
				'params'  => array(
					'form' => 'WebBuilder.module.TemplatesManager.TemplateEditor',
					'data' => 'empty'
				),
				'enabled' => true
			),

			'createCopy' => array(
				'title'   => 'Vytvořit kopii',
				'type'    => 'form',
				'dataDep' => true,
				'params'  => array(
					'form' => 'WebBuilder.module.TemplatesManager.TemplateEditor',
					'data' => 'copy'
				),
				'enabled' => true
			),

			'createInherited' => array(
				'title'   => 'Vytvořit poděděnou',
				'type'    => 'form',
				'dataDep' => true,
				'params'  => array(
					'form' => 'WebBuilder.module.TemplatesManager.TemplateEditor',
					'data' => 'inherited'
				),
				'enabled' => true
			),
			
			'edit' => array(
				'title'   => 'Upravit',
				'type'    => 'form',
				'dataDep' => true,
				'params'  => array(
					'form' => 'WebBuilder.module.TemplatesManager.TemplateEditor',
					'data' => 'record'
				),
				'enabled' => true
			),
			
			'remove' => array(
				'title'   => 'Smazat',
				'type'    => 'remove',
				'enabled' => true
			),
		);
	}
	
	/**
	 * Returns module UI definition
	 *
	 * Used for defining UI within concrete modules implementations
	 *
	 * @return array
	 */
	public function getViewConfiguration()
	{
		return array(
			'type' => 'WebBuilder.module.TemplatesManager.TemplatesList',
			'barActions' => array(
				array(
					'type'  => 'splitButton',
					'title' => 'Založit novou',
					'items' => array( 'createEmpty', 'createCopy', 'createInherited' )
				),
				'edit',
				'remove'
			),
		);
	}
	
	/**
	 * Loads data for dataList
	 *
	 * @param  RequestInterface $request
	 * @return DataListResponse
	 */
	public function loadListData( RequestInterface $request )
	{		
		$request = new DataRequest( $request );
		
		$dataFeeder = new cDBFeederBase( 'WebBuilder\\WebBuilder\\DataObjects\\BlocksSet', $this->database );
		$data       = $dataFeeder->get();
		$count      = $dataFeeder->getCount();
		
		return new DataListResponse( true, $data, $count, function( BlocksSet $record ) {
			return array(
				'ID'    => $record->getID(),
				'name'  => $record->getName(),
				'image' => 'images/templateThumb.png'
			);
		} );		
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