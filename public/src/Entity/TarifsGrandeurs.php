<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TarifsGrandeurs
 *
 * @ORM\Table(name="tarifs_grandeurs", indexes={@ORM\Index(name="catalogue_id", columns={"catalogue_id"})})
 * @ORM\Entity
 */
class TarifsGrandeurs
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
     * @ORM\Column(name="tarif_grandeur_id", type="integer", nullable=true)
     */
    private $tarifGrandeurId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="grandeur_type_materiel_id", type="integer", nullable=true)
     */
    private $grandeurTypeMaterielId;

    /**
     * @var float|null
     *
     * @ORM\Column(name="prix_achat", type="float", precision=8, scale=4, nullable=true)
     */
    private $prixAchat;

    /**
     * @var float|null
     *
     * @ORM\Column(name="prix_maxi", type="float", precision=8, scale=4, nullable=true)
     */
    private $prixMaxi;

    /**
     * @var string|null
     *
     * @ORM\Column(name="debut", type="string", length=255, nullable=true)
     */
    private $debut;

    /**
     * @var string|null
     *
     * @ORM\Column(name="fin", type="string", length=255, nullable=true)
     */
    private $fin;

    /**
     * @var \Catalogue
     *
     * @ORM\ManyToOne(targetEntity="Catalogue")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="catalogue_id", referencedColumnName="catalogue_id")
     * })
     */
    private $catalogue;


}
