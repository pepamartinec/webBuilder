Ext.define( 'DemoCMS.widget.ImageUploadPopup',
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

		me.form = Ext.create( 'Ext.form.Panel', {
			bodyPadding : 5,
			url : me.module.getActionUrl( 'uploadImages' ),

			defaults : {
				anchor : '100%'
			}
		});

		Ext.apply( me, {
			layout : 'fit',
			width  : 400,
			modal  : true,

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

		me.form.removeAll( true );
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

		me.form.add( field );
	},

	doUpload : function()
	{
		var me   = this,
		    form = me.form;

		var webPageIdField = Ext.create( 'Ext.form.field.Hidden', {
			name  : 'webPageID',
			value : me.webPageId
		});

		form.add( webPageIdField );

		form.submit({
			success : function( bForm, action ) {
				form.remove( webPageIdField, true );
				me.fireEvent( 'uploadcomplete' );

				me.close();
			}
		});
	}
});