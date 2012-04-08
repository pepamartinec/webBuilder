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
	module : '\\WebBuilder\\WebBuilder\\ExtAdmin\\TemplatesManager\\TemplateEditor',

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

		me.initData();

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
	},

	initData : function( cb, cbScope )
	{
		var me = this;

		me.instancesStore = Ext.create( 'WebBuilder.EditorStore' );

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

	onBlocksLoad : function( blocksStore, blocks )
	{
		var me = this;

		// fill some default content
		if( me.value == null ) {
			me.value = {
				blockID    : 4,
				templateID : 10
			};
		}
//
//		// convert value to block instance
//		if( me.value ) {
//
//		// no value, create empty root instance
//		} else {
//			var block    = blocksStore.getById( defaultBlockId ),
//			    template = block.templates().getById( defaultTemplateId ),
//			    root     = Ext.create( 'WebBuilder.BlockInstance', block, template );
//
//			me.instancesStore.setRoot( root );
//		}

		// init field mixin
		// this must be done after block load
		// so we have all data for instances creation
		me.initField();
	},

	/**
	 * Returns the current data value of the field.
	 *
	 * @return {Object} value The field value
	 */
	getValue : function()
	{
		var root = this.instancesStore.getRoot();

		return this.getValue_instance( root );
	},

	/**
	 * @ignore
	 */
	getValue_instance : function( instance )
	{
		return {
			blockID : instance.block.getId(),
			config  : Ext.clone( instance.config ),

			templateID : instance.template && instance.template.getId(),
			slots      : instance.slots    && Ext.Object.map( instance.slots, this.getValue_walkInstanceSlot, this )
		};
	},

	/**
	 * @ignore
	 */
	getValue_walkInstanceSlot : function( name, children )
	{
		return Ext.Array.map( children, this.getValue_instance, this );
	},

	/**
	 * Sets a data value into the field and runs the change detection and validation.
	 *
	 * @param {Object} value The value to set
	 * @return {WebBuilder.widget.TemplateCanvas} this
	 */
	setValue : function( value )
	{
		var me    = this,
		    store = me.instancesStore;

		// clear data storage
		store.clear();

		var root = me.setValue_createInstance( value );
		store.setRoot( root );

		me.checkChange();

		return me;
	},

	/**
	 * @ignore
	 */
	setValue_createInstance : function( value )
	{
		var me       = this,
		    block    = me.blocksStore.getById( value.blockID ),
		    template = value.templateID && block.templates().getById( value.templateID ),
		    config   = value.config;

		// create instance
		var instance = Ext.create( 'WebBuilder.BlockInstance', block, template );

		// create children
		if( value.slots ) {
			Ext.Object.each( value.slots, function( name, children ) {
				Ext.Array.forEach( children, function( child ) {
					var childInstance = me.setValue_createInstance( child );

					instance.addChild( childInstance );
				});
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