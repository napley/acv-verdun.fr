<?php

namespace MusicBox\Extension;

/**
 * Provides a way to retrieve the Soundcloud oEmbed code.
 */
class InfoSite extends \Twig_Extension
{
    private $app;
    
    function __construct($app)
    {
        $this->app = $app;
    }
    
    public function getFunctions()
    {
         return array(
            'infosites' => new \Twig_Function_Method($this, 'infosites', array('is_safe' => array('html')))
        );
    }
    
    public function infosites($name)
    {
        $info = $this->app['repository.infosite']->findByNom($name);

        return $info->getValeur();
    }


    public function getName()
    {
        return "infosites";
    }

}
