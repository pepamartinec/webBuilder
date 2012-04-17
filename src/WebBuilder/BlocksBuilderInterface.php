<?php
namespace WebBuilder;

/**
 * Blocks tree builder
 *
 * Used for implementation of various tree
 * building algorythms
 */
interface BlocksBuilderInterface
{
	/**
	 * Constructs new tree builder
	 *
	 * @param WebBlocksFactoryInterface $blocksFactory
	 */
	public function __construct( WebBlocksFactoryInterface $blocksFactory );

	/**
	 * Renders given block
	 *
	 * @param BlockInstance $block
	 */
	public function buildBlock( BlockInstance $block );

	/**
	 * Tests whether builder is capable of rendering given blocks set
	 *
	 * @param array $blocks
	 */
	public function testBlocks( array $blocks );
}