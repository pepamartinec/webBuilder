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

			items : [{
				xtype      : 'textfield',
				fieldLabel : 'Titulek',
				name       : 'title',
			},{
				xtype      : 'textfield',
				fieldLabel : 'URL',
				name       : 'urlName'
			}]
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
	}],

	getData : function() {
		return this.form.getValues();
	},

	setData : function( data )
	{
		return this.form.setValues( data );
	}
});