<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\AtelierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AtelierRepository::class)]
#[ApiResource(
    operations: [new Get(), new GetCollection(), new Post(), new Put(), new Delete()],
    normalizationContext: ['groups' => ['atelier:read']],
    denormalizationContext: ['groups' => ['atelier:write']],
    security: "is_granted('PUBLIC_ACCESS')",
    securityPostDenormalize: "is_granted('ROLE_ADMIN') or object.getOwner() == user"
)]
class Atelier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['atelier:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 4, max: 180)]
    #[Groups(['atelier:read', 'atelier:write'])]
    private string $title = '';

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    #[Groups(['atelier:read', 'atelier:write'])]
    private string $description = '';

    #[ORM\Column]
    #[Assert\Positive]
    #[Groups(['atelier:read', 'atelier:write'])]
    private int $dureeMinutes = 60;

    #[ORM\Column]
    #[Assert\Positive]
    #[Groups(['atelier:read', 'atelier:write'])]
    private int $capacite = 10;

    #[ORM\Column(length: 20)]
    #[Groups(['atelier:read', 'atelier:write'])]
    private string $status = 'draft';

    #[ORM\Column]
    #[Groups(['atelier:read'])]
    private bool $archived = false;

    #[ORM\ManyToOne]
    #[Groups(['atelier:read', 'atelier:write'])]
    private ?Theme $theme = null;

    #[ORM\ManyToOne]
    #[Groups(['atelier:read', 'atelier:write'])]
    private ?Intervenant $intervenant = null;

    #[ORM\ManyToOne]
    private ?User $owner = null;

    /**
     * @var Collection<int, SessionAtelier>
     */
    #[ORM\OneToMany(mappedBy: 'atelier', targetEntity: SessionAtelier::class, cascade: ['persist'], orphanRemoval: true)]
    #[Groups(['atelier:read'])]
    private Collection $sessions;

    public function __construct()
    {
        $this->sessions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDureeMinutes(): int
    {
        return $this->dureeMinutes;
    }

    public function setDureeMinutes(int $dureeMinutes): self
    {
        $this->dureeMinutes = $dureeMinutes;

        return $this;
    }

    public function getCapacite(): int
    {
        return $this->capacite;
    }

    public function setCapacite(int $capacite): self
    {
        $this->capacite = $capacite;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function isArchived(): bool
    {
        return $this->archived;
    }

    public function setArchived(bool $archived): self
    {
        $this->archived = $archived;

        return $this;
    }

    public function getTheme(): ?Theme
    {
        return $this->theme;
    }

    public function setTheme(?Theme $theme): self
    {
        $this->theme = $theme;

        return $this;
    }

    public function getIntervenant(): ?Intervenant
    {
        return $this->intervenant;
    }

    public function setIntervenant(?Intervenant $intervenant): self
    {
        $this->intervenant = $intervenant;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection<int, SessionAtelier>
     */
    public function getSessions(): Collection
    {
        return $this->sessions;
    }

    public function addSession(SessionAtelier $session): self
    {
        if (!$this->sessions->contains($session)) {
            $this->sessions->add($session);
            $session->setAtelier($this);
        }

        return $this;
    }

    public function removeSession(SessionAtelier $session): self
    {
        if ($this->sessions->removeElement($session) && $session->getAtelier() === $this) {
            $session->setAtelier(null);
        }

        return $this;
    }
}
