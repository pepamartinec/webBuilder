Ext.define( 'WebBuilder.widget.TemplateCanvas.DropZone', {
	extend : 'WebBuilder.widget.TemplateCanvas.AbstractDropZone',

	/**
	 * Block drop handler
	 *
	 * @param {HTMLElement} [slotDom]
	 * @param {Ext.dd.DragSource} [dragSource]
	 * @param {Event} [e]
	 * @param {Object} [data]
	 * @returns {Boolean}
	 */
	onNodeDrop : function( slotDom, dragSource, e, data )
	{
		var draggedInstance = this.getTargetInstance( data.blockDom );
		    targetInstance  = this.getTargetInstance( slotDom );

		// no instance supplied
		if( draggedInstance == null || targetInstance == null ) {
			return false;
		}

		var position  = this.findInsertPosition( slotDom, e ),
		    slotName  = this.getTargetSlotName( slotDom );

		// insert instance
		this.instancesStore.insert( draggedInstance, targetInstance, slotName, position );

		return true;
	}
});
