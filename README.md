# Redmine Command

A simple Redmine slack integration to manage issues.
We are currently working on the install process through composer/packagist so expect the next update shortly!

Uses
* One Slack "Slash Commands" and one "Incoming WebHooks" integration (see Install).
* [php-redmine-api](https://github.com/kbsali/php-redmine-api)
* [KLogger](https://github.com/katzgrau/KLogger)

How does it work?
* It installs as a PHP application on your web server.
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
//The token from the slash commands integration.
$config->token = "LKJdfjkJkDKJNFJ";

// the URL from the incoming webhook integration:
$config->slack_webhook = "https://hooks.slack.com/services/JLHDF/LDJF/KJHkjhdfkjhdfd";

// the authentication token for your team (Slack API):
$config->slack_api_token = "xoxp-8923479834779328749832-34234-234-234";

// Your redmine URL
$config->redmine_url = "https://your/redmine/url/";

// Your Redmine API access key
$config->redmine_api_key = "0a236328687abe774";
```

Make sure you give write permissions to the log_dir folder.

## Contribute

If you want to add aditional commands, your are welcome to contribute. All you need to do is extend the AbstractCommand class, and add a new entry to the CommandFactory. (You can see CmdShow.php for an example of what a command must do).

