Ext.define( 'WebBuilder.model.Block',
{
	extend : 'extAdmin.Model',
	
	fields : [{
		name : 'title',
		type : 'string'
	},{
		name : 'thumb',
		type : 'string'
	},{
		name : 'codeName',
		type : 'string',
		mapping : 'code_name'
	}]
});