<?php

namespace MusicBox\Extension;

/**
 * Provides a way to retrieve the Soundcloud oEmbed code.
 */
class Widget extends \Twig_Extension
{
    private $app;
    
    function __construct($app)
    {
        $this->app = $app;
    }
    
    public function getFunctions()
    {
         return array(
            'widgets' => new \Twig_Function_Method($this, 'widgets', array('is_safe' => array('html')))
        );
    }
    
    public function widgets($name)
    {
        $codeHtml = $this->app['repository.widget']->getWidgetCode($name);

        return $codeHtml;
    }


    public function getName()
    {
        return "widgets";
    }

}
