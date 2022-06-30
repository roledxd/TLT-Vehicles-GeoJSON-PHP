<?php
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/javascript; charset=utf-8');
    include_once("CSVParser.php");
    $jsonParser = CSVParserFactory::Create("json");
    $jsonParser->IsFirstRowHeader = false;
    $url = "http://transport.tallinn.ee/gps.txt";
    $HIDE_PARKED = true; //does not show parked vehicles (not in service)
    $json = $jsonParser->Parse($url);
    $features = [];
    $proc = 0;
    foreach(json_decode($json) as $vehicle) 
    {
        $veh->type = "Feature";
        $veh->geometry->type = "Point";
        $veh->geometry->coordinates = [$vehicle[2]/ 1000000 , $vehicle[3]/ 1000000 ];
        $veh->properties->transport = $vehicle[0];
        $veh->properties->line = $vehicle[1];
        $veh->properties->sign = $vehicle[4];
        $veh->properties->angle = $vehicle[5];
        $veh->properties->vehicle_id = $vehicle[6];
        
        if($HIDE_PARKED == true){
            if($veh->properties->angle !== "999"){
                $features[] = $veh;
            }
        }else{
            $features[] = $veh;    
        }
        $proc = $proc + 1;
        $veh = null;
    }
    $collection->type = "FeatureCollection";
    $collection->features = $features;
    echo json_encode($collection);
?>
