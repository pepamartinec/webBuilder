<?php
namespace WebBuilder\BlocksLoaders;

use Inspirio\Database\cDBFeederBase;

use WebBuilder\DataDependencies\UndefinedData;
use WebBuilder\DataDependencies\InheritedData;
use WebBuilder\DataDependencies\ConstantData;
use WebBuilder\BlockInstance;
use WebBuilder\DataObjects\BlocksSet;
use WebBuilder\BlocksLoaderInterface;

class DatabaseLoader implements BlocksLoaderInterface
{
	/**
	 * @var \Inspirio\Database\cDatabase
	 */
	protected $database;

	/**
	 * @var \Inspirio\Database\cDBFeederBase
	 */
	protected $blocksSetsFeeder;

	/**
	 * Constructs
	 *
	 * @param \Database $blocksSetsFeeder
	 */
	public function __construct( cDBFeederBase $blocksSetsFeeder )
	{
		$this->database         = $blocksSetsFeeder->database();
		$this->blocksSetsFeeder = $blocksSetsFeeder;
	}

	/**
	 * Loads and initializes blocks instances used in given BlocksSet
	 *
	 * @param  BlocksSet $blocksSet blocks set
	 * @return array                 array( BlockInstance )
	 *
	 * @throws InvalidBlocksSetException
	 * @throws \DatabaseException
	 */
	public function fetchBlocksInstances( BlocksSet $blocksSet )
	{
		// merge BlocksSet with parent
		if( $blocksSet->getParentID() ) {
			$parentSet = $this->blocksSetsFeeder->whereID( $blocksSet->getParentID() )->getOne();

			if( $parentSet === null ) {
				throw new InvalidBlocksSetException("BlocksSet '{$blocksSet->getID()}' has invalid parent '{$blocksSet->getParentID()}'");
			}

			$instances = $this->fetchBlocksInstances( $parentSet );

		} else {
			$instances = array();
		}

		$this->database->query("
			SELECT
				instances.ID       instance_ID,
				blocks.ID          block_ID,
				blocks.code_name   block_name,
				templates.ID       template_ID,
				templates.filename template_filename,

				blocks_to_blocks.parent_instance_ID parent_instance_ID,
				parent_templates.block_ID           parent_block_ID,
				blocks_to_blocks.parent_slot_ID     parent_slot_ID,
				parent_blocks_slots.code_name       parent_slot_name,
				blocks_to_blocks.position           parent_slot_position

			-- CURRENT BLOCK --
			FROM       blocks_instances instances
			INNER JOIN blocks_templates templates ON ( templates.ID = instances.template_ID )
			INNER JOIN blocks                     ON ( blocks.ID = templates.block_ID )

			-- PARENT BLOCK --
			LEFT JOIN blocks_instances_subblocks blocks_to_blocks    ON ( blocks_to_blocks.inserted_instance_ID = instances.ID )
			LEFT JOIN blocks_templates_slots     parent_blocks_slots ON ( parent_blocks_slots.ID = blocks_to_blocks.parent_slot_ID )
			LEFT JOIN blocks_templates           parent_templates    ON ( parent_templates.ID = parent_blocks_slots.template_ID )

			WHERE instances.blocks_set_ID = {$blocksSet->getID()}
			ORDER BY parent_instance_ID ASC, position ASC
		");

		$result = $this->database->fetchArray();

		foreach( $result as $r ) {
			$instanceID = (int)$r['instance_ID'];
			$parentID   = (int)$r['parent_instance_ID'];

			// touch block instance
			if( isset( $instances[ $instanceID ] ) === false ) {
				$instances[ $instanceID ] = new BlockInstance( $instanceID );
			}

			$block = $instances[ $instanceID ];

			$block->blockID      = (int)$r['block_ID'];
			$block->blockName    = $r['block_name'];
			$block->templateID   = (int)$r['template_ID'];
			$block->templateFile = $r['template_filename'];

			// block instance has parent block instance defined
			if( $parentID ) {
				// touch parent block instance
				if( isset( $instances[ $parentID ] ) === false ) {
					$instances[ $parentID ] = new BlockInstance( $parentID );
				}

				$parent = $instances[ $parentID ];
				$parent->addChild( $block, $r['parent_slot_name'], $r['parent_slot_position'] );

			} else {
				$block->parent = null;
			}

			$block->dataDependencies += $this->fetchInstanceDataConstant( $block->ID );
			$block->dataDependencies += $this->fetchInstanceDataInherited( $block, $instances );
			$block->dataDependencies += $this->fetchInstanceDataRequirements( $block );
		}

		return $instances;
	}

	private function fetchInstanceDataRequirements( BlockInstance $instance )
	{
		$properties = array();

		$this->database->query("
			SELECT
				requirements.ID,
				requirements.property

			FROM blocks_instances instances
			INNER JOIN blocks_templates templates ON ( instances.template_ID = templates.ID )
			INNER JOIN blocks_data_requirements requirements ON ( templates.block_ID = requirements.block_ID )

			WHERE instances.ID = {$instance->ID}
		");
		$result = $this->database->fetchArray();

		if( $result != null ) {
			foreach( $result as $item ) {
				$properties[ $item['property'] ] = new UndefinedData( $instance, (int)$item['ID'], $item['property'] );
			}
		}

		return $properties;
	}

	private function fetchInstanceDataInherited( BlockInstance $instance, array &$knownInstances )
	{
		$data = array();

		$this->database->query("
			SELECT
				properties.property         property,
				data.provider_instance_ID   source_ID,
				providers.provider_property source_property

			FROM blocks_instances_data_inherited data
			INNER JOIN blocks_data_requirements_providers providers ON ( data.provider_property_ID = providers.ID )
			INNER JOIN blocks_data_requirements properties ON ( providers.required_property_ID = properties.ID )

			WHERE data.instance_ID = {$instance->ID}
		");
		$result = $this->database->fetchArray();

		if( $result != null ) {
			foreach( $result as $item ) {
				if( isset( $knownInstances[ $item['source_ID'] ] ) === false ) {
					$knownInstances[ $item['source_ID'] ] = new BlockInstance( $item['source_ID'] );
				}

				$data[ $item['property'] ] = new InheritedData( $instance, $item['property'], $knownInstances[ $item['source_ID'] ], $item['source_property'] );
			}
		}

		return $data;
	}

	private function fetchInstanceDataConstant( $instanceID )
	{
		$data = array();

		$this->database->query("
			SELECT
				properties.property property,
				data.value          value

			FROM blocks_instances_data_constant data
			INNER JOIN blocks_data_requirements properties ON ( data.property_ID = properties.ID )

			WHERE instance_ID = {$instanceID}
		");
		$result = $this->database->fetchArray();

		if( $result != null ) {
			foreach( $result as $item ) {
				$data[ $item['property'] ] = new ConstantData( $item['property'], $item['value'] );
			}
		}

		return $data;
	}
}