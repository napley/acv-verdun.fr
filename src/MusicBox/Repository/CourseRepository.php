<?php

namespace MusicBox\Repository;

use Doctrine\DBAL\Connection;
use MusicBox\Entity\Course;

/**
 * Course repository
 */
class CourseRepository implements RepositoryInterface
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $db;
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $app;

    public function __construct(Connection $db, $app)
    {
        $this->db = $db;
        $this->app = $app;
    }

    /**
     * Saves the course to the database.
     *
     * @param \MusicBox\Entity\Course $course
     */
    public function save($course)
    {
        $courseData = array(
            'title' => $course->getTitle(),
            'link' => urldecode($course->getLink()),
            'start_at' => $course->getStartAt()->format('Y-m-d H:i:s'),
            'end_at' => $course->getEndAt()->format('Y-m-d H:i:s')
        );

        if ($course->getId()) {

            $this->db->update('courses', $courseData, array('course_id' => $course->getId()));
        }
        else {
            $this->db->insert('courses', $courseData);
            // Get the id of the newly created course and set it on the entity.
            $id = $this->db->lastInsertId();
            $course->setId($id);
        }
    }

    /**
     * Deletes the course.
     *
     * @param \MusicBox\Entity\Course $course
     */
    public function delete($course)
    {
        return $this->db->delete('courses', array('course_id' => $course->getId()));
    }

    /**
     * Returns the total number of courses.
     *
     * @return integer The total number of courses.
     */
    public function getCount() {
        return $this->db->fetchColumn('SELECT COUNT(course_id) FROM courses');
    }

    /**
     * Returns an course matching the supplied id.
     *
     * @param integer $id
     *
     * @return \MusicBox\Entity\Course|false An entity object if found, false otherwise.
     */
    public function find($id)
    {
        $courseData = $this->db->fetchAssoc('SELECT * FROM courses WHERE course_id = ?', array($id));
        return $courseData ? $this->buildCourse($courseData) : FALSE;
    }

    /**
     * Returns an collection course end next today.
     *
     * @return array A collection of courses, keyed by course id.
     */
    public function findNextCourse($conditions = array(), $orderBy = array())
    {
        // Provide a default orderBy.
        if (!$orderBy) {
            $orderBy = array('start_at' => 'ASC');
        }
        
        $today = new \DateTime();
        $today = $today->format('Y-m-d H:i:s');

        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder
            ->select('a.*')
            ->from('courses', 'a')
            ->innerJoin('a', 'category_course', 'cc', 'a.course_id = cc.course_id')
            ->where('a.end_at > :today')
            ->orderBy('a.' . key($orderBy), current($orderBy));
        $parameters = array();
        foreach ($conditions as $key => $value) {
            $parameters[':' . $key] = $value;
            $where = $queryBuilder->expr()->eq($key, ':' . $key);
            $queryBuilder->andWhere($where);
        }
        $queryBuilder->setParameters($parameters);
        $queryBuilder->setParameter('today', $today);
        $statement = $queryBuilder->execute();
        $coursesData = $statement->fetchAll();

        $courses = array();
        foreach ($coursesData as $courseData) {
            $courseId = $courseData['course_id'];
            $courses[$courseId] = $this->buildCourse($courseData);
        }
        return $courses;
    }
       
    /**
     * Returns a collection of courses, sorted by name.
     *
     * @param integer $limit
     *   The number of courses to return.
     * @param integer $offset
     *   The number of courses to skip.
     * @param array $orderBy
     *   Optionally, the order by info, in the $column => $direction format.
     *
     * @return array A collection of courses, keyed by course id.
     */
    public function findAll($limit, $offset = 0, $orderBy = array())
    {
        // Provide a default orderBy.
        if (!$orderBy) {
            $orderBy = array('start_at' => 'ASC');
        }

        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder
            ->select('a.*')
            ->from('courses', 'a')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->orderBy('a.' . key($orderBy), current($orderBy));
        $statement = $queryBuilder->execute();
        $coursesData = $statement->fetchAll();

        $courses = array();
        foreach ($coursesData as $courseData) {
            $courseId = $courseData['course_id'];
            $courses[$courseId] = $this->buildCourse($courseData);
        }
        return $courses;
    }

    /**
     * Instantiates an course entity and sets its properties using db data.
     *
     * @param array $courseData
     *   The array of db data.
     *
     * @return \MusicBox\Entity\Course
     */
    protected function buildCourse($courseData)
    {
        
        $course = new Course();
        $course->setId($courseData['course_id']);
        $course->setTitle($courseData['title']);
        $course->setLink($courseData['link']);
        $startAt = new \DateTime( $courseData['start_at']);
        $course->setStartAt($startAt);
        $endAt = new \DateTime( $courseData['end_at']);
        $course->setEndAt($endAt);
        
        if (!empty($courseData['course_id'])) {
            $categories = $this->app['repository.categoryCourse']->findAllByCourse($courseData['course_id']);
            $course->setCategories($categories);
        } else {
            $course->setCategories(null);
        }
        return $course;
    }
}
