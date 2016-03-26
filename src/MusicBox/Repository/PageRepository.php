<?php

namespace MusicBox\Repository;

use Doctrine\DBAL\Connection;
use MusicBox\Entity\Page;

/**
 * Page repository
 */
class PageRepository implements RepositoryInterface
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
     * Saves the page to the database.
     *
     * @param \MusicBox\Entity\Page $page
     */
    public function save($page)
    {
        $pageData = array(
            'title' => $page->getTitle(),
            'description' => $page->getDescription(),
            'contenu' => $page->getContenu()
        );

        if ($page->getId()) {

            $this->db->update('pages', $pageData, array('page_id' => $page->getId()));
        }
        else {
            $this->db->insert('pages', $pageData);
            // Get the id of the newly created page and set it on the entity.
            $id = $this->db->lastInsertId();
            $page->setId($id);
        }
    }

    /**
     * Deletes the page.
     *
     * @param \MusicBox\Entity\Page $page
     */
    public function delete($page)
    {
        return $this->db->delete('pages', array('page_id' => $page->getId()));
    }

    /**
     * Returns the total number of pages.
     *
     * @return integer The total number of pages.
     */
    public function getCount() {
        return $this->db->fetchColumn('SELECT COUNT(page_id) FROM pages');
    }

    /**
     * Returns an page matching the supplied id.
     *
     * @param integer $id
     *
     * @return \MusicBox\Entity\Page|false An entity object if found, false otherwise.
     */
    public function find($id)
    {
        $pageData = $this->db->fetchAssoc('SELECT * FROM pages WHERE page_id = ?', array($id));
        return $pageData ? $this->buildPage($pageData) : FALSE;
    }

    /**
     * Returns an page matching the supplied name.
     *
     * @param string $name
     *
     * @return \MusicBox\Entity\Page|false An entity object if found, false otherwise.
     */
    public function findByName($name)
    {
        $pageData = $this->db->fetchAssoc('SELECT * FROM pages WHERE title = ?', array($name));
        return $pageData ? $this->buildPage($pageData) : FALSE;
    }
    
    /**
     * Retourne la liste des pages et catégories du site avec leur rang
     * @return array $datas
     */
    public function findAllOrderPage()
    {
        $pagesData = $this->db->fetchAll('SELECT p.page_id as id, p.title as name, op.rank as rank, "1" as type_page  FROM pages p LEFT JOIN order_page op ON op.id_element = p.page_id AND op.type_page = 1');
        $categoriesData = $this->db->fetchAll('SELECT c.category_id as id, c.title as name, op.rank as rank, "2" as type_page  FROM categories c LEFT JOIN order_page op ON op.id_element = c.category_id AND op.type_page = 2');
        $lockedData = $this->db->fetchAll('SELECT op.id as id, op.title as name, op.rank as rank, "3" as type_page  FROM order_page op WHERE op.type_page = 3');
        $datas = array_merge($pagesData, $categoriesData, $lockedData);
        
        usort($datas, function($a, $b) {
            return $a['rank'] - $b['rank'];
        });
        
        return $datas;
    }
    
    public function getRankPage()
    {
        $pagesData = $this->db->fetchAll('SELECT p.page_id as id, p.title as name, op.rank as rank, op.type_page as type_page  FROM pages p INNER JOIN order_page op ON op.id_element = p.page_id AND op.type_page = 1 WHERE op.rank IS NOT NULL');
        $categoriesData = $this->db->fetchAll('SELECT c.category_id as id, c.title as name, c.abrev as abrev, op.rank as rank, op.type_page as type_page  FROM categories c INNER JOIN order_page op ON op.id_element = c.category_id AND op.type_page = 2 WHERE op.rank IS NOT NULL');
        $lockedData = $this->db->fetchAll('SELECT op.rank as id, op.title as name, op.path as link, op.rank as rank, op.type_page as type_page  FROM order_page op WHERE op.type_page = 3 AND op.rank IS NOT NULL');
        $datas = array_merge($pagesData, $categoriesData, $lockedData);
        
        usort($datas, function($a, $b) {
            return $a['rank'] - $b['rank'];
        });
        
        foreach ($datas as $key => $data) {
            if ($data['type_page'] == 1) {
                $datas[$key]['link'] = $this->app['url_generator']->generate('page', array('nom_page' => $data['name']));
            }
            if ($data['type_page'] == 2) {
                $datas[$key]['link'] = $this->app['url_generator']->generate('cat', array('abrev_cat' => $data['abrev']));
            }
            if ($data['type_page'] == 3) {
                $datas[$key]['link'] = $this->app['url_generator']->generate($data['link']);
            }
        }
        
        return $datas;
    }
    
    /**
     * Retourne la liste des underPages lié à cette page trié par rank
     * @param array<\MusicBox\Entity\UnderPage> $page
     */
    public function getRankUnderPages($page)
    {
        $underPages = $this->app['repository.underPage']->findByPageRank($page);
        return $underPages;
    }
    
    
    public function saveRankUnderPages($arrayUnderPages, $page)
    {                
        $queryBuilder = $this->db->createQueryBuilder();

        $queryBuilder->delete('under_pages')
            ->where('page_id = :page', $page->getId())
            ->setParameter('page', $page->getId());
        $statement = $queryBuilder->execute();
        
        foreach ($arrayUnderPages['Titre'] as $rank => $underPage) {
            if (!empty($arrayUnderPages['Titre'][$rank])) {
                $underPagesData['rank'] = $rank +1;
                $underPagesData['title'] = $arrayUnderPages['Titre'][$rank];
                $underPagesData['link'] = $arrayUnderPages['Lien'][$rank];
                $underPagesData['page_id'] = $page->getId();
                $this->db->insert('under_pages', $underPagesData);
            }
        }
    }

    /**
     * Returns a collection of pages, sorted by name.
     *
     * @param integer $limit
     *   The number of pages to return.
     * @param integer $offset
     *   The number of pages to skip.
     * @param array $orderBy
     *   Optionally, the order by info, in the $column => $direction format.
     *
     * @return array A collection of pages, keyed by page id.
     */
    public function findAll($limit, $offset = 0, $orderBy = array())
    {
        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder
            ->select('a.*')
            ->from('pages', 'a')
            ->setMaxResults($limit)
            ->setFirstResult($offset);
        $statement = $queryBuilder->execute();
        $pagesData = $statement->fetchAll();

        $pages = array();
        foreach ($pagesData as $pageData) {
            $pageId = $pageData['page_id'];
            $pages[$pageId] = $this->buildPage($pageData);
        }
        return $pages;
    }

    /**
     * Instantiates an page entity and sets its properties using db data.
     *
     * @param array $pageData
     *   The array of db data.
     *
     * @return \MusicBox\Entity\Page
     */
    protected function buildPage($pageData)
    {        
        $page = new Page();
        $page->setId($pageData['page_id']);
        $page->setTitle($pageData['title']);
        $page->setDescription($pageData['description']);
        $page->setContenu($pageData['contenu']);
        return $page;
    }
}
