<?php

namespace Models;

Class Image extends \Core\Crud
{
    public static function deleteImg($sliderId, $imgId)
    {
        $image = new Image();
        $pic = $image->getOne(['id' => $imgId]);

        //delete pics from site folder
        $dirPath = SITE_IMG . 'gallery' . DS . $sliderId . DS;

        $thumb = $dirPath . $pic->thumb . '.jpg';
        $big = $dirPath . $pic->big . '.jpg';

        unlink($thumb);
        unlink($big);

        // delete image from bdd
        $sql = 'DELETE FROM image WHERE slider_id = ' . $sliderId . ' AND id = ' . $imgId;

        $image->oConnect->exec($sql);
    }

    public function setFrontStatus($imgId)
    {
        $image = new Image();
        $pic = $image->getOne(['id' => $imgId]);

        $front = $pic->front ? 0 : 1;

        $image->update([
            'id' => $imgId,
            'front' => $front
        ]);
        
    }
}