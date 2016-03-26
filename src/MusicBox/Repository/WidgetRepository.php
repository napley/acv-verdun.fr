<?php

namespace MusicBox\Repository;

use Doctrine\DBAL\Connection;
use MusicBox\Entity\Widget;

/**
 * Widget repository
 */
class WidgetRepository implements RepositoryInterface
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    public function saveOrder($data) {
        $widgets = $this->findAll(9999);
        
        foreach ($widgets as $widget) {
            $widget->setRank(null);
            $this->save($widget);
        }
        
        foreach ($data as $idWidget => $rang) {
            $OrderData = null;
            $OrderData['rank'] = ($rang == 0? null : $rang);
            $this->db->update('widgets', $OrderData, array('widget_id' => $idWidget));
        };
    }
    
    /**
     * Saves the widget to the database.
     *
     * @param \MusicBox\Entity\Widget $widget
     */
    public function save($widget)
    {
        $widgetData = array(
            'title' => $widget->getTitle(),
            'entete' => $widget->getEntete(),
            'contenu' => $widget->getContenu(),
            'rank' => $widget->getRank(),
            'on_pages' => $widget->getOnPages(),
            'on_cats' => $widget->getOnCats(),
            'locked' => $widget->getLocked()
        );

        if ($widget->getId()) {

            $this->db->update('widgets', $widgetData, array('widget_id' => $widget->getId()));
        }
        else {
            $this->db->insert('widgets', $widgetData);
            // Get the id of the newly created widget and set it on the entity.
            $id = $this->db->lastInsertId();
            $widget->setId($id);
        }
    }

    /**
     * Deletes the widget.
     *
     * @param \MusicBox\Entity\Widget $widget
     */
    public function delete($widget)
    {
        if (!$widget->getLocked()) {
            return $this->db->delete('widgets', array('widget_id' => $widget->getId()));
        }
        else {
            return true;
        }
    }

    /**
     * Returns the total number of widgets.
     *
     * @return integer The total number of widgets.
     */
    public function getCount() {
        return $this->db->fetchColumn('SELECT COUNT(widget_id) FROM widgets');
    }

    /**
     * Returns an widget matching the supplied id.
     *
     * @param integer $id
     *
     * @return \MusicBox\Entity\Widget|false An entity object if found, false otherwise.
     */
    public function find($id)
    {
        $widgetData = $this->db->fetchAssoc('SELECT * FROM widgets WHERE widget_id = ?', array($id));
        return $widgetData ? $this->buildWidget($widgetData) : FALSE;
    }

    /**
     * Returns an widget matching the supplied name.
     *
     * @param string $name
     *
     * @return \MusicBox\Entity\Widget|false An entity object if found, false otherwise.
     */
    public function findByName($name)
    {
        $widgetData = $this->db->fetchAssoc('SELECT * FROM widgets WHERE title = ?', array($name));
        return $widgetData ? $this->buildWidget($widgetData) : FALSE;
    }
    /**
     * 
     * @param int $category
     * @return type
     */
    public function findAllOrderWidget($category)
    {
        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder
            ->select('a.*')
            ->from('widgets', 'a');
        
        if (!empty($category)) {
            $queryBuilder->leftJoin('a', 'category_widget', 'cw', 'cw.widget_id = a.widget_id')
                ->where('cw.category_id = :cat')
                ->orwhere('a.on_cats = 1')
                ->setParameter('cat', $category);
        } else {
            $queryBuilder->where('a.on_pages = 1');
        }
        
        $queryBuilder->andwhere('a.rank IS NOT NULL')
            ->orderBy('a.rank','ASC');
        
        $statement = $queryBuilder->execute();
        $widgetsData = $statement->fetchAll();

        $widgets = array();
        foreach ($widgetsData as $widgetData) {
            $widgetId = $widgetData['widget_id'];
            $widgets[$widgetId] = $this->buildWidget($widgetData);
        }
        
        return $widgets;
    }

    /**
     * Returns a collection of widgets, sorted by name.
     *
     * @param integer $limit
     *   The number of widgets to return.
     * @param integer $offset
     *   The number of widgets to skip.
     * @param array $orderBy
     *   Optionally, the order by info, in the $column => $direction format.
     *
     * @return array A collection of widgets, keyed by widget id.
     */
    public function findAll($limit, $offset = 0, $orderBy = array())
    {
        // Provide a default orderBy.
        if (!$orderBy) {
            $orderBy = array('rank' => 'ASC');
        }
        
        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder
            ->select('a.*')
            ->from('widgets', 'a')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->orderBy(key($orderBy), current($orderBy));
        $statement = $queryBuilder->execute();
        $widgetsData = $statement->fetchAll();

        $widgets = array();
        foreach ($widgetsData as $widgetData) {
            $widgetId = $widgetData['widget_id'];
            $widgets[$widgetId] = $this->buildWidget($widgetData);
        }
        return $widgets;
    }

    /**
     * Instantiates an widget entity and sets its properties using db data.
     *
     * @param array $widgetData
     *   The array of db data.
     *
     * @return \MusicBox\Entity\Widget
     */
    protected function buildWidget($widgetData)
    {        
        $widget = new Widget();
        $widget->setId($widgetData['widget_id']);
        $widget->setTitle($widgetData['title']);
        $widget->setEntete($widgetData['entete']);
        $widget->setContenu($widgetData['contenu']);
        $widget->setRank($widgetData['rank']);
        $widget->setOnPages((bool) $widgetData['on_pages']);
        $widget->setOnCats((bool) $widgetData['on_cats']);
        $widget->setLocked((bool) $widgetData['locked']);
        
        if (!empty($widgetData['course_id'])) {
            $categories = $this->app['repository.categoryWidget']->findAllByCourse($widgetData['widget_id']);
            $widget->setCategories($categories);
        } else {
            $widget->setCategories(null);
        }
        
        return $widget;
    }
    
    public function getWidgetCode($name)
    {
        $widget = $this->findByName($name);
        
        if (!empty($widget) && !empty($widget->getContenu())){
            $codeHtml = '<div class="panel-box">
                <div class="titles">
                    <h4>'.$widget->getEntete().'</h4>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        '.$widget->getContenu().'
                    </div>
                </div>
            </div>';
        } else {
            $codeHtml = '';
        }
        
        return $codeHtml;
    }
}
