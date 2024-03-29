<?php

namespace App\Entity;

use App\Repository\OwnerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OwnerRepository::class)]
class Owner
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 60)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $age = null;

    #[ORM\Column]
    private ?bool $active = null;

    #[ORM\OneToMany(targetEntity: Wallet::class, mappedBy: 'owner_id', orphanRemoval: true)]
    private Collection $wallet_list;

    public function __construct()
    {
        $this->wallet_list = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(int $age): static
    {
        $this->age = $age;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return Collection<int, Wallet>
     */
    public function getWalletList(): Collection
    {
        return $this->wallet_list;
    }

    public function addWalletList(Wallet $walletList): static
    {
        if (!$this->wallet_list->contains($walletList)) {
            $this->wallet_list->add($walletList);
            $walletList->setOwnerId($this);
        }

        return $this;
    }

    public function removeWalletList(Wallet $walletList): static
    {
        if ($this->wallet_list->removeElement($walletList)) {
            // set the owning side to null (unless already changed)
            if ($walletList->getOwnerId() === $this) {
                $walletList->setOwnerId(null);
            }
        }

        return $this;
    }
}
