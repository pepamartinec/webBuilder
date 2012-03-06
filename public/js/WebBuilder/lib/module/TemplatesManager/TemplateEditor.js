Ext.define( 'WebBuilder.module.TemplatesManager.TemplateEditor',
{
	extend : 'Ext.tab.Panel',
	
	mixins : {
		editor : 'extAdmin.component.editor.EditorFeature'
	},
	
	requires : [
		'Ext.container.Container',
		'WebBuilder.component.TemplateEditor'
	],
	
	width  : 800,
	height : 600,
	
	/**
	 * @required
	 * @cfg {extAdmin.Environment} env
	 */
	env : null,
	
	/**
	 * Editor initialization
	 * 
	 */
	initComponent : function()
	{
		var me = this;
		
		var generalTab = Ext.create( 'Ext.container.Container', {
			title   : 'Obecné',
			padding : 5,
			
			items : [{
				xtype      : 'textfield',
				fieldLabel : 'Název',
				name       : 'name'
			}]
		});
		
		var canvasTab = Ext.create( 'WebBuilder.component.TemplateEditor', {
			title  : 'Návrh šablony',
			env    : me.env
		});
		
		
		me.border = false;
		me.items = [ generalTab, canvasTab ];
		
		
		me.title = 'Úprava šablony';
		me.iconCls = 'i-delete';
		
		me.mixins.editor.constructor.call( me );
		
		me.callParent( arguments );
	}
});