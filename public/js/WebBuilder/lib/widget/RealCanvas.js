Ext.define( 'WebBuilder.widget.RealCanvas', {
	extend : 'WebBuilder.widget.AbstractTemplateCanvas',

	initComponent : function()
	{
		var me = this;

		me.tplCache = {};

		me.initInstanceTpl();

		me.callParent();
	},

	initInstanceTpl : function()
	{
		var me = this,
		    tpl;

		// create the slot renderer
		tpl = Ext.create( 'Ext.Template',
			'<div id="template-block-instance-{id}-slot-{slotName}" class="{slotCls}">',
				'<div class="{titleCls} {slotTitleCls}">{slotName}</div>',
				'<tpl for="values.slots[\'{slotName}\']">',
					'{[ this.getInstanceTpl( values ).apply( values ) ]}',
				'</tpl>',
			'</div>'
		);

		me.slotRendererCode = tpl.apply({
		    blockCls      : me.blockCls,
		    slotCls       : me.slotCls,
		    titleCls      : me.titleCls,
		    blockTitleCls : me.blockTitleCls,
		    slotTitleCls  : me.slotTitleCls,
		    blockToolsCls : me.blockToolsCls,
		    blockToolCls  : me.blockToolCls,
		    configToolCls : me.configToolCls,
		    removeToolCls : me.removeToolCls,

		    id            : '{id}',
		    slotName      : '$1',
		    blockTitle    : '{block.data.title}',
		    templateTitle : '{template.data.title}'
		});


		// create the instance tpl
		tpl = Ext.create( 'Ext.Template',
			'<div id="template-block-instance-{id}" class="{blockCls}">',
				'<div class="{titleCls} {blockTitleCls}">',
					'<span>{blockTitle} [{templateTitle}]</span>',
					'<div class="{blockToolsCls}">',
						'<div class="{blockToolCls} {configToolCls}"></div>',
						'<div class="{blockToolCls} {removeToolCls}"></div>',
					'</div>',
				'</div>'

				// here comes the {content}

			// following is part of the blockFooter
			// '</div>'
		);

		me.blockHeader = tpl.apply({
		    blockCls      : me.blockCls,
		    titleCls      : me.titleCls,
		    blockTitleCls : me.blockTitleCls,
		    blockToolsCls : me.blockToolsCls,
		    blockToolCls  : me.blockToolCls,
		    configToolCls : me.configToolCls,
		    removeToolCls : me.removeToolCls,

		    id            : '{id}',
		    blockTitle    : '{block.data.title}',
		    templateTitle : '{template.data.title}'
		});

		me.blockFooter = '</div>';
	},

	/**
	 * Returns the template of given block instance
	 *
	 * @param {WebBuilder.BlockInstance} instance
	 * @return {Ext.Template}
	 */
	getInstanceTpl : function( instance )
	{
		var me       = this,
		    template = instance.template;

		if( me.tplCache[ template.getId() ] == null ) {
			var content = template.get('content');

			// remove non-designer parts of templates
			var designRe = /<!--\[if\s+design\]>([\s\S]*?)(?:<!\[else\]-->([\s\S]*?))?<!(?:--)?\[end\]-->/g;
			content = content.replace( designRe, '$1' );

			// replace slot nodes with some decodable garbage
			// this is needed, beacause Twig and XTemplates
			// has similiar syntax and we want to inject
			// some XTemplate code for slot rendering
			var twigSlotRe  = /{% slot ([\w-]+)[^%]+%}/gi,
			    localSlotRe = /#!&_slot_([\w-]+)_&!#/g;

			content = content.replace( twigSlotRe, '#!&_slot_$1_&!#' );

			// remove unwanted Twig stuff
			var macroRe = /{%\s*macro[\s\S]*?%}[\s\S]*?{%\s*endmacro\s*%}/g;
			content = content.replace( macroRe, '' );

			var controlRe = /{%\s*(.*?)\s*%}/g;
			content = content.replace( controlRe, '' );

			// replace Twig variables
			var htmlTagRe = /<[\s\S]+?>/g,
			    varRe     = /{{\s*([\w\.]+).*?\s*}}/g;
			content = content.replace( htmlTagRe, function( tag ) { return tag.replace( varRe, '' ); }); // variables in the HTML tags
			content = content.replace( varRe, ' <span class="x-block-variable">[$1]</span>' );           // variables in the text nodes

			// setup slot rendering
			content = content.replace( localSlotRe, me.slotRendererCode );

			var tpl = Ext.create( 'Ext.XTemplate',
					me.blockHeader,
						content,
					me.blockFooter,

					{
						disableFormats : true,
						compiled       : true,

						getInstanceTpl : Ext.Function.bind( me.getInstanceTpl, me )
					}
			);

			me.tplCache[ template.getId() ] = tpl;

		} else {
			tpl = me.tplCache[ template.getId() ];
		}

		return tpl;
	},

	/**
	 * Returns the template of given block instance
	 *
	 * @param {WebBuilder.BlockInstance} instance
	 * @return {Ext.Template}
	 */
	getDocumentTpl : function( instance )
	{
		return this.getInstanceTpl( instance );
	}
});