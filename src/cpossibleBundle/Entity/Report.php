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
    private $adresseErp;

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

