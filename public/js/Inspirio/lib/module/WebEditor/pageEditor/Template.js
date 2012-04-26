Ext.define( 'Inspirio.module.WebEditor.pageEditor.Template',
{
	extend : 'Ext.panel.Panel',

	requires : [
		'Ext.layout.container.Fit',
		'WebBuilder.component.TemplateEditor',
		'Inspirio.component.TemplateSelectorPopup'
	],

	/**
	 * @required
	 * @cfg {extAdmin.Environment} env
	 */
	env : null,

	initComponent : function()
	{
		var me = this;

		me.blockSetIdField = Ext.create( 'Ext.form.field.Hidden' );
		me.parentIdField   = Ext.create( 'Ext.form.field.Hidden' );

		me.templateEditor = Ext.create( 'WebBuilder.component.TemplateEditor', {
			env   : me.env
		});

		Ext.apply( me, {
			title  : 'Šablona',
			layout : 'fit',

			items : [ me.templateEditor ],

			dockedItems : [{
				dock  : 'top',
				xtype : 'toolbar',

				items : [{
					xtype   : 'button',
					text    : 'Náhled',
					iconCls : 'i-monitor',

					handler : me.preview,
					scope   : me
				},{
					xtype : 'button',
					text  : 'Načíst předdefinovanou',
					iconCls : 'i-folder',

					handler : me.loadPredefined,
					scope   : me
				},{
					xtype   : 'button',
					text    : 'Uložit mezi předdefinované',
					iconCls : 'i-disk',

					handler : me.saveAsPredefined,
					scope   : me
				},{
					xtype : 'tbfill'
				},{
					xtype   : 'button',
					iconCls : 'i-arrow-out'
				}]
			}]
		});

		me.callParent();
	},

	preview : function()
	{

	},

	loadPredefined : function()
	{
		var me = this;

		if( me.templateSelectorPopup == null ) {
			var loadAction = [ '\\WebBuilder\\Administration\\TemplateManager\\TemplateEditor', 'loadData_record' ];

			me.templateSelectorPopup = Ext.create( 'Inspirio.component.TemplateSelectorPopup', {
				env         : me.env,
				closeAction : 'hide',

				handler : function( records ) {
					me.env.runAction( loadAction, {
						data : { ID : records[0].getId() },

						success : function( data ) {
							var template = data.data.template; // TODO why the double data?

						//	me.clearBlockInstanceID( template );

							me.templateEditor.setValue( template );
						}
					});
				},

				socpe : me
			});
		}

		me.templateSelectorPopup.show();
	},

	clearBlockInstanceID : function( blockInstance )
	{
		blockInstance.ID = null;

		if( blockInstance.slots ) {
			Ext.Object.each( blockInstance.slots, function( name, children ) {
				Ext.Array.each( children, this.clearBlockInstanceID, this );
			}, this );
		}
	},

	saveAsPredefined : function()
	{

	},

	getData : function()
	{
		return {
			blockSetID       : this.blockSetIdField.getValue(),
			parentBlockSetID : this.parentIdField.getValue(),
			template         : this.templateEditor.getValue()
		};
	},

	setData : function( data )
	{
		this.blockSetIdField.setValue( data.blockSetID );
		this.parentIdField.setValue( data.parentBlockSetID );

		this.templateEditor.setBlockSetId( data.blockSetID );
		this.templateEditor.setValue( data.template );

		return this;
	}
});