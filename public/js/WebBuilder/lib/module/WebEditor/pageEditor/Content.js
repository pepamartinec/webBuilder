Ext.define( 'WebBuilder.module.WebEditor.pageEditor.Content',
{
	extend : 'Ext.form.Panel',
	
	title : 'Obsah',
	
	layout : 'fit',
	
	items : [{
		xtype : 'htmleditor'
	}]
});