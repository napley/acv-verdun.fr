<?php

namespace MusicBox\Controller;

use MusicBox\Entity\AlbumPhoto;
use MusicBox\Form\Type\AlbumPhotoType;
use MusicBox\Form\Type\DateAlbumPhotoType;
use Silex\Application;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

class AdminAlbumPhotoController
{

    public function indexAction(Request $request, Application $app)
    {
        // Perform pagination logic.
        $limit = 15;
        $total = $app['repository.albumPhoto']->getCount();
        $numPages = ceil($total / $limit);
        $currentPage = $request->query->get('albumPhoto', 1);
        $currentPage = ($currentPage < 1 ? 1 : $currentPage);
        $offset = ($currentPage - 1) * $limit;
        $albumPhotos = $app['repository.albumPhoto']->findAll($limit, $offset);
        $albumPhotosOld = $app['repository.albumPhoto']->findAllOld(99999, 0);

        $data = array(
            'albumPhotos' => $albumPhotos,
            'albumPhotosOld' => $albumPhotosOld,
            'currentPage' => $currentPage,
            'numPages' => $numPages,
            'here' => $app['url_generator']->generate('admin_albumPhotos'),
        );
        return $app['twig']->render('admin_albumPhotos.html.twig', $data);
    }

    public function addAction(Request $request, Application $app)
    {
        $albumPhoto = new AlbumPhoto();
        $form = $app['form.factory']->create(new AlbumPhotoType(), $albumPhoto);

        //Récupération des catégories
        $arrayPreselection = [];
        $categories = $app['repository.category']->findAll();
        foreach ($categories as $category) {
            $arrayPreselection[]['category'] = $category;
        }

        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $app['repository.albumPhoto']->save($albumPhoto);
                $app['repository.categoryAlbumPhoto']->clean($request->request->get('categories'), $albumPhoto->getId());
                $message = 'L\'album photo ' . $albumPhoto->getTitle() . ' a été enregistré.';
                $app['session']->getFlashBag()->add('success', $message);
                // Redirect to the edit albumPhoto.
                $redirect = $app['url_generator']->generate('admin_albumPhoto_edit', array('albumPhoto' => $albumPhoto->getId()));
                return $app->redirect($redirect);
            }
        }

        $data = array(
            'form' => $form->createView(),
            'title' => 'Ajouter un nouvel album photo',
            'link' => 'admin_albumPhotos',
            'album' => 0,
            'catActPres' => $arrayPreselection
        );
        return $app['twig']->render('form_albumPhoto.html.twig', $data);
    }

    public function editAction(Request $request, Application $app)
    {
        $albumPhoto = $request->attributes->get('albumPhoto');
        if (!$albumPhoto) {
            $app->abort(404, 'The requested albumPhoto was not found.');
        }
        $form = $app['form.factory']->create(new AlbumPhotoType(), $albumPhoto);

        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $app['repository.albumPhoto']->save($albumPhoto);
                $app['repository.categoryAlbumPhoto']->clean($request->request->get('categories'), $albumPhoto->getId());
                $message = 'L\'album photo ' . $albumPhoto->getTitle() . ' a été enregistré.';
                $app['session']->getFlashBag()->add('success', $message);
            }
        }

        //Récupération des catégories et préselection
        $categories = $app['repository.category']->findAll();
        $arrayPreselection = [];
        $i = 0;
        foreach ($categories as $category) {
            $arrayPreselection[$i]['category'] = $category;
            $categoryAlbumPhoto = $app['repository.categoryAlbumPhoto']->findByCategoryAndAlbumPhoto($category->getId(), $albumPhoto->getId());
            if ($categoryAlbumPhoto != NULL) {
                $arrayPreselection[$i]['categoryAlbumPhoto'] = $categoryAlbumPhoto;
            }
            $i++;
        }

        $data = array(
            'form' => $form->createView(),
            'title' => "Modification de l'album : " . $albumPhoto->getTitle(),
            'link' => 'admin_albumPhotos',
            'album' => $albumPhoto->getId(),
            'catActPres' => $arrayPreselection
        );
        return $app['twig']->render('form_albumPhoto.html.twig', $data);
    }

    public function deleteAction(Request $request, Application $app)
    {
        $albumPhoto = $request->attributes->get('albumPhoto');
        if (!$albumPhoto) {
            $app->abort(404, 'The requested albumPhoto was not found.');
        }

        $app['repository.albumPhoto']->delete($albumPhoto);
        return $app->redirect($app['url_generator']->generate('admin_albumPhotos'));
    }

    public function migrationAction(Request $request, Application $app)
    {
        $oldAlbumPhotoId = $request->attributes->get('id');
        $oldAlbumPhoto = $app['repository.albumPhoto']->getOldAlbumPhoto($oldAlbumPhotoId);

        if (empty($oldAlbumPhoto['created'])) {
            return $app->redirect($app['url_generator']->generate('admin_albumPhoto_date', array('id' => $oldAlbumPhotoId)));
        } else {
            if (!empty($oldAlbumPhoto['created'])) {
                if (empty($app['repository.albumPhoto']->find($oldAlbumPhotoId))) {
                    $return = $app['repository.albumPhoto']->migrOldAlbumPhoto($oldAlbumPhoto);
                    if ($return == true) {
                        $message = 'L\'album photo a été migré.';
                        $app['session']->getFlashBag()->add('success', $message);
                    }
                }
            }
            return $app->redirect($app['url_generator']->generate('admin_albumPhotos'));
        }
    }

    public function dateAction(Request $request, Application $app)
    {
        $oldAlbumPhotoId = $request->attributes->get('id');
        $oldAlbumPhoto = $app['repository.albumPhoto']->getOldAlbumPhoto($oldAlbumPhotoId);

        $form = $app['form.factory']->create(new DateAlbumPhotoType(), $oldAlbumPhoto);
            
        if ($request->isMethod('GET')) {
            $form->bind($request);
            if ($form->isValid()) {
                $data = $form->getData();
                $app['repository.albumPhoto']->setDateCreated($data['created'], $data['id']);
                return $app->redirect($app['url_generator']->generate('admin_albumPhoto_migr', array('id' => $oldAlbumPhotoId)));
            }
        }
        $data = array(
            'form' => $form->createView(),
            'title' => "Date de l'album : " . $oldAlbumPhoto['nom'],
            'oldAlbum' => $oldAlbumPhoto,
            'link' => 'admin_albumPhoto_date'
        );
        return $app['twig']->render('form_date_albumPhoto.html.twig', $data);
    }

    public function uploadAction(Request $request, Application $app)
    {
        error_reporting(E_ALL | E_STRICT);
        //require('UploadHandler.php');
        $upload_handler = new \MusicBox\Entity\UploadHandler();
    }

}
