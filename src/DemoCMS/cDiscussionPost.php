<?php
namespace DemoCMS;

use Inspirio\Database\aDataObject;

class cDiscussionPost extends aDataObject
{
	protected static $items = array(
		'ID' => array(
			'dbColumn' => 'ID',
			'type'     => 'int',
		),

		'webPageID' => array(
			'dbColumn' => 'web_page_ID',
			'type'     => 'int',
			'sanitize' => 'foreingKey',
		),

		'authorName' => array(
			'dbColumn' => 'author_name',
			'type'     => 'string',
			'sanitize' => 'nullOnEmpty',
		),

		'authorEmail' => array(
			'dbColumn' => 'author_email',
			'type'     => 'string',
			'sanitize' => 'nullOnEmpty',
		),

		'content' => array(
			'dbColumn' => 'content',
			'type'     => 'string',
		),

		'createdOn' => array(
			'dbColumn' => 'created_on',
			'type'     => 'datetime',
		),

		'webPage' => array(
			'type' => 'cWebPage'
		)
	);

	protected static $meta = array(
		'tableName' => 'discussion_posts'
	);
}
