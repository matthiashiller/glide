<?php

namespace Glide\Manipulators;

use Glide\Request;
use Mockery;

class SizeTest extends \PHPUnit_Framework_TestCase
{
    private $manipulator;
    private $callback;

    public function setUp()
    {
        $this->manipulator = new Size();
        $this->callback = Mockery::on(function () {
            return true;
        });
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testCreateInstance()
    {
        $this->assertInstanceOf('Glide\Manipulators\Size', $this->manipulator);
    }

    public function testRun()
    {
        $image = Mockery::mock('Intervention\Image\Image', function ($mock) {
            $mock->shouldReceive('width')->andReturn('200')->twice();
            $mock->shouldReceive('height')->andReturn('200')->twice();
            $mock->shouldReceive('resize')->with('100', '100', $this->callback)->once();
        });

        $this->manipulator->run(new Request('image.jpg', ['w' => '100']), $image);
    }

    public function testGetWidth()
    {
        $this->assertEquals('100', $this->manipulator->getWidth('100'));
        $this->assertEquals(false, $this->manipulator->getWidth(null));
        $this->assertEquals(false, $this->manipulator->getWidth('a'));
        $this->assertEquals(false, $this->manipulator->getWidth('100.1'));
        $this->assertEquals(false, $this->manipulator->getWidth('-100'));
    }

    public function testGetHeight()
    {
        $this->assertEquals('100', $this->manipulator->getHeight('100'));
        $this->assertEquals(false, $this->manipulator->getHeight(null));
        $this->assertEquals(false, $this->manipulator->getHeight('a'));
        $this->assertEquals(false, $this->manipulator->getHeight('100.1'));
        $this->assertEquals(false, $this->manipulator->getHeight('-100'));
    }

    public function testGetFit()
    {
        $this->assertEquals('contain', $this->manipulator->getFit('contain'));
        $this->assertEquals('max', $this->manipulator->getFit('max'));
        $this->assertEquals('stretch', $this->manipulator->getFit('stretch'));
        $this->assertEquals('crop', $this->manipulator->getFit('crop'));
        $this->assertEquals('contain', $this->manipulator->getFit('invalid'));
    }

    public function testGetCrop()
    {
        $this->assertEquals('top-left', $this->manipulator->getCrop('top-left'));
        $this->assertEquals('top', $this->manipulator->getCrop('top'));
        $this->assertEquals('top-right', $this->manipulator->getCrop('top-right'));
        $this->assertEquals('left', $this->manipulator->getCrop('left'));
        $this->assertEquals('center', $this->manipulator->getCrop('center'));
        $this->assertEquals('right', $this->manipulator->getCrop('right'));
        $this->assertEquals('bottom-left', $this->manipulator->getCrop('bottom-left'));
        $this->assertEquals('bottom', $this->manipulator->getCrop('bottom'));
        $this->assertEquals('bottom-right', $this->manipulator->getCrop('bottom-right'));
        $this->assertEquals('center', $this->manipulator->getCrop(null));
        $this->assertEquals('center', $this->manipulator->getCrop('invalid'));
    }

    public function testRunResize()
    {
        $image = Mockery::mock('Intervention\Image\Image', function ($mock) {
            $mock->shouldReceive('resize')->with('100', '100', $this->callback)->twice();
            $mock->shouldReceive('resize')->with('100', '100')->once();
            $mock->shouldReceive('fit')->with('100', '100', $this->callback, 'center')->once();
        });

        $this->manipulator->runResize($image, 'contain', '100', '100');
        $this->manipulator->runResize($image, 'max', '100', '100');
        $this->manipulator->runResize($image, 'stretch', '100', '100');
        $this->manipulator->runResize($image, 'crop', '100', '100', 'center');
    }

    public function testRunContainResize()
    {
        $image = Mockery::mock('Intervention\Image\Image', function ($mock) {
            $mock->shouldReceive('resize')->with('100', '100', $this->callback)->once();
        });

        $this->manipulator->runContainResize($image, '100', '100');
    }

    public function testRunMaxResize()
    {
        $image = Mockery::mock('Intervention\Image\Image', function ($mock) {
            $mock->shouldReceive('resize')->with('100', '100', $this->callback)->once();
        });

        $this->manipulator->runMaxResize($image, '100', '100');
    }

    public function testRunStretchResize()
    {
        $image = Mockery::mock('Intervention\Image\Image', function ($mock) {
            $mock->shouldReceive('resize')->with('100', '100')->once();
        });

        $this->manipulator->runStretchResize($image, '100', '100');
    }

    public function testRunCropResize()
    {
        $image = Mockery::mock('Intervention\Image\Image', function ($mock) {
            $mock->shouldReceive('fit')->with('100', '100', $this->callback, 'center')->once();
        });

        $this->manipulator->runCropResize($image, '100', '100', 'center');
    }
}