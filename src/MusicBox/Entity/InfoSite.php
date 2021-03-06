<?php

namespace MusicBox\Entity;

class InfoSite
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
    protected $nom;

    /**
     * Title.
     *
     * @var string
     */
    protected $description;
    
    /**
     * Abrégé.
     *
     * @var string
     */
    protected $valeur;
    
    /**
     * type image.
     *
     * @var boolean
     */
    protected $img;



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
    public function getNom()
    {
        return $this->nom;
    }

    public function setNom($nom)
    {
        $this->nom = $nom;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @var string
     */
    public function getValeur()
    {
        return $this->valeur;
    }

    public function setValeur($valeur)
    {
        $this->valeur = $valeur;
    }

    /**
     * @var boolean
     */
    public function getImg()
    {
        if ($this->img == 0) {
            return false;
        } else {
            return true; 
        }
    }

    public function setImg($img)
    {
        
        if ($img == false) {
            $this->img = 0;
        } else {
            $this->img = 1;
        }
    }

}
