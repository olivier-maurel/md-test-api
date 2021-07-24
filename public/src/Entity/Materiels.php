<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Materiels
 *
 * @ORM\Table(name="materiels", indexes={@ORM\Index(name="fabricant_id", columns={"fabricant_id"}), @ORM\Index(name="type_id", columns={"type_id"})})
 * @ORM\Entity
 */
class Materiels
{
    /**
     * @var int
     *
     * @ORM\Column(name="materiel_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $materielId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="debut_commercialisation", type="string", length=255, nullable=true)
     */
    private $debutCommercialisation;

    /**
     * @var string|null
     *
     * @ORM\Column(name="fin_commercialisation", type="string", length=255, nullable=true)
     */
    private $finCommercialisation;

    /**
     * @var float|null
     *
     * @ORM\Column(name="prix_public", type="float", precision=8, scale=4, nullable=true)
     */
    private $prixPublic;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nom_court", type="string", length=255, nullable=true)
     */
    private $nomCourt;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nom", type="string", length=255, nullable=true)
     */
    private $nom;

    /**
     * @var string|null
     *
     * @ORM\Column(name="commentaire", type="string", length=255, nullable=true)
     */
    private $commentaire;

    /**
     * @var int|null
     *
     * @ORM\Column(name="client_id", type="integer", nullable=true)
     */
    private $clientId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="caracteristiques", type="string", length=255, nullable=true)
     */
    private $caracteristiques;

    /**
     * @var string|null
     *
     * @ORM\Column(name="tarifs_grandeurs", type="string", length=255, nullable=true)
     */
    private $tarifsGrandeurs;

    /**
     * @var string|null
     *
     * @ORM\Column(name="images", type="string", length=255, nullable=true)
     */
    private $images;

    /**
     * @var string|null
     *
     * @ORM\Column(name="marque", type="string", length=255, nullable=true)
     */
    private $marque;

    /**
     * @var string|null
     *
     * @ORM\Column(name="reference_fabricant", type="string", length=255, nullable=true)
     */
    private $referenceFabricant;

    /**
     * @var float|null
     *
     * @ORM\Column(name="_score", type="float", precision=8, scale=4, nullable=true)
     */
    private $score;

    /**
     * @var \Type
     *
     * @ORM\ManyToOne(targetEntity="Type")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="type_id", referencedColumnName="type_id")
     * })
     */
    private $type;

    /**
     * @var \Fabricant
     *
     * @ORM\ManyToOne(targetEntity="Fabricant")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fabricant_id", referencedColumnName="fabricant_id")
     * })
     */
    private $fabricant;


}
