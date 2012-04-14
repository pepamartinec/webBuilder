Ext.define( 'WebBuilder.widget.ConfigPopup', {
	extend : 'Ext.window.Window',
	
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
		console.log(config);
		// instance already active
		// just update values
		if( instance === me.currentInstance ) {
			Ext.Array.forEach( me.configItems, function( field ) {
				field.setValue( config[ field.getName() ] );
			});
			
		// switch current instance
		} else {
			// remove old fields
			me.removeAll();
			me.configItems = [];
			
			// create new fields
			Ext.Object.each( config, me.createConfigItem, me );
			me.add( me.configItems );
			
			me.currentInstance = instance;			
		}
	},
	
	createConfigItem : function( name, value )
	{
		var me   = this,
		    item = null;
		
		// inherited data
		if( Ext.isObject( value ) ) {
			item = Ext.create( 'WebBuilder.widget.DataLinkField', {
				name       : name,
				fieldLabel : name,
				value      : value
			});
			
		// constant data
		} else {
			item = Ext.create( 'Ext.form.field.Text', {
				name       : name,
				fieldLabel : name,
				value      : value
			});
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
			var config = {};
			
			Ext.Array.forEach( me.configItems, function( field ) {
				config[ field.getName() ] = field.getValue();
			});
			
			me.currentInstance.setConfig( config );
		}
		
		me.close();
	}
});