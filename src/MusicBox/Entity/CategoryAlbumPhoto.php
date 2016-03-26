<?php

namespace MusicBox\Entity;

class CategoryAlbumPhoto
{
    /**
     * CategoryCourse id.
     *
     * @var integer
     */
    protected $id;
    
    /**
     * Category.
     *
     * @var \MusicBox\Entity\Category
     */
    protected $category;

    /**
     * Course.
     *
     *  @var \MusicBox\Entity\AlbumPhoto
     */
    protected $albumPhoto;
    
    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function setCategory($category)
    {
        $this->category = $category;
    }

    public function getAlbumPhoto()
    {
        return $this->albumPhoto;
    }

    public function setAlbumPhoto($albumPhoto)
    {
        $this->albumPhoto = $albumPhoto;
    }

}
