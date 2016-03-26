<?php

namespace MusicBox\Repository;

use Doctrine\DBAL\Connection;
use MusicBox\Entity\Category;

/**
 * Category repository
 */
class CategoryRepository implements RepositoryInterface
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
     * Saves the category to the database.
     *
     * @param \MusicBox\Entity\Category $category
     */
    public function save($category)
    {
        $categoryData = array(
            'title' => $category->getTitle(),
            'abrev' => $category->getAbrev()
        );

        if ($category->getId()) {

            $this->db->update('categories', $categoryData, array('category_id' => $category->getId()));
        }
        else {
            $this->db->insert('categories', $categoryData);
            // Get the id of the newly created category and set it on the entity.
            $id = $this->db->lastInsertId();
            $category->setId($id);
        }
    }

    /**
     * Deletes the category.
     *
     * @param \MusicBox\Entity\Category $category
     */
    public function delete($category)
    {
        return $this->db->delete('categories', array('category_id' => $category->getId()));
    }

    /**
     * Returns the total number of categories.
     *
     * @return integer The total number of categories.
     */
    public function getCount() {
        return $this->db->fetchColumn('SELECT COUNT(category_id) FROM categories');
    }

    /**
     * Returns an category matching the supplied id.
     *
     * @param integer $id
     *
     * @return \MusicBox\Entity\Category|false An entity object if found, false otherwise.
     */
    public function find($id)
    {
        $categoryData = $this->db->fetchAssoc('SELECT * FROM categories WHERE category_id = ?', array($id));
        return $categoryData ? $this->buildCategory($categoryData) : FALSE;
    }

    /**
     * Returns an category matching the supplied id.
     *
     * @param integer $id
     *
     * @return \MusicBox\Entity\Category|false An entity object if found, false otherwise.
     */
    public function findByAbrev($abrev)
    {
        $categoryData = $this->db->fetchAssoc('SELECT * FROM categories WHERE abrev = ?', array($abrev));
        return $categoryData ? $this->buildCategory($categoryData) : FALSE;
    }
    
    /**
     * Retourne la liste des underPages lié à cette catégorie trié par rank
     * @param array<\MusicBox\Entity\UnderPage> $page
     */
    public function getRankUnderPages($cat)
    {
        $underPages = $this->app['repository.underPage']->findByCatRank($cat);
        return $underPages;
    }
    
    
    public function saveRankUnderPages($arrayUnderPages, $cat)
    {                
        $queryBuilder = $this->db->createQueryBuilder();

        $queryBuilder->delete('under_pages')
            ->where('cat_id = :cat', $cat->getId())
            ->setParameter('cat', $cat->getId());
        $statement = $queryBuilder->execute();
        
        foreach ($arrayUnderPages['Titre'] as $rank => $underPage) {
            if (!empty($arrayUnderPages['Titre'][$rank])) {
                $underPagesData['rank'] = $rank +1;
                $underPagesData['title'] = $arrayUnderPages['Titre'][$rank];
                $underPagesData['link'] = $arrayUnderPages['Lien'][$rank];
                $underPagesData['cat_id'] = $cat->getId();
                $this->db->insert('under_pages', $underPagesData);
            }
        }
    }


    /**
     * Returns a collection of categories, sorted by name.
     *
     * @param integer $limit
     *   The number of categories to return.
     * @param integer $offset
     *   The number of categories to skip.
     * @param array $orderBy
     *   Optionally, the order by info, in the $column => $direction format.
     *
     * @return array A collection of categories, keyed by category id.
     */
    public function findAll($limit = 1000, $offset = 0, $orderBy = array())
    {
        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder
            ->select('a.*')
            ->from('categories', 'a')
            ->setMaxResults($limit)
            ->setFirstResult($offset);
        $statement = $queryBuilder->execute();
        $categoriesData = $statement->fetchAll();

        $categories = array();
        foreach ($categoriesData as $categoryData) {
            $categoryId = $categoryData['category_id'];
            $categories[$categoryId] = $this->buildCategory($categoryData);
        }
        return $categories;
    }

    /**
     * Instantiates an category entity and sets its properties using db data.
     *
     * @param array $categoryData
     *   The array of db data.
     *
     * @return \MusicBox\Entity\Category
     */
    protected function buildCategory($categoryData)
    {        
        $category = new Category();
        $category->setId($categoryData['category_id']);
        $category->setTitle($categoryData['title']);
        $category->setAbrev($categoryData['abrev']);
        return $category;
    }
}
