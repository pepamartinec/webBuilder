<?php
namespace WebBuilder;

use WebBuilder\DataObjects\BlockSet;

class BlocksBuildersFactory
{
	/**
	 * @var WebBlocksFactory
	 */
	protected $blocksFactory;

	/**
	 * Constructs new BlockSetFacotry
	 *
	 * @param WebBlocksFactory  $blocksFactory
	 * @param \Twig_Environment $twig
	 */
	public function __construct(WebBlocksFactory $blocksFactory)
	{
		$this->blocksFactory = $blocksFactory;
	}

	/**
	 * Returns array of avaliable blocks builders
	 *
	 * @return array
	 */
	public static final function getAvailableBuilders()
	{
		// hardcoded because builders has to be sorted with decreasing priority
		// TODO move to application settings, may vary from project to project
		return array(
			'SimpleBuilder',
			'CrossDependenciesBuilder'
		);
	}

	/**
	 * Analyzes given set of the block instances and suggests which builder would fit the best
	 *
	 * @param  array $instances
	 * @return string
	 */
	public static function analyzeBlockSet(array $instances)
	{
		// TODO
		return 'CrossDependenciesBuilder';
	}

	/**
	 * Creates blocks builder for given blocks set
	 *
	 * @param  BlockSet $blockSet
	 * @param  bool     $forceAnalysis
	 * @return BlocksBuilderInterface
	 *
	 * @throws InvalidBuilderTypeException
	 */
	public function getBlocksBuilder($builderType, array $instances, $forceAnalysis = false)
	{
		// no pregenerated type or analysis forced
		if ($builderType == null || $forceAnalysis === true) {
			$builderType = self::analyzeBlockSet($instances);
		}

		// invalid builder type
		if (in_array($builderType, self::getAvailableBuilders()) === false) {
			throw new InvalidBuilderTypeException($builderType);
		}

		// create builder
		$builderClass = __NAMESPACE__ .'\\Builders\\'. $builderType;
		return new $builderClass($this->blocksFactory);
	}
}