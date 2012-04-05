Ext.define( 'WebBuilder.model.BlockTemplateSlot',
{
	extend : 'extAdmin.Model',
	
	uses : [
		'WebBuilder.model.BlockTemplate'
	],
	
	fields : [{
		name : 'title',
		type : 'string'
	},{
		name : 'codeName',
		type : 'string'
	}],
	
	belongsTo : [{
		model : 'WebBuilder.model.BlockTemplate',
		name  : 'template'
	}]
});