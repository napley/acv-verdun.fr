<?php

namespace MusicBox\Extension;

/**
 * Provides a way to retrieve the Soundcloud oEmbed code.
 */
class OrderPage extends \Twig_Extension
{
    private $app;
    
    function __construct($app)
    {
        $this->app = $app;
    }
    
    public function getGlobals()
    {
        $pagesOrder = $this->app['repository.page']->getRankPage();

        
        return array(
            'orderpage' => $pagesOrder,
        );
    }

    public function getName()
    {
        return "orderpage";
    }

}