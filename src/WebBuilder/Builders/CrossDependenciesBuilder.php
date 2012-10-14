<?php
namespace WebBuilder\Builders;

use WebBuilder\BlocksBuilderInterface;
use WebBuilder\BlockInstance;
use WebBuilder\WebBlocksFactoryInterface;

class CrossDependenciesBuilder implements BlocksBuilderInterface
{
	const S_FRESH = 1;
	const S_INIT  = 2;
	const S_READY = 3;

	/**
	 * @var WebBlocksFactoryInterface
	 */
	private $blocksFactory;

	/**
	 * @var array
	 */
	private $states;

	/**
	 * @var array
	 */
	private $dependencies;

	/**
	 * Constructs new tree builder
	 *
	 * @param WebBlocksFactoryInterface $blocksFactory
	 */
	public function __construct(WebBlocksFactoryInterface $blocksFactory)
	{
		$this->blocksFactory = $blocksFactory;
		$this->states        = array();
		$this->dependencies  = array();
	}

	/**
	 * @see \WebBuilder\BlocksBuilderInterface::buildBlock()
	 */
	public function buildBlock(BlockInstance $block, $dataModified = false)
	{
	    // invalidate the dependent blocks
	    if($dataModified) {
	        $this->invalidateBlock($block->ID);
	    }

	    $this->initBlock($block);
	}

	/**
	 * @see \WebBuilder\BlocksBuilderInterface::testBlocks()
	 */
	public function testBlocks(array $blocks)
	{
	    return true;
	}

	/**
	 * Initializes the block instance.
	 *
	 * @param BlockInstance $block
	 */
	private function initBlock(BlockInstance $block)
	{
		// touch the block state
		if (isset($this->states[$block->ID]) === false) {
			$this->states[$block->ID] = self::S_FRESH;
		}

		$blockState =& $this->states[$block->ID];

		// the block was already initialized
		if ($blockState === self::S_READY) {
			return;
		}

		// setup required data
		$blockState       = self::S_INIT;
		$block->data      = array();

		if (!isset($this->dependencies[$block->ID])) {
			$this->dependencies[$block->ID] = array();
		}

		foreach ($block->dataDependencies as $property => $dependency) {
			/* @var $dependency \WebBuilder\DataDependencyInterface */

			$provider = $dependency->getProvider();

			if ($provider !== null) {
				// touch the data provider state
				if (!isset($this->states[$provider->ID])) {
					$this->states[$provider->ID] = self::S_FRESH;
				}

				// the data provider was not initialized yet
				if ($this->states[$provider->ID] !== self::S_READY) {
					$this->initBlock($provider);
				}
			}

			// pick data
			$block->data[$property] = $dependency->getTargetData();
		}

		// setup the block data
		$blockInstance = $this->blocksFactory->createBlock($block->blockName);
		$block->data  += call_user_func_array(array($blockObj, 'setupRenderData'), $block->data);

		$blockState = self::S_READY;
	}

	/**
	 * Invalidates the block and all its dependants.
	 *
	 * @param int $instanceID
	 */
	private function invalidateBlock($instanceID)
	{
	    $this->states[$instanceID] = self::S_FRESH;

	    if (isset( $this->dependencies[$instanceID])) {
	        foreach ($this->dependencies[$instanceID] as $dependentID) {
	            $this->invalidateBlock($dependentID);
	        }
	    }
	}
}