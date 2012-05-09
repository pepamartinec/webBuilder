Ext.define( 'DemoCMS.component.TemplateSelectorPopup',
{
	extend : 'Ext.window.Window',

	title : 'Výběr webové stránky',

	okBtnTitle     : 'Použít',
	cancelBtnTitle : 'Storno',

	/**
	 * @required
	 * @cfg {extAdmin.Environment} env
	 */
	env : null,

	/**
	 * @cfg {extAdmin.Module/String} module
	 */
	module : '\\WebBuilder\\Administration\\WebEditor\\PageList',

	/**
	 * @cfg {Array/String} blocksLoadAction
	 */
	loadAction : 'loadListData',

	/**
	 * Editor initialization
	 *
	 */
	initComponent : function()
	{
		var me = this;

		me.module = me.env.getModule( me.module );

		me.list = Ext.create( 'Ext.tree.Panel', {
			forceFit : true,

			store : me.module.createStore({
				type       : 'Ext.data.TreeStore',
				loadAction : me.loadAction,
				autoLoad   : true,

				defaultRootProperty : 'data',
				nodeParam : 'parentID',
				async : false,

				// disable root node autoLoad
			    root: {
			    	expanded : true,
			    	text     : '',
			    	data     : [],
			    	ID		 : '',
			    	allowDrop : false
			    },

				model : extAdmin.component.Model.create({
					title : { type : 'string' }
				}),
				implicitModel : true
			}),

			columns : [{
				dataIndex : 'title',
				header    : 'Název'
			}]
		});

		Ext.apply( me, {
			layout : 'fit',
			width  : 400,
			height : 200,
			modal  : true,

			items   : [ me.list ],
			buttons : [{
				text    : me.okBtnTitle,
				iconCls : 'i-ok',
				handler : me.onSelect,
				scope   : me
			},{
				text    : me.cancelBtnTitle,
				iconCls : 'i-cancel',
				handler : me.close,
				scope   : me
			}]
		});

		me.callParent();
	},

	onSelect : function()
	{
		var me      = this,
		    records = me.list.getSelectionModel().getSelection();

		me.close();
		Ext.callback( me.handler, me.scope, [ records ] );
	}
});