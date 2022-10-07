<?php

namespace App\Entity;

use App\Application\Project\UserBundle\Entity\User;
use App\Repository\ApplicationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ApplicationRepository::class)]
class Application
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $description = null;

    #[ORM\ManyToOne(targetEntity: "App\Application\Project\UserBundle\Entity\User", inversedBy: "application")]
    #[ORM\JoinColumn(name: "user", referencedColumnName: "id")]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: "App\Entity\Diagram", inversedBy: "application")]
    #[ORM\JoinColumn(name: "diagram", referencedColumnName: "id")]
    private ?Diagram $diagram = null;

    #[ORM\ManyToOne(targetEntity: "App\Entity\Framework", inversedBy: "application")]
    #[ORM\JoinColumn(name: "framework", referencedColumnName: "id")]
    private ?Framework $framework = null;

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
     * @return Diagram|null
     */
    public function getDiagram(): ?Diagram
    {
        return $this->diagram;
    }

    /**
     * @param Diagram|null $diagram
     */
    public function setDiagram(?Diagram $diagram): void
    {
        $this->diagram = $diagram;
    }

    /**
     * @return Framework|null
     */
    public function getFramework(): ?Framework
    {
        return $this->framework;
    }

    /**
     * @param Framework|null $framework
     */
    public function setFramework(?Framework $framework): void
    {
        $this->framework = $framework;
    }

}
