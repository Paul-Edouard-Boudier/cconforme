<?php

namespace cpossibleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DbaCategorie
 *
 * @ORM\Table(name="dba_categorie")
 * @ORM\Entity
 */
class DbaCategorie
{
    /**
     * @var integer
     *
     * @ORM\Column(name="categorie_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $categorieId;

    /**
     * @var string
     *
     * @ORM\Column(name="categorie_code", type="string", length=4, nullable=true)
     */
    private $categorieCode;

    /**
     * @var string
     *
     * @ORM\Column(name="categorie_nom", type="string", length=45, nullable=true)
     */
    private $categorieNom;

    /**
     * @return string
     */
    public function getCategorieCode()
    {
        return $this->categorieCode;
    }

    /**
     * @return string
     */
    public function getCategorieNom()
    {
        return $this->categorieNom;
    }

    /**
     * @return string
     */
    public function getCategorieId()
    {
        return $this->categorieId;
    }
}

