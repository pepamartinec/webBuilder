Ext.define( 'DemoCMS.module.WebEditor.pageEditor.Images',
{
	extend : 'DemoCMS.component.ImageManager',

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