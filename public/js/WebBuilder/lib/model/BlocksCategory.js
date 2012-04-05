Ext.define( 'WebBuilder.model.BlocksCategory',
{
	extend : 'extAdmin.component.Model',
	
	uses : [
		'WebBuilder.model.Block'
	],
	
	fields : [{
		name : 'title',
		type : 'string'
	}],
	
	hasMany : [{
		model : 'WebBuilder.model.Block',
		name  : 'blocks'
	}]
});