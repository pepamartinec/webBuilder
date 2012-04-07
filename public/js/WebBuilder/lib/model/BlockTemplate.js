Ext.define( 'WebBuilder.model.BlockTemplate',
{
	extend : 'extAdmin.Model',

	uses : [
		'WebBuilder.model.Block',
		'WebBuilder.model.BlockTemplateSlot'
	],

	fields : [{
		name : 'blockID',
		type : 'int'
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
		model      : 'WebBuilder.model.Block',
		getterName : 'getBlock',
		setterName : 'setBlock',
		primaryKey : 'ID',
		foreignKey : 'blockID'
	}],

	hasMany : [{
		model      : 'WebBuilder.model.BlockTemplateSlot',
		name       : 'slots',
		primaryKey : 'ID',
		foreignKey : 'templateID'
	}]
});