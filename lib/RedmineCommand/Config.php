<?php
namespace RedmineCommand;

use Psr\Log\LogLevel;
use SlackHookFramework\Configuration;

define ('URL_ISSUES', "/issues/");

class Config extends Configuration {
  public $redmine_url;
  public $redmine_api_key;
  public $redmine_url_issues;

  public function __construct () {
  	parent::__construct();
    $this->redmine_url = null;
    $this->redmine_api_key = null;
    $this->redmine_url_issues = URL_ISSUES;
  }

  public function getRedmineIssuesUrl () {
    return rtrim($this->redmine_url, '/') . $this->redmine_url_issues;
  }
}

