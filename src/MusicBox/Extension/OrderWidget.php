<?php

namespace MusicBox\Extension;

/**
 * Provides a way to retrieve the Soundcloud oEmbed code.
 */
class OrderWidget extends \Twig_Extension
{
    private $app;
    
    function __construct($app)
    {
        $this->app = $app;
    }
    
    public function getFunctions()
    {
         return array(
            'orderwidgets' => new \Twig_Function_Method($this, 'widgets', array('is_safe' => array('html')))
        );
    }
    
    public function widgets($params)
    {
        $orderwidgets = $this->app['repository.widget']->findAllOrderWidget($params);

        return $orderwidgets;
    }


    public function getName()
    {
        return "orderwidgets";
    }

}
