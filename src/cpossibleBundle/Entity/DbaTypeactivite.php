<?php

namespace cpossibleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DbaTypeactivite
 *
 * @ORM\Table(name="dba_typeActivite", indexes={@ORM\Index(name="fk_dba_listeActivite_user1", columns={"typeActivite_code"})})
 * @ORM\Entity
 */
class DbaTypeactivite
{
    /**
     * @return int
     */
    public function getTypeactiviteId()
    {
        return $this->typeactiviteId;
    }

    /**
     * @param int $typeactiviteId
     */
    public function setTypeactiviteId($typeactiviteId)
    {
        $this->typeactiviteId = $typeactiviteId;
    }

    /**
     * @return string
     */
    public function getTypeactiviteCode()
    {
        return $this->typeactiviteCode;
    }

    /**
     * @param string $typeactiviteCode
     */
    public function setTypeactiviteCode($typeactiviteCode)
    {
        $this->typeactiviteCode = $typeactiviteCode;
    }

    /**
     * @return string
     */
    public function getTypeactiviteNom()
    {
        return $this->typeactiviteNom;
    }

    /**
     * @param string $typeactiviteNom
     */
    public function setTypeactiviteNom($typeactiviteNom)
    {
        $this->typeactiviteNom = $typeactiviteNom;
    }
    /**
     * @var integer
     *
     * @ORM\Column(name="typeActivite_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $typeactiviteId;

    /**
     * @var string
     *
     * @ORM\Column(name="typeActivite_code", type="string", length=10, nullable=true)
     */
    private $typeactiviteCode;

    /**
     * @var string
     *
     * @ORM\Column(name="typeActivite_nom", type="string", length=128, nullable=true)
     */
    private $typeactiviteNom;


}
