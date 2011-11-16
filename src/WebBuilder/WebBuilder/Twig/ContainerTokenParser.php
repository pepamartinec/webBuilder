<?php
namespace WebBuilder\WebBuilder\Twig;

class ContainerTokenParser extends \Twig_TokenParser
{
	public function parse( \Twig_Token $token )
	{
		$lineNo = $token->getLine();
		$slot   = $this->parser->getStream()->expect( \Twig_Token::NAME_TYPE )->getValue();

		$this->parser->getStream()->expect( \Twig_Token::BLOCK_END_TYPE );

		return null;
	}

	public function getTag()
	{
		return 'container';
	}
}