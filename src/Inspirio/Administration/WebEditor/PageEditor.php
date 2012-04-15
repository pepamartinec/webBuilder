<?php
namespace Inspirio\Administration\WebEditor;

use ExtAdmin\Response\ActionResponse;

use Inspirio\cWebPage;

use WebBuilder\DataDependencies\Solver;
use WebBuilder\BlocksLoaders\DatabaseLoader;
use WebBuilder\Administration\TemplateManager\BlockInstancesUpdater;
use WebBuilder\DataObjects\BlockSet;
use Inspirio\Database\cDBFeederBase;
use ExtAdmin\RequestInterface;
use Inspirio\cSimplePage;
use Inspirio\Database\cDatabase;
use ExtAdmin\Module\DataEditor\DataEditor;

class PageEditor extends DataEditor
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
			'type'       => 'WebBuilder.module.WebEditor.PageEditor',
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

	public function loadData_record( RequestInterface $request )
	{
		$recordID = $request->getData( 'ID', 'int' );

		// load web page
		$webPageFeeder = new cDBFeederBase( '\\Inspirio\\cWebPage', $this->database );
		$webPage       = $webPageFeeder->whereID( $recordID )->getOne();

		// load associated simplePage
		$simplePageFeeder = new cDBFeederBase( '\\Inspirio\\cSimplePage', $this->database );
		$simplePage       = $simplePageFeeder->whereID( $webPage->getEntityID() )->getOne();

		// load template
		$blockSetFeeder = new cDBFeederBase( '\\WebBuilder\\DataObjects\\BlockSet', $this->database );
		$blockSet       = $blockSetFeeder->whereID( $webPage->getBlockSetID() )->getOne();

		$loader       = new DatabaseLoader( $blockSetFeeder );
		$instances    = $loader->fetchBlocksInstances( $blockSet );
		$rootInstance = reset( $instances );

		$response = new ActionResponse( true );
		$response->setData( array(
			'ID'           => $webPage->getID(),
			'entityID'     => $webPage->getEntityID(),
			'blockSetID'   => $webPage->getBlockSetID(),
			'title'        => $webPage->getTitle(),
			'urlName'      => $webPage->getUrlName(),
			'titleImageID' => $webPage->getTitleImageID(),

			'perex'   => $simplePage->getPerex(),
			'content' => $simplePage->getContent(),

			'template' => $rootInstance->export(),
		));

		return $response;
	}

	public function saveData( RequestInterface $request )
	{
		try {
			$this->database->transactionStart();

			$title = $request->getData( 'title', 'string' );

			// content page
			$simplePage = new cSimplePage( array(
				'ID'      => $request->getData( 'entityID', 'int' ),
				'perex'   => $request->getData( 'perex',    'string' ),
				'content' => $request->getData( 'content',  'string' ),
			) );

			$simplePageFeeder = new cDBFeederBase( '\\Inspirio\\cSimplePage', $this->database );
			$simplePageFeeder->save( $simplePage );

			// template
			$blockSet = new BlockSet( array(
				'ID'   => $request->getData( 'blockSetID', 'int' ),
				'name' => "[{$title}]",
			) );

			$blockSetFeeder = new cDBFeederBase( '\\WebBuilder\\DataObjects\\BlockSet', $this->database );
			$blockSetFeeder->save( $blockSet );

			$updater = new BlockInstancesUpdater( $this->database );
			$updater->saveBlockInstances( $blockSet, $request->getRawData('template') );

			$loader    = new DatabaseLoader( $blockSetFeeder );
			$instances = $loader->fetchBlocksInstances( $blockSet );

			$solver = new Solver( $this->database );
			$solver->solveMissingDependencies( $instances );

			// create webPage
			$webPage = new cWebPage( array(
				'ID'         => $request->getData( 'ID', 'int' ),
				'blockSetID' => $blockSet->getID(),
				'entityType' => '\\Inspirio\\cSimplePage',
				'entityID'   => $simplePage->getID(),
				'title'      => $title,
				'urlName'    => $request->getData( 'urlName', 'string' ),
			) );

			$webPageFeeder = new cDBFeederBase( '\\Inspirio\\cWebPage', $this->database );
			$webPageFeeder->save( $webPage );

			$this->database->transactionCommit();

		} catch( Exception $e ) {
			$this->database->transactionRollback();
			throw $e;
		}

		$response = new ActionResponse( true );
		$response->setData( array(
				'ID'         => $webPage->getID(),
				'blockSetID' => $webPage->getBlockSetID(),
				'entityID'   => $webPage->getEntityID(),
				'title'      => $webPage->getTitle(),
				'urlName'    => $webPage->getUrlName(),

				'perex'   => $simplePage->getPerex(),
				'content' => $simplePage->getContent(),
		) );

		return $response;
	}
}