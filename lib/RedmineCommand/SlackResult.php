<?php

namespace RedmineCommand;

// TODO move constants to a global configuration
define ( 'R_TEXT', 'text' );
define ( 'R_MRKDWN', 'mrkdwn' );
define ( 'R_ATT', 'attachments' );
define ( 'R_TITLE', 'title' );
define ( 'R_FALLBACK', 'fallback' );
define ( 'R_PRETEXT', 'pretext' );
define ( 'R_MRKDWN_IN', 'mrkdwn_in' );
define ( 'R_FIELDS', 'fields' );
define ( 'R_VALUE', 'value' );
define ( 'R_SHORT', 'short' );
define ( 'R_CHANNEL', 'channel' );

/**
 * Abstract wrapper class for an array.
 *
 * @author Luis Augusto Peña Pereira <lpenap at gmail dot com>
 *        
 */
abstract class AbstractArray {
	/**
	 * Internal array to be wrapped.
	 *
	 * @var array an associative array of strings that can be converted to a json.
	 */
	protected $a;
	
	/**
	 * Constructor.
	 * Initializes the internal array
	 */
	public function __construct() {
		$this->a = array ();
	}
	
	/**
	 * Returns the internal array.
	 *
	 * @return array
	 */
	public function toArray() {
		return $this->a;
	}
	
	/**
	 * Returns the json representation of the internal array.
	 *
	 * @return string
	 */
	public function toJson() {
		return json_encode ( $this->a );
	}
	
	/**
	 * Stores a child array with the $key key.
	 *
	 * @param string $key        	
	 * @param array $objs_array
	 *        	array of \RedmineCommand\AbstractArray (subclasses) instances.
	 */
	public function setArray($key, $objs_array) {
		$this->a [$key] = array ();
		foreach ( $objs_array as $obj ) {
			$this->a [$key] [] = $obj->toArray ();
		}
	}
	
	/**
	 * Getter method for a specific value referenced by the given key.
	 * @param string $key
	 */
	public function getValue ($key) {
    if (isset ($this->a[$key])) {
		  return $this->a[$key];
    } else {
      return NULL;
    }
	}
}

/**
 * Class to define accessor methods to the parent json element present
 * in a payload to the slack incoming webhook.
 * Each payload consists of one instance of SlackResult.
 * In a SlackResult instance, an array of SlackResultAttachment
 * can be stored to represent "attachments" in a message to slack.
 * In a SlackResultAttachment, an array of SlackResultAttachmentField
 * can be stored to represent "fields" in the given attachment of a message to slack.
 *
 * @author Luis Augusto Peña Pereira <lpenap at gmail dot com>
 *        
 */
class SlackResult extends AbstractArray {
	public function __construct() {
		parent::__construct ();
		$this->a [R_MRKDWN] = true;
	}
	public function setText($text) {
		$this->a [R_TEXT] = $text;
	}
	public function setMrkdwn($mrkdwn) {
		$this->a [R_MRKDWN] = $mrkdwn;
	}
	public function setAttachmentsArray($att) {
		$this->setArray ( R_ATT, $att );
	}
	public function setChannel($channel) {
		$this->a [R_CHANNEL] = $channel;
	}
}

/**
 * Class to be used as an attachment in a message to slack.
 *
 * @author Luis Augusto Peña Pereira <lpenap at gmail dot com>
 *        
 */
class SlackResultAttachment extends AbstractArray {
	public function __construct() {
		parent::__construct ();
		$this->a [R_MRKDWN_IN] = array (
				R_PRETEXT,
				R_TEXT,
				R_TITLE,
				R_FALLBACK,
				R_FIELDS 
		);
	}
	public function setTitle($title) {
		$this->a [R_TITLE] = $title;
	}
	public function setFallback($fallback) {
		$this->a [R_FALLBACK] = $fallback;
	}
	public function setPretext($pretext) {
		$this->a [R_PRETEXT] = $pretext;
	}
	public function setText($text) {
		$this->a [R_TEXT] = $text;
	}
	public function getText() {
		return $this->a [R_TEXT];
	}
	public function setMrkdwnArray($arr) {
		$this->a [R_MRKDWN_IN] = $arr;
	}
	public function setFieldsArray($fields) {
		$this->setArray ( R_FIELDS, $fields );
	}
}

/**
 * Class to be used as field in an attachment.
 *
 * @author Luis Augusto Peña Pereira <lpenap at gmail dot com>
 *        
 */
class SlackResultAttachmentField extends AbstractArray {
	public static function withAttributes ($title, $value, $isShort = true) {
		$instance = new self();
    $instance->setTitle ($title);
    $instance->setValue ($value);
    $instance->setShort ($isShort);
    return $instance;
	}
	public function setTitle($title) {
		$this->a [R_TITLE] = $title;
	}
	public function setValue($value) {
		$this->a [R_VALUE] = $value;
	}
	public function setShort($isShort) {
		$this->a [R_SHORT] = $isShort;
	}
	public static function compare($a, $b) {
		$al = strtolower ( $a->getValue(R_TITLE) );
		$bl = strtolower ( $b->getValue(R_TITLE) );
		if ($al == $bl) {
			return 0;
		}
		return ($al > $bl) ? + 1 : - 1;
	}
}
