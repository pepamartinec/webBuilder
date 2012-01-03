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
			
			model : 'WebBuilder.module.TemplatesManager.TemplatesList.Template'
			
//			sorters       : {
//				property  : viewConfig.sort.column,
//				direction : viewConfig.sort.dir
//			}
		});
		
		me.dataList = Ext.create( 'Ext.view.View', {
			xtype : 'dataview',
			
			componentCls : 'x-templates-list',
			itemCls : 'x-item',
			itemTpl : [
				'<div class="thumb"><img src="{image}" title="{name}" alt="{name}" /></div>',
				'<div class="name">{name}</div>'
			],
			
			store : me.store
		});
		
		me.callParent( arguments );
	},
	
}, function() {
	
	Ext.define( 'WebBuilder.module.TemplatesManager.TemplatesList.Template', {
		extend : 'extAdmin.component.dataBrowser.Model',
		
		fields : [
			{ name : 'name',  type : 'string' },
			{ name : 'image', type : 'string' }
		]
	});
});