<?php

namespace Libs\Upload;

use Libs\Upload\UploadClass;

class MyUpload{

	private $aFile = array();
	private $oImg = "";
	private $sPath = "";

	/**
	 * [__construct parameters init
	 * @param object $oImg    	img infos from form
	 * @param array  $aSizes   	img sizes
	 */

	public function upload($object, $aOptions){

		if(!empty($object) && !empty($aOptions)){
			      
			$img = new UploadClass($_FILES['image']);

			//if album img selected
			if ($img->uploaded) {

				//create img id and path if create or update
				if(isset($object->lastInsert)){
					$oid = $object->lastInsert;
					$sPath = SITE_IMG.$object->class.DS.$object->lastInsert;
				}else{
					$oid = $object->id;
					$sPath = SITE_IMG.$object->class.DS.$object->id;
				}

				//del img dir if update context
   				if (isset($object->id)) {
   					//delete dir if exist
					$dirContent = scandir(SITE_IMG.$object->class.DS.$object->id);

					foreach ($dirContent as $fileName) {
						unlink(SITE_IMG.$object->class.DS.$object->id.DS.$fileName);
					}

					is_dir(SITE_IMG.$object->class.DS.$object->id)?rmdir(SITE_IMG.$object->class.DS.$object->id):false;
   				}

				//create array with upload options
				foreach ($aOptions as $option) {

					//set img options
					$img->file_new_name_body = $object->class.'_'.$option['size'];
					$img->image_convert = 'jpg';
					$img->image_resize = true;
					$img->auto_create_dir = true;
					foreach ($option as $key => $value) {
						if($key != 'size') {
							$img->{$key} = $value;
						}
					}

					//process modifications
					$img->Process($sPath);

					//request
					$sql = 'UPDATE '.$object->class.'
					SET '.$option['size'].' = "'.$img->file_dst_name_body.'"
					WHERE '.$object->id.' = '.$oid;

					//save in db
					$query = $object->oConnect->exec($sql);
				}
			}
		}
	}

	public function multiUpload($object, $aOptions){

		//Multi upload links with an album
		//set gallery folder
		$folder_name = 'gallery';
		$images = [];
		
		if (!empty($object) && !empty($aOptions)) {
			//build img arrays
			$imgsNb = count($_FILES['gallery']['name']);
			for ($i=0; $i < $imgsNb; $i++) {
				foreach ($_FILES['gallery'] as $fileParams => $values) {
					$images[$i][$fileParams] = array_shift($_FILES['gallery'][$fileParams]);
				}
			}

			//create img id and path
			//update or create context
			$oid = isset($object->id) ? $object->id : $object->lastInsert;
			$sPath = SITE_IMG.$folder_name.DS.$oid;

			//compute each img
			foreach ($images as $img) {

				$img = new UploadClass($img);

				if ($img->uploaded) {

					$queryValues = array();
					$aSizes = array();
					//create images
					foreach ($aOptions as $option) {

						//initialise img
						$img->file_new_name_body = str_replace(' ', '_', $img->file_src_name_body.'_'.$option['size']);
						$img->image_convert = 'jpg';
						$img->image_resize = true;
						$img->auto_create_dir = true;

						foreach ($option as $key => $value) {
							if($key != 'size') {
								$img->{$key} = $value;
							}
						}

						$img->Process($sPath);

						//stock img sizes & names
						$aSizes[] = $option['size'];
						$queryValues[] = '"'.$img->file_dst_name_body.'"';
					}

					//query
					$sql = 'INSERT INTO image ('.implode(', ', $aSizes).', '. $object->xxx_id .')
					VALUES ('.implode(', ', $queryValues).', "'. $oid .'")';

					//save in db			   	   				   	
					$query = $object->oConnect->exec($sql);
				}  
			}
		}	
	}   
}//end class
