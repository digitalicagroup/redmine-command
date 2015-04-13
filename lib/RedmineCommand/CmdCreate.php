<?php

namespace RedmineCommand;

use Redmine\Client;
use Redmine\Api\SimpleXMLElement;
use SlackHookFramework\AbstractCommand;
use SlackHookFramework\Util;
use SlackHookFramework\SlackResult;
use SlackHookFramework\SlackResultAttachment;
use SlackHookFramework\SlackResultAttachmentField;

/**
 * Class to handle "create" commands.
 *
 * @author Luis Augusto Peña Pereira <lpenap at gmail dot com>
 *        
 */
class CmdCreate extends AbstractCommand {
	/**
	 * Factory method to be implemented from \RedmineCommand\AbstractCommand .
	 *
	 * Must return an instance of \RedmineCommand\SlackResult .
	 *
	 * @see \RedmineCommand\AbstractCommand::executeImpl()
	 * @return \RedmineCommand\SlackResult
	 */
	protected function executeImpl() {
		$log = $this->log;
		$result = new SlackResult ();
		
		$log->debug ( "CmdCreate: Parameters: " . implode ( ",", $this->cmd ) );
		
		$client = new Client ( $this->config->redmine_url, $this->config->redmine_api_key );
		$client->setImpersonateUser ( $this->post ["user_name"] );
		
		if (empty ( $this->cmd ) || (count ( $this->cmd ) < 4)) {
			print self::getHelperText ( $client );
		} else {
			$resultText = "[requested by " . $this->post ["user_name"] . "]";
			$resultText .= " New issue created: ";
			
			$attachments = array ();
			$attachment = null;
			$attachmentError = null;
			
			$issue = $client->api ( 'issue' )->create ( array (
					'project_id' => $this->cmd [0],
					'tracker_id' => ( int ) $this->cmd [1],
					'assigned_to' => $this->cmd [2],
					'subject' => implode ( " ", array_slice ( $this->cmd, 3, count ( $this->cmd ) - 3 ) ) 
			) );
			
			if (! $issue instanceof SimpleXMLElement) {
				$attachmentError = new SlackResultAttachment ();
				$attachmentError->setTitle ( "Error creating issue" );
				$attachmentError->setText ( "See log for details..." );
				$attachments [] = $attachmentError;
				$log->debug ( "CmdCreate: error creating issue!" );
			} else {
				$attachment = Utils::convertIssueToAttachment ( $this->config->getRedmineIssuesUrl (), $issue );
				$attachments [] = $attachment;
			}
			
			$result->setText ( $resultText );
			
			$result->setAttachmentsArray ( $attachments );
		}
		return $result;
	}
	
	protected function getHelperText($client) {
		$text = "Ussage:  create <project_identifier> <tracker_id> <assigned_to> <subject>\n\n";
		$text .= " [ project_identifier ]:\n";
		$projects = $client->api ( 'project' )->all ();
		$projects_clean = array ();
		foreach ( $projects ['projects'] as $proj ) {
			$projects_clean [] = $proj ['identifier'];
		}
		asort ( $projects_clean );
		$count = 0;
		foreach ( $projects_clean as $proj ) {
			$text .= $proj;
			if ($count == 4) {
				$text .= "\n";
				$count = 0;
			} else {
				$text .= "     ";
			}
			$count ++;
		}
		$text .= "\n\n [ tracker_id ]:\n";
		$trackers = $client->api ( 'tracker' )->all ();
		foreach ( $trackers ['trackers'] as $track ) {
			$text .= $track ['id'] . "   -   " . $track ['name'] . "\n";
		}
		$text .= "\n [ assigned_to ]:\n";
		$users = $client->api ( 'user' )->all ();
		$users_clean = array ();
		foreach ( $users ['users'] as $user ) {
			$users_clean [] = $user ['login'];
		}
		asort ( $users_clean );
		$count = 0;
		foreach ( $users_clean as $user ) {
			$text .= $user;
			if ($count == 4) {
				$text .= "\n";
				$count = 0;
			} else {
				$text .= "     ";
			}
			$count ++;
		}
		$text .= "\n\nUssage:  create <project_identifier> <tracker_id> <assigned_to> <subject>\n";
		$text .= "Example: create myproject 1 username this is the subject\n";
		return $text;
	}
}
