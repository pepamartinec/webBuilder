<?php

include_once 'common.php';

$pdo = new PDO( "mysql:host=localhost;dbname=webBuilder", USER, PASSWORD );
$pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

$bg = new WebBuilder\WebBuilder\BlocksGenerator( $pdo );

echo '<pre>';
$bg->registerBlocks( __DIR__.'/src/WebBuilder/Blocks/', true );
$bg->registerTemplates( __DIR__.'/templates/', true );