<?php

namespace MusicBox\Entity;

class Course
{
    /**
     * User id.
     *
     * @var integer
     */
    protected $id;

    /**
     * Username.
     *
     * @var string
     */
    protected $title;

    /**
     * Name.
     *
     * @var string
     */
    protected $link;


    /**
     * When the course start.
     *
     * @var DateTime
     */
    protected $startAt;


    /**
     * Liste des category de la  course
     *
     */
    protected $categories;

    /**
     * When the course end.
     *
     * @var DateTime
     */
    protected $endAt;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }
    
    function getLink()
    {
        return $this->link;
    }

    function setLink($link)
    {
        $this->link = $link;
    }

        public function getStartAt()
    {
        return $this->startAt;
    }

    public function setStartAt(\DateTime $startAt)
    {
        $this->startAt = $startAt;
    }

    public function getEndAt()
    {
        return $this->endAt;
    }

    public function setEndAt(\DateTime $endAt)
    {
        $this->endAt = $endAt;
    }

    public function getCategories()
    {
        return $this->categories;
    }

    public function setCategories($categories)
    {
        $this->categories = $categories;
    }

}
