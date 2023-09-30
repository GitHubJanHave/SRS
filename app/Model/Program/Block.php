<?php

declare(strict_types=1);

namespace App\Model\Program;

use App\Model\Structure\Subevent;
use App\Model\User\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use function implode;

/**
 * Entita programový blok.
 */
#[ORM\Entity]
#[ORM\Table(name: 'block')]
class Block
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', nullable: false)]
    private int|null $id = null;

    /**
     * Název programového bloku.
     */
    #[ORM\Column(type: 'string', unique: true)]
    protected string $name;

    /**
     * Programy v bloku.
     *
     * @var Collection<int, Program>
     */
    #[ORM\OneToMany(mappedBy: 'block', targetEntity: Program::class, cascade: ['persist'])]
    #[ORM\OrderBy(['start' => 'ASC'])]
    protected Collection $programs;

    /**
     * Lektor.
     *
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'lecturersBlocks', cascade: ['persist'])]
    protected Collection $lectors;

    /**
     * Kategorie bloku.
     */
    #[ORM\ManyToOne(targetEntity: Category::class, cascade: ['persist'], inversedBy: 'blocks')]
    protected Category|null $category = null;

    /**
     * Podakce bloku.
     */
    #[ORM\ManyToOne(targetEntity: Subevent::class, cascade: ['persist'], inversedBy: 'blocks')]
    protected Subevent $subevent;

    /**
     * Povinnost.
     */
    #[ORM\Column(type: 'string')]
    protected string $mandatory;

    /**
     * Délka programového bloku.
     */
    #[ORM\Column(type: 'integer')]
    protected int $duration;

    /**
     * Kapacita.
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    protected int|null $capacity = null;

    /**
     * Povoleno přihlašování náhradníků?
     */
    #[ORM\Column(type: 'boolean')]
    protected bool $alternatesAllowed = false;

    /**
     * Pomůcky.
     */
    #[ORM\Column(type: 'string', nullable: true)]
    protected string|null $tools = null;

    /**
     * Stručný popis.
     */
    #[ORM\Column(type: 'text', nullable: true)]
    protected string|null $perex = null;

    /**
     * Podrobný popis.
     */
    #[ORM\Column(type: 'text', nullable: true)]
    protected string|null $description = null;

    public function __construct(
        string $name,
        int $duration,
        int|null $capacity,
        bool $alternatesAllowed,
        string $mandatory,
    ) {
        $this->name              = $name;
        $this->duration          = $duration;
        $this->capacity          = $capacity;
        $this->alternatesAllowed = $alternatesAllowed;
        $this->mandatory         = $mandatory;
        $this->programs          = new ArrayCollection();
        $this->lectors           = new ArrayCollection();
    }

    public function getId(): int|null
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /** @return Collection<int, Program> */
    public function getPrograms(): Collection
    {
        return $this->programs;
    }

    /**
     * Vrací počet programů bloku.
     */
    public function getProgramsCount(): int
    {
        return $this->programs->count();
    }

    public function addProgram(Program $program): void
    {
        if (! $this->programs->contains($program)) {
            $this->programs->add($program);
            $program->setBlock($this);
        }
    }

    /** @return Collection<int, User> */
    public function getLectors(): Collection
    {
        return $this->lectors;
    }

    public function getLectorsText(): string
    {
        return implode(', ', $this->lectors->map(static fn (User $lector) => $lector->getDisplayName())->toArray());
    }

    /** @param Collection<int, User> $lectors */
    public function setLectors(Collection $lectors): void
    {
        foreach ($this->lectors as $lector) {
            $this->removeLector($lector);
        }

        foreach ($lectors as $lector) {
            $this->addLector($lector);
        }
    }

    public function addLector(User $lector): void
    {
        if (! $this->lectors->contains($lector)) {
            $this->lectors->add($lector);
            $lector->addLecturersBlock($this);
        }
    }

    public function removeLector(User $lector): void
    {
        if ($this->lectors->contains($lector)) {
            $this->lectors->removeElement($lector);
            $lector->removeLecturersBlock($this);
        }
    }

    public function getCategory(): Category|null
    {
        return $this->category;
    }

    public function setCategory(Category|null $category): void
    {
        $this->category?->removeBlock($this);

        $category?->addBlock($this);

        $this->category = $category;
    }

    public function getSubevent(): Subevent|null
    {
        return $this->subevent;
    }

    public function setSubevent(Subevent $subevent): void
    {
        if (isset($this->subevent)) {
            $this->subevent->removeBlock($this);
        }

        $subevent->addBlock($this);

        $this->subevent = $subevent;
    }

    public function getMandatory(): string
    {
        return $this->mandatory;
    }

    public function setMandatory(string $mandatory): void
    {
        $this->mandatory = $mandatory;
    }

    public function getDuration(): int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): void
    {
        $this->duration = $duration;
    }

    public function getCapacity(): int|null
    {
        return $this->capacity;
    }

    public function setCapacity(int|null $capacity): void
    {
        $this->capacity = $capacity;
    }

    public function isAlternatesAllowed(): bool
    {
        return $this->alternatesAllowed;
    }

    public function setAlternatesAllowed(bool $alternatesAllowed): void
    {
        $this->alternatesAllowed = $alternatesAllowed;
    }

    public function getTools(): string|null
    {
        return $this->tools;
    }

    public function setTools(string|null $tools): void
    {
        $this->tools = $tools;
    }

    public function getPerex(): string|null
    {
        return $this->perex;
    }

    public function setPerex(string|null $perex): void
    {
        $this->perex = $perex;
    }

    public function getDescription(): string|null
    {
        return $this->description;
    }

    public function setDescription(string|null $description): void
    {
        $this->description = $description;
    }
}
