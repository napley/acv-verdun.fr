<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
 
// Register service providers.
$app->register(new Silex\Provider\DoctrineServiceProvider());
$app->register(new Silex\Provider\FormServiceProvider());
$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\ValidatorServiceProvider());
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\TranslationServiceProvider());
$app->register(new Silex\Provider\SwiftmailerServiceProvider());

$app->register(new Silex\Provider\SecurityServiceProvider(), array(
    'security.firewalls' => array(
        'admin' => array(
            'pattern' => '^/',
            'form' => array(
                'login_path' => '/login',
                'check_path' => '/admin/login_check',
                'username_parameter' => 'form[username]',
                'password_parameter' => 'form[password]',
            ),
            'logout'  => true,
            'anonymous' => true,
            'users' => $app->share(function () use ($app) {
                return new MusicBox\Repository\UserRepository($app['db'], $app['security.encoder.digest']);
            }),
        ),
    ),
    'security.role_hierarchy' => array(
       'ROLE_ADMIN' => array('ROLE_USER'),
    ),
));
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.options' => array(
        'cache' => isset($app['twig.options.cache']) ? $app['twig.options.cache'] : false,
        'strict_variables' => true,
    ),
    'twig.form.templates' => array('form_div_layout.html.twig', 'common/form_div_layout.html.twig'),
    'twig.path' => array(__DIR__ . '/../app/views')
));

$app["twig"] = $app->share($app->extend("twig", function (\Twig_Environment $twig, Silex\Application $app) {
    $twig->addExtension(new MusicBox\Extension\Widget($app));
    $twig->addExtension(new MusicBox\Extension\Course($app));
    $twig->addExtension(new MusicBox\Extension\Circuit($app));
    $twig->addExtension(new MusicBox\Extension\InfoSite($app));
    $twig->addExtension(new MusicBox\Extension\CategoryMenu($app));
    $twig->addExtension(new MusicBox\Extension\OrderPage($app));
    $twig->addExtension(new MusicBox\Extension\OrderWidget($app));
    $twig->addExtension(new MusicBox\Extension\UnderPages($app));
    $twig->addExtension(new MusicBox\Extension\Pages($app));
    $twig->addExtension(new MusicBox\Extension\LastAlbum($app));

    return $twig;
}));

// Register repositories.
$app['repository.artist'] = $app->share(function ($app) {
    return new MusicBox\Repository\ArtistRepository($app['db']);
});
$app['repository.actualite'] = $app->share(function ($app) {
    return new MusicBox\Repository\ActualiteRepository($app['db'], $app);
});
$app['repository.page'] = $app->share(function ($app) {
    return new MusicBox\Repository\PageRepository($app['db'], $app);
});
$app['repository.underPage'] = $app->share(function ($app) {
    return new MusicBox\Repository\UnderPageRepository($app['db'], $app);
});
$app['repository.widget'] = $app->share(function ($app) {
    return new MusicBox\Repository\WidgetRepository($app['db']);
});
$app['repository.category'] = $app->share(function ($app) {
    return new MusicBox\Repository\CategoryRepository($app['db'], $app);
});
$app['repository.infosite'] = $app->share(function ($app) {
    return new MusicBox\Repository\InfoSiteRepository($app['db']);
});
$app['repository.service'] = $app->share(function ($app) {
    return new MusicBox\Repository\ServiceRepository($app['db']);
});
$app['repository.course'] = $app->share(function ($app) {
    return new MusicBox\Repository\CourseRepository($app['db'], $app);
});
$app['repository.albumPhoto'] = $app->share(function ($app) {
    return new MusicBox\Repository\AlbumPhotoRepository($app['db'], $app);
});
$app['repository.entrainement'] = $app->share(function ($app) {
    return new MusicBox\Repository\EntrainementRepository($app['db']);
});
$app['repository.categoryActualite'] = $app->share(function ($app) {
    return new MusicBox\Repository\CategoryActualiteRepository($app['db'], $app['repository.category'], $app['repository.actualite']);
});
$app['repository.categoryCourse'] = $app->share(function ($app) {
    return new MusicBox\Repository\CategoryCourseRepository($app['db'], $app['repository.category'], $app['repository.course']);
});
$app['repository.categoryWidget'] = $app->share(function ($app) {
    return new MusicBox\Repository\CategoryWidgetRepository($app['db'], $app['repository.category'], $app['repository.widget']);
});
$app['repository.categoryAlbumPhoto'] = $app->share(function ($app) {
    return new MusicBox\Repository\CategoryAlbumPhotoRepository($app['db'], $app['repository.category'], $app['repository.albumPhoto']);
});
$app['repository.user'] = $app->share(function ($app) {
    return new MusicBox\Repository\UserRepository($app['db'], $app['security.encoder.digest']);
});
$app['repository.comment'] = $app->share(function ($app) {
    return new MusicBox\Repository\CommentRepository($app['db'], $app['repository.artist'], $app['repository.user']);
});
$app['repository.like'] = $app->share(function ($app) {
    return new MusicBox\Repository\LikeRepository($app['db'], $app['repository.artist'], $app['repository.user']);
});
// Register custom services.
$app['soundcloud'] = $app->share(function ($app) {
    return new MusicBox\Service\SoundCloud();
});

// Protect admin urls.
$app->before(function (Request $request) use ($app) {
    $protected = array(
        '/admin/users/' => 'ROLE_ADMIN',
        '/admin/infosites/' => 'ROLE_ADMIN',
        'admin/' => 'ROLE_USER',
    );
    $path = $request->getPathInfo();
    foreach ($protected as $protectedPath => $role) {
        if (strpos($path, $protectedPath) !== FALSE && !$app['security']->isGranted($role)) {
            throw new AccessDeniedException();
        }
    }
});

// Register the error handler.
$app->error(function (\Exception $e, $code) use ($app) {
    if ($app['debug']) {
        return;
    }

    switch ($code) {
        case 404:
            $message = 'The requested page could not be found.';
            break;
        default:
            $message = 'We are sorry, but something went terribly wrong.';
    }

    return new Response($message, $code);
});

return $app;
