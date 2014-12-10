<?php
require_once 'vendor/autoload.php';

use Psr\Log\LogLevel;

/**
 * This is the entry point for your redmine-command slack integration.
 * It handles the configuration parameters and invokes the command
 * factory parsing and execution of commands.
 * This file should be placed at the same level of your "vendor" folder.
 */

$config = new RedmineCommand\Configuration ();

/**
 * token sent by slack (from your "Slash Commands" integration).
 */
$config->token = "vuLKJlkjdsflkjLKJLKJlkjd";

/**
 * URL of the Incoming WebHook slack integration.
 */
$config->slack_webhook_url = "https://hooks.slack.com/services/LKJDFKLJFD/DFDFSFDDSFDS/sdlfkjdlkfjLKJLKJKLJO";

/**
 * Slack API authentication token for your team.
 */
$config->slack_api_token = "xoxp-98475983759834-38475984579843-34985793845";

/**
 * Base URL of redmine installation.
 */
$config->redmine_url = "https://your/redmine/install";

/**
 * Redmine API key.
 */
$config->redmine_api_key = "0d089u4sldkfjfljlksdjffj43099034j";

/**
 * Log level threshold.
 * The default is DEBUG.
 * If you are done testing or installing in production environment,
 * uncomment this line.
 */
// $config->log_level = LogLevel::WARNING;

/**
 * logs folder, make sure the invoker have write permission.
 */
$config->log_dir = "/srv/api/redmine-command/logs";

/**
 * Database folder, used by some commands to store user related temporal information.
 * Make sure the invoker have write permission.
 */
$config->db_dir = "/srv/api/redmine-command/db";

/**
 * This is to prevent redmine-command entry point to be called outside slack.
 * If you want it to be called from anywhere, comment the following 3 lines:
 */

if (! RedmineCommand\Validator::validate ( $_POST, $config )) {
	die ();
}

/**
 * Entry point execution.
 */
$command = RedmineCommand\CommandFactory::create ( $_POST, $config );
$command->execute ();
$command->post ();
