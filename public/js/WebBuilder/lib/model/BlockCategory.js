Ext.define( 'WebBuilder.model.BlockCategory',
{
	extend : 'Ext.data.Model',
	
	requires : [
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