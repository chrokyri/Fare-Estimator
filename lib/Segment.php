<?php

/**
 * Calculate Speed (U), Distance (Dt), Elapsed Time(Dt)
 * foreach Segment
 *
 * @package    Segment
 * @author     Kyriakidis Chronis <kyriakidischronis@gmail.com>
 */
class Segment
{
    const
        RADIUS_OF_EARTH = 6371000, // Earth's radius in meters.
        CONVERT_SECONDS_INTO_HOUR = 3600,
        CONVERT_METERS_INTO_KILOMETERS = 1000,
        CONVERT_CENTS_INTO_EURO = 100;

    private $p1, $p2;

    private $u;

    private $distance;

    private $elapsedTime;

    public function __construct(TUPLE $p1, TUPLE $p2)
    {
        $this->p1 = $p1;
        $this->p2 = $p2;
    }

    public function calculateFareEstimation()
    {
        $params = array(
            'speed' => $this->u,
            'timeFirstTouplet' => $this->p1->__get('timestamp'),
            'timeSecondTuplet' => $this->p2->__get('timestamp'),
            'distanceInKm' => $this->distance,
            'elapsedTimeInHours' => $this->elapsedTime
        );
        $segmentCharge = new SegmentCharge($params);
        $fares = $segmentCharge->calculate() / self::CONVERT_CENTS_INTO_EURO;

        return $fares;
    }

    /** Calculate Speed U = Ds / Dt */
    public function calculateSpeed()
    {
        $this->distance = $this->_getDistanceInKilometers();
        $this->elapsedTime = $this->_getElapsedTimeInHours();

        /** Catch Divide by zero problem **/
        if ($this->elapsedTime == 0) {
            $this->u = -1;
        } else {
            $this->u = ($this->distance / $this->elapsedTime);
        }

        return $this->u;
    }

    /** Calculate Distance */
    private function _getDistanceInKilometers()
    {

        $diffLatitude = ($this->p2->__get('latitudeRadian') - $this->p1->__get('latitudeRadian'));
        $diffLongitude = ($this->p2->__get('longitudeRadian') - $this->p1->__get('longitudeRadian'));

        $a = sin($diffLatitude / 2) * sin($diffLatitude / 2) + cos($this->p1->__get('latitudeRadian'))
            * cos($this->p2->__get('latitudeRadian')) * sin($diffLongitude / 2) * sin($diffLongitude / 2);

        $c = 2 * asin(sqrt($a));

        $distance = self::RADIUS_OF_EARTH * $c / self::CONVERT_METERS_INTO_KILOMETERS;
        return $distance;
    }

    /** Calculate Time */
    private function _getElapsedTimeInHours()
    {
        $elapsedTime = ($this->p2->__get('timestamp') - $this->p1->__get('timestamp')) / self::CONVERT_SECONDS_INTO_HOUR;
        return $elapsedTime;
    }

    public function __destruct()
    {
        $this->p1 = null;
        $this->p2 = null;
    }

}
