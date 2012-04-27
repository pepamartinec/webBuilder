Ext.define( 'DemoCMS.model.Image',
{
	extend : 'extAdmin.Model',

	idProperty : 'ID',

	fields : [{
		name : 'ID',
		type : 'int'
	},{
		name : 'title',
		type : 'string'
	},{
		name : 'filenameFull',
		type : 'string'
	},{
		name : 'filenameThumb',
		type : 'string'
	},{
		name : 'createdOn',
		type : 'datetime'
	}]
});