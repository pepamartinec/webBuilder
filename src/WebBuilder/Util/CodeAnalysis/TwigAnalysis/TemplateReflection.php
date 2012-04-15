<?php
namespace WebBuilder\Util\CodeAnalysis\TwigAnalysis;

class TemplateReflection implements TemplateReflectionInterface
{
	protected $pathname;

	protected $parentBlock;

	protected $slots;

	protected $code;

	/**
	 * Constructor
	 *
	 * @param ReflectionFactory $analyzer
	 * @param array $repository
	 * @param string $filename
	 */
	public function __construct( ReflectionFactory $analyzer, $repository, $filename )
	{
		$baseNs  = $repository['namespace'];
		$baseDir = $repository['baseDir'];
		$tplDir  = $repository['tplDir'];

		$this->pathname = realpath( $filename );
		$this->pathname = substr( $this->pathname, strlen( $baseDir ) );

		$code = file_get_contents( $filename );

		// pick parent block
		$pattern = '/^\{% container (?P<name>[^ ]+) %\}/i';
		$matches = array();
		preg_match( $pattern, $code, $matches );

		$localPath      = substr( $this->pathname, strlen( $tplDir ), -( strlen( basename( $this->pathname ) ) + 1 ) );
		$blockNameParts = explode( '/', $localPath );

		array_unshift( $blockNameParts, $baseNs );
		array_push( $blockNameParts, $matches['name'] );
		array_walk( $blockNameParts, function( $value, $key ) { return ucfirst( $value ); } );

		$this->parentBlock = implode( '\\', $blockNameParts );


		// pick defined slots
		$pattern = '/\{% \s+ slot \s+ (?P<name>[^ ]+) (?: \s+ with \s+ \[ [^\[\]]+ \] )? \s+ %\}/xi';
		preg_match_all( $pattern, $code, $matches );

		$this->slots = $matches['name'];
	}

	/**
	 * (non-PHPdoc)
	 * @see Reflector::export()
	 */
	public static function export()
	{

	}

	/**
	 * (non-PHPdoc)
	 * @see Reflector::__toString()
	 */
	public function __toString()
	{
		return $this->filename;
	}

	public function getSlots()
	{
		return $this->slots;
	}

	public function getParentBlock()
	{
		return $this->parentBlock;
	}

	public function getPathname()
	{
		return $this->pathname;
	}

	public function getName()
	{
		return basename( $this->pathname );
	}
}