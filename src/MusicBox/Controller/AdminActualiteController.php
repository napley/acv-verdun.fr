<?php

namespace MusicBox\Controller;

use MusicBox\Entity\Actualite;
use MusicBox\Form\Type\ActualiteType;
use Silex\Application;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

class AdminActualiteController
{
    public function indexAction(Request $request, Application $app)
    {
        // Perform pagination logic.
        $limit = 10;
        $total = $app['repository.actualite']->getCount();
        $numPages = ceil($total / $limit);
        $currentPage = $request->query->get('page', 1);
        $currentPage = ($currentPage<1 ? 1 : $currentPage);
        $offset = ($currentPage - 1) * $limit;
        $actualites = $app['repository.actualite']->findAll($limit, $offset);

        $data = array(
            'actualites' => $actualites,
            'currentPage' => $currentPage,
            'numPages' => $numPages,
            'here' => $app['url_generator']->generate('admin_actualites'),
        );
        return $app['twig']->render('admin_actualites.html.twig', $data);
    }

    public function addAction(Request $request, Application $app)
    {
        $actualite = new Actualite();
        $token = $app['security']->getToken();
        $user = $token->getUser();
        $actualite->setUser($user);
        $form = $app['form.factory']->create(new ActualiteType(), $actualite);
        
        //Récupération des catégories
        $arrayPreselection = [];
        $categories = $app['repository.category']->findAll();
        foreach ($categories as $category) {
            $arrayPreselection[]['category'] = $category;
        }  
        
        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $app['repository.actualite']->save($actualite);
                $app['repository.categoryActualite']->clean($request->request->get('categories'), $actualite->getId());
                $message = 'L\'article ' . $actualite->getTitle() . ' a été enregistré.';
                $app['session']->getFlashBag()->add('success', $message);
                // Redirect to the edit page.
                $redirect = $app['url_generator']->generate('admin_actualite_edit', array('actualite' => $actualite->getId()));
                return $app->redirect($redirect);
            }
        }

        $data = array(
            'form' => $form->createView(),
            'title' => "Ajout d'un nouveau article",
            'link' => 'admin_actualites',
            'catActPres' => $arrayPreselection
        );
        return $app['twig']->render('form_actualite.html.twig', $data);
    }

    public function editAction(Request $request, Application $app)
    {
        $actualite = $request->attributes->get('actualite');
        if (!$actualite) {
            $app->abort(404, 'The requested article was not found.');
        }
        $form = $app['form.factory']->create(new ActualiteType(), $actualite);

        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $app['repository.actualite']->save($actualite);
                $app['repository.categoryActualite']->clean($request->request->get('categories'), $actualite->getId());
                $message = 'L\'article ' . $actualite->getTitle() . ' a été enregistré.';
                $app['session']->getFlashBag()->add('success', $message);
            }
        }

        //Récupération des catégories et préselection
        $categories = $app['repository.category']->findAll();
        $arrayPreselection = [];
        $i = 0;
        foreach ($categories as $category) {
            $arrayPreselection[$i]['category'] = $category;
            $categoryActualite = $app['repository.categoryActualite']->findByCategoryAndActualite($category->getId(), $actualite->getId());
            if ($categoryActualite != NULL) {
                $arrayPreselection[$i]['categoryActualite'] = $categoryActualite;
            }
            $i++;
        }
        
        $data = array(
            'form' => $form->createView(),
            'title' => "Modification de l'article : " . $actualite->getTitle(),
            'link' => 'admin_actualites',
            'catActPres' => $arrayPreselection
        );
        return $app['twig']->render('form_actualite.html.twig', $data);
    }

    public function deleteAction(Request $request, Application $app)
    {
        $actualite = $request->attributes->get('actualite');
        if (!$actualite) {
            $app->abort(404, 'The requested article was not found.');
        }

        $app['repository.actualite']->delete($actualite);
        return $app->redirect($app['url_generator']->generate('admin_actualites'));
    }
}
