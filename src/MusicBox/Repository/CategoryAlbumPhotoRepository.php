<?php

namespace MusicBox\Repository;

use Doctrine\DBAL\Connection;
use MusicBox\Entity\CategoryAlbumPhoto;

/**
 * Like repository
 */
class CategoryAlbumPhotoRepository implements RepositoryInterface
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
     * @var \MusicBox\Repository\AlbumPhotoRepository
     */
    protected $albumPhotoRepository;

    public function __construct(Connection $db, $categoryRepository, $albumPhotoRepository)
    {
        $this->db = $db;
        $this->categoryRepository = $categoryRepository;
        $this->albumPhotoRepository = $albumPhotoRepository;
    }

    /**
     * Saves the like to the database.
     *
     * @param \MusicBox\Entity\CategoryAlbumPhoto $categoryAlbumPhoto
     */
    public function save($categoryAlbumPhoto)
    {
        $categoryAlbumPhotoData = array(
            'category_id' => $categoryAlbumPhoto->getCategory()->getId(),
            'albumPhoto_id' => $categoryAlbumPhoto->getAlbumPhoto()->getId(),
        );

        if ($categoryAlbumPhoto->getId()) {
            $this->db->update('category_albumPhoto', $categoryAlbumPhotoData, array('category_albumPhoto_id' => $categoryAlbumPhoto->getId()));
        } else {
            $this->db->insert('category_albumPhoto', $categoryAlbumPhotoData);
            // Get the id of the newly created categoryAlbumPhoto and set it on the entity.
            $id = $this->db->lastInsertId();
            $categoryAlbumPhoto->setId($id);
        }
    }

    /**
     * Saves the like to the database.
     *
     * @param array $categoryAlbumPhoto
     */
    public function saveWithId($categoryAlbumPhoto)
    {
        $categoryAlbumPhotoData = array(
            'category_id' => $categoryAlbumPhoto['category_id'],
            'albumPhoto_id' => $categoryAlbumPhoto['albumPhoto_id'],
        );

        if ($categoryAlbumPhoto['id']) {
            $this->db->update('category_albumPhoto', $categoryAlbumPhotoData, array('category_albumPhoto_id' => $categoryAlbumPhoto->getId()));
        } else {
            $this->db->insert('category_albumPhoto', $categoryAlbumPhotoData);
            // Get the id of the newly created categoryAlbumPhoto and set it on the entity.
            $id = $this->db->lastInsertId();
            $categoryAlbumPhoto['id'];
        }
    }

    /**
     * Saves the category by albumPhoto to the database.
     *
     * @param array $categoriesId
     * @param integer $albumPhotoId
     */
    public function clean($categoriesId, $albumPhotoId)
    {
        foreach ($categoriesId as $categoryId) {
            $existCatAct = $this->findByCategoryAndAlbumPhoto($categoryId, $albumPhotoId);
            if (empty($existCatAct)) {
                $this->saveWithId(['category_id' => $categoryId, 'albumPhoto_id' => $albumPhotoId]);
            }
        }
        
        $categoriesAlbumPhoto = $this->findAllByAlbumPhoto($albumPhotoId, 1000, 0, ['category_albumPhoto_id' => 'DESC']);
        foreach ($categoriesAlbumPhoto as $categoryAlbumPhoto) {
            if (!in_array($categoryAlbumPhoto->getCategory()->getId(), $categoriesId)) {
                $this->delete($categoryAlbumPhoto->getId());
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
        return $this->db->delete('category_albumPhoto', array('category_albumPhoto_id' => $id));
    }

    /**
     * Returns the total number of category_albumPhoto.
     *
     * @return integer The total number of category_albumPhoto.
     */
    public function getCount() {
        return $this->db->fetchColumn('SELECT COUNT(category_albumPhoto_id) FROM category_albumPhoto');
    }

    /**
     * Returns a like matching the supplied id.
     *
     * @param integer $id
     *
     * @return \MusicBox\Entity\CategoryAlbumPhoto|false A category_albumPhoto if found, false otherwise.
     */
    public function find($id)
    {
        $categoryAlbumPhotoData = $this->db->fetchAssoc('SELECT * FROM category_albumPhoto WHERE category_albumPhoto_id = ?', array($id));
        return $categoryAlbumPhotoData ? $this->buildCategoryAlbumPhoto($categoryAlbumPhotoData) : FALSE;
    }

    /**
     * Returns a collection of category AlbumPhoto for the given user id.
     *
     * @param integer $categoryId
     *   The artist id.
     * @param integer $albumPhotoId
     *   The user id.
     *
     * @return \MusicBox\Entity\CategoryAlbumPhoto|false A CategoryAlbumPhoto if found, false otherwise.
     */
    public function findByCategoryAndAlbumPhoto($categoryId, $albumPhotoId)
    {
        $conditions = array(
            'category_id' => $categoryId,
            'albumPhoto_id' => $albumPhotoId,
        );
        $categoryAlbumPhotos = $this->getCategoryAlbumPhotos($conditions, 1, 0, ['category_albumPhoto_id' => 'DESC']);
        if ($categoryAlbumPhotos) {
            return reset($categoryAlbumPhotos);
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
        return $this->getgetCategoryAlbumPhotos(array(), $limit, $offset, $orderBy);
    }

    /**
     * Returns a collection of category_albumPhoto for the given category id.
     *
     * @param integer $categoryId
     *   The category id.
     * @param integer $limit
     *   The number of category_albumPhoto to return.
     * @param integer $offset
     *   The number of category_albumPhoto to skip.
     * @param array $orderBy
     *   Optionally, the order by info, in the $column => $direction format.
     *
     * @return array A collection of category_albumPhoto, keyed by category_albumPhoto id.
     */
    public function findAllByCategory($categoryId, $limit, $offset = 0, $orderBy = array())
    {
        $conditions = array(
            'category_id' => $categoryId,
        );
        return $this->getCategoryAlbumPhotos($conditions, $limit, $offset, $orderBy);
    }

    /**
     * Returns a collection of category_albumPhoto for the given albumPhoto id.
     *
     * @param $albumPhotoId
     *   The albumPhoto id.
     * @param integer $limit
     *   The number of category_albumPhoto to return.
     * @param integer $offset
     *   The number of category_albumPhoto to skip.
     * @param array $orderBy
     *   Optionally, the order by info, in the $column => $direction format.
     *
     * @return array A collection of category_albumPhoto, keyed by category_albumPhoto id.
     */
    public function findAllByAlbumPhoto($albumPhotoId, $limit = null, $offset = 0, $orderBy = array())
    {
        $conditions = array(
            'albumPhoto_id' => $albumPhotoId,
        );
        return $this->getCategoryAlbumPhotos($conditions, $limit, $offset, $orderBy);
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
    protected function getCategoryAlbumPhotos(array $conditions, $limit, $offset, $orderBy = array())
    {
        // Provide a default orderBy.
        if (!$orderBy) {
            $orderBy = array('albumPhoto_id' => 'ASC');
        }
        
        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder
            ->select('l.*')
            ->from('category_albumPhoto', 'l')
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
        $categoryAlbumPhotosData = $statement->fetchAll();

        $categoryAlbumPhotos = array();
        foreach ($categoryAlbumPhotosData as $categoryAlbumPhotoData) {
            $categoryAlbumPhotoId = $categoryAlbumPhotoData['category_albumPhoto_id'];
            $categoryAlbumPhotos[$categoryAlbumPhotoId] = $this->buildCategoryAlbumPhoto($categoryAlbumPhotoData, $conditions);
        }
        return $categoryAlbumPhotos;
    }

    /**
     * Instantiates a like entity and sets its properties using db data.
     *
     * @param array $categoryAlbumPhotoData
     *   The array of db data.
     *
     * @return \MusicBox\Entity\CategoryAlbumPhoto
     */
    protected function buildCategoryAlbumPhoto($categoryAlbumPhotoData, $conditions)
    {

        $categoryAlbumPhoto = new CategoryAlbumPhoto();
        $categoryAlbumPhoto->setId($categoryAlbumPhotoData['category_albumPhoto_id']);
                
        // Load the related artist and user.
        if (!isset($conditions['category_id'])) {
            $category = $this->categoryRepository->find($categoryAlbumPhotoData['category_id']);       
            $categoryAlbumPhoto->setCategory($category); 
        }
        if (!isset($conditions['albumPhoto_id'])) {
            $albumPhoto = $this->actualiteRepository->find($categoryAlbumPhotoData['albumPhoto_id']);
            $categoryAlbumPhoto->setAlbumPhoto($albumPhoto);
        }

        return $categoryAlbumPhoto;
    }
}
