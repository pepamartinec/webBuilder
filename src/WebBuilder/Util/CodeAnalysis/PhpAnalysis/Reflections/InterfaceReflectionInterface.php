<?php
namespace WebBuilder\Util\CodeAnalysis\PhpAnalysis\Reflections;

use WebBuilder\Util\CodeAnalysis\ReflectionInterface;

interface InterfaceReflectionInterface extends ReflectionInterface
{
	/**
	 * Returns definition file name
	 *
	 * @return string|null
	 */
	public function getDefinitionFile();

	/**
	 * Returns line number within definition file
	 *
	 * @return int|null
	 */
	public function getStartLine();

	/**
	 * Returns containing namespace
	 *
	 * @return iReflectionNamespace
	 */
	public function getNamespace();

	/**
	 * Returns interface name
	 *
	 * @return string
	 */
	public function getName();

	/**
	 * Returns fully qualified interface name
	 *
	 * @return string
	 */
	public function getFullName();

	/**
	 * Returns direct parent interface
	 *
	 * @return iReflectionInterface|null
	 */
	public function getParent();

	/**
	 * Checks, wheter interface has given parent
	 *
	 * @param  string $parentName
	 * @return bool
	 */
	public function hasParent( $parentName );

	/**
	 * Returns interfaces (this and every parent)
	 *
	 * @return array
	 */
	public function getInterfaces();
}
