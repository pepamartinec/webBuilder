<?php
namespace WebBuilder;

use ExtAdmin\Request\FakeRequest;

include_once 'common.php';

$extAdmin = new \ExtAdmin\ExtAdmin();

// register Inspirio modules factory
$database = new \Inspirio\Database\cDatabase( DATABASE, HOST, USER, PASSWORD );
$labels   = new \SimpleXMLElement('<root></root>');
$factory  = new \Inspirio\ExtAdmin\cModuleFactory( $database, $labels );

$extAdmin->registerModuleFactory( '\\Inspirio', $factory );

// register WebBuilder modules factory
$factory  = new \WebBuilder\WebBuilder\ExtAdmin\ModulesFactory( $database, $labels );

$extAdmin->registerModuleFactory( '\\WebBuilder', $factory );

// handle client request
$module     = '\\WebBuilder\\WebBuilder\\ExtAdmin\\TemplatesManager\\TemplateEditor';
$action     = 'loadData_record';
$parameters = null;
$data       = array(
	'ID' => 8
);

$request = new \ExtAdmin\Request\FakeRequest( $module, $action, $parameters, $data );

$extAdmin->handleClientRequest( $request );