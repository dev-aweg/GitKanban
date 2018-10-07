<?php

use KanbanBoard\Authentication;
use KanbanBoard\GithubActual;
use KanbanBoard\Utilities;

require '../classes/KanbanBoard/Github.php';
require '../classes/Utilities.php';
require '../classes/KanbanBoard/Authentication.php';
require "../../var/security/repos_list.php";

$keys_path = 'keys.php';
$isLocal = $_SERVER['SERVER_NAME'] != "127.0.0.1" ? false : true;
if ($isLocal) {
	$keys_path = 'local_keys.php';
}

require "../../var/security/{$keys_path}";

putenv("GH_REPOSITORIES={$repos}");
putenv("GH_ACCOUNT={$account}");
putenv("GH_CLIENT_ID={$client_id}");
putenv("GH_CLIENT_SECRET={$client_secret}");

$repositories = explode('|', Utilities::env('GH_REPOSITORIES'));
$authentication = new \KanbanBoard\Login();
$token = $authentication->login();
$github = new GithubClient($token, Utilities::env('GH_ACCOUNT'));
$board = new \KanbanBoard\Application($github, $repositories, array('waiting-for-feedback'));
$data = $board->board();
$m = new Mustache_Engine(array(
	'loader' => new Mustache_Loader_FilesystemLoader('../views'),
));
echo $m->render('index', array('milestones' => $data));
