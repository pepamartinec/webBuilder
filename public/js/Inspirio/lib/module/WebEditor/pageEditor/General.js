Ext.define( 'Inspirio.module.WebEditor.pageEditor.General',
{
	extend : 'Ext.form.Panel',

	title : 'Obecné',

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

		var titleField = Ext.create( 'Ext.form.field.Text', {
			fieldLabel : 'Titulek',
			name       : 'title'
		});

		var urlField = Ext.create( 'extAdmin.widget.form.UrlName', {
			fieldLabel  : 'URL',
			name        : 'urlName',
			sourceField : titleField
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

					items : [ titleField, urlField ]

				},{
					xtype : 'tbspacer',
					width : 5

				},{
					xtype  : 'container',
					layout : {
						type  : 'vbox',
						align : 'stretch'
					},

					flex : 1,

					items : [{
						xtype      : 'fieldcontainer',
						fieldLabel : 'Platnost',

						items : [{
							xtype : 'component',
							html  : 'od'
						},{
							xtype : 'datefield',
							name  : 'validFrom'
						},{
							xtype : 'component',
							html  : 'do'
						},{
							xtype : 'datefield',
							name  : 'validTo'
						}]
					},{
						xtype      : 'checkboxfield',
						fieldLabel : 'Publikovat',
						name       : 'published'
					}]
				}]

			},{
				xtype      : 'htmleditor',
				fieldLabel : 'Perex',
				name       : 'perex',
				labelAlign : 'top',
				flex       : 1
			}]
		});

		me.callParent();
	},

	getData : function() {
		return this.form.getValues();
	},

	setData : function( data )
	{
		return this.form.setValues( data );
	}
});