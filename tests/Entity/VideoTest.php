<?php

namespace App\Tests\Entity;

use PHPUnit\Framework\TestCase;
use App\Entity\Video;

class VideoTest extends TestCase
{
    public function testYoutubeIntegration()
    {
        $video = (new Video())->setUrl('https://www.youtube.com/watch?v=SA7AIQw-7Ms');
        $video->extractIdentif();

        $this->assertSame('https://www.youtube-nocookie.com/embed/SA7AIQw-7Ms', $video->urlVideo());
        $this->assertSame('https://img.youtube.com/vi/SA7AIQw-7Ms/hqdefault.jpg', $video->urlImage());
    }

    public function testDailyMotionIntegration()
    {
        $video = (new Video())->setUrl('https://www.dailymotion.com/video/x6dzcbf');
        $video->extractIdentif();

        $this->assertSame('https://www.dailymotion.com/embed/video/x6dzcbf', $video->urlVideo());
        $this->assertSame('https://www.dailymotion.com/thumbnail/150x120/video/x6dzcbf', $video->urlImage());
    }

    public function testVimeoIntegration()
    {
        $video = (new Video())->setUrl('https://vimeo.com/250495979');
        $video->extractIdentif();

        $this->assertSame('https://player.vimeo.com/video/250495979', $video->urlVideo());
        $this->assertRegExp('#https\:\/\/i\.vimeocdn\.com\/video\/([0-9])+_100x75\.jpg#', $video->urlImage());
    }
}
