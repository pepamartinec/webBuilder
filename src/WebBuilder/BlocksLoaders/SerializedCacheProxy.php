<?php
namespace WebBuilder\BlocksLoaders;

use WebBuilder\DataObjects\BlocksSet;
use WebBuilder\BlocksLoaderInterface;

class SerializedCacheProxy implements BlocksLoaderInterface
{
	/**
	 * @var \DBFeederBase
	 */
	protected $blocksSetsFeeder;

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
	public function __construct( \DBFeederBase $blocksSetsFeeder, BlocksLoaderInterface $loader )
	{
		$this->blocksSetsFeeder  = $blocksSetsFeeder;
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
	 * @param  BlocksSet $blocksSet         desired blocks set
	 * @param  bool       $forceRegeneration if TRUE, any cached data will be regenerated
	 * @return array                         array[ WebPageInterface ]
	 */
	public function fetchBlocksInstances( BlocksSet $blocksSet )
	{
		$blocksStructure = $blocksSet->getPregeneratedStructure( $blocksSetID );

		if( $blocksStructure !== null && $this->forceRegeneration === false ) {
			return unserialize( $blocksStructure );
		}

		$blocksSet->setPregeneratedStructure( $this->blocksSetsFeeder->database()->escape( serialize( $blocksStructure ) ) );
		$this->blocksSetsFeeder->save( $blocksSet );

		return $blocksStructure;
	}
}