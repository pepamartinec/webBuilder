<?php
namespace DemoCMS\Administration\WebEditor;

use ExtAdmin\Response\FileResponse;

use DemoCMS\cImageHandler;

use ExtAdmin\Response\DataBrowserResponse;

use ExtAdmin\Request\AbstractRequest;

use Inspirio\Database\cDBFeederBase;
use ExtAdmin\Response\ActionResponse;
use DemoCMS\cImage;
use Inspirio\Database\cDatabase;
use ExtAdmin\Response\DataStoreResponse;
use ExtAdmin\Request\DataRequestDecorator;
use ExtAdmin\RequestInterface;
use ExtAdmin\Module\ModuleBase;

class ImageList extends ModuleBase
{
	/**
	 * @var cDatabase
	 */
	protected $database;

	/**
	 * Module constructor
	 *
	 * @param cDatabase $database
	 * @param \SimpleXMLElement $labels
	 */
	public function __construct( cDatabase $database, \SimpleXMLElement $labels )
	{
		$this->database = $database;
	}

	/**
	 * Returns module actions definition
	 *
	 * Used for defining actions within concrete modules implementations
	 *
	 * @return array
	 */
	public function actions()
	{
		return array(
			'loadImages'   => true,
			'updateImages' => true,
			'deleteImages' => true,
			'uploadImages' => true,
			'serveImage'   => true,
		);
	}

	/**
	 * Module UI viewConfiguration
	 *
	 * @return array
	 */
	public function viewConfiguration()
	{
		return array();
	}

	/**
	 * Loads data for dataList
	 *
	 * @param  RequestInterface $request
	 * @return DataBrowserResponse
	 */
	public function loadImages( RequestInterface $request )
	{
		$request = new DataRequestDecorator( $request );
		$webPageID = $request->getFilter( 'webPageID', 'int' );

		if( $webPageID == null ) {
			return new DataStoreResponse( true, array() );
		}

		$imageFeeder = new cDBFeederBase( '\\DemoCMS\\cImage', $this->database );
		$images      = $imageFeeder->whereColumnEq( 'web_page_ID', $webPageID )->get();

		if( $images === null ) {
			return new DataStoreResponse( true, array() );
		}

		$extractor = function( cImage $image ) {
			return array(
				'ID'            => $image->getID(),
				'title'         => $image->getTitle(),
				'filenameFull'  => $image->getFilenameFull(),
				'filenameThumb' => $image->getFilenameThumb(),
			);
		};

		return new DataStoreResponse( true, $images, sizeof( $images ), $extractor );
	}

	/**
	 * Deletes the images
	 *
	 * @param RequestInterface $request
	 * @return ActionResponse
	 */
	public function deleteImages( RequestInterface $request )
	{
		$images = $request->getRawData( 'records' );

		if( ! is_array( $images ) ) {
			$response = new ActionResponse( false );
			$response->setMessage( "Neplatný požadavek" );
			return $response;
		}

		$imageIDs = array();
		foreach( $images as $image ) {
			$imageID = AbstractRequest::secureData( $image, 'ID', 'int' );

			if( $imageID ) {
				$imageIDs[] = $imageID;
			}
		}

		if( sizeof( $imageIDs ) > 0 ) {
			$imageFeeder = new cDBFeederBase( '\\DemoCMS\\cImage', $this->database );
			$images      = $imageFeeder->whereColumnIn( 'ID', $imageIDs )->get();

			foreach( $images as $image ) {
				@unlink( PATH_TO_ROOT . $image->getFilenameFull() );
				@unlink( PATH_TO_ROOT . $image->getFilenameThumb() );
			}

			$imageFeeder->whereColumnIn( 'ID', $imageIDs )->remove();
		}

		return new ActionResponse( true );
	}

	/**
	 * Handles images upload
	 *
	 * @param RequestInterface $request
	 * @return ActionResponse
	 */
	public function uploadImages( RequestInterface $request )
	{
		// FIXME find a way to use standard Request data instead of the $_POST
		$webPageID = AbstractRequest::secureData( $_POST, 'webPageID', 'int' );
		$images    = $this->getUploadedFiles( 'images' );
		$status    = array();

		foreach( $images as $i => $image ) {
			// not valid file
			if( ! is_uploaded_file( $image['tmp_name'] ) ) {
				$status[$i] = array(
					'status'  => false,
					'message' => "Soubor se nepodařilo nahrát"
				);
				continue;
			}

			try {
				$this->processUploadedImage( $webPageID, $image );

				$status[$i] = array(
					'status' => true
				);

			} catch( Exception $e ) {
				$status[$i] = array(
					'status'  => false,
					'message' => 'Při ukládání obrázku došlo k chybě'
				);
			}
		}

		$response = new ActionResponse( true );
		$response->setHeader( 'Content-Type', 'text/html; charset=utf-8' );
		$response->setData( $status );

		return $response;
	}

	/**
	 * Proccesses the upload of one image
	 *
	 * @param unknown_type $webPageID
	 * @param unknown_type $uploadedFile
	 *
	 * @throws \Exception
	 */
	protected function processUploadedImage( $webPageID, $uploadedFile )
	{
		$pathPrefix       = PATH_TO_ROOT;
		$repositoryPath   = 'public/repository/';
		$thumbDir         = $repositoryPath . 'thumbs/';
		$originalDir      = $repositoryPath . 'original/';
		$filename         = $this->cleanFilename( $uploadedFile['name'] );
		$thumbFilename    = $this->findFreeFilename( $pathPrefix . $thumbDir, $filename );
		$originalFilename = $this->findFreeFilename( $pathPrefix . $originalDir, $filename );

		// touch target dirs
		if( ! is_dir( $pathPrefix . $originalDir ) ) {
			mkdir( $pathPrefix . $originalDir, 0777, true );
		}

		if( ! is_dir( $pathPrefix . $thumbDir ) ) {
			mkdir( $pathPrefix . $thumbDir, 0777, true );
		}

		// save original
		$succ = move_uploaded_file( $uploadedFile['tmp_name'], $pathPrefix . $originalDir . $originalFilename );

		if( $succ == false ) {
			throw new \Exception( 'Image not saved' );
		}

		// create thumb
		try {
			$imagine   = new \Imagine\Gd\Imagine();
			$thumbSize = new \Imagine\Image\Box( 160, 160 );
			$mode      = \Imagine\Image\ImageInterface::THUMBNAIL_INSET;

			$imagine->open( $pathPrefix . $originalDir . $originalFilename )
			        ->thumbnail( $thumbSize, $mode )
			        ->save( $pathPrefix . $thumbDir . $thumbFilename );

		} catch( \Exception $e ) {
			unlink( $pathPrefix . $originalDir . $originalFilename );
			throw $e;
		}

		// save database record
		try {
			$image = new cImage( array(
				'webPageID'     => $webPageID,
				'title'         => $uploadedFile['name'],
				'filenameFull'  => $originalDir . $originalFilename,
				'filenameThumb' => $thumbDir . $thumbFilename,
			));

			$imageFeeder = new cDBFeederBase( '\\DemoCMS\\cImage', $this->database );
			$imageFeeder->save( $image );

		} catch( \Exception $e ) {
			unlink( $pathPrefix . $originalDir . $originalFilename );
			unlink( $pathPrefix . $thumbDir . $thumbFilename );

			throw $e;
		}
	}

	/**
	 * Removes unwanted characters from the filename
	 *
	 * @param string $filename
	 * @return string
	 */
	protected function cleanFilename( $filename )
	{
		return preg_replace( '/[^\w\.]/', '_', $filename );
	}

	/**
	 * Returns unique version of the filename
	 *
	 * @param string $dirPath
	 * @param string $filename
	 */
	protected function findFreeFilename( $dirPath, $filename )
	{
		if( ! file_exists( $dirPath . $filename ) ) {
			return $filename;
		}

		$pi      = pathinfo( $dirPath . $filename );
		$counter = 0;

		do {
			$filename = $pi['filename'] .'-'. ++$counter .'.'. $pi['extension'];

		} while( file_exists( $dirPath . $filename ) );

		return $filename;
	}

	/**
	 * Returns list of uploaded files
	 *
	 * @param string $filesIndex
	 * @return array
	 */
	protected function getUploadedFiles( $filesIndex )
	{
		if( ! isset( $_FILES[ $filesIndex ] ) ) {
			return array();
		}

		$files         = $_FILES[ $filesIndex ];
		$transformed   = array();
		$numberOfFiles = sizeof( $files['name'] );

		for( $i = 0; $i < $numberOfFiles; ++$i ) {
			// null file
			if( $files['name'][$i] == null ) {
				continue;
			}

			$file = array(
				'name'     => $files['name'][$i],
				'tmp_name' => $files['tmp_name'][$i],
				'type'     => $files['type'][$i],
				'size'     => $files['size'][$i],
				'error'    => $files['error'][$i]
			);

			$transformed[] = $file;
		}

		return $transformed;
	}

	/**
	 * Action - ServeImage
	 *
	 * @param array $parameters
	 */
	public function serveImage( RequestInterface $request )
	{
		$imageID = $request->getParameter( 'imageID', 'int' );
		$variant = $request->getParameter( 'variant', 'type' );

		if( $imageID == null || ! in_array( $variant, array( 'full', 'thumb' ) ) ) {
			return new FileResponse( null );
		}

		$imageHandler = new cImageHandler( $this->database );
		$imageFeeder  = $imageHandler->getImageFeeder();
		$image        = $imageFeeder->whereID( $imageID )->getOne();

		if( $image == null ) {
			return new FileResponse( null );
		}

		switch( $variant ) {
			case 'thumb': $property = 'filenameThumb'; break;
			default     : // no break
			case 'full' : $property = 'filenameFull'; break;
		}

		return new FileResponse( $image->get( $property ) );
	}
}