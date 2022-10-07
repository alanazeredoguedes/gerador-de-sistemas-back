<?php

namespace App\Entity;

use App\Repository\FrameworkRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Sonata\MediaBundle\Model\Media;

#[ORM\Entity(repositoryClass: FrameworkRepository::class)]
class Framework
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $description = null;

    #[ORM\ManyToOne(targetEntity: "App\Entity\SonataMediaMedia", cascade: ['persist'])]
    private $logo;

    #[ORM\Column(type: 'boolean')]
    private ?bool $active = null;

    #[ORM\ManyToOne(targetEntity: "App\Entity\ProgrammingLanguage", inversedBy: "framework")]
    #[ORM\JoinColumn(name: "programmingLanguage", referencedColumnName: "id")]
    private ?ProgrammingLanguage $programmingLanguage = null;

    #[ORM\OneToMany(mappedBy: "application", targetEntity: "App\Entity\Application")]
    private mixed $application;

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


    public function getLogo()
    {
        return $this->logo;
    }


    public function setLogo( $logo)
    {
        $this->logo = $logo;
    }

    /**
     * @return bool|null
     */
    public function getActive(): ?bool
    {
        return $this->active;
    }

    /**
     * @param bool|null $active
     */
    public function setActive(?bool $active): void
    {
        $this->active = $active;
    }

    /**
     * @return ProgrammingLanguage|null
     */
    public function getProgrammingLanguage(): ?ProgrammingLanguage
    {
        return $this->programmingLanguage;
    }

    /**
     * @param ProgrammingLanguage|null $programmingLanguage
     */
    public function setProgrammingLanguage(?ProgrammingLanguage $programmingLanguage): void
    {
        $this->programmingLanguage = $programmingLanguage;
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

}
