<?php

include_once 'common.php';

$database = new \cDatabase( DATABASE );

$admin = new \WebBuilder\cWebBuilderAdmin( $database );
$admin->generateInstancesDataDependencies();
