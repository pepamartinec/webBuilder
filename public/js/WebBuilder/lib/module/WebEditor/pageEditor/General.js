Ext.define( 'WebBuilder.module.WebEditor.pageEditor.General',
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
				name       : 'publish'
			}]
		}]
		
	},{
		xtype      : 'htmleditor',
		fieldLabel : 'Perex',
		labelAlign : 'top',
		flex       : 1
	}]
});