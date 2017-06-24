<?php

namespace MusicBox\Controller;

use MusicBox\Entity\InfoSite;
use MusicBox\Form\Type\InfoSiteType;
use Silex\Application;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

class AdminInfoSiteController
{
    public function indexAction(Request $request, Application $app)
    {
        // Perform pagination logic.
        $limit = 30;
        $total = $app['repository.infosite']->getCount();
        $numPages = ceil($total / $limit);
        $currentPage = $request->query->get('page', 1);
        $currentPage = ($currentPage<1 ? 1 : $currentPage);
        $offset = ($currentPage - 1) * $limit;
        $infosites = $app['repository.infosite']->findAll($limit, $offset);

        $data = array(
            'infosites' => $infosites,
            'currentPage' => $currentPage,
            'numPages' => $numPages,
            'here' => $app['url_generator']->generate('admin_infosites'),
        );
        return $app['twig']->render('admin_infosites.html.twig', $data);
    }

    public function addAction(Request $request, Application $app)
    {
        $infosite = new InfoSite();
        $form = $app['form.factory']->create(new InfoSiteType(), $infosite);
        
        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $app['repository.infosite']->save($infosite);
                $message = "L'information du site " . $infosite->getNom() . ' a été enregistré.';
                $app['session']->getFlashBag()->add('success', $message);
                // Redirect to the edit infosite.
                $redirect = $app['url_generator']->generate('admin_infosite_edit', array('infosite' => $infosite->getId()));
                return $app->redirect($redirect);
            }
        }

        $data = array(
            'form' => $form->createView(),
            'title' => "Ajout d'une nouvelle information du site",
            'link' => 'admin_infosites'
        );
        return $app['twig']->render('form.html.twig', $data);
    }

    public function editAction(Request $request, Application $app)
    {
        $infosite = $request->attributes->get('infosite');        
        if (!$infosite) {
            $app->abort(404, 'The requested infosite was not found.');
        }
        $form = $app['form.factory']->create(new InfoSiteType(), $infosite);

        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $app['repository.infosite']->save($infosite);
                $message = "L'information du site : " . $infosite->getNom() . ' a été enregistré.';
                $app['session']->getFlashBag()->add('success', $message);
            }
        }

        $data = array(
            'form' => $form->createView(),
            'title' => "Modification de l'information du site : " . $infosite->getNom(),
            'link' => 'admin_infosites'
        );
        return $app['twig']->render('form.html.twig', $data);
    }

    public function deleteAction(Request $request, Application $app)
    {
        $infosite = $request->attributes->get('infosite');
        if (!$infosite) {
            $app->abort(404, 'The requested infosite was not found.');
        }

        $app['repository.infosite']->delete($infosite);
        return $app->redirect($app['url_generator']->generate('admin_infosites'));
    }
}
