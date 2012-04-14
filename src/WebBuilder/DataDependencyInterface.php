<?php
namespace WebBuilder;

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
	* @return \WebBuilder\BlockInstance|null
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

	/**
	 * Exports internal data for client-side usage
	 *
	 * @return mixed
	 */
	public function export();
}