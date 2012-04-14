<?php
namespace WebBuilder\Twig;

class SlotNode extends \Twig_Node
{
	public function __construct( $slot, $parameters, $lineNo, $tag )
	{
		parent::__construct( array( 'parameters' => $parameters ), array( 'name' => $slot ), $lineNo, $tag );
	}

	public function compile( \Twig_Compiler $compiler )
	{
        $compiler->addDebugInfo( $this );

		$compiler->write( "\$this->builder->renderSlot( \$this->block, '{$this->getAttribute('name')}', " );

		if( sizeof( $this->getNode('parameters') ) > 0 ) {
			$compiler->subcompile( $this->getNode('parameters') );
		} else {
			$compiler->raw( 'null' );
		}

        $compiler->raw( " );\n" );
	}
}