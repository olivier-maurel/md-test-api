<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Grandeur
 *
 * @ORM\Table(name="grandeur")
 * @ORM\Entity
 */
class Grandeur
{
    /**
     * @var int
     *
     * @ORM\Column(name="grandeur_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $grandeurId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="code", type="string", length=255, nullable=true)
     */
    private $code;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nom", type="string", length=255, nullable=true)
     */
    private $nom;

    /**
     * @var string|null
     *
     * @ORM\Column(name="volumetrique", type="string", length=255, nullable=true)
     */
    private $volumetrique;

    /**
     * @var int|null
     *
     * @ORM\Column(name="ordre", type="integer", nullable=true)
     */
    private $ordre;

    /**
     * @var int|null
     *
     * @ORM\Column(name="grandeur_type_id", type="integer", nullable=true)
     */
    private $grandeurTypeId;


}
