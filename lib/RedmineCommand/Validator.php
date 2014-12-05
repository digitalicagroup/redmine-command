<?php

namespace RedmineCommand;

use Katzgrau\KLogger\Logger;

/**
 * Class to be used in the verification of a slack request.
 * 
 * @author Luis Augusto Peña Pereira <lpenap at gmail dot com>
 *        
 */
class Validator {
	/**
	 * It return whether or not a configured token matches with the token
	 * received (from slack) in the $_POST parameters.
	 * 
	 * @param array $post
	 *        	reference to the $_POST parameters.
	 * @param \RedmineCommand\Configuration $configuration        	
	 * @return boolean
	 */
	public static function validate($post, $configuration) {
		// TODO move constants to global configuration
		$log = new Logger ( $configuration->log_dir, $configuration->log_level );
		$token = $configuration->token;
		$result = false;
		if ($token != null && isset ( $post ['token'] )) {
			if (strcmp ( $token, $post ['token'] ) == 0)
				$result = true;
		}
		return $result;
	}
}
