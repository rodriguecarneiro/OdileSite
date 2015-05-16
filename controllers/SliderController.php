<?php

use Models\Image;
use Models\Slider;

class SliderController extends \Core\Controller
{
	public function showAction()
	{
		$slug = $this->getParam('slug');

		$images = [];
		if($slug !== null){
	
			$slider = (new Slider)->getOne([
				'slug' => $slug
			]);

			if($slider === null){
				$this->notFound();
			}

			$images = (new Image)->select([
				'where' => ['slider_id' => $slider->id],
				'orderBy' => 'order'
			]);

		}
		
		$this->render('index', [
			'images' => $images
		]);
	}
}