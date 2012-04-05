Ext.define( 'WebBuilder.widget.TemplateCanvas.AbstractDropZone', {
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
	}
});