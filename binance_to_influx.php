<?php
	# Load shittycomposer stuff (e.g. influx interface)
	require('composer/vendor/autoload.php');

	# instantiate influx client
	$client = new InfluxDB\Client('localhost', 8086);

	# use influx "coins" database
	$database = $client->selectDB('coins');

	# used to hold the data that will be written to influx
	$points = array();

	$json = json_decode(file_get_contents('https://api.binance.com/api/v1/ticker/price'), true);
	foreach($json as $pair){
		if(isset($pair['symbol'])){
			$points[] = new InfluxDB\Point('price', $pair['price'], ['pair' => $pair['symbol'], 'exchange' => 'Binance'], [], date('U'));
		}
	}
	$result = $database->writePoints($points, InfluxDB\Database::PRECISION_SECONDS);
	if($result === true){
		exit(0);
	}else{
		exit(1);
	}
