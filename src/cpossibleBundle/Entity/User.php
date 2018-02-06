<?php

namespace cpossibleBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\AttributeOverrides;
use Doctrine\ORM\Mapping\AttributeOverride;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 *
 * @AttributeOverrides({
 *     @AttributeOverride(name="emailCanonical",
 *         column=@ORM\Column(
 *             name="emailCanonical",
 *             type="string",
 *             length=255,
 *             unique=false
 *         )
 *     )
 * })
 */

class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    public function __construct()
    {
        parent::__construct();
        $this->addRole("ROLE_ADMIN");
    }

    public function getDepartement() {
        return $this->departement;
    }

    public function setDepartement($departement) {
        $this->departement = $departement;
    }
    public function getCommune() {
        return $this->commune;
    }

    public function setCommune($commune) {
        $this->commune = $commune;
    }

    public function getEpciMetropole() {
        return $this->epci_metropole;
    }

    public function setEpciMetropole($epci_metropole) {
        $this->epci_metropole = $epci_metropole;
    }
}

?>
