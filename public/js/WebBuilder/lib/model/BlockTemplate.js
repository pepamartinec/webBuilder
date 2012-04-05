Ext.define( 'WebBuilder.model.BlockTemplate',
{
	extend : 'extAdmin.Model',
	
	uses : [
		'WebBuilder.model.Block',
		'WebBuilder.model.BlockTemplateSlot'
	],
	
	fields : [{
		name : 'title',
		type : 'string'
	},{
		name : 'thumb',
		type : 'string'
	},{
		name : 'filename',
		type : 'string'
	},{
		name : 'structure',
		type : 'string'
	}],
	
	belongsTo : [{
		model : 'WebBuilder.model.Block',
		name  : 'block'
	}],
	
	hasMany : [{
		model : 'WebBuilder.model.BlockTemplateSlot',
		name  : 'slots'
	}]
});