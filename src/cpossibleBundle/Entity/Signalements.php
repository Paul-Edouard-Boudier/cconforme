<?php

namespace cpossibleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Signalements
 *
 * @ORM\Table(name="signalements")
 * @ORM\Entity
 */
class Signalements
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="adresse_erp", type="string", length=200, nullable=false)
     */
    private $adresseErp;

    /**
     * @var string
     *
     * @ORM\Column(name="adresse_signalee", type="string", length=200, nullable=false)
     */
    private $adresseSignalee;

    /**
     * @var string
     *
     * @ORM\Column(name="user_email", type="string", length=200, nullable=false)
     */
    private $userEmail;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="string", length=200, nullable=false)
     */
    private $message;


}

