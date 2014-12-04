<?php

namespace RedmineCommand;

// TODO move constants to global configuration
define ('R_TEXT', 'text');
define ('R_MRKDWN', 'mrkdwn');
define ('R_ATT', 'attachments');
define ('R_TITLE', 'title');
define ('R_FALLBACK', 'fallback');
define ('R_PRETEXT', 'pretext');
define ('R_MRKDWN_IN', 'mrkdwn_in');
define ('R_FIELDS', 'fields');
define ('R_VALUE', 'value');
define ('R_SHORT', 'short');
define ('R_CHANNEL', 'channel');

abstract class AbstractArray {
  protected $a;
  public function __construct () {
    $this->a = array ();
  }
  public function toArray () {
    return $this->a;
  }
  public function toJson () {
    return json_encode ($this->a);
  }
  public function setArray($key, $objs_array) {
    $this->a[$key] = array();
    foreach ($objs_array as $obj) {
      $this->a[$key][] = $obj->toArray();
    }
  } 
}

class SlackResult extends AbstractArray {
  public function __construct () {
    parent::__construct();
    $this->a[R_MRKDWN] = true;
  }
  public function setText ($text) {
    $this->a[R_TEXT] = $text;
  }
  public function setMrkdwn ($mrkdwn) {
    $this->a[R_MRKDWN] = $mrkdwn;
  }
  public function setAttachmentsArray ($att) {
    $this->setArray(R_ATT, $att);
  }
  public function setChannel ($channel) { 
    $this->a[R_CHANNEL] = $channel;
  }
}

class SlackResultAttachment extends AbstractArray {
  public function __construct () {
    parent::__construct();
    $this->a[R_MRKDWN_IN] = array (
      R_PRETEXT, R_TEXT, R_TITLE, R_FALLBACK, R_FIELDS);
  }
  public function setTitle ($title) {
    $this->a[R_TITLE] = $title;
  }
  public function setFallback ($fallback) {
    $this->a[R_FALLBACK] = $fallback;
  }
  public function setPretext ($pretext) {
    $this->a[R_PRETEXT] = $pretext;
  }
  public function setText ($text) {
    $this->a[R_TEXT] = $text;
  }
  public function getText () {
    return $this->a[R_TEXT];
  }
  public function setMrkdwnArray ($arr) {
    $this->a[R_MRKDWN_IN] = $arr;
  }
  public function setFieldsArray ($fields) {
    $this->setArray (R_FIELDS, $fields);
  }
}

class SlackResultAttachmentField extends AbstractArray {
  public function setTitle ($title) {
    $this->a[R_TITLE] = $title;
  }
  public function setValue ($value) {
    $this->a[R_VALUE] = $value;
  }
  public function setShort ($isShort) {
    $this->a[R_SHORT] = $isShort;
  }
}

