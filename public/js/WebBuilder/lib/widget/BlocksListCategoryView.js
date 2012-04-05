Ext.define( 'WebBuilder.widget.BlocksListCategoryView',
{
	extend : 'Ext.view.View',
	
	requires : [
		'Ext.dd.DragZone'
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
		me.dragZone = Ext.create( 'Ext.view.DragZone', {
			view     : me,
			ddGroup  : me.ddGroup,
			dragText : '{0} selected node{1}'
		});
		
		me.callParent( arguments );
	},
	
	getDragData: function( e ) {
		var me       = this,
		    sourceEl = e.getTarget( this.itemSelector );
		
		if( sourceEl ) {
			var d = sourceEl.cloneNode( true );
			d.id = Ext.id();
			
			return {
				ddel     : d,
				sourceEl : sourceEl,
				repairXY : Ext.fly( sourceEl ).getXY(),
				
				sourceStore : me.store,
				records     : [ me.getRecord( sourceEl ).data ]
			};
		}
	}
});