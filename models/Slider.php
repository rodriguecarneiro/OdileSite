<?php

Class Slider extends \Core\Crud
{
    public function get_thumb_picture($slider_id = 0)
    {
        $sql = 'SELECT thumb FROM image WHERE slider_id = ' . $slider_id . ' ORDER BY id LIMIT 1';
        $query = $this->oConnect->query($sql);
        return current($query->fetch(PDO::FETCH_ASSOC));
    }
}