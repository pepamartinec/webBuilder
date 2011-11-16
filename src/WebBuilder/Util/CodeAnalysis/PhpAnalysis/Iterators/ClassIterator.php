<?php
namespace WebBuilder\Util\CodeAnalysis\PhpAnalysis\Iterators;

use WebBuilder\Util\CodeAnalysis\PhpAnalysis\Reflections\iReflectionNamespace;

class NamespaceIterator implements \RecursiveIterator
{
	/**
	 * @var iReflectionNamespace
	 */
	protected $root;

	/**
	 * @var iReflectionNamespace
	 */
	protected $current;

	public function __construct( array $namespaces )
	{
		$names = array_keys( $namespaces );
	}

	public function hasChildren()
	{
	//	$this->current->
	}

	public function getChildren () {}

	public function current () {}

	public function next () {}

	public function key () {}

	public function valid () {}

	public function rewind () {}
}