<?php
namespace WebBuilder;

use ExtAdmin\Request\PayloadRequest;

include_once 'common.php';

$extAdmin = new \ExtAdmin\ExtAdmin();

// register Inspirio modules factory
$database = new \Inspirio\Database\cDatabase( DATABASE, HOST, USER, PASSWORD );
$labels   = new \SimpleXMLElement('<root></root>');
$factory  = new \Inspirio\ExtAdmin\cModuleFactory( $database, $labels );

$extAdmin->registerModuleFactory( '\\Inspirio', $factory );

// register WebBuilder modules factory
$factory  = new \WebBuilder\ExtAdmin\ModulesFactory( $database, $labels );

$extAdmin->registerModuleFactory( '\\WebBuilder', $factory );

// handle client request
$request = new \ExtAdmin\Request\PayloadRequest();

$extAdmin->handleClientRequest( $request );