Ext.define( 'WebBuilder.widget.TemplateCanvas', {
	extend : 'Ext.Component',

	requires : [
		'extAdmin.patch.DropZoneTargetInitialization',

		'WebBuilder.widget.templateCanvas.DragZone',
		'WebBuilder.widget.templateCanvas.DropZone'
	],

	/**
	 * @required
	 * @cfg {extAdmin.Module} module
	 */
	module : null,

	/**
	 * @required
	 * @cfg {WebBuilder.EditorStore} instancesStore
	 */
	instancesStore : null,

	/**
	 * @cfg {String} ddGroup
	 */
	ddGroup : 'template-canvas',

	/**
	 * ClassName of canvas root element
	 *
	 * @cfg {String} canvasCls
	 */
	canvasCls : Ext.baseCSSPrefix +'template-canvas',

	/**
	 * ClassName of block nodes
	 *
	 * @cfg {String} blockCls
	 */
	blockCls : Ext.baseCSSPrefix +'block',

	/**
	 * ClassName of block title node
	 *
	 * @cfg {String} blockTitleCls
	 */
	blockTitleCls : Ext.baseCSSPrefix +'block-title',

	/**
	 * ClassName of block tools container node
	 *
	 * @cfg {String} blockToolsCls
	 */
	blockToolsCls : Ext.baseCSSPrefix +'tools',

	/**
	 * ClassName of block tool node
	 *
	 * @cfg {String} blockToolCls
	 */
	blockToolCls : Ext.baseCSSPrefix +'tool',

	/**
	 * ClassName of config tool icon
	 *
	 * @cfg {String} configToolCls
	 */
	configToolCls : Ext.baseCSSPrefix +'config',

	/**
	 * ClassName of remove tool icon
	 *
	 * @cfg {String} removeToolCls
	 */
	removeToolCls : Ext.baseCSSPrefix +'remove',

	/**
	 * ClassName of slot nodes
	 *
	 * @cfg {String} slotCls
	 */
	slotCls : Ext.baseCSSPrefix +'slot',


	/**
	 * ClassName of slot title nodes
	 *
	 * @cfg {String} slotTitleCls
	 */
	slotTitleCls : Ext.baseCSSPrefix +'slot-title',

	/**
	 * ClassName of title nodes
	 *
	 * @cfg {String} titleCls
	 */
	titleCls : Ext.baseCSSPrefix +'title',

	/**
	 * Slot highlight className
	 *
	 * @cfg {String} overCls
	 */
	overCls : Ext.baseCSSPrefix +'over',

	/**
	 * ClassName applied on dragged node
	 *
	 * @cfg {String} dragCls
	 */
	dragCls : Ext.baseCSSPrefix +'drag',

	/**
	 * @private
	 * @property {RegExp} instanceIdRe
	 */
	instanceIdRe : new RegExp( 'template-block-instance-(\\d+)' ),

	/**
	 * Drop position pointer DOM node
	 *
	 * @property {HTMLElement}
	 */
	insertPtrDom : {
    	tag : 'hr',
    	cls : Ext.baseCSSPrefix +'insert-pointer'
    },

	html : {
		tag : 'iframe',
		src : 'about:blank',

		width  : '100%',
		height : '100%',
		frameborder : '0'
	},

	/**
	 * Component initialization
	 *
	 * @protected
	 */
	initComponent : function()
	{
		var me = this;

		me.callParent();

        Ext.apply( me.renderSelectors, {
            iframeEl : 'iframe'
        });

        // create insert position pointer instance
        me.insertPtrDom = Ext.DomHelper.createDom( me.insertPtrDom );

        // create block-tools instance
//        me.toolsEl      = Ext.get( Ext.DomHelper.createDom( me.toolsEl ) );
//        me.configToolEl = me.toolsDom.down( Ext.baseCSSPrefix +'config' );
//        me.removeToolEl = me.toolsDom.down( Ext.baseCSSPrefix +'remove' );
//
//        me.configToolEl.on( 'click', function() { console.log('config'); } );
//        me.removeToolEl.on( 'click', me.handleRemoveToolClick, me );
	},

	/**
	 * Component cleanup
	 *
	 * @protected
	 */
	beforeDestroy : function()
	{
		var me = this;

		me.cleanupIframe();

		me.callParent();
	},

	/**
	 * Events initialization
	 *
	 * @protected
	 */
	initEvents : function()
	{
		var me = this;

		me.callParent();

		// iFrame
		me.iframeEl.on( 'load', me.initIframe, me );
		me.iframeEl.on( 'unload', me.cleanupIframe, me );
		me.iframeEl.set({ 'src' : 'about:blank' });

		// instances store
		me.mon( me.instancesStore, 'add', me.handleInstanceAdd, me );
		me.mon( me.instancesStore, 'remove', me.handleInstanceRemove, me );
		me.mon( me.instancesStore, 'templatechange', me.handleInstanceTemplateChange, me );
	},

	/**
	 * iFrame initialization
	 *
	 * @protected
	 */
	initIframe : function()
	{
		var me  = this;

		var doc = me.iframeEl.dom.contentDocument;

		me.headEl = Ext.get( doc.head );
		me.bodyEl = Ext.get( doc.body );

		// load stylesheets
		Ext.DomHelper.append( me.headEl.dom, {
			tag  : 'link',
			rel  : 'stylesheet',
			type : 'text/css',
			href : 'css/WebAdmin.css'
		});

		Ext.DomHelper.append( me.headEl.dom, {
			tag  : 'link',
			rel  : 'stylesheet',
			type : 'text/css',
			href : me.module.getActionUrl( 'loadSimplifiedStylesheet', {
				stylesheet : 'public/css/style.css'
			})
		});

		// mark canvas root
		me.bodyEl.addCls( me.canvasCls );

		// add iFrame to document.getElementById chain
		extAdmin.addElementGetter( doc.getElementById, doc );

		// init D&D
		Ext.EventManager.on( doc, 'mousemove', me.handleIframeMouseMove, me );
		Ext.EventManager.on( doc, 'mouseup',   me.handleIframeMouseUp,   me );
		Ext.EventManager.on( doc, 'click',     me.handleIframeClick,     me );

		me.dragZone = Ext.create( 'WebBuilder.widget.templateCanvas.DragZone', me.bodyEl, {
			ddGroup : me.ddGroup,

			blockCls      : me.blockCls,
			blockTitleCls : me.blockTitleCls,
			dragCls       : me.dragCls
		});

		me.dropZone = Ext.create( 'WebBuilder.widget.templateCanvas.DropZone', me.iframeEl, {
			ddGroup        : me.ddGroup,
			instancesStore : me.instancesStore,

			blockCls     : me.blockCls,
			slotCls      : me.slotCls,
			overCls      : me.overCls,
			insertPtrDom : me.insertPtrDom,
			instanceIdRe : me.instanceIdRe
		});

		var store = me.instancesStore,
		    root  = store.getRoot();

		if( root ) {
			me.handleInstanceAdd( store, root );
		}
	},

	/**
	 * Iframe cleanup
	 *
	 * @protected
	 */
	cleanupIframe : function()
	{
		var me = this;

		if( me.iframeEl ) {
			var doc = me.iframeEl.dom.contentDocument;

			Ext.EventManager.un( doc, 'mousemove', me.handleIframeMouseMove );
			Ext.EventManager.un( doc, 'mouseup',   me.handleIframeMouseUp   );
			Ext.EventManager.un( doc, 'click',     me.handleIframeClick   );

			extAdmin.removeElementGetter( doc.getElementById );
		}
	},

	handleIframeMouseMove : function( event )
	{
		var iframeXY = this.iframeEl.getXY(),
		    eventXY  = event.getXY();

		// fake event position & relay to DD manager
		event.innerXY = eventXY;
		event.xy = [ iframeXY[0] + eventXY[0], iframeXY[1] + eventXY[1] ];
		Ext.dd.DragDropManager.handleMouseMove( event );

		// styling helpers
		var blockDom = event.getTarget( '.'+ this.blockCls );

		if( blockDom != this.lastOverBlock ) {
			if( this.lastOverBlock ) {
				Ext.fly( this.lastOverBlock ).removeCls( this.overCls );
				this.lastOverBlock = null;
			}

			if( blockDom ) {
				Ext.fly( blockDom ).addCls( this.overCls );
				this.lastOverBlock = blockDom;
			}
		}

		// restore original position
		event.xy = eventXY;
	},

	handleIframeMouseUp : function( event )
	{
		var iframeXY = this.iframeEl.getXY(),
		    eventXY  = event.getXY();

		// fake event position & relay to DD manager
		event.innerXY = eventXY;
		event.xy = [ iframeXY[0] + eventXY[0], iframeXY[1] + eventXY[1] ];
		Ext.dd.DragDropManager.handleMouseUp( event );

		// restore original position
		event.xy = eventXY;
	},

	handleIframeClick : function( event )
	{
		// check tool click
		var me     = this,
		    target = Ext.fly( event.target );

		if( target.hasCls( me.blockToolCls ) ) {
			var blockDom = target.findParent( '.'+ me.blockCls ),
		        instance = me.getBlockInstance( blockDom );

			if( instance ) {
				// CONFIG
				if( target.hasCls( me.configToolCls ) ) {
					console.log( instance.block.get('title') );

				// REMOVE
				} else if( target.hasCls( me.removeToolCls ) ) {
					me.instancesStore.remove( instance );
				}
			}
		}
	},

	/**
	 * Handler for insertion of the block instance from store
	 *
	 * @param {WebBuilder.EditorStore} [store]
	 * @param {WebBuilder.BlockInstance} [instance]
	 * @param {WebBuilder.BlockInstance} [parentBlock]
	 * @param {String} [parentSlotName]
	 * @param {Number,Null} [position]
	 */
	handleInstanceAdd : function( store, instance, parentBlock, parentSlotName, position )
	{
		var me = this;

		// create block DOM node
		var blockDom = Ext.DomHelper.createDom( me.createBlockDefinition( instance ) );

		if( parentBlock ) {
			var slotDom      = me.findSlotDom( parentBlock, parentSlotName ),
			    insertBefore = slotDom.childNodes[ position ];

			if( insertBefore ) {
				slotDom.insertBefore( blockDom, insertBefore );

			} else {
				slotDom.appendChild( blockDom );
			}

		} else {
			this.bodyEl.dom.appendChild( blockDom );
		}
	},

	/**
	 * Handler for removal of the block instance from store
	 *
	 * @param {WebBuilder.EditorStore} [store]
	 * @param {WebBuilder.BlockInstance} [instance]
	 */
	handleInstanceRemove : function( store, instance )
	{
		var blockDom = this.findBlockDom( instance );

		if( blockDom ) {
			Ext.fly( blockDom ).remove();
		}
	},

	/**
	 * Handler for change of block instance template
	 *
	 * We need to re-render complete block, because slots may change with
	 * different templates
	 *
	 * @param {WebBuilder.EditorStore} [store]
	 * @param {WebBuilder.BlockInstance} [instance]
	 * @param {Object} [oldSlots]
	 */
	handleInstanceTemplateChange : function( store, instance, oldSlots )
	{
		var me = this;

		// find existing DOM node
		var blockDom = me.findBlockDom( instance );

		// instance not rendered yet, nothing to do
		if( blockDom == null ) {
			return;
		}

		// replace old DOM node with refreshed one
		var newBlockDom = Ext.DomHelper.createDom( me.createBlockDefinition( instance ) );
		Ext.fly( newBlockDom ).replace( blockDom );
	},

	/**
	 * Handles user block configuration request
	 *
	 * @param {Event} [event]
	 */
	handleConfigToolClick : function( event )
	{
		console.log('configure me');
	},

	/**
	 * Returns instance belonging to the block DOM node
	 *
	 * @param {HTMLElement} [blockDom]
	 * @return {WebBuilder.BlockInstance}
	 */
	getBlockInstance : function( blockDom )
	{
		var match = blockDom.id.match( this.instanceIdRe );

		if( match == null ) {
			return null;
		}

		return this.instancesStore.get( match[1] );
	},

	/**
	 * Finds DOM node representing the block instance (if any)
	 *
	 * @param {WebBuilder.BlockInstance} [instance]
	 * @return {HTMLElement}
	 */
	findBlockDom : function( instance )
	{
		return this.iframeEl.dom.contentDocument.getElementById( 'template-block-instance-'+ instance.id );
	},

	/**
	 * Returns the slot DOM node
	 *
	 * @param {WebBuilder.BlockInstance} [instance]
	 * @param {String} [slotName]
	 * @return {HTMLElement}
	 */
	findSlotDom : function( instance, slotName )
	{
		return this.iframeEl.dom.contentDocument.getElementById( 'template-block-instance-'+ instance.id +'-slot-'+ slotName );
	},

	/**
	 * Create DOM node of given block instance
	 *
	 * @param {WebBuilder.BlockInstance} [instance]
	 * @returns {HTMLElement}
	 */
	createBlockDom : function( instance )
	{
		return Ext.DomHelper.createDom( this.createBlockDefinition( instance ) );
	},

	/**
	 * Creates Ext.dom.Helper definition of block (including its children)
	 *
	 * @param {WebBuilder.BlockInstance} [instance]
	 * @returns {Object}
	 */
	createBlockDefinition : function( instance )
	{
		var me = this;

		// create block DOM node definition
		var block    = instance.block,
		    blockDef = {
				tag : 'div',
				id  : 'template-block-instance-'+ instance.id,
				cls : me.blockCls,

				children : [{
					tag  : 'div',
					cls  : [ me.titleCls, me.blockTitleCls ].join(' '),

					children : [{
						tag  : 'span',
						html : block.get('title')

					},{
				    	tag : 'div',
				    	cls : me.blockToolsCls,

				    	children : [{
				    		tag : 'div',
				    		cls : [ me.blockToolCls, me.configToolCls ].join(' ')

				    	},{
				    		tag : 'div',
				    		cls : [ me.blockToolCls, me.removeToolCls ].join(' ')
				    	}]
				    }]
				}]
			};

		// create slots DOM nodes definition
		if( instance.slots ) {
			Ext.Object.each( instance.slots, function( name, children ) {
				var childrenDef = Ext.Array.map( children, me.createBlockDefinition, me );

				childrenDef.unshift({
					tag  : 'div',
					cls  : [ me.titleCls, me.slotTitleCls ].join(' '),
					html : name
				});

				blockDef.children.push({
					tag : 'div',
					id  : 'template-block-instance-'+ instance.id +'-slot-'+ name,
					cls : me.slotCls,

					children : childrenDef
				});
			});
		}

		return blockDef;
	}
});