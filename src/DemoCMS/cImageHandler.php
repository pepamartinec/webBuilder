<?php
namespace DemoCMS;

use Inspirio\Database\cDatabase;
use Inspirio\Database\cDBFeederBase;

class cImageHandler
{
	/**
	 * @var cDatabase
	 */
	protected $database;

	/**
	 * @var cDBFeederBase
	 */
	protected $feeder;

	/**
	 * @var string
	 */
	protected $pathPrefix;

	/**
	 * @var string
	 */
	protected $dirOriginal;

	/**
	 * @var string
	 */
	protected $dirThumb;

	/**
	 * Handler constructor
	 *
	 * @param cDatabase $database
	 */
	public function __construct( cDatabase $database )
	{
		$this->database = $database;
		$this->feeder   = new cDBFeederBase( '\\DemoCMS\\cImage', $this->database );

		$this->pathPrefix  = PATH_TO_ROOT;
		$repositoryPath    = 'public/repository/';
		$this->dirOriginal = $repositoryPath . 'original/';
		$this->dirThumb    = $repositoryPath . 'thumb/';

	}

	/**
	 * Returns the image feeder
	 *
	 * @return cDBFeederBase
	 */
	public function getImageFeeder()
	{
		return $this->feeder;
	}

	/**
	 * Deletes images
	 *
	 * @param array $IDs
	 */
	public function deleteImages( array $images = null )
	{
		if( $images == null ) {
			return;
		}

		$IDs = array();
		foreach( $images as $image ) {
			$ID = $image->getID();

			if( $ID ) {
				$IDs[] = $ID;
			}
		}

		// delete images from the database
		$this->feeder->whereColumnIn( 'ID', $IDs )->remove();

		// delete images from the filesystem
		foreach( $images as $image ) {
			$filenameOriginal = $this->pathPrefix . $this->dirOriginal . $image->getFilenameFull();
			$filenameThumb    = $this->pathPrefix . $this->dirOriginal . $image->getFilenameThumb();

			if( is_file( $filenameOriginal ) ) {
				@unlink( $filenameOriginal );
			}

			if( is_file( $filenameThumb ) ) {
				@unlink( $filenameThumb );
			}
		}
	}

	/**
	 * Deletes images by their IDs
	 *
	 * @param array $IDs
	 */
	public function deleteImagesByIDs( array $IDs = null )
	{
		if( $IDs == null ) {
			return;
		}

		$images = $this->feeder->whereColumnIn( 'ID', $IDs )->get();

		$this->deleteImages( $images );
	}
}