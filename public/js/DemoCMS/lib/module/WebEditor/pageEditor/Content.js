Ext.define( 'DemoCMS.module.WebEditor.pageEditor.Content',
{
	extend : 'DemoCMS.widget.HtmlEditor',

	requires : [
		'DemoCMS.widget.htmlEditor.ImagePlugin'
	],

	constructor : function( config )
	{
		var me = this;

		me.imagePlugin = Ext.create( 'DemoCMS.widget.htmlEditor.ImagePlugin', {
			editor : config.editor
		});

		config.plugins = [ me.imagePlugin ];

		me.callParent([ config ]);
	},

	getData : function() {
		return {
			content : this.getValue()
		};
	},

	setData : function( data )
	{
		this.setValue( data.content );
		this.imagePlugin.parentId = data.ID;

		return this;
	}
});