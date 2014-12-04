<?php
namespace RedmineCommand;

use Katzgrau\KLogger\Logger;

abstract class AbstractCommand {
  protected $post;
  protected $config;
  protected $log;
  protected $cmd;
  protected $response_to_source_channel;
  private $result;

  public function __construct ($post, $config, $arr = array()) {
    $this->log = new Logger ($config->log_dir);
    $this->post = $post;
    $this->config = $config;
    $this->cmd = $arr;
    $this->response_to_source_channel = true;
    $this->result = new SlackResult();
  }

  /**
   * Function to be implemented by command subclasses.
   * Should return a proper instance of SlackResult class.
   */
  abstract protected function executeImpl ();
  
  // TODO move channel_id string to global config
  public function execute () {
    $this->log->debug(
      "AbstractCommand (".get_class($this)."): command array: {".
      implode(",", $this->cmd)."}");
    $this->result = $this->executeImpl ();
    $this->log->debug ( $this->result->toJson());

    if ($this->response_to_source_channel) {
      $this->log->debug (
        "AbstractCommand (".get_class($this).
        "): requesting channel name for channel: ".
        $this->post["channel_id"]);
      $this->result->setChannel (
        Util::getChannelName ($this->config, $this->post["channel_id"])
      );
    }
    return $this->result;
  }
  
  public function setResponseToSourceChannel ($bool) {
    $this->response_to_source_channel = $bool;
  }

  public function post() {
    $json = $this->result->toJson();
    $this->log->debug (
      "AbstractCommand (".get_class($this)."): response json: $json");
    return Util::post (
      $this->config->slack_webhook_url, $json);
  }
}
