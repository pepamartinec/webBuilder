<?php

namespace WebBuilder\Util\CodeAnalysis\TwigAnalysis;

use WebBuilder\Util\CodeAnalysis\iPassiveReflection;

class ReflectionFactory
{
	/**
	 * @var array
	 */
	protected $templates;
	
	/**
	 * @var array
	 */
	protected $analyzedFiles;
	
	/**
	 * Constructs new analyzer
	 */
	public function __construct()
	{
		$this->templates     = array();
		$this->analyzedFiles = array();
	}
	
	/**
	 * Analyzes whole directory
	 *
	 * @param string $dirName
	 * @param bool   $recursive
	 */
	public function analyzeDirectory( $dirName, $recursive = true )
	{
		$iterator = $recursive ?
			new \RecursiveIteratorIterator( new \RecursiveDirectoryIterator( $dirName ) ) :
			new \DirectoryIterator( $dirName );

		foreach( $iterator as $item ) {
			if( $item->isDir() ) {
				continue;
			}

			if( preg_match( '/\.twig/i', $item->getFilename() ) == 0 ) {
				continue;
			}

			$this->analyzeFile( $item->getPathname() );
		}
	}

	/**
	 * Analyzes whole file
	 *
	 * @param string $fileName
	 */
	public function analyzeFile( $fileName )
	{
		if( in_array( $fileName, $this->analyzedFiles ) ) {
			return;
		}

		if( is_file( $fileName ) === false ) {
			throw new InvalidFileException( $fileName );
		}

		$this->analyzedFiles[] = $fileName;

		$this->templates[] = new TemplateReflection( $this, $fileName );
	}
	
	/**
	 * Return templates iterator
	 *
	 * @return \Iterator
	 */
	public function getNamespacesIterator()
	{
		return new \ArrayIterator( $this->templates );
	}
}