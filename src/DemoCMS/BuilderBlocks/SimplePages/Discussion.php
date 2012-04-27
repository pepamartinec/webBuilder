<?php
namespace DemoCMS\BuilderBlocks\SimplePages;

use DemoCMS\cDiscussionPost;
use DemoCMS\cWebPage;
use Inspirio\Database\cDBFeederBase;
use WebBuilder\WebBlock;

class Discussion extends WebBlock
{
	const SESSION_MSG_KEY = 'discussionFormMessage';

	public static function requires()
	{
		return array(
			'webPage' => 'cWebPage'
		);
	}

	public static function provides()
	{
		return array(
			'posts'    => 'array[ cDiscussionPost ]',
			'formData' => 'array'
		);
	}

	public function processData( cWebPage $webPage, cDBFeederBase $postFeeder )
	{
		$message = null;
		if( isset( $_SESSION[ self::SESSION_MSG_KEY ] ) ) {
			$message = $_SESSION[ self::SESSION_MSG_KEY ];
			unset( $_SESSION[ self::SESSION_MSG_KEY ] );
		}

		if( ! isset( $_POST['sendDiscussionPost'] ) ) {
			return array(
				'successMessage' => $message
			);
		}

		$name    = strip_tags( $_POST['name'] );
		$email   = strip_tags( $_POST['email'] );
		$content = $_POST['content'];

		if( !$content ) {
			return array(
				'errorMessage' => 'Vyplňte prosím obsah zprávy.',
				'name'         => $name,
				'email'        => $email,
				'content'      => $content,
			);
		}

		$post = new cDiscussionPost( array(
			'webPageID'   => $webPage->getID(),
			'authorName'  => $name,
			'authorEmail' => $email,
			'content'     => $content,
			'createdOn'   => date( 'Y-m-d H:i:s' )
		) );

		try {
			$postFeeder->save( $post );

		} catch( \Exception $e ) {
			return array(
				'errorMessage' => $e->getMessage(), //'Nastal problém při ukládání příspěvku. Zkuste prosím opakovat později.',
				'name'         => $name,
				'email'        => $email,
				'content'      => $content,
			);
		}

		$_SESSION[ self::SESSION_MSG_KEY ] = 'Vaše zpráva byla přidána.';
		redirect( null );
	}

	public function setupData( cWebPage $webPage )
	{
		$postFeeder = new cDBFeederBase( '\\DemoCMS\\cDiscussionPost', $this->database );

		$formData = $this->processData( $webPage, $postFeeder );
		$posts    = $postFeeder->orderBy( 'created_on', 'desc' )->get();

		return array(
			'posts'    => $posts,
			'formData' => $formData,
		);
	}
}