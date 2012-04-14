Ext.define( 'WebBuilder.model.Block',
{
	extend : 'extAdmin.Model',

	uses : [
		'WebBuilder.model.BlocksCategory',
		'WebBuilder.model.BlockTemplate'
	],

	fields : [{
		name : 'categoryID',
		type : 'int'
	},{
		name : 'title',
		type : 'string'
	},{
		name : 'thumb',
		type : 'string'
	},{
		name : 'codeName',
		type : 'string'
	},{
		name : 'requires'
	},{
		name : 'provides'
	}],

	belongsTo : [{
		model      : 'WebBuilder.model.BlocksCategory',
		getterName : 'getCategory',
		setterName : 'setCategory',
		primaryKey : 'ID',
		foreignKey : 'categoryID'
	}],

	hasMany : [{
		model      : 'WebBuilder.model.BlockTemplate',
		name       : 'templates',
		primaryKey : 'ID',
		foreignKey : 'templateID'
	}]
});