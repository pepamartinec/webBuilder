<?php
namespace WebBuilder\ExtAdmin;

use Inspirio\Database\cDatabase;
use ExtAdmin\ModuleFactoryInterface;

class ModulesFactory implements ModuleFactoryInterface
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
	 * @return array
	 */
	public function getModulesList()
	{
		return array(
			__NAMESPACE__.'\\TemplatesManager\\TemplatesList',
			__NAMESPACE__.'\\TemplatesManager\\TemplateEditor',
			__NAMESPACE__.'\\TemplatesManager\\PagesList',
			__NAMESPACE__.'\\TemplatesManager\\PageEditor',
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