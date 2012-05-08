Ext.define( 'DemoCMS.widget.HtmlEditor', {
	extend : 'Ext.form.field.HtmlEditor',

	/**
	 * @required
	 * @cfg {extAdmin.component.editor.DataEditorFeature}
	 */
	editor : null,

	enableSourceEdit : false,

	createBtn : function ( id, toggle, handler, tooltip )
	{
		var baseCSSPrefix = Ext.baseCSSPrefix,
		    tipsEnabled   = Ext.tip.QuickTipManager && Ext.tip.QuickTipManager.isEnabled();

	    this.toolbar.add({
	        itemId       : id,
	        cls          : baseCSSPrefix + 'btn-icon',
	        iconCls      : baseCSSPrefix + 'edit-'+id,
	        enableToggle : toggle !== false,
	        scope        : this,
	        handler      : handler || this.relayBtnCmd,
	        clickEvent   : 'mousedown',
	        tooltip      : tipsEnabled ? tooltip || undefined : undefined,
	        overflowText : tooltip ? tooltip.title : undefined,
	        tabIndex     : -1
	    });
	}
});