<?php
namespace WebBuilder\BuildStrategy;

use WebBuilder\BlockInstance;

/**
 * Thrown when circular data dependency between some blocks is found.
 *
 * @author Josef Martinec <joker806@gmail.com>
 */
class CircularDependencyException extends \RuntimeException
{
	public function __construct(BlockInstance $block, $property)
	{
		parent::__construct("Circular dependency detected at '{$block}', property '{$property}'");
	}
}