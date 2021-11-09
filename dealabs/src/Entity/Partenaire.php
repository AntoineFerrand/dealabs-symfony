<?php

namespace App\Entity;

use App\Repository\PartenaireRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PartenaireRepository::class)
 */
class Partenaire
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $url;

    /**
     * @ORM\OneToMany(targetEntity=Deals::class, mappedBy="partenaire")
     */
    private $dealsList;

    public function __construct()
    {
        $this->deals = new ArrayCollection();
        $this->dealsList = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return Collection|Deals[]
     */
    public function getDealsList(): Collection
    {
        return $this->dealsList;
    }

    public function addDealsList(Deals $dealsList): self
    {
        if (!$this->dealsList->contains($dealsList)) {
            $this->dealsList[] = $dealsList;
            $dealsList->setPartenaire($this);
        }

        return $this;
    }

    public function removeDealsList(Deals $dealsList): self
    {
        if ($this->dealsList->removeElement($dealsList)) {
            // set the owning side to null (unless already changed)
            if ($dealsList->getPartenaire() === $this) {
                $dealsList->setPartenaire(null);
            }
        }

        return $this;
    }
}
