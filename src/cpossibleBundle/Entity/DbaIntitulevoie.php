<?php

namespace cpossibleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DbaIntitulevoie
 *
 * @ORM\Table(name="dba_intituleVoie")
 * @ORM\Entity
 */
class DbaIntitulevoie
{
    /**
     * @var integer
     *
     * @ORM\Column(name="intituleVoie_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $intitulevoieId;

    /**
     * @var string
     *
     * @ORM\Column(name="intituleVoie_code", type="string", length=3, nullable=true)
     */
    private $intitulevoieCode;

    /**
     * @var string
     *
     * @ORM\Column(name="intituleVoie_nom", type="string", length=45, nullable=true)
     */
    private $intitulevoieNom;


}
