<?php

/**
 * Calculate Charge Foreach Segment
 *
 * @package    SegmentCharge
 * @author     Kyriakidis Chronis <kyriakidischronis@gmail.com>
 */
class SegmentCharge
{
    const
        SPEED_LIMIT = 10, // in km/h
        DAY_CHARGE = 0.68, // when moving (per km)
        NIGHT_CHARGE = 1.19, // when moving (per km)
        IDLE_CHARGE = 1.85, // IDLE (per hour)
        NIGHT__ROUTE_START = '24:00:00',
        NIGHT_ROUTE_END = '05:00:00',
        CONVERT_SECONDS_INTO_HOUR = 3600;

    private $params;

    public function __construct(array $params)
    {
        $this->params = $params;
    }

    public function calculate()
    {
        $fare = 0;
        if ($this->params['speed'] > self::SPEED_LIMIT) {
            $fare = $this->_overSpeedLimitCalcFare();
        } else {
            $fare = $this->_underSpeedLimitCalcFare();
        }

        return $fare;
    }

    private function _overSpeedLimitCalcFare()
    {
        $timeFirstTouplet = date('H:i:s', $this->params['timeFirstTouplet']);
        $timeSecondTuplet = date('H:i:s', $this->params['timeSecondTuplet']);

        $fare = 0;
        if ( (($timeFirstTouplet >= self::NIGHT_ROUTE_END) && ($timeFirstTouplet < self::NIGHT__ROUTE_START)) &&
            (($timeSecondTuplet >= self::NIGHT_ROUTE_END) && ($timeSecondTuplet < self::NIGHT__ROUTE_START)) )
        {
            /** Route between 05:00 and 24:00 */
            $fare = (self::DAY_CHARGE / $this->params['distanceInKm']);
        } else if (($timeFirstTouplet < self::NIGHT_ROUTE_END) && ($timeSecondTuplet < self::NIGHT_ROUTE_END)) {
            /** Route between 00:00 and 05:00 */
            $fare = (self::NIGHT_CHARGE / $this->params['distanceInKm']);
        } else {
            if (($timeFirstTouplet < self::NIGHT_ROUTE_END) && ($timeSecondTuplet >= self::NIGHT_ROUTE_END)) {
                /** Route begin between 00:00 and 05:00 and finish between 05:00 and 24:00 */
                $startingTime = self::NIGHT_ROUTE_END;
                $endingTime = $timeSecondTuplet; // get only time from timestamp
            } else {
                /** Route begin between 05:00 and 24:00 and finish between 00:00 and 05:00 */
                $startingTime = $timeFirstTouplet;
                $endingTime = self::NIGHT__ROUTE_START;
            }
            $fare = $this->_getFareSegmentBetweenDayAndNightRoute($startingTime, $endingTime);
        }

        return $fare;
    }

    private function _underSpeedLimitCalcFare()
    {
        $fare = 0;
        if (($this->params['speed'] >= 0) && ($this->params['speed'] <= self::SPEED_LIMIT)) {
            $fare = (self::IDLE_CHARGE * $this->params['elapsedTimeInHours']);
        } else {
            $error = "Speed cannot be negative";
            echo $error;
            error_log($error);
        }

        return $fare;
    }

    private function _getFareSegmentBetweenDayAndNightRoute($startingTime, $endingTime)
    {
        $hoursInDayRoute = $this->_getTimeDiffInHours($startingTime, $endingTime);

        $distanceKmInDayRoute = ($this->params['speed'] * $hoursInDayRoute);
        $distanceKmInNightRoute = $this->params['distanceInKm'] - $distanceKmInDayRoute;

        $fareForDayRoute = $distanceKmInDayRoute / self::DAY_CHARGE;
        $fareForNightRoute = $distanceKmInNightRoute / self::NIGHT_CHARGE;

        $totalFare = $fareForDayRoute + $fareForNightRoute;

        return $totalFare;
    }

    private function _getTimeDiffInHours($startTime, $endTime)
    {
        $startTimeExplode = explode(':', $startTime);
        $endTimeExplode = explode(':', $endTime);

        $timestampStart = mktime($startTimeExplode[0], $startTimeExplode[1], $startTimeExplode[2], date('n'), date('j'),
            date('y'));
        $timestampEnd = mktime($endTimeExplode[0], $endTimeExplode[1], $endTimeExplode[2], date('n'), date('j'),
            date('y'));

        $diff = abs($timestampEnd - $timestampStart) / self::CONVERT_SECONDS_INTO_HOUR;

        return $diff;
    }

    public function __destruct() {
        $this->params = null;
    }

}
