Ext.define( 'DemoCMS.module.WebEditor.pageEditor.Content',
{
	extend : 'Ext.form.field.HtmlEditor',

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