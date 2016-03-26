<?php

namespace MusicBox\Controller;

use MusicBox\Entity\Page;
use MusicBox\Form\Type\PageType;
use Silex\Application;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

class AdminPageController
{
    public function indexAction(Request $request, Application $app)
    {
        // Perform pagination logic.
        $limit = 15;
        $total = $app['repository.page']->getCount();
        $numPages = ceil($total / $limit);
        $currentPage = $request->query->get('page', 1);
        $currentPage = ($currentPage<1 ? 1 : $currentPage);
        $offset = ($currentPage - 1) * $limit;
        $pages = $app['repository.page']->findAll($limit, $offset);

        $data = array(
            'pages' => $pages,
            'currentPage' => $currentPage,
            'numPages' => $numPages,
            'here' => $app['url_generator']->generate('admin_pages'),
        );
        return $app['twig']->render('admin_pages.html.twig', $data);
    }

    public function addAction(Request $request, Application $app)
    {
        $page = new Page();
        $form = $app['form.factory']->create(new PageType(), $page);
        
        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $app['repository.page']->save($page);
                $app['repository.page']->saveRankUnderPages($request->request->get('underPages'), $page);
                $message = 'La page ' . $page->getTitle() . ' a été enregistré.';
                $app['session']->getFlashBag()->add('success', $message);
                // Redirect to the edit page.
                $redirect = $app['url_generator']->generate('admin_page_edit', array('page' => $page->getId()));
                return $app->redirect($redirect);
            }
        }

        $data = array(
            'underPages' => array(),
            'form' => $form->createView(),
            'title' => 'Ajout d\'une nouvelle page',
            'link' => 'admin_pages'
        );
        return $app['twig']->render('form_page.html.twig', $data);
    }

    public function editAction(Request $request, Application $app)
    {
        $page = $request->attributes->get('page');        
        if (!$page) {
            $app->abort(404, 'The requested page was not found.');
        }
        $form = $app['form.factory']->create(new PageType(), $page);

        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $app['repository.page']->save($page);
                $app['repository.page']->saveRankUnderPages($request->request->get('underPages'), $page);
                $message = 'La page ' . $page->getTitle() . ' a été enregistré.';
                $app['session']->getFlashBag()->add('success', $message);
            }
        }
        
        $underPages = $app['repository.page']->getRankUnderPages($page);

        $data = array(
            'underPages' => $underPages,
            'form' => $form->createView(),
            'title' => 'Modification de la page : ' . $page->getTitle(),
            'link' => 'admin_pages'
        );
        return $app['twig']->render('form_page.html.twig', $data);
    }

    public function deleteAction(Request $request, Application $app)
    {
        $page = $request->attributes->get('page');
        if (!$page) {
            $app->abort(404, 'The requested page was not found.');
        }

        $app['repository.page']->delete($page);
        return $app->redirect($app['url_generator']->generate('admin_pages'));
    }
    
    public function orderAction(Request $request, Application $app)
    {
        if ($request->isMethod('POST')) {
            $data = $_POST['page'];
            $app['repository.page']->saveOrder($data);
            
            $message = 'L\'ordre des pages a été enregistré.';
            $app['session']->getFlashBag()->add('success', $message);
            
            return $app->redirect($app['url_generator']->generate('admin_pages'));
        }
        else {
            $pagesOrder = $app['repository.page']->findAllOrderPage();

            $data = array(
                'pages' => $pagesOrder,
                'title' => 'Modification de l\'ordre des pages.',
                'link' => 'admin_pages_order'
            );

            return $app['twig']->render('admin_pages_order.html.twig', $data);
        }
    }
}
