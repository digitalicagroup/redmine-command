# Redmine Command

A simple Redmine slack integration to manage issues.

Uses
* One Slack "Slash Commands" and one "Incoming WebHooks" integration (see Install).
* [php-redmine-api](https://github.com/kbsali/php-redmine-api)
* [KLogger](https://github.com/katzgrau/KLogger)

How does it work?
* It installs as a PHP application on your web server (using composer).
* Through a "Slash Commands" Slack integration, it receives requests.
* It communicates with your redmine installation to gather (or update) data.
* Posts the results to an "Incoming WebHooks" Slack integration in the originator's channel or private group (yeah, private group!).

## Current Features

The current stable release implements an extensible architecture that support easy implementation of future commands.
Commands list:
* show . Shows all the details in a redmine issue(s).
 * ussage (from slack): /redmine show issue_numbers
 * example: /redmine show 1 2 10

## TODO

* Move all strings variables to a global definitions file.
* Implement more commands. The current work in progress centers around creating issues.

## Requirements

* PHP >= 5.4 with cURL extension,
* "Enable REST web service" on your redmine settings (Administration > Settings > Authentication)
 * Your "API access key" from your profile page.
* Slack integrations (see install).

## Install

### On Slack

* Create a new "Slash Commands" integration with the following data:
 * Command: /redmine (or whatever you like)
 * URL: the URL pointing to the index.php of your redmine-command install
 * Method: POST
 * Token: copy this token, we'll need it later.

* Create a new "Incoming WebHooks" slack integration:
 * Post to Channel: Pick one, but this will be ignored by redmine-command.
 * Webhook URL: copy this URL, we'll need it later.
 * Descriptive Label, Customize Name, Customize Icon: whatever you like.

* Go to [Slack API](https://api.slack.com/) and copy the authentication token for your team.

* Go to your profile page on Redmine and copy your "API access key".

### On your web server

Install [composer](http://getcomposer.org/download/) in a folder of your preference (should be accessible from your web server) then run:
```bash
$ php composer.phar require digitalicagroup/redmine-command:~0.1
$ cp vendor/digitalicagroup/redmine-command/index.php .
```
The last line copies index.php from the package with the configuration you need to modify.

Edit index.php and add the following configuration parameters:
```php
/**
 * token sent by slack (from your "Slash Commands" integration).
 */
$config->token =              "vuLKJlkjdsflkjLKJLKJlkjd";

/**
 * URL of the Incoming WebHook slack integration.
 */ 
$config->slack_webhook_url =  "https://hooks.slack.com/services/LKJDFKLJFD/DFDFSFDDSFDS/sdlfkjdlkfjLKJLKJKLJO";

/**
 * Slack API authentication token for your team.
 */
$config->slack_api_token =    "xoxp-98475983759834-38475984579843-34985793845";

/**
 * Base URL of redmine installation.
 */
$config->redmine_url =        "https://your/redmine/install";

/**
 * Redmine API key.
 */
$config->redmine_api_key =    "0d089u4sldkfjfljlksdjffj43099034j";

/**
 * Log level threshold. The default is DEBUG.
 * If you are done testing or installing in production environment,
 * uncomment this line.
 */
//$config->log_level =           LogLevel::WARNING;

/**
 * logs folder, make sure the invoker have write permission.
 */
$config->log_dir =            "/srv/api/redmine-command/logs";
```

Make sure you give write permissions to the log_dir folder.

## Troubleshooting

This is a list of common errors:
* "I see some errors about permissions in the apache error log".
 * The process running redmine-command (usually the web server) needs write permissions to the folder configured in you $config->log_dir parameter.
 * For example, if you are running apache, that folder group must be assigned to www-data and its write permission for groups must be turned on.
* "I followed the steps and nothing happens, nothing in web server error log and nothing in the app log".
 * If you see nothing in the logs (and have the debug level setted), may be the app is dying in the process of validating the slack token. redmine-command validates that the request matches with the configured token or the app dies at the very beginning.
* "There is no error in the web server error log, I see some output in the app log (with the debug log level), but i get nothing in my channel/group".
 * Check in the app log for the strings "[DEBUG] Util: group found!" or "[DEBUG] Util: channel found!" . If you can't see those strings, check if your slack authentication token for your team is from an user that have access to the private group you are writing from. 
* I just developed a new command but I am getting a class not found error on CommandFactory.
 * Every time you add a new command (hence a new class), you must update the composer autoloader. just type:
 * php composer.phar update  

## Contribute

If you want to add aditional commands, your are welcome to contribute. All you need to do is extend the AbstractCommand class, and add a new entry to the commands_definition.json file. (You can see CmdShow.php for an example of what a command must do).
All commands are received through the same "Slash Command Integration", so the first word after the /redmine must be the command trigger. The next words are splited by one or more spaces and passed to the command that triggered.

The active development is done under the unstable branch. And the last stable release candidate is in the master branch.

## About Digitalica

We are a small firm focusing on mobile apps development (iOS, Android) and we are passionate about new technologies and ways that helps us work better.
* This project homepage: [RedmineCommand](https://github.com/digitalicagroup/redmine-command)
* Digitalica homepage: [digitalicagroup.com](http://digitalicagroup.com)
