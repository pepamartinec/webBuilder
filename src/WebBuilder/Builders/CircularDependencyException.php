<?php
namespace WebBuilder\Builders;

use WebBuilder\BlockInstance;

use inspirio\BuilderException;

class CircularDependencyException extends \RuntimeException
{
	public function __construct(BlockInstance $block, $property)
	{
		parent::__construct("Circular dependency detected at '{$block}', property '{$property}'");
	}
}