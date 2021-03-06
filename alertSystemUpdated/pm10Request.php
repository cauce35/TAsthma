<?php
/**
 * Created by Jeremy
 * Date: 31/8/17
 * Time: 6:22 PM
 */

//record current time
function getTime() {
    date_default_timezone_set('Australia/Melbourne');
    return date('YmdH');
}

//curl call to get all measures
function getAPIResponse($date) {
    $url = 'http://sciwebsvc.epa.vic.gov.au/aqapi/SitesHourlyAirQuality?datetimestart=' . $date;
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
    $res = curl_exec($curl);
    curl_close($curl);
    return $res;
}

//process to retrieve pm10 measure and site location from json format, and covert the responese into json response
function processResponse($response) {
    $result = array();
    $json = json_decode($response);
    $sites = $json->NonIncidentSites;
    for ($i = 0; $i < count($sites); $i++) {
        $site = $sites[$i];
        $measurements = $site->Measurements;
        for ($j = 0; $j < count($measurements); $j++) {
            if ($measurements[$j]->ShortName === "PM10") {
                array_push($result,array('lat' => $site->Latitude,
                    'lon' => $site->Longitude, 'value' => $measurements[$j]->Value));
            }
        }
    }
    return json_encode($result);
}