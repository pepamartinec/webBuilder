Ext.define( 'DemoCMS.component.ImageManager',
{
	extend : 'Ext.panel.Panel',

	requires : [
		'Ext.layout.container.Fit',
		'Ext.toolbar.Toolbar',
		'Ext.button.Button',
		'DemoCMS.widget.ImageList',
		'DemoCMS.widget.ImageUploadPopup'
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
	 * Component initialization
	 *
	 */
	initComponent : function()
	{
		var me = this;

		me.module = me.env.getModule('\\DemoCMS\\Administration\\WebEditor\\ImageList');

		me.imageList = Ext.create( 'DemoCMS.widget.ImageList', {
			module : me.module
		});

		Ext.apply( me, {
			layout : 'fit',
			items  : [ me.imageList ],

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
				fn     : function() {
					me.loadData( me.webPageId );
				}
			}
		});
	},

	loadData : function( webPageId, cb, scope )
	{
		this.webPageId = webPageId;

		this.imageList.loadData( webPageId, cb, scope );
	},

	showImageUpload : function()
	{
		var me = this;

		if( me.webPageId == null && me.editor != null ) {
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
			me.uploadPopup = Ext.create( 'DemoCMS.widget.ImageUploadPopup', {
				module      : me.module,
				closeAction : 'hide',
				webPageId   : me.webPageId,

				listeners : {
					uploadcomplete : function() {
						me.loadData( me.webPageId );
					}
				}
			});
		}

		me.uploadPopup.show();
	}
});