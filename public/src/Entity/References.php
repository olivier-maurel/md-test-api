<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * References
 *
 * @ORM\Table(name="references", indexes={@ORM\Index(name="materiel_id", columns={"materiel_id"})})
 * @ORM\Entity
 */
class References
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int|null
     *
     * @ORM\Column(name="materiel_reference_id", type="integer", nullable=true)
     */
    private $materielReferenceId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="reference_fabricant", type="string", length=255, nullable=true)
     */
    private $referenceFabricant;

    /**
     * @var \Materiels
     *
     * @ORM\ManyToOne(targetEntity="Materiels")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="materiel_id", referencedColumnName="materiel_id")
     * })
     */
    private $materiel;


}
