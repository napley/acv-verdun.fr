<?php

namespace MusicBox\Entity;

class Widget
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
     * Entete.
     *
     * @var string
     */
    protected $entete;

    /**
     * Contenu.
     *
     * @var string
     */
    protected $contenu;

    /**
     * Rank.
     *
     * @var int
     */
    protected $rank;
    
    /**
     * onPages id.
     *
     * @var boolean
     */
    protected $onPages;
    
    /**
     * onCats id.
     *
     * @var boolean
     */
    protected $onCats;
    
    /**
     * locked id.
     *
     * @var boolean
     */
    protected $locked;


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
    public function getEntete()
    {
        return $this->entete;
    }

    public function setEntete($entete)
    {
        $this->entete = $entete;
    }

    /**
     * @var string
     */
    public function getContenu()
    {
        return $this->contenu;
    }

    public function setContenu($contenu)
    {
        $this->contenu = $contenu;
    }

    /**
     * @var int
     */
    public function getRank()
    {
        return $this->rank;
    }

    public function setRank($rank)
    {
        $this->rank = $rank;
    }

    /**
     * @var boolean
     */
    public function getOnPages()
    {
        return $this->onPages;
    }

    public function setOnPages($onPages)
    {
        $this->onPages = $onPages;
    }

    /**
     * @var boolean
     */
    public function getOnCats()
    {
        return $this->onCats;
    }

    public function setOnCats($onCats)
    {
        $this->onCats = $onCats;
    }

    public function getCategories()
    {
        return $this->categories;
    }

    public function setCategories($categories)
    {
        $this->categories = $categories;
    }
    
    public function getLocked()
    {
        return $this->locked;
    }

    public function setLocked($locked)
    {
        $this->locked = $locked;
    }
}
