<?php

namespace App\Framework\Entity;

use App\Entity\Change;
use App\Entity\User;
use DateTime;

trait ChangeTrait
{
    /**
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $createdAt;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="actor_id", nullable=true)
     */
    protected $actor;

    /**
     * @var string
     * @ORM\Column(type="string", length=16, nullable=false)
     */
    private $objectType;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    private $objectId;

    /**
     * @var string
     * @ORM\Column(type="string", length=16, nullable=true)
     */
    private $property;

    /**
     * @var string
     * @ORM\Column(type="string", length=256, nullable=true)
     */
    private $old = null;

    /**
     * @var string
     * @ORM\Column(type="string", length=256, nullable=false)
     */
    private $new = '';

    /**
     * @return User
     */
    public function getActor(): ?User
    {
        return $this->actor;
    }

    /**
     * @param User $actor
     * @return self
     */
    public function setActor(?User $actor): self
    {
        $this->actor = $actor;
        return $this;
    }

    /**
     * @return string
     */
    public function getObjectType(): string
    {
        return $this->objectType;
    }

    /**
     * @param string $objectType
     * @return self
     */
    public function setObjectType(string $objectType): self
    {
        $this->objectType = $objectType;
        return $this;
    }

    /**
     * @return int
     */
    public function getObjectId(): int
    {
        return $this->objectId;
    }

    /**
     * @param int $objectId
     * @return self
     */
    public function setObjectId(int $objectId): self
    {
        $this->objectId = $objectId;
        return $this;
    }

    /**
     * @return string
     */
    public function getProperty(): ?string
    {
        return $this->property;
    }

    /**
     * @param string $property
     * @return self
     */
    public function setProperty(?string $property): self
    {
        $this->property = $property;
        return $this;
    }

    /**
     * @return string
     */
    public function getOld(): ?string
    {
        return $this->old;
    }

    /**
     * @param string $old
     * @return self
     */
    public function setOld(?string $old): self
    {
        $this->old = $old;
        return $this;
    }

    /**
     * @return string
     */
    public function getNew(): string
    {
        return $this->new;
    }

    /**
     * @param string $new
     * @return self
     */
    public function setNew(string $new): self
    {
        $this->new = $new;
        return $this;
    }

    public function setObject(object $object, int $forceId = null)
    {
        $this->setObjectType(substr(strrchr(get_class($object), '\\'), 1));

        if ($forceId) {
            $this->setObjectId($forceId);
        } elseif (method_exists($object, 'getId') && is_callable([$object, 'getId'])) {
            $this->setObjectId($object->getId());
        }
        return $this;
    }

    public function set(string $property, $old, $new)
    {
        return $this
            ->setProperty($property)
            ->setOld($old)
            ->setNew($new);
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @ORM\PrePersist
     */
    public function _prePersist()
    {
        if ($this->createdAt === null) {
            $this->createdAt = new DateTime;
        }
    }
}
