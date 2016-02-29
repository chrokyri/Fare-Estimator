<?php

/**
 * From that class begin the Fare Estimation Algorithm (entry point)
 *
 * @package    FareEstimator
 * @author     Kyriakidis Chronis <kyriakidischronis@gmail.com>
 */
class FareEstimator
{
    const
        FILE_NAME = 'Fare_Estimate_Export',
        EXPORT_FOLDER = "/exports/",
        MINIMUM_CHARGE = 3.16,
        CHARGE_START = 1.19;

    private $tuples = array();

    public function __construct($csvFile)
    {
        if (isset($csvFile)) {
            $this->tuples = array_map('str_getcsv', fgetcsv($csvFile));
        } else {
            echo "CSV file is empty.\n";
            exit;
        }
    }

    public function executeFareCalculation()
    {
        $multDimArrByRide = $this->_transformIntoMultDimArrayByRideID();
        $totalFareByRide = $this->_getTotalFares($multDimArrByRide);
        $filePath = $this->_convertArrayToCsvFile($totalFareByRide);

        echo "Export created in path : " . $filePath . "\n";
    }

    private function _getTotalFares($multDimArrByRide)
    {
        $totalFareByRide = array();
        foreach ($multDimArrByRide as $rideId => $rideDetails) {
            $totalFareByRide[$rideId] = self::CHARGE_START;
            for ($i = 1; $i < count($rideDetails); $i++) {
                $p1 = new TUPLE($rideDetails[$i - 1]);
                $p2 = new TUPLE($rideDetails[$i]);
                $s = new SEGMENT($p1, $p2);

                $speed = $s->calculateSpeed();
                if (($speed > 100) || ($speed == -1)) {
                    unset($rideDetails[$i]);
                    $rideDetails = array_values($rideDetails); // refresh array after delete item
                    $i--;
                } else {
                    $totalFareByRide[$rideId] += $s->calculateFareEstimation();
                }
            }

            if ($totalFareByRide[$rideId] < self::MINIMUM_CHARGE) {
                $totalFareByRide[$rideId] = self::MINIMUM_CHARGE;
            }
        }
        return $totalFareByRide;
    }

    private function _convertArrayToCsvFile($totalFareByRide)
    {
        $now = new DateTime;
        $filePath = ROOT_DIR . self::EXPORT_FOLDER;
        $file = $filePath . self::FILE_NAME . '__ForDate_' . $now->format('d-m-Y H-i-s') . '.csv';

        if (!is_dir($filePath)) {
            mkdir($filePath, 0777, true);
        }

        $fp = fopen($file, 'w');
        foreach ($totalFareByRide as $rideId => $fareEstimate) {
            fputcsv($fp, array($rideId, $fareEstimate));
        }
        fclose($fp);

        return $file;
    }

    private function _transformIntoMultDimArrayByRideID()
    {
        $tmp = array();
        foreach ($this->tuples as $value) {
            $tmp[$value[0]][] = $value;
        }

        return $tmp;
    }

    private function _increaseFile()
    {
        ini_set('memory_limit', '-1');
        $tmp = array();
        for ($i = 0; $i <= 15000; $i = $i + 9) {
            foreach ($this->tuples as $value) {
                $index = $i + $value[0];
                $value[0]=$index;
                array_push($tmp, $value);
            }
        }

        return $tmp;
    }

    public function __destruct()
    {
        $this->tuples = null;
    }

}
