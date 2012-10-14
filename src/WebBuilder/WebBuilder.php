<?php
namespace WebBuilder;

use Inspirio\Database\cDBFeederBase;
use Inspirio\Database\cDatabase;

/**
 * Base WebBuilder class
 *
 * @author Josef Martinec <joker806@gmail.com>
 */
class WebBuilder
{
    const DEFAULT_FORM_TOKEN_KEY = 'wbfk';

	/**
	 * @var BlocksLoaderInterface
	 */
	private $blockLoader;

	/**
	 * @var WebBlocksFactoryInterface[]
	 */
	private $blockFactories;

	/**
	 * @var bool
	 */
	private $debug;

	/**
	 * @var string
	 */
	private $formTokenKey;

	/**
	 * @var Inspirio\Database\cDBFeederBase
	 */
	private $blockSetsFeeder;

	/**
	 * @var \Twig_Environment
	 */
	private $twig;

	/**
	 * Constructor
	 *
	 * @param \Database $database
	 */
	public function __construct(BlocksLoaderInterface $blockLoader, array $config = null)
	{
	    $this->blockFactories = array();

		$this->blockLoader  = $blockLoader;

		// TODO dependency injection of the Twig

		if($config === null) {
			$config = array();
		}

		$config += array(
			'debug'        => false,
		    'formTokenKey' => self::DEFAULT_FORM_TOKEN_KEY,
		);

		$this->debug        = $config['debug'];
		$this->formTokenKey = $config['formTokenKey'];

		// init Twig
		$loader     = new \Twig_Loader_Filesystem(PATH_TO_ROOT);
		$this->twig = new \Twig_Environment($loader, array(
			'cache'               => './tmp/',
			'debug'               => true,
			'base_template_class' => '\WebBuilder\Twig\WebBuilderTemplate'
		));

		$this->twig->addExtension(new Twig\WebBuilderExtension($this));
	}

	/**
	 * Registers the block factory
	 *
	 * @param BlockFactoryInterface $factory
	 * @return WebBuilder
	 */
	public function addBlockFactory(BlockFactoryInterface $factory)
	{
        $this->blockFactories[] = $factory;
        return $this;
	}

	/**
	 * Renders page for given web structure item.
	 *
	 * @param  WebPage $webPage
	 * @return mixed
	 *
	 * @throws BlockSetIntegrityException
	 * // TODO propably more exceptions
	 */
	public function render(WebPageInterface $webPage)
	{
		// load the block instances
		$instances = $this->blockLoader->loadBlockInstances();

		$rootBlock = $this->findRootBlock($instances);
		if ($rootBlock === null) {
			throw new BlockSetIntegrityException('No root block found');
		}

		// setup the root block data
		$rootData = array(
			'webPage' => $webPage
		);

		foreach ($rootBlock->dataDependencies as &$dependency) { /* @var $dependency DataDependencyInterface */
			$property = $dependency->getProperty();

			if(array_key_exists($property, $rootData)) {
				$dependency = new DataDependencies\ConstantData($property, $rootData[$property]);
			}
		}

		// create the blocks builder
		$builderFactory = new BlocksBuildersFactory($this->blockFactory);
		$blockBuilder   = $builderFactory->getBlocksBuilder('CrossDependenciesBuilder', $instances, true);

		// STEP 1: forms data proccessing
        // TODO use HttpFoundation instead of POST??
        if (isset($_POST[$this->getFormTokenKey()])) {
            $blockId = $_POST[$this->getFormTokenKey()];
            $block   = $this->findBlock($blockId);

            if ($block !== null) {
                $blockInstance = $this->blockFactory->createBlock($block->blockName);
                $returnValue   = $blockInstance->proccessFormData();

                if ($returnValue !== null) {
                    return $returnValue;
                }
            }
        }

		// STEP 2: build the blocks & solve data dependencies
		$blockBuilder->buildBlock($rootBlock);

		// STEP 3: blocks rendering
		$template = $this->twig->loadTemplate($rootBlock->templateFile);
		$template->setBuilder($blockBuilder);
		$template->setBlock($rootBlock);

		return $template->render($rootBlock->data);
	}

	/**
	 * Returns root block of the block set.
	 *
	 * @return BlockInstance|null
	 */
	private function findRootBlock(array $instances)
	{
		foreach ($instances as $instance) { /* @var $instance BlockInstance */
			if ($instance->parent === null) {
				return $instance;
			}
		}

		return null;
	}

	/**
	 * Finds the block identified by the Id.
	 *
	 * @param int $blockId
	 * @return BlockInstance|null
	 */
	private function findBlock($blockId)
	{
	    foreach ($instances as $instance) { /* @var $instance BlockInstance */
	        if ($instance->ID === $blockId) {
	            return $instance;
	        }
	    }

	    return null;
	}

	/**
	 * Returns the Twig environment instance.
	 *
	 * @return \Twig_Environment
	 */
	public function getTwig()
	{
	    return $this->twig;
	}

	/**
	 * Changes the form token key.
	 *
	 * @param string $tokenKey
	 */
	public function setFormTokenKey($tokenKey)
	{
	    $this->formTokenKey = $tokenKey;
	}

	/**
	 * Returns the block form token name.
	 *
	 * @return string
	 */
	public function getFormTokenKey()
	{
        return $this->formTokenKey;
	}

	/**
	 * Returns the block form token value.
	 *
	 * @return string
	*/
	public function getFormTokenValue(BlockInstance $block)
	{
        return $block->ID;
	}
}
