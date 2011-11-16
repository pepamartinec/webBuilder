<?php
namespace WebBuilder\WebBuilder\Builders;

use WebBuilder\WebBuilder\BlockInstance;

use inspirio\BuilderException;

class CircularDependencyException extends BuilderException
{
	public function __construct( BlockInstance $block, $property )
	{
		parent::__construct("Circular dependency detected at '{$block}', property '{$property}'");
	}
}