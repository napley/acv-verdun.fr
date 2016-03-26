<?php

namespace MusicBox\Controller;

use MusicBox\Entity\Course;
use MusicBox\Form\Type\CourseType;
use Silex\Application;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

class AdminCourseController
{
    public function indexAction(Request $request, Application $app)
    {
        // Perform pagination logic.
        $limit = 15;
        $total = $app['repository.course']->getCount();
        $numPages = ceil($total / $limit);
        $currentPage = $request->query->get('course', 1);
        $currentPage = ($currentPage<1 ? 1 : $currentPage);
        $offset = ($currentPage - 1) * $limit;
        $courses = $app['repository.course']->findAll($limit, $offset);

        $data = array(
            'courses' => $courses,
            'currentPage' => $currentPage,
            'numPages' => $numPages,
            'here' => $app['url_generator']->generate('admin_courses'),
        );
        return $app['twig']->render('admin_courses.html.twig', $data);
    }

    public function addAction(Request $request, Application $app)
    {
        $course = new Course();
        $form = $app['form.factory']->create(new CourseType(), $course);
        
        //Récupération des catégories
        $arrayPreselection = [];
        $categories = $app['repository.category']->findAll();
        foreach ($categories as $category) {
            $arrayPreselection[]['category'] = $category;
        }  
        
        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $app['repository.course']->save($course);
                $app['repository.categoryCourse']->clean($request->request->get('categories'), $course->getId());
                $message = 'La course ' . $course->getTitle() . ' a été enregistré.';
                $app['session']->getFlashBag()->add('success', $message);
                // Redirect to the edit course.
                $redirect = $app['url_generator']->generate('admin_course_edit', array('course' => $course->getId()));
                return $app->redirect($redirect);
            }
        }

        $data = array(
            'form' => $form->createView(),
            'title' => 'Ajouter une nouvelle course',
            'link' => 'admin_courses',
            'catActPres' => $arrayPreselection
        );
        return $app['twig']->render('form_course.html.twig', $data);
    }

    public function editAction(Request $request, Application $app)
    {
        $course = $request->attributes->get('course');        
        if (!$course) {
            $app->abort(404, 'The requested course was not found.');
        }
        $form = $app['form.factory']->create(new CourseType(), $course);

        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $app['repository.course']->save($course);
                $app['repository.categoryCourse']->clean($request->request->get('categories'), $course->getId());
                $message = 'La course ' . $course->getTitle() . ' a été enregistré.';
                $app['session']->getFlashBag()->add('success', $message);
            }
        }
        
        //Récupération des catégories et préselection
        $categories = $app['repository.category']->findAll();
        $arrayPreselection = [];
        $i = 0;
        foreach ($categories as $category) {
            $arrayPreselection[$i]['category'] = $category;
            $categoryCourse = $app['repository.categoryCourse']->findByCategoryAndCourse($category->getId(), $course->getId());
            if ($categoryCourse != NULL) {
                $arrayPreselection[$i]['categoryCourse'] = $categoryCourse;
            }
            $i++;
        }

        $data = array(
            'form' => $form->createView(),
            'title' => 'Modification de la course : ' . $course->getTitle(),
            'link' => 'admin_courses',
            'catActPres' => $arrayPreselection
        );
        return $app['twig']->render('form_course.html.twig', $data);
    }

    public function deleteAction(Request $request, Application $app)
    {
        $course = $request->attributes->get('course');
        if (!$course) {
            $app->abort(404, 'The requested course was not found.');
        }

        $app['repository.course']->delete($course);
        return $app->redirect($app['url_generator']->generate('admin_courses'));
    }
}
