<?php

namespace MusicBox\Controller;

use Silex\Application;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class IndexController
{
    public function indexAction(Request $request, Application $app)
    {
        $limit = 10;
        $total = $app['repository.actualite']->getCount();
        $numPages = ceil($total / $limit);
        $currentPage = $request->query->get('page', 1);
        $currentPage = ($currentPage<1 ? 1 : $currentPage);
        $offset = ($currentPage - 1) * $limit;
        
        $newestOrderBy = array('created_at' => 'DESC');
        $newestActualites = $app['repository.actualite']->findAllAffiched(null, $limit, $offset, $newestOrderBy);

        $data = array(
            'groupedNewestActualites' => $newestActualites,
            'currentPage' => $currentPage,
            'numPages' => $numPages,
            'here' => $app['url_generator']->generate('homepage'),
        );
        return $app['twig']->render('index.html.twig', $data);
    }
    
    public function indexCatAction(Request $request, Application $app)
    {
        $limit = 1;
        $total = $app['repository.actualite']->getCount();
        $numPages = ceil($total / $limit);
        $currentPage = $request->attributes->get('num_page');
        $currentPage = ($currentPage<1 ? 1 : $currentPage);
        $offset = ($currentPage - 1) * $limit;
        
        $category = $request->attributes->get('abrev_cat');
        $conditions = [];
        if (!empty($category)) {
            $conditions['category_id'] = $category->getId();
            $newestOrderBy = array('created_at' => 'DESC');
            $newestActualites = $app['repository.actualite']->findAllAffichedByCategory($conditions, $limit, $offset, $newestOrderBy);
        } else {
            $newestOrderBy = array('created_at' => 'DESC');
            $newestActualites = $app['repository.actualite']->findAllAffiched(null, $limit, $offset, $newestOrderBy);
        }
        
        $paramRoute['abrev_cat'] = $category->getAbrev();
        var_dump($paramRoute);

        $data = array(
            'groupedNewestActualites' => $newestActualites,
            'currentPage' => $currentPage,
            'numPages' => $numPages,
            'here' => $app['url_generator']->generate('cat_article'),
            'nomRoute' => 'cat_article',
            'paramRoute' => $paramRoute
        );
        return $app['twig']->render('index.html.twig', $data);
    }
}
