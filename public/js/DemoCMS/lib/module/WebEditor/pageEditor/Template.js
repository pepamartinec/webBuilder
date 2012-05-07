Ext.define( 'DemoCMS.module.WebEditor.pageEditor.Template',
{
	extend : 'Ext.panel.Panel',

	requires : [
		'Ext.layout.container.Fit',
		'WebBuilder.component.TemplateEditor',
		'WebBuilder.UndoPlugin',
		'DemoCMS.component.TemplateSelectorPopup'
	],

	/**
	 * @required
	 * @cfg {extAdmin.Environment} env
	 */
	env : null,

	/**
	 * @required
	 * @cfg {extAdmin.component.editor.DataEditorFeature} editor
	 */
	editor : null,

	/**
	 * Component initialization
	 *
	 */
	initComponent : function()
	{
		var me = this;

		me.blockSetIdField = Ext.create( 'Ext.form.field.Hidden' );
		me.parentIdField   = Ext.create( 'Ext.form.field.Hidden' );

		var undoPlugin = Ext.create( 'WebBuilder.UndoPlugin' );

		me.templateEditor = Ext.create( 'WebBuilder.component.TemplateEditor', {
			plugins : [ undoPlugin ],

			env   : me.env
		});

		me.undoBtn = Ext.create( 'Ext.button.Button', {
			text    : 'Zpět',
			iconCls : 'i-arrow-undo',

			handler : me.templateEditor.undo,
			scope   : me.templateEditor
		});

		me.redoBtn = Ext.create( 'Ext.button.Button', {
			text    : 'Vpřed',
			iconCls : 'i-arrow-redo',

			handler : me.templateEditor.redo,
			scope   : me.templateEditor
		});

		undoPlugin.on( 'historychange', me.onTemplateHistoryChange, me );

		Ext.apply( me, {
			layout : 'fit',

			items : [ me.templateEditor ],

			dockedItems : [{
				dock  : 'top',
				xtype : 'toolbar',

				items : [{
					xtype   : 'button',
					text    : 'Nová',
					iconCls : 'i-application-form-delete',

					handler : me.reset,
					scope   : me
				},{
					xtype : 'button',
					text  : 'Načíst předdefinovanou',
					iconCls : 'i-folder',

					handler : me.loadPredefined,
					scope   : me
				},{
					xtype : 'tbseparator'

				}, me.undoBtn, me.redoBtn, {

					xtype : 'tbseparator'
				},{
					xtype : 'button',
					text  : 'Náhled',
					iconCls : 'i-monitor',

					handler : me.showPreview,
					scope   : me
				}]
			}]
		});

		me.callParent();
	},

	loadPredefined : function()
	{
		var me = this;

		if( me.templateSelectorPopup == null ) {
			var loadAction = [ '\\WebBuilder\\Administration\\TemplateManager\\TemplateEditor', 'loadData_record' ];

			me.templateSelectorPopup = Ext.create( 'DemoCMS.component.TemplateSelectorPopup', {
				env         : me.env,
				closeAction : 'hide',

				handler : function( records ) {
					me.env.runAction( loadAction, {
						data : { ID : records[0].getId() },

						success : function( data ) {
							var template = data.data.template; // TODO why the double data?

						//	me.clearBlockInstanceID( template );

							me.templateEditor.setValue( template );
							me.parentIdField.setValue( data.data.ID );
						}
					});
				},

				socpe : me
			});
		}

		me.templateSelectorPopup.show();
	},

	clearBlockInstanceID : function( blockInstance )
	{
		blockInstance.ID = null;

		if( blockInstance.slots ) {
			Ext.Object.each( blockInstance.slots, function( name, children ) {
				Ext.Array.each( children, this.clearBlockInstanceID, this );
			}, this );
		}
	},

	reset : function()
	{
		var me = this;

		Ext.MessageBox.show({
			title    : 'Vymazat obsah šablony?',
			msg      : 'Opravdu chcete vymazat obsah této šablony?',
			buttons  : Ext.MessageBox.YESNO,
			icon     : Ext.MessageBox.QUESTION,
			closable : false,
			fn       : function( buttonId ) {
				if( buttonId !== 'yes' ) {
					return;
				}

				me.templateEditor.clear();
			}
		});
	},

	onTemplateHistoryChange : function()
	{
		var me  = this,
		    tpl = me.templateEditor;

		me.undoBtn.setDisabled( ! tpl.hasPrevState() );
		me.redoBtn.setDisabled( ! tpl.hasNextState() );
	},

	showPreview : function()
	{
		var me = this;

		if( ! me.previewWindow || me.previewWindow.closed ) {
			// TODO window.closed is not a part of any W3C specification
			// but is supported by all mayor browsers

			me.previewWindow = window.open( 'about:blank' );
		}

		var doc = me.previewWindow.document;

		Ext.DomHelper.overwrite( doc.body, {
			tag  : 'p',
			html : 'Probáhá zpracování náhledu..'
		});

		me.editor.module.runRawAction( 'preview', {
			data : me.editor.getData(),

			success : me.onPreviewLoad,
			scope   : me
		});
	},

	onPreviewLoad : function( content )
	{
		var me  = this,
		    doc = me.previewWindow.document;

		// show preview notification bar
		var bar = '<div style="position: fixed; background-image: url(data:image/gif;base64,R0lGODlhCgAKALMAAP8AAP8pKQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACwAAAAACgAKAAAEFTBICWqdwd6pAe6fFlpYRo4eWopoBAA7); color: white; font-weight: bolder; padding: 3px; opacity: 0.8; width: 100%; top:0; left: 0; text-align: center; z-index: 1000">Náhled stránky</div>';

		// write the preview content
		doc.open();
		doc.write( content );
		doc.write( bar );
		doc.close();

		// modify the title
		doc.title = '[Náhled] '+ doc.title;

		// prevent user from navigating away from the preview
		doc.onmousedown = me.previewEventsHandler;
		doc.onmouseup   = me.previewEventsHandler;
		doc.onclick     = me.previewEventsHandler;
		doc.onkeypress  = me.previewEventsHandler;
		doc.onkeyup     = me.previewEventsHandler;

		// refresh the preview on the F5 key
		doc.onkeydown = function( e ) { return me.previewKeypressHandler( e ); };
	},

	previewEventsHandler : function( e )
	{
		e.preventDefault();
		e.stopPropagation();
	},

	previewKeypressHandler : function( e )
	{
		var code = e.keyCode || e.charCode;

		// TODO let Ext handle the hardcoded F5 keyCode

		if( code == 116 ) {
			this.showPreview();
		}

		e.preventDefault();
		e.stopPropagation();
	},

	getData : function()
	{
		return {
			blockSetID       : this.blockSetIdField.getValue(),
			parentBlockSetID : this.parentIdField.getValue(),
			template         : this.templateEditor.getValue()
		};
	},

	setData : function( data )
	{
		this.blockSetIdField.setValue( data.blockSetID );
		this.parentIdField.setValue( data.parentBlockSetID );

		this.templateEditor.setBlockSetId( data.blockSetID );
		this.templateEditor.setValue( data.template );

		return this;
	}
});