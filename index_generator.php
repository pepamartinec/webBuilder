<?php

include_once 'common.php';

$pdo = new PDO( "mysql:host=localhost;dbname=webBuilder", USER, PASSWORD );
$pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

$generator = new WebBuilder\BlocksGenerator( $pdo );

echo '<pre>';

$pdo->beginTransaction();

try {
	// register blocks
	$dirs = array(
		__DIR__.'/src/WebBuilder/Blocks/',
		__DIR__.'/src/Inspirio/BuilderBlocks/'
	);

	$generator->registerBlocks( $dirs );


	// register templates
	$dirs = array(
		array(
			'namespace' => '\\WebBuilder\\Blocks',
			'baseDir'   => __DIR__,
			'tplDir'    => 'src/WebBuilder/Templates/'
		),

		array(
			'namespace' => '\\Inspirio\\BuilderBlocks',
			'baseDir'   => __DIR__,
			'tplDir'    => 'src/Inspirio/BuilderTemplates/'
		),
	);

	$generator->registerTemplates( $dirs );

	$pdo->commit();

} catch ( Exception $e ) {
	$pdo->rollBack();
	throw $e;
}