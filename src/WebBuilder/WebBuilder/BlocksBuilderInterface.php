<?php
namespace WebBuilder\WebBuilder;

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
	 * @param WebBlocksFactoryInterface  $blocksFactory
	 * @param \Twig_Environment  $twig
	 */
	public function __construct( WebBlocksFactoryInterface $blocksFactory, \Twig_Environment $twig );

	/**
	 * Renders given block
	 *
	 * @param BlockInstance $block
	 */
	public function renderBlock( BlockInstance $block );

	/**
	 * Tests whether builder is capable of rendering given blocks set
	 *
	 * @param array $blocks
	 */
	public function testBlocks( array $blocks );
}