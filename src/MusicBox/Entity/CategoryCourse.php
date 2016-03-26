<?php

namespace MusicBox\Entity;

class CategoryCourse
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
     *  @var \MusicBox\Entity\Course
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

    public function getCourse()
    {
        return $this->course;
    }

    public function setCourse($course)
    {
        $this->course = $course;
    }

}
