<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CommentRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Comment
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(type="text")
     */
    private $contenu;

    /**
     * @var App\Entity\User
     *
     * @Assert\Valid
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User", cascade={"persist","remove"})
     */
    private $auhtor;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Figure",inversedBy="comments")
     */
    private $figure;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getContenu()
    {
        return $this->contenu;
    }

    /**
     * @param string $contenu
     *
     * @return self
     */
    public function setContenu($contenu)
    {
        $this->contenu = $contenu;

        return $this;
    }

    /**
     * @return App\Entity\User
     */
    public function getAuhtor()
    {
        return $this->auhtor;
    }

    /**
     * @param App\Entity\User $auhtor
     *
     * @return self
     */
    public function setAuhtor(User $auhtor)
    {
        $this->auhtor = $auhtor;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     *
     * @return self
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function setCreatedAtNow()
    {
        if (null == $this->getCreatedAt()) {
            $this->setCreatedAt(new \DateTime('now'));
        }
    }

    /**
     * @return mixed
     */
    public function getFigure()
    {
        return $this->figure;
    }

    /**
     * @param mixed $figure
     *
     * @return self
     */
    public function setFigure(Figure $figure)
    {
        $this->figure = $figure;

        return $this;
    }
}
