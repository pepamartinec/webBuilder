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
	 * Slot id regexp
	 *
	 * @required
	 * @cfg {RegExp} slotIdRe
	 */
	slotIdRe : null,

	/**
	 * Drop position pointer DOM node
	 *
	 * @property {HTMLElement} insertPtrDom
	 */
	insertPtrDom : null,

	/**
	 * Constructor
	 *
	 * @param {Object} [config]
	 */
	constructor : function( config )
	{
		var me = this;

		me.callParent( arguments );

		me.insertPtrDom = Ext.DomHelper.createDom({
			tag : 'hr',
	    	cls : Ext.baseCSSPrefix +'insert-pointer'
		});
	},

	/**
	 * Returns valid drop target (if exists)
	 *
	 * @param {Ext.EventObject} [e]
	 * @returns {HTMLElement}
	 */
	getTargetFromEvent : function( e )
	{
//		return Ext.fly( e.target ).hasCls( this.slotCls ) ? e.target : null;

		var blockCls = '.'+ this.blockCls,
		    slotCls  = '.'+ this.slotCls,
		    target = e.target,
		    limit  = 5;

		while( target && --limit >= 0 ) {
			if( Ext.DomQuery.is( target, slotCls ) ) {
				return target;

			} else if( Ext.DomQuery.is( target, blockCls ) ) {
				return false;
			}

			target = target.parentNode;
		}

		return false;
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

	/**
	 * Returns ID of the slot represented by the DOM node
	 *
	 * @param {HTMLElement} [slotDom]
	 * @returns {Number}
	 */
	parseSlotId : function( slotDom )
	{
		var match = slotDom.id.match( this.slotIdRe ),
	        id    = parseInt( match[1] );

		return isNaN( id ) ? null : id;
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

		Ext.log({
			msg  : '['+ this.$className +'][onNodeDrop] Invalid drag source',
			dump : dragSource
		});

		return false;
	},

	handleBlockListDrop : function( slotDom, dragSource, e, data )
	{
		var block          = data.block,
		    targetInstance = this.getTargetInstance( slotDom );

		// no block supplied
		if( block == null || targetInstance == null ) {
			return false;
		}

		var position  = this.findInsertPosition( slotDom, e ),
		    slotId    = this.parseSlotId( slotDom ),
		    instance  = Ext.create( 'WebBuilder.BlockInstance', block );

		// select default template
		instance.setTemplate( block.templates().getAt(0) );

		// insert instance
		targetInstance.addChild( instance, slotId, position );

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

		var position = this.findInsertPosition( slotDom, e ),
		    slotId   = this.parseSlotId( slotDom );

		// insert instance
		targetInstance.addChild( draggedInstance, slotId, position );

		return true;
	}
});
