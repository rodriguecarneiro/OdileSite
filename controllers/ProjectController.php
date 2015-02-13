<?php

class ProjectController extends \Core\Controller
{
	public function indexAction()
	{
		$this->render('index', [
			'var' => 'yes ca marche'
		]);
	}
}