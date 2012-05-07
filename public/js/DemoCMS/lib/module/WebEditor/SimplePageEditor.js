Ext.define( 'DemoCMS.module.WebEditor.SimplePageEditor',
{
	extend : 'Ext.tab.Panel',

	mixins : {
		editor : 'extAdmin.component.editor.DataEditorFeature'
	},

	requires : [
		'Ext.layout.container.Fit',
		'DemoCMS.module.WebEditor.pageEditor.General',
		'DemoCMS.module.WebEditor.pageEditor.Content',
		'DemoCMS.module.WebEditor.pageEditor.Discussion',
		'DemoCMS.module.WebEditor.pageEditor.Images',
		'DemoCMS.module.WebEditor.pageEditor.Template'
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

		me.generalTab   = Ext.create( 'DemoCMS.module.WebEditor.pageEditor.General', {
			env    : me.env,
			editor : me,
			border : false,
			title  : 'Obecné'
		});

		me.contentTab   = Ext.create( 'DemoCMS.module.WebEditor.pageEditor.Content', {
			env    : me.env,
			editor : me,
			border : false,
			title  : 'Obsah'
		});

		me.imagesTab    = Ext.create( 'DemoCMS.module.WebEditor.pageEditor.Images', {
			env    : me.env,
			editor : me,
			border : false,
			title  : 'Obrázky'
		});

		me.dicussionTab = Ext.create( 'DemoCMS.module.WebEditor.pageEditor.Discussion', {
			border : false,
			title  : 'Diskuze'
		});

		me.templateTab  = Ext.create( 'DemoCMS.module.WebEditor.pageEditor.Template', {
			env    : me.env,
			editor : me,
			border : false,
			title  : 'Šablona'
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