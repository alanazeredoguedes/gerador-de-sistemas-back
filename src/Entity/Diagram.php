<?php

namespace App\Entity;

use App\Application\Project\UserBundle\Entity\User;
use App\Repository\DiagramRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DiagramRepository::class)]
class Diagram
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $description = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?string $structure = null;

    #[ORM\ManyToOne(targetEntity: "App\Application\Project\UserBundle\Entity\User", inversedBy: "diagram")]
    #[ORM\JoinColumn(name: "user", referencedColumnName: "id")]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: "diagram", targetEntity: "App\Entity\Application")]
    private mixed $application;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $isTemplate = null;


    public function __construct()
    {
        $this->application = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string|null
     */
    public function getStructure(): ?string
    {
        return $this->structure;
    }

    /**
     * @param string|null $structure
     */
    public function setStructure(?string $structure): void
    {
        $this->structure = $structure;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User|null $user
     */
    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getApplication(): mixed
    {
        return $this->application;
    }

    /**
     * @param mixed $application
     */
    public function setApplication(mixed $application): void
    {
        $this->application = $application;
    }

    public function getIsTemplate(): ?bool
    {
        return $this->isTemplate;
    }


    public function setIsTemplate(?bool $isTemplate): void
    {
        $this->isTemplate = $isTemplate;
    }


}
