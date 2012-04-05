Ext.define( 'WebBuilder.BlockInstance', {

	statics : {
		idCounter : 0,

		genId : function()
		{
			return ( ++this.idCounter ).toString();
		}
	},

	id        : null,
	store     : null,
	block     : null,
	config    : null,
	template  : null,
	slots     : null,
	parent    : null,

	/**
	 * Constructor
	 *
	 * @param {WebBuilder.model.Block} [block]
	 */
	constructor : function( block )
	{
		var me = this;

		me.id     = me.self.genId();
		me.block  = block;
		me.config = {};
	}
});