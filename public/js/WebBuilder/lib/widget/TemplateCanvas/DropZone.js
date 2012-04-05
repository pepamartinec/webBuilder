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
		var blockDom = data.blockDom;
		
		var parentDom = Ext.fly( slotDom ).up( this.blockCls ),
		    position  = this.findInsertPosition( slotDom, e ),
		    parent    = parentDom.blockInstance,
		    instance  = blockDom.blockInstance;
		
		if( parent == null ) {
			// <debug>
				Ext.log({
					level : 'warn',
					msg   : 'Target parent block has no block instance associated.',
					dump  : parentDom
				});
			// </debug>
			
			return false;
		}
		
		// insert instance
		this.instancesStore.insert( instance, parent, position );
		
		// insert block DOM node
		var insertBefore = position && slotDom.childNodes[ position ];
		
		blockDom.parentNode.removeChild( blockDom );
		
		if( insertBefore ) {
			slotDom.insertBefore( blockDom, insertBefore );
			
		} else {
			slotDom.appendChild( blockDom );
		}
		
		return true;
	}
});
