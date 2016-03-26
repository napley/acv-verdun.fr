<?php

namespace MusicBox\Controller;

use MusicBox\Entity\Entrainement;
use MusicBox\Form\Type\EntrainementType;
use Silex\Application;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

class AdminEntrainementController
{
    public function indexAction(Request $request, Application $app)
    {
        // Perform pagination logic.
        $limit = 15;
        $total = $app['repository.entrainement']->getCount();
        $numPages = ceil($total / $limit);
        $currentPage = $request->query->get('entrainement', 1);
        $currentPage = ($currentPage<1 ? 1 : $currentPage);
        $offset = ($currentPage - 1) * $limit;
        $entrainements = $app['repository.entrainement']->findAll($limit, $offset);

        $data = array(
            'entrainements' => $entrainements,
            'currentPage' => $currentPage,
            'numPages' => $numPages,
            'here' => $app['url_generator']->generate('admin_entrainements'),
        );
        return $app['twig']->render('admin_entrainements.html.twig', $data);
    }

    public function addAction(Request $request, Application $app)
    {
        $entrainement = new Entrainement();
        $form = $app['form.factory']->create(new EntrainementType(), $entrainement);
        
        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $app['repository.entrainement']->save($entrainement);
                $message = 'Le circuit ' . $entrainement->getTitle() . ' a été enregistré.';
                $app['session']->getFlashBag()->add('success', $message);
                // Redirect to the edit entrainement.
                $redirect = $app['url_generator']->generate('admin_entrainement_edit', array('entrainement' => $entrainement->getId()));
                return $app->redirect($redirect);
            }
        }

        $data = array(
            'form' => $form->createView(),
            'title' => 'Ajouter un nouveau circuit',
            'link' => 'admin_entrainements'
        );
        return $app['twig']->render('form.html.twig', $data);
    }

    public function editAction(Request $request, Application $app)
    {
        $entrainement = $request->attributes->get('entrainement');        
        if (!$entrainement) {
            $app->abort(404, 'The requested entrainement was not found.');
        }
        $form = $app['form.factory']->create(new EntrainementType(), $entrainement);

        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $app['repository.entrainement']->save($entrainement);
                $message = 'Le circuit ' . $entrainement->getTitle() . ' a été enregistré.';
                $app['session']->getFlashBag()->add('success', $message);
            }
        }

        $data = array(
            'form' => $form->createView(),
            'title' => 'Modification du circuit : ' . $entrainement->getTitle(),
            'link' => 'admin_entrainements',
            'script' => $entrainement->getScript()
        );
        return $app['twig']->render('form_entrainement.html.twig', $data);
    }

    public function deleteAction(Request $request, Application $app)
    {
        $entrainement = $request->attributes->get('entrainement');
        if (!$entrainement) {
            $app->abort(404, 'The requested entrainement was not found.');
        }

        $app['repository.entrainement']->delete($entrainement);
        return $app->redirect($app['url_generator']->generate('admin_entrainements'));
    }
}
