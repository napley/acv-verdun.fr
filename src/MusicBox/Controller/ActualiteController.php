<?php

namespace MusicBox\Controller;

use Silex\Application;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

class ActualiteController
{
    public function indexAction(Request $request, Application $app)
    {
        // Perform pagination logic.
        $limit = 10;
        $total = $app['repository.actualite']->getCountAffiched();
        $numPages = ceil($total / $limit);
        $currentPage = $request->query->get('page', 1);
        $currentPage = ($currentPage<1 ? 1 : $currentPage);
        $offset = ($currentPage - 1) * $limit;
        $actualites = $app['repository.actualite']->findAllAffiched($limit, $offset);
        // Divide actualites into groups of 4.
        $groupSize = 4;
        $progress = 0;
        while ($progress < $limit) {
            $groupedActualites[] = array_slice($actualites, $progress, $groupSize);
            $progress += $groupSize;
        }

        $data = array(
            'groupedActualites' => $groupedActualites,
            'currentPage' => $currentPage,
            'numPages' => $numPages,
            'here' => $app['url_generator']->generate('actualites'),
        );
        return $app['twig']->render('actualites.html.twig', $data);
    }

    public function viewAction(Request $request, Application $app)
    {
        $actualite = $request->attributes->get('actualite');
        if (!$actualite) {
            $app->abort(404, 'The requested actualite was not found.');
        }

        $data = array(
            'actualite' => $actualite,
        );
        return $app['twig']->render('actualite.html.twig', $data);
    }
}
