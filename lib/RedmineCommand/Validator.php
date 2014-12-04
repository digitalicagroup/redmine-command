<?php
namespace RedmineCommand;

use Katzgrau\KLogger\Logger;

class Validator {
  // TODO move constants to global configuration
  public static function validate ($post, $configuration) {
    $log = new Logger ($configuration->log_dir);
    $token = $configuration->token;
    $result = false;
    if ($token!=null && isset ($post['token'])) {
      if (strcmp($token, $post['token'])==0)
        $result = true;
    }
    return $result;
  }
}
    
