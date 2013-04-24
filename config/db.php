<?php
class Database{
function db_connect(){
       $host='';
	   
	   
	$db='exotic_Live';//database name
	$user='exotic_exoticf';//database user
	$pass='exoticfriends';//database password
	
	// $db='exotic_exoticfriendsDev';//database name
	// $user='exotic_exoticf';//database user
	// $pass='exoticfriends';//database password

//        $host='localhost';
//	$db='exotic_live_123';//database name
//	$user='root';//database user
//	$pass='root';//database password

        $conn=mysql_connect($host, $user, $pass);
			mysql_query('SET character_set_results=utf8');
			mysql_query('SET names=utf8');
			mysql_query('SET character_set_client=utf8');
			mysql_query('SET character_set_connection=utf8');
			mysql_query('SET character_set_results=utf8');
			mysql_query('SET collation_connection=utf8_general_ci');
        mysql_select_db($db,$conn);
}
}

$obj_db=new Database();
$obj_db->db_connect();
?>
