<?php

namespace RedmineCommand;

use Redmine\Client;
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
			$resultText .= " Issue number required!";
		} else {
			$resultText .= " Issue Details: ";
		}
		$client->setImpersonateUser('luis');
		$texto = $client->api('issue')->create(array(
				'project_id'  => 'testing',
				'subject'     => 'testing subject',
				'description' => 'long description blablabla',
				'assigned_to' => 'luis',
		));
		
		// Fetching issues and adding them as slack attachments
		$attachments = array ();
		$attachment = new SlackResultAttachment ();
		$attachmentUnknown->setTitle ( "Unknown Issues:" );
		$attachmentUnknown->setText ( $texto );
		$attachments [] = $attachment;
// 		$attachmentUnknown = null;
// 		foreach ( $this->cmd as $issueId ) {
// 			$log->debug ( "CmdShow: calling Redmine api for issue id #$issueId" );
// 			$issue = $client->api ( 'issue' )->show ( ( int ) $issueId );
// 			$attachment = new SlackResultAttachment ();
// 			if (! is_array ( $issue )) {
// 				if (strcmp ( $issue, "Syntax error" ) == 0) {
// 					if ($attachmentUnknown == null) {
// 						$attachmentUnknown = new SlackResultAttachment ();
// 						$attachmentUnknown->setTitle ( "Unknown Issues:" );
// 						$attachmentUnknown->setText ( "" );
// 					}
// 					$log->debug ( "CmdShow: #$issueId issue unknown!" );
// 					$attachmentUnknown->setText ( $attachmentUnknown->getText () . " $issueId" );
// 				}
// 			} else {
// 				$log->debug ( "CmdShow: #$issueId issue found!" );
// 				$attachment = Utils::convertIssueToAttachment ( $this->config->getRedmineIssuesUrl (), $issueId, $issue );
// 				$attachments [] = $attachment;
// 			}
// 		}
// 		$result->setText ( $resultText );
// 		if ($attachmentUnknown != null) {
// 			$attachments [] = $attachmentUnknown;
// 		}
		$result->setAttachmentsArray ( $attachments );
		return $result;
	}
}
