<?php
namespace DemoCMS\Administration;

use Inspirio\Database\cDatabase;
use ExtAdmin\ModuleFactoryInterface;

class cModuleFactory implements ModuleFactoryInterface
{
	/**
	 * @var cDatabase
	 */
	protected $database;

	/**
	 * @var \SimpleXMLElement
	 */
	protected $labels;

	/**
	 * Constructor
	 *
	 * @param cDatabase $database
	 * @param \SimpleXMLElement $labels
	 */
	public function __construct( cDatabase $database, \SimpleXMLElement $labels )
	{
		$this->database = $database;
		$this->labels   = $labels;
	}

	/**
	 * Returns list of available module names
	 *
	 * FIXME this should be dynamic
	 *
	 * @return array
	 */
	public function getModulesList()
	{
		return array(
			__NAMESPACE__.'\\WebEditor\\PageList',
			__NAMESPACE__.'\\WebEditor\\SimplePageEditor',
			__NAMESPACE__.'\\TextBlockManager\\TextBlockList',
			__NAMESPACE__.'\\TextBlockManager\\TextBlockEditor',
		);
	}

	/**
	 * Creates instance of requested module
	 *
	 * @param  string  $moduleName
	 * @return ExtAdmin\ModuleInterface
	 */
	public function factoryModule( $moduleName )
	{
		return new $moduleName( $this->database, $this->labels );
	}
}