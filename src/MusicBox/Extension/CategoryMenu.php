<?php

namespace MusicBox\Extension;

/**
 * Provides a way to retrieve the Soundcloud oEmbed code.
 */
class CategoryMenu extends \Twig_Extension
{
    private $app;
    
    function __construct($app)
    {
        $this->app = $app;
    }
    
    public function getGlobals()
    {
        $categories = $this->app['repository.category']->findAll();

        
        return array(
            'categorymenu' => $categories,
        );
    }

    public function getName()
    {
        return "categorymenu";
    }

}
