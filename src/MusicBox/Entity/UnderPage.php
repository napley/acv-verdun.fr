<?php

namespace MusicBox\Entity;

class UnderPage
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
     * Description.
     *
     * @var string
     */
    protected $link;
    
    /**
     * Page.
     *
     * @var \MusicBox\Entity\Page
     */
    protected $page;
    
    /**
     * rank.
     *
     * @var integer
     */
    protected $rank;

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
    public function getLink()
    {
        return $this->link;
    }

    public function setLink($link)
    {
        $this->link = $link;
    }

    /**
     * 
     * @return \MusicBox\Entity\Page
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * 
     * @param \MusicBox\Entity\Page $page
     */
    public function setPage($page)
    {
        $this->page = $page;
    }

    public function getRank()
    {
        return $this->rank;
    }

    public function setRank($rank)
    {
        $this->rank = $rank;
    }

}
