<?php

namespace App\Entity;

use App\Framework\Entity\IdentityProvider;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity
 * @ORM\Table(name="games", uniqueConstraints={
 *     @ORM\UniqueConstraint(columns={"id"})
 * })
 */
class Game implements IdentityProvider
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(type="integer", nullable=false, unique=true)
     * @Groups({"export", "import-createEventEntry"})
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     * @Groups({"export", "export-gamesGetList"})
     */
    private $name;

    /**
     * @var int|null
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"export"})
     */
    private $achievementsCnt;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $standalone;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return Game
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Game
     */
    public function setName(string $name): Game
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return int
     */
    public function getAchievementsCnt(): ?int
    {
        return $this->achievementsCnt;
    }

    /**
     * @param int $achievementsCnt
     * @return Game
     */
    public function setAchievementsCnt(?int $achievementsCnt): Game
    {
        $this->achievementsCnt = $achievementsCnt;
        return $this;
    }

    /**
     * @return bool
     */
    public function isStandalone(): bool
    {
        return $this->standalone;
    }

    /**
     * @param bool $standalone
     * @return Game
     */
    public function setStandalone(bool $standalone): Game
    {
        $this->standalone = $standalone;
        return $this;
    }
}
