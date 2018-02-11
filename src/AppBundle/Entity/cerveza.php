<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * cerveza
 *
 * @ORM\Table(name="cerveza")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\cervezaRepository")
 */
class cerveza
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
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255)
     */
    private $nombre;

    /**
     * @var float
     *
     * @ORM\Column(name="alc", type="float")
     */
    private $alc;


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
     * Set nombre
     *
     * @param string $nombre
     *
     * @return cerveza
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set alc
     *
     * @param float $alc
     *
     * @return cerveza
     */
    public function setAlc($alc)
    {
        $this->alc = $alc;

        return $this;
    }

    /**
     * Get alc
     *
     * @return float
     */
    public function getAlc()
    {
        return $this->alc;
    }
}

