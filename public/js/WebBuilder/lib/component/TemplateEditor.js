Ext.define( 'WebBuilder.component.TemplateEditor',
{
	extend : 'Ext.container.Container',
	
	requires : [
		'Ext.layout.container.Border',
		'WebBuilder.widget.BlockList',
		'WebBuilder.widget.TemplateCanvas'
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
	 * @cfg {Array/String} blocksLoadAction
	 */
	blocksLoadAction : null,
	
	/**
	 * Editor initialization
	 * 
	 */
	initComponent : function()
	{
		var me = this;
		
		me.module = me.env.getModule( me.module || '\\WebBuilder\\WebBuilder\\ExtAdmin\\TemplatesManager\\TemplateEditor' );
		
		me.list = Ext.create( 'WebBuilder.widget.BlockList', {
			region      : 'east',
			split       : true,
			collapsible : true,
			width       : 150,
			
			env        : me.env,
			module     : me.module,
			loadAction : me.blocksLoadAction || 'loadAvailableBlocks'
		});
		
		me.canvas = Ext.create( 'WebBuilder.widget.TemplateCanvas', {
			region : 'center',
			border : false
		});
		
		Ext.apply( me, {
			layout : 'border',			
			items  : [ me.canvas, me.list ]
		});
		
		me.callParent( arguments );
	}
});