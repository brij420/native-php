<?php

class Database {

    function db_connect() {
	$host = '';

	$host = 'localhost';
	// $db = 'test_db_mysnl'; //database name
	// $user = 'root'; //database user
	// $pass = ''; //database password
	$db = 'exotic_Live'; //database name
	$user = 'exotic_exoticf'; //database user
	$pass = 'exoticfriends'; //database password

	$conn = mysql_connect($host, $user, $pass) or die(mysql_error());
	mysql_select_db($db, $conn);
    }

}

$obj_db = new Database();
$obj_db->db_connect();
$query = "SELECT * FROM event_list where latitude=''  AND  longitude=''  order by even_id asc ";

$result = execute_query($query, true, "select");
$str = '';

if (!empty($result) && ($result['count']) > 0 && (is_array($result))) {
    foreach ($result as $kk => $row) {
	$even_id = $row['even_id'];

	if (isset($row['even_addr']) && ($row['even_addr'])) {
	    $even_addr = $row['even_addr'];
	    $addr = str_replace(" ", "%20", $even_addr);
	    $str.=$addr . ',';
	}
	if (isset($row['even_state']) && ($row['even_state'])) {
	    $loc_state = $row['even_state'];
	    $state = str_replace(" ", "%20", $loc_state);
	    $str.=$state . ',';
	}
	if (isset($row['even_city']) && ($row['even_city'])) {
	    $city_old = $row['even_city'];
	    $city = str_replace(" ", "%20", $city_old);
	    $str.=$city . ',';
	}
	if (isset($row['even_zip']) && ($row['even_zip'])) {
	    $zip = $row['even_zip'];
	    $str.=$zip;
	}
	if (isset($row['even_country']) && ($row['even_country'])) {
	    $loc_country = $row['even_country'];
	    $country = str_replace(" ", "%20", $loc_country);
	    $str.=$country;
	}
	if ($str != '') {
	    $homepage = file_get_contents("http://maps.google.com/maps/geo?q=$str&output=json");
	    $decode = json_decode($homepage, TRUE);

	    $longitude = $decode['Placemark'][0]['Point']['coordinates'][0];
	    $latitude = $decode['Placemark'][0]['Point']['coordinates'][1];

	    $query_update = "UPDATE event_list SET latitude = '$latitude' ,longitude = '$longitude' WHERE even_id='$even_id'";

	    //execute_query($query_update, true, "update");
	}
    }
    echo "<BR>Lan-Long updated at " . date("Y-m-d H:i:s");
} else {
    echo "<BR>No records with empty lat-long found in even_list table";
}

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