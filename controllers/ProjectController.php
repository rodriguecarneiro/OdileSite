<?php

use Models\Image;

class ProjectController extends \Core\Controller
{
	public function indexAction()
	{
		$slider = (new Image)->select([
			'where' => ['front' => 1],
			'orderBy' => 'order'
		]);

		$this->render('index', [
			'slider' => $slider
		]);
	}
}