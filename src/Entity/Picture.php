<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PictureRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Picture
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
     * @ORM\Column(name="extension", type="string", length=6)
     */
    private $extension;

    /**
     * @Assert\NotBlank()
     * @Assert\File(
     *     maxSize = "1M",
     *     mimeTypes = {"image/png", "image/jpeg"},
     *     mimeTypesMessage = "Doit être soit un jpeg ou un png",
     *     maxSizeMessage = "Ne doit pas dépasser 1Mo"
     * )
     */
    private $file;

    private $tempFilename;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Figure",inversedBy="images")
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
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * @param string $extension
     *
     * @return self
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;

        return $this;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function setTempFilename($tempFilename)
    {
        $this->tempFilename = $tempFilename;

        return $this;
    }

    public function getTempFilename()
    {
        return $this->tempFilename;
    }

    public function setFile(File $file)
    {
        $this->file = $file;
        if (!is_null($this->getExtension())) {
            $this->setTempFilename($this->getExtension());
            $this->setExtension(null);
        }
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (is_null($this->file)) {
            return;
        }
        $this->setExtension($this->file->guessExtension());
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (is_null($this->file)) {
            return;
        }
        if (!is_null($this->getTempFilename())) {
            $oldFile = $this->getUploadRootDir().'/'.$this->getId().'.'.$this->getTempFilename();
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
        }

        $this->file->move(
            $this->getUploadRootDir(),
            $this->getId().'.'.$this->getExtension()
        );
    }

    /**
     * @ORM\PreRemove()
     */
    public function preRemoveUpload()
    {
        $this->setTempFilename($this->getUploadRootDir().'/'.$this->getId().'.'.$this->getExtension());
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if (file_exists($this->getTempFilename())) {
            unlink($this->getTempFilename());
        }
    }

    public function getUploadDir()
    {
        return 'uploads/img/media';
    }

    protected function getUploadRootDir()
    {
        return __DIR__.'/../../public/'.$this->getUploadDir();
    }

    public function getWebPath()
    {
        return '/'.$this->getUploadDir().'/'.$this->getId().'.'.$this->getExtension();
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
