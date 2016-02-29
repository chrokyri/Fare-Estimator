<?php

require_once('../lib/SegmentCharge.php');

class SegmentChargeTest extends PHPUnit_Framework_TestCase
{
    private static $params = array(
        'speed' => '11',
        'distanceInKm' => '1',
        'elapsedTimeInHours' => '0.8499649859944'
    );

    public function testCalculateDayRoute()
    {
        self::$params['timeFirstTouplet'] = '1442487757'; // '14:02:37'
        self::$params['timeSecondTuplet'] = '1442487766'; // '14:02:46'
        $segmentCharge = new SegmentCharge(self::$params);
        $this->assertEquals(0.68, $segmentCharge->calculate());
    }

    public function testCalculateNightRoute()
    {
        self::$params['timeFirstTouplet'] = '1442437237'; // '00:00:37'
        self::$params['timeSecondTuplet'] = '1442437246'; // '00:00:46'
        $segmentCharge = new SegmentCharge(self::$params);
        $this->assertEquals(1.19, $segmentCharge->calculate());
    }

    public function testCalculateDayToNightRoute()
    {
        self::$params['timeFirstTouplet'] = '1442523595'; // '23:59:55'
        self::$params['timeSecondTuplet'] = '1442437205'; // '00:00:05'
        $segmentCharge = new SegmentCharge(self::$params);
        $this->assertEquals(0.8499649859944, $segmentCharge->calculate(), '', 0.000001);
    }

    public function testCalculateNightToDayRoute()
    {
        self::$params['timeFirstTouplet'] = '1442455195'; // '04:59:55'
        self::$params['timeSecondTuplet'] = '1442455205'; // '05:00:04'
        $segmentCharge = new SegmentCharge(self::$params);
        $this->assertEquals(0.8499649859944, $segmentCharge->calculate(), '', 0.000001);
    }

    public function testCalculateIdleRoute()
    {
        $otherParams = array(
            'speed' => '2.1550435802536',
            'timeFirstTouplet' => '04:59:50',
            'timeSecondTuplet' => '05:00:05',
            'distanceInKm' => '0.005387608950634',
            'elapsedTimeInHours' => '0.0025'
        );
        $segmentCharge = new SegmentCharge($otherParams);
        $this->assertEquals(0.004625, $segmentCharge->calculate(), '', 0.000001);
    }

}
