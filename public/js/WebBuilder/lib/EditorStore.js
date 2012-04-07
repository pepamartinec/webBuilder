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
			'templatechange',
			'beforeconfigchange',
			'configchange'
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

		if( me.fireEvent( 'beforeadd', me, instance, null, null, null ) === false ) {
			return;
		}

		// remove instance from its original place
		instance.remove();

		// replace current instances with new ones
		var oldRoot = me.root;

		if( oldRoot ) {
			me.remove( oldRoot );
		}

		me.root = instance;
		me.addInstance( instance );

		me.fireEvent( 'add', me, instance, null, null, null );

		return oldRoot;
	},

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
	addInstance_walkSlots : function( slotName, slotItems ) {
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



	onBeforeAddChild : function( instance, child, slotName, position )
	{
		return this.fireEvent( 'beforeadd', this, child, instance, slotName, position );
	},

	onAddChild : function( instance, child, slotName, position )
	{
		// add to the store (including all children)
		this.addInstance( child );

		// notify about addition
		this.fireEvent( 'add', this, child, instance, slotName, position );
	},

	onBeforeRemoveChild : function( instance, child, slotName, position )
	{
		return this.fireEvent( 'beforeremove', this, child, instance, slotName, position );
	},

	onRemoveChild : function( instance, child, slotName, position )
	{
		var me = this;

		// remove from store (including all children)
		me.removeInstance( child );

		// notify about removal
		this.fireEvent( 'remove', this, child, instance, slotName, position );
	},

	onBeforeTemplateChange : function( instance, template )
	{
		return this.fireEvent( 'beforetemplatechange', this, instance, template );
	},

	onTemplateChange : function( instance, oldTemplate )
	{
		return this.fireEvent( 'templatechange', this, instance, oldTemplate );
	},

	onBeforeConfigChange : function( instance, config )
	{
		return this.fireEvent( 'beforeconfigchange', this, instance, config );
	},

	onConfigChange : function( instance, config )
	{
		return this.fireEvent( 'configchange', this, instance, Ext.Object.getKeys( config ) );
	}
});