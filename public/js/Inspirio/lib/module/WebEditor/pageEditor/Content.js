Ext.define( 'Inspirio.module.WebEditor.pageEditor.Content',
{
	extend : 'Ext.form.field.HtmlEditor',
	
	title : 'Obsah',
	
	getData : function() {
		return {
			content : this.getValue()
		};
	},
	
	setData : function( data )
	{
		this.setValue( data.content );
		
		return this;
	}
});