<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TimestampRepository")
 * @ORM\Table(name="timestamps")
 */
class Timestamp
{
    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(type="string")
     */
    private $stamp;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $time;

    public function __construct(string $stamp, DateTime $time = null)
    {
        $this->stamp = $stamp;
        $this->time = $time ?: new DateTime;
    }

    /**
     * @return mixed
     */
    public function getStamp()
    {
        return $this->stamp;
    }

    /**
     * @param mixed $stamp
     * @return Timestamp
     */
    public function setStamp($stamp)
    {
        $this->stamp = $stamp;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getTime(): DateTime
    {
        return $this->time;
    }

    /**
     * @param DateTime $time
     * @return Timestamp
     */
    public function setTime(DateTime $time): Timestamp
    {
        $this->time = $time;
        return $this;
    }

    public function setToNow()
    {
        return $this->setTime(new DateTime);
    }
}
