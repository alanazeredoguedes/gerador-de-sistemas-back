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

    #[ORM\Column(type: 'datetime', length: 255, nullable: true)]
    private ?\DateTime $lastGenerate = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $url = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $repository = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $accessEmail = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $accessPassword = null;


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

    /**
     * @return \DateTime|null
     */
    public function getLastGenerate(): ?\DateTime
    {
        return $this->lastGenerate;
    }

    /**
     * @param \DateTime|null $lastGenerate
     */
    public function setLastGenerate(?\DateTime $lastGenerate): void
    {
        $this->lastGenerate = $lastGenerate;
    }

    /**
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param string|null $url
     */
    public function setUrl(?string $url): void
    {
        $this->url = $url;
    }

    /**
     * @return string|null
     */
    public function getRepository(): ?string
    {
        return $this->repository;
    }

    /**
     * @param string|null $repository
     */
    public function setRepository(?string $repository): void
    {
        $this->repository = $repository;
    }

    /**
     * @return string|null
     */
    public function getAccessEmail(): ?string
    {
        return $this->accessEmail;
    }

    /**
     * @param string|null $accessEmail
     */
    public function setAccessEmail(?string $accessEmail): void
    {
        $this->accessEmail = $accessEmail;
    }

    /**
     * @return string|null
     */
    public function getAccessPassword(): ?string
    {
        return $this->accessPassword;
    }

    /**
     * @param string|null $accessPassword
     */
    public function setAccessPassword(?string $accessPassword): void
    {
        $this->accessPassword = $accessPassword;
    }



}
