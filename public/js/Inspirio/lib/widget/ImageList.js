Ext.define( 'Inspirio.widget.ImageList',
{
	extend : 'Ext.view.View',

	requires : [
		'Inspirio.model.Image'
	],

	/**
	 * @cfg {extAdmin.Module} module
	 */
	module : null,

	componentCls : Ext.baseCSSPrefix +'web-page-images',

	itemTpl : [
		'<div class="x-controls">',
//			'<div class="x-tool x-edit"></div>',
			'<div class="x-tool x-remove"></div>',
		'</div>',
		'<img src="{filenameThumb}" />',
		'<div class="x-title">{title}</div>'
	],

	/**
	 * Component initialization
	 *
	 */
	initComponent : function()
	{
		var me = this;

		me.store = me.module.createStore({
			model       : 'Inspirio.model.Image',
			loadAction  : 'loadImages',
			writeAction : 'updateImages'
		});

		me.callParent();
	},

	/**
	 * Item click handler
	 *
	 * @param {Inspirio.model.Image} [record]
	 * @param {HTMLElement} [itemDom]
	 * @param {Number} [idx]
	 * @param {Event} [e]
	 */
	onItemClick : function( record, itemDom, idx, e )
	{
		var me = this;

		if( e.getTarget( '.x-remove' ) ) {
			Ext.MessageBox.show({
				title    : 'Smazat obrázek?',
				msg      : 'Opravdu chcete tento obrázek smazat?',
				buttons  : Ext.MessageBox.YESNO,
				icon     : Ext.MessageBox.QUESTION,
				closable : false,
				fn       : function( buttonId ) {
					if( buttonId !== 'yes' ) {
						return;
					}

					me.module.runAction( 'deleteImages', {
						data : {
							records : [ record.getData() ]
						},

						complete : function() {
							me.store.load();
						}
					});
				}
			});
		}
	}
});