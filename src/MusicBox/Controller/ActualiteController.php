<?php

namespace MusicBox\Controller;

use Silex\Application;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

class ActualiteController
{
    public function indexAction(Request $request, Application $app)
    {
        // Perform pagination logic.
        $limit = 10;
        $total = $app['repository.actualite']->getCountAffiched();
        $numPages = ceil($total / $limit);
        $currentPage = $request->query->get('page', 1);
        $currentPage = ($currentPage<1 ? 1 : $currentPage);
        $offset = ($currentPage - 1) * $limit;
        $actualites = $app['repository.actualite']->findAllAffiched($limit, $offset);
        // Divide actualites into groups of 4.
        $groupSize = 4;
        $progress = 0;
        while ($progress < $limit) {
            $groupedActualites[] = array_slice($actualites, $progress, $groupSize);
            $progress += $groupSize;
        }

        $data = array(
            'groupedActualites' => $groupedActualites,
            'currentPage' => $currentPage,
            'numPages' => $numPages,
            'here' => $app['url_generator']->generate('actualites'),
        );
        return $app['twig']->render('actualites.html.twig', $data);
    }

    public function viewAction(Request $request, Application $app)
    {
        $actualite = $request->attributes->get('actualite');
        if (!$actualite) {
            $app->abort(404, 'The requested actualite was not found.');
        }

        $data = array(
            'actualite' => $actualite,
        );
        return $app['twig']->render('actualite.html.twig', $data);
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
