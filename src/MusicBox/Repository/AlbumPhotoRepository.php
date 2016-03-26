<?php

namespace MusicBox\Repository;

use Doctrine\DBAL\Connection;
use MusicBox\Entity\AlbumPhoto;

/**
 * AlbumPhoto repository
 */
class AlbumPhotoRepository implements RepositoryInterface
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
     * Saves the albumPhoto to the database.
     *
     * @param \MusicBox\Entity\AlbumPhoto $albumPhoto
     */
    public function save($albumPhoto)
    {
        $albumPhotoData = array(
            'title' => $albumPhoto->getTitle(),
            'description' => $albumPhoto->getDescription(),
            'created_at' => $albumPhoto->getCreatedAt()->format('Y-m-d H:i:s')
        );

        if ($albumPhoto->getId()) {

            $this->db->update('albumPhotos', $albumPhotoData, array('albumPhoto_id' => $albumPhoto->getId()));
        } else {
            $this->db->insert('albumPhotos', $albumPhotoData);
            // Get the id of the newly created albumPhoto and set it on the entity.
            $id = $this->db->lastInsertId();
            $albumPhoto->setId($id);
        }
    }

    /**
     * Deletes the albumPhoto.
     *
     * @param \MusicBox\Entity\AlbumPhoto $albumPhoto
     */
    public function delete($albumPhoto)
    {
        return $this->db->delete('albumPhotos', array('albumPhoto_id' => $albumPhoto->getId()));
    }

    /**
     * Returns the total number of albumPhotos.
     *
     * @return integer The total number of albumPhotos.
     */
    public function getCount()
    {
        return $this->db->fetchColumn('SELECT COUNT(albumPhoto_id) FROM albumPhotos');
    }

    /**
     * 
     */
    public function getOldAlbumPhoto($oldAlbumPhotoId)
    {
        $oldAlbumPhoto = $this->db->fetchAssoc('SELECT * FROM albumFoto af  WHERE af.id = ?', array($oldAlbumPhotoId));
        if (!empty($oldAlbumPhoto['created'])) {
            $oldAlbumPhoto['created'] = new \DateTime($oldAlbumPhoto['created']);
        }
        return $oldAlbumPhoto;
    }
    
    public function setDateCreated($createdDate, $idAlbumPhoto) {
        $count = $this->db->executeUpdate('UPDATE albumFoto SET created = ? WHERE id = ?', array($createdDate->format('Y-m-d'), $idAlbumPhoto));
        if ($count == 1) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Récupération donnée ancien album photo
     */
    public function migrOldAlbumPhoto($oldAlbumPhoto)
    {
        $migrCat = $this->db->fetchAssoc('SELECT * FROM migr_category WHERE old_id = ?', array($oldAlbumPhoto['id_categorie']));
        
        $albumPhotoData = [];
        $albumPhotoData['albumPhoto_id'] = $oldAlbumPhoto['id'];
        $albumPhotoData['title'] = $oldAlbumPhoto['nom'];
        $albumPhotoData['description'] = $oldAlbumPhoto['description'];
        $albumPhotoData['created_at'] = $oldAlbumPhoto['created']->format('Y-m-d H:i:s');
        $this->db->insert('albumPhotos', $albumPhotoData);

        if (!empty($migrCat['new_id'])) {
            $categoryAlbumPhotoData = array(
                'category_id' => $migrCat['new_id'],
                'albumPhoto_id' => $albumPhotoData['albumPhoto_id']
            );
            $this->db->insert('category_albumPhoto', $categoryAlbumPhotoData);
        }

        $preDir = __DIR__ . "/../../../..";
        $dir = "/admin/ckfinder/userfiles/images/galerie/" . $oldAlbumPhoto['id'];
        $dirDest = "/update/web/server/php/files/" . $oldAlbumPhoto['id'];

        $files = scandir($preDir . $dir);

        mkdir($preDir . $dirDest);
        mkdir($preDir . $dirDest . "/thumbnail/");
        $tabPhotos = [];
        foreach ($files as $file) {
            if (!empty($file) && $file != '.' && $file != '..' && $file != 'thumbnail') {
                $tabPhotos[] = ['file' => $dir . "/" . $file];
                copy($preDir . $dir . "/" . $file, $preDir . $dirDest . "/" . $file);
                $this->imagethumb($preDir . $dir . "/" . $file, $preDir . $dirDest . "/thumbnail/" . $file, 250);
            }
        }
        return true;
    }

    /**
     * Returns the total number of albumPhotos.
     *
     * @return integer The total number of albumPhotos.
     */
    public function getCountYear($year)
    {
        return $this->db->fetchColumn('SELECT COUNT(albumPhoto_id) FROM albumPhotos WHERE YEAR(created_at) = ' . $year);
    }

    /**
     * Returns an albumPhoto matching the supplied id.
     *
     * @param integer $id
     *
     * @return \MusicBox\Entity\AlbumPhoto|false An entity object if found, false otherwise.
     */
    public function find($id)
    {
        $albumPhotoData = $this->db->fetchAssoc('SELECT * FROM albumPhotos WHERE albumPhoto_id = ?', array($id));
        return $albumPhotoData ? $this->buildAlbumPhoto($albumPhotoData) : FALSE;
    }

    /**
     * Returns a collection of albumPhotos, sorted by name.
     *
     * @param integer $limit
     *   The number of albumPhotos to return.
     * @param integer $offset
     *   The number of albumPhotos to skip.
     * @param array $orderBy
     *   Optionally, the order by info, in the $column => $direction format.
     *
     * @return array A collection of albumPhotos, keyed by albumPhoto id.
     */
    public function findByYear($annee = null, $limit = 999999999, $offset = 0, $orderBy = array())
    {
        // Provide a default orderBy.
        if (!$orderBy) {
            $orderBy = array('created_at' => 'DESC');
        }

        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder
                ->select('a.*')
                ->from('albumPhotos', 'a')
                ->where('YEAR(a.created_at) = :annee')
                ->setMaxResults($limit)
                ->setFirstResult($offset)
                ->orderBy('a.' . key($orderBy), current($orderBy))
                ->setParameter('annee', $annee);

        $statement = $queryBuilder->execute();
        $albumPhotosData = $statement->fetchAll();

        $albumPhotos = array();
        foreach ($albumPhotosData as $albumPhotoData) {
            $albumPhotoId = $albumPhotoData['albumPhoto_id'];
            $albumPhotos[$albumPhotoId] = $this->buildAlbumPhoto($albumPhotoData);
        }

        return $albumPhotos;
    }

    /**
     * Returns a collection of albumPhotos, sorted by name.
     *
     * @param integer $limit
     *   The number of albumPhotos to return.
     * @param integer $offset
     *   The number of albumPhotos to skip.
     * @param array $orderBy
     *   Optionally, the order by info, in the $column => $direction format.
     *
     * @return array A collection of albumPhotos, keyed by albumPhoto id.
     */
    public function findAll($limit, $offset = 0, $orderBy = array())
    {
        // Provide a default orderBy.
        if (!$orderBy) {
            $orderBy = array('created_at' => 'DESC');
        }

        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder
                ->select('a.*')
                ->from('albumPhotos', 'a')
                ->setMaxResults($limit)
                ->setFirstResult($offset)
                ->orderBy('a.' . key($orderBy), current($orderBy));
        $statement = $queryBuilder->execute();
        $albumPhotosData = $statement->fetchAll();

        $albumPhotos = array();
        foreach ($albumPhotosData as $albumPhotoData) {
            $albumPhotoId = $albumPhotoData['albumPhoto_id'];
            $albumPhotos[$albumPhotoId] = $this->buildAlbumPhoto($albumPhotoData);
        }
        return $albumPhotos;
    }

    /**
     * Returns a collection of albumPhotos, sorted by name.
     *
     * @param integer $limit
     *   The number of albumPhotos to return.
     * @param integer $offset
     *   The number of albumPhotos to skip.
     * @param array $orderBy
     *   Optionally, the order by info, in the $column => $direction format.
     *
     * @return array A collection of albumPhotos, keyed by albumPhoto id.
     */
    public function findAllOld($limit, $offset = 0, $orderBy = array())
    {
        // Provide a default orderBy.
        if (!$orderBy) {
            $orderBy = array('created' => 'DESC');
        }

        $OldAlbum = $this->db->fetchAll('SELECT a.id, a.nom, a.created FROM albumFoto a LEFT JOIN albumPhotos aa ON aa.albumPhoto_id = a.id WHERE aa.albumPhoto_id IS NULL');
        return $OldAlbum;
    }

    /**
     * Instantiates an albumPhoto entity and sets its properties using db data.
     *
     * @param array $albumPhotoData
     *   The array of db data.
     *
     * @return \MusicBox\Entity\AlbumPhoto
     */
    protected function buildAlbumPhoto($albumPhotoData)
    {
        $albumPhoto = new AlbumPhoto();
        $albumPhoto->setId($albumPhotoData['albumPhoto_id']);
        $albumPhoto->setTitle($albumPhotoData['title']);
        $albumPhoto->setDescription($albumPhotoData['description']);
        $createdAt = new \DateTime($albumPhotoData['created_at']);
        $albumPhoto->setCreatedAt($createdAt);

        if (!empty($albumPhotoData['albumPhoto_id'])) {
            $categories = $this->app['repository.categoryAlbumPhoto']->findAllByAlbumPhoto($albumPhotoData['albumPhoto_id']);
            $albumPhoto->setCategories($categories);
        } else {
            $albumPhoto->setCategories(null);
        }

        return $albumPhoto;
    }

    function imagethumb($image_src, $image_dest = NULL, $max_size = 100, $expand = FALSE, $square = FALSE)
    {
        if (!file_exists($image_src))
            return FALSE;

        // Récupère les infos de l'image
        $fileinfo = getimagesize($image_src);
        if (!$fileinfo)
            return FALSE;

        $width = $fileinfo[0];
        $height = $fileinfo[1];
        $type_mime = $fileinfo['mime'];
        $type = str_replace('image/', '', $type_mime);

        if (!$expand && max($width, $height) <= $max_size && (!$square || ($square && $width == $height) )) {
            // L'image est plus petite que max_size
            if ($image_dest) {
                return copy($image_src, $image_dest);
            } else {
                header('Content-Type: ' . $type_mime);
                return (boolean) readfile($image_src);
            }
        }

        // Calcule les nouvelles dimensions
        $ratio = $width / $height;

        if ($square) {
            $new_width = $new_height = $max_size;

            if ($ratio > 1) {
                // Paysage
                $src_y = 0;
                $src_x = round(($width - $height) / 2);

                $src_w = $src_h = $height;
            } else {
                // Portrait
                $src_x = 0;
                $src_y = round(($height - $width) / 2);

                $src_w = $src_h = $width;
            }
        } else {
            $src_x = $src_y = 0;
            $src_w = $width;
            $src_h = $height;

            if ($ratio > 1) {
                // Paysage
                $new_width = $max_size;
                $new_height = round($max_size / $ratio);
            } else {
                // Portrait
                $new_height = $max_size;
                $new_width = round($max_size * $ratio);
            }
        }

        // Ouvre l'image originale
        $func = 'imagecreatefrom' . $type;
        if (!function_exists($func))
            return FALSE;

        $image_src = $func($image_src);
        $new_image = imagecreatetruecolor($new_width, $new_height);

        // Gestion de la transparence pour les png
        if ($type == 'png') {
            imagealphablending($new_image, false);
            if (function_exists('imagesavealpha'))
                imagesavealpha($new_image, true);
        }

        // Gestion de la transparence pour les gif
        elseif ($type == 'gif' && imagecolortransparent($image_src) >= 0) {
            $transparent_index = imagecolortransparent($image_src);
            $transparent_color = imagecolorsforindex($image_src, $transparent_index);
            $transparent_index = imagecolorallocate($new_image, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);
            imagefill($new_image, 0, 0, $transparent_index);
            imagecolortransparent($new_image, $transparent_index);
        }

        // Redimensionnement de l'image
        imagecopyresampled(
                $new_image, $image_src, 0, 0, $src_x, $src_y, $new_width, $new_height, $src_w, $src_h
        );

        // Enregistrement de l'image
        $func = 'image' . $type;
        if ($image_dest) {
            $func($new_image, $image_dest);
        } else {
            header('Content-Type: ' . $type_mime);
            $func($new_image);
        }

        // Libération de la mémoire
        imagedestroy($new_image);

        return TRUE;
    }

}
