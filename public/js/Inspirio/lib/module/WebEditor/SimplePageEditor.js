Ext.define( 'Inspirio.module.WebEditor.SimplePageEditor',
{
	extend : 'Ext.tab.Panel',

	mixins : {
		editor : 'extAdmin.component.editor.DataEditorFeature'
	},

	requires : [
		'Ext.layout.container.Fit',
		'Inspirio.module.WebEditor.pageEditor.General',
		'Inspirio.module.WebEditor.pageEditor.Content',
		'Inspirio.module.WebEditor.pageEditor.Discussion',
		'Inspirio.module.WebEditor.pageEditor.Images',
		'Inspirio.module.WebEditor.pageEditor.Template'
	],

	width  : 800,
	height : 600,

	/**
	 * Editor initialization
	 *
	 */
	initComponent : function()
	{
		var me = this;

		me.generalTab   = Ext.create( 'Inspirio.module.WebEditor.pageEditor.General', {
			env    : me.env,
			editor : me
		});
		me.contentTab   = Ext.create( 'Inspirio.module.WebEditor.pageEditor.Content' );
		me.imagesTab    = Ext.create( 'Inspirio.module.WebEditor.pageEditor.Images', {
			env    : me.env,
			editor : me,
			border : false,
			title  : 'Obrázky'
		});
		me.dicussionTab = Ext.create( 'Inspirio.module.WebEditor.pageEditor.Discussion' );
		me.templateTab  = Ext.create( 'Inspirio.module.WebEditor.pageEditor.Template', {
			env : me.env
		});


		me.border = false;
		me.items  = [ me.generalTab, me.contentTab, me.imagesTab, me.dicussionTab, me.templateTab ];


		me.title   = 'Úprava stránky';
		me.iconCls = 'i-edit';
		me.layout  = 'fit';

		me.mixins.editor.constructor.call( me );

		me.callParent( arguments );
	},

	setData : function( data )
	{
		this.generalTab.setData( data );
		this.contentTab.setData( data );
		this.imagesTab.setData( data );
		this.dicussionTab.setData( data );
		this.templateTab.setData( data );

		return this;
	},

	getData : function()
	{
		var me   = this,
		    data = {};

		Ext.apply( data, me.generalTab.getData() );
		Ext.apply( data, me.contentTab.getData() );
		Ext.apply( data, me.imagesTab.getData() );
		Ext.apply( data, me.dicussionTab.getData() );
		Ext.apply( data, me.templateTab.getData() );

		return data;
	},

	getRecordId : function()
	{
		return this.getData()['ID'];
	},

	isDirty : function()
	{
		return false;
	}
});