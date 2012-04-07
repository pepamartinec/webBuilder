Ext.define( 'WebBuilder.model.BlockTemplateSlot',
{
	extend : 'extAdmin.Model',

	uses : [
		'WebBuilder.model.BlockTemplate'
	],

	fields : [{
		name : 'templateID',
		type : 'int'
	},{
		name : 'codeName',
		type : 'string'
	}],

	belongsTo : [{
		model : 'WebBuilder.model.BlockTemplate',
		getterName : 'getTemplate',
		setterName : 'setTemplate',
		primaryKey : 'ID',
		foreignKey : 'templateID'
	}]
});