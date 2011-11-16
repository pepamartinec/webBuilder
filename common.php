<?php

define( 'HOST', 'localhost' );
define( 'USER', 'root' );
define( 'PASSWORD', 'joker123' );
define( 'DATABASE', 'webBuilder' );
define( 'AUTH_DATABASE_NAME', 'webBuilder' );

define( 'PATH_TO_ROOT', __DIR__.'/' );

define( 'PATH_TO_WEBSERVER_ROOT', __DIR__.'/../' );
define( 'PATH_FROM_ROOT_TO_BASE', 'webBuilder/' );

require __DIR__.'/vendor/.composer/autoload.php';

function autoload( $className )
{
	if( strpos( $className, '\\' ) ) {
		
		switch( substr( $className, 0, 1 ) ) {
	//		case 'b': $dir = 'blocks'; break;
	//		case 'd': $dir = 'classes/DataObjects'; break;
			default:  $dir = 'classes'; break;
		}
	
		$nameParts = explode( '\\', $className );
		$filePath  = __DIR__ . '/'. $dir .'/'. implode( '/', $nameParts ) .'.php';
	
		include $filePath;
		
	} else {
		include __DIR__.'/classes/'.$className.'.inc.php';
	}
}

//spl_autoload_register( 'autoload' );





function safeInput( $what, $type = 'string' )
{
	return $what;
}

function determineLanguage()
{
	return 'cs';
}