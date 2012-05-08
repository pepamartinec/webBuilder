Ext.define( 'DemoCMS.module.WebEditor.pageEditor.General',
{
	extend : 'Ext.form.Panel',

	requires : [
		'Ext.form.field.Text',
		'extAdmin.widget.form.UrlName',
		'Ext.form.field.Checkbox',
		'DemoCMS.widget.TitleImage',
		'Ext.form.field.Hidden'
	],

	defaults : {
		anchor : '100%'
	},

	layout : {
		type  : 'vbox',
		align : 'stretch'
	},

	bodyPadding : 5,

	initComponent : function()
	{
		var me = this;

		me.titleField = Ext.create( 'Ext.form.field.Text', {
			fieldLabel : 'Titulek',
			name       : 'title'
		});

		me.urlField = Ext.create( 'extAdmin.widget.form.UrlName', {
			fieldLabel  : 'URL',
			name        : 'urlName',
			sourceField : me.titleField
		});

		me.publishField = Ext.create( 'Ext.form.field.Checkbox', {
			fieldLabel : 'Publikovat',
			name       : 'published',
		});

		me.titleImageField = Ext.create( 'DemoCMS.widget.TitleImage', {
			fieldLabel : 'Titulní obrázek',
			labelAlign : 'top',
			name       : 'titleImageID',
			env    : me.env,
			editor : me.editor,

			flex : 1
		});

		Ext.apply( me, {
			items : [{
				xtype : 'hiddenfield',
				name  : 'ID'

			},{
				xtype : 'hiddenfield',
				name  : 'parentID'

			},{
				xtype : 'container',
				layout : {
					type  : 'hbox',
					align : 'stretchMax'
				},

				items : [{
					xtype  : 'container',
					layout : {
						type  : 'vbox',
						align : 'stretch'
					},

					flex : 1,

					items : [ me.titleField, me.urlField, me.publishField ]

				},{
					xtype : 'tbspacer',
					width : 5

				}, me.titleImageField ]

			},{

				xtype      : 'htmleditor',
				fieldLabel : 'Perex',
				name       : 'perex',
				labelAlign : 'top',
				flex       : 1,
				enableSourceEdit : false
			}]
		});

		me.callParent();
	},

	getData : function() {
		return this.form.getValues();
	},

	setData : function( data )
	{
		var me = this;

		me.titleImageField.parentId = data.ID;

		if( ! data.parentID ) {
			me.urlField.setReadOnly( true );
			me.urlField.setValue( '/' );
		}

		return me.form.setValues( data );
	}
});