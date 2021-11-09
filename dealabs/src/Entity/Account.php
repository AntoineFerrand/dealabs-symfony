<?php

namespace App\Entity;

use App\Repository\AccountRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=AccountRepository::class)
 * @Vich\Uploadable
 * @UniqueEntity(
 *     fields={"email"},
 *     message="L'adresse email que vous avez entrée est déjà utilisée"
 * )
 */
class Account implements UserInterface, \Serializable
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
    private $login;

    private $old_password;

    private $new_password;

    private $confirm_new_password;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min="8", minMessage="Votre mot de passe doit faire au minimum 8 caractères")
     */
    private $password;

    /**
     * @Assert\EqualTo(propertyPath="password", message="Vous n'avez pas tapez le même mot de passe")
     */
    private $confirm_password;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @ORM\OneToMany(targetEntity=Vote::class, mappedBy="account", orphanRemoval=true)
     */
    private $votes;

    /**
     * @ORM\OneToMany(targetEntity=Commentaire::class, mappedBy="account", orphanRemoval=true)
     */
    private $commentaires;

    /**
     * @ORM\OneToMany(targetEntity=Thumb::class, mappedBy="account", orphanRemoval=true)
     */
    private $thumbs;

    /**
     * @ORM\OneToMany(targetEntity=Deals::class, mappedBy="creator", orphanRemoval=true)
     * @ORM\OrderBy({"creationDate" = "ASC"})
     */
    private $deals;

    /**
     * @ORM\Column(type="integer")
     */
    private $voteCount;

    /**
     * @ORM\Column(type="integer")
     */
    private $dealCount;

    /**
     * @ORM\Column(type="integer")
     */
    private $commentCount;

    /**
     * @ORM\ManyToMany(targetEntity=Badge::class, inversedBy="accounts")
     */
    private $badges;

    /**
     * @ORM\ManyToMany(targetEntity=Deals::class)
     */
    private $savedDeals;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\OneToOne(targetEntity=Avatar::class, mappedBy="account", cascade={"persist", "remove"})
     * @
     */
    private $avatar;

    /**
     * @ORM\OneToMany(targetEntity=Alerte::class, mappedBy="creator", orphanRemoval=true)
     */
    private $alertes;

    public function __construct()
    {
        $this->votes = new ArrayCollection();
        $this->commentaires = new ArrayCollection();
        $this->thumbs = new ArrayCollection();
        $this->deals = new ArrayCollection();
        $this->badges = new ArrayCollection();
        $this->voteCount = 0;
        $this->commentCount = 0;
        $this->dealCount = 0;
        $this->savedDeals = new ArrayCollection();
        $this->alertes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): self
    {
        $this->login = $login;

        return $this;
    }

    public function getOldPassword(): ?string
    {
        return $this->old_password;
    }

    public function setOldPassword(string $old_password): self
    {
        $this->old_password = $old_password;

        return $this;
    }

    public function getNewPassword(): ?string
    {
        return $this->new_password;
    }

    public function setNewPassword(string $new_password): self
    {
        $this->new_password = $new_password;

        return $this;
    }

    public function getConfirmNewPassword(): ?string
    {
        return $this->confirm_new_password;
    }

    public function setConfirmNewPassword(string $confirm_new_password): self
    {
        $this->confirm_new_password = $confirm_new_password;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getConfirmPassword(): ?string
    {
        return $this->confirm_password;
    }

    public function setConfirmPassword(string $confirm_password): self
    {
        $this->confirm_password = $confirm_password;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Collection|Vote[]
     */
    public function getVotes(): Collection
    {
        return $this->votes;
    }

    public function addVote(Vote $vote): self
    {
        if (!$this->votes->contains($vote)) {
            $this->votes[] = $vote;
            $vote->setAccount($this);
        }

        return $this;
    }

    public function removeVote(Vote $vote): self
    {
        if ($this->votes->removeElement($vote)) {
            // set the owning side to null (unless already changed)
            if ($vote->getAccount() === $this) {
                $vote->setAccount(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Commentaire[]
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaire $commentaire): self
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires[] = $commentaire;
            $commentaire->setAccount($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): self
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getAccount() === $this) {
                $commentaire->setAccount(null);
            }
        }

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
            $thumb->setAccount($this);
        }

        return $this;
    }

    public function removeThumb(Thumb $thumb): self
    {
        if ($this->thumbs->removeElement($thumb)) {
            // set the owning side to null (unless already changed)
            if ($thumb->getAccount() === $this) {
                $thumb->setAccount(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Deals[]
     */
    public function getDeals(): Collection
    {
        return $this->deals;
    }

    public function addDeal(Deals $deal): self
    {
        if (!$this->deals->contains($deal)) {
            $this->deals[] = $deal;
            $deal->setCreator($this);
        }

        return $this;
    }

    public function removeDeal(Deals $deal): self
    {
        if ($this->deals->removeElement($deal)) {
            // set the owning side to null (unless already changed)
            if ($deal->getCreator() === $this) {
                $deal->setCreator(null);
            }
        }

        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    public function getUsername()
    {
        // TODO: Implement getUsername() method.
        return $this->getEmail();
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getVoteCount(): ?int
    {
        return $this->voteCount;
    }

    public function setVoteCount(int $voteCount): self
    {
        $this->voteCount = $voteCount;

        return $this;
    }

    public function getDealCount(): ?int
    {
        return $this->dealCount;
    }

    public function setDealCount(int $dealCount): self
    {
        $this->dealCount = $dealCount;

        return $this;
    }

    public function getCommentCount(): ?int
    {
        return $this->commentCount;
    }

    public function setCommentCount(int $commentCount): self
    {
        $this->commentCount = $commentCount;

        return $this;
    }

    /**
     * @return Collection|Badge[]
     */
    public function getBadges(): Collection
    {
        return $this->badges;
    }

    public function addBadge(Badge $badge): self
    {
        if (!$this->badges->contains($badge)) {
            $this->badges[] = $badge;
        }
        return $this;
    }

    /**
     * @return Collection|Deals[]
     */
    public function getSavedDeals(): Collection
    {
        return $this->savedDeals;
    }

    public function addSavedDeal(Deals $savedDeal): self
    {
        if (!$this->savedDeals->contains($savedDeal)) {
            $this->savedDeals[] = $savedDeal;
        }

        return $this;
    }

    public function removeBadge(Badge $badge): self
    {
        $this->badges->removeElement($badge);
    }
    
    public function removeSavedDeal(Deals $savedDeal): self
    {
        $this->savedDeals->removeElement($savedDeal);

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
    }

    /**
     * @return Collection|Alerte[]
     */
    public function getAlertes(): Collection
    {
        return $this->alertes;
    }

    public function addAlerte(Alerte $alerte): self
    {
        if (!$this->alertes->contains($alerte)) {
            $this->alertes[] = $alerte;
            $alerte->setCreator($this);
        }

        return $this;
    }

    public function getAvatar(): ?Avatar
    {
        return $this->avatar;
    }

    public function setAvatar(Avatar $avatar): self
    {
        if ($avatar->getAccount() !== $this) {
            $avatar->setAccount($this);
        }
        $this->avatar = $avatar;
        return $this;
    }

    public function serialize() {

        return serialize(array(
            $this->id,
            $this->login,
            $this->password,
            $this->description,
            $this->commentCount,
            $this->dealCount,
            $this->voteCount,
            $this->roles,
            $this->email,
            $this->badges,
            $this->commentaires,
            $this->confirm_new_password,
            $this->confirm_password,
            $this->deals,
            $this->new_password,
            $this->old_password,
            $this->savedDeals,
            $this->thumbs,
            $this->votes
        ));

    }

    public function unserialize($serialized)
    {

        list (
            $this->id,
            $this->login,
            $this->password,
            $this->description,
            $this->commentCount,
            $this->dealCount,
            $this->voteCount,
            $this->roles,
            $this->email,
            $this->badges,
            $this->commentaires,
            $this->confirm_new_password,
            $this->confirm_password,
            $this->deals,
            $this->new_password,
            $this->old_password,
            $this->savedDeals,
            $this->thumbs,
            $this->votes

            ) = unserialize($serialized);
    }

    public function removeAlerte(Alerte $alerte): self
    {
        if ($this->alertes->removeElement($alerte)) {
            // set the owning side to null (unless already changed)
            if ($alerte->getCreator() === $this) {
                $alerte->setCreator(null);
            }
        }

        return $this;
    }
}
