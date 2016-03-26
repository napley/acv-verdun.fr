<?php

namespace MusicBox\Repository;

use Doctrine\DBAL\Connection;
use MusicBox\Entity\CategoryCourse;

/**
 * Like repository
 */
class CategoryCourseRepository implements RepositoryInterface
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $db;

    /**
     * @var \MusicBox\Repository\CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var \MusicBox\Repository\CourseRepository
     */
    protected $courseRepository;

    public function __construct(Connection $db, $categoryRepository, $courseRepository)
    {
        $this->db = $db;
        $this->categoryRepository = $categoryRepository;
        $this->courseRepository = $courseRepository;
    }

    /**
     * Saves the like to the database.
     *
     * @param \MusicBox\Entity\CategoryCourse $categoryCourse
     */
    public function save($categoryCourse)
    {
        $categoryCourseData = array(
            'category_id' => $categoryCourse->getCategory()->getId(),
            'course_id' => $categoryCourse->getCourse()->getId(),
        );

        if ($categoryCourse->getId()) {
            $this->db->update('category_course', $categoryCourseData, array('category_course_id' => $categoryCourse->getId()));
        } else {
            $this->db->insert('category_course', $categoryCourseData);
            // Get the id of the newly created categoryCourse and set it on the entity.
            $id = $this->db->lastInsertId();
            $categoryCourse->setId($id);
        }
    }

    /**
     * Saves the like to the database.
     *
     * @param array $categoryCourse
     */
    public function saveWithId($categoryCourse)
    {
        $categoryCourseData = array(
            'category_id' => $categoryCourse['category_id'],
            'course_id' => $categoryCourse['course_id'],
        );

        if ($categoryCourse['id']) {
            $this->db->update('category_course', $categoryCourseData, array('category_course_id' => $categoryCourse->getId()));
        } else {
            $this->db->insert('category_course', $categoryCourseData);
            // Get the id of the newly created categoryCourse and set it on the entity.
            $id = $this->db->lastInsertId();
            $categoryCourse['id'];
        }
    }

    /**
     * Saves the category by course to the database.
     *
     * @param array $categoriesId
     * @param integer $courseId
     */
    public function clean($categoriesId, $courseId)
    {
        foreach ($categoriesId as $categoryId) {
            $existCatAct = $this->findByCategoryAndCourse($categoryId, $courseId);
            if (empty($existCatAct)) {
                $this->saveWithId(['category_id' => $categoryId, 'course_id' => $courseId]);
            }
        }
        
        $categoriesCourse = $this->findAllByCourse($courseId, 1000, 0, ['category_course_id' => 'DESC']);
        foreach ($categoriesCourse as $categoryCourse) {
            if (!in_array($categoryCourse->getCategory()->getId(), $categoriesId)) {
                $this->delete($categoryCourse->getId());
            }
        }
    }


    /**
     * Deletes the like.
     *
     * @param integer $id
     */
    public function delete($id)
    {
        return $this->db->delete('category_course', array('category_course_id' => $id));
    }

    /**
     * Returns the total number of category_course.
     *
     * @return integer The total number of category_course.
     */
    public function getCount() {
        return $this->db->fetchColumn('SELECT COUNT(category_course_id) FROM category_course');
    }

    /**
     * Returns a like matching the supplied id.
     *
     * @param integer $id
     *
     * @return \MusicBox\Entity\CategoryCourse|false A category_course if found, false otherwise.
     */
    public function find($id)
    {
        $categoryCourseData = $this->db->fetchAssoc('SELECT * FROM category_course WHERE category_course_id = ?', array($id));
        return $categoryCourseData ? $this->buildCategoryCourse($categoryCourseData) : FALSE;
    }

    /**
     * Returns a collection of category Course for the given user id.
     *
     * @param integer $categoryId
     *   The artist id.
     * @param integer $courseId
     *   The user id.
     *
     * @return \MusicBox\Entity\CategoryCourse|false A CategoryCourse if found, false otherwise.
     */
    public function findByCategoryAndCourse($categoryId, $courseId)
    {
        $conditions = array(
            'category_id' => $categoryId,
            'course_id' => $courseId,
        );
        $categoryCourses = $this->getCategoryCourses($conditions, 1, 0, ['category_course_id' => 'DESC']);
        if ($categoryCourses) {
            return reset($categoryCourses);
        }
    }

    /**
     * Returns a collection of likes.
     *
     * @param integer $limit
     *   The number of likes to return.
     * @param integer $offset
     *   The number of likes to skip.
     * @param array $orderBy
     *   Optionally, the order by info, in the $column => $direction format.
     *
     * @return array A collection of likes, keyed by like id.
     */
    public function findAll($limit, $offset = 0, $orderBy = array())
    {
        return $this->getCategoryCourses(array(), $limit, $offset, $orderBy);
    }

    /**
     * Returns a collection of category_course for the given category id.
     *
     * @param integer $categoryId
     *   The category id.
     * @param integer $limit
     *   The number of category_course to return.
     * @param integer $offset
     *   The number of category_course to skip.
     * @param array $orderBy
     *   Optionally, the order by info, in the $column => $direction format.
     *
     * @return array A collection of category_course, keyed by category_course id.
     */
    public function findAllByCategory($categoryId, $limit, $offset = 0, $orderBy = array())
    {
        $conditions = array(
            'category_id' => $categoryId,
        );
        return $this->getCategoryCourses($conditions, $limit, $offset, $orderBy);
    }

    /**
     * Returns a collection of category_course for the given course id.
     *
     * @param $courseId
     *   The course id.
     * @param integer $limit
     *   The number of category_course to return.
     * @param integer $offset
     *   The number of category_course to skip.
     * @param array $orderBy
     *   Optionally, the order by info, in the $column => $direction format.
     *
     * @return array A collection of category_course, keyed by category_course id.
     */
    public function findAllByCourse($courseId, $limit = null, $offset = 0, $orderBy = array())
    {
        $conditions = array(
            'course_id' => $courseId,
        );
        return $this->getCategoryCourses($conditions, $limit, $offset, $orderBy);
    }

    /**
     * Returns a collection of likes for the given conditions.
     *
     * @param integer $limit
     *   The number of likes to return.
     * @param integer $offset
     *   The number of likes to skip.
     * @param $orderBy
     *   Optionally, the order by info, in the $column => $direction format.
     *
     * @return array A collection of likes, keyed by like id.
     */
    protected function getCategoryCourses(array $conditions, $limit, $offset, $orderBy = array())
    {
        // Provide a default orderBy.
        if (!$orderBy) {
            $orderBy = array('category_id' => 'ASC');
        }
        
        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder
            ->select('l.*')
            ->from('category_course', 'l')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->orderBy('l.' . key($orderBy), current($orderBy));
        $parameters = array();
        foreach ($conditions as $key => $value) {
            $parameters[':' . $key] = $value;
            $where = $queryBuilder->expr()->eq('l.' . $key, ':' . $key);
            $queryBuilder->andWhere($where);
        }
        $queryBuilder->setParameters($parameters);
        $statement = $queryBuilder->execute();
        $categoryCoursesData = $statement->fetchAll();

        $categoryCourses = array();
        foreach ($categoryCoursesData as $categoryCourseData) {
            $categoryCourseId = $categoryCourseData['category_course_id'];
            $categoryCourses[$categoryCourseId] = $this->buildCategoryCourse($categoryCourseData, $conditions);
        }
        return $categoryCourses;
    }

    /**
     * Instantiates a like entity and sets its properties using db data.
     *
     * @param array $categoryCourseData
     *   The array of db data.
     *
     * @return \MusicBox\Entity\CategoryCourse
     */
    protected function buildCategoryCourse($categoryCourseData, $conditions)
    {

        $categoryCourse = new CategoryCourse();
        $categoryCourse->setId($categoryCourseData['category_course_id']);
        
        // Load the related artist and user.
        if (!isset($conditions['category_id'])) {
            $category = $this->categoryRepository->find($categoryCourseData['category_id']);
            $categoryCourse->setCategory($category);   
        }
        if (!isset($conditions['course_id'])) {
            $course = $this->courseRepository->find($categoryCourseData['course_id']);
            $categoryCourse->setCourse($course);
        }
        return $categoryCourse;
    }
}
