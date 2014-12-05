<?php

namespace RedmineCommand;

use Redmine\Client;

/**
 * Class to handle "show" commands.
 * This command will receive an array of issue numbers, will
 * gather information from a redmine project, and store its
 * results on the in the internal result attribute of
 * AbstractCommand.
 * Each issue is stored as slack attachment
 * (\RedmineCommand\SlackResultAttachment) . Unknown issues will
 * be attached at the end.
 *
 * @author Luis Augusto Peña Pereira <lpenap at gmail dot com>
 *        
 */
class CmdShow extends AbstractCommand {
	/**
	 * Factory method to be implemented from \RedmineCommand\AbstractCommand .
	 *
	 *
	 * Must return an instance of \RedmineCommand\SlackResult .
	 *
	 * @see \RedmineCommand\AbstractCommand::executeImpl()
	 * @return \RedmineCommand\SlackResult
	 */
	protected function executeImpl() {
		$log = $this->log;
		$result = new SlackResult ();
		
		$log->debug ( "CmdShow: Issues Id: " . implode ( ",", $this->cmd ) );
		
		$client = new Client ( $this->config->redmine_url, $this->config->redmine_api_key );
		
		$resultText = "[requested by " . $this->post ["user_name"] . "]";
		if (empty ( $this->cmd )) {
			$resultText .= " Issue number required!";
		} else {
			$resultText .= " Issue Details: ";
		}
		
		// Fetching issues and adding them as slack attachments
		$attachments = array ();
		$attachmentUnknown = null;
		foreach ( $this->cmd as $issueId ) {
			$log->debug ( "CmdShow: calling Redmine api for issue id #$issueId" );
			$issue = $client->api ( 'issue' )->show ( ( int ) $issueId );
			$attachment = new SlackResultAttachment ();
			if (! is_array ( $issue )) {
				if (strcmp ( $issue, "Syntax error" ) == 0) {
					if ($attachmentUnknown == null) {
						$attachmentUnknown = new SlackResultAttachment ();
						$attachmentUnknown->setTitle ( "Unknown Issues:" );
						$attachmentUnknown->setText ( "" );
					}
					$log->debug ( "CmdShow: #$issueId issue unknown!" );
					$attachmentUnknown->setText ( $attachmentUnknown->getText () . " $issueId" );
				}
			} else {
				$log->debug ( "CmdShow: #$issueId issue found!" );
				$attachment = Util::convertIssueToAttachment ( $this->config->getRedmineIssuesUrl (), $issueId, $issue );
				$attachments [] = $attachment;
			}
		}
		$result->setText ( $resultText );
		if ($attachmentUnknown != null) {
			$attachments [] = $attachmentUnknown;
		}
		$result->setAttachmentsArray ( $attachments );
		return $result;
	}
}
