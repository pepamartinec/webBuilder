<?php
namespace WebBuilder\Twig;

use WebBuilder\WebBuilderInterface;

class WebBuilderExtension extends \Twig_Extension
{
	protected $webBuilder;

	public function __construct( WebBuilderInterface $webBuilder )
	{
		$this->webBuilder = $webBuilder;
	}

	public function getTokenParsers()
	{
		return array(
			new SlotTokenParser(),
			new ContainerTokenParser()
		);
	}

	public function getName()
	{
		return 'WebBuilder';
	}

	public function getWebBuilder()
	{
		return $this->webBuilder;
	}
}