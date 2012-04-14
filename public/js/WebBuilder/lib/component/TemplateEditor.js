Ext.define( 'WebBuilder.component.TemplateEditor',
{
	extend : 'Ext.container.Container',
	alias  : 'widget.templateeditor',

	mixins : {
		field : 'Ext.form.field.Field'
	},

	requires : [
		'Ext.layout.container.Border',
		'WebBuilder.widget.BlocksList',
		'WebBuilder.widget.TemplateCanvas',

		'WebBuilder.EditorStore',
		'extAdmin.Store',
		'WebBuilder.BlockInstance',
		'WebBuilder.model.Block'
	],

	uses : [
		'Ext.JSON',
		'extAdmin.patch.ExtObjectMap'
	],

	/**
	 * @required
	 * @cfg {extAdmin.Environment} env
	 */
	env : null,

	/**
	 * @cfg {extAdmin.Module/String} module
	 */
	module : '\\WebBuilder\\ExtAdmin\\TemplatesManager\\TemplateEditor',

	/**
	 * @cfg {Array/String} blocksLoadAction
	 */
	blocksLoadAction : null,

	/**
	 * Editor initialization
	 *
	 */
	initComponent : function()
	{
		var me = this;

		me.module = me.env.getModule( me.module );

		me.initStores();

		// init internal components
		var ddGroup = me.getId();

		me.list = Ext.create( 'WebBuilder.widget.BlocksList', {
			region      : 'east',
			split       : true,
			collapsible : true,
			width       : 150,

			module      : me.module,
			ddGroup     : ddGroup,
			blocksStore : me.blocksStore
		});

		me.canvas = Ext.create( 'WebBuilder.widget.TemplateCanvas', {
			region : 'center',
			title  : 'Plátno',

			module         : me.module,
			ddGroup        : ddGroup,
			instancesStore : me.instancesStore
		});

//		me.tree = Ext.create( 'WebBuilder.widget.TemplateTree', {
//			region : 'center',
//			border : false,
//			title  : 'Strom',
//
//			ddGroup : ddGroup,
//			store   : me.instancesStore
//		});

		Ext.apply( me, {
			layout : 'border',
			items  : [{
					xtype  : 'tabpanel',
					region : 'center',
					border : false,

					items : [ me.canvas ]
				},

				me.list ]
		});

		me.callParent( arguments );
		me.initField();
	},

	initStores : function( cb, cbScope )
	{
		var me = this;

		me.instancesStore = Ext.create( 'WebBuilder.EditorStore', {
			listeners : {
				scope  : me,
				change : me.handleInstancesStoreChange
			}
		});

		me.blocksStore = extAdmin.Store.create({
			env        : me.env,
			loadAction : [ me.module.name, 'loadBlocks' ],
			model      : 'WebBuilder.model.Block',

			remoteSort   : false,
			remoteFilter : false,
			autoLoad     : true,

			listeners : {
				scope : me,
				load  : me.onBlocksLoad
			}
		});
	},

	/**
	 * Blocks store data load handler
	 *
	 * Initializes the real field value. This must be done after the blocks store load,
	 * so we have all the data for instances creation.
	 *
	 * @param {Ext.data.Store} [blocksStore]
	 * @param {WebBuilder.model.Block[]} [blocks]
	 */
	onBlocksLoad : function( blocksStore, blocks )
	{
		var me    = this,
		    value = me.getValue();

		// fill some default content
		if( value == null ) {
			value = {
				blockID    : 4,
				templateID : 10
			};
		}

		// init real field value
		// this must be done after block load
		// so we have all data for instances creation
		me.setValue( value );
	},

	/**
	 * Refreshes the field value with current instances store content
	 *
	 * @protected
	 */
	handleInstancesStoreChange : function()
	{
		var me    = this,
		    root  = me.instancesStore.getRoot(),
		    value = me.handleInstancesStoreChange_instance( root );

		me.mixins.field.setValue.call( me, value );
	},

	/**
	 * @ignore
	 * @private
	 */
	handleInstancesStoreChange_instance : function( instance )
	{
		if( instance == null ) {
			return null;
		}

		return {
			ID      : instance.getPersistentId(),
			blockID : instance.block.getId(),
			data    : Ext.clone( instance.config ),

			templateID : instance.template && instance.template.getId(),
			slots      : instance.slots    && Ext.Object.map( instance.slots, this.handleInstancesStoreChange_walkInstanceSlot, this )
		};
	},

	/**
	 * @ignore
	 * @private
	 */
	handleInstancesStoreChange_walkInstanceSlot : function( name, children )
	{
		return Ext.Array.map( children, this.handleInstancesStoreChange_instance, this );
	},

	/**
	 * Sets a data value into the field and runs the change detection and validation.
	 *
	 * @param {Object} value The value to set
	 * @return {WebBuilder.widget.TemplateCanvas} this
	 */
	setValue : function( value )
	{
		var me = this;

		// blocks store is not inited yet
		// just the value as the inner field value
		if( me.blocksStore.isLoading() === true ) {
			return me.mixins.field.setValue.apply( me, arguments );
		}

		// route the value to the instances store
		// it updates its internal values and
		// thru the 'change' event changes the local
		// inner field value
		var root = me.setValue_createInstance( value );
		me.instancesStore.setRoot( root );

		return this;
	},

	/**
	 * @ignore
	 * @private
	 */
	setValue_createInstance : function( value )
	{
		if( value == null ) {
			return null;
		}

		var me    = this,
		    block = me.blocksStore.getById( value.blockID );

		if( block == null ) {
			return null;
		}

		// create instance
		var instance = Ext.create( 'WebBuilder.BlockInstance', value.ID, block ),
		    template = value.templateID && block.templates().getById( value.templateID );
		
		instance.setConfig( value.data || {} );
		instance.setTemplate( template );

		// create children
		if( value.slots ) {
			Ext.Object.each( value.slots, function( id, children ) {
				if( Ext.isIterable( children ) ) {
					Ext.Array.each( children, function( child, position ) {
						var childInstance = me.setValue_createInstance( child );

						instance.addChild( childInstance, id );
					});
					
				} else {
					Ext.Object.each( children, function( position, child ) {
						var childInstance = me.setValue_createInstance( child );

						instance.addChild( childInstance, id );
					});
				}
				

			});
		}

		return instance;
	},

	/**
	 * Returns whether two field {@link #getValue values} are logically equal.
	 *
	 * @param {Object} value1 The first value to compare
	 * @param {Object} value2 The second value to compare
	 * @return {Boolean} True if the values are equal, false if inequal.
	 */
	isEqual: function( value1, value2 )
	{
		return Ext.JSON.encode( value1 ) === Ext.JSON.encode( value2 );
	},

	/**
	 * Returns the parameter(s) that would be included in a standard form submit for this field.
	 *
	 * @return {Object} A mapping of submit parameter names to values. It can also return null
	 * if there are no parameters to be submitted.
	 */
	getSubmitData: function()
	{
		// <debug>
			Ext.Error.raise({
				msg : '[WebBuilder.component.TemplateEditor][getSubmitData] has no default implementation, '+
				      'because it returns structured data. Please create your own implementation or use getModelData instead',
				templateEditor : this
			});
		// </debug>

		return null;
	},
});