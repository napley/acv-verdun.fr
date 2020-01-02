<?php

namespace MusicBox\Controller;

use Silex\Application;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

class AlbumPhotoController
{

    public function indexAction(Request $request, Application $app)
    {
        $limit = 30;
        $currentYear = $request->query->get('annee', null);
        $currentPage = $request->query->get('page', 1);
        $currentPage = ($currentPage < 1 ? 1 : $currentPage);
        $offset = ($currentPage - 1) * $limit;
        $newestOrderBy = array('created_at' => 'DESC');
        if (!empty($currentYear)) {
            $newestAlbumPhotos = $app['repository.albumPhoto']->findByYear($currentYear, $limit, $offset, $newestOrderBy);
            $total = $app['repository.albumPhoto']->getCountYear($currentYear);
        } else {
            $newestAlbumPhotos = $app['repository.albumPhoto']->findAll($limit, $offset, $newestOrderBy);
            $total = $app['repository.albumPhoto']->getCount();
        }

        $numPages = ceil($total / $limit);

        // Divide artists into groups of 2.
        $groupSize = 2;
        $groupedNewestAlbumPhotos = array();
        $progress = 0;
        while ($progress < $limit) {
            $groupedNewestAlbumPhotos[] = array_slice($newestAlbumPhotos, $progress, $groupSize);
            $progress += $groupSize;
        }

        $listeAnnee = [];
        for ($year = date('Y'); $year > 2009; $year--) {
            $listeAnnee[] = $year;
        }


        $data = array(
            'groupedNewestAlbumPhotos' => $groupedNewestAlbumPhotos,
            'currentPage' => $currentPage,
            'numPages' => $numPages,
            'here' => $app['url_generator']->generate('albumPhotos'),
            'filtreAnnee' => $listeAnnee,
            'currentYear' => $currentYear
        );
        return $app['twig']->render('albumPhotos.html.twig', $data);
    }

    public function viewAction(Request $request, Application $app)
    {
        $albumPhoto = $request->attributes->get('albumPhoto');
        if (!$albumPhoto) {
            $app->abort(404, 'The requested albumPhoto was not found.');
        }

        $data = array(
            'albumPhoto' => $albumPhoto,
        );
        return $app['twig']->render('albumPhoto.html.twig', $data);
    }

    public function likeAction(Request $request, Application $app)
    {
        $artist = $request->attributes->get('artist');
        $token = $app['security']->getToken();
        $user = $token->getUser();
        if (!$artist) {
            $app->abort(404, 'The requested artist was not found.');
        }
        if ($user == 'anon.') {
            // Only logged-in users can comment.
            return;
        }

        // Don't allow the user to like the artist twice.
        $existingLike = $app['repository.like']->findByArtistAndUser($artist->getId(), $user->getId());
        if (!$existingLike) {
            // Save the individual like record.
            $like = new Like();
            $like->setArtist($artist);
            $like->setUser($user);
            $app['repository.like']->save($like);

            // Increase the counter on the artist.
            $numLikes = $artist->getLikes();
            $numLikes++;
            $artist->setLikes($numLikes);
            $app['repository.artist']->save($artist);
        }
        return '';
    }

    protected function sendNotification($comment, Application $app)
    {
        $artist = $comment->getArtist();
        $user = $comment->getUser();
        $messageBody = 'The following comment was posted by ' . $user->getUsername() . ":\n";
        $messageBody .= $comment->getComment();
        $message = \Swift_Message::newInstance()
                ->setSubject('New comment posted for artist ' . $artist->getName())
                ->setFrom(array($app['site_email']))
                ->setTo(array($app['admin_email']))
                ->setBody('The following comment was posted by :');
        $app['mailer']->send($message);
    }

}
