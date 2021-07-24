<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Fabricant
 *
 * @ORM\Table(name="fabricant")
 * @ORM\Entity
 */
class Fabricant
{
    /**
     * @var int
     *
     * @ORM\Column(name="fabricant_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $fabricantId;

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
     * @ORM\Column(name="image", type="string", length=255, nullable=true)
     */
    private $image;


}
