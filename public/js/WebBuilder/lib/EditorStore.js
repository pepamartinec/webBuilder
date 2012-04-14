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
	 * @private
	 */
	suspendChangeEvt : 0,

// ====================== PUBLIC INTERFACE ====================== //

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
			'add',
			'remove',
			'templatechange',
			'configchange',
			'change'
		);

		me.store = Ext.create( 'Ext.util.HashMap' );
	},

	/**
	 * Removes all instances
	 *
	 * @return {WebBuilder.EditorStore} this
	 */
	clear : function()
	{
		return this.setRoot( null );
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
	 * Sets the instance as root
	 *
	 * @param {WebBuilder.EditorStore.Instance} [instance]
	 * @return {WebBuilder.EditorStore.Instance}
	 */
	setRoot : function( instance )
	{
		var me = this;

		// suspend change event
		// so clients do just bulk get 'change' notification
		me.startChange();

		// remove current root
		var oldRoot = me.root;

		me.clearInstances();
		
		if( oldRoot ) {
			// notify about removal
			me.fireEvent( 'remove', me, oldRoot, null, null, null );
		}

		if( instance ) {
			// remove new root from its original place
			instance.remove();

			// set new root
			me.root = instance;

			me.addInstance( instance );
			me.fireEvent( 'add', me, instance, null, null, null );
		}

		me.commitChange();

		return oldRoot;
	},

// ====================== INNER MANIPULATION METHODS ====================== //

	/**
	 * Adds the instance and all its children.
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

		} else {
			Ext.log({
				level : 'warn',
				msg   : '['+ me.$className +'][addInstance] Instance is already present in this store',
				dump  : {
					store    : me,
					instance : instance
				}
			});
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
	addInstance_walkSlots : function( slotId, slotItems ) {
		Ext.Array.forEach( slotItems, this.addInstance, this );
	},

	/**
	 * Removes the instance and all its children.
	 *
	 * @protected
	 * @param {WebBuilder.EditorStore.Instance}
	 */
	removeInstance : function( instance )
	{
		// remove children from store
		if( instance.slots ) {
			Ext.Object.each( instance.slots, this.removeInstance_walkSlot, this );
		}

		instance.store = null;

		// remove instance from store
		this.store.removeAtKey( instance.id );
	},

	/**
	 * @ignore
	 */
	removeInstance_walkSlot : function( name, children )
	{
		Ext.Array.forEach( children, this.removeInstance, this );
	},

	/**
	 * Removes all instances
	 *
	 * @protected
	 */
	clearInstances : function()
	{
		var me    = this,
		    store = me.store;

		// remove existing data
		if( me.root ) {
			me.removeInstance( me.root );

			me.root = null;
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
				me.removeInstance( instance );
			});
		}
	},

// ====================== INSTANCES NOTIFICATION INTERFACE ====================== //

	startChange : function()
	{
		++this.suspendChangeEvt;
	},

	commitChange : function()
	{
		--this.suspendChangeEvt;

		if( this.suspendChangeEvt === 0 ) {
			this.fireEvent( 'change', this );
		}
	},

	onAddChild : function( instance, child, slotId, position )
	{
		var me = this;

		// add to the store (including all children)
		me.addInstance( child );

		// notify about addition
		me.fireEvent( 'add', me, child, instance, slotId, position );
	},

	onRemoveChild : function( instance, child, slotId, position )
	{
		var me = this;

		// remove from store (including all children)
		me.removeInstance( child );

		// notify about removal
		me.fireEvent( 'remove', me, child, instance, slotId, position );
	},

	onTemplateChange : function( instance, oldTemplate )
	{
		this.fireEvent( 'templatechange', this, instance, oldTemplate );
	},

	onConfigChange : function( instance, config )
	{
		this.fireEvent( 'configchange', this, instance, Ext.Object.getKeys( config ) );
	}
});