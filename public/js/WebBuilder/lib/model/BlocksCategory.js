Ext.define( 'WebBuilder.model.BlocksCategory',
{
	extend : 'extAdmin.Model',

	uses : [
		'WebBuilder.model.Block'
	],

	idProperty : 'ID',

	fields : [{
		name : 'title',
		type : 'string'
	}],

	hasMany : [{
		model      : 'WebBuilder.model.Block',
		name       : 'blocks',
		primaryKey : 'ID',
		foreignKey : 'categoryID'
	}]
});