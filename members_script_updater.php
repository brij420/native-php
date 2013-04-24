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
$query = "SELECT * FROM members";

$result = mysql_query($query);
if (mysql_num_rows($result) > 0) {
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
        $decode = array();
        $mem_id = $row['mem_id'];
        $str = '';

        if (isset($row['state']) && ($row['state'])) {
            $loc_state = $row['state'];
            $state = str_replace(" ", "%20", $loc_state);
            $str.=$state . ',';
        }

        if (isset($row['country']) && ($row['country'])) {
            $loc_country = $row['country'];
            $country = str_replace(" ", "%20", $loc_country);
            $str.=$country . ',';
        }
        if (isset($row['city']) && ($row['city'])) {
            $city_old = $row['city'];
            $city = str_replace(" ", "%20", $city_old);
            $str.=$city . ',';
        }
        if (isset($row['zip']) && ($row['zip'])) {
            $zip = $row['zip'];
            $str.=$zip;
        }
        $homepage = file_get_contents('http://maps.google.com/maps/geo?q="' . $city . ',' . $state . ',' . $country . ',' . $zip . '"&output=json');
        $decode = json_decode($homepage, TRUE);
        //if (file_get_contents($homepage) !== FALSE) {

        $longitude = $decode['Placemark'][0]['Point']['coordinates'][0];
        $latitude = $decode['Placemark'][0]['Point']['coordinates'][1];
        echo $str;
       echo $query_update = "UPDATE members SET latitude = '$latitude' ,longitude = '$longitude' WHERE mem_id='$mem_id'";
        mysql_query($query_update);
        // } else {
        //    echo "Cannot access '$homepage' to read contents.";
        // }
    }
}
?>