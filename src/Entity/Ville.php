<?php

namespace App\Entity;
use App\Entity\Ville;

use App\Repository\VilleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VilleRepository::class)]
class Ville
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    private ?string $nom = null;

    /**
     * @var Collection<int, Vol>
     */
    #[ORM\OneToMany(targetEntity: Vol::class, mappedBy: 'ville_depart')]
    private Collection $vols_depart;

    /**
     * @var Collection<int, Vol>
     */
    #[ORM\OneToMany(targetEntity: Vol::class, mappedBy: 'ville_arrivee')]
    private Collection $vols_arrivee;

    public function __construct()
    {
        $this->vols_depart = new ArrayCollection();
        $this->vols_arrivee = new ArrayCollection();
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return Collection<int, Vol>
     */
    public function getVolsDepart(): Collection
    {
        return $this->vols_depart;
    }

    public function addVolsDepart(Vol $volsDepart): static
    {
        if (!$this->vols_depart->contains($volsDepart)) {
            $this->vols_depart->add($volsDepart);
            $volsDepart->setVilleDepart($this);
        }

        return $this;
    }

    public function removeVolsDepart(Vol $volsDepart): static
    {
        if ($this->vols_depart->removeElement($volsDepart)) {
            // set the owning side to null (unless already changed)
            if ($volsDepart->getVilleDepart() === $this) {
                $volsDepart->setVilleDepart(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Vol>
     */
    public function getVolsArrivee(): Collection
    {
        return $this->vols_arrivee;
    }

    public function addVolsArrivee(Vol $volsArrivee): static
    {
        if (!$this->vols_arrivee->contains($volsArrivee)) {
            $this->vols_arrivee->add($volsArrivee);
            $volsArrivee->setVilleArrivee($this);
        }

        return $this;
    }

    public function removeVolsArrivee(Vol $volsArrivee): static
    {
        if ($this->vols_arrivee->removeElement($volsArrivee)) {
            // set the owning side to null (unless already changed)
            if ($volsArrivee->getVilleArrivee() === $this) {
                $volsArrivee->setVilleArrivee(null);
            }
        }

        return $this;
    }


}