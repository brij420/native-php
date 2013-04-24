<?php

class Database {

    function db_connect() {
	$host = '';

	$host = 'localhost';
	
	$db = 'exotic_Live'; //database name
	$user = 'exotic_exoticf'; //database user
	$pass = 'exoticfriends'; //database password

	$conn = mysql_connect($host, $user, $pass) or die(mysql_error());
	mysql_select_db($db, $conn);
    }

}

$obj_db = new Database();
$obj_db->db_connect();
$query = "SELECT * FROM members where latitude='' AND longitude=''";

$result = execute_query($query, true, "select");
$str = '';

if (!empty($result) && ($result['count']) > 0 && (is_array($result))) {
    foreach ($result as $kk => $row) {
	$mem_id = $row['mem_id'];

	if (isset($result[$kk]['saddress']) && ($result[$kk]['saddress'])) {
	    $saddress = trim($result[$kk]['saddress']);
	    $addrs = str_replace(" ", "%20", $saddress);
	    $str.=$addrs . ',';
	}
	if (isset($result[$kk]['city']) && ($result[$kk]['city'])) {
	    $city_old = trim($result[$kk]['city']);
	    $city = str_replace(" ", "%20", $city_old);
	    $str.=$city . ',';
	}
	if (isset($result[$kk]['state']) && ($result[$kk]['state'])) {
	    $loc_state = trim($result[$kk]['state']);
	    $state = str_replace(" ", "%20", $loc_state);
	    $str.=$state . ',';
	}
	if (isset($result[$kk]['country']) && ($result[$kk]['country'])) {
	    $loc_country = trim($result[$kk]['country']);
	    $country = str_replace(" ", "%20", $loc_country);
	    $str.=$country . ',';
	}

	if (isset($result[$kk]['zip']) && ($result[$kk]['zip'])) {
	    $zip = trim($result[$kk]['zip']);
	    $str.=$zip;
	}
	if ($str != '') {
	    $homepage = file_get_contents('http://maps.google.com/maps/geo?q=' . $str . '&output=json');
	    $decode = json_decode($homepage, TRUE);
	    //if (file_get_contents($homepage) !== FALSE) {

	    $longitude = $decode['Placemark'][0]['Point']['coordinates'][0];
	    $latitude = $decode['Placemark'][0]['Point']['coordinates'][1];
	    $query_update = "UPDATE members SET latitude = '$latitude' ,longitude = '$longitude' WHERE mem_id='$mem_id'";
	   // execute_query($query_update, true, "update");
	}
    }
    echo "Lan-Long updated at " . date("Y-m-d H:i:s");
}
else
    echo "<BR>No records with empty lat-long found in members table.";

function execute_query($query, $check, $querytype="select") {

    $query_result = array();
    $count = 0;

    $result = mysql_query($query) OR die(mysql_error());

    if ($querytype == "select") {


	if ((mysql_num_rows($result) > 0) && ($check)) {
	    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$query_result[] = $row;
		$count++;
	    }
	    if (($check) && ($count)) {
		$query_result['count'] = $count;
	    } else {
		$query_result['count'] = 0;
	    }
	    if (!isset($query_result['count'])) {
		unset($query_result['count']);
	    }
	}
	if ((mysql_num_rows($result) > 0) && (!$check)) {
	    $query_result = mysql_fetch_array($result, MYSQL_ASSOC);
	}
    }
    if ($querytype == "insert") {
	$query_result['count'] = mysql_affected_rows();
	$query_result['last_id'] = mysql_insert_id();
    }
    if ($querytype == "delete") {
	$query_result['count'] = mysql_affected_rows();
    }
    if ($querytype == "update") {
	$query_result['count'] = mysql_affected_rows();
    }
    return $query_result;
}
?>