<?php

namespace App\Entity;

use App\Repository\VoteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=VoteRepository::class)
 */
class Vote
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isPositive;


    /**
     * @ORM\ManyToOne(targetEntity=Account::class, inversedBy="votes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $account;

    /**
     * @ORM\ManyToOne(targetEntity=Deals::class, inversedBy="votes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $deals;

    public function __construct()
    {
        $this->deals = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIsPositive(): ?bool
    {
        return $this->isPositive;
    }

    public function setIsPositive(bool $isPositive): self
    {
        $this->isPositive = $isPositive;

        return $this;
    }

    public function getAccount(): ?Account
    {
        return $this->account;
    }

    public function setAccount(?Account $account): self
    {
        $this->account = $account;

        return $this;
    }

    public function getDeals(): ?Deals
    {
        return $this->deals;
    }

    public function setDeals(?Deals $deals): self
    {
        $this->deals = $deals;

        return $this;
    }
}
