<?php
/*
 * Script to allow insertion of temperature in DB.
 * AuthID : required to allow insertion
 *
 */
$dbhost = '127.0.0.1';
$dbuser = 'home_temp';
$dbpass = 'dbpass';
$dbname = 'home_temp';
$ret = 'KO';
$msg = '';
$apikey = 'longkey.....';

$captID = $_GET['captID'];
$temp   = $_GET['temp'];

if($apikey == $_GET['apikey']) {

    $db = new mysqli($dbhost,$dbuser,$dbpass,$dbname, 3307);

    if ($db->connect_errno) {
        $msg = "DB Connection Error : (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    } else {

        $sql = "INSERT INTO external_temps( sonde_ID, temp) VALUES ($captID,$temp)";

        if (!$db->query($sql)) {
            $msg = "Data insert failed : (" . $db->errno . ") " . $db->error;
            /*
            * Add some DB Cleanup (Like keep only Hourly measure for last week)
            */

        } else {
            $ret = 'OK';
        }
    }
} else {
    $msg = "Wrong API Key";
}
/*
 * Send back action status
 */
$retVal = array('status' => $ret, 'msg' => $msg);
echo json_encode($retVal);
?>