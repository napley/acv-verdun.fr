<?php

namespace MusicBox\Extension;

/**
 * Provides a way to retrieve the Soundcloud oEmbed code.
 */
class LastAlbum extends \Twig_Extension
{
    private $app;
    
    function __construct($app)
    {
        $this->app = $app;
    }
    
    public function getGlobals()
    {
        $albumPhotos = $this->app['repository.albumPhoto']->findAll(3);

        
        return array(
            'lastalbums' => $albumPhotos,
        );
    }

    public function getName()
    {
        return "lastalbums";
    }

}
