<?php

namespace MusicBox\Extension;

/**
 * Provides a way to retrieve the Soundcloud oEmbed code.
 */
class Course extends \Twig_Extension
{
    private $app;
    
    function __construct($app)
    {
        $this->app = $app;
    }
    
    public function getGlobals()
    {
//        var_dump($param);
//        exit;
        $courses = $this->app['repository.course'];

        
        return array(
            'courses' => $courses,
        );
    }
    
//    public function getFunctions()
//    {
//         return array(
//            'courses' => new \Twig_Function_Method($this, 'courses', array('is_safe' => array('html')))
//        );
//    }
    
//    public function courses($param)
//    {
//        $courses = $this->app['repository.course'];
//
//        
//        return array(
//            'courses' => $courses,
//        );
//    }


    public function getName()
    {
        return "courses";
    }

}
