<?php
namespace WebBuilder\Util\CodeAnalysis\PhpAnalysis\Reflections;

use WebBuilder\Util\CodeAnalysis\ReflectionInterface;

/**
 * Dummy reflection interface
 * 
 * Dummy reflection represents temporary substitute for real
 * reflection, that has been required somewhere, but its real
 * definition has not been found yet
 *  
 * Every dummy reflection class should implement this
 * 
 * @author Josef Martinec
 */
interface DummyReflectionInterface extends ReflectionInterface
{
	
}