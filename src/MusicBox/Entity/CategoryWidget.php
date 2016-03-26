<?php

namespace MusicBox\Entity;

class CategoryWidget
{
    /**
     * CategoryWidget id.
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
     * Widget.
     *
     *  @var \MusicBox\Entity\Widget
     */
    protected $course;
    
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

    public function getWidget()
    {
        return $this->widget;
    }

    public function setWidget($widget)
    {
        $this->widget = $widget;
    }

}
