<?php

use WebBuilder\WebBuilder;

use WebBuilder\WebBlocksFactory;

use WebBuilder\Persistance\DatabaseLoader;

use Inspirio\Database\cDBFeederBase;

include_once 'common.php';
session_start();

$database = new Inspirio\Database\cDatabase( DATABASE, HOST, USER, PASSWORD );

$url = '/'. $_GET['url'];

$webPageFeeder = new Inspirio\Database\cDBFeederBase( '\\DemoCMS\\cWebPage', $database );
$webPage       = $webPageFeeder->whereColumnEq( 'url_name', $url )->getOne();

// 404
if( $webPage == null ) {
	header("HTTP/1.0 404 Not Found");
	echo '<h1 style="color:red;">404 - Page not found</h1>';
	exit;
}

// load the webPage
$simplePageFeeder = new cDBFeederBase( '\\DemoCMS\\cSimplePage', $database );
$simplePage       = $simplePageFeeder->whereColumnEq( 'web_page_ID', $webPage->getID() )->getOne();

$webPage->setContentItem( $simplePage );
$simplePage->setWebPage( $webPage );

// create the builder
$blockLoader = new \WebBuilder\Persistance\DatabaseLoader( $database, $webPage->getBlockSetID() );
$blockFactory = new \WebBuilder\WebBlocksFactory( $database );

$builder = new \WebBuilder\WebBuilder( $blockLoader, $blockFactory );

$twig = $builder->getTwig();
$twig->addGlobal( 'BASE_HREF', BASE_HREF );

// render the webPage
echo $builder->render( $webPage );