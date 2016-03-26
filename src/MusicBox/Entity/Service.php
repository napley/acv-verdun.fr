<?php

namespace MusicBox\Entity;

class Service
{
    /**
     * Service id.
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
    protected $description;
    
    /**
     * Abrégé.
     *
     * @var string
     */
    protected $utilisateur;
    
    /**
     * Abrégé.
     *
     * @var string
     */
    protected $password;


    function getId()
    {
        return $this->id;
    }

    function getTitle()
    {
        return $this->title;
    }

    function getDescription()
    {
        return $this->description;
    }

    function getUtilisateur()
    {
        return $this->utilisateur;
    }

    function getPassword()
    {
        return $this->password;
    }

    function setId($id)
    {
        $this->id = $id;
    }

    function setTitle($title)
    {
        $this->title = $title;
    }

    function setDescription($description)
    {
        $this->description = $description;
    }

    function setUtilisateur($utilisateur)
    {
        $this->utilisateur = $utilisateur;
    }

    function setPassword($password)
    {
        $this->password = $password;
    }

}
