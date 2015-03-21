<?php

namespace RedmineCommand;

use Katzgrau\KLogger\Logger;
use SlackHookFramework\Util;
use SlackHookFramework\SlackResult;
use SlackHookFramework\SlackResultAttachment;
use SlackHookFramework\SlackResultAttachmentField;

/**
 * Class to store utility methods.
 *
 * @author Luis Augusto Peña Pereira <lpenap at gmail dot com>
 *        
 */
class Utils {
	/**
	 * Converts an array representation of an issue (as returned by the php-redmine-api) to an
	 * instance of SlackResultAttachment to be used in messages sent to a slack incoming webhook.
	 *
	 * @param string $redmine_issues_url        	
	 * @param string $issue_id        	
	 * @param array $issue        	
	 * @return \SlackHookFramework\SlackResultAttachment
	 * @see \SlackHookFramework\SlackResultAttachment
	 * @link https://github.com/kbsali/php-redmine-api
	 * @link https://github.com/digitalicagroup/slack-hook-framework
	 */
	public static function convertIssueToAttachment($redmine_issues_url, $issue_id, $issue) {
		$attachment = new SlackResultAttachment ();
		$attachment->setTitle ( "#" . $issue_id . " " . $issue ['issue'] ['subject'] );
		$attTitle = "[<" . $redmine_issues_url . $issue_id . "|" . $issue ['issue'] ['tracker'] ['name'] . " #" . $issue_id . ">]";
		$attachment->setPretext ( $attTitle );
		$attachment->setTitle ( $issue ['issue'] ['subject'] );
		$attachment->setText ( $issue ["issue"] ["description"] );
		$fixed_version = "None";
		if (isset ( $issue ["issue"] ["fixed_version"] ["name"] )) {
			$fixed_version = $issue ["issue"] ["fixed_version"] ["name"];
		}
		$estimated_hours = "None";
		if (isset ( $issue ["issue"] ["estimated_hours"] )) {
			$estimated_hours = $issue ["issue"] ["estimated_hours"];
		}
		$assigned_to = "None";
		if (isset ( $issue ["issue"] ["assigned_to"] ["name"] )) {
			$assigned_to = $issue ["issue"] ["assigned_to"] ["name"];
		}
		$fields = array ();
		$fields [] = Util::createField ( "Project", $issue ["issue"] ["project"] ["name"] );
		$fields [] = Util::createField ( "Version", $fixed_version );
		$fields [] = Util::createField ( "Status", $issue ["issue"] ["status"] ["name"] );
		$fields [] = Util::createField ( "Priority", $issue ["issue"] ["priority"] ["name"] );
		$fields [] = Util::createField ( "Assigned To", $assigned_to );
		$fields [] = Util::createField ( "Author", $issue ["issue"] ["author"] ["name"] );
		$fields [] = Util::createField ( "Start Date", $issue ["issue"] ["start_date"] );
		$fields [] = Util::createField ( "Estimated Hours", $estimated_hours );
		$fields [] = Util::createField ( "Done Ratio", $issue ["issue"] ["done_ratio"] . "%" );
		$fields [] = Util::createField ( "Spent Hours", $issue ["issue"] ["spent_hours"] );
		$fields [] = Util::createField ( "Created On", $issue ["issue"] ["created_on"] );
		$fields [] = Util::createField ( "Updated On", $issue ["issue"] ["updated_on"] );
		$attachment->setFieldsArray ( $fields );
		return $attachment;
	}

}
