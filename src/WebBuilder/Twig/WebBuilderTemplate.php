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

	public function setBuilder( BlocksBuilderInterface $builder )
	{
		$this->builder = $builder;
	}

	public function setBlock( BlockInstance $block )
	{
		$this->block = $block;
	}
}