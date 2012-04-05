Ext.define( 'WebBuilder.widget.blocksList.CategoryView',
{
	extend : 'Ext.view.View',

	requires : [
		'WebBuilder.widget.blocksList.DragZone'
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

	itemTpl : '{[ values.title || values.codeName ]}',

	onRender : function()
	{
		var me = this;

		// init dragZone
		me.dragZone = Ext.create( 'WebBuilder.widget.blocksList.DragZone', {
			view     : me,
			ddGroup  : me.ddGroup
		});

		me.callParent( arguments );
	}
});