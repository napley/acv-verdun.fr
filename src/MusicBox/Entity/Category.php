<?php

namespace MusicBox\Entity;

class Category
{
    /**
     * User id.
     *
     * @var integer
     */
    protected $id;

    /**
     * Title.
     *
     * @var string
     */
    protected $title;
    
    /**
     * Abrégé.
     *
     * @var string
     */
    protected $abrev;


    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @var string
     */
    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @var string
     */
    public function getAbrev()
    {
        return $this->abrev;
    }

    public function setAbrev($abrev)
    {
        $this->abrev = $abrev;
    }

}
