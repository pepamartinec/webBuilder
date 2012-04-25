Ext.define( 'Inspirio.widget.ImageUploadPopup',
{
	extend : 'Ext.window.Window',

	requires : [
		'Ext.layout.container.Fit',
		'Ext.form.Panel',
		'Ext.form.field.File'
	],

	title : 'Nahrání obrázků',

	okBtnTitle     : 'Nahrát',
	cancelBtnTitle : 'Storno',

	/**
	 * @cfg {extAdmin.Module} module
	 */
	module : null,

	/**
	 * @cfg {String} uploadAction
	 */
	uploadAction : 'uploadImage',

	/**
	 * @cfg {Number} webPageId
	 */
	webPageId : null,

	/**
	 * Editor initialization
	 *
	 */
	initComponent : function()
	{
		var me = this;

		me.webPageIdField = Ext.create( 'Ext.form.field.Hidden', {
			name  : 'webPageID'
		});

		me.form = Ext.create( 'Ext.form.Panel', {
			bodyPadding : 5,
			url : me.module.getActionUrl( 'uploadImages' ),

			defaults : {
				anchor : '100%'
			},

			items : [ me.webPageIdField ]
		});

		me.fileFields = [];

		Ext.apply( me, {
			layout : 'fit',
			width  : 400,

			items : [ me.form ],

			buttons : [{
				text    : me.okBtnTitle,
				iconCls : 'i-server-to',
				handler : me.doUpload,
				scope   : me
			},{
				text    : me.cancelBtnTitle,
				iconCls : 'i-cancel',
				handler : me.close,
				scope   : me
			}]
		});

		me.callParent();

		me.addEvents( 'uploadcomplete' );
	},

	show : function()
	{
		var me = this;

		me.form.remove( me.fileFields );
		me.fileFields = [];

		me.addFileInput();

		me.callParent();
	},

	addFileInput : function()
	{
		var me = this,
		    field;

		// remove change listener from previous field
		field = me.items.last();

		if( field ) {
			field.un( me.addFileInput );
		}

		// create new file field
		field = Ext.create( 'Ext.form.field.File', {
			name  : 'images[]',
			reset : Ext.emptyFn
		});

		field.on( 'change', me.addFileInput, me );

		me.fileFields.push( field );
		me.form.add( field );
	},

	doUpload : function()
	{
		var me = this;

		me.webPageIdField.setValue( me.webPageId );

		me.form.submit({
			success : function( form, action ) {
				me.fireEvent( 'uploadcomplete' );

				me.close();
			}
		});
	}
});