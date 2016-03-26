<?php

namespace MusicBox\Controller;

use Silex\Application;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class CategoryController
{
    public function indexAction(Request $request, Application $app)
    {
        $category = $request->attributes->get('abrev_cat');
        
        $limit = 10;
        $total = $app['repository.actualite']->getCountAffichedByCategory($category->getId());
        $numPages = ceil($total / $limit);
        $currentPage = $request->query->get('page', 1);
        $currentPage = ($currentPage<1 ? 1 : $currentPage);
        $offset = ($currentPage - 1) * $limit;
        
        $newestOrderBy = array('created_at' => 'DESC');
        $newestActualites = $app['repository.actualite']->findAllAffichedByCategory(array('category_id' => $category->getId()), $limit, $offset, $newestOrderBy);

        $data = array(
            'groupedNewestActualites' => $newestActualites,
            'currentPage' => $currentPage,
            'numPages' => $numPages,
            'here' => $app['url_generator']->generate('cat', array('abrev_cat' => $category->getAbrev())),
            'category' => $category
        );
        return $app['twig']->render('cat.html.twig', $data);
    }
    
}
