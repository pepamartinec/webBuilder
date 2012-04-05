<?php

define( 'HOST', 'localhost' );
define( 'USER', 'root' );
define( 'PASSWORD', '' );
define( 'DATABASE', 'webBuilder' );
define( 'AUTH_DATABASE_NAME', 'webBuilder' );

define( 'PATH_TO_ROOT', __DIR__.'/' );

define( 'PATH_TO_WEBSERVER_ROOT', __DIR__.'/../' );
define( 'PATH_FROM_ROOT_TO_BASE', 'webBuilder/' );

define( 'BASE_HREF', 'http://dev-local/'. PATH_FROM_ROOT_TO_BASE );

require __DIR__.'/vendor/.composer/autoload.php';

function exception_error_handler($errno, $errstr, $errfile, $errline ) {
	throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}
set_error_handler("exception_error_handler");



function safeInput( $what, $type = 'string' )
{
	return $what;
}

function determineLanguage()
{
	return 'cs';
}