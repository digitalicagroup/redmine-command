<?php

namespace RedmineCommand;

/**
 * Class to parse help data (loaded by CommandFactory) into a SlackResult instance
 * containing all available commands into fields of a single attachment.
 *
 * @author Luis Augusto Peña Pereira <lpenap at gmail dot com>
 *        
 */
class CmdHelp extends AbstractCommand {
	protected function executeImpl() {
		$log = $this->log;
		$result = new SlackResult ();
		$result->setText ( "redmine-command help" );
		$att = new SlackResultAttachment ();
		$att->setTitle ( "Available Commands:" );
		$att->setFallback ( "Available Commands:" );
		
		$fields = array ();
		$help_data = CommandFactory::getHelpData ();
		if ($help_data == null) {
			$log->error ( "CmdHelp: Error loading help data, check commands_definition.json format or file permissions" );
		} else {
			$help_keys = array_keys ( $help_data );
			foreach ( $help_keys as $key ) {
				$fields [] = SlackResultAttachmentField::withAttributes ( $key, $help_data [$key], false );
			}
		}
		usort ( $fields, array (
				"RedmineCommand\SlackResultAttachmentField",
				"compare" 
		) );
		$att->setFieldsArray ( $fields );
		$result->setAttachmentsArray ( array (
				$att 
		) );
		return $result;
	}
}
