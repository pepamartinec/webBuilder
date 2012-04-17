<?php
namespace WebBuilder\Builders;

use WebBuilder\BlocksBuilderInterface;
use WebBuilder\BlockInstance;
use WebBuilder\WebBlocksFactoryInterface;

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
	 * @param WebBlocksFactoryInterface $blocksFactory
	 */
	public function __construct( WebBlocksFactoryInterface $blocksFactory )
	{
		$this->blocksFactory = $blocksFactory;
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
			/* @var $dependency \WebBuilder\DataDependencyInterface */

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

// FIXME this was commented, because provider can be null in this section
// is this important or can it be just removed?
//			if( $initDependencies ) {
//				$this->dependencies[ $provider->ID ][] = $block->ID;
//			}

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
	 * Renders given block
	 *
	 * @param BlockInstance $block
	 * @param bool $dataModified
	 * @return \WebBuilder\Twig\WebBuilderTemplate
	 */
	public function buildBlock( BlockInstance $block, $dataModified = false )
	{
		// invalidate dependent blocks
		if( $dataModified ) {
			$this->invalidateBlock( $block->ID );
		}

		$this->initBlock( $block );
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