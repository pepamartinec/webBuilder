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
	 * @param string            $filename template filename
	 */
	public function __construct( ReflectionFactory $analyzer, $filename )
	{
		$this->pathname = realpath( $filename );

		// TODO right path matching
		$this->pathname = substr( $this->pathname, strlen( PATH_TO_ROOT ) );

		$code = file_get_contents( $filename );

		// pick parent block
		$pattern = '/^\{% container (?P<name>[^ ]+) %\}/i';
		preg_match( $pattern, $code, $matches );

		// TODO implement right matching
		$localPath = substr( $this->pathname, strlen( 'templates/' ), -( strlen( basename( $this->pathname ) ) + 1 ) );
		$pathParts = explode( '/', $localPath );
		
		foreach( $pathParts as &$part ) {
			$part = ucfirst( $part );
		}

		$this->parentBlock = '\\WebBuilder\\Blocks\\'. implode( '\\', $pathParts ) .'\\'. $matches['name'];


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