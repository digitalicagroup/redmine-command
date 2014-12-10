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
			foreach ( $help_data as $h ) {
				$fields [] = SlackResultAttachmentField::withAttributes ( $f ["help_title"], $f ["help_text"], false );
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
