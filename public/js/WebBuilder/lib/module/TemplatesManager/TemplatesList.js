Ext.define( 'WebBuilder.module.TemplatesManager.TemplatesList',
{
	extend : 'extAdmin.component.dataBrowser.DataBrowser',
	
	requires : [
		'Ext.view.View'
	],
	
	initComponent : function()
	{
		var me = this;
		
		me.store = me.module.createStore({
			action : 'loadListData',
			
			model : 'WebBuilder.module.TemplatesManager.TemplatesList.Template',
			
//			sorters       : {
//				property  : viewConfig.sort.column,
//				direction : viewConfig.sort.dir
//			}
		});
		
		me.dataList = Ext.create( 'Ext.view.View', {
			xtype : 'dataview',
			
			itemCls : 'template',
			itemTpl : ['{name}'],
			
			store : me.store
		});
		
		me.callParent( arguments );
	},
	
}, function() {
	
	Ext.define( 'WebBuilder.module.TemplatesManager.TemplatesList.Template', {
		extend : 'extAdmin.component.dataBrowser.Model',
		
		fields : [
			{ name : 'name' }
		]
	});
});