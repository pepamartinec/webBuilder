Ext.define( 'DemoCMS.widget.ImageList',
{
	extend : 'Ext.view.View',

	requires : [
		'DemoCMS.model.Image'
	],

	/**
	 * @required
	 * @cfg {extAdmin.Module} module
	 */
	module : null,

	/**
	 * @property {String} componentCls
	 */
	componentCls : Ext.baseCSSPrefix +'web-page-images',

	/**
	 * @property {Array} itemTpl
	 */
	itemTpl : [
		'<div class="x-controls">',
			'<div class="x-tool x-remove"></div>',
		'</div>',
		'<img src="{filenameThumb}" />',
		'<div class="x-title">{title}</div>'
	],

	autoScroll : true,

	/**
	 * Component initialization
	 *
	 */
	initComponent : function()
	{
		var me = this;

		me.store = me.module.createStore({
			model       : 'DemoCMS.model.Image',
			loadAction  : 'loadImages',
			writeAction : 'updateImages'
		});

		me.callParent();

		me.webPageId = null;
	},

	/**
	 * Reloads the data
	 *
	 * @param {Number} webPageId
	 * @param {Function} cb
	 * @param {Object} scope
	 * @return {DemoCMS.widget.ImageList}
	 */
	loadData : function( webPageId, cb, scope )
	{
		this.webPageId = webPageId;

		this.store.load({
			filters : [{
				property : 'webPageID',
				value    : webPageId
			}],

			callback : cb,
			scope    : scope
		});

		return this;
	},

	/**
	 * Item click handler
	 *
	 * @param {DemoCMS.model.Image} [record]
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
							me.loadData( me.webPageId );
						}
					});
				}
			});
		}
	}
});