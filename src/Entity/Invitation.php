<?php

namespace App\Entity;

use App\Repository\InvitationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InvitationRepository::class)]
class Invitation
{
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_DECLINED = 'declined';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;



    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'Invitation')]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id", nullable: false)]
    private ?User $user_id = null;

    #[ORM\ManyToOne(targetEntity: Token::class, inversedBy: 'Token')]
    #[ORM\JoinColumn(name: "token_id", referencedColumnName: "id", nullable: false)]
    private ?Token $token_id = null;

    #[ORM\Column(length: 30)]
    private ?string $status = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getUserId(): ?User
    {
        return $this->user_id;
    }

    public function setUserId(?User $user_id): static
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getTokenId(): ?Token
    {
        return $this->token_id;
    }

    public function setTokenId(?Token $token_id): static
    {
        $this->token_id = $token_id;

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
}
