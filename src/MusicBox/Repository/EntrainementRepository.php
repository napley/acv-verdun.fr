<?php

namespace MusicBox\Repository;

use Doctrine\DBAL\Connection;
use MusicBox\Entity\Entrainement;

/**
 * Entrainement repository
 */
class EntrainementRepository implements RepositoryInterface
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * Saves the entrainement to the database.
     *
     * @param \MusicBox\Entity\Entrainement $entrainement
     */
    public function save($entrainement)
    {
        $entrainementData = array(
            'title' => $entrainement->getTitle(),
            'script' => $entrainement->getScript()
        );

        if ($entrainement->getId()) {

            $this->db->update('entrainements', $entrainementData, array('entrainement_id' => $entrainement->getId()));
        }
        else {
            $this->db->insert('entrainements', $entrainementData);
            // Get the id of the newly created entrainement and set it on the entity.
            $id = $this->db->lastInsertId();
            $entrainement->setId($id);
        }
    }

    /**
     * Deletes the entrainement.
     *
     * @param \MusicBox\Entity\Entrainement $entrainement
     */
    public function delete($entrainement)
    {
        return $this->db->delete('entrainements', array('entrainement_id' => $entrainement->getId()));
    }

    /**
     * Returns the total number of entrainements.
     *
     * @return integer The total number of entrainements.
     */
    public function getCount() {
        return $this->db->fetchColumn('SELECT COUNT(entrainement_id) FROM entrainements');
    }

    /**
     * Returns an entrainement matching the supplied id.
     *
     * @param integer $id
     *
     * @return \MusicBox\Entity\Entrainement|false An entity object if found, false otherwise.
     */
    public function find($id)
    {
        $entrainementData = $this->db->fetchAssoc('SELECT * FROM entrainements WHERE entrainement_id = ?', array($id));
        return $entrainementData ? $this->buildEntrainement($entrainementData) : FALSE;
    }

    /**
     * Returns a collection of entrainements, sorted by name.
     *
     * @param integer $limit
     *   The number of entrainements to return.
     * @param integer $offset
     *   The number of entrainements to skip.
     * @param array $orderBy
     *   Optionally, the order by info, in the $column => $direction format.
     *
     * @return array A collection of entrainements, keyed by entrainement id.
     */
    public function findAll($limit = 1000, $offset = 0, $orderBy = array())
    {
        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder
            ->select('a.*')
            ->from('entrainements', 'a')
            ->setMaxResults($limit)
            ->setFirstResult($offset);
        $statement = $queryBuilder->execute();
        $entrainementsData = $statement->fetchAll();

        $entrainements = array();
        foreach ($entrainementsData as $entrainementData) {
            $entrainementId = $entrainementData['entrainement_id'];
            $entrainements[$entrainementId] = $this->buildEntrainement($entrainementData);
        }
        return $entrainements;
    }

    /**
     * Instantiates an entrainement entity and sets its properties using db data.
     *
     * @param array $entrainementData
     *   The array of db data.
     *
     * @return \MusicBox\Entity\Entrainement
     */
    protected function buildEntrainement($entrainementData)
    {        
        $entrainement = new Entrainement();
        $entrainement->setId($entrainementData['entrainement_id']);
        $entrainement->setTitle($entrainementData['title']);
        $entrainement->setScript($entrainementData['script']);
        return $entrainement;
    }
}
