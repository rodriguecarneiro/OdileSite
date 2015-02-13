<?php
	session_start();
	header('Content-Type: text/html; charset=utf-8');
	date_default_timezone_set('Europe/Paris');

	require_once '../bootstrap.php';

	//include App
	set_include_path(get_include_path() . PATH_SEPARATOR . "../");
	require_once "../core/Application.php";

	//launch
	$app = new \Core\Application();
	$app->run();