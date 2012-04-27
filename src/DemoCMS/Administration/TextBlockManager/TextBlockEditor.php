<?php
namespace DemoCMS\Administration\TextBlockManager;

use ExtAdmin\Response\ActionResponse;

use ExtAdmin\RequestInterface;

use DemoCMS\cTextBlock;

use Inspirio\Database\cDBFeederBase;

use Inspirio\Database\cDatabase;
use ExtAdmin\Module\DataEditor\DataEditor;

class TextBlockEditor extends DataEditor
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
			'loadData_new'    => true,
			'loadData_record' => true,

			'saveData' => array(
				'type' => 'save'
			),

			'cancel' => array(
				'type' => 'cancel'
			),
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
				'type'       => 'DemoCMS.module.TextBlockManager.TextBlockEditor',
				'loadAction' => 'loadData_record',
				'saveAction' => 'saveData',

				'buttons' => array(
					array(
						'type'   => 'button',
						'text'   => 'Uložit',
						'action' => 'saveData'
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
	 * Loads the editor data of an existing record
	 *
	 * @param RequestInterface $request
	 * @return ActionResponse
	 */
	public function loadData_new( RequestInterface $request )
	{
		return new ActionResponse( true );
	}

	/**
	 * Loads the editor data of an existing record
	 *
	 * @param RequestInterface $request
	 * @return ActionResponse
	 */
	public function loadData_record( RequestInterface $request )
	{
		$textBlockID = $request->getData( 'ID', 'int' );

		$textBlockFeeder = new cDBFeederBase( '\\DemoCMS\\cTextBlock', $this->database );
		$textBlock       = $textBlockFeeder->whereID( $textBlockID )->getOne();

		if( $textBlock == null ) {
			$response = new ActionResponse( false );
			$response->setMessage( 'Požadovaný text nebyl nalezen' );
			return $response;
		}

		$response = new ActionResponse( true );
		$response->setData( array(
			'ID'      => $textBlock->getID(),
			'title'   => $textBlock->getTitle(),
			'imageID' => $textBlock->getImageID(),
			'content' => $textBlock->getContent(),
		));

		return $response;
	}

	/**
	 * Saves the data received from the editor
	 *
	 * @param RequestInterface $request
	 * @return ActionResponse
	 */
	public function saveData( RequestInterface $request )
	{
		$textBlock = new cTextBlock( array(
			'ID'      => $request->getData( 'ID', 'int' ),
			'title'   => $request->getData( 'title', 'string' ),
			'imageID' => $request->getData( 'imageID', 'int' ),
			'content' => $request->getData( 'content', 'string' ),
		), true );

		$textBlockFeeder = new cDBFeederBase( '\\DemoCMS\\cTextBlock', $this->database );
		$textBlockFeeder->save( $textBlock );

		$response = new ActionResponse( true );
		$response->setData( array(
			'ID'      => $textBlock->getID(),
			'title'   => $textBlock->getTitle(),
			'imageID' => $textBlock->getImageID(),
			'content' => $textBlock->getContent(),
		));

		return $response;
	}
}
