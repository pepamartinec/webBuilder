<?php
namespace WebBuilder\WebBuilder\ExtAdmin\TemplatesManager;

use ExtAdmin\Response\DataStoreResponse;

use ExtAdmin\Response\DataBrowserResponse;

use Inspirio\Database\cDBFeederBase;
use ExtAdmin\Response\Response;
use ExtAdmin\RequestInterface;
use ExtAdmin\ResponseInterface;
use Inspirio\Database\cDatabase;
use ExtAdmin\Module\DataEditor\DataEditor;

class TemplateEditor extends DataEditor
{
	/**
	 * @var cDatabase
	 */
	protected $database;

	/**
	 * @var \SimpleXMLElement
	 */
	protected $labels;

	/**
	 * Module constructor
	 *
	 * @param cDatabase $database
	 * @param \SimpleXMLElement $labels
	 */
	public function __construct( cDatabase $database, \SimpleXMLElement $labels )
	{
		$this->database = $database;
		$this->labels   = $labels;
	}

	/**
	 * Returns module actions definition
	 *
	 * Used for defining actions within concrete modules implementations
	 *
	 * @return array
	 */
	protected function actions()
	{
		return array(
			'loadData_new'       => true,
			'loadData_record'    => true,
			'loadData_copy'      => true,
			'loadData_inherited' => true,

			'submitData' => array(
				'type' => 'save'
			),

			'cancel' => array(
				'type' => 'cancel'
			),

			'loadAvailableBlocks' => true
		);
	}

	/**
	 * Module view configuration
	 *
	 * @return array
	 */
	public function viewConfiguration()
	{
		return array(
			'type'         => 'WebBuilder.module.TemplatesManager.TemplateEditor',
			'loadAction'   => 'loadData_record',
			'submitAction' => 'submitData',

			'buttons' => array(
				array(
					'type'   => 'button',
					'text'   => 'Uložit',
					'action' => 'submitData'
				),

				array(
					'type'   => 'button',
					'text'   => 'Storno',
					'action' => 'cancel'
				)
			)
		);
	}

	/**
	 * Loads template by ID
	 *
	 * @param  int                $templateID
	 * @param  RessponseInterface $response
	 * @return BlocksSet
	 */
	private function loadTemplate( $templateID, ResponseInterface $response )
	{
		/* @var $template BlocksSet */
		$template = null;

		// load template
		if( $templateID != null ) {
			$templatesFeeder = new cDBFeederBase( '\\WebBuilder\\WebBuilder\\DataObjects\\BlocksSet', $this->database );
			$template        = $templatesFeeder->whereID( $templateID )->getOne();
		}

		if( $template === null ) {
			$response->setSuccess( false )
			         ->setMessage( 'Template not found' );

			return null;
		}

		return $template;
	}

	/**
	 * Loads data for new template
	 *
	 * @param  RequestInterface $request
	 * @return Response
	 */
	public function loadData_new( RequestInterface $request )
	{
		return new Response( true );
	}

	/**
	 * Loads data for new template
	 *
	 * @param  RequestInterface $request
	 * @return Response
	 */
	public function loadData_record( RequestInterface $request )
	{
		$templateID = $request->getData( 'recordID', 'int' );
		$response   = new Response( true );

		$template = $this->loadTemplate( $templateID, $response );

		if( $response->getSuccess() === false ) {
			return $response;
		}

		$response->setData( $template->getInnerValues() );

		return $response;
	}

	/**
	 * Loads data for creating template copy
	 *
	 * @param  RequestInterface $request
	 * @return Response
	 */
	public function loadData_copy( RequestInterface $request )
	{
		$templateID = $request->getData( 'recordID', 'int' );
		$response   = new Response( true );

		$template = $this->loadTemplate( $templateID, $response );

		if( $response->getSuccess() === false ) {
			return $response;
		}

		$template->setID( null );
		$template->setTitle( "{$template->getTitle()} (kopie)" );

		$response->setData( $template->getInnerValues() );

		return $response;
	}

	/**
	 * Loads data for creating inherited template
	 *
	 * @param  RequestInterface $request
	 * @return Response
	 */
	public function loadData_inherited( RequestInterface $request )
	{
		$templateID = $request->getData( 'recordID', 'int' );
		$response   = new Response( true );

		$template = $this->loadTemplate( $templateID, $response );

		if( $response->getSuccess() === false ) {
			return $response;
		}

		$template->setParentID( $template->getID() );
		$template->setID( null );

		$response->setData( $template->getInnerValues() );

		return $response;
	}

	/**
	 * Loads list of available template building blocks
	 *
	 * @param RequestInterface $request
	 * @return DataStoreResponse
	 */
	public function loadAvailableBlocks( RequestInterface $request )
	{
		$blocksFeeder = new cDBFeederBase( '\\WebBuilder\\WebBuilder\\DataObjects\\Block', $this->database );
		$blocks       = $blocksFeeder->getRaw();

		$categories = array(
			array( 'title' => 'Obecné', 'blocks' => $blocks ),
			array( 'title' => 'Layout' ),
			array( 'title' => 'Galerie obrázků' ),
			array( 'title' => 'Stránky' ),
			array( 'title' => 'Diskuze' ),
		);

		return new DataStoreResponse( true, $categories, sizeof( $categories ) );
	}
}