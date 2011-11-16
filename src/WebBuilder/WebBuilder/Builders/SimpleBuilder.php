<?php
namespace WebBuilder\WebBuilder\Builders;

use WebBuilder\WebBuilder\BlocksBuilderInterface;
use WebBuilder\WebBuilder\BlockInstance;
use WebBuilder\WebBuilder\WebBlocksFactoryInterface;

class SimpleBuilder implements BlocksBuilderInterface
{
	/**
	 * Blocks factory
	 *
	 * @var WebBlocksFactoryInterface
	 */
	protected $blocksFactory;

	/**
	 * @var \Twig_Environment
	 */
	protected $twig;

	/**
	 * Constructs new tree builder
	 *
	 * @param WebBlocksFactoryInterface  $blocksFactory
	 * @param \Twig_Environment  $twig
	 */
	public function __construct( WebBlocksFactoryInterface $blocksFactory, \Twig_Environment $twig )
	{
		$this->blocksFactory = $blocksFactory;
		$this->twig = $twig;
	}

	/**
	 * Renders given block
	 *
	 * @param BlockInstance $block
	 */
	public function renderBlock( BlockInstance $block )
	{
		// init required data
		$block->data = array();
		foreach( $block->dataDependencies as $property => $dependency ) {
			/* @var $dependency \WebBuilder\WebBuilder\DataDependencyInterface */

			$block->data[ $property ] = $dependency->getTargetData();
		}
		
		// setup provided data
		if( method_exists( $block->blockName, 'setupData' ) ) {
			$blockObj = $this->blocksFactory->createBlock( $block->blockName );

			$block->data += call_user_func_array( array( $blockObj, 'setupData' ), $block->data );
		}
		
		/* @var $template \WebBuilder\WebBuilder\Twig\WebBuilderTemplate */
		$template = $this->twig->loadTemplate( $block->template );
		
		// TODO ugly hack
		$template->setBuilder( $this );
		$template->setBlock( $block );

		return '<div class="block">'.
			'<span class="name">'.
				$block.
				'<pre class="data">'.print_r( $block->data, true ).'</pre>'.
			'</span>'.
			$template->render( $block->data ).'</div>';
	}

	/**
	 * Renders given block slot
	 *
	 * @param BlockInstance $block
	 * @param string         $slotName
	 * @param array          $runtimeData
	 */
	public function renderSlot( BlockInstance $block, $slotName, array $runtimeData = null )
	{
		if( !isset( $block->slots[ $slotName ] ) ) {
			return;
		}
		
		$originalData = null;
		if( $runtimeData !== null ) {
			$originalData =& $block->data;
			$block->data  =  $runtimeData + $block->data;
		}

		echo '<div class="slot"><span class="name">'.$slotName.'</span>';
		foreach( $block->slots[ $slotName ] as $innerBlock ) {
			echo $this->renderBlock( $innerBlock );
		}
		echo '</div>';
		
		if( $originalData !== null ) {
			$block->data =& $originalData;
		}
	}

	/**
	 * Tests whether builder is capable of rendering given blocks set
	 *
	 * @param array $blocks
	 */
	public function testBlocks( array $blocks )
	{
		return true;
	}
}