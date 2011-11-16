<?php
namespace WebBuilder\Util\CodeAnalysis\PhpAnalysis;

use WebBuilder\Util\CodeAnalysis\CodeAnalysisException;

class UnexpectedEndOfSourceException extends CodeAnalysisException
{
	public function __construct( $expected )
	{
		$tokenStr = is_string( $token ) ? $token : token_name( $token[0] );
		$msg = "Unexpected token end of source code";

		if( $expected !== null )
			$msg .= ', \''.token_name( $expected ).'\' expected';

		parent::__construct( $msg );
	}
}