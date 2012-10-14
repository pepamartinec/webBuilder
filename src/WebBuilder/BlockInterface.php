<?php
namespace WebBuilder;

/**
 * Web block interface
 *
 * Interface that must be implemented by every WebBlock.
 *
 * @author Josef Martinec <joker806@gmail.com>
 */
interface BlockInterface
{

// ================= RENDER DATA MAPPING =================

	/**
	 * Tells which data the block requires.
	 *
	 * @return array|null
	 */
	public static function requires();

	/**
	 * Tells which data the block provides.
	 *
	 * @return array|null
	 */
	public static function provides();

	/**
	 * Returns the block configuration options.
	 *
	 * @return array|null
	 */
	public static function config();

// ================= RENDERING =================

	/**
	 * Setups the data needed to render the block.
	 *
	 * @return array
	 */
	public function setupRenderData();

// ================= FORM DATA PROCCESSING =================

    /**
     * Proccesses the action data.
     *
     * @return mixed|null
     */
	public function proccessFormData();
}