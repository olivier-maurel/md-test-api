<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Type
 *
 * @ORM\Table(name="type", indexes={@ORM\Index(name="metier_id", columns={"metier_id"})})
 * @ORM\Entity
 */
class Type
{
    /**
     * @var int
     *
     * @ORM\Column(name="type_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $typeId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="famille", type="string", length=255, nullable=true)
     */
    private $famille;

    /**
     * @var string|null
     *
     * @ORM\Column(name="serialisable", type="string", length=255, nullable=true)
     */
    private $serialisable;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nom", type="string", length=255, nullable=true)
     */
    private $nom;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ordre", type="string", length=255, nullable=true)
     */
    private $ordre;

    /**
     * @var string|null
     *
     * @ORM\Column(name="image", type="string", length=255, nullable=true)
     */
    private $image;

    /**
     * @var string|null
     *
     * @ORM\Column(name="color", type="string", length=255, nullable=true)
     */
    private $color;

    /**
     * @var string|null
     *
     * @ORM\Column(name="option", type="string", length=255, nullable=true)
     */
    private $option;

    /**
     * @var \Metier
     *
     * @ORM\ManyToOne(targetEntity="Metier")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="metier_id", referencedColumnName="metier_id")
     * })
     */
    private $metier;


}
