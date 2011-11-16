<?php
namespace WebBuilder\Util\CodeAnalysis;

class InvalidFileException extends CodeAnalysisException
{
	public function __construct( $fileName, \Exception $previous = null )
	{
		parent::__construct( "File '{$fileName}' not found", $previous );
	}
}