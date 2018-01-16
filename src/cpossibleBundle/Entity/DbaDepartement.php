<?php

namespace cpossibleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DbaDepartement
 *
 * @ORM\Table(name="dba_departement", indexes={@ORM\Index(name="fk_dba_departement_user1_idx", columns={"departement_code"}), @ORM\Index(name="fk_dba_departement_user2_idx", columns={"departement_nom"}), @ORM\Index(name="fk_dba_departement_ext1_idx", columns={"departement_slug"}), @ORM\Index(name="fk_dba_departement_ext2_idx", columns={"departement_nom_soundex"})})
 * @ORM\Entity
 */
class DbaDepartement
{
    /**
     * @var integer
     *
     * @ORM\Column(name="departement_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $departementId;

    /**
     * @var string
     *
     * @ORM\Column(name="departement_code", type="string", length=3, nullable=true)
     */
    private $departementCode;

    /**
     * @var string
     *
     * @ORM\Column(name="departement_nom", type="string", length=45, nullable=true)
     */
    private $departementNom;

    /**
     * @var string
     *
     * @ORM\Column(name="departement_nom_uppercase", type="string", length=45, nullable=true)
     */
    private $departementNomUppercase;

    /**
     * @var string
     *
     * @ORM\Column(name="departement_slug", type="string", length=45, nullable=true)
     */
    private $departementSlug;

    /**
     * @var string
     *
     * @ORM\Column(name="departement_message", type="string", length=45, nullable=true)
     */
    private $departementMessage;

    /**
     * @var string
     *
     * @ORM\Column(name="departement_nom_soundex", type="string", length=45, nullable=true)
     */
    private $departementNomSoundex;

    /**
     * @var string
     *
     * @ORM\Column(name="departement_token", type="string", length=45, nullable=true)
     */
    private $departementToken;

    /**
     * @var string
     *
     * @ORM\Column(name="departement_procedure", type="string", length=45, nullable=true)
     */
    private $departementProcedure;

    /**
     * @return string
     */
    public function getDepartementToken()
    {
        return $this->departementToken;
    }

    /**
     * @return string
     */
    public function getDepartementProcedure()
    {
        return $this->departementProcedure;
    }

    /**
     * @return string
     */
    public function getDepartementCode()
    {
        return $this->departementCode;
    }

    /**
     * @return string
     */
    public function getDepartementNom()
    {
        return $this->departementNom;
    }

    /**
     * @param string $token
     */
    // public function setDepartementToken($token)
    // {
    //     $this->listeerpId = $token;
    // }

    /**
     * @param string $procedure
     */
    // public function setDepartementProcedure($procedure)
    // {
    //     $this->listeerpId = $procedure;
    // }


}
