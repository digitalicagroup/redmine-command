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
	 * Must return an instance of \RedmineCommand\SlackResult .
	 *
	 * @see \RedmineCommand\AbstractCommand::executeImpl()
	 * @return \RedmineCommand\SlackResult
	 */
	protected function executeImpl() {
		$log = $this->log;
		$result = new SlackResult ();
		
		$log->debug ( "CmdCreate: Issues Id: " . implode ( ",", $this->cmd ) );
		
		$client = new Client ( $this->config->redmine_url, $this->config->redmine_api_key );
		
		$resultText = "[requested by " . $this->post ["user_name"] . "]";
		if (empty ( $this->cmd )) {
			$resultText .= " details needed!";
		} else {
			$resultText .= " New issue created: ";
		}
		
		$attachments = array ();
		$attachment = null;
		$attachmentError = null;
		
		// creating issue
		$client->setImpersonateUser ( 'luis' );
		$issue = $client->api ( 'issue' )->create ( array (
				'project_id' => 'testing',
				'subject' => 'testing subject',
				'description' => 'long description blablabla',
				'assigned_to' => 'luis' 
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
		return $result;
	}
}
