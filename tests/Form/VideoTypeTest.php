<?php

namespace App\Tests\Form;

use App\Entity\Video;
use App\Form\VideoType;
use Symfony\Component\Form\Test\TypeTestCase;

class VideoTypeTest extends TypeTestCase
{
    /**
     * @dataProvider urlForForm
     */
    public function testVideoTypeFormData($url, $valid)
    {
        $formData = [
            'url' => $url,
        ];

        $video = new Video();

        $form = $this->factory->create(VideoType::class, $video);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals(
            $valid,
            preg_match(
                '#^(http|https):\/\/(www.youtube.com|www.dailymotion.com|vimeo.com)\/#',
                $url
            )
        );
        $this->assertEquals($video, $form->getData());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }

    public function urlForForm()
    {
        return [
            ['http://www.youtube.com/watch?v=SA7AIQw-7Ms', true],
            ['https://www.youtube.com/watch?v=SA7AIQw-7Ms', true],
            ['https://www.dailymotion.com/video/x6dzcbf', true],
            ['http://www.dailymotion.com/video/x6dzcbf', true],
            ['https://vimeo.com/250495979', true],
            ['http://vimeo.com/250495979', true],
            ['https://google.com', false],
            ['kswniscincicsin', false],
            ['<script>alert("Hello World")</script>', false],
        ];
    }
}
