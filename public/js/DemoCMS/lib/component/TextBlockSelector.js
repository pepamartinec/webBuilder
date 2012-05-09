Ext.define( 'DemoCMS.component.TextBlockSelector',
{
	extend : 'Ext.window.Window',

	title : 'Výběr šablony',

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
	module : '\\WebBuilder\\Administration\\TextBlockManager\\TextBlockList',

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

		me.list = Ext.create( 'Ext.grid.Panel', {
			forceFit : true,

			store : me.module.createStore({
				loadAction : me.loadAction,
				autoLoad   : true,

				model : extAdmin.component.Model.create({
					name : { type : 'string' }
				}),
				implicitModel : true
			}),

			columns : [{
				dataIndex : 'name',
				header    : 'Název'
			}]
		});

		Ext.apply( me, {
			layout : 'fit',
			width  : 600,
			height : 400,
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
				handler : function() { me.close(); }
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