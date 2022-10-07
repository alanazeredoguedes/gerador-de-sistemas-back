<?php

namespace App\Entity;

use App\Repository\ProgrammingLanguageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Sonata\MediaBundle\Model\Media;

#[ORM\Entity(repositoryClass: ProgrammingLanguageRepository::class)]
class ProgrammingLanguage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne(targetEntity: "App\Entity\SonataMediaMedia", cascade: ['persist'])]
    private mixed $logo;

    #[ORM\Column(type: 'boolean')]
    private bool $active = false;

    #[ORM\OneToMany(mappedBy: "programmingLanguage", targetEntity: "App\Entity\Framework")]
    private mixed $framework;

    public function __construct()
    {
        $this->framework = new ArrayCollection();
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
     * @return mixed
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * @param mixed $logo
     */
    public function setLogo($logo): void
    {
        $this->logo = $logo;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     */
    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    /**
     * @return mixed
     */
    public function getFramework(): mixed
    {
        return $this->framework;
    }

    /**
     * @param mixed $framework
     */
    public function setFramework(mixed $framework): void
    {
        $this->framework = $framework;
    }



}
