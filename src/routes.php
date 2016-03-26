<?php

// Register route converters.
// Each converter needs to check if the $id it received is actually a value,
// as a workaround for https://github.com/silexphp/Silex/pull/768.
$app['controllers']->convert('artist', function ($id) use ($app) {
    if ($id) {
        return $app['repository.artist']->find($id);
    }
});
$app['controllers']->convert('actualite', function ($id) use ($app) {
    if ($id) {
        return $app['repository.actualite']->find($id);
    }
});
$app['controllers']->convert('page', function ($id) use ($app) {
    if ($id) {
        return $app['repository.page']->find($id);
    }
});
$app['controllers']->convert('nom_page', function ($name) use ($app) {
    if ($name) {
        return $app['repository.page']->findByName($name);
    }
});
$app['controllers']->convert('widget', function ($id) use ($app) {
    if ($id) {
        return $app['repository.widget']->find($id);
    }
});
$app['controllers']->convert('category', function ($id) use ($app) {
    if ($id) {
        return $app['repository.category']->find($id);
    }
});
$app['controllers']->convert('infosite', function ($id) use ($app) {
    if ($id) {
        return $app['repository.infosite']->find($id);
    }
});
$app['controllers']->convert('service', function ($id) use ($app) {
    if ($id) {
        return $app['repository.service']->find($id);
    }
});
$app['controllers']->convert('course', function ($id) use ($app) {
    if ($id) {
        return $app['repository.course']->find($id);
    }
});
$app['controllers']->convert('albumPhoto', function ($id) use ($app) {
    if ($id) {
        return $app['repository.albumPhoto']->find($id);
    }
});
$app['controllers']->convert('entrainement', function ($id) use ($app) {
    if ($id) {
        return $app['repository.entrainement']->find($id);
    }
});
$app['controllers']->convert('abrev_cat', function ($abrev) use ($app) {
    if ($abrev) {
        return $app['repository.category']->findByAbrev($abrev);
    }
});
$app['controllers']->convert('comment', function ($id) use ($app) {
    if ($id) {
        return $app['repository.comment']->find($id);
    }
});
$app['controllers']->convert('user', function ($id) use ($app) {
    if ($id) {
        return $app['repository.user']->find($id);
    }
});

// Register routes.
$app->get('/', 'MusicBox\Controller\IndexController::indexAction')
    ->bind('homepage');
$app->get('/p/{num_page}', 'MusicBox\Controller\IndexController::indexAction')
    ->value('num_page', '1')
    ->bind('article_page');

$app->get('/me', 'MusicBox\Controller\UserController::meAction')
    ->bind('me');
$app->match('/login', 'MusicBox\Controller\UserController::loginAction')
    ->bind('login');
$app->get('/logout', 'MusicBox\Controller\UserController::logoutAction')
    ->bind('logout');

$app->get('/artists', 'MusicBox\Controller\ArtistController::indexAction')
    ->bind('artists');
$app->match('/artist/{artist}', 'MusicBox\Controller\ArtistController::viewAction')
    ->bind('artist');
$app->match('/artist/{artist}/like', 'MusicBox\Controller\ArtistController::likeAction')
    ->bind('artist_like');
$app->get('/api/artists', 'MusicBox\Controller\ApiArtistController::indexAction');
$app->get('/api/artist/{artist}', 'MusicBox\Controller\ApiArtistController::viewAction');
$app->post('/api/artist', 'MusicBox\Controller\ApiArtistController::addAction');
$app->put('/api/artist/{artist}', 'MusicBox\Controller\ApiArtistController::editAction');
$app->delete('/api/artist/{artist}', 'MusicBox\Controller\ApiArtistController::deleteAction');

$app->get('/api/entrainements', 'MusicBox\Controller\ApiEntrainementController::indexAction');

$app->get('/articles', 'MusicBox\Controller\ActualiteController::indexAction')
    ->bind('actualites');
$app->match('/article/{actualite}', 'MusicBox\Controller\ActualiteController::viewAction')
    ->bind('actualite');

$app->get('/album-photo', 'MusicBox\Controller\AlbumPhotoController::indexAction')
    ->bind('albumPhotos');

$app->get('/album-photo/{albumPhoto}', 'MusicBox\Controller\AlbumPhotoController::viewAction')
    ->bind('albumPhoto');

$app->match('/page/{nom_page}', 'MusicBox\Controller\PageController::viewAction')
    ->bind('page');

$app->match('/cat/{abrev_cat}', 'MusicBox\Controller\CategoryController::indexAction')
    ->bind('cat');

$app->get('/admin', 'MusicBox\Controller\AdminController::indexAction')
    ->bind('admin');

$app->get('/admin/services', 'MusicBox\Controller\AdminServiceController::indexAction')
    ->bind('admin_services');
$app->match('/admin/services/add', 'MusicBox\Controller\AdminServiceController::addAction')
    ->bind('admin_services_add');
$app->match('/admin/services/{service}/edit', 'MusicBox\Controller\AdminServiceController::editAction')
    ->bind('admin_services_edit');
$app->match('/admin/services/{service}/delete', 'MusicBox\Controller\AdminServiceController::deleteAction')
    ->bind('admin_services_delete');

$app->get('/admin/artists', 'MusicBox\Controller\AdminArtistController::indexAction')
    ->bind('admin_artists');
$app->match('/admin/artists/add', 'MusicBox\Controller\AdminArtistController::addAction')
    ->bind('admin_artist_add');
$app->match('/admin/artists/{artist}/edit', 'MusicBox\Controller\AdminArtistController::editAction')
    ->bind('admin_artist_edit');
$app->match('/admin/artists/{artist}/delete', 'MusicBox\Controller\AdminArtistController::deleteAction')
    ->bind('admin_artist_delete');

$app->get('/admin/', 'MusicBox\Controller\AdminActualiteController::indexAction')
    ->bind('admin');
$app->get('/admin/articles', 'MusicBox\Controller\AdminActualiteController::indexAction')
    ->bind('admin_actualites');
$app->match('/admin/articles/add', 'MusicBox\Controller\AdminActualiteController::addAction')
    ->bind('admin_actualite_add');
$app->match('/admin/articles/{actualite}/edit', 'MusicBox\Controller\AdminActualiteController::editAction')
    ->bind('admin_actualite_edit');
$app->match('/admin/articles/{actualite}/delete', 'MusicBox\Controller\AdminActualiteController::deleteAction')
    ->bind('admin_actualite_delete');

$app->get('/admin/users', 'MusicBox\Controller\AdminUserController::indexAction')
    ->bind('admin_users');
$app->match('/admin/users/add', 'MusicBox\Controller\AdminUserController::addAction')
    ->bind('admin_user_add');
$app->match('/admin/users/{user}/edit', 'MusicBox\Controller\AdminUserController::editAction')
    ->bind('admin_user_edit');
$app->match('/admin/users/{user}/delete', 'MusicBox\Controller\AdminUserController::deleteAction')
    ->bind('admin_user_delete');

$app->get('/admin/pages', 'MusicBox\Controller\AdminPageController::indexAction')
    ->bind('admin_pages');
$app->match('/admin/pages/add', 'MusicBox\Controller\AdminPageController::addAction')
    ->bind('admin_page_add');
$app->match('/admin/pages/{page}/edit', 'MusicBox\Controller\AdminPageController::editAction')
    ->bind('admin_page_edit');
$app->match('/admin/pages/{page}/delete', 'MusicBox\Controller\AdminPageController::deleteAction')
    ->bind('admin_page_delete');
$app->match('/admin/pages/order', 'MusicBox\Controller\AdminPageController::orderAction')
    ->bind('admin_pages_order');

$app->get('/admin/widgets', 'MusicBox\Controller\AdminWidgetController::indexAction')
    ->bind('admin_widgets');
$app->match('/admin/widgets/add', 'MusicBox\Controller\AdminWidgetController::addAction')
    ->bind('admin_widget_add');
$app->match('/admin/widgets/{widget}/edit', 'MusicBox\Controller\AdminWidgetController::editAction')
    ->bind('admin_widget_edit');
$app->match('/admin/widgets/{widget}/delete', 'MusicBox\Controller\AdminWidgetController::deleteAction')
    ->bind('admin_widget_delete');
$app->match('/admin/widgets/order', 'MusicBox\Controller\AdminWidgetController::orderAction')
    ->bind('admin_widgets_order');

$app->get('/admin/entrainements', 'MusicBox\Controller\AdminEntrainementController::indexAction')
    ->bind('admin_entrainements');
$app->match('/admin/entrainements/add', 'MusicBox\Controller\AdminEntrainementController::addAction')
    ->bind('admin_entrainement_add');
$app->match('/admin/entrainements/{entrainement}/edit', 'MusicBox\Controller\AdminEntrainementController::editAction')
    ->bind('admin_entrainement_edit');
$app->match('/admin/entrainements/{entrainement}/delete', 'MusicBox\Controller\AdminEntrainementController::deleteAction')
    ->bind('admin_entrainement_delete');

$app->get('/admin/courses', 'MusicBox\Controller\AdminCourseController::indexAction')
    ->bind('admin_courses');
$app->match('/admin/courses/add', 'MusicBox\Controller\AdminCourseController::addAction')
    ->bind('admin_course_add');
$app->match('/admin/courses/{course}/edit', 'MusicBox\Controller\AdminCourseController::editAction')
    ->bind('admin_course_edit');
$app->match('/admin/courses/{course}/delete', 'MusicBox\Controller\AdminCourseController::deleteAction')
    ->bind('admin_course_delete');

$app->get('/admin/albumPhotos', 'MusicBox\Controller\AdminAlbumPhotoController::indexAction')
    ->bind('admin_albumPhotos');
$app->match('/admin/albumPhotos/add', 'MusicBox\Controller\AdminAlbumPhotoController::addAction')
    ->bind('admin_albumPhoto_add');
$app->match('/admin/albumPhotos/{albumPhoto}/edit', 'MusicBox\Controller\AdminAlbumPhotoController::editAction')
    ->bind('admin_albumPhoto_edit');
$app->match('/admin/albumPhotos/upload', 'MusicBox\Controller\AdminAlbumPhotoController::uploadAction')
    ->bind('admin_albumPhoto_upload');
$app->match('/admin/albumPhotos/{albumPhoto}/delete', 'MusicBox\Controller\AdminAlbumPhotoController::deleteAction')
    ->bind('admin_albumPhoto_delete');
$app->get('/admin/albumPhotos/migration/{id}', 'MusicBox\Controller\AdminAlbumPhotoController::migrationAction')
    ->bind('admin_albumPhoto_migr');
$app->get('/admin/albumPhotos/setDate/{id}', 'MusicBox\Controller\AdminAlbumPhotoController::dateAction')
    ->bind('admin_albumPhoto_date');

$app->get('/admin/categories', 'MusicBox\Controller\AdminCategoryController::indexAction')
    ->bind('admin_categories');
$app->match('/admin/categories/add', 'MusicBox\Controller\AdminCategoryController::addAction')
    ->bind('admin_category_add');
$app->match('/admin/categories/{category}/edit', 'MusicBox\Controller\AdminCategoryController::editAction')
    ->bind('admin_category_edit');
$app->match('/admin/categories/{category}/delete', 'MusicBox\Controller\AdminCategoryController::deleteAction')
    ->bind('admin_category_delete');

$app->get('/admin/infosites', 'MusicBox\Controller\AdminInfoSiteController::indexAction')
    ->bind('admin_infosites');
$app->match('/admin/infosites/add', 'MusicBox\Controller\AdminInfoSiteController::addAction')
    ->bind('admin_infosite_add');
$app->match('/admin/infosites/{infosite}/edit', 'MusicBox\Controller\AdminInfoSiteController::editAction')
    ->bind('admin_infosite_edit');
$app->match('/admin/infosites/{infosite}/delete', 'MusicBox\Controller\AdminInfoSiteController::deleteAction')
    ->bind('admin_infosite_delete');

$app->get('/admin/comments', 'MusicBox\Controller\AdminCommentController::indexAction')
    ->bind('admin_comments');
$app->match('/admin/comments/{comment}/edit', 'MusicBox\Controller\AdminCommentController::editAction')
    ->bind('admin_comment_edit');
$app->match('/admin/comments/{comment}/delete', 'MusicBox\Controller\AdminCommentController::deleteAction')
    ->bind('admin_comment_delete');
$app->match('/admin/comments/{comment}/approve', 'MusicBox\Controller\AdminCommentController::approveAction')
    ->bind('admin_comment_approve');
$app->match('/admin/comments/{comment}/unapprove', 'MusicBox\Controller\AdminCommentController::unapproveAction')
    ->bind('admin_comment_unapprove');
