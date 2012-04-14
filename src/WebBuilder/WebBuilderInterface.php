<?php
namespace WebBuilder;

interface WebBuilderInterface
{
	/**
	 * Renders page for given web structure item
	 *
	 * @param  WebPageInterface $webPage
	 * @return string
	 *
	 * @throws BlocksSetIntegrityException
	 */
	public function render( WebPageInterface $webPage );
}