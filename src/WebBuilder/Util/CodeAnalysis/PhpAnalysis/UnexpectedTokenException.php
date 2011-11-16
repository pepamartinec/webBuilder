<?php
namespace WebBuilder\Util\CodeAnalysis\PhpAnalysis;

use WebBuilder\Util\CodeAnalysis\CodeAnalysisException;

class UnexpectedTokenException extends CodeAnalysisException
{
	public function __construct( array $token, $expected = null )
	{
		$tokenStr = is_string( $token ) ? $token : token_name( $token[0] );
		$msg = "Unexpected token {$tokenStr}";

		if( $expected !== null )
			$msg .= ', '.token_name( $expected ).' expected';

		parent::__construct( $msg );
	}
}