Ext.define( 'WebBuilder.widget.DataLinkField', {
	extend : 'Ext.form.field.Display',
	
	valueToRaw : function( value )
	{
		return value['providerID'] +' :: '+ value['providerProperty'];
	}
});