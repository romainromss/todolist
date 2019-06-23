<?php


namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

class AbstractEntity
{
    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(type="uuid")
     */
    protected $id;
    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;
    /**
     * @var \DateTime|null
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $updatedAt;
    /**
     * AbstractEntity constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $this->id = Uuid::uuid4()->toString();
        $this->createdAt = new \DateTime();
        $this->updatedAt = null;
    }
    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
    /**
     * @return \DateTime|null
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
    /**
     * @throws \Exception
     */
    public function onUpdatedAt()
    {
        $this->updatedAt = new \DateTime();
    }
}
