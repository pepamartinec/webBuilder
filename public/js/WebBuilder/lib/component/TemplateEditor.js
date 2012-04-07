Ext.define( 'WebBuilder.component.TemplateEditor',
{
	extend : 'Ext.container.Container',
	alias  : 'widget.templateeditor',

	requires : [
		'Ext.layout.container.Border',
		'WebBuilder.widget.BlocksList',
		'WebBuilder.widget.TemplateCanvas',

		'WebBuilder.EditorStore',
		'extAdmin.Store',
		'WebBuilder.BlockInstance',
		'WebBuilder.model.Block'
	],

	/**
	 * @required
	 * @cfg {extAdmin.Environment} env
	 */
	env : null,

	/**
	 * @cfg {extAdmin.Module/String} module
	 */
	module : '\\WebBuilder\\WebBuilder\\ExtAdmin\\TemplatesManager\\TemplateEditor',

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

		me.module = me.env.getModule( me.module );

		me.initData();

		// init internal components
		var ddGroup = me.getId();

		me.list = Ext.create( 'WebBuilder.widget.BlocksList', {
			region      : 'east',
			split       : true,
			collapsible : true,
			width       : 150,

			module      : me.module,
			ddGroup     : ddGroup,
			blocksStore : me.blocksStore
		});

		me.canvas = Ext.create( 'WebBuilder.widget.TemplateCanvas', {
			region : 'center',
			title  : 'Plátno',

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

	initData : function( cb, cbScope )
	{
		var me = this;

		me.instancesStore = Ext.create( 'WebBuilder.EditorStore' );

		me.blocksStore = extAdmin.Store.create({
			env        : me.env,
			loadAction : [ me.module.name, 'loadBlocks' ],
			model      : 'WebBuilder.model.Block',

			remoteSort   : false,
			remoteFilter : false,
			autoLoad     : true,

			listeners : {
				scope : me,
				load  : me.onBlocksLoad
			}
		});
	},

	onBlocksLoad : function( blocksStore, blocks )
	{
		var me = this,
			defaultBlockId    = 4,
			defaultTemplateId = 10;

		// convert value to block instance
		if( me.value ) {

		// no value, create empty root instance
		} else {
			var block    = blocksStore.getById( defaultBlockId ),
			    template = block.templates().getById( defaultTemplateId ),
			    root     = Ext.create( 'WebBuilder.BlockInstance', block, template );

			me.instancesStore.setRoot( root );
		}
	}
});