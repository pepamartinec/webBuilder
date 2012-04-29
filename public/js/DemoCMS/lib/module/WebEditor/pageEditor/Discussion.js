Ext.define( 'DemoCMS.module.WebEditor.pageEditor.Discussion',
{
	extend : 'Ext.view.View',

	requires : [
		'DemoCMS.model.DiscussionPost'
	],

	componentCls : Ext.baseCSSPrefix +'discussion-posts',

	itemTpl : [
		'<div class="x-authorName">Autor: {authorName}<tpl if="authorEmail"> &lt;{authorEmail}&gt;</tpl></div>',
		'<div class="x-controls"><div class="x-tool x-remove"></div></div>',
		'<div class="x-date">Datum: {createdOn:date}</div>',
		'<div class="x-content">{content:htmlEncode}</div>'
	],

	store : {
		type  : 'store',
		model : 'DemoCMS.model.DiscussionPost'
	},

	onItemClick : function( record, itemDom, idx, e )
	{
		var btnDom = e.getTarget( '.x-remove' );

		if( btnDom ) {
			this.store.remove( record );
		}
	},

	getData : function()
	{
		return {
			discussion : Ext.Array.map( this.store.getRemovedRecords(), function( record ) {
				return record.getId();
			})
		};
	},

	setData : function( data )
	{
		this.store.loadData( data.discussion || [] );

		return this;
	}
});