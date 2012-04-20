Ext.define( 'WebBuilder.widget.ConfigPopup', {
	extend : 'Ext.window.Window',

	uses : [
		'WebBuilder.ConstantData',
		'WebBuilder.InheritedData'
	],

	title : 'Nastavení bloku',

	okBtnTitle     : 'Použít',
	cancelBtnTitle : 'Storno',

	/**
	 * Component initialization
	 *
	 */
	initComponent : function()
	{
		var me = this;

		/**
		 * @protected
		 * @property {WebBuilder.BlockInstance/Null} currentInstance
		 */
		me.currentInstance = null;

		/**
		 * @protected
		 * @property {Ext.form.Field} templateField
		 */
		me.templateField = null;

		/**
		 * @protected
		 * @property {Array} configItems
		 */
		me.configItems = [];

		me.formPanel = Ext.create( 'Ext.form.Panel', {

		});

		Ext.apply( me, {
			layout      : 'anchor',
			bodyPadding : 5,

			buttons : [{
				text    : me.okBtnTitle,
				iconCls : 'i-ok',
				handler : me.applyConfig,
				scope   : me
			},{
				text    : me.cancelBtnTitle,
				iconCls : 'i-cancel',
				handler : me.close,
				scope   : me
			}]
		});

		me.callParent();
	},

	setInstance : function( instance )
	{
		var me     = this,
		    config = instance.getConfig();

		// instance already active
		// just update values
		if( instance === me.currentInstance ) {
			Ext.Array.forEach( me.configItems, function( field ) {
				field.setValue( config[ field.getName() ] );
			});

		// switch current instance
		} else {
			// remove old fields
			me.removeAll( true );
			me.configItems = [];

			// create template config field
			me.createTemplateConfigItem( instance );

			// create new fields
			Ext.Object.each( config, me.createConfigItem, me );

			me.add( me.templateField );
			me.add( me.configItems );
			me.currentInstance = instance;
		}
	},

	createTemplateConfigItem : function( instance )
	{
		var templates = instance.block.templates();

		this.templateField = Ext.create( 'Ext.form.field.ComboBox', {
			name       : 'template',
			fieldLabel : 'Šablona',
			value      : instance.template.getId(),
			readOnly   : templates.getCount() == 1,

			store        : instance.block.templates(),
			displayField : 'title',
			valueField   : 'ID',
			queryMode    : 'local'
		});
	},

	createConfigItem : function( name, value )
	{
		var me   = this,
		    item = null;

		// constant data
		if( value instanceof WebBuilder.ConstantData ) {
			item = Ext.create( 'Ext.form.field.Text', {
				name       : name,
				fieldLabel : name,
				value      : value.getValue()
			});

		// inherited data
		} else if( value instanceof WebBuilder.InheritedData ) {
			item = Ext.create( 'WebBuilder.widget.DataLinkField', {
				name       : name,
				fieldLabel : name,
				value      : value
			});

		// invalid data
		} else {
			Ext.log({
				level : 'warn',
				msg   : 'Invalid "'+ name +'" value "'+ value +'"'
			});

			return;
		}

		me.configItems.push( item );
	},

	applyConfig : function()
	{
		var me = this;

		// no current instance
		if( me.currentInstance == null ) {
			Ext.log({
				msg : '['+ me.$className +'][applyConfig] Cannot apply the config, no currentInstance is set.'
			});

		// apply config to the current instance
		} else {
			// set template
			me.currentInstance.setTemplate( me.currentInstance.block.templates().getById( me.templateField.getValue() ) );

			// apply config
			var config = {};

			Ext.Array.forEach( me.configItems, function( field ) {
				config[ field.getName() ] = field.getValue();
			});

			me.currentInstance.setConfig( config );
		}

		me.close();
	}
});