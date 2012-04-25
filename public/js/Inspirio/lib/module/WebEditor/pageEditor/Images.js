Ext.define( 'Inspirio.module.WebEditor.pageEditor.Images',
{
	extend : 'Ext.panel.Panel',

	requires : [
		'Ext.layout.container.Fit',
		'Ext.toolbar.Toolbar',
		'Ext.button.Button',
		'Inspirio.widget.ImageList',
		'Inspirio.widget.ImageUploadPopup'
	],

	/**
	 * @required
	 * @cfg {extAdmin.Environment} env
	 */
	env : null,

	/**
	 * @required
	 * @cfg {extAdmin.component.editor.DataEditorFeature} editor
	 */
	editor : null,

	/**
	 * @property {Number} webPageId
	 */
	webPageId : null,

	/**
	 * @property {String} title
	 */
	title : 'Obrázky',

	/**
	 * Component initialization
	 *
	 */
	initComponent : function()
	{
		var me = this;

		me.module = me.env.getModule('\\Inspirio\\Administration\\WebEditor\\ImageList');

		me.imageList = Ext.create( 'Inspirio.widget.ImageList', {
			module : me.module
		});

		Ext.apply( me, {
			items : [ me.imageList ],

			dockedItems : [{
				dock  : 'bottom',
				xtype : 'toolbar',
				items : [{
					xtype   : 'button',
					text    : 'Nahrát nové obrázky',
					iconCls : 'i-add',
					handler : me.showImageUpload,
					scope   : me
				}]
			}]
		});

		me.callParent();

		me.uploadPopup = null;

		me.on({
			show : {
				single : true,
				fn     : me.reloadList,
				scope  : me
			}
		});
	},

	reloadList : function()
	{
		var me = this;

		me.imageList.getStore().filter({
			property : 'webPageID',
			value    : me.webPageId
		});
	},

	showImageUpload : function()
	{
		var me = this;

		if( me.webPageId == null ) {
			me.editor.saveData({
				success : me.showImageUpload_afterSave,
				scope   : me
			});

		} else {
			me.showImageUpload_afterSave();
		}
	},

	showImageUpload_afterSave : function()
	{
		var me = this;

		if( me.uploadPopup == null ) {
			me.uploadPopup = Ext.create( 'Inspirio.widget.ImageUploadPopup', {
				module      : me.module,
				closeAction : 'hide',
				webPageId   : me.webPageId,

				listeners : {
					uploadcomplete : me.reloadList,
					scope          : me
				}
			});
		}

		me.uploadPopup.show();
	},

	getData : function()
	{
		return {};
	},

	setData : function( data )
	{
		var me = this;

		me.webPageId = data.ID;

		if( me.uploadPopup ) {
			me.uploadPopup.webPageId = me.webPageId;
			me.reloadList();
		}

		return this;
	}
});