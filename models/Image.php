<?php

namespace Models;

use Models\Image;

Class Image extends \Core\Crud
{
    public static function deleteImg($slider_id, $img_id)
    {
        $image = new Image();
        $pic = current($image->select(['where' => ['id' => $img_id]]));

        //delete pics from site folder
        $dirPath = SITE_IMG . 'gallery' . DS . $slider_id . DS;

        $thumb = $dirPath . $pic->thumb . '.jpg';
        $big = $dirPath . $pic->big . '.jpg';

        unlink($thumb);
        unlink($big);

        // delete image from bdd
        $sql = 'DELETE FROM image WHERE slider_id = ' . $slider_id . ' AND id = ' . $img_id;

        $image->oConnect->exec($sql);
    }
}