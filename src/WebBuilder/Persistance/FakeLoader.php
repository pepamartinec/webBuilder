<?php
namespace WebBuilder\Persistance;

class FakeLoader implements BlocksLoaderInterface
{
	/**
	 * @var array
	 */
	protected $instances;

	/**
	 * Constructs
	 *
	 * @param \Database $blockSetsFeeder
	 */
	public function __construct(array $instances)
	{
		$this->instances = $instances;
	}

	/**
	 * Loads the block instances
	 *
	 * @return array
	 *
	 * @throws InvalidBlockSetException
	 */
	public function loadBlockInstances()
	{
		return $this->instances;
	}
}