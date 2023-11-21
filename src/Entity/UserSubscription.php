<?php

namespace App\Entity;

use App\Repository\UserSubscriptionRepository;
use BenTools\WebPushBundle\Model\Subscription\UserSubscriptionInterface;
use BenTools\WebPushBundle\Model\Subscription\UserSubscriptionManagerInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserSubscriptionRepository::class)]
class UserSubscription implements UserSubscriptionInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'userSubscriptions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 255)]
    private ?string $subscriptionHash = null;

    #[ORM\Column]
    private array $subscription = [];

    public function __construct(User $user, string $subscriptionHash, array $subscription)
    {
        $this->user = $user;
        $this->subscriptionHash = $subscriptionHash;
        $this->subscription = $subscription;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): \Symfony\Component\Security\Core\User\UserInterface
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getSubscriptionHash(): string
    {
        return $this->subscriptionHash;
    }

    public function setSubscriptionHash(string $subscriptionHash): static
    {
        $this->subscriptionHash = $subscriptionHash;

        return $this;
    }

    public function getSubscription(): array
    {
        return $this->subscription;
    }

    public function setSubscription(array $subscription): static
    {
        $this->subscription = $subscription;

        return $this;
    }
    /**
     * @inheritDoc
     */
    public function getEndpoint(): string
    {
        return $this->subscription['endpoint'];
    }

    /**
     * @inheritDoc
     */
    public function getPublicKey(): string
    {
        return $this->subscription['keys']['p256dh'];
    }

    /**
     * @inheritDoc
     */
    public function getAuthToken(): string
    {
        return $this->subscription['keys']['auth'];
    }


    /**
     * Content-encoding (default: aesgcm).
     *
     * @return string
     */
    public function getContentEncoding(): string
    {
        return $this->subscription['content-encoding'] ?? 'aesgcm';
    }
}
