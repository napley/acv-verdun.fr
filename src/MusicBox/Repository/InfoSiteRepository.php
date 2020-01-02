<?php

namespace MusicBox\Repository;

use Doctrine\DBAL\Connection;
use MusicBox\Entity\InfoSite;

/**
 * InfoSite repository
 */
class InfoSiteRepository implements RepositoryInterface
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
     * Saves the infosite to the database.
     *
     * @param \MusicBox\Entity\InfoSite $infosite
     */
    public function save($infosite)
    {
        $infositeData = array(
            'nom' => $infosite->getNom(),
            'description' => $infosite->getDescription(),
            'valeur' => $infosite->getValeur(),
            'img' => $infosite->getImg()
        );

        if ($infosite->getId()) {

            $this->db->update('infosites', $infositeData, array('infosite_id' => $infosite->getId()));
        }
        else {
            $this->db->insert('infosites', $infositeData);
            // Get the id of the newly created infosite and set it on the entity.
            $id = $this->db->lastInsertId();
            $infosite->setId($id);
        }
    }

    /**
     * Deletes the infosite.
     *
     * @param \MusicBox\Entity\InfoSite $infosite
     */
    public function delete($infosite)
    {
        return $this->db->delete('infosites', array('infosite_id' => $infosite->getId()));
    }

    /**
     * Returns the total number of infosites.
     *
     * @return integer The total number of infosites.
     */
    public function getCount() {
        return $this->db->fetchColumn('SELECT COUNT(infosite_id) FROM infosites');
    }

    /**
     * Returns an infosite matching the supplied id.
     *
     * @param integer $id
     *
     * @return \MusicBox\Entity\InfoSite|false An entity object if found, false otherwise.
     */
    public function find($id)
    {
        $infositeData = $this->db->fetchAssoc('SELECT * FROM infosites WHERE infosite_id = ?', array($id));
        return $infositeData ? $this->buildInfoSite($infositeData) : FALSE;
    }

    /**
     * Returns an infosite matching the supplied id.
     *
     * @param integer $id
     *
     * @return \MusicBox\Entity\InfoSite|false An entity object if found, false otherwise.
     */
    public function findByNom($nom)
    {
        $infositeData = $this->db->fetchAssoc('SELECT * FROM infosites WHERE nom = ?', array($nom));
        return $infositeData ? $this->buildInfoSite($infositeData) : FALSE;
    }

    /**
     * Returns a collection of infosites, sorted by name.
     *
     * @param integer $limit
     *   The number of infosites to return.
     * @param integer $offset
     *   The number of infosites to skip.
     * @param array $orderBy
     *   Optionally, the order by info, in the $column => $direction format.
     *
     * @return array A collection of infosites, keyed by infosite id.
     */
    public function findAll($limit = 1000, $offset = 0, $orderBy = array())
    {
        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder
            ->select('a.*')
            ->from('infosites', 'a')
            ->setMaxResults($limit)
            ->setFirstResult($offset);
        $statement = $queryBuilder->execute();
        $infositesData = $statement->fetchAll();

        $infosites = array();
        foreach ($infositesData as $infositeData) {
            $infositeId = $infositeData['infosite_id'];
            $infosites[$infositeId] = $this->buildInfoSite($infositeData);
        }
        return $infosites;
    }

    /**
     * Instantiates an infosite entity and sets its properties using db data.
     *
     * @param array $infositeData
     *   The array of db data.
     *
     * @return \MusicBox\Entity\InfoSite
     */
    protected function buildInfoSite($infositeData)
    {        
        $infosite = new InfoSite();
        $infosite->setId($infositeData['infosite_id']);
        $infosite->setNom($infositeData['nom']);
        $infosite->setDescription($infositeData['description']);
        $infosite->setValeur($infositeData['valeur']);
        $infosite->setImg($infositeData['img']);
        return $infosite;
    }
}
