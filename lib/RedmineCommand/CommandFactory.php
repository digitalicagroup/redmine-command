<?php

namespace RedmineCommand;

use Katzgrau\KLogger\Logger;

/**
 * Class to handle input parameters parsing and command creation.
 * Aditional command classes must extend AbstractCommand and be
 * added to commands_definition.json
 *
 * @author Luis Augusto Peña Pereira <lpenap at gmail dot com>
 *        
 */
class CommandFactory {
	protected static $classes = null;
	protected static $help_data = null;
	
	/**
	 * Factory method to create command instances.
	 * New commands should be added to commands_definition.json
	 *
	 * @param array $post
	 *        	Reference to $_POST
	 * @param \RedmineCommand\Configuration $config
	 *        	Configuration instance with parameters.
	 * @return \RedmineCommand\AbstractCommand Returns an instance of an AbstractCommand subclass.
	 */
	public static function create($post, $config) {
		$cmd = new CmdUnknown ( $post, $config );
		$log->debug ( "CommandFactory: post received (json encoded): " . json_encode ( $post ) );
		$log = new Logger ( $config->log_dir, $config->log_level );
		
		// checking if commands definitions have been loaded
		if (self::$classes == null || self::$help_data == null) {
			$result = self::reloadDefinitions ();
			if ($result) {
				$log->debug ( "CommandFactory: commands_definition.json loaded" );
			} else {
				$log->error ( "CommandFactory: Error loading commands_definition.json, check json format or file permissions." );
			}
		}
		// TODO move strings parameter 'text' to global definition
		if (isset ( $post ['text'] ) && self::$classes != null) {
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
	
	/**
	 * Read command definitions from commands_definition.json.
	 *
	 * @return boolean returns false if json could not be loaded, true otherwise.
	 */
	public static function reloadDefinitions() {
		$result = false;
		$json = json_decode ( file_get_contents ( "./commands_definition.json" ), true );
		if ($json != null) {
			self::$classes = array ();
			self::$help_data = array ();
			foreach ( $json ["commands"] as $command ) {
				self::$classes [$command ["trigger"]] = $command ["class"];
				self::$help_data [$command ["help_title"]] = $command ["help_text"];
			}
			$result = true;
		}
		return $result;
	}
	
	public static function getHelpData() {
		if (self::$help_data == null) {
			self::reloadDefinitions();
		}
		return self::$help_data;
	}
}
