Ext.define( 'WebBuilder.module.WebEditor.pageEditor.Template',
{
	extend : 'Ext.panel.Panel',
	
	requires : [
		'Ext.layout.container.Fit',
		'WebBuilder.component.TemplateEditor',
		'WebBuilder.component.TemplateSelectorPopup'
	],
	
	/**
	 * @required
	 * @cfg {extAdmin.Environment} env
	 */
	env : null,
	
	initComponent : function()
	{
		var me = this;
		
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
			var loadAction = [ '\\WebBuilder\\ExtAdmin\\TemplatesManager\\TemplateEditor', 'loadData_record' ];
			
			me.templateSelectorPopup = Ext.create( 'WebBuilder.component.TemplateSelectorPopup', {
				env         : me.env,
				closeAction : 'hide',
				
				handler : function( records ) {
					me.env.runAction( loadAction, {
						data : { ID : records[0].getId() },
						
						success : function( data ) {
							me.templateEditor.setValue( data.data.template );
						}
					});
				},
				
				socpe : me
			});
		}
		
		me.templateSelectorPopup.show();
	},
	
	saveAsPredefined : function()
	{
		
	}
});