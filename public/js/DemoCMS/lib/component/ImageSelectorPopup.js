Ext.define( 'DemoCMS.component.ImageSelectorPopup',
{
	extend : 'Ext.window.Window',

	requires : [
		'DemoCMS.component.ImageManager'
	],

	title : 'Výběr obrázku',

	okBtnTitle     : 'Vybrat',
	cancelBtnTitle : 'Storno',

	/**
	 * @required
	 * @cfg {extAdmin.Environment} env
	 */
	env : null,

	/**
	 * @required
	 * @cfg {extAdmin.component.editor.EditorFeature} editor
	 */
	editor : null,

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

		me.module = me.env.getModule( '\\DemoCMS\\Administration\\WebEditor\\ImageList' );

		me.list = Ext.create( 'DemoCMS.component.ImageManager', {
			env    : me.env,
			editor : me.editor
		});

		Ext.apply( me, {
			layout : 'fit',
			width  : 600,
			height : 600,
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

	show : function( webPageId, selectorFn, scope )
	{
		var me = this,
		    cb = null;

		if( selectorFn ) {
			cb = function( records ) {
				var selected = Ext.Array.filter( records, selectorFn, scope );

				me.list.imageList.getSelectionModel().select( selected );
			};
		}

		me.list.loadData( webPageId, cb );

		me.callParent();
	},

	onSelect : function()
	{
		var me      = this,
		    records = me.list.imageList.getSelectionModel().getSelection();

		me.close();
		Ext.callback( me.handler, me.scope, [ records ] );
	}
});