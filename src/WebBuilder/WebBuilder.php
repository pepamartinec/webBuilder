<?php
namespace WebBuilder;

use WebBuilder\DataDependencies\ConstantData;
use WebBuilder\Twig\WebBuilderExtension;

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
	protected $blockSetsFeeder;

	/**
	 * @var \Twig_Environment
	 */
	protected $twig;

	/**
	 * Constructor
	 *
	 * @param \Database $database
	 */
	public function __construct( cDatabase $database, array $config = null )
	{
		$this->database = $database;

		if( $config === null ) {
			$config = array();
		}

		$config += array(
			'debug' => false
		);

		$this->debug = $config['debug'];

		$this->blockSetsFeeder = new cDBFeederBase( '\WebBuilder\DataObjects\BlockSet', $this->database );

		// init Twig
		$loader     = new \Twig_Loader_Filesystem( PATH_TO_ROOT );
		$this->twig = new \Twig_Environment( $loader, array(
			'cache'               => './tmp/',
			'debug'               => $this->debug,
			'base_template_class' => '\WebBuilder\Twig\WebBuilderTemplate'
		) );

		$this->twig->addExtension( new WebBuilderExtension( $this ) );
	}

	/**
	 * Returns the Twig environment instance
	 *
	 * @return \Twig_Environment
	 */
	public function getTwig()
	{
		return $this->twig;
	}

	/**
	 * Renders page for given web structure item
	 *
	 * @param  WebPage $webPage
	 * @return string
	 *
	 * @throws BlockSetIntegrityException
	 */
	public function render( WebPageInterface $webPage )
	{
		// load blocks set for given web structure item
		$blockSet = $this->getBlockSet( $webPage, true );

		// create blocks builder
		$blocksFactory   = new WebBlocksFactory( $this->database );
		$buildersFactory = new BlocksBuildersFactory( $blocksFactory, $this->twig );
		$blocksBuilder   = $buildersFactory->getBlocksBuilder( $blockSet, true );

		$rootBlock = $blockSet->getRootBlock();
		if( $rootBlock === null ) {
			throw new BlockSetIntegrityException( "Blocks set {$blockSet->getName()}[{$blockSet->getID()}] has no root block defined" );
		}

		// setup root block data
		$rootData = array(
			'webPage' => $webPage
		);

		foreach( $rootBlock->dataDependencies as &$dependency ) {
			/* @var $dependency \WebBuilder\DataDependencyInterface */
			$property = $dependency->getProperty();

			if( array_key_exists( $property, $rootData ) ) {
				$dependency = new ConstantData( $property, $rootData[ $property ] );
			}
		}

		// build and render blocks
		return $blocksBuilder->renderBlock( $rootBlock );
	}

	/**
	 * Loads BlockSet belongig to given WebPage and fills underlying blocks definitions
	 *
	 * @param  WebPageInterface $wePage
	 * @param  bool $forceRegenreration
	 * @return \WebBuilder\DataObjects\BlockSet
	 */
	protected function getBlockSet( WebPageInterface $wePage, $forceRegeneration = false )
	{
		$blockSet = $this->blockSetsFeeder->whereID( $wePage->getBlockSetID() )->getOne();

		if( $blockSet === null ) {
			throw new InvalidBlockException( 'Invalid BlockSet requested' );
		}

		$blocksLoader = new BlocksLoaders\DatabaseLoader( $this->blockSetsFeeder );

		// TODO disable this permanently for now
		if( $this->debug === false && false ) {
			$blocksLoader = new BlocksLoaders\SerializedCacheProxy( $this->blockSetsFeeder, $blocksLoader );
//			$blocksLoader->forceRegeneration( $forceRegeneration );
		}

		$blocks = $blocksLoader->fetchBlocksInstances( $blockSet );

		$blockSet->setBlocks( $blocks );

		return $blockSet;
	}
}
