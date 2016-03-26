<?php

namespace MusicBox\Controller;

use Silex\Application;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

class PageController
{
    public function indexAction(Request $request, Application $app)
    {
        // Perform pagination logic.
        $limit = 4;
        $total = $app['repository.page']->getCount();
        $numPages = ceil($total / $limit);
        $currentPage = $request->query->get('page', 1);
        $currentPage = ($currentPage<1 ? 1 : $currentPage);
        $offset = ($currentPage - 1) * $limit;
        $pages = $app['repository.page']->findAll($limit, $offset);
        // Divide pages into groups of 4.
        $groupSize = 4;
        $progress = 0;
        while ($progress < $limit) {
            $groupedPages[] = array_slice($pages, $progress, $groupSize);
            $progress += $groupSize;
        }

        $data = array(
            'groupedPages' => $groupedPages,
            'currentPage' => $currentPage,
            'numPages' => $numPages,
            'here' => $app['url_generator']->generate('pages'),
        );
        return $app['twig']->render('pages.html.twig', $data);
    }

    public function viewAction(Request $request, Application $app)
    {
        $page = $request->attributes->get('nom_page');
        if (!$page) {
            $app->abort(404, 'The requested page was not found.');
        }

        $data = array(
            'page' => $page,
        );
        return $app['twig']->render('page.html.twig', $data);
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
