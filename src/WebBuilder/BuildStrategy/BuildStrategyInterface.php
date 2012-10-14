<?php
namespace WebBuilder\BuildStrategy;

use WebBuilder\BlockInstance;
use WebBuilder\BlockFactoryInterface;

/**
 * Blocks tree builder interface
 *
 * Used for implementation of various tree building algorythms
 *
 * @author Josef Martinec <joker806@gmail.com>
 */
interface BuildStrategyInterface
{
	/**
	 * Constructs new tree builder
	 *
	 * @param WebBlocksFactoryInterface $blocksFactory
	 */
	public function __construct(BlockFactoryInterface $blocksFactory);

	/**
	 * Renders given block
	 *
	 * @param BlockInstance $block
	 */
	public function buildBlock(BlockInstance $block);

	/**
	 * Tests whether builder is capable of rendering given blocks set
	 *
	 * @param array $blocks
	 */
	public function testBlocks(array $blocks);
}