Ext.define( 'WebBuilder.widget.templateCanvas.DropZone', {
	extend : 'Ext.dd.DropZone',

	/**
	 * ClassName of block nodes
	 *
	 * @required
	 * @cfg {String} blockCls
	 */
	blockCls : null,

	/**
	 * ClassName of slot nodes
	 *
	 * @required
	 * @cfg {String} slotCls
	 */
	slotCls : null,

	/**
	 * Slot highlight className
	 *
	 * @required
	 * @cfg {String} overCls
	 */
	overCls : null,

	/**
	 * Block instance id regexp
	 *
	 * @required
	 * @cfg {RegExp} instanceIdRe
	 */
	instanceIdRe : null,

	/**
	 * Drop position pointer DOM node
	 *
	 * @required
	 * @cfg {HTMLElement}
	 */
	insertPtrDom : null,

	/**
	 * Returns valid drop target (if exists)
	 *
	 * @param {Ext.EventObject} [e]
	 * @returns {HTMLElement}
	 */
	getTargetFromEvent : function( e )
	{
		return e.getTarget( '.'+ this.slotCls );
	},

	/**
	 * Slot mouseenter event handler
	 *
	 * @param {HTMLElement} [slotDom]
	 * @param {Ext.dd.DragSource} [dragSource]
	 * @param {Event} [e]
	 * @param {Object} [data]
	 */
	onNodeEnter : function( slotDom, dragSource, e, data )
	{
		// highlight entered slot
		Ext.fly( slotDom ).addCls( this.overCls );
	},

	/**
	 * Slot mouseover event handler
	 *
	 * @param {HTMLElement} [slotDom]
	 * @param {Ext.dd.DragSource} [dragSource]
	 * @param {Event} [e]
	 * @param {Object} [data]
	 * @returns {String}
	 */
	onNodeOver : function( slotDom, dragSource, e, data )
	{
		// show drop position pointer
		var insertPosition = this.findInsertPosition( slotDom, e ),
		    insertBefore   = insertPosition && slotDom.childNodes[ insertPosition ];

		if( insertBefore ) {
			slotDom.insertBefore( this.insertPtrDom, insertBefore );

		} else {
			slotDom.appendChild( this.insertPtrDom );
		}

		return Ext.dd.DropZone.prototype.dropAllowed;
	},

	/**
	 * Slot mouseout event handler
	 *
	 * @param {HTMLElement} [slotDom]
	 * @param {Ext.dd.DragSource} [dragSource]
	 * @param {Event} [e]
	 * @param {Object} [data]
	 */
	onNodeOut : function( slotDom, dragSource, e, data )
	{
		// remove slot highlight
		Ext.fly( slotDom ).removeCls( this.overCls );

		// remove drop position pointer
		slotDom.removeChild( this.insertPtrDom );
	},

	/**
	 * Returns the position, where in the slot should be a child
	 * block dropped.
	 *
	 * @param {HTMLElement} [slotDom]
	 * @param {Event} [e]
	 * @returns {Number}
	 */
	findInsertPosition : function( slotDom, e )
	{
		var children = slotDom.children;

		if( children ) {
			// use iFrame inner coordinates
			var y = e.innerXY[1];

			for( var i = 0, cl = children.length; i < cl; ++i ) {
				if( children[i].className != this.blockCls ) {
					continue;
				}

				if( children[i].offsetTop > y ) {
					return i;
				}
			};
		}

		return null;
	},

	/**
	 * Returns the drop target instance.
	 *
	 * @param {HTMLElement} [slotDom]
	 * @returns {WebBuilder.BlockInstance}
	 */
	getTargetInstance : function( slotDom )
	{
		// find parent block
		var parentDom = Ext.fly( slotDom ).findParent( '.'+ this.blockCls );

		// pick block id
		var match      = parentDom.id.match( this.instanceIdRe ),
		    instanceId = match && match[1];

		if( ! instanceId ) {
			return null;
		}

		return this.instancesStore.get( instanceId );
	},

	getTargetSlotName : function( slotDom )
	{
		return slotDom.children[0].innerHTML;
	},

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
		// ugly switch is needed here because we have more kind
		// of dragSources (blockList, canvas self), but Ext allows
		// only one DropZone instance per target

		if( dragSource instanceof WebBuilder.widget.blocksList.DragZone ) {
			return this.handleBlockListDrop( slotDom, dragSource, e, data );
		}

		if( dragSource instanceof WebBuilder.widget.templateCanvas.DragZone ) {
			return this.handleCanvasDrop( slotDom, dragSource, e, data );
		}

		return false;
	},

	handleBlockListDrop : function( slotDom, dragSource, e, data )
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
	},

	handleCanvasDrop : function( slotDom, dragSource, e, data )
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
