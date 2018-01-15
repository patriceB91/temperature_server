<?php
/*
 * Script to allow insertion of temperature in DB.
 * AuthID : required to allow insertion
 * 
 * - Update to insert Date Time from script, instead of AUTO in DB to keep exact same time for all probes.
 *
 *  External config.php file is required. 
 *  It must contains the following information :
 * 
 *      $dbhost = '127.0.0.1';
 *      $dbuser = 'db_user_name';
 *      $dbpass = 'db_passwd';
 *      $dbname = 'home_temp';
 *      $apikey = 'A secret key that must match the ESP code';
 * 
 */
require_once('config.php');

$ret = 'KO';
$msg = '';

$captID = $_GET['captID'];
$sondeHexID = $_GET['sondeID'];
$temp   = $_GET['temp'];

$insertDate = date("Y-m-d H:i:s");

if($apikey == $_GET['apikey']) {

    $db = new mysqli($dbhost,$dbuser,$dbpass,$dbname, 3307);

    if ($db->connect_errno) {
        $msg = "DB Connection Error : (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    } else {

        $sql = "INSERT INTO external_temps( sondeNum, sondeID, temp, date_time) VALUES ($captID,'$sondeHexID',$temp, '$insertDate')";
        //echo "SQL : " . $sql;

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