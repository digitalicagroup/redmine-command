<?php
namespace RedmineCommand;

use Katzgrau\KLogger\Logger;

class CommandFactory {
  /**
   * Classes definitions for commands.
   * To add a new Command, implement your class
   * extending AbstractCommand, and add it here.
   */
  protected static $classes = array (
    "show" => "RedmineCommand\CmdShow"
  );

  public static function create ($post, $config) {
    $cmd = new CmdUnknown ($post, $config);
    $log = new Logger($config->log_dir);
    $log->debug ("CommandFactory: post received (json encoded): ".json_encode($post));
    // TODO move strings parameter 'text' to global definition
    if (isset ($post['text'])) {
      $log->debug ("CommandFactory: text received: ". $post['text']);
      // parsing inputs by space
      $input = preg_split ("/[\s]+/", $post['text']);
      // the first word represents the command
      if (in_array ($input[0], array_keys (self::$classes))) {
        $class = self::$classes[$input[0]];
        array_shift ($input);
        $cmd = new $class($post, $config, $input);
      }
    }
    return $cmd;
  }
}

