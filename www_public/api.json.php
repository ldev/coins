<?php
    
    # require symbol to be set
    if(!isset($_GET['symbol'])){
        exit('Missing coin pair (?symbol=LTCBTC)');
    }

    $symbol = preg_replace('/[^a-z]/i', '', $_GET['symbol']);
    
    # Load shittycomposer stuff (e.g. influx interface)
    require('../composer/vendor/autoload.php');

	# instantiate influx client
	$client = new InfluxDB\Client('localhost', 8086);

	# use influx "coins" database
	$database = $client->selectDB('coins');

    # perform query
    if(isset($_GET['duration']) && $_GET['duration'] === '1h'){
        $result = $database->query('select * from price WHERE pair = \'' . $symbol . '\' AND time > now() - 1h');
    }else{
        $result = $database->query('select * from price WHERE pair = \'' . $symbol . '\' AND time > now() - 4w');
    }
    $points = $result->getPoints();
    
    # debug
    # var_dump($points);

    $output_array = array();
    foreach($points as $point){
        $date = new DateTime($point['time']);
        $output_array[] = array($date->getTimestamp(), (float) $point['value']);
    }

    exit(json_encode(array('status' => 'success', 'data' => $output_array), JSON_PRETTY_PRINT));
