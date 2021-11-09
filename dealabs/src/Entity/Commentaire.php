<?php

namespace App\Entity;

use App\Repository\CommentaireRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CommentaireRepository::class)
 */
class Commentaire
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
    private $content;

    /**
     * @ORM\ManyToOne(targetEntity=Account::class, inversedBy="commentaires")
     * @ORM\JoinColumn(nullable=false)
     */
    private $account;

    /**
     * @ORM\OneToMany(targetEntity=Thumb::class, mappedBy="commentaire", orphanRemoval=true)
     */
    private $thumbs;

    /**
     * @ORM\ManyToOne(targetEntity=Deals::class, inversedBy="commentaires")
     * @ORM\JoinColumn(nullable=false)
     */
    private $deal;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    public function __construct()
    {
        $this->thumbs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

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

    /**
     * @return Collection|Thumb[]
     */
    public function getThumbs(): Collection
    {
        return $this->thumbs;
    }

    public function addThumb(Thumb $thumb): self
    {
        if (!$this->thumbs->contains($thumb)) {
            $this->thumbs[] = $thumb;
            $thumb->setCommentaire($this);
        }

        return $this;
    }

    public function removeThumb(Thumb $thumb): self
    {
        if ($this->thumbs->removeElement($thumb)) {
            // set the owning side to null (unless already changed)
            if ($thumb->getCommentaire() === $this) {
                $thumb->setCommentaire(null);
            }
        }

        return $this;
    }

    public function getDeal(): ?Deals
    {
        return $this->deal;
    }

    public function setDeal(?Deals $deal): self
    {
        $this->deal = $deal;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }
}
