<?php

use Models\Image;
use Models\Slider;

class ProjectController extends \Core\Controller
{
	public function indexAction()
	{
		if($this->getGET('page')){
			
			$slug = $this->getGET('page');

	
			$slider = (new Slider)->getOne([
				'slug' => $slug
			]);

			if($slider === null){
				$this->notFound();
			}

			$images = (new Image)->select([
				'where' => ['slider_id' => $slider->id]
			]);

		} else {
			
			$images = (new Image)->select([
				'where' => ['front' => 1],
				'orderBy' => 'order'
			]);
		}

		$this->render('index', [
			'images' => $images
		]);
	}
}