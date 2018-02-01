<?php

namespace cpossibleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DbaTypedossier
 *
 * @ORM\Table(name="dba_typeDossier", indexes={@ORM\Index(name="fk_dba_typeDossier_user1", columns={"typeDossier_code"})})
 * @ORM\Entity
 */
class DbaTypedossier
{
    /**
     * @var integer
     *
     * @ORM\Column(name="typeDossier_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $typedossierId;

    /**
     * @var string
     *
     * @ORM\Column(name="typeDossier_code", type="string", length=11, nullable=true)
     */
    private $typedossierCode;

    public function getTypeDossierCode()
    {
        return $this->typedossierCode;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="typeDossier_nom", type="string", length=255, nullable=true)
     */
    private $typedossierNom;


}

