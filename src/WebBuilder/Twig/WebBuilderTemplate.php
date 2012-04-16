<?php
namespace WebBuilder\Twig;

use WebBuilder\BlockInstance;
use WebBuilder\BlocksBuilderInterface;

abstract class WebBuilderTemplate extends \Twig_Template
{
	/**
	 * @var BlocksBuilderInterface
	 */
	protected $builder;

	/**
	 * @var BlockInstance
	 */
	protected $block;

	public function setBuilder( BlocksBuilderInterface $builder )
	{
		$this->builder = $builder;
	}

	public function setBlock( BlockInstance $block )
	{
		$this->block = $block;
	}

	public function render( array $context )
	{
		if( $this->env->isDebug() ) {
			return
				'<div class="block">'.
					'<span class="name">'.
						$this->block.
						'<pre class="data">'.print_r( $this->block->data, true ).'</pre>'.
					'</span>'.
					parent::render( $context ).
				'</div>';

		} else {
			return parent::render( $context );
		}
	}
}