<?php

namespace MusicBox\Extension;

/**
 * Provides a way to retrieve the Soundcloud oEmbed code.
 */
class Pages extends \Twig_Extension
{
    private $app;
    
    function __construct($app)
    {
        $this->app = $app;
    }
    
    public function getGlobals()
    {
        $pages = $this->app['repository.page']->findAll(999);

        
        return array(
            'pages' => $pages,
        );
    }

    public function getName()
    {
        return "pages";
    }

}