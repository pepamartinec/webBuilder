Ext.define( 'Inspirio.module.WebEditor.pageEditor.Images',
{
	extend : 'Inspirio.component.ImageManager',

	getData : function()
	{
		return {};
	},

	setData : function( data )
	{
		var me = this;

		me.webPageId = data.ID;

		if( me.uploadPopup ) {
			me.loadData( data.ID );
		}

		return this;
	}
});