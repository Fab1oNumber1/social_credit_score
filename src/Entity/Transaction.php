<?php

namespace App\Entity;

use App\Repository\TransactionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TransactionRepository::class)]
class Transaction extends Model
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $value = 10;

    #[ORM\ManyToOne(inversedBy: 'transactions')]
    private ?User $user = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, options: ['default' => 'review'])]
    private ?string $status = 'review';

    #[ORM\ManyToOne(inversedBy: 'createdTransactions')]
    private ?User $author = null;

    #[ORM\ManyToMany(targetEntity: User::class)]
    private Collection $approvers;

    #[ORM\OneToMany(mappedBy: 'transaction', targetEntity: TransactionComment::class, orphanRemoval: true)]
    private Collection $transactionComments;

    #[ORM\ManyToOne(inversedBy: 'transactions')]
    private ?Media $media = null;

    public function __construct()
    {
        parent::__construct();
        $this->approvers = new ArrayCollection();
        $this->transactionComments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(int $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getApprovers(): Collection
    {
        return $this->approvers;
    }

    public function addApprover(User $approver): static
    {
        if (!$this->approvers->contains($approver)) {
            $this->approvers->add($approver);
        }

        return $this;
    }

    public function removeApprover(User $approver): static
    {
        $this->approvers->removeElement($approver);

        return $this;
    }

    /**
     * @return Collection<int, TransactionComment>
     */
    public function getTransactionComments(): Collection
    {
        return $this->transactionComments;
    }

    public function addTransactionComment(TransactionComment $transactionComment): static
    {
        if (!$this->transactionComments->contains($transactionComment)) {
            $this->transactionComments->add($transactionComment);
            $transactionComment->setTransaction($this);
        }

        return $this;
    }

    public function removeTransactionComment(TransactionComment $transactionComment): static
    {
        if ($this->transactionComments->removeElement($transactionComment)) {
            // set the owning side to null (unless already changed)
            if ($transactionComment->getTransaction() === $this) {
                $transactionComment->setTransaction(null);
            }
        }

        return $this;
    }

    public function getMedia(): ?Media
    {
        return $this->media;
    }

    public function setMedia(?Media $media): static
    {
        $this->media = $media;

        return $this;
    }
}
