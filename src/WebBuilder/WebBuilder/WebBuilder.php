<?php
namespace WebBuilder\WebBuilder;

use WebBuilder\WebBuilder\DataObjects\WebStructureItem;
use WebBuilder\WebBuilder\Twig\WebBuilderExtension;

use Inspirio\Database\cDBFeederBase;
use Inspirio\Database\cDatabase;

class WebBuilder implements WebBuilderInterface
{
	/**
	 * @var Inspirio\Database\cDatabase
	 */
	protected $database;

	/**
	 * @var bool
	 */
	protected $debug;

	/**
	 * @var Inspirio\Database\cDBFeederBase
	 */
	protected $blocksSetsFeeder;

	/**
	 * Constructor
	 *
	 * @param \Database $database
	 */
	public function __construct( cDatabase $database, $debug = false )
	{
		$this->database = $database;
		$this->debug    = $debug;

		$this->blocksSetsFeeder = new cDBFeederBase( '\WebBuilder\WebBuilder\DataObjects\BlocksSet', $this->database );

		// init Twig
		$loader     = new \Twig_Loader_Filesystem( PATH_TO_ROOT );
		$this->twig = new \Twig_Environment( $loader, array(
			'cache'               => './tmp/',
			'debug'               => $this->debug,
			'base_template_class' => '\WebBuilder\WebBuilder\Twig\WebBuilderTemplate'
		) );
		
		$this->twig->addExtension( new WebBuilderExtension( $this ) );
	}

	/**
	 * Renders page for given web structure item
	 *
	 * @param  WebStructureItem $sItem
	 * @return string
	 *
	 * @throws BlocksSetIntegrityException
	 */
	public function render( WebStructureItem $structureItem )
	{
		// load blocks set for given web structure item
		$blocksSet = $this->getBlocksSet( $structureItem, true );
		
		// create blocks builder
		$blocksFactory   = new WebBlocksFactory( $this->database );
		$buildersFactory = new BlocksBuildersFactory( $blocksFactory, $this->twig );
		$blocksBuilder   = $buildersFactory->getBlocksBuilder( $blocksSet, true );
		
		$rootBlock = $blocksSet->getRootBlock();
		if( $rootBlock === null ) {
			throw new BlocksSetIntegrityException( "Blocks set {$blocksSet->getName()}[{$blocksSet->getID()}] has no root block defined" );
		}
		
		// setup root block data
		$rootData = array(
			'structureItem' => $structureItem
		);
		
		foreach( $rootBlock->dataDependencies as &$dependency ) {
			/* @var $dependency \WebBuilder\WebBuilder\DataDependencyInterface */
			$property = $dependency->getProperty();
			
			if( array_key_exists( $property, $rootData ) ) {
				$dependency = new ConstantData( $property, $rootData[ $property ] );
			}
		}

		// build and render blocks
		return $blocksBuilder->renderBlock( $rootBlock );
	}

	/**
	 * Loads BlocksSet belongig to given WebStructureItem and fills underlying blocks definitions
	 *
	 * @param  WebStructureItem $structureItem
	 * @param  bool              $forceRegenreration
	 * @return \WebBuilder\WebBuilder\DataObjects\BlocksSet
	 */
	protected function getBlocksSet( WebStructureItem $structureItem, $forceRegeneration = false )
	{
		$blocksSet = $this->blocksSetsFeeder->whereID( $structureItem->getBlocksSetID() )->getOne();
		
		if( $blocksSet === null ) {
			throw new InvalidBlockException( 'Invalid BlocksSet requested' );
		}

		$blocksLoader = new BlocksLoaders\DatabaseLoader( $this->blocksSetsFeeder );

		if( $this->debug === false ) {
			$blocksLoader = new BlocksLoaders\SerializedCacheProxy( $this->blocksSetsFeeder, $blocksLoader );
//			$blocksLoader->forceRegeneration( $forceRegeneration );
		}

		$blocks = $blocksLoader->fetchBlocksInstances( $blocksSet );
		
		$blocksSet->setBlocks( $blocks );

		return $blocksSet;
	}
}
