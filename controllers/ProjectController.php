<?php

use Models\Image;

class ProjectController extends \Core\Controller
{
	public function indexAction()
	{
		$slider = (new Image)->select([
			'where' => ['slider_id' => 2],
			'orderBy' => 'order'
		]);

		$this->render('index', [
			'slider' => $slider
		]);
	}
}