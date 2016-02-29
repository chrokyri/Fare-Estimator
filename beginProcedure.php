<?php

define('ROOT_DIR', dirname(__FILE__));

foreach (array('FareEstimator', 'Tuple', 'Segment', 'SegmentCharge','loadCsvFile','SpitInThreads') as $lib) {
    require_once(ROOT_DIR . './lib/' . $lib . '.php');
}

echo "Load file.\n";
$csvFile = ROOT_DIR . '/csv_files/paths.csv';
$fareEstimator = new FareEstimator($csvFile);

echo "Run calculation.\n";
$fareEstimator->executeFareCalculation();

echo "Calculation is finished.\n";