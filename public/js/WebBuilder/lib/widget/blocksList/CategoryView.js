Ext.define( 'WebBuilder.widget.blocksList.CategoryView',
{
	extend : 'Ext.view.View',

	/**
	 * @required
	 * @cfg {Ext.data.Store} store
	 */
	data : null,

	itemSelector : Ext.baseCSSPrefix +'template-block',

	tpl : [
		'<tpl for=".">',
			'<div class="', Ext.baseCSSPrefix ,'template-block ', Ext.baseCSSPrefix ,'template-block-{ID}">{[ values.title || values.codeName ]}</div>',
		'</tpl>'
	]
});