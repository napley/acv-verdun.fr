<?php

namespace MusicBox\Repository;

use Doctrine\DBAL\Connection;
use MusicBox\Entity\Actualite;

/**
 * Actualite repository
 */
class ActualiteRepository implements RepositoryInterface
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
     * Saves the actualite to the database.
     *
     * @param \MusicBox\Entity\Actualite $actualite
     */
    public function save($actualite)
    {
        $actualiteData = array(
            'user_id' => $actualite->getUser()->getId(),
            'title' => $actualite->getTitle(),
            'description' => $actualite->getDescription(),
            'contenu' => $actualite->getContenu(),
            'affichage' => $actualite->getAffichage()
        );

        if ($actualite->getId()) {

            $this->db->update('actualites', $actualiteData, array('actualite_id' => $actualite->getId()));
        }
        else {
            // The actualite is new, note the creation timestamp.
            $actualiteData['created_at'] = date('Y-m-d H:i:s');

            $this->db->insert('actualites', $actualiteData);
            // Get the id of the newly created actualite and set it on the entity.
            $id = $this->db->lastInsertId();
            $actualite->setId($id);
        }
    }

    /**
     * Deletes the actualite.
     *
     * @param \MusicBox\Entity\Actualite $actualite
     */
    public function delete($actualite)
    {
        return $this->db->delete('actualites', array('actualite_id' => $actualite->getId()));
    }

    /**
     * Returns the total number of actualites.
     *
     * @return integer The total number of actualites.
     */
    public function getCount() {
        return $this->db->fetchColumn('SELECT COUNT(actualite_id) FROM actualites');
    }
    
    /**
     * 
     * @return type
     */
    public function getCountAffiched() {
        return $this->db->fetchColumn('SELECT COUNT(actualite_id) FROM actualites WHERE affichage = 1 ');
    }
    
    /**
     * 
     * @return type
     */
    public function getCountAffichedByCategory($category) {
        return $this->db->fetchColumn('SELECT COUNT(a.actualite_id) FROM actualites a INNER JOIN category_actualite ca ON ca.actualite_id = a.actualite_id AND ca.category_id = '.$category.' WHERE a.affichage = 1 ');
    }
    
    /**
     * Returns an actualite matching the supplied id.
     *
     * @param integer $id
     *
     * @return \MusicBox\Entity\Actualite|false An entity object if found, false otherwise.
     */
    public function find($id)
    {
        $actualiteData = $this->db->fetchAssoc('SELECT * FROM actualites WHERE actualite_id = ?', array($id));
        return $actualiteData ? $this->buildActualite($actualiteData) : FALSE;
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
    public function findAllAffiched($conditions = array(), $limit = null, $offset = 0, $orderBy = array())
    {
        $conditions['affichage'] = 1;
        
        return $this->getActualites($conditions, $limit, $offset, $orderBy);
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
    public function findAllAffichedByCategory($conditions = array(), $limit = null, $offset = 0, $orderBy = array())
    {
        $conditions['affichage'] = 1;
        
        return $this->getActualitesByCategory($conditions, $limit, $offset, $orderBy);
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
    protected function getActualites(array $conditions, $limit, $offset, $orderBy = array())
    {
        // Provide a default orderBy.
        if (!$orderBy) {
            $orderBy = array('created_at' => 'ASC');
        }
        
        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder
            ->select('l.*')
            ->from('actualites', 'l')
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
        $actualitesData = $statement->fetchAll();

        $actualites = array();
        foreach ($actualitesData as $actualiteData) {
            $actualiteId = $actualiteData['actualite_id'];
            $actualites[$actualiteId] = $this->buildActualite($actualiteData);
        }
        return $actualites;
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
    protected function getActualitesByCategory(array $conditions, $limit, $offset, $orderBy = array())
    {
        // Provide a default orderBy.
        if (!$orderBy) {
            $orderBy = array('created_at' => 'ASC');
        }
        
        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder
            ->select('l.*')
            ->from('actualites', 'l')
            ->innerJoin('l', 'category_actualite', 'ca', 'l.actualite_id = ca.actualite_id')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->orderBy(key($orderBy), current($orderBy));
        $parameters = array();
        foreach ($conditions as $key => $value) {
            $parameters[':' . $key] = $value;
            $where = $queryBuilder->expr()->eq($key, ':' . $key);
            $queryBuilder->andWhere($where);
        }
        $queryBuilder->setParameters($parameters);
        $statement = $queryBuilder->execute();
        $actualitesData = $statement->fetchAll();

        $actualites = array();
        foreach ($actualitesData as $actualiteData) {
            $actualiteId = $actualiteData['actualite_id'];
            $actualites[$actualiteId] = $this->buildActualite($actualiteData);
        }
        return $actualites;
    }


    /**
     * Returns a collection of actualites, sorted by name.
     *
     * @param integer $limit
     *   The number of actualites to return.
     * @param integer $offset
     *   The number of actualites to skip.
     * @param array $orderBy
     *   Optionally, the order by info, in the $column => $direction format.
     *
     * @return array A collection of actualites, keyed by actualite id.
     */
    public function findAll($limit, $offset = 0, $orderBy = array())
    {
        // Provide a default orderBy.
        if (!$orderBy) {
            $orderBy = array('created_at' => 'ASC');
        }

        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder
            ->select('a.*')
            ->from('actualites', 'a')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->orderBy('a.' . key($orderBy), current($orderBy));
        $statement = $queryBuilder->execute();
        $actualitesData = $statement->fetchAll();

        $actualites = array();
        foreach ($actualitesData as $actualiteData) {
            $actualiteId = $actualiteData['actualite_id'];
            $actualites[$actualiteId] = $this->buildActualite($actualiteData);
        }
        return $actualites;
    }

    /**
     * Instantiates an actualite entity and sets its properties using db data.
     *
     * @param array $actualiteData
     *   The array of db data.
     *
     * @return \MusicBox\Entity\Actualite
     */
    protected function buildActualite($actualiteData)
    {
        $user = $this->app['repository.user']->find($actualiteData['user_id']);
        
        
        $actualite = new Actualite();
        $actualite->setId($actualiteData['actualite_id']);
        $actualite->setAffichage((bool) $actualiteData['affichage']);
        $actualite->setUser($user);
        $actualite->setTitle($actualiteData['title']);
        $actualite->setDescription($actualiteData['description']);
        $actualite->setContenu($actualiteData['contenu']);
        $createdAt = new \DateTime( $actualiteData['created_at']);
        $actualite->setCreatedAt($createdAt);
        
        
        if (!empty($actualiteData['actualite_id'])) {
            $categories = $this->app['repository.categoryActualite']->findAllByActualite($actualiteData['actualite_id']);
            $actualite->setCategories($categories);
        } else {
            $actualite->setCategories(null);
        }
        
        return $actualite;
    }
}
