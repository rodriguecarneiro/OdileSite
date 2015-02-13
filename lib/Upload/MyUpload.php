<?php

require_once 'class.upload.php';

class MyUpload{

	private $aFile = array();
	private $oImg = "";
	private $sPath = "";

	/**
	 * [__construct parameters init
	 * @param object $oImg    	img infos from form
	 * @param array  $aSizes   	img sizes
	 */

	public function upload($oImg, $aOptions){

		if(!empty($oImg) && !empty($aOptions)){				    	 
			      
			$img = new upload($_FILES['img']);

			//if album img selected
			if ($img->uploaded) {

				//create img id and path if create or update
				if(isset($oImg->lastInsert)){
					$oid = $oImg->lastInsert;
					$sPath = SITE_IMG.$oImg->class.DS.$oImg->lastInsert;
				}else{
					$oid = $oImg->{$oImg->xxx_id};
					$sPath = SITE_IMG.$oImg->class.DS.$oImg->{$oImg->xxx_id};
				}
				
				//del img dir if update context
   				if (isset($oImg->{$oImg->xxx_id})) {
   					//delete dir if exist
					$dirContent = scandir(SITE_IMG.$oImg->class.DS.$oImg->{$oImg->xxx_id});
					
					foreach ($dirContent as $fileName) {
						unlink(SITE_IMG.$oImg->class.DS.$oImg->{$oImg->xxx_id}.DS.$fileName);	
					}
					   
					is_dir(SITE_IMG.$oImg->class.DS.$oImg->{$oImg->xxx_id})?rmdir(SITE_IMG.$oImg->class.DS.$oImg->{$oImg->xxx_id}):false;	 		
   				}

				//create array with upload options
				foreach ($aOptions as $option) {
					
					//set img options
					$img->file_new_name_body = $oImg->class.'_'.$option['size'];
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
					$sql = 'UPDATE '.$oImg->class.'
					SET '.$option['size'].' = "'.$img->file_dst_name_body.'"
					WHERE '.$oImg->xxx_id.' = '.$oid;					   

					//save in db			   	   				   	
					$query = $oImg->oConnect->exec($sql);		   
				} 	
			}	   
		}	
	}

	public function multiUpload($oImg, $aOptions){
		//Multi upload links with an album
		//set gallery folder
		$folder_name = 'gallery';

		if (!empty($oImg) && !empty($aOptions)) {
			//build img arrays
			$imgsNb = count($_FILES['gallery']['name']);
			for ($i=0; $i < $imgsNb; $i++) { 
				foreach ($_FILES['gallery'] as $fileParams => $values) {
					$tab[$i][$fileParams] = array_shift($_FILES['gallery'][$fileParams]);
				}
			}
			//create img id and path
			//update or create context
			$oid = isset($oImg->{$oImg->xxx_id}) ? $oImg->{$oImg->xxx_id} : $oImg->lastInsert ;
			$sPath = SITE_IMG.$folder_name.DS.$oid;

			//compute each img
			foreach ($tab as $img) {

				$img = new upload($img);

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
						$aSizes[] = '`'.$option['size'].'`';
						$queryValues[] = '"'.$img->file_dst_name_body.'"';
					} 	

					//query 
					$sql = 'INSERT INTO image ('.implode(', ', $aSizes).', `'. $oImg->xxx_id .'`)
					VALUES ('.implode(', ', $queryValues).', "'. $oid .'")';						   	

					//save in db			   	   				   	
					$query = $oImg->oConnect->exec($sql);		   
				}  
			}
		}	
	}   
}//end class
