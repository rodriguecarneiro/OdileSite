<?php

use Libs\Upload\MyUpload;
use Models\Slider;
use Models\Image;

class SliderController extends \Core\Controller
{
	public function newAction()
	{
		$this->render('new');
	}

	public function addAction()
	{
		$slider = (new Slider)->create();
		
		$upload = new MyUpload();
		if (count($_FILES['gallery']['name']) !== 0) {
			$upload->multiUpload(
				$slider,
				array(
					array('size' => 'big', 'image_resize' => true, 'image_ratio_crop' => true, 'image_x' => 800, 'image_y' => 533),
					array('size' => 'thumb', 'image_resize' => true, 'image_ratio_crop' => true, 'image_x' => 290, 'image_y' => 140)
				)
			);
		}
		
		$this->redirect('/admin');
	}

	public function editAction()
	{
		try{
			$slider = $this->model->getCurrent($this->getParam('id'));
		}catch(Exception $e){
			echo $e->getMessage() . '<br/><a href="/admin">Retour</a>';
			return false;
		}

		$slider->images = $this->getModel('image')->select([
			'where' => ['slider_id' => $slider->id],
			'orderBy' => 'order'
		]);

		$this->render('edit', [
			'slider' => $slider
		]);
	}

	public function updateAction()
	{
		$slider = $this->model->update();

		$upload = new MyUpload();
		if (count($_FILES['gallery']['name']) !== 0) {
			$upload->multiUpload(
				$slider,
				array(
					array('size' => 'big', 'image_resize' => true, 'image_ratio_crop' => true, 'image_x' => 800, 'image_y' => 533),
					array('size' => 'thumb', 'image_resize' => true, 'image_ratio_crop' => true, 'image_x' => 290, 'image_y' => 140)
				)
			);
		}

		$this->redirect('/admin/slider/edit/' . $slider->id);
	}

	public function deleteSliderAction()
	{
		$sliderId = $this->getPOST('sliderId');
		(new Slider)->delete($sliderId);
	}

	public function setImageToFrontAction()
	{
		$imgId = $this->getPOST('imageId');
		(new Image())->setFrontStatus($imgId);
	}

	public function deleteImageAction()
	{
		$sliderId = $this->getPOST('sliderId');
		$imgId = $this->getPOST('imageId');
		(new Image)->deleteImg($sliderId, $imgId);
	}

	public function setImagesOrderAction()
	{
		foreach ($_POST['order'] as $key => $pic) {
			$imageId = substr($pic, 4);
			$sql = "UPDATE image i SET i.order = $key WHERE i.id = $imageId";
			$this->model->oConnect->exec($sql);
		}
	}
}