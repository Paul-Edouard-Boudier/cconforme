<?php

namespace cpossibleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DbaTypestatut
 *
 * @ORM\Table(name="dba_typeStatut")
 * @ORM\Entity
 */
class DbaTypestatut
{
    /**
     * @var integer
     *
     * @ORM\Column(name="typeStatut_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $typestatutId;

    /**
     * @var string
     *
     * @ORM\Column(name="typeStatut_code", type="string", length=5, nullable=true)
     */
    private $typestatutCode;

    /**
     * @var string
     *
     * @ORM\Column(name="typeStatut_nom", type="string", length=25, nullable=true)
     */
    private $typestatutNom;


}

