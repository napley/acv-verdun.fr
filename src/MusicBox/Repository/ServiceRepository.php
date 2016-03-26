<?php

namespace MusicBox\Repository;

use Doctrine\DBAL\Connection;
use MusicBox\Entity\Service;

/**
 * Service repository
 */
class ServiceRepository implements RepositoryInterface
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
     * Saves the service to the database.
     *
     * @param \MusicBox\Entity\Service $service
     */
    public function save($service)
    {
        $serviceData = array(
            'title' => $service->getTitle(),
            'description' => $service->getDescription(),
            'utilisateur' => $service->getUtilisateur(),
            'password' => $service->getPassword()
        );

        if ($service->getId()) {

            $this->db->update('services', $serviceData, array('service_id' => $service->getId()));
        }
        else {
            $this->db->insert('services', $serviceData);
            // Get the id of the newly created service and set it on the entity.
            $id = $this->db->lastInsertId();
            $service->setId($id);
        }
    }

    /**
     * Deletes the service.
     *
     * @param \MusicBox\Entity\Service $service
     */
    public function delete($service)
    {
        return $this->db->delete('services', array('service_id' => $service->getId()));
    }

    /**
     * Returns the total number of services.
     *
     * @return integer The total number of services.
     */
    public function getCount() {
        return $this->db->fetchColumn('SELECT COUNT(service_id) FROM services');
    }

    /**
     * Returns an service matching the supplied id.
     *
     * @param integer $id
     *
     * @return \MusicBox\Entity\Service|false An entity object if found, false otherwise.
     */
    public function find($id)
    {
        $serviceData = $this->db->fetchAssoc('SELECT * FROM services WHERE service_id = ?', array($id));
        return $serviceData ? $this->buildService($serviceData) : FALSE;
    }

    /**
     * Returns an service matching the supplied id.
     *
     * @param integer $id
     *
     * @return \MusicBox\Entity\Service|false An entity object if found, false otherwise.
     */
    public function findByNom($nom)
    {
        $serviceData = $this->db->fetchAssoc('SELECT * FROM services WHERE title = ?', array($nom));
        return $serviceData ? $this->buildService($serviceData) : FALSE;
    }

    /**
     * Returns a collection of services, sorted by name.
     *
     * @param integer $limit
     *   The number of services to return.
     * @param integer $offset
     *   The number of services to skip.
     * @param array $orderBy
     *   Optionally, the order by info, in the $column => $direction format.
     *
     * @return array A collection of services, keyed by service id.
     */
    public function findAll($limit = 1000, $offset = 0, $orderBy = array())
    {
        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder
            ->select('a.*')
            ->from('services', 'a')
            ->setMaxResults($limit)
            ->setFirstResult($offset);
        $statement = $queryBuilder->execute();
        $servicesData = $statement->fetchAll();

        $services = array();
        foreach ($servicesData as $serviceData) {
            $serviceId = $serviceData['service_id'];
            $services[$serviceId] = $this->buildService($serviceData);
        }
        return $services;
    }

    /**
     * Instantiates an service entity and sets its properties using db data.
     *
     * @param array $serviceData
     *   The array of db data.
     *
     * @return \MusicBox\Entity\Service
     */
    protected function buildService($serviceData)
    {        
        $service = new Service();
        $service->setId($serviceData['service_id']);
        $service->setTitle($serviceData['title']);
        $service->setDescription($serviceData['description']);
        $service->setUtilisateur($serviceData['utilisateur']);
        $service->setPassword($serviceData['password']);
        return $service;
    }
}
