Ext.define( 'WebBuilder.module.TemplatesManager.TemplateEditor',
{
	extend : 'Ext.tab.Panel',

	mixins : {
		editor : 'extAdmin.component.editor.DataEditorFeature'
	},

	requires : [
		'Ext.container.Container',
		'WebBuilder.component.TemplateEditor'
	],

	width  : 800,
	height : 600,

	/**
	 * Editor initialization
	 *
	 */
	initComponent : function()
	{
		var me = this;

		me.nameField = Ext.create( 'Ext.form.field.Text', {
			fieldLabel : 'Název',
			name       : 'name'
		});

		me.templateEditor = Ext.create( 'WebBuilder.component.TemplateEditor', {
			env : me.env
		});



		var generalTab = Ext.create( 'Ext.container.Container', {
			title   : 'Obecné',
			padding : 5,

			items : [ me.nameField ]
		});

		var editorTab = Ext.apply( me.templateEditor, {
			title  : 'Návrh šablony'
		});


		Ext.apply( me, {
			title   : 'Úprava šablony',
			iconCls : 'i-delete',
			border  : false,

			items  : [ generalTab, editorTab ]
		});

		me.mixins.editor.constructor.call( me );

		me.callParent( arguments );
	},

	setData : function( data )
	{
		console.log( 'set data ', data );
	},

	getData : function()
	{
		var me = this;

		return {
			name     : me.nameField.getValue(),
			template : me.templateEditor.getValue()
		};
	},

	isDirty : function()
	{
		return true;
	}
});