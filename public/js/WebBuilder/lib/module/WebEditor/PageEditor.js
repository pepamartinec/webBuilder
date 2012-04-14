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
		
		var generalTab  = Ext.create( 'WebBuilder.module.WebEditor.pageEditor.General' ),
		    contentTab  = Ext.create( 'WebBuilder.module.WebEditor.pageEditor.Content' ),
		    templateTab = Ext.create( 'WebBuilder.module.WebEditor.pageEditor.Template', {
		    	env : me.env
		    });
		
		
		me.border = false;
		me.items  = [ generalTab, contentTab, templateTab ];
		
		
		me.title   = 'Úprava stránky';
		me.iconCls = 'i-edit';
		me.layout  = 'fit';
		
		me.mixins.editor.constructor.call( me );
		
		me.callParent( arguments );
	},
	
	setData : function( data )
	{
		console.log( 'set data ', data );
	},
	
	getData : function()
	{
		return {};
	},
	
	isDirty : function()
	{
		return false;
	}
});