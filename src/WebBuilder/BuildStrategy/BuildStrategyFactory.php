<?php
namespace WebBuilder\BuildStrategy;

use WebBuilder\DataObjects\BlockSet;

class BuildStrategyFactory
{
	/**
	 * @var BlockFactoryInterface[]
	 */
	protected $blockFactories;

	/**
	 * Constructs new BlockSetFacotry
	 *
	 * @param BlockFactoryInterface[] $blockFactories
	 */
	public function __construct(array $blockFactories)
	{
		$this->blockFactories = $blockFactories;
	}

	/**
	 * Returns an array of avaliable blocks builders
	 *
	 * @return array
	 */
	public static final function getAvailableBuilders()
	{
		// hardcoded because builders has to be sorted with decreasing priority
		// TODO move to application settings, may vary from project to project
		return array(
			'SimpleStrategy',
			'CrossDependenciesStrategy'
		);
	}

	/**
	 * Analyzes the block instace set and suggests which builder would fit the best.
	 *
	 * @param  BlockSet $blockSet
	 * @return string
	 */
	public function analyzeBlockSet(BlockSet $blockSet)
	{
		// TODO
		return 'CrossDependenciesStrategy';
	}

	/**
	 * Creates blocks builder for given blocks set
	 *
	 * @param  BlockSet $blockSet
	 * @param  bool     $forceAnalysis
	 * @return BuildStrategyInterface
	 *
	 * @throws InvalidBuilderTypeException
	 */
	public function getBuildStrategy(BlockSet $blockSet, $forceAnalysis = false)
	{
	    $strategy = $blockSet->getBuildStrategy();

		// no pregenerated type or analysis forced
		if ($strategy == null || $forceAnalysis === true) {
			$strategy = $this->analyzeBlockSet($blockSet);
		}

		// invalid builder type
		if (!in_array($strategy, self::getAvailableBuilders())) {
			throw new InvalidBuildStrategyException($strategy);
		}

		// create builder
		$builderClass = __NAMESPACE__ . $strategy;
		return new $builderClass($this->blockFactories);
	}
}