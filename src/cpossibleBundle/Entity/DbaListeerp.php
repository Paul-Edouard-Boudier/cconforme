<?php

namespace cpossibleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

// OK so i know this entity has been written like garbage (same goes for the others, but the one I wrote), i mean, check those names, it's pure nonsens, however, it wasn't me but two 'professional symfony dev' that did it and writting everything back would have take me so much time, I just could not.

/**
 * DbaListeerp
 *
 * @ORM\Table(name="dba_listeERP", indexes={@ORM\Index(name="fk_dba_listeERP_user1_idx", columns={"listeERP_demandeur"}), @ORM\Index(name="fk_dba_listeERP_user2_idx", columns={"liste_ERP_nom_erp"}), @ORM\Index(name="fk_dba_listeERP_ext1_idx", columns={"listeERP_type"}), @ORM\Index(name="fk_dba_listeERP_ext2_idx", columns={"listeERP_numero_voie"}), @ORM\Index(name="fk_dba_listeERP_ext3_idx", columns={"listeERP_complement_voie"}), @ORM\Index(name="fk_dba_listeERP_ext4_idx", columns={"listeERP_nom_voie"}), @ORM\Index(name="fk_dba_listeERP_ext5_idx", columns={"listeERP_alias_nom_voie"}), @ORM\Index(name="fk_dba_listeERP_ext6_idx", columns={"listeERP_lieu_dit"}), @ORM\Index(name="fk_dba_listeERP_ext7_idx", columns={"listeERP_code_postal"}), @ORM\Index(name="fk_dba_listeERP_ext8_idx", columns={"listeERP_nom_commune"}), @ORM\Index(name="fk_dba_listeERP_ext9_idx", columns={"listeERP_departement"}), @ORM\Index(name="listeERP_siret", columns={"listeERP_siret"})})
 * @ORM\Entity
 */
class DbaListeerp
{
    /**
     * @var integer
     *
     * @ORM\Column(name="listeERP_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $listeerpId;

    /**
     * @var string
     *
     * @ORM\Column(name="listeERP_typeDossier", type="string", length=15, nullable=true)
     */
    private $listeerpTypedossier;

    /**
     * @var string
     *
     * @ORM\Column(name="listeERP_id_adap", type="string", length=45, nullable=true)
     */
    private $listeerpIdAdap;

    /**
     * @var string
     *
     * @ORM\Column(name="listeERP_demandeur", type="string", length=255, nullable=true)
     */
    private $listeerpDemandeur;

    /**
     * @var string
     *
     * @ORM\Column(name="liste_ERP_nom_erp", type="string", length=255, nullable=true)
     */
    private $listeErpNomErp;

    /**
     * @var string
     *
     * @ORM\Column(name="listeERP_nature", type="string", length=5, nullable=true)
     */
    private $listeerpNature;

    /**
     * @var string
     *
     * @ORM\Column(name="listeERP_categorie", type="string", length=10, nullable=true)
     */
    private $listeerpCategorie;

    /**
     * @var string
     *
     * @ORM\Column(name="listeERP_type", type="string", length=10, nullable=true)
     */
    private $listeerpType;

    /**
     * @var string
     *
     * @ORM\Column(name="listeERP_date_valid_adap", type="string", length=15, nullable=true)
     */
    private $listeerpDateValidAdap;

    /**
     * @var string
     *
     * @ORM\Column(name="listeERP_delai_adap", type="string", length=2, nullable=true)
     */
    private $listeerpDelaiAdap;

    /**
     * @var string
     *
     * @ORM\Column(name="listeERP_id_ign", type="string", length=25, nullable=true)
     */
    private $listeerpIdIgn;

    /**
     * @var string
     *
     * @ORM\Column(name="listeERP_siret", type="string", length=14, nullable=false)
     */
    private $listeerpSiret;

    /**
     * @var string
     *
     * @ORM\Column(name="listeERP_numero_voie", type="string", length=5, nullable=true)
     */
    private $listeerpNumeroVoie;

    /**
     * @var string
     *
     * @ORM\Column(name="listeERP_numero_complement", type="string", length=10, nullable=true)
     */
    private $listeerpNumeroComplement;

    /**
     * @var string
     *
     * @ORM\Column(name="listeERP_complement_voie", type="string", length=255, nullable=true)
     */
    private $listeerpComplementVoie;

    /**
     * @var string
     *
     * @ORM\Column(name="listeERP_nom_voie", type="string", length=255, nullable=true)
     */
    private $listeerpNomVoie;

    /**
     * @var string
     *
     * @ORM\Column(name="listeERP_alias_nom_voie", type="string", length=255, nullable=true)
     */
    private $listeerpAliasNomVoie;

    /**
     * @var string
     *
     * @ORM\Column(name="listeERP_lieu_dit", type="string", length=255, nullable=true)
     */
    private $listeerpLieuDit;

    /**
     * @var string
     *
     * @ORM\Column(name="listeERP_code_postal", type="string", length=10, nullable=true)
     */
    private $listeerpCodePostal;

    /**
     * @var string
     *
     * @ORM\Column(name="listeERP_code_insee", type="string", length=10, nullable=true)
     */
    private $listeerpCodeInsee;

    /**
     * @var string
     *
     * @ORM\Column(name="listeERP_nom_commune", type="string", length=255, nullable=true)
     */
    private $listeerpNomCommune;

    /**
     * @var string
     *
     * @ORM\Column(name="listeERP_departement", type="string", length=255, nullable=true)
     */
    private $listeerpDepartement;

    /**
     * @var integer
     *
     * @ORM\Column(name="listeERP_statut", type="integer", nullable=true)
     */
    private $listeerpStatut;

    /**
     * @var double
     *
     * @ORM\Column(name="listeERP_Longitude", type="double", nullable=true)
     */
    private $listeerpLongitude;

    /**
     * @var double
     *
     * @ORM\Column(name="listeERP_Latitude", type="double", nullable=true)
     */
    private $listeerpLatitude;

    /**
     * @var integer
     *
     * @ORM\Column(name="listeERP_dossier_tps", type="integer", nullable=true)
     */
    private $listeerpDossierTps;

    /**
     * @var string
     *
     * @ORM\Column(name="listeERP_adresse_temporaire", type="string", length=255, nullable=true)
     */
    private $listeerpAdresseTemporaire;

    /**
     * @return string
     */
    public function getListeerpAdresseTemporaire()
    {
        return $this->listeerpAdresseTemporaire;
    }

    /**
     * @param string $listeerpAdresseTemporaire
     */
    public function setListeerpAdresseTemporaire($listeerpAdresseTemporaire)
    {
        $this->listeerpAdresseTemporaire = $listeerpAdresseTemporaire;
    }    

    /**
     * @return int
     */
    public function getListeerpDossierTps()
    {
        return $this->listeerpDossierTps;
    }

    /**
     * @param int $listeerpDossierTps
     */
    public function setListeerpDossierTps($listeerpDossierTps)
    {
        $this->listeerpDossierTps = $listeerpDossierTps;
    }

    /**
     * @return int
     */
    public function getListeerpId()
    {
        return $this->listeerpId;
    }

    /**
     * @param int $listeerpId
     */
    public function setListeerpId($listeerpId)
    {
        $this->listeerpId = $listeerpId;
    }

    /**
     * @return string
     */
    public function getListeerpTypedossier()
    {
        return $this->listeerpTypedossier;
    }

    /**
     * @param string $listeerpTypedossier
     */
    public function setListeerpTypedossier($listeerpTypedossier)
    {
        $this->listeerpTypedossier = $listeerpTypedossier;
    }

    /**
     * @return string
     */
    public function getListeerpIdAdap()
    {
        return $this->listeerpIdAdap;
    }

    /**
     * @param string $listeerpIdAdap
     */
    public function setListeerpIdAdap($listeerpIdAdap)
    {
        $this->listeerpIdAdap = $listeerpIdAdap;
    }

    /**
     * @return string
     */
    public function getListeerpDemandeur()
    {
        return $this->listeerpDemandeur;
    }

    /**
     * @param string $listeerpDemandeur
     */
    public function setListeerpDemandeur($listeerpDemandeur)
    {
        $this->listeerpDemandeur = $listeerpDemandeur;
    }

    /**
     * @return string
     */
    public function getListeErpNomErp()
    {
        return $this->listeErpNomErp;
    }

    /**
     * @param string $listeErpNomErp
     */
    public function setListeErpNomErp($listeErpNomErp)
    {
        $this->listeErpNomErp = $listeErpNomErp;
    }

    /**
     * @return string
     */
    public function getListeerpNature()
    {
        return $this->listeerpNature;
    }

    /**
     * @param string $listeerpNature
     */
    public function setListeerpNature($listeerpNature)
    {
        $this->listeerpNature = $listeerpNature;
    }

    /**
     * @return string
     */
    public function getListeerpCategorie()
    {
        return $this->listeerpCategorie;
    }

    /**
     * @param string $listeerpCategorie
     */
    public function setListeerpCategorie($listeerpCategorie)
    {
        $this->listeerpCategorie = $listeerpCategorie;
    }

    /**
     * @return string
     */
    public function getListeerpType()
    {
        return $this->listeerpType;
    }

    /**
     * @param string $listeerpType
     */
    public function setListeerpType($listeerpType)
    {
        $this->listeerpType = $listeerpType;
    }

    /**
     * @return string
     */
    public function getListeerpDateValidAdap()
    {
        return $this->listeerpDateValidAdap;
    }

    /**
     * @param string $listeerpDateValidAdap
     */
    public function setListeerpDateValidAdap($listeerpDateValidAdap)
    {
        $this->listeerpDateValidAdap = $listeerpDateValidAdap;
    }

    /**
     * @return string
     */
    public function getListeerpDelaiAdap()
    {
        return $this->listeerpDelaiAdap;
    }

    /**
     * @param string $listeerpDelaiAdap
     */
    public function setListeerpDelaiAdap($listeerpDelaiAdap)
    {
        $this->listeerpDelaiAdap = $listeerpDelaiAdap;
    }

    /**
     * @return string
     */
    public function getListeerpIdIgn()
    {
        return $this->listeerpIdIgn;
    }

    /**
     * @param string $listeerpIdIgn
     */
    public function setListeerpIdIgn($listeerpIdIgn)
    {
        $this->listeerpIdIgn = $listeerpIdIgn;
    }

    /**
     * @return string
     */
    public function getListeerpSiret()
    {
        return $this->listeerpSiret;
    }

    /**
     * @param string $listeerpSiret
     */
    public function setListeerpSiret($listeerpSiret)
    {
        $this->listeerpSiret = $listeerpSiret;
    }

    /**
     * @return string
     */
    public function getListeerpNumeroVoie()
    {
        return $this->listeerpNumeroVoie;
    }

    /**
     * @param string $listeerpNumeroVoie
     */
    public function setListeerpNumeroVoie($listeerpNumeroVoie)
    {
        $this->listeerpNumeroVoie = $listeerpNumeroVoie;
    }

    /**
     * @return string
     */
    public function getListeerpNumeroComplement()
    {
        return $this->listeerpNumeroComplement;
    }

    /**
     * @param string $listeerpNumeroComplement
     */
    public function setListeerpNumeroComplement($listeerpNumeroComplement)
    {
        $this->listeerpNumeroComplement = $listeerpNumeroComplement;
    }

    /**
     * @return string
     */
    public function getListeerpComplementVoie()
    {
        return $this->listeerpComplementVoie;
    }

    /**
     * @param string $listeerpComplementVoie
     */
    public function setListeerpComplementVoie($listeerpComplementVoie)
    {
        $this->listeerpComplementVoie = $listeerpComplementVoie;
    }

    /**
     * @return string
     */
    public function getListeerpNomVoie()
    {
        return $this->listeerpNomVoie;
    }

    /**
     * @param string $listeerpNomVoie
     */
    public function setListeerpNomVoie($listeerpNomVoie)
    {
        $this->listeerpNomVoie = $listeerpNomVoie;
    }

    /**
     * @return string
     */
    public function getListeerpAliasNomVoie()
    {
        return $this->listeerpAliasNomVoie;
    }

    /**
     * @param string $listeerpAliasNomVoie
     */
    public function setListeerpAliasNomVoie($listeerpAliasNomVoie)
    {
        $this->listeerpAliasNomVoie = $listeerpAliasNomVoie;
    }

    /**
     * @return string
     */
    public function getListeerpLieuDit()
    {
        return $this->listeerpLieuDit;
    }

    /**
     * @param string $listeerpLieuDit
     */
    public function setListeerpLieuDit($listeerpLieuDit)
    {
        $this->listeerpLieuDit = $listeerpLieuDit;
    }

    /**
     * @return string
     */
    public function getListeerpCodePostal()
    {
        return $this->listeerpCodePostal;
    }

    /**
     * @param string $listeerpCodePostal
     */
    public function setListeerpCodePostal($listeerpCodePostal)
    {
        $this->listeerpCodePostal = $listeerpCodePostal;
    }

    /**
     * @return string
     */
    public function getListeerpCodeInsee()
    {
        return $this->listeerpCodeInsee;
    }

    /**
     * @param string $listeerpCodeInsee
     */
    public function setListeerpCodeInsee($listeerpCodeInsee)
    {
        $this->listeerpCodeInsee = $listeerpCodeInsee;
    }

    /**
     * @return string
     */
    public function getListeerpNomCommune()
    {
        return $this->listeerpNomCommune;
    }

    /**
     * @param string $listeerpNomCommune
     */
    public function setListeerpNomCommune($listeerpNomCommune)
    {
        $this->listeerpNomCommune = $listeerpNomCommune;
    }

    /**
     * @return string
     */
    public function getListeerpDepartement()
    {
        return $this->listeerpDepartement;
    }

    /**
     * @param string $listeerpDepartement
     */
    public function setListeerpDepartement($listeerpDepartement)
    {
        $this->listeerpDepartement = $listeerpDepartement;
    }

    /**
     * @return int
     */
    public function getListeerpStatut()
    {
        return $this->listeerpStatut;
    }

    /**
     * @param int $listeerpStatut
     */
    public function setListeerpStatut($listeerpStatut)
    {
        $this->listeerpStatut = $listeerpStatut;
    }

    /**
     * Set listeerpLongitude.
     *
     * @param float $listeerpLongitude
     */
    public function setListeerpLongitude($listeerpLongitude)
    {
        $this->listeerpLongitude = $listeerpLongitude;

        return $this;
    }

    /**
     * Get listeerpLongitude.
     *
     * @return float
     */
    public function getListeerpLongitude()
    {
        return $this->listeerpLongitude;
    }

    /**
     * Set listeerpLatitude.
     *
     * @param float $listeerpLatitude
     */
    public function setListeerpLatitude($listeerpLatitude)
    {
        $this->listeerpLatitude = $listeerpLatitude;

        return $this;
    }

    /**
     * Get listeerpLatitude.
     *
     * @return float
     */
    public function getListeerpLatitude()
    {
        return $this->listeerpLatitude;
    }
}
