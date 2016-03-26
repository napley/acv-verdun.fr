<?php

namespace MusicBox\Controller;

use MusicBox\Entity\Widget;
use MusicBox\Form\Type\WidgetType;
use Silex\Application;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

class AdminWidgetController
{
    public function indexAction(Request $request, Application $app)
    {
        // Perform pagination logic.
        $limit = 15;
        $total = $app['repository.widget']->getCount();
        $numPages = ceil($total / $limit);
        $currentPage = $request->query->get('widget', 1);
        $currentPage = ($currentPage<1 ? 1 : $currentPage);
        $offset = ($currentPage - 1) * $limit;
        $widgets = $app['repository.widget']->findAll($limit, $offset);

        $data = array(
            'widgets' => $widgets,
            'currentPage' => $currentPage,
            'numPages' => $numPages,
            'here' => $app['url_generator']->generate('admin_widgets'),
        );
        return $app['twig']->render('admin_widgets.html.twig', $data);
    }

    public function addAction(Request $request, Application $app)
    {
        $widget = new Widget();
        $form = $app['form.factory']->create(new WidgetType(), $widget);
        
        //Récupération des catégories
        $arrayPreselection = [];
        $categories = $app['repository.category']->findAll();
        foreach ($categories as $category) {
            $arrayPreselection[]['category'] = $category;
        }  
        
        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $app['repository.widget']->save($widget);
                $app['repository.categoryWidget']->clean($request->request->get('categories'), $widget->getId());
                $message = 'Le widget ' . $widget->getTitle() . ' a été enregistré.';
                $app['session']->getFlashBag()->add('success', $message);
                // Redirect to the edit widget.
                $redirect = $app['url_generator']->generate('admin_widget_edit', array('widget' => $widget->getId()));
                return $app->redirect($redirect);
            }
        }

        $data = array(
            'form' => $form->createView(),
            'title' => "Ajout d'un nouveau widget",
            'link' => 'admin_widgets',
            'catActPres' => $arrayPreselection
        );
        return $app['twig']->render('form_widget.html.twig', $data);
    }

    public function editAction(Request $request, Application $app)
    {
        $widget = $request->attributes->get('widget');        
        if (!$widget) {
            $app->abort(404, 'The requested widget was not found.');
        }
        $form = $app['form.factory']->create(new WidgetType(), $widget);

        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $app['repository.widget']->save($widget);
                $app['repository.categoryWidget']->clean($request->request->get('categories'), $widget->getId());
                $message = 'Le widget ' . $widget->getTitle() . ' a été enregistré.';
                $app['session']->getFlashBag()->add('success', $message);
            }
        }
        
        //Récupération des catégories et préselection
        $categories = $app['repository.category']->findAll();
        $arrayPreselection = [];
        $i = 0;
        foreach ($categories as $category) {
            $arrayPreselection[$i]['category'] = $category;
            $categoryWidget = $app['repository.categoryWidget']->findByCategoryAndWidget($category->getId(), $widget->getId());
            if ($categoryWidget != NULL) {
                $arrayPreselection[$i]['categoryWidget'] = $categoryWidget;
            }
            $i++;
        }

        $data = array(
            'form' => $form->createView(),
            'title' => 'Modification du widget : ' . $widget->getTitle(),
            'link' => 'admin_widgets',
            'catActPres' => $arrayPreselection
        );
        return $app['twig']->render('form_widget.html.twig', $data);
    }

    public function deleteAction(Request $request, Application $app)
    {
        $widget = $request->attributes->get('widget');
        if (!$widget) {
            $app->abort(404, 'The requested widget was not found.');
        }

        $app['repository.widget']->delete($widget);
        return $app->redirect($app['url_generator']->generate('admin_widgets'));
    }
    
    public function orderAction(Request $request, Application $app)
    {
        if ($request->isMethod('POST')) {
            $data = $_POST['widget'];
            $app['repository.widget']->saveOrder($data);
            
            $message = 'L\'ordre des widgets a été enregistré.';
            $app['session']->getFlashBag()->add('success', $message);
            
            return $app->redirect($app['url_generator']->generate('admin_widgets'));
        }
        else {
            $widgetsOrder = $app['repository.widget']->findAll(9999);

            $data = array(
                'widgets' => $widgetsOrder,
                'title' => 'Modification de l\'ordre des widgets.',
                'link' => 'admin_widgets_order'
            );

            return $app['twig']->render('admin_widgets_order.html.twig', $data);
        }
    }
}
