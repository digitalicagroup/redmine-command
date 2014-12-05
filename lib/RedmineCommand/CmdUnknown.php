<?php

namespace RedmineCommand;

/**
 * Default command to be executed when the input parameters
 * can't be assigned to any command.
 *
 * @author Luis Augusto Peña Pereira <lpenap at gmail dot com>
 *        
 */
class CmdUnknown extends AbstractCommand {
	protected function executeImpl() {
		$result = new SlackResult ();
		$result->setText ( 'Unknown Command' );
		$this->log->debug ( "CmdUnknown: Executing CmdUnknown" );
		return $result;
	}
} 
