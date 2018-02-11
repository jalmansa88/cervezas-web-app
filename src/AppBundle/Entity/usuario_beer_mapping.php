<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * usuario_beer_mapping
 *
 * @ORM\Table(name="usuario_beer_mapping")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\usuario_beer_mappingRepository")
 */
class usuario_beer_mapping
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userId;

    /**
     * @var int
     *
     * @ORM\Column(name="cerveza_id", type="integer")
     */
    private $cervezaId;

    /**
     * @var string
     * 
     * @ORM\Column(name="notes", type="string", length=255)
     */
    private $notes;
    
    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     *
     * @return usuario_beer_mapping
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set cervezaId
     *
     * @param integer $cervezaId
     *
     * @return usuario_beer_mapping
     */
    public function setCervezaId($cervezaId)
    {
        $this->cervezaId = $cervezaId;

        return $this;
    }

    /**
     * Get cervezaId
     *
     * @return int
     */
    public function getCervezaId()
    {
        return $this->cervezaId;
    }
    
    /**
    * Set notes
    *
    * @param string $notes
    *
    * @return usuario_beer_mapping
    */
    public function setNotes($notes)
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * Get notes
     *
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }
}

