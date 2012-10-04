<?php
namespace WebBuilder;

/**
 * Web block interface
 *
 * Interface that must be implemented by every WebBlock
 *
 * @author Josef Martinec <joker806@gmail.com>
 */
interface WebBlockInterface
{
    // TODO make this a WebBuilder settings
    const FORM_TOKEN_KEY = 'wbfk';

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

	/**
	 * Returns the block form token name.
	 *
	 * @return string
	 */
	public function getFormTokenKey();

	/**
	 * Returns the block form token value.
	 *
	 * @return string
	 */
	public function getFormTokenValue();

    /**
     * Proccesses the action data.
     */
	public function proccessFormData();

	/**
	 * Setups the data needed to render the block.
	 *
	 * @return array
	 */
	public function setupRenderData();
}