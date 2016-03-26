<?php

namespace MusicBox\Entity;

class Actualite
{
    /**
     * Article id.
     *
     * @var integer
     */
    protected $id;
    
    /**
     * Article affichage.
     *
     * @var boolean
     */
    protected $affichage;


    /**
     * User.
     *
     *  @var \MusicBox\Entity\User
     */
    protected $user;

    /**
     * Article title.
     *
     * @var string
     */
    protected $title;

    /**
     * Article description.
     *
     * @var string
     */
    protected $description;

    /**
     * Article contenu.
     *
     * @var string
     */
    protected $contenu;

    /**
     * texte contenu.
     *
     */
    protected $texte_description;

    /**
     * texte contenu.
     *
     */
    protected $texte_contenu;

    /**
     * Liste des category de la  course
     *
     */
    protected $categories;

    /**
     * When the actualite entity was created.
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
    
    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user)
    {
        $this->user = $user;
    }

    public function getAffichage()
    {
        return $this->affichage;
    }

    public function setAffichage($affichage)
    {
        $this->affichage = $affichage;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getContenu()
    {
        return $this->contenu;
    }

    public function setContenu($contenu)
    {
        $this->contenu = $contenu;
    }

    public function getTexteDescription()
    {
        return $this->texte_description;
    }

    public function setTexteDescription($texte_description)
    {
        $this->texte_description = $texte_description;
    }

    public function getTexteContenu()
    {
        return $this->texte_contenu;
    }

    public function setTexteContenu($texte_contenu)
    {
        $this->texte_contenu = $texte_contenu;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
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
