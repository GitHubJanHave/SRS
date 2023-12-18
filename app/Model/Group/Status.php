<?php

declare(strict_types=1);

namespace App\Model\Group;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entita kategorie programového bloku.
 */
#[ORM\Entity]
#[ORM\Table(name: 'status')]
class Status
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', nullable: false)]
    private int|null $id = null;

    /**
     * Název kategorie.
     */
    #[ORM\Column(type: 'string', unique: true)]
    protected string $name;

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
}
