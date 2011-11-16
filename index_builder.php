<?php

include_once 'common.php';

$database = new Inspirio\Database\cDatabase( DATABASE, HOST, USER, PASSWORD );

$wsFeeder = new Inspirio\Database\cDBFeederBase( 'WebBuilder\WebBuilder\DataObjects\WebStructureItem', $database );
$wsItem = $wsFeeder->whereColumnEq( 'url_name', '/' )->getOne();

if( $wsItem == null ) {
	echo '<h1 style="color:red;">404 - Page not found</h1>';
	exit;
}

$builder = new WebBuilder\WebBuilder\WebBuilder( $database, array( 'debug' => true ));
echo $builder->render( $wsItem );

echo 'Mem usage: '.number_format( memory_get_peak_usage() / 1000000, 2, ',', ' ' ).'MB';