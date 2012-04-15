Ext.define( 'WebBuilder.module.WebEditor.PageEditor',
{
	extend : 'Ext.tab.Panel',
	
	mixins : {
		editor : 'extAdmin.component.editor.DataEditorFeature'
	},
	
	requires : [
		'Ext.layout.container.Fit',		
		'WebBuilder.module.WebEditor.pageEditor.General',
		'WebBuilder.module.WebEditor.pageEditor.Content',
		'WebBuilder.module.WebEditor.pageEditor.Template'
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
		
		me.generalTab  = Ext.create( 'WebBuilder.module.WebEditor.pageEditor.General' ),
		me.contentTab  = Ext.create( 'WebBuilder.module.WebEditor.pageEditor.Content' ),
		me.templateTab = Ext.create( 'WebBuilder.module.WebEditor.pageEditor.Template', {
			env : me.env
		});
		
		
		me.border = false;
		me.items  = [ me.generalTab, me.contentTab, me.templateTab ];
		
		
		me.title   = 'Úprava stránky';
		me.iconCls = 'i-edit';
		me.layout  = 'fit';
		
		me.mixins.editor.constructor.call( me );
		
		me.callParent( arguments );
	},
	
	setData : function( data )
	{
		this.generalTab.setData( data );
		this.contentTab.setData( data );
		this.templateTab.setData( data );
		
		return this;
	},
	
	getData : function()
	{
		var me   = this,
		    data = {};
		
		Ext.apply( data, me.generalTab.getData() );
		Ext.apply( data, me.contentTab.getData() );
		Ext.apply( data, me.templateTab.getData() );
		
		return data;
	},
	
	isDirty : function()
	{
		return false;
	}
});