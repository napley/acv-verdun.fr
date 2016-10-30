<?php

namespace MusicBox\Controller;

use Silex\Application;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

class PageController
{
    public function indexAction(Request $request, Application $app)
    {
        // Perform pagination logic.
        $limit = 4;
        $total = $app['repository.page']->getCount();
        $numPages = ceil($total / $limit);
        $currentPage = $request->query->get('page', 1);
        $currentPage = ($currentPage<1 ? 1 : $currentPage);
        $offset = ($currentPage - 1) * $limit;
        $pages = $app['repository.page']->findAll($limit, $offset);
        // Divide pages into groups of 4.
        $groupSize = 4;
        $progress = 0;
        while ($progress < $limit) {
            $groupedPages[] = array_slice($pages, $progress, $groupSize);
            $progress += $groupSize;
        }

        $data = array(
            'groupedPages' => $groupedPages,
            'currentPage' => $currentPage,
            'numPages' => $numPages,
            'here' => $app['url_generator']->generate('pages'),
        );
        return $app['twig']->render('pages.html.twig', $data);
    }

    public function viewAction(Request $request, Application $app)
    {
        $page = $request->attributes->get('nom_page');
        if (!$page) {
            $app->abort(404, 'The requested page was not found.');
        }

        $data = array(
            'page' => $page,
        );
        return $app['twig']->render('page.html.twig', $data);
    }
}
