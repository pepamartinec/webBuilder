Ext.define( 'WebBuilder.widget.BlockList',
{
	extend : 'Ext.container.Container',
	
	requires : [
		'Ext.layout.container.Accordion',
		'Ext.panel.Panel',
		'Ext.layout.container.Fit',
		'Ext.view.View',
		'WebBuilder.model.BlockCategory',
		'extAdmin.Store'
	],
	
	/**
	 * @required
	 * @cfg {extAdmin.Environment} env
	 */
	env : null,
	
	/**
	 * @cfg {extAdmin.Module/String} module
	 */
	module : null,
	
	/**
	 * @required
	 * @cfg {Array/String} loadAction
	 */
	loadAction : null,
	
	initComponent : function()
	{
		var me = this;
		
		Ext.apply( me, {
			layout : {
				type    : 'accordion',
				animate : true
			},
			
			items : []
		});
		
		var categoriesStore = extAdmin.Store.create({
			env        : me.env,
			loadAction : me.module.normalizeActionPtr( me.loadAction ),			
			model      : 'WebBuilder.model.BlockCategory',
			autoLoad   : true
		});
		
		me.callParent( arguments );
		
		categoriesStore.on( 'datachanged', function( store, categories ) {
			Ext.Array.each( categories, function( category ) {				
				me.add({
					xtype  : 'panel',
					layout : 'fit',
					title  : category.get('title'),
					
					items  : [{
						xtype : 'dataview',
						store : category.blocks(),
						
						itemTpl : '{codeName}',
						itemCls : 'item'
					}]
				});
			});
		});
	}
});