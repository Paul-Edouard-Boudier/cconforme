<?php

namespace cpossibleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Commune
 *
 * @ORM\Table(name="commune")
 * @ORM\Entity(repositoryClass="cpossibleBundle\Repository\CommuneRepository")
 */
class Commune
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
     * @ORM\Column(name="Commune_insee", type="integer")
     */
    private $codeInsee;

    /**
     * @var int
     *
     * @ORM\Column(name="Commune_cp", type="integer")
     */
    private $codePostal;

    /**
     * @var string
     *
     * @ORM\Column(name="Commune_nom", type="string", length=200)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="Commune_nom_google", type="string", length=200)
     */
    private $nomGoogle;


    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set codeInsee.
     *
     * @param int $codeInsee
     *
     * @return Commune
     */
    public function setCodeInsee($codeInsee)
    {
        $this->codeInsee = $codeInsee;

        return $this;
    }

    /**
     * Get codeInsee.
     *
     * @return int
     */
    public function getCodeInsee()
    {
        return $this->codeInsee;
    }

    /**
     * Set codePostal.
     *
     * @param int $codePostal
     *
     * @return Commune
     */
    public function setCodePostal($codePostal)
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    /**
     * Get codePostal.
     *
     * @return int
     */
    public function getCodePostal()
    {
        return $this->codePostal;
    }

    /**
     * Set nom.
     *
     * @param string $nom
     *
     * @return Commune
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom.
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Get nomGoogle.
     *
     * @return string
     */
    public function getNomGoogle()
    {
        return $this->nomGoogle;
    }

    /**
     * Set nom.
     *
     * @param string $nomGoogle
     *
     * @return Commun
     */
    public function setNomGoogle($nomGoogle)
    {
        $this->nomGoogle = $nomGoogle;

        return $this;
    }

}
