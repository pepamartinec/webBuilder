Ext.define( 'WebBuilder.widget.TemplateCanvas.BlocksListDropZone', {
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
		var block          = data.records[0],
		    targetInstance = this.getTargetInstance( slotDom );

		// no block supplied
		if( block == null || targetInstance == null ) {
			return false;
		}

		var position  = this.findInsertPosition( slotDom, e ),
		    slotName  = this.getTargetSlotName( slotDom ),
		    instance  = Ext.create( 'WebBuilder.BlockInstance', block );

		// select default template
		this.instancesStore.setTemplate( instance, block.templates().getAt(0) );

		// insert instance
		this.instancesStore.insert( instance, targetInstance, slotName, position );

		return true;
	}
});
