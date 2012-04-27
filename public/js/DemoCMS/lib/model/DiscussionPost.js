Ext.define( 'DemoCMS.model.DiscussionPost',
{
	extend : 'extAdmin.Model',

	idProperty : 'ID',

	fields : [{
		name : 'ID',
		type : 'int'
	},{
		name : 'authorName'
	},{
		name : 'authorEmail'
	},{
		name : 'content'
	},{
		name : 'createdOn',
		type : 'datetime'
	}]
});