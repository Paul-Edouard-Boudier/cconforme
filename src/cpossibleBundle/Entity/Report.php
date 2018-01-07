<?php

namespace cpossibleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Report
 *
 * @ORM\Table(name="signalements")
 * @ORM\Entity(repositoryClass="cpossibleBundle\Repository\ReportRepository")
 */
class Report
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


}

