<?php

namespace RedmineCommand;

class CmdHelp extends AbstractCommand {
	protected function executeImpl () {
		$log = $this->log;
		$result = new SlackResult ();
		$result->setText ("redmine-command help");
		$att = new SlackResultAttachment ();
		$att->setTitle ("Available Commands:");
		$att->setFallback ("Available Commands:");
		$fields = array();
		
		$fields[] = new SlackResultAttachmentField ("show", "<show> <issue numbers> Shows values for a list of issue numbers (space separated).", false);
		$fields[] = new SlackResultAttachmentField ("aaa", "text a", false);
		/**
		 * Add new instances of SlackResultAttachmentField here
		 * representing help texts for each command.
		 */
		
		usort ($fields, array ("RedmineCommand\SlackResultAttachmentField", "compare"));
		$att->setFieldsArray ($fields);
		$result->setAttachmentsArray (array($att));
		return $result;
	}
}