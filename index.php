<?php

include_once 'common.php';

$database = new Inspirio\Database\cDatabase( DATABASE, HOST, USER, PASSWORD );

$route = $_GET['route'];

if( $route === 'admin' || $route === 'admin/' ) {
?>
	<!-- JS LIBS -->
	<script type="text/javascript" src="public/javascripts/libs/jQuery_1.6/jquery.min.js"></script>
	<script type="text/javascript" src="pulic/javascripts/tinyMce/tiny_mce.js"></script>
	<script type="text/javascript" src="public/javascripts/libs/ExtJS_4.0/ext-all-debug.js"></script>
	
	<!-- STYLESHEETS -->
	<link rel="stylesheet" type="text/css" href="public/javascripts/ExtAdmin/resources/css/ExtAdmin.css"/>
	
	<!-- INIT ExtJS LOADER -->
	<script type="text/javascript">
		Ext.Loader.setConfig({
			enabled  : true,
			disableCaching : false,
			paths : {
				'ExtAdmin' : 'public/javascripts/ExtAdmin/lib',
				'inspirio' : 'public/javascripts/inspirio'
			}
		});
	</script>
	
	<script type="text/javascript" src="public/javascripts/inspirio/inspirio.js"></script>
	<script type="text/javascript" src="public/javascripts/ExtAdmin/app.js"></script>

	<script type="text/javascript">
		Ext.require(['ExtAdmin.App']);
	
		Ext.onReady( function() {
			
			// make URL parameters encoding PHP compatible
			var original = Ext.Object.toQueryString;
			Ext.Object.toQueryString = function( object ) {
				return original( object, true );
			};

			ExtAdmin.init({
				baseHref      : '<?php echo BASE_HREF; ?>',
				serverHandle  : 'scripts/adminModuleServer.php',
				localizations : [ '<?php echo implode( "','", $web->getLocalizations() ); ?>' ],
				debug         : true,
				authManager   : {
					loginScreenUrl  : 'admin/',
					loginHandlerUrl : 'scripts/auth.php',
				}
			});
			
			var ea = ExtAdmin.launch({
				renderTo : 'admin'
			});
		});
	</script>
<?php
	
} else {
	$wsFeeder = new Inspirio\Database\cDBFeederBase( 'WebBuilder\DataObjects\WebPage', $database );
	$wsItem = $wsFeeder->whereColumnEq( 'url_name', $route )->getOne();
	
	// 404
	if( $wsItem == null ) {
		header("HTTP/1.0 404 Not Found");
		echo '<h1 style="color:red;">404 - Page not found</h1>';
		exit;
	}
	
	$builder = new WebBuilder\WebBuilder( $database, array( 'debug' => true ));
	echo $builder->render( $wsItem );
}