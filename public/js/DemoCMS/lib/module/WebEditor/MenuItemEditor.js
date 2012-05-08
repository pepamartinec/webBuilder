Ext.define( 'DemoCMS.module.WebEditor.MenuItemEditor',
{
	extend : 'Ext.form.Panel',

	mixins : {
		editor : 'extAdmin.component.editor.DataEditorFeature'
	},

	width  : 400,

	/**
	 * Editor initialization
	 *
	 */
	initComponent : function()
	{
		var me = this;

		Ext.apply( me, {
			title   : 'Úprava položky menu',
			iconCls : 'i-edit',
			border  : false,
			bodyPadding : 5,

			defaults : {
				anchor : '100%'
			},

			items : [{
				xtype : 'hiddenfield',
				name  : 'ID'
			},{
				xtype : 'hiddenfield',
				name  : 'parentID'
			},{
				xtype      : 'textfield',
				name       : 'title',
				fieldLabel : 'Název'
			},{
				xtype      : 'checkbox',
				name       : 'published',
				fieldLabel : 'Publikovat'
			}]
		});

		me.mixins.editor.constructor.call( me );

		me.callParent( arguments );
	},

	setData : function( data )
	{
		this.form.setValues( data );
		return this;
	},

	getData : function()
	{
		return this.form.getValues();
	},

	getRecordId : function()
	{
		return this.getData()['ID'];
	},

	isDirty : function()
	{
		return false;
	}
});