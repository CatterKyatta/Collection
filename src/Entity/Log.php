<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * Class Log
 *
 * @package App\Entity
 * @ORM\Entity
 * @ORM\Table(name="koi_log")
 */
class Log
{
    /**
     * @var \Ramsey\Uuid\UuidInterface
     *
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $type;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $loggedAt;

    /**
     * @var integer
     * @ORM\Column(type="uuid")
     */
    private $objectId;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $objectLabel;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $objectClass;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $payload;

    /**
     * @var \App\Entity\User
     * @ORM\ManyToOne(targetEntity="User", inversedBy="logs")
     */
    private $user;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
    }

    /**
     * @return null|string
     */
    public function getId() : ?string
    {
        return $this->id->toString();
    }

    /**
     * Set loggedAt
     *
     * @param \DateTime $loggedAt
     *
     * @return Log
     */
    public function setLoggedAt($loggedAt) : self
    {
        $this->loggedAt = $loggedAt;

        return $this;
    }

    /**
     * Get loggedAt
     *
     * @return \DateTime
     */
    public function getLoggedAt()
    {
        return $this->loggedAt;
    }

    /**
     * @param $objectId
     * @return Log
     */
    public function setObjectId($objectId) : self
    {
        $this->objectId = $objectId;

        return $this;
    }

    /**
     * Get objectId
     *
     * @return integer
     */
    public function getObjectId()
    {
        return $this->objectId;
    }

    /**
     * Set objectClass
     *
     * @param string $objectClass
     *
     * @return Log
     */
    public function setObjectClass($objectClass) : self
    {
        $this->objectClass = $objectClass;

        return $this;
    }

    /**
     * Get objectClass
     *
     * @return string
     */
    public function getObjectClass()
    {
        return $this->objectClass;
    }

    /**
     * Set payload
     *
     * @param string $payload
     *
     * @return Log
     */
    public function setPayload($payload) : self
    {
        $this->payload = $payload;

        return $this;
    }

    /**
     * Get payload
     *
     * @return string
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * Set user
     *
     * @param \App\Entity\User $user
     *
     * @return Log
     */
    public function setUser(User $user = null) : self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \App\Entity\User
     */
    public function getUser() : User
    {
        return $this->user;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Log
     */
    public function setType($type) : self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set objectLabel
     *
     * @param string $objectLabel
     *
     * @return Log
     */
    public function setObjectLabel($objectLabel) : self
    {
        $this->objectLabel = $objectLabel;

        return $this;
    }

    /**
     * Get objectLabel
     *
     * @return string
     */
    public function getObjectLabel()
    {
        return $this->objectLabel;
    }
}
