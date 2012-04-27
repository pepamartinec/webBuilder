<?php
namespace WebBuilder;

use ExtAdmin\Request\PayloadRequest;

include_once 'common.php';

$extAdmin = new \ExtAdmin\ExtAdmin();

// register DemoCMS modules factory
$database = new \Inspirio\Database\cDatabase( DATABASE, HOST, USER, PASSWORD );
$labels   = new \SimpleXMLElement('<root></root>');
$factory  = new \DemoCMS\Administration\cModuleFactory( $database, $labels );

$extAdmin->registerModuleFactory( '\\DemoCMS', $factory );

// register WebBuilder modules factory
$factory  = new \WebBuilder\Administration\ModuleFactory( $database, $labels );

$extAdmin->registerModuleFactory( '\\WebBuilder', $factory );

// handle client request
$request = new \ExtAdmin\Request\PayloadRequest();

$extAdmin->handleClientRequest( $request );