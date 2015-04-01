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
		$client->setImpersonateUser ($this->post ["user_name"]);
		
		$text = self::getHelperText($client);
		print $text;
		
// 		$resultText = "[requested by " . $this->post ["user_name"] . "]";
// 		if (empty ( $this->cmd )) {
// 			$resultText .= " Ussage:  create ";
// 		} else {
// 			$resultText .= " New issue created: ";
// 		}
		
// 		$attachments = array ();
// 		$attachment = null;
// 		$attachmentError = null;
		
		// creating issue
// 		$issue = $client->api ( 'issue' )->create ( array (
// 				'project_id' => 'testing',
// 				'subject' => 'testing subject',
// 				'description' => 'long description blablabla',
// 				'assigned_to' => 'luis' 
// 		) );
		
// 		if (! $issue instanceof SimpleXMLElement) {
// 			$attachmentError = new SlackResultAttachment ();
// 			$attachmentError->setTitle ( "Error creating issue" );
// 			$attachmentError->setText ( "See log for details..." );
// 			$attachments [] = $attachmentError;
// 			$log->debug ( "CmdCreate: error creating issue!" );
// 		} else {
// 			$attachment = Utils::convertIssueToAttachment ( $this->config->getRedmineIssuesUrl (), $issue );
// 			$attachments [] = $attachment;
// 		}
		
// 		$result->setText ( $resultText );
		
// 		$result->setAttachmentsArray ( $attachments );
		return $result;
	}
	
	protected function getHelperText ($client) {
		$text = "Ussage:\n create <project_id> <tracker_id> <assigned_to> <subject>\n";
		$text .= "PROJECTS [project_id]:\n";
		$projects = $client->api('project')->all();
		foreach ($projects['projects'] as $proj) {
			$text .= "[". $proj['id'] . "]  -  ". $proj ['name'] ."\n"; 
		}
		$text .= "TRACKERS [project_id]:\n";
		$trackers = $client->api('tracker')->all();
		foreach ($trackers['trackers'] as $track) {
			$text .= "[". $track['id'] . "]  -  ". $track ['name'] ."\n";
		}
		$text .= "USERS [assigned_to]:\n";
		$users = $client->api('user')->all();
		foreach ($users['users'] as $user) {
			$text .= "[". $user['login'] . "]  -  ". $user ['firstname'] ." ".$user['lastname']."\n";
		}
		$text .= "Ussage:  create <project_id> <tracker_id> <assigned_to> <subject>\n";
		return $text;
	}
}
