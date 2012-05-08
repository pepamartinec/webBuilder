Ext.define( 'DemoCMS.widget.TitleImage',
{
	extend : 'Ext.form.field.Base',

	uses : [
		'Ext.DomHelper',
		'Ext.button.Button',
		'DemoCMS.component.ImageSelectorPopup'
	],

	texts : {
		chooseBtn : 'Vybrat',
		removeBtn : 'Zrušit',
		onError   : 'Vybraný obrázek se nepodařilo nahrát'
	},

	/**
	 * @required
	 * @cfg {extAdmin.Environment} env,
	 */
	env : null,

	/**
	 * @required
	 * @cfg {extAdmin.component.editor.EditorFeature} editor
	 */
	editor : null,

	/**
	 * @cfg {String} module
	 */
	module : '\\DemoCMS\\Administration\\WebEditor\\ImageList',

	/**
	 * @cfg {Mixed} loadAction
	 */
	loadAction : 'serveImage',

	/**
	 * ParentID filter used in the image selector popup
	 *
	 * @required
	 * @cfg {Number}
	 */
	parentId : null,

	/**
	 * Field template
	 *
	 * @protected
	 * @param {Array} fieldSubTpl
	 */
	fieldSubTpl : [
		'<div id="{id}" class="{fieldCls} x-form-field-image"></div>',
		{
			compiled : true,
			disableFormats : true
		}
	],

	/**
	 * Component initialization
	 *
	 */
	initComponent : function()
	{
		var me = this;

		me.module = me.env.getModule( me.module );

		me.callParent();
	},

	/**
	 * Render callback
	 *
	 * @protected
	 */
	onRender : function()
	{
		var me = this;

		me.callParent( arguments );

		// create image element
		me.imgEl = Ext.DomHelper.append( me.inputEl, {
			tag : 'img',
			src : Ext.BLANK_IMAGE_URL
		}, true );

		me.imgEl.on( 'error', function() {
			if( me.getRawValue() != null ) {
				me.markInvalid( me.texts.onError );
			}

			me.imgEl.hide();

			if( me.ownerCt ) {
				me.ownerCt.doLayout();
			}
		} );

		me.imgEl.on( 'load', function() {
			me.clearInvalid();
			me.imgEl.show();

			if( me.ownerCt ) {
				me.ownerCt.doLayout();
			}
		} );

		me.imgEl.on( 'click', function() {
			me.popImageSelector();
		} );

		// create buttons
		me.removeBtn = null;
		if( me.required != true ) {
			me.removeBtn = Ext.create( 'Ext.button.Button', {
				text     : me.texts.removeBtn,
				cls      : Ext.baseCSSPrefix + 'remove-btn',
				renderTo : me.inputEl,

				scope   : me,
				handler : function() {
					me.setRawValue( null );
				}
			});
		}

		me.chooseBtn = Ext.create( 'Ext.button.Button', {
			text     : me.texts.chooseBtn,
			cls      : Ext.baseCSSPrefix + 'choose-btn',
			renderTo : me.inputEl,

			scope   : me,
			handler : me.popImageSelector
		});

		// init image
		me.setRawValue( me.getRawValue() );
	},

	/**
	 * Raw value setter
	 *
	 * @param {Mixed} value
	 */
	setRawValue : function( value )
	{
		var me = this;
		    value = Ext.value( value, null );

		me.rawValue = value;

		if( me.imgEl ) {
			if( value ) {
				me.imgEl.set({
					src : this.env.getActionUrl( me.module, me.loadAction, {
						imageID : value,
						variant : 'thumb'
					})
				});

			} else {
				me.imgEl.set({
					src : Ext.BLANK_IMAGE_URL
				});
			}
		}

		me.checkChange();

		return value;
	},

	/**
	 * Raw value getter
	 *
	 * @return {Mixed}
	 */
	getRawValue : function()
	{
		return this.rawValue;
	},

	/**
	 * Pops image selector
	 *
	 */
	popImageSelector : function()
	{
		var me     = this,
		    editor = me.editor;

		if( editor.isDirty() || editor.isPersisted() == false ) {
			editor.saveData({
				scope   : me,
				success : me.popImageSelecter_onSubmit
			});

		} else {
			me.popImageSelecter_onSubmit();
		}
	},

	/**
	 * Pops image selector, second phase
	 *
	 * @private
	 */
	popImageSelecter_onSubmit : function()
	{
		var me = this;

		if( me.popup == null ) {
			me.popup = Ext.create( 'DemoCMS.component.ImageSelectorPopup', {
				env         : me.env,
				editor      : me.editor,
				closeAction : 'hide',

				handler : function( images ) {
					me.setRawValue( images[0].getId() );
				}
			});
		}

		var selector = null;

		if( me.rawValue ) {
			selector = function( record ) {
				return record.getId() == me.rawValue;
			};
		}

		me.popup.show( me.parentId, selector );
	}
});