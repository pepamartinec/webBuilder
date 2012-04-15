<?php
namespace WebBuilder;

interface WebPageInterface
{
	/**
	 * Returns the ID of assigned blockSet
	 *
	 * @return int
	 */
	public function getBlockSetID();

	/**
	 * Returns the title of the page
	 *
	 * @return string
	 */
	public function getTitle();
}