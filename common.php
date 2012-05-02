<?php

// pagodabox
if( isset( $_SERVER["DB1_HOST"] ) ) {
	define( 'HOST', $_SERVER["DB1_HOST"] );
	define( 'USER', $_SERVER["DB1_USER"] );
	define( 'PASSWORD', $_SERVER["DB1_PASS"] );
	define( 'DATABASE', $_SERVER["DB1_NAME"] );

	define( 'BASE_HREF', 'http://web-builder.pagodabox.com/' );

// localhost
} else {
	define( 'HOST', 'localhost' );
	define( 'USER', 'root' );
	define( 'PASSWORD', '' );
	define( 'DATABASE', 'webBuilder' );

	define( 'BASE_HREF', 'http://dev-local/webBuilder/' );
}

// server paths
define( 'PATH_TO_ROOT', __DIR__.'/' );


// activate composer autoloading
require __DIR__.'/vendor/.composer/autoload.php';

function exception_error_handler($errno, $errstr, $errfile, $errline ) {
	throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}
set_error_handler("exception_error_handler");



function determineLanguage()
{
	return 'cs';
}

function findCommonPrefix( array $strings )
{
	$leader = array_shift( $strings );

	// find shortest string
	$minLen = strlen( $leader );
	foreach( $strings as $string ) {
		$len = strlen( $string );

		if( $len < $minLen ) {
			$minLen = $len;
		}
	}

	for( $i = 0; $i < $minLen; ++$i ) {
		foreach( $strings as $string ) {
			if( $string[ $i ] !== $leader[ $i ] ) {
				return $i - 1;
			}
		}
	}

	return $minLen;
}

function redirect( $location )
{
	header( 'Location: '.$location );
	exit;
}
