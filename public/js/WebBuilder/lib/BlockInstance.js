Ext.define( 'WebBuilder.BlockInstance', {

	statics : {
		idCounter : 0,

		genId : function()
		{
			return ( ++this.idCounter ).toString();
		}
	},

	id        : null,
	store     : null,
	parent    : null,
	block     : null,
	template  : null,
	slots     : null,
	config    : null,

	/**
	 * Constructor
	 *
	 * @param {WebBuilder.model.Block} [block]
	 * @param {WebBuilder.model.BlockTemplate} [template=null]
	 */
	constructor : function( block, template )
	{
		var me = this;

		me.id     = me.self.genId();
		me.block  = block;
		me.config = {};

		if( template ) {
			me.setTemplate( template );
		}
	},

	notifyStore : function( event, args )
	{
		if( this.store == null ) {
			return true;
		}

		args = Array.prototype.slice.call( args, 0 );
		args.unshift( this );

		return this.store[ 'on'+ Ext.String.capitalize( event ) ].apply( this.store, args );
	},

	addChild : function( instance, slotName, position )
	{
		var me   = this,
		    slot = me.slots[ slotName ];

		if( slot == null ) {
			Ext.log({
				level : 'warn',
				msg   : '['+ me.$className +'][addChild] Invalid slot "'+ slotName +'".',
				dump  : me
			});

			return me;
		}

		// notify store
		if( me.notifyStore( 'beforeAddChild', arguments ) === false ) {
			return;
		}

		// remove instance from its original parent first
		instance.remove();

		// insert instance into target slot
		if( Ext.isEmpty( position ) ) {
			slot.push( instance );

		} else {
			Ext.Array.insert( slot, position, instance );
		}

		// link self as parent
		instance.parent = me;

		// notify store
		me.notifyStore( 'addChild', arguments );

		return me;
	},

	removeChild : function( instance )
	{
		var me = this;

		// notify store
		if( me.notifyStore( 'beforeRemoveChild', arguments ) === false ) {
			return;
		}

		var slotName    = null,
		    instanceIdx = -1;

		var Array = Ext.Array,
		    slots = me.slots,
		    slotInstances;

		// search for child in every slot
		for( slotName in slots ) {
			if( slots.hasOwnProperty( slotName ) === false ) {
				continue;
			}

			slotInstances = slots[ slotName ];
			instanceIdx   = Array.indexOf( slotInstances, instance );

			// child found
			if( instanceIdx !== -1 ) {
				Array.erase( slotInstances, instanceIdx, 1 );
				break;
			}
		}

		// child not found in any slot
		if( instanceIdx === -1 ) {
			Ext.log({
				level : 'warn',
				msg   : '['+ me.$className +'][removeChild] The child was not found within this node.',
				dump  : me
			});
		}

		// remove parent link
		instance.parent = null;

		// notify store
		me.notifyStore( 'removeChild', arguments );

		return instance;
	},

	remove : function()
	{
		var me = this;

		// remove self from parent
		if( me.parent ) {
			me.parent.removeChild( me );
		}

		return me;
	},

	setTemplate : function( template )
	{
		var me = this;

		// notify store
		if( me.notifyStore( 'beforeTemplateChange', [ template ] ) === false ) {
			return;
		}


		var oldTemplate = me.template,
		    oldSlots    = me.slots || {};

		me.template = template;
		me.slots    = {};

		template.slots().each( function( slot ) {
			var name = slot.get('codeName');

			me.slots[ name ] = [];
		});

		Ext.Object.each( oldSlots, function( name, children ) {

			// transfer children between equally named slots
			if( me.slots[ name ] ) {
				me.slots[ name ] = children;

			// remove children of slots that name does not match
			// TODO is this the right way?
			// other options are
			//  - move to some (random) slot (only applicable when new template has some slots)
			//  - notify user and let him choose what to do (remove, assign somewhere else)
			//  - some advanced alg. to determine target slot
			} else {
				Ext.Array.forEach( children, function( instance ) {
					// remove parent link
					instance.parent = null;

					// notify store
					me.notifyStore( 'removeChild', [ instance ] );
				});
			}
		});

		// notify store
		me.notifyStore( 'configTemplate', [ oldTemplate, oldSlots ] );

		return this;
	},

	setConfig : function( config )
	{
		var me = this;

		// notify store
		if( me.notifyStore( 'beforeConfigChange', arguments ) === false ) {
			return;
		}

		var myConfig = me.config;
		for( var idx in config ) {
			if( config.hasOwnProperty( idx ) === false ) {
				continue;
			}

			if( myConfig.hasOwnProperty( idx ) === false ) {
				continue;
			}

			myConfig[ idx ] = config[ idx ];
		}

		// notify store
		me.notifyStore( 'configChange', arguments );

		return this;
	}
});