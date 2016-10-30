<?php

namespace MusicBox\Controller;

use Silex\Application;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

class ApiEntrainementController
{
    public function indexAction(Request $request, Application $app)
    {
        $limit = $request->query->get('limit', 10000000);
        $offset = $request->query->get('offset', 0);
        $entrainements = $app['repository.entrainement']->findAll($limit, $offset);
        $data = array();
        foreach ($entrainements as $entrainement) {
            $data[] = array(
                'id' => $entrainement->getId(),
                'title' => $entrainement->getTitle()
            );
        }

        return $app->json($data);
    }
}
