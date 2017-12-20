<?php

namespace cpossibleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Report
 *
 * @ORM\Table(name="report")
 * @ORM\Entity(repositoryClass="cpossibleBundle\Repository\ReportRepository")
 */
class Report
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
     * @var string
     *
     * @ORM\Column(name="adresse_erp", type="string", length=200)
     */
    private $adresseErp;

    /**
     * @var string
     *
     * @ORM\Column(name="adresse_signalee", type="string", length=200)
     */
    private $adresseSignalee;

    /**
     * @var string
     *
     * @ORM\Column(name="user_email", type="string", length=200)
     */
    private $userEmail;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="string", length=200)
     */
    private $message;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set adresseErp
     *
     * @param string $adresseErp
     *
     * @return Report
     */
    public function setAdresseErp($adresseErp)
    {
        $this->adresseErp = $adresseErp;

        return $this;
    }

    /**
     * Get adresseErp
     *
     * @return string
     */
    public function getAdresseErp()
    {
        return $this->adresseErp;
    }

    /**
     * Set adresseSignalee
     *
     * @param string $adresseSignalee
     *
     * @return Report
     */
    public function setAdresseSignalee($adresseSignalee)
    {
        $this->adresseSignalee = $adresseSignalee;

        return $this;
    }

    /**
     * Get adresseSignalee
     *
     * @return string
     */
    public function getAdresseSignalee()
    {
        return $this->adresseSignalee;
    }

    /**
     * Set userEmail
     *
     * @param string $userEmail
     *
     * @return Report
     */
    public function setUserEmail($userEmail)
    {
        $this->userEmail = $userEmail;

        return $this;
    }

    /**
     * Get userEmail
     *
     * @return string
     */
    public function getUserEmail()
    {
        return $this->userEmail;
    }

    /**
     * Set message
     *
     * @param string $message
     *
     * @return Report
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }
}
