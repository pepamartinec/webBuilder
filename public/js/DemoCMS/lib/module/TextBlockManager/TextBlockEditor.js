Ext.define( 'DemoCMS.module.TextBlockManager.TextBlockEditor',
{
	extend : 'Ext.form.Panel',

	mixins : {
		editor : 'extAdmin.component.editor.DataEditorFeature'
	},

	width : 800,

	/**
	 * Editor initialization
	 *
	 */
	initComponent : function()
	{
		var me = this;

		Ext.apply( me, {
			border      : false,
			bodyPadding : 5,

			title   : 'Úprava textu',
			iconCls : 'i-edit',

			defaults : {
				anchor : '100%'
			},

			items : [{
				xtype : 'hiddenfield',
				name  : 'ID'
			},{
				xtype      : 'textfield',
				name       : 'title',
				fieldLabel : 'Název'
			},{
				xtype      : 'htmleditor',
				name       : 'content',
				fieldLabel : 'Obsah',

				height : 400
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