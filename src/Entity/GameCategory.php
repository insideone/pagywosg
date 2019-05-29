<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GameCategoryRepository")
 * @ORM\Table(name="game_categories", uniqueConstraints={
 *    @ORM\UniqueConstraint(columns={"name"})
 * })
 */
class GameCategory
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"import-createEventEntry", "import-updateEventEntry", "export"})
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", unique=true, length=128)
     * @Groups({"export"})
     */
    private $name;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return GameCategory
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
     * @return GameCategory
     */
    public function setName(string $name): GameCategory
    {
        $this->name = $name;
        return $this;
    }
}
