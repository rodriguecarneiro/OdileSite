<?php
	session_start();
	header('Content-Type: text/html; charset=utf-8');
	date_default_timezone_set('Europe/Paris');

	require_once '../bootstrap.php';

	require __DIR__ . '/../vendor/autoload.php';

	//launch
	$app = new Core\Application();
	$app->run();