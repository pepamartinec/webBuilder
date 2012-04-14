<?php
namespace WebBuilder\BlocksLoaders;

use WebBuilder\DataObjects\BlockSet;
use WebBuilder\BlocksLoaderInterface;

class SerializedCacheProxy implements BlocksLoaderInterface
{
	/**
	 * @var \DBFeederBase
	 */
	protected $blockSetsFeeder;

	/**
	 * @var BlocksLoaderInterface
	 */
	protected $loader;

	/**
	 * @var bool
	 */
	protected $forceRegeneration;

	/**
	 * Constructs
	 *
	 * @param BlocksLoaderInterface $loader
	 */
	public function __construct( \DBFeederBase $blockSetsFeeder, BlocksLoaderInterface $loader )
	{
		$this->blockSetsFeeder  = $blockSetsFeeder;
		$this->loader            = $loader;
		$this->forceRegeneration = false;
	}

	/**
	 * Sets whether proxy should fetch fresh data or use cached one
	 *
	 * @param bool $force
	 */
	public function setForceRegeneration( $force )
	{
		$this->forceRegeneration = $force;
	}

	/**
	 * Return complete blocks structure for given blocks set
	 *
	 * @param  BlockSet $blockSet         desired blocks set
	 * @param  bool       $forceRegeneration if TRUE, any cached data will be regenerated
	 * @return array                         array[ WebPageInterface ]
	 */
	public function fetchBlocksInstances( BlockSet $blockSet )
	{
		$blocksStructure = $blockSet->getPregeneratedStructure();

		if( $blocksStructure !== null && $this->forceRegeneration === false ) {
			return unserialize( $blocksStructure );
		}

		$blockSet->setPregeneratedStructure( $this->blockSetsFeeder->database()->escape( serialize( $blocksStructure ) ) );
		$this->blockSetsFeeder->save( $blockSet );

		return $blocksStructure;
	}
}