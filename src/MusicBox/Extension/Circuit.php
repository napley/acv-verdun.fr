<?php

namespace MusicBox\Extension;

/**
 * Provides a way to retrieve the Soundcloud oEmbed code.
 */
class Circuit extends \Twig_Extension
{
    private $app;
    
    function __construct($app)
    {
        $this->app = $app;
    }
    
    public function getFilters() {
        return array(
            "circuitFilter" => new \Twig_Filter_Method($this, "replaceCircuit"),
        );
    }

    public function replaceCircuit($input) {
        
        $matchs = null;
        $listMatch = array();
        $nbcorrespond = preg_match_all("/.*##(.*)##.*/", $input, $matchs);
        if ($nbcorrespond > 0){
            foreach ($matchs[1] as $match) {
                $listMatch['##'. $match .'##'] = trim($match);
            }
        }
        
        foreach ($listMatch as $nomMatch => $match) {
            $idMatch = str_replace('entrainement ', '', $match);
            $entrainement = $this->app['repository.entrainement']->find($idMatch);
            if (!empty($entrainement)) {
                $listMatch[$nomMatch] = $entrainement->getScript();
            } else {
                $listMatch[$nomMatch] = '## entrainement ' . $idMatch . ' ##';
            }
        }
        
        return str_replace(array_keys($listMatch), array_values($listMatch), $input);
    }

    public function getName()
    {
        return "circuitFilter";
    }

}
