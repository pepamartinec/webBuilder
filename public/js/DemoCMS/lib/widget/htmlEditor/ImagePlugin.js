Ext.define( 'DemoCMS.widget.htmlEditor.ImagePlugin', {
	extend : 'Ext.AbstractPlugin',
	alias  : 'plugin.htmleditor.image',

	/**
	 * @required
	 * @cfg {extAdmin.component.editor.DataEditorFeature} editor
	 */
	editor : null,

	/**
	 * @required
	 * @cfg {Number} parentId
	 */
	parentId : null,

	init : function( htmlEditor )
	{
		var me = this;

		me.htmlEditor = htmlEditor;

		htmlEditor.createBtn( 'insertimage', false, Ext.Function.bind( me.showSelector, me ) );
	},

	/**
	 * Shows the image selector
	 *
	 */
	showSelector : function()
	{
		var me     = this,
		    editor = me.editor;

		if( editor.isDirty() || editor.isPersisted() == false ) {
			editor.saveData({
				scope   : me,
				success : me.showSelector_onSubmit
			});

		} else {
			me.showSelector_onSubmit();
		}
	},

	/**
	 * Shows the image selector, second phase
	 *
	 * @private
	 */
	showSelector_onSubmit : function()
	{
		var me = this;

		if( me.popup == null ) {
			me.popup = Ext.create( 'DemoCMS.component.ImageSelectorPopup', {
				env         : me.editor.env,
				closeAction : 'hide',

				handler : me.onSelection,
				scope   : me
			});
		}

		me.popup.show( me.parentId );
	},

	onSelection : function( images )
	{
		if( images.length == 0 ) {
			return;
		}

		var image = images[0];

		this.htmlEditor.relayCmd( 'inserthtml',
			'<a href="'+ image.get('filenameFull') +'" class="pageContentImage">' +
				'<img src="'+ image.get('filenameThumb') +'" title="'+ image.get('title') +'" alt="'+ image.get('title') +'" />' +
			'</a>'

		);
	}
});