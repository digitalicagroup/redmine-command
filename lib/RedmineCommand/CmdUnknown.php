<?php
namespace RedmineCommand;

use Katzgrau\KLogger\Logger;

class CmdUnknown extends AbstractCommand {
  protected function executeImpl () {
    $log = new Logger ($this->config->log_dir);
    $result = new SlackResult ();
    $result->setText('Unknown Command');
    $log->debug ("CmdUnknown: Executing CmdUnknown");
    return $result;
  }
} 
