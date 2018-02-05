<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FigureRepository")
 * @UniqueEntity("name")
 * @ORM\HasLifecycleCallbacks
 */
class Figure
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255)
     */
    private $slug;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @var App\Entity\Group
     *
     * @Assert\Valid
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Group", cascade={"persist","remove"})
     */
    private $group;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime",nullable=true)
     */
    private $editedAt;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment",mappedBy="figure",cascade={"persist", "remove"})
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Picture",mappedBy="figure",cascade={"persist", "remove"})
     */
    private $images;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Video",mappedBy="figure",cascade={"persist", "remove"})
     */
    private $videos;

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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return self
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Group
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param Group $group
     *
     * @return self
     */
    public function setGroup(Group $group)
    {
        $this->group = $group;

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
     * @return \DateTime
     */
    public function getEditedAt()
    {
        return $this->editedAt;
    }

    /**
     * @param \DateTime $editedAt
     *
     * @return self
     */
    public function setEditedAt(\DateTime $editedAt)
    {
        $this->editedAt = $editedAt;

        return $this;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function setEditedAtNow()
    {
        if (null != $this->getCreatedAt()) {
            $this->setEditedAt(new \DateTime('now'));
        }
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

    ////////////////

    public function addComment(Comment $comment)
    {
        $this->comments[] = $comment;

        return $this;
    }

    public function removeComment(Comment $comment)
    {
        $this->comments->removeElement($comment);
    }

    public function getComments()
    {
        return $this->comments;
    }

    /////////////

    public function addVideo(Video $video)
    {
        $this->videos[] = $video;

        return $this;
    }

    public function removeVideo(Video $video)
    {
        $this->videos->removeElement($video);
    }

    public function getVideos()
    {
        return $this->videos;
    }

    //////////////

    public function addImage(Picture $image)
    {
        $this->images[] = $image;

        return $this;
    }

    public function removeImage(Picture $image)
    {
        $this->images->removeElement($image);
    }

    public function getImages()
    {
        return $this->images;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     *
     * @return self
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function putSlug()
    {
        if (null == $this->getSlug()) {
            $slug = $this->slugify($this->name);
            $this->setSlug($slug);
        }
    }

    private function slugify($str)
    {
        $search = ['', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''];
        $replace = ['s', 't', 's', 't', 's', 't', 's', 't', 'i', 'a', 'a', 'i', 'a', 'a', 'e', 'E'];
        $str = str_ireplace($search, $replace, strtolower(trim($str)));
        $str = preg_replace('/[^\w\d\-\ ]/', '', $str);
        $str = str_replace(' ', '-', $str);

        return preg_replace('/\-{2,}/', '-', $str);
    }
}
