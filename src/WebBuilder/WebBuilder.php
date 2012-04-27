<?php
namespace WebBuilder;

use WebBuilder\DataDependencies\ConstantData;
use WebBuilder\Twig\WebBuilderExtension;

use Inspirio\Database\cDBFeederBase;
use Inspirio\Database\cDatabase;

class WebBuilder implements WebBuilderInterface
{
	/**
	 * @var BlocksLoaderInterface
	 */
	protected $blockLoader;

	/**
	 * @var WebBlocksFactoryInterface
	 */
	protected $blockFactory;

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
	public function __construct( BlocksLoaderInterface $blockLoader, WebBlocksFactoryInterface $blockFactory, array $config = null )
	{
		$this->blockLoader  = $blockLoader;
		$this->blockFactory = $blockFactory;

		// TODO dependency injection of the Twig

		if( $config === null ) {
			$config = array();
		}

		$config += array(
			'debug' => false
		);

		$this->debug = $config['debug'];

		// init Twig
		$loader     = new \Twig_Loader_Filesystem( PATH_TO_ROOT );
		$this->twig = new \Twig_Environment( $loader, array(
			'cache'               => './tmp/',
			'debug'               => true,
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
		// load the block instances
		$instances = $this->blockLoader->loadBlockInstances();

		// create blocks builder
		$builderFactory = new BlocksBuildersFactory( $this->blockFactory );
		$blockBuilder   = $builderFactory->getBlocksBuilder( 'CrossDependenciesBuilder', $instances, true );

		$rootBlock = $this->pickRootBlock( $instances );
		if( $rootBlock === null ) {
			throw new BlockSetIntegrityException( "No root block found" );
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
		$blockBuilder->buildBlock( $rootBlock );

		$template = $this->twig->loadTemplate( $rootBlock->templateFile );
		$template->setBuilder( $blockBuilder );
		$template->setBlock( $rootBlock );

		return $template->render( $rootBlock->data );
	}

	/**
	 * Returns root block of set
	 *
	 * @return \inspirio\webBuilder\cBlockInstance
	 */
	protected function pickRootBlock( array $instances )
	{
		foreach( $instances as $instance ) {
			/* @var $instance BlockInstance */
			if( $instance->parent === null ) {
				return $instance;
			}
		}

		return null;
	}
}
