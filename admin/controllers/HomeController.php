<?php

use \Models\Slider;

class HomeController extends \Core\Controller
{
	public function indexAction()
	{
		$sliders = (new Slider)->select();

		if($sliders){
			foreach($sliders as $slider){
				$slider->thumb = $this->getModel('slider')->get_thumb_picture($slider->id);
			}
		}

		$this->render('index', [
			'sliders' => $sliders
		]);
	}
}