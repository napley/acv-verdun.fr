<?php

namespace MusicBox\Controller;

use Silex\Application;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

class AlbumPhotoController
{

    public function indexAction(Request $request, Application $app)
    {
        $limit = 30;
        $currentYear = $request->query->get('annee', null);
        $currentPage = $request->query->get('page', 1);
        $currentPage = ($currentPage < 1 ? 1 : $currentPage);
        $offset = ($currentPage - 1) * $limit;
        $newestOrderBy = array('created_at' => 'DESC');
        if (!empty($currentYear)) {
            $newestAlbumPhotos = $app['repository.albumPhoto']->findByYear($currentYear, $limit, $offset, $newestOrderBy);
            $total = $app['repository.albumPhoto']->getCountYear($currentYear);
        } else {
            $newestAlbumPhotos = $app['repository.albumPhoto']->findAll($limit, $offset, $newestOrderBy);
            $total = $app['repository.albumPhoto']->getCount();
        }

        $numPages = ceil($total / $limit);

        // Divide artists into groups of 2.
        $groupSize = 2;
        $groupedNewestAlbumPhotos = array();
        $progress = 0;
        while ($progress < $limit) {
            $groupedNewestAlbumPhotos[] = array_slice($newestAlbumPhotos, $progress, $groupSize);
            $progress += $groupSize;
        }

        $listeAnnee = [];
        for ($year = date('Y'); $year > 2009; $year--) {
            $listeAnnee[] = $year;
        }


        $data = array(
            'groupedNewestAlbumPhotos' => $groupedNewestAlbumPhotos,
            'currentPage' => $currentPage,
            'numPages' => $numPages,
            'here' => $app['url_generator']->generate('albumPhotos'),
            'filtreAnnee' => $listeAnnee,
            'currentYear' => $currentYear
        );
        return $app['twig']->render('albumPhotos.html.twig', $data);
    }

    public function viewAction(Request $request, Application $app)
    {
        $albumPhoto = $request->attributes->get('albumPhoto');
        if (!$albumPhoto) {
            $app->abort(404, 'The requested albumPhoto was not found.');
        }

        $data = array(
            'albumPhoto' => $albumPhoto,
        );
        return $app['twig']->render('albumPhoto.html.twig', $data);
    }

}
