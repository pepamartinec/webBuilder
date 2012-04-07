Ext.define( 'WebBuilder.EditorStore', {
	requires : [ 'Ext.util.HashMap' ],

	mixins : {
		observable : 'Ext.util.Observable'
	},

	/**
	 * Instances store
	 *
	 * @protected
	 * @property {Ext.util.HashMap} store
	 */
	store : null,

	/**
	 * Root instance
	 *
	 * @protected
	 * @property {WebBuilder.EditorStore.Instance} root
	 */
	root : null,

	/**
	 * Constructor
	 *
	 * @param {WebBuilder.EditorStore.Instance} rootInstance The root block instance
	 */
	constructor : function( config )
	{
		var me = this;

		me.mixins.observable.constructor.call( me, config );

		me.addEvents(
			'beforeadd',
			'add',
			'beforeremove',
			'remove',
			'beforetemplatechange',
			'templatechange'
		);

		me.store = Ext.create( 'Ext.util.HashMap' );
	},

	/**
	 * Adds instance to instances tree and storage
	 *
	 * @protected
	 * @param {WebBuilder.EditorStore.Instance}
	 */
	addInstance : function( instance )
	{
		var me    = this,
		    store = me.store,
		    id    = instance.id;

		// add instance to store
		if( store.containsKey( id ) == false ) {
			store.add( id, instance );
		}

		instance.store = me;

		// add all instance children
		if( instance.slots != null ) {
			Ext.Object.each( instance.slots, me.addInstance_walkSlots, me );
		}
	},

	/**
	 * @ignore
	 */
	addInstance_walkSlots : function( slotName, slotItems ) {
		Ext.Array.forEach( slotItems, this.addInstance, this );
	},

	/**
	 * Returns the instance with given id.
	 *
	 * @param {String} [id]
	 * @returns {WebBuilder.EditorStore.Instance}
	 */
	get : function( id )
	{
		return this.store.get( id );
	},

	/**
	 * Returns the root instance.
	 *
	 * @returns {WebBuilder.EditorStore.Instance}
	 */
	getRoot : function()
	{
		return this.root;
	},

	/**
	 * Inserts the block instance at given position
	 *
	 * @param {WebBuilder.EditorStore.Instance} [instance]
	 * @param {WebBuilder.EditorStore.Instance} [parentBlock]
	 * @param {String} [parentSlotName]
	 * @param {WebBuilder.EditorStore.Instance} [insertBefore=null]
	 * @returns
	 */
	insert : function( instance, parent, parentSlotName, position )
	{
		var me = this;

		if( me.fireEvent( 'beforeadd', me, instance, parent, parentSlotName, position ) === false ) {
			return;
		}

		// remove instance from its original position first
		var originalStore = instance.store;

		if( originalStore ) {
			originalStore.remove( instance );
		}

		// insert instance under its new parent
		var parentSlot = parent.slots[ parentSlotName ];

		if( parentSlot ) {
			if( position === undefined || position === null ) {
				parentSlot.push( instance );

			} else {
				Ext.Array.insert( parentSlot, position, instance );
			}
		}

		// save instance to the store
		me.addInstance( instance );

		me.fireEvent( 'add', me, instance, parent, parentSlotName, position );
	},

	/**
	 * Sets the instance as root
	 *
	 * @param {WebBuilder.EditorStore.Instance} [instance]
	 * @return {WebBuilder.EditorStore.Instance}
	 */
	setRoot : function( instance )
	{
		var me = this;

		if( me.fireEvent( 'beforeadd', me, instance, null, null, null ) === false ) {
			return;
		}

		// remove instance from its original position first
		var originalStore = instance.store;

		if( originalStore ) {
			originalStore.remove( instance );
		}

		// replace current instances with new ones
		var oldRoot = me.root;
		me.root = instance;

		if( oldRoot ) {
			me.remove( oldRoot );
		}

		me.addInstance( instance );



		me.fireEvent( 'add', me, instance, null, null, null );

		return oldRoot;
	},

	/**
	 * Removes all instances
	 *
	 * @return {WebBuilder.EditorStore} this
	 */
	clear : function()
	{
		var me    = this,
		    store = me.store;

		// remove existing data
		if( me.root ) {
			me.remove( root );
		}

		// check for orphaned instances
		if( store.getCount() > 0 ) {
			// this should never happen, because all instances
			// should be somewhere under the root and already
			// be removed with it

			// <debug>
				Ext.log({
					level : 'warn',
					msg   : '['+ me.$className +'][clear] Store contains orphaned instance.',
					store : store
				});
			// </debug>

			// remove orphaned instances one by one
			store.each( function( id, instance ) {
				me.remove( instance );
			});
		}

		return this;
	},

	/**
	 * Removes the block instance.
	 *
	 * @param {WebBuilder.EditorStore.Instance} [instance] The block to remove.
	 */
	remove : function( instance )
	{
		var me = this;

		// <debug>
			me.checkInstance( instance, true, true );
		// </debug>

		if( me.fireEvent( 'beforeremove', me, instance ) === false ) {
			return;
		}

		// remove from store (including all children)
		me.remove_removeInstace( instance );

		// remove from parent
		var parent      = instance.parent,
		    slotName    = null,
		    instanceIdx = null;

		if( parent ) {
			var Array       = Ext.Array,
			    parentSlots = parent.slots,
			    slotInstances;

			for( slotName in parentSlots ) {
				if( parentSlots.hasOwnProperty( name ) === false ) {
					continue;
				}

				slotInstances = parent.slots[ name ];
				instanceIdx   = Array.indexOf( slotInstances, instance );

				if( instanceIdx !== -1 ) {
					Array.erase( slotInstances, instanceIdx, 1 );
					break;
				}
			}

			if( instanceIdx === -1 ) {
				instanceIdx = null;
			}
		}

		// notify about removal
		me.fireEvent( 'remove', me, instance, parent, slotName, instanceIdx );
	},

	/**
	 * @ignore
	 */
	remove_removeInstace : function( instance )
	{
		// remove children
		if( instance.slots ) {
			Ext.Object.each( instance.slots, this.remove_removeSlotChildren, this );
		}

		// remove self
		this.store.removeAtKey( instance.id );
	},

	/**
	 * @ignore
	 */
	remove_removeSlotChildren : function( name, children )
	{
		Ext.Array.forEach( children, this.remove_removeInstace, this );
	},

	/**
	 * Sets the instance active template.
	 *
	 * @param {WebBuilder.EditorStore.Instance} [instance]
	 * @param {WebBuilder.model.BlockTemplate} [template]
	 */
	setTemplate : function( instance, template )
	{
		var me = this;

		// <debug>
			me.checkInstance( instance, false, true );
		// </debug>

		if( me.fireEvent( 'beforetemplatechange', me, instance, template ) === false ) {
			return;
		}

		var oldSlots = instance.slots || {};

		instance.template = template;
		instance.slots    = {};

		template.slots().each( function( slot ) {
			var name = slot.get('codeName');

			if( oldSlots[ name ] ) {
				instance.slots[ name ] = oldSlots[ name ];
				delete oldSlots[ name ];

			} else {
				instance.slots[ name ] = [];
			}
		});

		me.fireEvent( 'templatechange', me, instance, oldSlots );

		return oldSlots;
	},

	/**
	 * Check the block instance for valid store assignment
	 *
	 * @private
	 * @param {WebBuilder.EditorStore.Instance} [instance]
	 * @param {Boolean} [belongsToMe]
	 */
	checkInstance : function( instance, needsStore, belongsToMe )
	{
		var me = this;

		if( needsStore && instance.store == null ) {
			Ext.log({
				level : 'warn',
				msg   : '['+ me.$className +'] The block instance has no store associated',
				dump  : instance
			});

		}

		if( belongsToMe && me.store.containsKey( instance.id ) === false ) {
			Ext.log({
				level : 'warn',
				msg   : '['+ me.$className +'] The block instance is not associated with current store',
				dump  : instance
			});
		}
	}
});