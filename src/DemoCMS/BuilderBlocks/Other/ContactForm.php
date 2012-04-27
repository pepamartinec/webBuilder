<?php
namespace DemoCMS\BuilderBlocks\Other;

use DemoCMS\cWebPage;
use WebBuilder\WebBlock;

class ContactForm extends WebBlock
{
	const EMAIL_TO      = 'joker806@localhost';
	const EMAIL_FROM    = 'noreply@webbuilder.test';
	const EMAIL_SUBJECT = 'Dotaz z webu';

	const SESSION_MSG_KEY = 'contactFormMessage';

	public static function requires()
	{
		return array(
			'webPage' => 'cWebPage'
		);
	}

	public static function provides()
	{
		return array(
			'formData' => 'array'
		);
	}

	public function processData()
	{
		$message = null;
		if( isset( $_SESSION[ self::SESSION_MSG_KEY ] ) ) {
			$message = $_SESSION[ self::SESSION_MSG_KEY ];
			unset( $_SESSION[ self::SESSION_MSG_KEY ] );
		}

		if( ! isset( $_POST['sendMessage'] ) ) {
			return array(
				'successMessage' => $message
			);
		}

		$name    = htmlspecialchars( $_POST['name'] );
		$email   = htmlspecialchars( $_POST['email'] );
		$content = htmlspecialchars( $_POST['content'] );


		if( !$name || !$email || !$content ) {
			return array(
				'errorMessage' => 'Vyplňte prosím všechna pole.',
				'name'         => $name,
				'email'        => $email,
				'content'      => $content,
			);
		}

		$sent = mail( self::EMAIL_TO, self::EMAIL_SUBJECT, $content, "From: {$name} <{$email}>" );

		if( $sent == false ) {
			return array(
				'errorMessage' => 'Při odesíláni zprávy došlo k chybě. Zkuste prosím odeslat později.',
				'name'         => $name,
				'email'        => $email,
				'content'      => $content,
			);
		}

		$_SESSION[ self::SESSION_MSG_KEY ] = 'Vaše zpráva byla odeslána.';
		redirect( null );
	}

	public function setupData( cWebPage $webPage )
	{
		return array(
			'formData' => $this->processData()
		);
	}
}