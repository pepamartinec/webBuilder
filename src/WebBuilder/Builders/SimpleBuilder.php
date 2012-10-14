<?php
namespace WebBuilder\Builders;

use WebBuilder\BlocksBuilderInterface;
use WebBuilder\BlockInstance;
use WebBuilder\WebBlocksFactoryInterface;

/**
 * Simple blocks builder
 *
 * The fastest and simplest builder variant. Is intended
 * to use for blockSets with plain tree data dependencies
 * (no cross-dependencies).
 *
 * @author Josef Martinec <joker806@gmail.com>
 */
class SimpleBuilder implements BlocksBuilderInterface
{
	/**
	 * Blocks factory
	 *
	 * @var WebBlocksFactoryInterface
	 */
	protected $blocksFactory;

	/**
	 * Constructs new tree builder
	 *
	 * @param WebBlocksFactoryInterface $blocksFactory
	 */
	public function __construct(WebBlocksFactoryInterface $blocksFactory)
	{
		$this->blocksFactory = $blocksFactory;
	}

	/**
	 * Renders given block
	 *
	 * @param BlockInstance $block
	 */
	public function buildBlock(BlockInstance $block)
	{
		// init required data
		$block->data = array();
		foreach ($block->dataDependencies as $property => $dependency) {
			/* @var $dependency \WebBuilder\DataDependencyInterface */
			$block->data[$property] = $dependency->getTargetData();
		}

		// setup provided data
		if (method_exists($block->blockName, 'setupData')) {
			$blockObj = $this->blocksFactory->createBlock($block->blockName);

			$block->data += call_user_func_array(array($blockObj, 'setupRenderData'), $block->data);
		}
	}

	/**
	 * Tests whether builder is capable of rendering given blocks set
	 *
	 * @param array $blocks
	 */
	public function testBlocks(array $blocks)
	{
		return true;
	}
}