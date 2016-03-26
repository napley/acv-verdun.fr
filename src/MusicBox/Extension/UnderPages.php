<?php

namespace MusicBox\Extension;

/**
 * Provides a way to retrieve the Soundcloud oEmbed code.
 */
class UnderPages extends \Twig_Extension
{
    private $app;
    
    function __construct($app)
    {
        $this->app = $app;
    }
    
    public function getFunctions()
    {
         return array(
            'underpages' => new \Twig_Function_Method($this, 'underPages', array('is_safe' => array('html')))
        );
    }
    
    public function underPages($page)
    {
        $underpages = [];
        if ($page['type_page'] != 3) {
            switch ($page['type_page']) {
                case 1:
                    $page = $this->app['repository.page']->find($page['id']);
                    $underpages = $this->app['repository.underPage']->findByPageRank($page);

                    break;
                case 2:
                    $category = $this->app['repository.category']->find($page['id']);
                    $underpages = $this->app['repository.underPage']->findByCatRank($category);

                    break;

                default:
                    break;
            }
        }
        
        return $underpages;
    }


    public function getName()
    {
        return "underpages";
    }

}
