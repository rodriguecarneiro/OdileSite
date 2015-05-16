<?php

use Models\Image;

class HomeController extends \Core\Controller
{
	public function indexAction()
	{
		$images = (new Image)->select([
			'where' => ['front' => 1],
			'orderBy' => 'order'
		]);

		$this->render('index', [
			'images' => $images
		]);
	}
}