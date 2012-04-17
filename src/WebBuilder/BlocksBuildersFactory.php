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
	 * @var \Twig_Environment
	 */
	protected $twig;

	/**
	 * Constructs new BlockSetFacotry
	 *
	 * @param WebBlocksFactory  $blocksFactory
	 * @param \Twig_Environment $twig
	 */
	public function __construct( WebBlocksFactory $blocksFactory, \Twig_Environment $twig )
	{
		$this->blocksFactory = $blocksFactory;
		$this->twig          = $twig;
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
	 * Analyzes given blocks set and suggests which builder would fit best
	 *
	 * @param  BlockSet $blockSet
	 * @return string
	 */
	public static function analyzeBlockSet( BlockSet $blockSet )
	{
		// TODO
		return 'CrossDependenciesBuilder';
	}

	/**
	 * Creates blocks builder for given blocks set
	 *
	 * @param  BlockSet     $blockSet
	 * @param  bool           $forceAnalysis
	 * @return BlocksBuilderInterface
	 *
	 * @throws InvalidBuilderTypeException
	 */
	public function getBlocksBuilder( BlockSet $blockSet, $forceAnalysis = false )
	{
		$builderType = $blockSet->getBuilderType();

		// no pregenerated type or analysis forced
		if( $builderType == null || $forceAnalysis === true ) {
			$builderType = self::analyzeBlockSet( $blockSet );
		}

		// invalid builder type
		if( in_array( $builderType, self::getAvailableBuilders() ) === false ) {
			throw new InvalidBuilderTypeException( $builderType );
		}

		// create builder
		$builderClass = __NAMESPACE__ .'\\Builders\\'. $builderType;
		return new $builderClass( $this->blocksFactory );
	}
}