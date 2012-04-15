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
	 * @param array $repository
	 */
	public function analyzeDirectory( $repository )
	{
		$baseNs  = &$repository['namespace'];
		$baseDir = &$repository['baseDir'];
		$tplDir  = &$repository['tplDir'];

		// normalize paths
		if( substr( $baseDir, -1 ) !== '/' ) {
			$baseDir .= '/';
		}

		if( $tplDir === '/' ) {
			$tplDir = substr( $tplDir, 1 );
		}

		if( substr( $tplDir, -1 ) !== '/' ) {
			$tplDir .= '/';
		}

		$iterator = new \RecursiveIteratorIterator( new \RecursiveDirectoryIterator( $baseDir.'/'.$tplDir ) );

		foreach( $iterator as $item ) {
			if( $item->isDir() ) {
				continue;
			}

			if( preg_match( '/\.twig/i', $item->getFilename() ) == 0 ) {
				continue;
			}

			$this->analyzeFile( $repository, $item->getPathname() );
		}
	}

	/**
	 * Analyzes whole file
	 *
	 * @param array  $repository
	 * @param string $fileName
	 */
	public function analyzeFile( $repository, $fileName )
	{
		if( in_array( $fileName, $this->analyzedFiles ) ) {
			return;
		}

		if( is_file( $fileName ) === false ) {
			throw new InvalidFileException( $fileName );
		}

		$this->analyzedFiles[] = $fileName;

		$this->templates[] = new TemplateReflection( $this, $repository, $fileName );
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