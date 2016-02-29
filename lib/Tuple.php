<?php

/**
 * Tuple (p) is a single point into Route.
 * That class has important info about each Tuple
 *
 * @package    Tuple
 * @author     Kyriakidis Chronis <kyriakidischronis@gmail.com>
 */
class Tuple
{
    private $rideId;
    private $latitude;
    private $longitude;
    private $latitudeRadian;
    private $longitudeRadian;
    private $timestamp;

    public function __construct(array $tupleDetails)
    {
        $this->rideId = $tupleDetails[0];
        $this->latitude = $tupleDetails[1];
        $this->longitude = $tupleDetails[2];
        $this->latitudeRadian = deg2rad($tupleDetails[1]); // convert from degrees to radian
        $this->longitudeRadian = deg2rad($tupleDetails[2]); // convert from degrees to radian
        $this->timestamp = $tupleDetails[3];
    }

    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }

}
