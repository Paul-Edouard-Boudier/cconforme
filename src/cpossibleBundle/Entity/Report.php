<?php

namespace cpossibleBundle\Entity;

/**
 * Report
 */
class Report
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $nomErp;

    /**
     * @var string
     */
    private $adresseSignalee;

    /**
     * @var string
     */
    private $userEmail;

    /**
     * @var string
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
     * Set nomErp
     *
     * @param string $nomErp
     *
     * @return Report
     */
    public function setNomErp($nomErp)
    {
        $this->nomErp = $nomErp;

        return $this;
    }

    /**
     * Get nomErp
     *
     * @return string
     */
    public function getNomErp()
    {
        return $this->nomErp;
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
