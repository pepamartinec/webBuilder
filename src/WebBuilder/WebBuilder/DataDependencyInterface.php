<?php
namespace WebBuilder\WebBuilder;

interface DataDependencyInterface
{
	/**
	 * Returns target block property name
	 *
	 * @return string
	 */
	public function getProperty();

	/**
	* Returns data provider
	*
	* @return \WebBuilder\WebBuilder\BlockInstance|null
	*/
	public function getProvider();

	/**
	 * Returns dependency target data
	 *
	 * @return mixed
	 *
	 * @throws DataIntegrityException
	 */
	public function getTargetData();
}