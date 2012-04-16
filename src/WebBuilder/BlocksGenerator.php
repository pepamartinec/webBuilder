<?php
namespace WebBuilder;

use WebBuilder\Util\CodeAnalysis\TwigAnalysis\TemplateReflection;

use WebBuilder\Util\CodeAnalysis\PhpAnalysis\ReflectionFactory as PhpReflector;
use WebBuilder\Util\CodeAnalysis\PhpAnalysis\Reflections\NamespaceReflectionInterface;
use WebBuilder\Util\CodeAnalysis\PhpAnalysis\Reflections\ClassReflectionInterface;
use WebBuilder\Util\CodeAnalysis\TwigAnalysis\ReflectionFactory as TwigReflector;
use WebBuilder\Util\CodeAnalysis\TwigAnalysis\ReflectionTemplate;

class BlocksGenerator
{
	const TABLE_BLOCKS       = 'blocks';
	const TABLE_TEMPLATES    = 'blocks_templates';
	const TABLE_SLOTS        = 'blocks_templates_slots';
	const TABLE_REQUIREMENTS = 'blocks_data_requirements';

	/**
	 * @var \PDO
	 */
	protected $database;

	/**
	 * Constructor
	 *
	 * @param \PDO $database
	 */
	public function __construct( \PDO $dbh )
	{
		$this->database = $dbh;
		$this->blocks   = array();
	}

	/**
	 * Registers block into system
	 *
	 * @param ReflectionClassInterface $block
	 *
	 * @throws \DatabaseException
	 */
	public function registerBlock( ClassReflectionInterface $block, array $existingBlocks, array &$validBlocks )
	{
		$blockClassName = $block->getFullName();

		echo "- registering block {$blockClassName}\n";

		// if block with given name exists, reuse ID
		if( isset( $existingBlocks[ $blockClassName ] ) ) {
			$blockID = $existingBlocks[ $blockClassName ];

		// otherwise insert new into DB
		} else {
			$this->database->exec( 'INSERT INTO '.self::TABLE_BLOCKS.' ( code_name ) VALUES ( '.$this->database->quote( $blockClassName ).' )' );
			$blockID = $this->database->lastInsertId();
		}

		$validBlocks[] = $blockID;

		// DATA DEPENDENCIES
		$validDependencies = array();

		$sql = 'SELECT ID, property FROM '.self::TABLE_REQUIREMENTS.' WHERE block_ID = '.$blockID;
		foreach( $this->database->query( $sql ) as $r ) {
			$existingDependencies[ $r['property'] ] = $r['ID'];
		}

		$dependencies = $blockClassName::requires();
		if( $dependencies !== null ) {
			foreach( $dependencies as $property => $dataType ) {

				// if dependency with given name exists, reuse ID
				if( isset( $existingDependencies[ $property ] ) ) {
					$dependencyID = $existingDependencies[ $property ];
					$this->database->exec( 'UPDATE '.self::TABLE_REQUIREMENTS.' SET data_type = '.$this->database->quote( $dataType )." WHERE ID = {$dependencyID}" );

				// otherwise insert new into DB
				} else {
					$this->database->exec( 'INSERT INTO '.self::TABLE_REQUIREMENTS." ( block_ID, property, data_type ) VALUES ( {$blockID}, ".$this->database->quote( $property ).', '.$this->database->quote( $dataType ).' )' );
					$dependencyID = $this->database->lastInsertId();
				}

				$validDependencies[] = $dependencyID;
			}
		}

		// remove unknown dependencies within block
		if( sizeof( $validDependencies ) == 0 ) {
			$sql = 'DELETE FROM '.self::TABLE_REQUIREMENTS." WHERE block_ID = {$blockID}";
		} else {
			$sql = 'DELETE FROM '.self::TABLE_REQUIREMENTS." WHERE block_ID = {$blockID} AND ID NOT IN (".implode( ',', $validDependencies ).');';
		}

		$this->database->exec( $sql );
	}

	/**
	 * Registers blocks to system
	 *
	 * @param array $dirs
	 * @param bool  $recursive
	 *
	 * @throws DatabaseException
	 */
	public function registerBlocks( array $dirs )
	{
		// load existing blocks
		$existingBlocks = array();

		$sql = 'SELECT ID, code_name FROM '.self::TABLE_BLOCKS;
		foreach( $this->database->query( $sql ) as $r ) {
			$existingBlocks[ $r['code_name'] ] = $r['ID'];
		}

		$validBlocks = array();

		// search for blocks
		$analyzer  = new PhpReflector();

		foreach( $dirs as $dir ) {
			$analyzer->analyzeDirectory( $dir );
		}

		foreach( $analyzer->getNamespacesIterator() as $namespace ) {
			/* @var $namespace NamespaceReflectionInterface */

			foreach( $namespace->getClassIterator() as $class ) {
				/* @var $class ClassReflectionInterface */

				if( $class->implementsInterface( '\WebBuilder\WebBlockInterface' ) && $class->isAbstract() === false ) {
					$this->registerBlock( $class, $existingBlocks, $validBlocks );
				}
			}
		}

		// remove unknown block
		if( sizeof( $validBlocks ) == 0 ) {
			$this->database->exec( 'DELETE FROM '.self::TABLE_BLOCKS );

		} else {
			$this->database->exec( 'DELETE FROM '.self::TABLE_BLOCKS.' WHERE ID NOT IN ('.implode( ',', $validBlocks ).');' );
		}
	}

	/**
	 * Registers block into system
	 *
	 * @param ReflectionClass $block
	 *
	 * @throws \DatabaseException
	 */
	public function registerTemplate( TemplateReflection $template, array $existingTemplates, array &$validTemplates )
	{
		echo "- registering template {$template->getPathname()} (block {$template->getParentBlock()})\n";

		$pathname = $template->getPathname();
		// TODO make path relative to web root
		// $pathname = substr( $pathname, sizeof($webRoot) );

		// load block
		$dbs   = $this->database->query( 'SELECT ID FROM '.self::TABLE_BLOCKS.' WHERE code_name = '.$this->database->quote( $template->getParentBlock() ) );
		$block = $dbs->fetch();

		if( $block == null ) {
			$this->database->commit();
			return;
		}

		// if template with given name exists, reuse ID
		if( isset( $existingTemplates[ $pathname ] ) ) {
			$templateID = $existingTemplates[ $pathname ];

		// otherwise insert new into DB
		} else {
			$this->database->exec( 'INSERT INTO '.self::TABLE_TEMPLATES." ( block_ID, filename ) VALUES ( {$block['ID']}, ".$this->database->quote( $pathname ).' )' );
			$templateID = $this->database->lastInsertId();
			$existingTemplates[ $pathname ] = $templateID;
		}

		$validTemplates[] = $templateID;

		// SLOTS
		$validSlots    = array();

		$existingSlots = array();
		$sql = 'SELECT ID, code_name FROM '.self::TABLE_SLOTS.' WHERE template_ID = '.$templateID;
		foreach( $this->database->query( $sql ) as $r ) {
			$existingSlots[ $r['code_name'] ] = $r['ID'];
		}

		// loop over associated slots
		$slots = $template->getSlots();
		foreach( $slots as $slotName ) {

			// if slot with given name exists, reuse ID
			if( isset( $existingSlots[ $slotName ] ) ) {
				$slotID = $existingSlots[ $slotName ];

			// otherwise insert new into DB
			} else {
				$this->database->exec( 'INSERT INTO '.self::TABLE_SLOTS." ( template_ID, code_name ) VALUES ( {$templateID}, ".$this->database->quote( $slotName ).' )' );
				$slotID = $this->database->lastInsertId();
			}

			$validSlots[] = $slotID;
		}

		// remove unknown slots within template
		if( sizeof( $validSlots ) == 0 ) {
			$this->database->exec( 'DELETE FROM '.self::TABLE_SLOTS." WHERE template_ID = {$templateID};" );

		} else {
			$this->database->exec( 'DELETE FROM '.self::TABLE_SLOTS." WHERE template_ID = {$templateID} AND ID NOT IN (".implode( ',', $validSlots ).');' );
		}
	}

	/**
	 * Registers templates
	 *
	 * @param array $dirs
	 * @param bool $recursive
	 *
	 * @throws \DatabaseException
	 */
	public function registerTemplates( array $dirs )
	{
		$existingTemplates = array();

		// load existing templates
		$sql = 'SELECT ID, block_ID, filename FROM '.self::TABLE_TEMPLATES;
		foreach( $this->database->query( $sql ) as $r ) {
			$existingTemplates[ $r['filename'] ] = $r['ID'];
		}

		$validTemplates = array();

		// search for blocks
		$analyzer = new TwigReflector();

		foreach( $dirs as $dir ) {
			$analyzer->analyzeDirectory( $dir );
		}

		foreach( $analyzer->getNamespacesIterator() as $template ) {
			/* $template TemplateReflection */
			$this->registerTemplate( $template, $existingTemplates, $validTemplates );
		}

		// remove unknown templates
		if( sizeof( $validTemplates ) == 0 ) {
			$sql = 'DELETE FROM '.self::TABLE_TEMPLATES;
		} else {
			$sql = 'DELETE FROM '.self::TABLE_TEMPLATES." WHERE ID NOT IN (".implode( ',', $validTemplates ).')';
		}

		$this->database->exec( $sql );
	}
}