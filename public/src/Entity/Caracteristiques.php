<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Caracteristiques
 *
 * @ORM\Table(name="caracteristiques")
 * @ORM\Entity
 */
class Caracteristiques
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
     * @var string|null
     *
     * @ORM\Column(name="affichage_be_proactiv", type="string", length=255, nullable=true)
     */
    private $affichageBeProactiv;


}
