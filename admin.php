<?php

require 'common.php';

?><!doctype html>
<html lang="cs">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

	<title>Administrace projektu DemoCMS</title>
	<base href="<?php echo BASE_HREF; ?>" />
	<link rel="shortcut icon" href="public/favicon.ico">

	<!-- STYLESHEETS -->
	<link rel="stylesheet" type="text/css" href="vendor/Sencha/ExtJS/resources/css/ext-all-debug.css" />
	<link rel="stylesheet" type="text/css" href="http://dev-cdn/dumpJS/dump.css" />
	<link rel="stylesheet" type="text/css" href="vendor/extAdmin/extAdmin/resources/css/extAdmin.css" />

	<link rel="stylesheet" type="text/css" href="public/css/webBuilder.css" />
</head>
<body>
	<!-- JS LIBS -->
	<script type="text/javascript" src="vendor/Sencha/ExtJS/ext-debug.js"></script>
	<script type="text/javascript" src="http://dev-cdn/dumpJS/dump.js"></script>

	<!-- SETUP ExtJS -->
	<script type="text/javascript">
		Ext.Loader.setConfig({
			enabled        : true,
			disableCaching : false,
			paths : {
				'extAdmin'   : 'vendor/extAdmin/extAdmin/client',
				'WebBuilder' : 'public/js/WebBuilder/lib',
				'DemoCMS'    : 'public/js/DemoCMS/lib'
			}
		});
	</script>

	<!-- RUN extAdmin -->
	<script type="text/javascript" src="vendor/extAdmin/extAdmin/extAdmin.js"></script>
	<script type="text/javascript">
		// FireBug console fallback
		if( window.console === undefined ) {
			window.console = {
				log  : Ext.emptyFn,
				warn : Ext.emptyFn
			};
		}

		Ext.require([
		 	'extAdmin.ErrorHandler',
			'extAdmin.Environment',
			'extAdmin.Desktop',

			'Ext.Date'
		]);

		Ext.onReady( function() {

			Ext.Date.defaultFormat = 'd.m.Y';

			// setup error handling
			extAdmin.ErrorHandler.setDebug( true );

			// enable qTips
			Ext.tip.QuickTipManager.init();

			// create & init the environment
			var env = Ext.create( 'extAdmin.Environment' );

			env.init({
				serverEndpoint : 'extAdminEndpoint.php',

				baseHref : '<?php echo BASE_HREF; ?>',
				debug    : false

			}, function() {
				// launch the desktop app
				var admin = Ext.create( 'extAdmin.Desktop', {
					env : env,

					menuItems : [{
						name        : 'Editor Webu',
						iconCls     : 'i-web-editor',
						entryModule : '\\DemoCMS\\Administration\\WebEditor\\PageList',
					},{
						name        : 'Šablony stránek',
						iconCls     : 'i-template-manager',
						entryModule : '\\WebBuilder\\Administration\\TemplateManager\\TemplateList',
					},{
						name        : 'Textové bloky',
						iconCls     : 'i-text-block-manager',
						entryModule : '\\DemoCMS\\Administration\\TextBlockManager\\TextBlockList',
					}]
				});

				admin.openModule( '\\DemoCMS\\Administration\\WebEditor\\PageList' );
			});
		});
	</script>
</body>
</html>
