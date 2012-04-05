Ext.define( 'WebBuilder.module.WebEditor.pageEditor.Template',
{
	extend : 'Ext.panel.Panel',
	
	requires : [
		'WebBuilder.component.TemplateEditor'
	],
	
	title : 'Šablona',
	layout : 'fit',
	
	initComponent : function()
	{
		var me = this;
		
		me.items = [{
			xtype : 'templateeditor',
			env   : me.env
		}],
		
		me.dockedItems = [{
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
		}];
		
		me.callParent();
	},
	
	preview : function()
	{
		
	},
	
	loadPredefined : function()
	{
		return;
		
		var lookup = Ext.create( 'extAdmin.component.lookup.Popup', {
// TODO		readOnly : true,
			
			layout : 'fit',
			items  : [{
				xtype   : 'grid',
				columns : [{
					
				}]
			}]
		});
	},
	
	saveAsPredefined : function()
	{
		
	}
});