<?php

class Database {

    function db_connect() {
        $host = '';
        $db = 'exotic_exoticfriendsDev'; //database name
        $user = 'exotic_exoticf'; //database user
        $pass = 'exoticfriends'; //database password

        $conn = mysql_connect($host, $user, $pass);
        mysql_select_db($db, $conn);
    }

}

$obj_db = new Database();
$obj_db->db_connect();
$query = "SELECT * FROM event_list where latitude=''  AND  longitude=''  order by even_id asc ";

$result = mysql_query($query);
if (mysql_num_rows($result) > 0) {
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
        $decode = array();
        $even_id = $row['even_id'];
        $str = '';

        // if (isset($row['even_state']) && ($row['even_state'])) {
            // $loc_state = $row['even_state'];
            // $state = str_replace(" ", "%20", $loc_state);
            // $str.=$state . ',';
        // }
		if (isset($row['even_city']) && ($row['even_city'])) {
            $city_old = $row['even_city'];
            $city = str_replace(" ", "%20", $city_old);
            $str.=$city . ',';
        }
        if (isset($row['even_country']) && ($row['even_country'])) {
            $loc_country = $row['even_country'];
            $country = str_replace(" ", "%20", $loc_country);
            $str.=$country;
        }
        
        // if (isset($row['even_zip']) && ($row['even_zip'])) {
            // $zip = $row['even_zip'];
            // $str.=$zip;
        // }
		echo "http://maps.google.com/maps/geo?q=$str&output=json";
        $homepage = file_get_contents("http://maps.google.com/maps/geo?q=$str&output=json");
        $decode = json_decode($homepage, TRUE);
		
        //if (file_get_contents($homepage) !== FALSE) {
// $even_id = $row['even_id'];
        $longitude = $decode['Placemark'][0]['Point']['coordinates'][0];
        $latitude = $decode['Placemark'][0]['Point']['coordinates'][1];
		print_r($longitude.' '.$latitude);
        // print_r($latitude.$longitude.'\n'); print_r($decode);
        echo $query_update = "UPDATE event_list SET latitude = '$latitude' ,longitude = '$longitude' WHERE even_id='$even_id'";
        
		// die();
		mysql_query($query_update);
		echo '<br>';
        // } else {
        //    echo "Cannot access '$homepage' to read contents.";
        // }
    }
}
?>