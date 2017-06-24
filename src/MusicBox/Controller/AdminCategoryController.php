<?php

namespace MusicBox\Controller;

use MusicBox\Entity\Category;
use MusicBox\Form\Type\CategoryType;
use Silex\Application;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

class AdminCategoryController
{
    public function indexAction(Request $request, Application $app)
    {
        // Perform pagination logic.
        $limit = 15;
        $total = $app['repository.category']->getCount();
        $numPages = ceil($total / $limit);
        $currentPage = $request->query->get('page', 1);
        $currentPage = ($currentPage<1 ? 1 : $currentPage);
        $offset = ($currentPage - 1) * $limit;
        $categories = $app['repository.category']->findAll($limit, $offset);

        $data = array(
            'categories' => $categories,
            'currentPage' => $currentPage,
            'numPages' => $numPages,
            'here' => $app['url_generator']->generate('admin_categories'),
        );
        return $app['twig']->render('admin_categories.html.twig', $data);
    }

    public function addAction(Request $request, Application $app)
    {
        $category = new Category();
        $form = $app['form.factory']->create(new CategoryType(), $category);
        
        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $app['repository.category']->save($category);
                $app['repository.category']->saveRankUnderPages($request->request->get('underPages'), $page);
                $message = 'La catégorie ' . $category->getTitle() . ' a été enregistré.';
                $app['session']->getFlashBag()->add('success', $message);
                // Redirect to the edit category.
                $redirect = $app['url_generator']->generate('admin_category_edit', array('category' => $category->getId()));
                return $app->redirect($redirect);
            }
        }

        $data = array(
            'underPages' => array(),
            'form' => $form->createView(),
            'title' => "Ajout d'une nouvelle catégorie",
            'link' => 'admin_categories'
        );
        return $app['twig']->render('form_cat.html.twig', $data);
    }

    public function editAction(Request $request, Application $app)
    {
        $category = $request->attributes->get('category');        
        if (!$category) {
            $app->abort(404, 'The requested category was not found.');
        }
        $form = $app['form.factory']->create(new CategoryType(), $category);

        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $app['repository.category']->save($category);
                $app['repository.category']->saveRankUnderPages($request->request->get('underPages'), $category);
                $message = 'La catégorie ' . $category->getTitle() . ' a été enregistré.';
                $app['session']->getFlashBag()->add('success', $message);
            }
        }
        
        $underPages = $app['repository.category']->getRankUnderPages($category);

        $data = array(
            'underPages' => $underPages,
            'form' => $form->createView(),
            'title' => 'Modification de la catégorie : ' . $category->getTitle(),
            'link' => 'admin_categories'
        );
        return $app['twig']->render('form_cat.html.twig', $data);
    }

    public function deleteAction(Request $request, Application $app)
    {
        $category = $request->attributes->get('category');
        if (!$category) {
            $app->abort(404, 'The requested category was not found.');
        }

        $app['repository.category']->delete($category);
        return $app->redirect($app['url_generator']->generate('admin_categories'));
    }
}
