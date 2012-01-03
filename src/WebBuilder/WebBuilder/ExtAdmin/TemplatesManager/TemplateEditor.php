<?php
namespace WebBuilder\WebBuilder\ExtAdmin\TemplatesManager;

use ExtAdmin\ResponseInterface;

use ExtAdmin\Response\Response;

use WebBuilder\WebBuilder\DataObjects\BlocksSet;
use ExtAdmin\Response\DataListResponse;
use ExtAdmin\Module\Form\Form;
use ExtAdmin\RequestInterface;
use Inspirio\Database\cDatabase;
use Inspirio\Database\cDBFeederBase;

class TemplatesList extends Form
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
	protected function actions()
	{
		return array(
			'loadData_empty'     => true,
			'loadData_copy'      => true,
			'loadData_inherited' => true,
			'loadData_record'    => true,
			
			'submitData' => true,
			'preview'    => true
		);
	}
	
	/**
	 * Returns module models definition
	 *
	 * Used for defining models within concrete modules implementations
	 *
	 * @return array
	 */
	protected function models()
	{
		return array(
			'WebBuilder\\WebBuilder\\DataObjects\\BlocksSet',
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
	public function loadData_empty( RequestInterface $request )
	{		
		return new Response( true );		
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
}