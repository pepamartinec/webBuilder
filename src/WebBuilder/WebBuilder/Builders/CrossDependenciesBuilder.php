<?php
namespace WebBuilder\WebBuilder\Builders;

use WebBuilder\WebBuilder\BlocksBuilderInterface;
use WebBuilder\WebBuilder\BlockInstance;
use WebBuilder\WebBuilder\WebBlocksFactoryInterface;

class CrossDependenciesBuilder implements BlocksBuilderInterface
{
	const S_FRESH    = 1;
	const S_INIT     = 2;
	const S_READY    = 3;

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
	 * @var array
	 */
	protected $states;

	/**
	 * @var array
	 */
	protected $dependencies;

	/**
	 * Constructs new tree builder
	 *
	 * @param WebBlocksFactoryInterface  $blocksFactory
	 * @param \Twig_Environment  $twig
	 */
	public function __construct( WebBlocksFactoryInterface $blocksFactory, \Twig_Environment $twig )
	{
		$this->blocksFactory = $blocksFactory;
		$this->twig          = $twig;
		$this->states        = array();
		$this->dependencies  = array();
	}

	public function initBlock( BlockInstance $block )
	{
		// touch block state
		if( isset( $this->states[ $block->ID ] ) === false ) {
			$this->states[ $block->ID ] = self::S_FRESH;
		}

		$blockState =& $this->states[ $block->ID ];

		// block already initialized
		if( $blockState === self::S_READY ) {
			return;
		}

		// setup required data
		$blockState       = self::S_INIT;
		$initDependencies = isset( $this->dependencies[ $block->ID ] ) === false;
		$block->data      = array();

		if( $initDependencies ) {
			$this->dependencies[ $block->ID ] = array();
		}

		foreach( $block->dataDependencies as $property => $dependency ) {
			/* @var $dependency \WebBuilder\WebBuilder\DataDependencyInterface */

			// check provider state
			$provider = $dependency->getProvider();
			if( $provider !== null ) {
				// touch provider state
				if( isset( $this->states[ $provider->ID ] ) === false ) {
					$this->states[ $provider->ID ] = self::S_FRESH;
				}

				// data provider not initialized yet
				if( $this->states[ $provider->ID ] !== self::S_READY ) {
					$this->initBlock( $provider );
				}
			}

			if( $initDependencies ) {
				$this->dependencies[ $provider->ID ][] = $block->ID;
			}

			// pick data
			$block->data[ $property ] = $dependency->getTargetData();
		}

		// setup provided data
		if( method_exists( $block->blockName, 'setupData' ) ) {
			$blockObj = $this->blocksFactory->createBlock( $block->blockName );

			$block->data += call_user_func_array( array( $blockObj, 'setupData' ), $block->data );
		}

		$blockState = self::S_READY;
	}

	/**
	 * Renders given block
	 *
	 * @param BlockInstance $block
	 */
	public function renderBlock( BlockInstance $block )
	{
		$this->initBlock( $block );

		/* @var $template \WebBuilder\WebBuilder\Twig\WebBuilderTemplate */
		$template = $this->twig->loadTemplate( $block->templateFile );

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

	protected function invalidateBlock( $instanceID )
	{
		$this->states[ $instanceID ] = self::S_FRESH;

		if( isset( $this->dependencies[ $instanceID ] ) ) {
			foreach( $this->dependencies[ $instanceID ] as $dependentID ) {
				$this->invalidateBlock( $dependentID );
			}
		}
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
			/* @var $innerBlock \WebBuilder\WebBuilder\BlockInstance */

			// invalidate dependent blocks
			if( $runtimeData !== null ) {
				$this->invalidateBlock( $innerBlock->ID );
			}

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