<?php
namespace WebBuilder\Util\CodeAnalysis\PhpAnalysis\Reflections;

use WebBuilder\Util\CodeAnalysis\ReflectionInterface;

/**
 * Static reflection interface
 * 
 * Static reflection represents reflection created by only parsing
 * of source code
 * 
 * Every dummy reflection class should implement this
 * 
 * @author Josef Martinec
 */
interface StaticReflectionInterface extends ReflectionInterface
{
	
}