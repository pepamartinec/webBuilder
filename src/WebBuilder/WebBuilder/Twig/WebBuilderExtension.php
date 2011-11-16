<?php
namespace WebBuilder\WebBuilder\Twig;

use WebBuilder\WebBuilder\WebBuilderInterface;

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
			new tSlotTokenParser(),
			new tContainerTokenParser()
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