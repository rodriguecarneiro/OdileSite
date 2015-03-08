<?php

namespace Models;

use PDO;

Class Slider extends \Core\Crud
{
    public function get_thumb_picture($slider_id = 0)
    {
        $sql = 'SELECT thumb FROM image WHERE slider_id = ' . $slider_id . ' ORDER BY `order` LIMIT 1';
        $query = $this->oConnect->query($sql);

        return $query->fetch(PDO::FETCH_ASSOC)['thumb'];
    }

    public function delete($slider_id = 0)
    {
        $delPics = 'DELETE FROM image WHERE slider_id = ' . $slider_id . '';
        $delAlbum = 'DELETE FROM slider WHERE id = ' . $slider_id . '';

        //delete pics from site folder
        $dirPath = SITE_IMG . 'gallery' . DS . $slider_id . DS;
        $pics = array_splice(scandir($dirPath), 2);
        foreach ($pics as $pic) {
            unlink($dirPath . $pic);
        }

        //delete folder when empty
        rmdir($dirPath);

        //delete from db
        $this->oConnect->exec($delPics);
        $this->oConnect->exec($delAlbum);
    }
}