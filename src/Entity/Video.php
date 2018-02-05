<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VideoRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Video
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
     * @ORM\Column(name="plateforme", type="string", length=20)
     */
    private $plateforme;

    /**
     * @var string
     *
     * @ORM\Column(name="identifiant", type="string", length=255)
     */
    private $identifiant;

    /**
     * @Assert\Regex(
     *     pattern="#^(http|https):\/\/(www.youtube.com|www.dailymotion.com|vimeo.com)\/#",
     *     match=true,
     *     message="L'url doit correspondre à l'url d'une vidéo Youtube, DailyMotion ou Vimeo"
     * )
     */
    private $url;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Figure",inversedBy="videos")
     */
    private $figure;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getPlateforme(): string
    {
        return $this->plateforme;
    }

    /**
     * @param string $plateforme
     *
     * @return self
     */
    public function setPlateforme(string $plateforme)
    {
        $this->plateforme = $plateforme;

        return $this;
    }

    /**
     * @return string
     */
    public function getIdentifiant(): string
    {
        return $this->identifiant;
    }

    /**
     * @param string $identifiant
     *
     * @return self
     */
    public function setIdentifiant(string $identifiant)
    {
        $this->identifiant = $identifiant;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     *
     * @return self
     */
    public function setUrl(string $url)
    {
        $this->url = $url;

        return $this;
    }

    private function youtubeId($url)
    {
        $tableaux = explode('=', $url);

        $this->setIdentifiant($tableaux[1]);
        $this->setPlateforme('youtube');
    }

    private function dailymotionId($url)
    {
        $cas = explode('/', $url);
        $idb = $cas[4];
        $bp = explode('_', $idb);
        $id = $bp[0];

        $this->setIdentifiant($id);
        $this->setPlateforme('dailymotion');
    }

    private function vimeoId($url)
    {
        $tableaux = explode('/', $url);

        $id = $tableaux[count($tableaux) - 1];

        $this->setIdentifiant($id);
        $this->setPlateforme('vimeo');
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     * @ORM\PreFlush()
     */
    public function extractIdentif()
    {
        $url = $this->getUrl();

        if (preg_match('#^(http|https)://www.youtube.com/#', $url)) {
            $this->youtubeId($url);
        } elseif ((preg_match('#^(http|https)://www.dailymotion.com/#', $url))) {
            $this->dailymotionId($url);
        } elseif ((preg_match('#^(http|https)://vimeo.com/#', $url))) {
            $this->vimeoId($url);
        }
    }

    public function urlVideo()
    {
        $control = $this->getPlateforme();
        $id = strip_tags($this->getIdentifiant());

        if ('youtube' == $control) {
            $embed = 'https://www.youtube-nocookie.com/embed/'.$id;

            return $embed;
        } elseif ('dailymotion' == $control) {
            $embed = 'https://www.dailymotion.com/embed/video/'.$id;

            return $embed;
        } elseif ('vimeo' == $control) {
            $embed = 'https://player.vimeo.com/video/'.$id;

            return $embed;
        }
    }

    public function urlImage()
    {
        $control = $this->getPlateforme();
        $id = strip_tags($this->getIdentifiant());

        if ('youtube' == $control) {
            $image = 'https://img.youtube.com/vi/'.$id.'/hqdefault.jpg';

            return $image;
        } elseif ('dailymotion' == $control) {
            $image = 'https://www.dailymotion.com/thumbnail/150x120/video/'.$id.'';

            return $image;
        } elseif ('vimeo' == $control) {
            $hash = unserialize(file_get_contents('https://vimeo.com/api/v2/video/'.$id.'.php'));
            $image = $hash[0]['thumbnail_small'];

            return $image;
        }
    }

    public function video()
    {
        $video = "
        <iframe width='100%' height='100%' src='"
        .$this->urlVideo().
        "'  frameborder='0'  allowfullscreen></iframe>";

        return $video;
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
