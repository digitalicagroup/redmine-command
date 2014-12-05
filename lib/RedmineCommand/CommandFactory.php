<?php

namespace RedmineCommand;

use Katzgrau\KLogger\Logger;

/**
 * Class to handle input parameters parsing and command creation.
 * Aditional command classes only need to be added to attribute
 * $classes with a FQDN string.
 *
 * @author Luis Augusto Peña Pereira <lpenap at gmail dot com>
 *        
 */
class CommandFactory {
	
	/**
	 * References to FQDN of command classes.
	 * Aditional commands should
	 * be added here only.
	 *
	 * @var array an array of strings representing FQDN of command classes.
	 */
	protected static $classes = array (
			"show" => "RedmineCommand\CmdShow" 
	);
	public static function create($post, $config) {
		$cmd = new CmdUnknown ( $post, $config );
		$log = new Logger ( $config->log_dir, $config->log_level );
		$log->debug ( "CommandFactory: post received (json encoded): " . json_encode ( $post ) );
		// TODO move strings parameter 'text' to global definition
		if (isset ( $post ['text'] )) {
			$log->debug ( "CommandFactory: text received: " . $post ['text'] );
			// parsing inputs by space
			$input = preg_split ( "/[\s]+/", $post ['text'] );
			// the first word represents the command
			if (in_array ( $input [0], array_keys ( self::$classes ) )) {
				$class = self::$classes [$input [0]];
				array_shift ( $input );
				$cmd = new $class ( $post, $config, $input );
			}
		}
		return $cmd;
	}
}
