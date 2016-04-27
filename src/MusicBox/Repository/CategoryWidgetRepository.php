<?php

namespace MusicBox\Repository;

use Doctrine\DBAL\Connection;
use MusicBox\Entity\CategoryWidget;

/**
 * Like repository
 */
class CategoryWidgetRepository implements RepositoryInterface
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
     * @var \MusicBox\Repository\WidgetRepository
     */
    protected $widgetRepository;

    public function __construct(Connection $db, $categoryRepository, $widgetRepository)
    {
        $this->db = $db;
        $this->categoryRepository = $categoryRepository;
        $this->widgetRepository = $widgetRepository;
    }

    /**
     * Saves the like to the database.
     *
     * @param \MusicBox\Entity\CategoryWidget $categoryWidget
     */
    public function save($categoryWidget)
    {
        $categoryWidgetData = array(
            'category_id' => $categoryWidget->getCategory()->getId(),
            'widget_id' => $categoryWidget->getWidget()->getId(),
        );

        if ($categoryWidget->getId()) {
            $this->db->update('category_widget', $categoryWidgetData, array('category_widget_id' => $categoryWidget->getId()));
        } else {
            $this->db->insert('category_widget', $categoryWidgetData);
            // Get the id of the newly created categoryWidget and set it on the entity.
            $id = $this->db->lastInsertId();
            $categoryWidget->setId($id);
        }
    }

    /**
     * Saves the like to the database.
     *
     * @param array $categoryWidget
     */
    public function saveWithId($categoryWidget)
    {
        $categoryWidgetData = array(
            'category_id' => $categoryWidget['category_id'],
            'widget_id' => $categoryWidget['widget_id'],
        );

        if ($categoryWidget['id']) {
            $this->db->update('category_widget', $categoryWidgetData, array('category_widget_id' => $categoryWidget->getId()));
        } else {
            $this->db->insert('category_widget', $categoryWidgetData);
            // Get the id of the newly created categoryWidget and set it on the entity.
            $id = $this->db->lastInsertId();
            $categoryWidget['id'];
        }
    }

    /**
     * Saves the category by widget to the database.
     *
     * @param array $categoriesId
     * @param integer $widgetId
     */
    public function clean($categoriesId, $widgetId)
    {
        if (!empty($categoriesId)){
            foreach ($categoriesId as $categoryId) {
                $existCatAct = $this->findByCategoryAndWidget($categoryId, $widgetId);
                if (empty($existCatAct)) {
                    $this->saveWithId(['category_id' => $categoryId, 'widget_id' => $widgetId]);
                }
            }
        
            $categoriesWidget = $this->findAllByWidget($widgetId, 1000, 0, ['category_widget_id' => 'DESC']);
            if (!empty($categoriesWidget)){
                foreach ($categoriesWidget as $categoryWidget) {
                    if (!in_array($categoryWidget->getCategory()->getId(), $categoriesId)) {
                        $this->delete($categoryWidget->getId());
                    }
                }
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
        return $this->db->delete('category_widget', array('category_widget_id' => $id));
    }

    /**
     * Returns the total number of category_widget.
     *
     * @return integer The total number of category_widget.
     */
    public function getCount() {
        return $this->db->fetchColumn('SELECT COUNT(category_widget_id) FROM category_widget');
    }

    /**
     * Returns a like matching the supplied id.
     *
     * @param integer $id
     *
     * @return \MusicBox\Entity\CategoryWidget|false A category_widget if found, false otherwise.
     */
    public function find($id)
    {
        $categoryWidgetData = $this->db->fetchAssoc('SELECT * FROM category_widget WHERE category_widget_id = ?', array($id));
        return $categoryWidgetData ? $this->buildCategoryWidget($categoryWidgetData) : FALSE;
    }

    /**
     * Returns a collection of category Widget for the given user id.
     *
     * @param integer $categoryId
     *   The artist id.
     * @param integer $widgetId
     *   The user id.
     *
     * @return \MusicBox\Entity\CategoryWidget|false A CategoryWidget if found, false otherwise.
     */
    public function findByCategoryAndWidget($categoryId, $widgetId)
    {
        $conditions = array(
            'category_id' => $categoryId,
            'widget_id' => $widgetId,
        );
        $categoryWidgets = $this->getCategoryWidgets($conditions, 1, 0, ['category_widget_id' => 'DESC']);
        if ($categoryWidgets) {
            return reset($categoryWidgets);
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
        return $this->getCategoryWidgets(array(), $limit, $offset, $orderBy);
    }

    /**
     * Returns a collection of category_widget for the given category id.
     *
     * @param integer $categoryId
     *   The category id.
     * @param integer $limit
     *   The number of category_widget to return.
     * @param integer $offset
     *   The number of category_widget to skip.
     * @param array $orderBy
     *   Optionally, the order by info, in the $column => $direction format.
     *
     * @return array A collection of category_widget, keyed by category_widget id.
     */
    public function findAllByCategory($categoryId, $limit, $offset = 0, $orderBy = array())
    {
        $conditions = array(
            'category_id' => $categoryId,
        );
        return $this->getCategoryWidgets($conditions, $limit, $offset, $orderBy);
    }

    /**
     * Returns a collection of category_widget for the given widget id.
     *
     * @param $widgetId
     *   The widget id.
     * @param integer $limit
     *   The number of category_widget to return.
     * @param integer $offset
     *   The number of category_widget to skip.
     * @param array $orderBy
     *   Optionally, the order by info, in the $column => $direction format.
     *
     * @return array A collection of category_widget, keyed by category_widget id.
     */
    public function findAllByWidget($widgetId, $limit = null, $offset = 0, $orderBy = array())
    {
        $conditions = array(
            'widget_id' => $widgetId,
        );
        return $this->getCategoryWidgets($conditions, $limit, $offset, $orderBy);
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
    protected function getCategoryWidgets(array $conditions, $limit, $offset, $orderBy = array())
    {
        // Provide a default orderBy.
        if (!$orderBy) {
            $orderBy = array('category_id' => 'ASC');
        }
        
        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder
            ->select('l.*')
            ->from('category_widget', 'l')
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
        $categoryWidgetsData = $statement->fetchAll();

        $categoryWidgets = array();
        foreach ($categoryWidgetsData as $categoryWidgetData) {
            $categoryWidgetId = $categoryWidgetData['category_widget_id'];
            $categoryWidgets[$categoryWidgetId] = $this->buildCategoryWidget($categoryWidgetData, $conditions);
        }
        return $categoryWidgets;
    }

    /**
     * Instantiates a like entity and sets its properties using db data.
     *
     * @param array $categoryWidgetData
     *   The array of db data.
     *
     * @return \MusicBox\Entity\CategoryWidget
     */
    protected function buildCategoryWidget($categoryWidgetData, $conditions)
    {

        $categoryWidget = new CategoryWidget();
        $categoryWidget->setId($categoryWidgetData['category_widget_id']);
        
        // Load the related artist and user.
        if (!isset($conditions['category_id'])) {
            $category = $this->categoryRepository->find($categoryWidgetData['category_id']);
            $categoryWidget->setCategory($category);   
        }
        if (!isset($conditions['widget_id'])) {
            $widget = $this->widgetRepository->find($categoryWidgetData['widget_id']);
            $categoryWidget->setWidget($widget);
        }
        return $categoryWidget;
    }
}
