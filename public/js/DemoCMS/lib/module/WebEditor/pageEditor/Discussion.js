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
		var me = this;

		if( e.getTarget( '.x-remove' ) ) {
			Ext.MessageBox.show({
				title    : 'Smazat diskuzní příspěvek?',
				msg      : 'Opravdu chcete tento diskuzní příspěvek smazat?',
				buttons  : Ext.MessageBox.YESNO,
				icon     : Ext.MessageBox.QUESTION,
				closable : false,
				fn       : function( buttonId ) {
					if( buttonId !== 'yes' ) {
						return;
					}

					me.store.remove( record );
				}
			});
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