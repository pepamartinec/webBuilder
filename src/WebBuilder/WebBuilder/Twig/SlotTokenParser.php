<?php
namespace WebBuilder\WebBuilder\Twig;

class SlotTokenParser extends \Twig_TokenParser
{
	public function parse( \Twig_Token $token )
	{
        $name = $this->parser->getStream()->expect( \Twig_Token::NAME_TYPE )->getValue();

        $parameters = null;
        if ($this->parser->getStream()->test( \Twig_Token::NAME_TYPE, 'with')) {
            $this->parser->getStream()->next();

            $parameters = $this->parser->getExpressionParser()->parseExpression();
        }

        $this->parser->getStream()->expect( \Twig_Token::BLOCK_END_TYPE );

		return new tSlotNode( $name, $parameters, $token->getLine(), $this->getTag() );
	}

	public function getTag()
	{
		return 'slot';
	}
}