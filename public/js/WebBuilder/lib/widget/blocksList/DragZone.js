Ext.define( 'WebBuilder.widget.blocksList.DragZone', {
	extend : 'Ext.view.DragZone',

	dragText : '{0} selected node{1}'

//	/**
//	 * Returns data of the dragged item
//	 *
//	 * @param {Event} [e]
//	 * @returns {Object}
//	 */
//	getDragData: function( e ) {
//		var view     = this.view,
//		    sourceEl = e.getTarget( view.itemSelector );
//
//		if( sourceEl ) {
//			var d = sourceEl.cloneNode( true );
//			d.id = Ext.id();
//
//			return {
//				ddel     : d,
//				sourceEl : sourceEl,
//				repairXY : Ext.fly( sourceEl ).getXY(),
//
//				view        : view,
//				sourceStore : view.store,
//				records     : [ view.getRecord( sourceEl ).data ]
//			};
//		}
//	}
});
