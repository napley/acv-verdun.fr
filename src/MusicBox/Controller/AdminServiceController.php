<?php

namespace MusicBox\Controller;

use MusicBox\Entity\Service;
use MusicBox\Form\Type\ServiceType;
use Silex\Application;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

class AdminServiceController
{
    public function indexAction(Request $request, Application $app)
    {
        // Perform pagination logic.
        $limit = 30;
        $total = $app['repository.service']->getCount();
        $numPages = ceil($total / $limit);
        $currentPage = $request->query->get('service', 1);
        $currentPage = ($currentPage<1 ? 1 : $currentPage);
        $offset = ($currentPage - 1) * $limit;
        $services = $app['repository.service']->findAll($limit, $offset);

        $data = array(
            'services' => $services,
            'currentPage' => $currentPage,
            'numPages' => $numPages,
            'here' => $app['url_generator']->generate('admin_services'),
        );
        return $app['twig']->render('admin_services.html.twig', $data);
    }

    public function addAction(Request $request, Application $app)
    {
        $service = new Service();
        $form = $app['form.factory']->create(new ServiceType(), $service);
        
        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $app['repository.service']->save($service);
                $message = "Le service " . $service->getTitle() . ' a été enregistré.';
                $app['session']->getFlashBag()->add('success', $message);
                // Redirect to the edit service.
                $redirect = $app['url_generator']->generate('admin_services_edit', array('service' => $service->getId()));
                return $app->redirect($redirect);
            }
        }

        $data = array(
            'form' => $form->createView(),
            'title' => "Ajout d'un nouveau service",
            'link' => 'admin_services'
        );
        return $app['twig']->render('form.html.twig', $data);
    }

    public function editAction(Request $request, Application $app)
    {
        $service = $request->attributes->get('service');        
        if (!$service) {
            $app->abort(404, 'The requested service was not found.');
        }
        $form = $app['form.factory']->create(new ServiceType(), $service);

        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $app['repository.service']->save($service);
                $message = "Le service : " . $service->getTitle() . ' a été enregistré.';
                $app['session']->getFlashBag()->add('success', $message);
            }
        }

        $data = array(
            'form' => $form->createView(),
            'title' => "Modification du service : " . $service->getTitle(),
            'link' => 'admin_services'
        );
        return $app['twig']->render('form.html.twig', $data);
    }

    public function deleteAction(Request $request, Application $app)
    {
        $service = $request->attributes->get('service');
        if (!$service) {
            $app->abort(404, 'The requested service was not found.');
        }

        $app['repository.service']->delete($service);
        return $app->redirect($app['url_generator']->generate('admin_services'));
    }
}
