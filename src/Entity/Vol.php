<?php

namespace App\Entity;

use App\Repository\VolRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VolRepository::class)]
class Vol
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    private ?string $numero = null;

     #[ORM\Column(type: 'datetime')]
    private ?\DateTime $heure_depart = null;


    #[ORM\Column(type: 'datetime')]    
    private ?\DateTime $heure_arrivee = null;

    #[ORM\Column]
    private ?float $prix = null;

    #[ORM\Column]
    private ?bool $reduction = null;

    #[ORM\Column]
    private ?int $places_disponibles = null;
   // Déclaration correcte de la propriété villeDepart
    #[ORM\ManyToOne(inversedBy: 'vols_depart')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Ville $villeDepart = null;

    // Déclaration correcte de la propriété villeArrivee
    #[ORM\ManyToOne(inversedBy: 'vols_arrivee')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Ville $villeArrivee = null;

 // Getter et Setter pour villeDepart
    public function getVilleDepart(): ?Ville
    {
        return $this->villeDepart;
    }

    public function setVilleDepart(?Ville $villeDepart): self
    {
        $this->villeDepart = $villeDepart;
        return $this;
    }
 
      // Getter et Setter pour villeArrivee
    public function getVilleArrivee(): ?Ville
    {
        return $this->villeArrivee;
    }

    public function setVilleArrivee(?Ville $villeArrivee): self
    {
        $this->villeArrivee = $villeArrivee;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(string $numero): static
    {
        $this->numero = $numero;
        return $this;
    }

  
    public function getHeureDepart(): ?\DateTime
    {
        return $this->heure_depart;
    }

    public function setHeureDepart(\DateTime $heure_depart): self
    {
        $this->heure_depart = $heure_depart;

        return $this;
    }

    public function getHeureArrivee(): ?\DateTime
    {
        return $this->heure_arrivee;
    }

    public function setHeureArrivee(\DateTime $heure_arrivee): self
    {
        $this->heure_arrivee = $heure_arrivee;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): static
    {
        $this->prix = $prix;
        return $this;
    }

    public function isReduction(): ?bool
    {
        return $this->reduction;
    }

    public function setReduction(bool $reduction): static
    {
        $this->reduction = $reduction;
        return $this;
    }

    public function getPlacesDisponibles(): ?int
    {
        return $this->places_disponibles;
    }

    public function setPlacesDisponibles(int $places_disponibles): static
    {
        $this->places_disponibles = $places_disponibles;
        return $this;
    }


}