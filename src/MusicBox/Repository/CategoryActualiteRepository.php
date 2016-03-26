<?php

namespace MusicBox\Repository;

use Doctrine\DBAL\Connection;
use MusicBox\Entity\CategoryActualite;

/**
 * Like repository
 */
class CategoryActualiteRepository implements RepositoryInterface
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
     * @var \MusicBox\Repository\ActualiteRepository
     */
    protected $actualiteRepository;

    public function __construct(Connection $db, $categoryRepository, $actualiteRepository)
    {
        $this->db = $db;
        $this->categoryRepository = $categoryRepository;
        $this->actualiteRepository = $actualiteRepository;
    }

    /**
     * Saves the like to the database.
     *
     * @param \MusicBox\Entity\CategoryActualite $categoryActualite
     */
    public function save($categoryActualite)
    {
        $categoryActualiteData = array(
            'category_id' => $categoryActualite->getCategory()->getId(),
            'actualite_id' => $categoryActualite->getActualite()->getId(),
        );

        if ($categoryActualite->getId()) {
            $this->db->update('category_actualite', $categoryActualiteData, array('category_actualite_id' => $categoryActualite->getId()));
        } else {
            $this->db->insert('category_actualite', $categoryActualiteData);
            // Get the id of the newly created categoryActualite and set it on the entity.
            $id = $this->db->lastInsertId();
            $categoryActualite->setId($id);
        }
    }

    /**
     * Saves the like to the database.
     *
     * @param array $categoryActualite
     */
    public function saveWithId($categoryActualite)
    {
        $categoryActualiteData = array(
            'category_id' => $categoryActualite['category_id'],
            'actualite_id' => $categoryActualite['actualite_id'],
        );

        if ($categoryActualite['id']) {
            $this->db->update('category_actualite', $categoryActualiteData, array('category_actualite_id' => $categoryActualite->getId()));
        } else {
            $this->db->insert('category_actualite', $categoryActualiteData);
            // Get the id of the newly created categoryActualite and set it on the entity.
            $id = $this->db->lastInsertId();
            $categoryActualite['id'];
        }
    }

    /**
     * Saves the category by actualite to the database.
     *
     * @param array $categoriesId
     * @param integer $actualiteId
     */
    public function clean($categoriesId, $actualiteId)
    {
        foreach ($categoriesId as $categoryId) {
            $existCatAct = $this->findByCategoryAndActualite($categoryId, $actualiteId);
            if (empty($existCatAct)) {
                $this->saveWithId(['category_id' => $categoryId, 'actualite_id' => $actualiteId]);
            }
        }
        
        $categoriesActualite = $this->findAllByActualite($actualiteId, 1000, 0, ['category_actualite_id' => 'DESC']);
        foreach ($categoriesActualite as $categoryActualite) {
            if (!in_array($categoryActualite->getCategory()->getId(), $categoriesId)) {
                $this->delete($categoryActualite->getId());
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
        return $this->db->delete('category_actualite', array('category_actualite_id' => $id));
    }

    /**
     * Returns the total number of category_actualite.
     *
     * @return integer The total number of category_actualite.
     */
    public function getCount() {
        return $this->db->fetchColumn('SELECT COUNT(category_actualite_id) FROM category_actualite');
    }

    /**
     * Returns a like matching the supplied id.
     *
     * @param integer $id
     *
     * @return \MusicBox\Entity\CategoryActualite|false A category_actualite if found, false otherwise.
     */
    public function find($id)
    {
        $categoryActualiteData = $this->db->fetchAssoc('SELECT * FROM category_actualite WHERE category_actualite_id = ?', array($id));
        return $categoryActualiteData ? $this->buildCategoryActualite($categoryActualiteData) : FALSE;
    }

    /**
     * Returns a collection of category Actualite for the given user id.
     *
     * @param integer $categoryId
     *   The artist id.
     * @param integer $actualiteId
     *   The user id.
     *
     * @return \MusicBox\Entity\CategoryActualite|false A CategoryActualite if found, false otherwise.
     */
    public function findByCategoryAndActualite($categoryId, $actualiteId)
    {
        $conditions = array(
            'category_id' => $categoryId,
            'actualite_id' => $actualiteId,
        );
        $categoryActualites = $this->getCategoryActualites($conditions, 1, 0, ['category_actualite_id' => 'DESC']);
        if ($categoryActualites) {
            return reset($categoryActualites);
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
        return $this->getgetCategoryActualites(array(), $limit, $offset, $orderBy);
    }

    /**
     * Returns a collection of category_actualite for the given category id.
     *
     * @param integer $categoryId
     *   The category id.
     * @param integer $limit
     *   The number of category_actualite to return.
     * @param integer $offset
     *   The number of category_actualite to skip.
     * @param array $orderBy
     *   Optionally, the order by info, in the $column => $direction format.
     *
     * @return array A collection of category_actualite, keyed by category_actualite id.
     */
    public function findAllByCategory($categoryId, $limit, $offset = 0, $orderBy = array())
    {
        $conditions = array(
            'category_id' => $categoryId,
        );
        return $this->getCategoryActualites($conditions, $limit, $offset, $orderBy);
    }

    /**
     * Returns a collection of category_actualite for the given actualite id.
     *
     * @param $actualiteId
     *   The actualite id.
     * @param integer $limit
     *   The number of category_actualite to return.
     * @param integer $offset
     *   The number of category_actualite to skip.
     * @param array $orderBy
     *   Optionally, the order by info, in the $column => $direction format.
     *
     * @return array A collection of category_actualite, keyed by category_actualite id.
     */
    public function findAllByActualite($actualiteId, $limit = null, $offset = 0, $orderBy = array())
    {
        $conditions = array(
            'actualite_id' => $actualiteId,
        );
        return $this->getCategoryActualites($conditions, $limit, $offset, $orderBy);
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
    protected function getCategoryActualites(array $conditions, $limit, $offset, $orderBy = array())
    {
        // Provide a default orderBy.
        if (!$orderBy) {
            $orderBy = array('actualite_id' => 'ASC');
        }
        
        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder
            ->select('l.*')
            ->from('category_actualite', 'l')
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
        $categoryActualitesData = $statement->fetchAll();

        $categoryActualites = array();
        foreach ($categoryActualitesData as $categoryActualiteData) {
            $categoryActualiteId = $categoryActualiteData['category_actualite_id'];
            $categoryActualites[$categoryActualiteId] = $this->buildCategoryActualite($categoryActualiteData, $conditions);
        }
        return $categoryActualites;
    }

    /**
     * Instantiates a like entity and sets its properties using db data.
     *
     * @param array $categoryActualiteData
     *   The array of db data.
     *
     * @return \MusicBox\Entity\CategoryActualite
     */
    protected function buildCategoryActualite($categoryActualiteData, $conditions)
    {
        $categoryActualite = new CategoryActualite();
        $categoryActualite->setId($categoryActualiteData['category_actualite_id']);
        
        // Load the related artist and user.
        if (!isset($conditions['category_id'])) {
            $category = $this->categoryRepository->find($categoryActualiteData['category_id']);       
            $categoryActualite->setCategory($category); 
        }
        if (!isset($conditions['actualite_id'])) {
            $actualite = $this->actualiteRepository->find($categoryActualiteData['course_id']);
            $categoryActualite->setActualite($actualite);
        }

        return $categoryActualite;
    }
}
