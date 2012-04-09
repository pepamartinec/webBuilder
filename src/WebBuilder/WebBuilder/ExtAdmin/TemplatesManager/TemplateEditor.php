<?php
namespace WebBuilder\WebBuilder\ExtAdmin\TemplatesManager;

use ExtAdmin\Request\AbstractRequest;

use WebBuilder\WebBuilder\BlocksLoaders\DatabaseLoader;

use WebBuilder\WebBuilder\DataObjects\BlocksSet;
use WebBuilder\WebBuilder\BlockInstance;
use ExtAdmin\Request\Request;
use Inspirio\Database\aDataObject;
use WebBuilder\WebBuilder\DataObjects\Block;
use WebBuilder\WebBuilder\WebBuilderInterface;
use WebBuilder\WebBuilder\DataObjects\WebStructureItem;
use ExtAdmin\Response\HtmlResponse;
use WebBuilder\WebBuilder\Twig\WebBuilderExtension;
use ExtAdmin\Response\CssResponse;
use WebBuilder\WebBuilder\DataObjects\BlocksCategory;
use ExtAdmin\Response\DataStoreResponse;
use ExtAdmin\Response\DataBrowserResponse;
use Inspirio\Database\cDBFeederBase;
use ExtAdmin\Response\ActionResponse;
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

			'saveData' => array(
				'type' => 'save'
			),

			'cancel' => array(
				'type' => 'cancel'
			),

			'loadBlocksCategories' => true,
			'loadBlocks' => true,
			'loadSimplifiedStylesheet' => true,
			'loadMasterBlockTemplate' => true,
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
			'type'       => 'WebBuilder.module.TemplatesManager.TemplateEditor',
			'loadAction' => 'loadData_record',
			'saveAction' => 'saveData',

			'buttons' => array(
				array(
					'type'   => 'button',
					'text'   => 'Dum dum',
					'action' => 'dumAction'
				),

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
	 * Loads template by ID
	 *
	 * @param  int                $blocksSetID
	 * @param  RessponseInterface $response
	 * @return BlocksSet
	 */
	private function loadTemplateData( $blocksSetID, ResponseInterface $response )
	{
		/* @var $template BlocksSet */
		$blocksSet = null;

		// load blocks set
		$blocksSetsFeeder = new cDBFeederBase( '\\WebBuilder\\WebBuilder\\DataObjects\\BlocksSet', $this->database );
		$blocksSet        = $blocksSetsFeeder->whereID( $blocksSetID )->getOne();

		if( $blocksSet === null ) {
			$response->setSuccess( false )
			         ->setMessage( 'Template not found' );

			return null;
		}

		// load template blocks
		$blocksLoader = new \WebBuilder\WebBuilder\BlocksLoaders\DatabaseLoader( $blocksSetsFeeder );
		$blocks       = $blocksLoader->fetchBlocksInstances( $blocksSet );
		$rootBlock    = reset( $blocks );

		return array(
			'ID'       => $blocksSet->getID(),
			'name'     => $blocksSet->getName(),
			'parentID' => $blocksSet->getParentID(),
			'template' => $rootBlock->export()
		);
	}

	/**
	 * Loads data for new template
	 *
	 * @param  RequestInterface $request
	 * @return Response
	 */
	public function loadData_new( RequestInterface $request )
	{
		$response = new ActionResponse( true );

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
		$templateID = $request->getData( 'ID', 'int' );
		$response   = new ActionResponse( true );

		$templateData = $this->loadTemplateData( $templateID, $response );

		if( $response->getSuccess() === false ) {
			return $response;
		}

		$response->setData( $templateData );

		return $response;
	}

	private function loadData_record_buildInstanceData()
	{

	}

	/**
	 * Loads data for creating template copy
	 *
	 * @param  RequestInterface $request
	 * @return Response
	 */
	public function loadData_copy( RequestInterface $request )
	{
		$templateID = $request->getData( 'ID', 'int' );
		$response   = new ActionResponse( true );

		$templateData = $this->loadTemplateData( $templateID, $response );

		if( $response->getSuccess() === false ) {
			return $response;
		}

		$templateData['ID'] = null;
		$templateData['name'] += ' (kopie)';

		$response->setData( $templateData );

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
		$templateID = $request->getData( 'ID', 'int' );
		$response   = new ActionResponse( true );

		$templateData = $this->loadTemplateData( $templateID, $response );

		if( $response->getSuccess() === false ) {
			return $response;
		}

		$templateData['parentID'] = $templateData['ID'];
		$templateData['ID'] = null;
		$templateData['name'] += ' (kopie)';

		$response->setData( $templateData );

		return $response;
	}

	/**
	 * Loads list of available blocks categories
	 *
	 * @param RequestInterface $request
	 * @return DataStoreResponse
	 */
	public function loadBlocksCategories( RequestInterface $request )
	{
		$categoriesFeeder = new cDBFeederBase( '\\WebBuilder\\WebBuilder\\DataObjects\\BlocksCategory', $this->database );
		$categories       = $categoriesFeeder->get();

		$response = new DataStoreResponse( true, $categories, null, function( BlocksCategory $category ) {
			return array(
				'ID'    => $category->getID(),
				'title' => $category->getTitle(),
			);
		});

		return $response;
	}

	/**
	 * Loads list of available template building blocks
	 *
	 * @param RequestInterface $request
	 * @return DataStoreResponse
	 */
	public function loadBlocks( RequestInterface $request )
	{
		$blocksFeeder = new cDBFeederBase( '\\WebBuilder\\WebBuilder\\DataObjects\\Block', $this->database );
		$blocks       = $blocksFeeder->get();

		$templatesFeeder = new cDBFeederBase( '\\WebBuilder\\WebBuilder\\DataObjects\\BlockTemplate', $this->database );
		$templates       = $templatesFeeder->groupBy( 'block_ID' )->get();

		$slotsFeeder = new cDBFeederBase( '\\WebBuilder\\WebBuilder\\DataObjects\\BlockTemplateSlot', $this->database );
		$slots       = $slotsFeeder->groupBy( 'template_ID' )->get();

		$data = array();

		foreach( $blocks as $block ) {
			/* @var $block Block */
			$blockID = $block->getID();

			$blockData = array(
				'ID'         => $blockID,
				'categoryID' => $block->getCategoryID(),
				'title'      => $block->getTitle(),
				'codeName'   => $block->getCodeName(),
				'templates'  => array(),
			);

			if( isset( $templates[ $blockID ] ) ) {
				foreach( $templates[ $blockID ] as $template ) {
					/* @var $template BlockTemplate */
					$templateID = $template->getID();

					$templateData = array(
						'ID'        => $templateID,
						'blockID'   => $blockID,
						'filename'  => $template->getFilename(),
						'slots' => array(),
					);

					if( isset( $slots[ $templateID ] ) ) {
						foreach( $slots[ $templateID ] as $slot ) {
							/* @var $template BlockTemplate */
							$slotID = $slot->getID();

							$slotData = array(
								'ID'         => $slotID,
								'templateID' => $templateID,
								'codeName'   => $slot->getCodeName(),
							);

							$templateData['slots'][] = $slotData;
						}
					}

					$blockData['templates'][] = $templateData;
				}
			}

			$data[] = $blockData;
		}

		$response = new ActionResponse( true );
		$response->setData( $data );

		return $response;
	}

	/**
	 * Loads simplified stylesheet
	 *
	 * @param RequestInterface $request
	 * @return ResponseInterface
	 */
	public function loadSimplifiedStylesheet( RequestInterface $request )
	{
		echo $sheetFile = PATH_TO_ROOT . $request->getParameter( 'stylesheet', 'string' );

		// file not found
		if( is_file( $sheetFile ) === false ) {
			$response = new CssResponse( '' );
			$response->setStatus( CssResponse::S_NOT_FOUND );

			return $response;
		}

		$sheetContent = file_get_contents( $sheetFile );

		$parser = new \CSSParser( $sheetContent );
		$css    = $parser->parse();

		$rulesWhiteList = array(
			'width',  'min-width',  'max-width',
			'height', 'min-height', 'max-height',
			'margin',
			'float', 'clear',
			'display'
		);

		foreach( $css->getAllRuleSets() as $ruleSet ) {
			/* @var $ruleSet \CSSDeclarationBlock */

			foreach( $ruleSet->getRules() as $rule ) {
				/* @var $rule \CSSRule */
				if( in_array( $rule->getRule(), $rulesWhiteList ) === false ) {
					$ruleSet->removeRule( $rule );
				}
			}

			if( sizeof( $ruleSet->getRules() ) === 0 ) {
				$css->remove( $ruleSet );
			}
		}

		$response = new CssResponse( $css->__toString() );
		$response->setLastModified( filemtime( $sheetFile ) );

		return $response;
	}

	/**
	 * Loads block template
	 *
	 * @param RequestInterface $request
	 * @return ResponseInterface
	 */
	public function loadMasterBlockTemplate( RequestInterface $request )
	{
// 		$templateID = $request->getParameter( 'templateID', 'int' );
// 		$template   = null;
// 		$response   = new ActionResponse( true );

// 		if( $templateID ) {
// 			$templatesFeeder = new cDBFeederBase( '\\WebBuilder\\WebBuilder\\DataObject\\BlockTemplate', $this->database );
// 			$template        = $templatesFeeder->whereID( $tempateID )->getID();
// 		}

// 		if( $template === null ) {
// 			$response->setSuccess( false )
// 			         ->setMessage( "No template with ID '{$templateID}' found" );

// 			return $response;
// 		}

		// init Twig
		$loader = new \Twig_Loader_Filesystem( PATH_TO_ROOT );
		$twig   = new \Twig_Environment( $loader, array(
//			'cache'               => './tmp/',
//			'base_template_class' => '\WebBuilder\WebBuilder\Twig\WebBuilderTemplate'
		) );

		$builder = new DummyBuilder();
		$twig->addExtension( new WebBuilderExtension( $builder ) );

		/* @var $template \WebBuilder\WebBuilder\Twig\WebBuilderTemplate */
		$template = $twig->loadTemplate( 'templates/core/webPage_html5.twig' );

		// TODO ugly hack


		$html = $template->render( array(
			'config' => array(
				'stylesheet' => 'public/css/style.css'
			)
		) );

		return new HtmlResponse( $html );
	}

	/**
	 * Saves submitted template data
	 *
	 * @param RequestInterface $request
	 * @return DataStoreResponse
	 */
	public function saveData( RequestInterface $request )
	{
		$ID       = $request->getData( 'ID', 'int' );
		$name     = $request->getData( 'name', 'string' );
		$slotIDs  = array();
		$instance = $this->saveData_safeInputBlock( $request->getRawData( 'template' ), $slotIDs );

		try {
			$this->database->transactionStart();

			// convert slot identificators
			$this->convertSlotIdentificators( $instance );

			// create blocksSet (template)
			$blocksSet = new BlocksSet( array(
				'ID'   => $ID,
				'name' => $name
			) );

			$blockSetsFeeder = new cDBFeederBase( '\\WebBuilder\\WebBuilder\\DataObjects\\BlocksSet', $this->database );
			$blockSetsFeeder->save( $blocksSet );

			// save blocks instances (template items)

			$sql = "DELETE FROM blocks_instances WHERE blocks_set_ID = {$blocksSet->getID()}";
			$this->database->query( $sql );

			$this->saveData_saveInstance( $blocksSet->getID(), $instance );

			$this->database->transactionCommit();

		} catch( Exception $e ) {
			$this->database->transactionRollback();
			throw $e;
		}

		// reload updated data
		$response = new ActionResponse( true );
		$response->setData( $this->loadTemplateData( $blocksSet->getID(), $response ) );

		return $response;
	}

	private function saveData_safeInputBlock( array $rawBlock, array &$slotIDs )
	{
		$instance = array(
			'templateID' => AbstractRequest::secureData( $rawBlock, 'templateID', 'int' ),
			'slots'      => array()
		);

		$instances[] = &$instance;

		foreach( $rawBlock['slots'] as $rawSlotID => $children ) {
			$slotID = AbstractRequest::secureValue( $rawSlotID, 'string' );

			$slotIDs[] = $slotID;

			$instance['slots'][ $slotID ] = array();
			$slot = &$instance['slots'][ $slotID ];

			foreach( $children as $rawChild ) {
				$slot[] = $this->saveData_safeInputBlock( $rawChild, $slotIDs );
			}
		}

		return $instance;
	}

	private function convertSlotIdentificators( array &$rootInstance )
	{
		// collect used codeNames
		$slotCodeNames = array();
		$this->convertSlotIdentificators_collectSlots( $rootInstance, $slotCodeNames );

		// load slots
		$filter = array();
		foreach( $slotCodeNames as $templateID => $codeNames ) {
			$codeNames = implode( "','", $codeNames );

			$filter[] = "( template_ID = {$templateID} AND code_name IN ('{$codeNames}') )";
		}

		$slotsFeeder = new cDBFeederBase( '\\WebBuilder\\WebBuilder\\DataObjects\\BlockTemplateSlot', $this->database );
		$slots       = $slotsFeeder->where( implode( ' OR ', $filter ) )->groupBy( 'template_ID' )->indexBy( 'code_name' )->get();

		// convert identificators
		$this->convertSlotIdentificators_convert( $rootInstance, $slots );
	}

	private function convertSlotIdentificators_collectSlots( array $instance, array &$slots )
	{
		// skip blocks with no slots
		if( $instance['slots'] == null ) {
			return;
		}

		$templateID = $instance['templateID'];

		foreach( $instance['slots'] as $codeName => $children ) {
			$slots[ $templateID ][] = $codeName;

			foreach( $children as $child ) {
				$this->convertSlotIdentificators_collectSlots( $child, $slots );
			}
		}
	}

	private function convertSlotIdentificators_convert( array &$instance, array $slots )
	{
		// skip blocks with no slots
		if( $instance['slots'] == null ) {
			return;
		}

		$templateID = $instance['templateID'];

		if( isset( $slots[ $templateID ] ) === false ) {
			// ERROR
		}

		$templateSlots = $slots[ $templateID ];
		$slotsByIDs    = array();

		foreach( $instance['slots'] as $codeName => $children ) {
			if( isset( $templateSlots[ $codeName ] ) === false ) {
				// ERROR
			}

			$slot = $templateSlots[ $codeName ];

			$slotsByIDs[ $slot->getID() ] = array();
			$newChildren = &$slotsByIDs[ $slot->getID() ];

			foreach( $children as &$child ) {
				$this->convertSlotIdentificators_convert( $child, $slots );

				$newChildren[] = $child;
			}
		}

		$instance['slots'] = $slotsByIDs;
	}

	private function saveData_saveInstance( $blocksSetID, array &$instance )
	{
		$sql = "INSERT INTO blocks_instances ( blocks_set_ID, template_ID ) VALUES ( {$blocksSetID}, {$instance['templateID']} )";
		$this->database->query( $sql );

		$instance['ID'] = $this->database->getLastInsertedId();

		foreach( $instance['slots'] as $slotID => $children ) {
			foreach( $children as $position => &$child ) {
				$this->saveData_saveInstance( $blocksSetID, $child );

				$sql = "INSERT INTO blocks_instances_subblocks ( parent_instance_ID, parent_slot_ID, position, inserted_instance_ID ) VALUES ( {$instance['ID']}, {$slotID}, {$position}, {$child['ID']} )";
				$this->database->query( $sql );
			}
		}
	}
}

class DummyBuilder implements WebBuilderInterface
{
	public function render( WebStructureItem $structureItem )
	{

	}
}