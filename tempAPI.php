<?php
require_once('config.php');

$msg = '';
$status = 'KO';

$period = $_GET['period'];

$capteurs = array('Air' => '28FFB35BA415041F', 'Pool' => '28FF3F2FA4150453');

$db = new mysqli($dbhost,$dbuser,$dbpass,$dbname, 3307);
    
    if ($db->connect_errno) {
        $msg = "DB Connection Error : (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    } else {
        $yset = false;
        $result = array();
        foreach($capteurs as $key => $value) {

                switch ($period) {
                    case 'thisweek' :
                        $sql = "SELECT `temp`, DATE_FORMAT(`date_time`, '%d/%m/%Y - %H:00') as time, date_time
                                    FROM `external_temps` WHERE `sondeID` = '{$value}' 
                                    AND `date_time` >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                                    AND `date_time` < CURDATE()
                                UNION ALL
                                SELECT `temp`, DATE_FORMAT(`date_time`, '%H:%i') as time, date_time 
                                    FROM `external_temps` WHERE `sondeID` = '{$value}' 
                                    AND `date_time` >= CURDATE()
                                    ORDER BY `date_time` ASC";
                        break;


                    default: 
                    // $sql = "SELECT  INTO external_temps( sondeNum, sondeID, temp) VALUES ($captID,'$sondeHexID',$temp)";
                    // $sql = "SELECT `temp`, `date_time` FROM `external_temps` WHERE `sondeID` = '28FF0447A415042B' ORDER BY `date_time` ASC";
                    $sql = "SELECT `temp`, DATE_FORMAT(`date_time`, '%H:%i') as time
                            FROM `external_temps` WHERE `sondeID` = '{$value}' 
                            AND `date_time` >= CURDATE()
                            ORDER BY `time` ASC";
                        break;
                }
                // echo "SQL : " . $sql;
                $res = $db->query($sql);

                if( !$res ) {
                    $msg = "Data read failed : (" . $db->errno . ") " . $db->error;
                } else {
                    $ret = 'OK';

                    $rowsx = array();
                    $rowsx['name'] = $key;
                    if(!$yset) {
                        $rowsy = array();
                        $rowsy['name'] = 'Period';
                    }
                    while($row = $res->fetch_array(MYSQLI_ASSOC)) {
                        $rowsx['data'][] = $row['temp'];
                        if (!$yset) {
                            $rowsy['labels'][] = $row['time'];
                        }
                    }
    
                    if(!$yset) {
                        array_push($result,$rowsy);
                        $yset = true;
                    }
                    array_push($result,$rowsx);
                }
        }
    }

/*
 * Send back action status
 */
$retVal = array('status' => $ret, 'msg' => $msg, 'data' => $result);
echo json_encode($retVal, JSON_NUMERIC_CHECK);
?>