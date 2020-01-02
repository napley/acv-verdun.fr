<?php

namespace MusicBox\Entity;

class AlbumPhoto
{
    /**
     * User id.
     *
     * @var integer
     */
    protected $id;

    /**
     * Intitule.
     *
     * @var string
     */
    protected $title;

    /**
     * Description.
     *
     * @var string
     */
    protected $description;

    /**
     * When the course start.
     *
     * @var DateTime
     */
    protected $createdAt;

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
    
    function getDescription()
    {
        return $this->description;
    }

    function setDescription($description)
    {
        $this->description = $description;
    }

        public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }
    
    public function getFirstPhoto()
    {
        $preDir = __DIR__."/../../../../update/web";
        $firstFile = '';
        $dir = "/server/php/files/" . $this->id . '/thumbnail';
        $files = scandir($preDir . $dir);
        if (empty($files)) {
            $dir = "/server/php/files/" . $this->id;
            $files = scandir($dir);
        }
        
        foreach ($files as $file) {
            if (!empty($file) && $file != '.' && $file != '..') {
                $firstFile = $file;
                break;
            }
        }
        
        if (empty($firstFile)) {
           $firstFile = '/img/acv.jpg'; 
        } else {
           $firstFile = $dir.'/'.$firstFile; 
        }
        
        return $firstFile;
    }

    public function getThumbnails()
    { 
    }

    public function getPhotos()
    {
        $preDir = __DIR__."/../../../../update/web";
        $dirThumbnail = "/server/php/files/" . $this->id . '/thumbnail';
        $dir = "/server/php/files/" . $this->id;
        
        $files = scandir($preDir.$dir);
        
        $tabPhotos = [];
        foreach ($files as $file) {
            if (!empty($file) && $file != '.' && $file != '..' && $file != 'thumbnail') {
                if (file_exists($preDir.$dirThumbnail."/".$file)) {
                    $tabPhotos[] = ['thumbnail' => $dirThumbnail."/".$file, 'file' => $dir."/".$file];
                } else {
                    $tabPhotos[] = ['thumbnail' => $dir."/".$file, 'file' => $dir."/".$file];
                }
            }
        }
        
        return $tabPhotos;
    }

    public function getNbPhotos()
    {
        $preDir = __DIR__."/../../../../update/web";
        $dirThumbnail = "/server/php/files/" . $this->id . '/thumbnail';
        $dir = "/server/php/files/" . $this->id;
        
        $files = scandir($preDir.$dir);
        
        $nb = 0;
        foreach ($files as $file) {
            if (!empty($file) && $file != '.' && $file != '..') {
                $nb++;
            }
        }
        
        return $nb;
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
