<?php
namespace RedmineCommand;

use Katzgrau\KLogger\Logger;

class Util {
  // TODO move constants to global configuration file
  public static function post ($url, $payload, 
      $contentType = 'Content-Type: application/json') {
    $ch = curl_init ($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array( 
      $contentType,
      'Content-Length: ' . strlen($payload))
    );
    $result = curl_exec($ch);
    //DebugLog("curl post result: ".$result);
    return $result;
  }

  // TODO move constants to global configuration file
  public static function getChannelName ($config, $channelId) {
    $log = new Logger ($config->log_dir);
    $channel = '';
    $api_channels_info_url = $config->api_channels_info_url;
    $api_groups_list_url = $config->api_groups_list_url;
    $slack_api_token = $config->slack_api_token;
    // Querying channels info service first
    $payload = array ("token" => $slack_api_token, "channel" => $channelId);
    $log->debug (
      "Util: going to invoke channels.info: $api_channels_info_url".
      " with payload: ". http_build_query($payload ));

    $result = self::post ($api_channels_info_url, http_build_query($payload),
      'multipart/form-data');
    $result = json_decode ($result, true);
    if ($result["ok"]) { 
      // Channel found!
      $channel = $result["channel"]["name"];
    } else { 
      // Querying groups list
      $payload = array("token" => $slack_api_token);
      $result = self::post ($api_groups_list_url, http_build_query($payload), 
        'multipart/form-data');
      $result = json_decode ($result, true);
      if ($result["ok"]) { 
        // look for group
        foreach ($result["groups"] as $group) { 
          if (strcmp ($group["id"], $channelId) == 0) { 
            $channel = $group["name"];
            break;
          }
        }
      }
    }
    return "#".$channel;
  }

  public static function convertIssueToAttachment 
    ($redmine_issues_url, $issue_id, $issue) {
    $attachment = new SlackResultAttachment();
    $attachment->setTitle ("#".$issue_id." ".$issue['issue']['subject']);
    $attTitle = "[<".
      $redmine_issues_url.$issue_id."|".
      $issue['issue']['tracker']['name'] .
      " #".$issue_id . 
      ">]";
    $attachment->setPretext ($attTitle);
    $attachment->setTitle ($issue['issue']['subject']);
    $attachment->setText ($issue["issue"]["description"]);
    $fixed_version = "None";
    if (isset ($issue["issue"]["fixed_version"]["name"])) { 
      $fixed_version = $issue["issue"]["fixed_version"]["name"];
    }
    $estimated_hours = "None";
    if (isset ($issue["issue"]["estimated_hours"])) {
      $estimated_hours = $issue["issue"]["estimated_hours"];
    }
    $assigned_to = "None";
    if (isset ($issue["issue"]["assigned_to"]["name"])) {
      $assigned_to = $issue["issue"]["assigned_to"]["name"];
    }
    $fields = array();
    $fields[] = self::createField ("Project", $issue["issue"]["project"]["name"]);
    $fields[] = self::createField ("Version", $fixed_version);
    $fields[] = self::createField ("Status", $issue["issue"]["status"]["name"]);
    $fields[] = self::createField ("Priority", $issue["issue"]["priority"]["name"]);
    $fields[] = self::createField ("Assigned To", $assigned_to);
    $fields[] = self::createField ("Author", $issue["issue"]["author"]["name"]);
    $fields[] = self::createField ("Start Date", $issue["issue"]["start_date"]);
    $fields[] = self::createField ("Estimated Hours", $estimated_hours);
    $fields[] = self::createField ("Done Ratio", $issue["issue"]["done_ratio"]."%");
    $fields[] = self::createField ("Spent Hours", $issue["issue"]["spent_hours"]);
    $fields[] = self::createField ("Created On", $issue["issue"]["created_on"]);
    $fields[] = self::createField ("Updated On", $issue["issue"]["updated_on"]);
    $attachment->setFieldsArray ($fields);
    return $attachment;
  }
  protected static function createField ($title, $value, $short = true) {
    $field = new SlackResultAttachmentField();
    $field->setTitle ($title);
    $field->setValue($value);
    $field->setShort($short);
    return $field;
  }
}
