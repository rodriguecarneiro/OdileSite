<?php

class ProjectController extends \Core\Controller
{
	public function indexAction()
	{
		$this->isLoggedIn();
		
		$sliders = $this->getModel('slider')->select();

		if(current($sliders)){
			foreach($sliders as $slider){
				$slider->thumb = $this->getModel('slider')->get_thumb_picture($slider->id);
			}
		}

		$this->render('index', [
			'sliders' => $sliders
		]);
	}
}