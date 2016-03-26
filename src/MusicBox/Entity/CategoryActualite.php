<?php

namespace MusicBox\Entity;

class CategoryActualite
{
    /**
     * CategoryActualite id.
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
     * Actualite.
     *
     *  @var \MusicBox\Entity\Actualite
     */
    protected $actualite;
    
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

    public function getActualite()
    {
        return $this->actualite;
    }

    public function setActualite($actualite)
    {
        $this->actualite = $actualite;
    }

}
