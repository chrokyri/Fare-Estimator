<?php

require_once('../lib/Segment.php');
require_once('../lib/SegmentCharge.php');
require_once('../lib/Tuple.php');

class SegmentTest extends PHPUnit_Framework_TestCase
{
    private $p1, $p2;

    public function setUp()
    {
        $this->p1 = new Tuple(array(1, 37.966660, 23.728308, 1405594957));
        $this->p2 = new Tuple(array(1, 37.966627, 23.728263, 1405594966));
    }

    public function testCalculateSpeedAndFare()
    {
        $segment = new Segment($this->p1, $this->p2);
        $this->assertEquals(2.1550435802536, $segment->calculateSpeed(), '', 0.0000000000001);
        $this->assertEquals(4.625E-5, $segment->calculateFareEstimation());
    }

    public function testGetDistanceInKilometers() // private method
    {
        $segment = new Segment($this->p1, $this->p2);
        $segmentRef = new ReflectionClass('Segment');
        $method = $segmentRef->getMethod('_getDistanceInKilometers');
        $method->setAccessible(true);

        $this->assertEquals(0.005387608950634, $method->invokeArgs($segment, array()), '', 0.000000000000001);
    }

    public function testGetElapsedTimeInHours() // private method
    {
        $segment = new Segment($this->p1, $this->p2);
        $segmentRef = new ReflectionClass('Segment');
        $method = $segmentRef->getMethod('_getElapsedTimeInHours');
        $method->setAccessible(true);

        $this->assertEquals(0.0025, $method->invokeArgs($segment, array()));
    }

}
