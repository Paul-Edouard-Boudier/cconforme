<?php

namespace cpossibleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DbaTypeerp
 *
 * @ORM\Table(name="dba_typeERP", indexes={@ORM\Index(name="fk_dba_typeERP_user1", columns={"typeERP_code"})})
 * @ORM\Entity
 */
class DbaTypeerp
{
    /**
     * @var integer
     *
     * @ORM\Column(name="typeERP_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $typeerpId;

    /**
     * @var string
     *
     * @ORM\Column(name="typeERP_code", type="string", length=5, nullable=true)
     */
    private $typeerpCode;

    public function getTypeErpCode()
    {
        return $this->typeerpCode;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="typeERP_nom", type="string", length=25, nullable=true)
     */
    private $typeerpNom;


}

