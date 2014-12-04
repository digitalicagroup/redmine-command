<?php
require_once 'vendor/autoload.php';

$config = new RedmineCommand\Configuration();

// token sent by slack (from your "Slash Commands" integration)
$config->token =              "vuLKJlkjdsflkjLKJLKJlkjd";

// url of the Incoming WebHook for this integration
$config->slack_webhook_url =  "https://hooks.slack.com/services/LKJDFKLJFD/DFDFSFDDSFDS/sdlfkjdlkfjLKJLKJKLJO";

// Slack API token
$config->slack_api_token =    "xoxp-98475983759834-38475984579843-34985793845";

// Base URL of redmine installation.
$config->redmine_url =        "https://your/redmine/install";

// Redmine API key
$config->redmine_api_key =    "0d089u4sldkfjfljlksdjffj43099034j";

// if true, prints as many info as posible to the error_log
// if false, prints ERRORs only
$config->debug =              true;

// log folder, make sure the invoker have write permission
$config->log_dir =            "/srv/api/redmine-command-dev/logs";

// We should validate that we are being invoked by slack with the correct token
if (!RedmineCommand\Validator::validate($_POST, $config)) {
  die;
} 

$command = RedmineCommand\CommandFactory::create($_POST, $config);
$command->execute ();
$command->post ();
