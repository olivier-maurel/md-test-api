<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tarifs
 *
 * @ORM\Table(name="tarifs", indexes={@ORM\Index(name="catalogue_id", columns={"catalogue_id"}), @ORM\Index(name="materiel_id", columns={"materiel_id"})})
 * @ORM\Entity
 */
class Tarifs
{
    /**
     * @var int
     *
     * @ORM\Column(name="tarif_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $tarifId;

    /**
     * @var float|null
     *
     * @ORM\Column(name="prix_achat_groupe", type="float", precision=8, scale=4, nullable=true)
     */
    private $prixAchatGroupe;

    /**
     * @var string|null
     *
     * @ORM\Column(name="prix_premiere_monte", type="string", length=255, nullable=true)
     */
    private $prixPremiereMonte;

    /**
     * @var string|null
     *
     * @ORM\Column(name="taux_restitution", type="string", length=255, nullable=true)
     */
    private $tauxRestitution;

    /**
     * @var float|null
     *
     * @ORM\Column(name="markup_agence", type="float", precision=8, scale=4, nullable=true)
     */
    private $markupAgence;

    /**
     * @var string|null
     *
     * @ORM\Column(name="reconditionne", type="string", length=255, nullable=true)
     */
    private $reconditionne;

    /**
     * @var string|null
     *
     * @ORM\Column(name="debut", type="string", length=255, nullable=true)
     */
    private $debut;

    /**
     * @var int|null
     *
     * @ORM\Column(name="prix_cession_vendeur", type="integer", nullable=true)
     */
    private $prixCessionVendeur;

    /**
     * @var string|null
     *
     * @ORM\Column(name="prix_achat_groupe_calcule", type="string", length=255, nullable=true)
     */
    private $prixAchatGroupeCalcule;

    /**
     * @var int|null
     *
     * @ORM\Column(name="prix_cession_agence", type="integer", nullable=true)
     */
    private $prixCessionAgence;

    /**
     * @var \Catalogue
     *
     * @ORM\ManyToOne(targetEntity="Catalogue")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="catalogue_id", referencedColumnName="catalogue_id")
     * })
     */
    private $catalogue;

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
