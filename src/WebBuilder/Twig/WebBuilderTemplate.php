<?php
namespace WebBuilder\Twig;

use WebBuilder\BlockInstance;
use WebBuilder\BlocksBuilderInterface;

abstract class WebBuilderTemplate extends \Twig_Template
{
	/**
	 * @var BlocksBuilderInterface
	 */
	protected $builder;

	/**
	 * @var BlockInstance
	 */
	protected $block;

	/**
	 * Sets the blocks builder
	 *
	 * @param BlocksBuilderInterface $builder
	 */
	public function setBuilder( BlocksBuilderInterface $builder )
	{
		$this->builder = $builder;
	}

	/**
	 * Sets the block instance
	 *
	 * @param BlockInstance $block
	 */
	public function setBlock( BlockInstance $block )
	{
		$this->block = $block;
	}

	public function render( array $context )
	{
		return parent::render( $context );
	}

	/**
	 * Renders given block slot
	 *
	 * @param BlockInstance $block
	 * @param string $slotName
	 * @param array $runtimeData
	 */
	public function renderSlot( $slotName, array $runtimeData = null )
	{
		// invalid/empty slot
		if( ! isset( $this->block->slots[ $slotName ] ) ) {
			return;
		}

		$hasRuntimeData = $runtimeData !== null;

		$originalData = null;
		if( $hasRuntimeData ) {
			$originalData =& $this->block->data;
			$this->block->data = $runtimeData + $this->block->data;
		}

		ob_start();
		foreach( $this->block->slots[ $slotName ] as $innerBlock ) {
			/* @var $innerBlock \WebBuilder\BlockInstance */
			$this->builder->buildBlock( $innerBlock, $hasRuntimeData );

			$template = $this->env->loadTemplate( $innerBlock->templateFile );
			$template->setBuilder( $this->builder );
			$template->setBlock( $innerBlock );

			echo '<div class="block">'. $template->render( $innerBlock->data ) .'</div>';
		}

		if( $hasRuntimeData ) {
			$this->block->data =& $originalData;
		}

		return ob_get_clean();
	}
}