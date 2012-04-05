Ext.define( 'WebBuilder.model.Block',
{
	extend : 'extAdmin.Model',
	
	uses : [
		'WebBuilder.model.BlocksCategory',
		'WebBuilder.model.BlockTemplate'
	],
	
	fields : [{
		name : 'title',
		type : 'string'
	},{
		name : 'thumb',
		type : 'string'
	},{
		name : 'codeName',
		type : 'string'
	}],
	
	belongsTo : [{
		model : 'WebBuilder.model.BlocksCategory',
		name  : 'category'
	}],
	
	hasMany : [{
		model : 'WebBuilder.model.BlockTemplate',
		name  : 'templates'
	}]
});