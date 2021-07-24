<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Catalogue
 *
 * @ORM\Table(name="catalogue")
 * @ORM\Entity
 */
class Catalogue
{
    /**
     * @var int
     *
     * @ORM\Column(name="catalogue_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $catalogueId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="client_id", type="string", length=255, nullable=true)
     */
    private $clientId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="code", type="string", length=255, nullable=true)
     */
    private $code;

    /**
     * @var string|null
     *
     * @ORM\Column(name="type", type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nom", type="string", length=255, nullable=true)
     */
    private $nom;

    /**
     * @var string|null
     *
     * @ORM\Column(name="defaut", type="string", length=255, nullable=true)
     */
    private $defaut;

    /**
     * @var string|null
     *
     * @ORM\Column(name="debut", type="string", length=255, nullable=true)
     */
    private $debut;

    /**
     * @var string|null
     *
     * @ORM\Column(name="souscriptions", type="string", length=255, nullable=true)
     */
    private $souscriptions;


}
