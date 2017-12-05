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

