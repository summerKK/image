<?php

use Intervention\Image\Gd\Commands\LimitColorsCommand as LimitColorsGd;
use Intervention\Image\Imagick\Commands\LimitColorsCommand as LimitColorsImagick;

class LimitColorsCommandTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }
    
    public function testGd()
    {
        $size = Mockery::mock('\Intervention\Image\Size', [32, 32]);
        $resource = imagecreatefromjpeg(__DIR__.'/images/test.jpg');
        $image = Mockery::mock('Intervention\Image\Image');
        $image->shouldReceive('getCore')->once()->andReturn($resource);
        $image->shouldReceive('setCore')->once();
        $image->shouldReceive('getSize')->once()->andReturn($size);
        $command = new LimitColorsGd([16]);
        $result = $command->execute($image);
        $this->assertTrue($result);
    }

    public function testImagick()
    {
        $size = Mockery::mock('\Intervention\Image\Size', [32, 32]);
        $imagick = Mockery::mock('\Imagick');
        $imagick->shouldReceive('separateImageChannel')->with(\Imagick::CHANNEL_ALPHA)->times(2);
        $imagick->shouldReceive('transparentPaintImage')->with('#ffffff', 0, 0, false)->once();
        $imagick->shouldReceive('negateImage')->with(false)->once();
        $imagick->shouldReceive('quantizeImage')->with(16, \Imagick::COLORSPACE_RGB, 0, false, false)->once();
        $imagick->shouldReceive('compositeImage')->once();
        $image = Mockery::mock('Intervention\Image\Image');
        $image->shouldReceive('getSize')->once()->andReturn($size);
        $image->shouldReceive('getCore')->times(3)->andReturn($imagick);
        $command = new LimitColorsImagick([16]);
        $result = $command->execute($image);
        $this->assertTrue($result);
    }
}
