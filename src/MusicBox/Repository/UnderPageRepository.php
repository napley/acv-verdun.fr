<?php

namespace MusicBox\Repository;

use Doctrine\DBAL\Connection;
use MusicBox\Entity\UnderPage;

/**
 * Page repository
 */
class UnderPageRepository implements RepositoryInterface
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

    public function saveOrder($data) {
        //$this->db->query('TRUNCATE TABLE order_page');
        $this->db->query('DELETE FROM order_page WHERE locked = 0');
        
        foreach ($data as $typePage => $orderPage) {
            foreach ($orderPage as $idPage => $rang) {
                $OrderData = null;
                if ($typePage == 3) {
                    $OrderData['rank'] = ($rang == 0? null : $rang);
                    $this->db->update('order_page', $OrderData, array('id' => $idPage));
                }
                else {
                    $OrderData['rank'] = ($rang == 0? null : $rang);
                    $OrderData['type_page'] = $typePage;
                    $OrderData['id_element'] = $idPage;
                    $OrderData['enable'] = 1;
                    $this->db->insert('order_page', $OrderData);
                }
            }
        }
    }
    
    /**
     * Saves the under_page to the database.
     *
     * @param \MusicBox\Entity\UnderPage $underPage
     */
    public function save($underPage)
    {
        $underPageData = array(
            'title' => $underPage->getTitle(),
            'description' => $underPage->getDescription(),
            'contenu' => $underPage->getContenu()
        );

        if ($underPage->getId()) {

            $this->db->update('under_pages', $underPageData, array('under_page_id' => $underPage->getId()));
        }
        else {
            $this->db->insert('under_pages', $underPageData);
            // Get the id of the newly created underPage and set it on the entity.
            $id = $this->db->lastInsertId();
            $underPage->setId($id);
        }
    }

    /**
     * Deletes the underPage.
     *
     * @param \MusicBox\Entity\UnderPage $underPage
     */
    public function delete($underPage)
    {
        return $this->db->delete('under_pages', array('under_page_id' => $underPage->getId()));
    }

    /**
     * Returns the total number of under_pages.
     *
     * @return integer The total number of under_pages.
     */
    public function getCount() {
        return $this->db->fetchColumn('SELECT COUNT(under_page_id) FROM under_pages');
    }

    /**
     * Returns an page matching the supplied id.
     *
     * @param integer $id
     *
     * @return \MusicBox\Entity\UnderPage|false An entity object if found, false otherwise.
     */
    public function find($id)
    {
        $underPageData = $this->db->fetchAssoc('SELECT * FROM under_pages WHERE under_page_id = ?', array($id));
        return $underPageData ? $this->buildUnderPage($underPageData) : FALSE;
    }

    /**
     * Returns an underPage matching the supplied name.
     *
     * @param string $name
     *
     * @return \MusicBox\Entity\UnderPage|false An entity object if found, false otherwise.
     */
    public function findByName($name)
    {
        $underPageData = $this->db->fetchAssoc('SELECT * FROM under_pages WHERE title = ?', array($name));
        return $underPageData ? $this->buildUnderPage($underPageData) : FALSE;
    }    
    
    
    public function findByPageRank($page)
    {
        $underPages = $this->findAll(9999, 0, null, ['page_id' => $page->getId()]);
        return $underPages;
    }
    
    /**
     * 
     * @param \MusicBox\Entity\Category $cat
     * @return type
     */
    public function findByCatRank($cat)
    {
        $underPages = $this->findAll(9999, 0, null, ['cat_id' => $cat->getId()]);
        return $underPages;
    }

    /**
     * Returns a collection of under_pages, sorted by name.
     *
     * @param integer $limit
     *   The number of under_pages to return.
     * @param integer $offset
     *   The number of under_pages to skip.
     * @param array $orderBy
     *   Optionally, the order by info, in the $column => $direction format.
     *
     * @return array A collection of under_pages, keyed by page id.
     */
    public function findAll($limit, $offset = 0, $orderBy = array(), $conditions = array())
    {
        // Provide a default orderBy.
        if (!$orderBy) {
            $orderBy = array('rank' => 'ASC');
        }
        
        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder
            ->select('a.*')
            ->from('under_pages', 'a')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->where('a.' . key($conditions).' = '.current($conditions))
            ->orderBy('a.' . key($orderBy), current($orderBy));
        $statement = $queryBuilder->execute();
        $underPagesData = $statement->fetchAll();

        $underPages = array();
        foreach ($underPagesData as $underPageData) {
            $underPageId = $underPageData['under_page_id'];
            $underPages[$underPageId] = $this->buildUnderPage($underPageData);
        }
        return $underPages;
    }

    /**
     * Instantiates an underPage entity and sets its properties using db data.
     *
     * @param array $underPageData
     *   The array of db data.
     *
     * @return \MusicBox\Entity\UnderPage
     */
    protected function buildUnderPage($underPageData)
    {        
        $underPage = new UnderPage();
        $underPage->setId($underPageData['under_page_id']);
        $underPage->setTitle($underPageData['title']);
        $underPage->setLink($underPageData['link']);
        $underPage->setRank($underPageData['rank']);
        
        if (!empty($underPageData['albumPhoto_id'])) {
            $page = $this->pageRepository->find($underPageData['page_id']);
            $underPage->setPage($categories);
        } else {
            $underPage->setPage(null);
        }
        
        return $underPage;
    }
}
