Ext.define( 'WebBuilder.component.TemplateEditor',
{
	extend : 'Ext.container.Container',
	alias  : 'widget.templateeditor',

	requires : [
		'Ext.layout.container.Border',
		'WebBuilder.widget.BlocksList',
		'WebBuilder.widget.TemplateCanvas',
		'extAdmin.Store',
		'WebBuilder.model.BlocksCategory',
		'WebBuilder.model.Block',
		'WebBuilder.model.BlockTemplate',
		'WebBuilder.model.BlockTemplateSlot'
	],

	/**
	 * @required
	 * @cfg {extAdmin.Environment} env
	 */
	env : null,

	/**
	 * @cfg {extAdmin.Module/String} module
	 */
	module : null,

	/**
	 * @cfg {Array/String} blocksLoadAction
	 */
	blocksLoadAction : null,

	/**
	 * Editor initialization
	 *
	 */
	initComponent : function()
	{
		var me = this;

		me.module = me.env.getModule( me.module || '\\WebBuilder\\WebBuilder\\ExtAdmin\\TemplatesManager\\TemplateEditor' );

		me.initData();

		// init components
		var ddGroup = 'template-editor-'+ me.getId();

		me.list = Ext.create( 'WebBuilder.widget.BlocksList', {
			region      : 'east',
			split       : true,
			collapsible : true,
			width       : 150,

			ddGroup : ddGroup,
			store   : me.categoriesStore
		});

		me.canvas = Ext.create( 'WebBuilder.widget.TemplateCanvas', {
			region : 'center',
//			border : false,
			title  : 'Plátno',

			listeners : {
				scope : me,
				render : me.initBlockInstances
			},

			module         : me.module,
			ddGroup        : ddGroup,
			instancesStore : me.instancesStore
		});

//		me.tree = Ext.create( 'WebBuilder.widget.TemplateTree', {
//			region : 'center',
//			border : false,
//			title  : 'Strom',
//
//			ddGroup : ddGroup,
//			store   : me.instancesStore
//		});

		Ext.apply( me, {
			layout : 'border',
			items  : [{
					xtype  : 'tabpanel',
					region : 'center',
					border : false,

					items : [ me.canvas ]
				},

				me.list ]
		});

		me.callParent( arguments );
	},

//	initData : function( cb, cbScope )
//	{
//		var me     = this,
//		    stores = me.stores = {};
//
//		stores.categories = Ext.create( 'Ext.data.Store', {
//			model : 'WebBuilder.model.BlocksCategory',
//			proxy : {
//				type   : 'memory',
//				reader : {
//					type : 'json',
//					root : 'categories'
//				}
//			}
//		});
//
//		stores.blocks = Ext.create( 'Ext.data.Store', {
//			model : 'WebBuilder.model.Block',
//			proxy : {
//				type   : 'memory',
//				reader : {
//					type : 'json',
//					root : 'blocks'
//				}
//			}
//		});
//
//		stores.templates = Ext.create( 'Ext.data.Store', {
//			model : 'WebBuilder.model.BlockTemplate',
//			proxy : {
//				type   : 'memory',
//				reader : {
//					type : 'json',
//					root : 'templates'
//				}
//			}
//		});
//
//		stores.slots = Ext.create( 'Ext.data.Store', {
//			model : 'WebBuilder.model.BlockTemplateSlot',
//			proxy : {
//				type   : 'memory',
//				reader : {
//					type : 'json',
//					root : 'slots'
//				}
//			}
//		});
//
//		this.module.runAction( 'loadBlocks', {
//			success : function( response ) {
//				Ext.Object.each( response.data, function( k, data ) {
//					stores[k].loadData( data );
//				});
//			}
//		});
//	}

	initData : function( cb, cbScope )
	{
		var me = this;

		me.instancesStore = Ext.create( 'WebBuilder.EditorStore' );

		me.categoriesStore = extAdmin.Store.create({
			env        : me.env,
			loadAction : [ me.module.name, 'loadBlocks' ],
			model      : 'WebBuilder.model.BlocksCategory',

			remoteSort   : false,
			remoteFilter : false,
			autoLoad     : true,

			listeners : {
				scope : me,
	//			load  : me.initBlockInstances
			}
		});
	},

	initBlockInstances : function()
	{
		var me         = this,
	    blockByIds = {};

		Ext.Function.defer( function() {



		me.categoriesStore.each( function( blocksCategory ) {
			blocksCategory.blocks().each( function( block ) {
				blockByIds[ block.getId() ] = block;
			});
		});

		var defaultBlockId    = '4',
		    defaultTemplateId = '10',
		    root              = null;

		// convert value to block instance
		if( me.value ) {

		// no value, create empty root instance
		} else {
			root = Ext.create( 'WebBuilder.BlockInstance', blockByIds[ defaultBlockId ] );

			me.instancesStore.setRoot( root );
			me.instancesStore.setTemplate( root, blockByIds[ defaultBlockId ].templates().getById( defaultTemplateId ) );
		}

		},2000);
	}
});