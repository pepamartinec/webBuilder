Ext.define( 'WebBuilder.widget.BlocksList',
{
	extend : 'Ext.container.Container',

	requires : [
		'Ext.layout.container.Accordion',
		'Ext.panel.Panel',
		'Ext.layout.container.Fit',
		'WebBuilder.widget.blocksList.CategoryView'
	],

	/**
	 * @required
	 * @cfg {Ext.data.Store} store
	 */
	store : null,

	/**
	 * @cfg {String} ddGroup
	 */
	ddGroup : undefined,

	componentCls : Ext.baseCSSPrefix+ 'blocks-list',

	/**
	 * Component initialization
	 *
	 */
	initComponent : function()
	{
		var me = this;

		Ext.apply( me, {
			layout : {
				type    : 'accordion',
				animate : true
			},

			items : []
		});

		me.callParent( arguments );

		me.store.on( 'datachanged', me.onCategoriesChange, me );
	},

	onCategoriesChange : function( store, categories )
	{
		var me = this;

		Ext.Array.forEach( categories, me.addCategory, me );
	},

	addCategory : function( category )
	{
		var me = this;

		var view = Ext.create( 'WebBuilder.widget.blocksList.CategoryView', {
			ddGroup : me.ddGroup,
			store   : category.blocks()
		});

		var panel = Ext.create( 'Ext.panel.Panel', {
			layout : 'fit',
			title  : category.get('title'),
			items  : [ view ]
		});

		this.add( panel );
	}
});