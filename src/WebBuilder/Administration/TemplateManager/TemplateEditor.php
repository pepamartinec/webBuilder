<?php
namespace WebBuilder\Administration\TemplateManager;

use WebBuilder\DataDependencies\Solver;

use ExtAdmin\Request\AbstractRequest;

use WebBuilder\BlocksLoaders\DatabaseLoader;

use WebBuilder\DataObjects\BlockSet;
use WebBuilder\BlockInstance;
use ExtAdmin\Request\Request;
use Inspirio\Database\aDataObject;
use WebBuilder\DataObjects\Block;
use WebBuilder\WebBuilderInterface;
use ExtAdmin\Response\HtmlResponse;
use WebBuilder\Twig\WebBuilderExtension;
use ExtAdmin\Response\CssResponse;
use WebBuilder\DataObjects\BlocksCategory;
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
			'type'       => 'WebBuilder.module.TemplateManager.TemplateEditor',
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
	 * @param  int                $blockSetID
	 * @param  RessponseInterface $response
	 * @return BlockSet
	 */
	private function loadTemplateData( $blockSetID, ResponseInterface $response )
	{
		/* @var $template BlockSet */
		$blockSet = null;

		// load blocks set
		$blockSetsFeeder = new cDBFeederBase( '\\WebBuilder\\DataObjects\\BlockSet', $this->database );
		$blockSet        = $blockSetsFeeder->whereID( $blockSetID )->getOne();

		if( $blockSet === null ) {
			$response->setSuccess( false )
			         ->setMessage( 'Template not found' );

			return null;
		}

		// load template blocks
		$blocksLoader = new \WebBuilder\BlocksLoaders\DatabaseLoader( $blockSetsFeeder );
		$blocks       = $blocksLoader->fetchBlocksInstances( $blockSet );
		$rootBlock    = reset( $blocks );

		return array(
			'ID'       => $blockSet->getID(),
			'name'     => $blockSet->getName(),
			'parentID' => $blockSet->getParentID(),
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
		$categoriesFeeder = new cDBFeederBase( '\\WebBuilder\\DataObjects\\BlocksCategory', $this->database );
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
		$blocksFeeder = new cDBFeederBase( '\\WebBuilder\\DataObjects\\Block', $this->database );
		$blocks       = $blocksFeeder->get();

		$templatesFeeder = new cDBFeederBase( '\\WebBuilder\\DataObjects\\BlockTemplate', $this->database );
		$templates       = $templatesFeeder->groupBy( 'block_ID' )->get();

		$slotsFeeder = new cDBFeederBase( '\\WebBuilder\\DataObjects\\BlockTemplateSlot', $this->database );
		$slots       = $slotsFeeder->groupBy( 'template_ID' )->get();

		$requirementsFeeder = new cDBFeederBase( '\\WebBuilder\\DataObjects\\BlockDataRequirement', $this->database );
		$requirements       = $requirementsFeeder->groupBy( 'block_ID' )->get();

		$providersFeeder = new cDBFeederBase( '\\WebBuilder\\DataObjects\\BlockDataRequirementProvider' , $this->database );
		$providers       = $providersFeeder->groupBy( 'provider_ID' )->get();

		$data = array();

		foreach( $blocks as $block ) {
			/* @var $block Block */
			$blockID = $block->getID();

			$className = $block->getCodeName();

			$blockData = array(
				'ID'         => $blockID,
				'categoryID' => $block->getCategoryID(),
				'title'      => $block->getTitle(),
				'codeName'   => $block->getCodeName(),
				'config'     => $className::config(),
				'requires'   => array(),
				'provides'   => array(),
				'templates'  => array(),
			);

			if( isset( $requirements[ $blockID ] ) ) {
				foreach( $requirements[ $blockID ] as $requirement ) {
					/* @var @requirement BlockDataRequirement */
					$reqData = array(
						'ID'        => $requirement->getID(),
						'property'  => $requirement->getProperty(),
						'dataType'  => $requirement->getDataType(),
					);

					$blockData['requires'] = $reqData;
				}
			}

			if( isset( $providers[ $blockID ] ) ) {
				foreach( $providers[ $blockID ] as $provider ) {
					/* @var $provider BlockDataRequirementProvider */
					$provData = array(
						'ID'                 => $provider->getID(),
						'blockID'            => $provider->getProviderID(),
						'property'           => $provider->getProviderProperty(),
						'requiredPropertyID' => $provider->getRequiredPropertyID(),
					);

					$blockData['provides'][] = $provData;
				}
			}

			if( isset( $templates[ $blockID ] ) ) {
				foreach( $templates[ $blockID ] as $template ) {
					/* @var $template BlockTemplate */
					$templateID = $template->getID();

					$templateData = array(
						'ID'        => $templateID,
						'blockID'   => $blockID,
						'filename'  => $template->getFilename(),
						'content'   => file_get_contents( $template->getFilename() ),
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
// 			$templatesFeeder = new cDBFeederBase( '\\WebBuilder\\DataObject\\BlockTemplate', $this->database );
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
//			'base_template_class' => '\WebBuilder\Twig\WebBuilderTemplate'
		) );

		$builder = new DummyBuilder();
		$twig->addExtension( new WebBuilderExtension( $builder ) );

		/* @var $template \WebBuilder\Twig\WebBuilderTemplate */
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
		try {
			$this->database->transactionStart();

			$blockSetsFeeder = new cDBFeederBase( '\\WebBuilder\\DataObjects\\BlockSet', $this->database );

			// save blockSet
			$blockSet = $this->saveBlockSet( $blockSetsFeeder, $request );

			// save block instances
			$updater   = new BlockInstancesUpdater( $this->database );
			$instances = $updater->saveBlockInstances( $blockSet, $request->getRawData('template') );

			$this->database->transactionCommit();

		} catch( Exception $e ) {
			$this->database->transactionRollback();
			throw $e;
		}

		$rootInstance = reset( $instances );

		$response = new ActionResponse( true );
		$response->setData( array(
			'ID'       => $blockSet->getID(),
			'name'     => $blockSet->getName(),
			'parentID' => $blockSet->getParentID(),
			'template' => $rootInstance->export(),
		) );

		return $response;
	}

	/**
	 * Saves BlockSet
	 *
	 * @param RequestInterface $request
	 * @return BlockSet
	 */
	private function saveBlockSet( cDBFeederBase $blockSetsFeeder, RequestInterface $request )
	{
		$ID   = $request->getData( 'ID', 'int' );
		$name = $request->getData( 'name', 'string' );

		$blockSet = new BlockSet( array(
			'ID'   => $ID,
			'name' => $name
		) );

		$blockSetsFeeder->save( $blockSet );

		return $blockSet;
	}
}
