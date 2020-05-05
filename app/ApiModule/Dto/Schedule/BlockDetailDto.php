<?php

declare(strict_types=1);

namespace App\ApiModule\Dto\Schedule;

use JMS\Serializer\Annotation as JMS;
use Nette;

/**
 * Objekt pro přenos údajů o bloku do FullCalendar.
 *
 * @author Jan Staněk <jan.stanek@skaut.cz>
 */
class BlockDetailDto
{
    use Nette\SmartObject;

    /**
     * @JMS\Type("int")
     * @var int
     */
    private $id;

    /**
     * @JMS\Type("string")
     * @var string
     */
    private $name;

    /**
     * @JMS\Type("string")
     * @var string
     */
    private $category;

    /**
     * @JMS\Type("array<App\ApiModule\Dto\Schedule\LectorDetailDto>")
     * @var LectorDetailDto[]
     */
    private $lectors;

    /**
     * @JMS\Type("string")
     * @var string
     */
    private $lectorsNames;

    /**
     * @JMS\Type("int")
     * @var int
     */
    private $duration;

    /**
     * @JMS\Type("int")
     * @var ?int
     */
    private $capacity;

    /**
     * @JMS\Type("boolean")
     * @var bool
     */
    private $mandatory;

    /**
     * @JMS\Type("boolean")
     * @var bool
     */
    private $autoRegistered;

    /**
     * @JMS\Type("string")
     * @var string
     */
    private $perex;

    /**
     * @JMS\Type("string")
     * @var string
     */
    private $description;

    /**
     * @JMS\Type("int")
     * @var int
     */
    private $programsCount;

    /**
     * @JMS\Type("boolean")
     * @var bool
     */
    private $userAllowed;

    /**
     * @JMS\Type("boolean")
     * @var bool
     */
    private $userAttends;

    public function getId() : int
    {
        return $this->id;
    }

    public function setId(int $id) : void
    {
        $this->id = $id;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function setName(string $name) : void
    {
        $this->name = $name;
    }

    public function getCategory() : string
    {
        return $this->category;
    }

    public function setCategory(string $category) : void
    {
        $this->category = $category;
    }

    /**
     * @return LectorDetailDto[]
     */
    public function getLectors() : array
    {
        return $this->lectors;
    }

    /**
     * @param LectorDetailDto[] $lectors
     */
    public function setLectors(array $lectors) : void
    {
        $this->lectors = $lectors;
    }

    public function getLectorsNames() : string
    {
        return $this->lectorsNames;
    }

    public function setLectorsNames(string $lectorsNames) : void
    {
        $this->lectorsNames = $lectorsNames;
    }

    public function getDuration() : int
    {
        return $this->duration;
    }

    public function setDuration(int $duration) : void
    {
        $this->duration = $duration;
    }

    public function getCapacity() : ?int
    {
        return $this->capacity;
    }

    public function setCapacity(?int $capacity) : void
    {
        $this->capacity = $capacity;
    }

    public function isMandatory() : bool
    {
        return $this->mandatory;
    }

    public function setMandatory(bool $mandatory) : void
    {
        $this->mandatory = $mandatory;
    }

    public function isAutoRegistered() : bool
    {
        return $this->autoRegistered;
    }

    public function setAutoRegistered(bool $autoRegistered) : void
    {
        $this->autoRegistered = $autoRegistered;
    }

    public function getPerex() : string
    {
        return $this->perex;
    }

    public function setPerex(string $perex) : void
    {
        $this->perex = $perex;
    }

    public function getDescription() : string
    {
        return $this->description;
    }

    public function setDescription(string $description) : void
    {
        $this->description = $description;
    }

    public function getProgramsCount() : int
    {
        return $this->programsCount;
    }

    public function setProgramsCount(int $programsCount) : void
    {
        $this->programsCount = $programsCount;
    }

    public function isUserAllowed() : bool
    {
        return $this->userAllowed;
    }

    public function setUserAllowed(bool $userAllowed) : void
    {
        $this->userAllowed = $userAllowed;
    }

    public function isUserAttends() : bool
    {
        return $this->userAttends;
    }

    public function setUserAttends(bool $userAttends) : void
    {
        $this->userAttends = $userAttends;
    }
}
