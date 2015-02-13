<?php

include_once '../lib/Upload/MyUpload.php';

class SliderController extends \Core\Controller
{
	public function newAction()
	{
		$this->isLoggedIn();
		$this->render('new');
	}

	public function addAction()
	{
		$slider = $this->model->create();

		$upload = new MyUpload();

		if (count($_FILES['gallery']['name']) !== 0) {
			$upload->multiUpload(
				$slider,
				array(
					array('size' => 'big', 'image_y' => 700, 'image_ratio_x' => true),
					array('size' => 'thumb', 'image_resize' => true, 'image_ratio_crop' => true, 'image_x' => 290, 'image_y' => 140)
				)
			);
		}
		
		$this->redirect('/admin');
	}
}