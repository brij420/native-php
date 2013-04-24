<?php

function badgesFeature($uid, $venue_id,$time, $announce_id=NULL) {
    $sitename = 'SocialNightlife.com';
    $siteurl = 'http://www.socialnightlife.com';
    // $time = time();
$appNotfn = array();
    /* Trendsetter */
    $sqlAppsHstCnt = "SELECT count(user_id) as cnt  FROM announce_arrival WHERE user_id ='" . $uid . "'";
    $resultAppsHstCnt = execute_query($sqlAppsHstCnt, true, "select");

    $total_count = 0;
    if (!empty($resultAppsHstCnt) && $resultAppsHstCnt['count'] > 0) {

	$total_count = $resultAppsHstCnt[0]['cnt'];

	$sqlBottelalert = "SELECT * from bottel_alert WHERE mail_sending='Y'  AND mem_id='" . $uid . "' AND bottel_type='Trendsetter' ";
	$resultBottelalert = execute_query($sqlBottelalert, true, "select");

	if (empty($resultBottelalert)) {

	    $sqlinsertbottelalert = "INSERT INTO  bottel_alert(`bottel_type`,`badge_type_id` ,`app_id`,`mem_id`, `alert_text`,`createdate`,`venue_id`,`date_alert`)
			  Value('Trendsetter','1','".$announce_id."','" . $uid . "','Congrats! You have just earned the Trendsetter badge','".date('Y-m-d H:i:s',time())."','$venue_id','$time')";
	    $resultinsertbottel = execute_query($sqlinsertbottelalert, true, "insert");
	    $lastinsertid = $resultinsertbottel['last_id'];
	    if ($lastinsertid) {
			//push_notification_for_badges('Congrats! You have just earned the Trendsetter badge', $uid, $uid);
			$appNotfn[]='Congrats! You have just earned the Trendsetter badge';
			
	    }
	    $params[0] = show_from_members($uid);
	    $sitename = "SocialNightlife.com";
	    $sitemail = "info@socialNightlife.com";
	    $params[3] = "You have just earned the Trendsetter bottle.";

	    $mes = "Hello, <br />Congrats! Being a trendsetter gives you a certain edge.<a href='$siteurl/index.php?pg=profile&usr=" . $uid . "&action=viewbottel&sid=123' > Click here</a> to see the full list of bottles.<br />";
	    $mes .='"Elevating The Social Nightlife Experience!"';
	    $params[4] = $mes . "<br/>";
	    if(mailbox($uid, '', $params) == 1){
			$updatebottelalert = "Update bottel_alert SET  mail_sending='Y' WHERE id=" . $lastinsertid;
			execute_query($updatebottelalert, true, "update");
		}
	    //Hotpresspost
	    $proname = show_from_members($uid);
	    $sqlinsertintohotpress = "INSERT INTO bulletin( `id` , `mem_id` ,  `body` , `date` ,`bottle_id`,`auto_genrate_text` )
					     VALUES ( NULL , '" . $uid . "',  'has just earned the Trendsetter Bottles. Congrats on starting something cool!', '" . time() . "','1','Y' )";
	    execute_query($sqlinsertintohotpress, true, "insert");
	}
    } else {
	$total_count = 0;
    }
    /* END Trendsetter */

    /* Viva Las Vegas */
    $sqlCity = "SELECT * FROM members WHERE mem_id = " . $uid . " AND city != 'Las Vegas'";
    $resultCity = execute_query($sqlCity, true, "select");

    if (!empty($resultCity) && $resultCity['count'] > 0) {
	$cityLasVegas = $resultCity[0]['city'];

	$statusLasVegas = 0;
	$sql = "SELECT DISTINCT(venue_id) FROM announce_arrival WHERE user_id = " . $uid . " ";
	$result = execute_query($sql, true, "select");
	if ($result['count'] > 0) {
	    foreach ($result as $kk => $getVenues) {

		$sqlAmb = "SELECT city FROM members  WHERE mem_id = '" . $getVenues['venue_id'] . "' AND city = 'Las Vegas'";

		$resultAmb = execute_query($sqlAmb, true, "select");
		if (is_array($resultAmb) && !empty($resultAmb) && $resultAmb['count'] > 0) {
		    $statusLasVegas = 1;
		}
	    }
	    if ($statusLasVegas == 1) {
		$sqlBottelalert = "SELECT * from bottel_alert WHERE mail_sending='Y' AND mem_id=" . $uid . " AND bottel_type='Las Vegas'";
		$resultBottelalert = execute_query($sqlBottelalert, true, "select");
		if ($resultBottelalert['count'] > 0) {
		    
		} else {
		    $sqlinsertbottelalert = "INSERT INTO  bottel_alert(`bottel_type`,`badge_type_id` ,`app_id`,`mem_id`,  `alert_text`,`createdate`,`venue_id`,`date_alert`)
		  Value('Las Vegas','2','".$announce_id."','" . $uid . "','Congrats! You have just earned the Viva Las Vegas bottle','".date('Y-m-d H:i:s',time())."','$venue_id','$time')";
		    $resultinsertbottel = execute_query($sqlinsertbottelalert, true, "insert");
		    $lastinsertid = $resultinsertbottel['last_id'];
		    if ($lastinsertid) {
				//push_notification_for_badges('Congrats! You have just earned the Viva Las Vegas bottle', $uid, $uid);
				$appNotfn[]='Congrats! You have just earned the Viva Las Vegas bottle';
				
		    }
		    $params[0] = show_from_members($uid);
		    $sitename = "SocialNightlife.com";
		    $sitemail = "info@socialNightlife.com";
		    $params[3] = "You have just earned the Viva Las Vegas bottle.";
		    $mes = "Hello, <br />Congrats on earning the Viva Las Vegas badge. Time to party it up! <a href='$siteurl/index.php?pg=profile&usr=" . $uid . "&action=viewbottel&sid=" . session_id() . "' > Click here</a> to see the full list of bottles<br />";
		    $mes .='"Elevating The Social Nightlife Experience!"';
		    $params[4] = $mes . "<br/>";
		    
			if(mailbox($uid, '', $params) == 1){
				$updatebottelalert = "Update bottel_alert SET  mail_sending='Y' WHERE id=" . $lastinsertid;
				execute_query($updatebottelalert, true, "update");
			}
		    //Hotpresspost
		    $proname = show_from_members($uid);
		    $sqlinsertintohotpress = "INSERT INTO bulletin( `id` , `mem_id` ,  `body` , `date` ,`bottle_id`,`auto_genrate_text` )
		                                 VALUES ( NULL , '" . $uid . "',  'has just earned the Viva Las Vegas Bottles. If you dont hear from them in 2 days, dont worry, Vegas was good to them and recovery has started.', '" . time() . "','2','Y' )";
		    execute_query($sqlinsertintohotpress, true, "insert");
		}
	    }
	}
    }

    /* END Viva Las Vegas */

    /* Tinsel Town */
    $sqlCity = "SELECT * FROM members WHERE mem_id = '" . $uid . "' AND city != 'Los Angeles'";
    $resultCity = execute_query($sqlCity, true, "select");

    $statusLA = 0;
    $sql = "SELECT DISTINCT(venue_id) FROM announce_arrival WHERE user_id = '" . $uid . "'";
    $result = execute_query($sql, true, "select");
    if (is_array($result) && !empty($result) && $result['count'] > 0) {
	foreach ($result AS $kk => $getVenues) {
	    if (is_array($getVenues)) {
		$sqlAmb = "SELECT city FROM members  WHERE mem_id = '" . $getVenues['venue_id'] . "' AND city = 'Los Angeles'";
		$resultAmb = execute_query($sqlAmb, true, "select");
		if (is_array($resultAmb) && !empty($resultAmb) && $resultAmb['count'] > 0) {
		    $statusLA = 1;
		}
	    }
	}
	if ($statusLA == 1) {
	    $sqlBottelalert = "SELECT * from bottel_alert WHERE mail_sending='Y' AND mem_id=" . $uid . " AND bottel_type='Tinsel Town'";
	    $resultBottelalert = execute_query($sqlBottelalert, true, "select");
	    if (empty($resultBottelalert)) {

		$sqlinsertbottelalert = "INSERT INTO  bottel_alert(`bottel_type`,`badge_type_id` ,`app_id`,`mem_id`,`alert_text`,`createdate`,`venue_id`,`date_alert`)
		  Value('Tinsel Town','3','".$announce_id."','" . $uid . "','Congrats! You have just earned the Tinsel Town bottle.','".date('Y-m-d H:i:s',time())."','$venue_id','$time')";
		$resultinsertbottel = execute_query($sqlinsertbottelalert, true, "insert");
		$lastinsertid = $resultinsertbottel['last_id'];
		if ($lastinsertid) {
		    //push_notification_for_badges('Congrats! You have just earned the Tinsel Town bottle.', $uid, $uid);
		    $appNotfn[]='Congrats! You have just earned the Tinsel Town bottle.';
		    
		}
		$params[0] = show_from_members($uid);
		$sitename = "SocialNightlife.com";
		$sitemail = "info@socialNightlife.com";
		$params[3] = "You have just earned the Tinsel Town bottle..";
		$mes = "Hello, <br />Congrats on earning the Tinsel Town Badge . Los Angeles and Hollywood are cities where dreams come true, but for now hit up the famous Sunset strip for a party. You never know who you might bump into <a href='$siteurl/index.php?pg=profile&usr=" . $uid . "&action=viewbottel&sid=123' > Click here</a> to see the full list of bottles<br />";
		$mes .='"Elevating The Social Nightlife Experience!"';
		$params[4] = $mes . "<br/>";
		
		if(mailbox($uid, '', $params) == 1){
			$updatebottelalert = "Update bottel_alert SET  mail_sending='Y' WHERE id=" . $lastinsertid;
			execute_query($updatebottelalert, true, "update");
		}
		//Hotpresspost
		$proname = show_from_members($uid);
		$sqlinsertintohotpress = "INSERT INTO bulletin( `id` , `mem_id` ,  `body` , `date` ,`bottle_id`,`auto_genrate_text` )
		                                 VALUES ( NULL , '" . $uid . "',  'has just earned the Tinsel Town Bottles. Red Carpet events, celebrity sightings etc.. Its going to be a fun time.', '" . time() . "','3','Y' )";
		execute_query($sqlinsertintohotpress, true, "insert");
	    }
	}
    }

    /* ENDTinsel Town */

    /* Miami */
    $sqlCity = "SELECT * FROM members WHERE mem_id = '$uid' AND city NOT IN('Miami','Miami Beach','Miami Gardens','Miami Lakes','Miami Springs')";
    $resultCity = execute_query($sqlCity, true, "select");

    $statusMiami = 0;

    if (is_array($resultCity) && !empty($resultCity) && $resultCity['count'] > 0) {

	$sqlAmb = "SELECT city FROM members  WHERE mem_id = '$venue_id' AND city IN('Miami','Miami Beach','Miami Gardens','Miami Lakes','Miami Springs')";
	$resultAmb = execute_query($sqlAmb, true, "select");
	if (is_array($resultAmb) && !empty($resultAmb) && $resultAmb['count'] > 0)
	    $statusMiami = 1;
	if ($statusMiami == 1) {
	    $sqlBottelalert = "SELECT * from bottel_alert WHERE mail_sending='Y' AND mem_id='" . $uid . "' AND bottel_type='Miami'";
	    $resultBottelalert = execute_query($sqlBottelalert, true, "select");

	    if (empty($resultBottelalert)) {

		$sqlinsertbottelalert = "INSERT INTO  bottel_alert(`bottel_type`,`badge_type_id` ,`app_id`,`mem_id`, `alert_text`,`createdate`,`venue_id`,`date_alert`)
		  Value('Miami','4','".$announce_id."','" . $uid . "','Congrats! You have just earned the Miami Beach bottle.','".date('Y-m-d H:i:s',time())."','$venue_id','$time')";
		$resultinsertbottel = execute_query($sqlinsertbottelalert, true, "insert");
		$lastinsertid = $resultinsertbottel['last_id'];
		if ($lastinsertid) {
		    //push_notification_for_badges('Congrats! You have just earned the Miami Beach bottle.', $uid, $uid);
		    $appNotfn[]='Congrats! You have just earned the Miami Beach bottle.';
		    
		}
		$params[0] = show_from_members($uid);
		$sitename = "SocialNightlife.com";
		$sitemail = "info@socialNightlife.com";
		$params[3] = "You have just earned the Miami Beach bottle.";
		$mes = "Hello, <br />Congrats! You are in beautiful Miami. South Beach is where all the action is. Be spontaneous or follow your itinerary. Either way, its going to rock!  <a href='$siteurl/index.php?pg=profile&usr=" . $uid . "&action=viewbottel&sid=" . session_id() . "' > Click here</a> to see the full list of bottles<br />";
		$mes .='"Elevating The Social Nightlife Experience!"';
		$params[4] = $mes . "<br/>";
		
		if(mailbox($uid, '', $params) == 1){
			$updatebottelalert = "Update bottel_alert SET  mail_sending='Y' WHERE id=$lastinsertid";
			execute_query($updatebottelalert, true, "update");
		}
		//Hotpresspost
		$proname = show_from_members($uid);
		$sqlinsertintohotpress = "INSERT INTO bulletin( `id` , `mem_id` ,  `body` , `date`,`bottle_id`,`auto_genrate_text` )
		     VALUES ( NULL , '" . $uid . "','has just earned the Miami Beach Bottles. Look out for some amazing photos and a new tan.', '" . time() . "' ,'4', 'Y')";
		execute_query($sqlinsertintohotpress, true, "insert");
	    }
	}
    }

    /* ENDMiami */

    /* New York */
    $sqlCity = "SELECT * FROM members WHERE mem_id = '" . $uid . "' AND ((city != 'NewYork') OR (city != 'New York'))";
    $resultCity = execute_query($sqlCity, true, "select");
    if (is_array($resultCity) && !empty($resultCity) && $resultCity['count'] > 0) {
	$cityNewYork = $resultCity[0]['city'];
    }

    $statusNewYork = 0;
    $sql = "SELECT DISTINCT(venue_id) FROM announce_arrival WHERE user_id = '" . $uid . "'";
    $result = execute_query($sql, true, "select");
    if (is_array($result) && !empty($result) && $result['count'] > 0) {
	foreach ($result AS $kk => $getVenues) {
	    $sqlAmb = "SELECT city FROM members  WHERE mem_id = '" . $getVenues['venue_id'] . "' AND ((city = 'NewYork') OR (city = 'New York'))";
	    $resultAmb = execute_query($sqlAmb, true, "select");
	    if (is_array($resultAmb) && !empty($resultAmb) && $resultAmb['count'] > 0) {
		$statusNewYork = 1;
	    }
	}

	if ($statusNewYork == 1) {
	    $sqlBottelalert = "SELECT * from bottel_alert WHERE mail_sending='Y'  AND mem_id='" . $uid . "' AND bottel_type='New York'";
	    $resultBottelalert = execute_query($sqlBottelalert, true, "select");
	    if (empty($resultBottelalert)) {

		$sqlinsertbottelalert = "INSERT INTO  bottel_alert(`bottel_type`,`badge_type_id` ,`app_id`,`mem_id`, `alert_text`,`createdate`,`venue_id`,`date_alert`)
		  Value('New York','5','".$announce_id."','" . $uid . "','Congrats! You have just earned the NYC bottle','".date('Y-m-d H:i:s',time())."','$venue_id','$time')";
		$resultinsertbottel = execute_query($sqlinsertbottelalert, true, "insert");
		$lastinsertid = $resultinsertbottel['last_id'];
		if ($lastinsertid) {
		    //push_notification_for_badges('Congrats! You have just earned the NYC bottle', $uid, $uid);
		    $appNotfn[]='Congrats! You have just earned the NYC bottle';
		    
		}
		$params[0] = show_from_members($uid);
		$sitename = "SocialNightlife.com";
		$sitemail = "info@socialNightlife.com";
		$params[3] = "You have just earned the NYC bottle";
		$mes = "Hello, <br />Broadway, Wall St, statue liberty and so much more, but more important, the hot nightlife in NYC awaits you. <a href='$siteurl/index.php?pg=profile&usr=" . $uid . "&action=viewbottel&sid=" . session_id() . "' > Click here</a> to see the full list of bottles<br />";
		$mes .='"Elevating The Social Nightlife Experience!"';
		$params[4] = $mes . "<br/>";
		if(mailbox($uid, '', $params) == 1){
			$updatebottelalert = "Update bottel_alert SET  mail_sending='Y' WHERE id=" . $lastinsertid;
			execute_query($updatebottelalert, true, "update");
		}
		//Hotpresspost
		$proname = show_from_members($uid);
		$sqlinsertintohotpress = "INSERT INTO bulletin( `id` , `mem_id` ,  `body` , `date` ,`bottle_id`,`auto_genrate_text` )
		    VALUES ( NULL , '" . $uid . "',  'has just earned the NYC Bottles. They could soon be hanging with the It crowd.', '" . time() . "','5','Y' )";
		execute_query($sqlinsertintohotpress, true, "insert");
	    }
	}
    }
    /* ENDNew York */

//    $statusLasVegas = 1;
//    $statusLA = 1;
//    $statusMiami = 1;
//    $statusNewYork = 1;
    /* IN EACH CITY Socialite */
    if ($statusLasVegas == 1 && $statusLA == 1 && $statusMiami == 1 && $statusNewYork == 1) {
	$inEachCity = 1;

	$sqlBottelalert = "SELECT * from bottel_alert WHERE mail_sending='Y'  AND mem_id='" . $uid . "' AND bottel_type='Socialite'";
	$resultBottelalert = execute_query($sqlBottelalert, TRUE, "select");
	if (empty($resultBottelalert)) {

	    $sqlinsertbottelalert = "INSERT INTO  bottel_alert(`bottel_type`,`badge_type_id` ,`app_id`,`mem_id`, `alert_text`,`createdate`,`venue_id`,`date_alert`)
		  Value('Socialite','7','".$announce_id."','" . $uid . "','Congrats! You have just earned the Socialite badge','".date('Y-m-d H:i:s',time())."','$venue_id','$time')";
	    $resultinsertbottel = execute_query($sqlinsertbottelalert, true, "insert");
	    $lastinsertid = $resultinsertbottel['last_id'];
	    if ($lastinsertid) {
			//push_notification_for_badges('Congrats! You have just earned the Socialite badge', $uid, $uid);
			$appNotfn[]='Congrats! You have just earned the Socialite badge';
			
	    }
	    $params[0] = show_from_members($uid);
	    $sitename = "SocialNightlife.com";
	    $sitemail = "info@socialNightlife.com";
	    $params[3] = " You have just earned the Socialite bottle.";
	    $mes = "Hello, <br />Going to hot spots around the country with the It crowd has it benefits. Continue your ways and who knows what exciting things might happen. <a href='$siteurl/index.php?pg=profile&usr=" . $uid . "&action=viewbottel&sid=" . session_id() . "' > Click here</a> to see the full list of bottles<br />";
	    $mes .='"Elevating The Social Nightlife Experience!"';
	    $params[4] = $mes . "<br/>";
	    if(mailbox($uid, '', $params) == 1){
			$updatebottelalert = "Update bottel_alert SET  mail_sending='Y' WHERE id=" . $lastinsertid;
			execute_query($updatebottelalert, true, "update");
		}
	    //Hotpresspost
	    $proname = show_from_members($uid);
	    $sqlinsertintohotpress = "INSERT INTO bulletin( `id` , `mem_id` ,  `body` , `date` ,`bottle_id`,`auto_genrate_text` )
		    VALUES ( NULL , '" . $uid . "',  'has just earned the Socialite Bottles. They are part of the It crowd now. They might already have an agent.', '" . time() . "','7','Y' )";
	    execute_query($sqlinsertintohotpress, true, "insert");
	}
    } else {
	$inEachCity = 0;
    }
    /* END IN EACH CITY */

    /* LAST 90 DAY 3 APP */
    $toDate = date('Y-m-d');
    $date = strtotime(date("Y-m-d", strtotime($toDate)) . " -90 days");
    $fromDate = date('Y-m-d', $date);
//19_01    $sqlAmb = "select count(user_id) as cnt,user_id,date from announce_arrival WHERE user_id IN (SELECT distinct a.user_id FROM announce_arrival AS a)  AND user_id = '$uid' AND date Between '$fromDate'  AND '$toDate' GROUP BY user_id ORDER BY cnt DESC,date ASC LIMIT 0,1";
    $sqlAmb = "SELECT COUNT(user_id) AS cnt,user_id
FROM announce_arrival
WHERE user_id = '$uid' AND venue_id='$venue_id'
AND announce_arrival.date BETWEEN '$fromDate' AND '$toDate'
GROUP BY date";
    $resultLast90 = execute_query($sqlAmb, true, "select");
    if (is_array($resultLast90) && !empty($resultLast90) && $resultLast90['count'] > 0) {

	if ($resultLast90[0]['cnt'] >= 5) {
	    $last90 = 1;

	    /* check for previous ambassador */
	    $getPreAmbassadr = "SELECT * FROM bottel_alert WHERE venue_id='$venue_id' AND bottel_type='LAST_90_DAY_3_APP'";
	    $getPreAmbassadrList = execute_query($getPreAmbassadr, true, "select");

	    if (!empty($getPreAmbassadrList) && (is_array($getPreAmbassadrList))) {
		$sqlAmbforPreAmb = "SELECT COUNT(a.user_id) AS cnt,a.user_id,a.date,id FROM announce_arrival AS a,members AS b WHERE a.venue_id = '{$getPreAmbassadrList[0]['venue_id']}' AND a.user_id = '{$getPreAmbassadrList[0]['mem_id']}' AND a.user_id = b.mem_id AND (b.photo_b_thumb != 'no' && b.photo_b_thumb != '') AND (b.profile_type !='C' || b.profile_type ='C') AND a.date BETWEEN '$fromDate' AND '$toDate' GROUP BY a.user_id ORDER BY cnt DESC,a.DATE ASC LIMIT 0,1";
		$resultLast90forPreAmb = execute_query($sqlAmbforPreAmb, true, "select");

		/* update the table bottel_alert */
		if ($resultLast90[0]['cnt'] > $resultLast90forPreAmb[0]['cnt']) {
		    $sqlinsertbottelalert = "update bottel_alert set `mem_id`='$uid' where id='{$getPreAmbassadrList[0]['id']}'";
		    $resultinsertbottel = execute_query($sqlinsertbottelalert, true, "update");

		    if (isset($resultinsertbottel)) {
			//push_notification_for_badges('Congrats! You have just earned the Ambassador badge', $uid, $uid);
			$appNotfn[]='Congrats! You have just earned the Ambassador badge';

		    }

//for ousted ambassador
		    $params[0] = show_from_members($getPreAmbassadrList[0]['mem_id']);
		    $sitename = "SocialNightlife.com";
		    $sitemail = "info@socialNightlife.com";
		    $params[3] = "You have been ousted as Ambassador";
		    $mes = "Hi (" . getname($getPreAmbassadrList[0]['mem_id']) . "),<br>You have been ousted as Ambassador for (" . getname($venue_id) . "). To regain your ambassadorship is easy. Continue visiting one of your favorite spots.<a href='$siteurl/index.php?pg=profile&usr=" . $uid . "&action=viewbottel&sid=" . session_id() . "' > Click here</a> to login to your profile.<br />Thanks,<br />SocialNightlife.com team<br /><br />";
		    $mes .='"Elevating The Social Nightlife Experience!"';
		    $params[4] = $mes . "<br/>";
		    mailbox($uid, '', $params);
//for ambassador
		    $params[0] = show_from_members($uid);
		    $sitename = "SocialNightlife.com";
		    $sitemail = "info@socialNightlife.com";
		    $params[3] = "(" . getname($uid) . ") you are now the Ambassador for (" . getname($venue_id) . ")";
		    $mes = "Hi (" . getname($uid) . "),<br>You are now the Ambassador for (" . getname($venue_id) . ") and might be eligible for a reward.<br>Please visit (" . getname($venue_id) . ") profile to see if a reward is being offered and if it is still available by contacting them for details.<a href='$siteurl/index.php?pg=profile&usr=" . $uid . "&action=viewbottel&sid=" . session_id() . "' > Click here</a> to go to profile page.<br />Thanks,<br />SocialNightlife.com team<br /><br />";
		    $mes .='"Elevating The Social Nightlife Experience!"';
		    $params[4] = $mes . "<br/>";
		    mailbox($uid, '', $params);
//for nightsite venue
		    $params[0] = show_from_members($venue_id);
		    $sitename = "SocialNightlife.com";
		    $sitemail = "info@socialNightlife.com";
		    $params[3] = "(" . getname($uid) . ") is now your Ambassador";
		    $mes = "Hi (" . getname($venue_id) . "),<br>(" . getname($uid) . ")is now your Ambassador. Please contact (" . getname($uid) . ") to inform them if there is a reward available for this achievement.<a href='$siteurl/index.php?pg=profile&usr=" . $uid . "&action=viewbottel&sid=" . session_id() . "' > Click here</a> to go to profile page.<br />Thanks,<br />SocialNightlife.com team<br /><br />";
		    $mes .='"Elevating The Social Nightlife Experience!"';
		    $params[4] = $mes . "<br/>";
		    
			if(mailbox($uid, '', $params) == 1){
				$updatebottelalert = "Update bottel_alert SET  mail_sending='Y' WHERE id=" . $resultLast90forPreAmb[0]['id'];
				execute_query($updatebottelalert, true, "update");
			}
		    //Hotpresspost
		    $proname = show_from_members($uid);
		    $sqlinsertintohotpress = "INSERT INTO bulletin( `id` , `mem_id` ,  `body` , `date` ,`bottle_id`,`auto_genrate_text` )
		    VALUES ( NULL , '" . $uid . "',  'is now Ambassador for " . getname($venue_id) . ".', '" . time() . "','Y' ),( NULL , '" . $uid . "',  '" . $proname['profilenam'] . "  has ousted (" . getname($getPreAmbassadrList[0]['mem_id']) . ") and is now the Ambassador for (" . getname($venue_id) . ").', '" . time() . "','6','Y' )";
		    execute_query($sqlinsertintohotpress, true, "insert");
		} else {
		    $sqlBottelalert = "SELECT * from bottel_alert WHERE mail_sending='Y' AND mem_id=" . $uid . " AND bottel_type='LAST_90_DAY_3_APP'";
		    $resultBottelalert = execute_query($sqlBottelalert, true, "select");
		    if (empty($resultBottelalert)) {

			$sqlinsertbottelalert = "INSERT INTO  bottel_alert(`bottel_type`,`badge_type_id` ,`app_id`,`mem_id`,`alert_text`,`createdate`,`venue_id`,`date_alert`)
		         Value('LAST_90_DAY_3_APP','6','".$announce_id."','" . $uid . "','Congrats! You have just earned the Ambassador badge','".date('Y-m-d H:i:s',time())."','$venue_id','$time')";
			$resultinsertbottel = execute_query($sqlinsertbottelalert, true, "insert");
			$lastinsertid = $resultinsertbottel['last_id'];
			if ($lastinsertid) {
			    //push_notification_for_badges('Congrats! You have just earned the Ambassador badge', $uid, $uid);
			    $appNotfn[]='Congrats! You have just earned the Ambassador badge';
			    
			}
//for ambassador
			$params[0] = show_from_members($uid);
			$sitename = "SocialNightlife.com";
			$sitemail = "info@socialNightlife.com";
			$params[3] = "(" . getname($uid) . ") you are now the Ambassador for (" . getname($venue_id) . ")";
			$mes = "Hi (" . getname($uid) . "),<br>You are now the Ambassador for (" . getname($venue_id) . ") and might be eligible for a reward.<br>Please visit (" . getname($venue_id) . ") profile to see if a reward is being offered and if it is still available by contacting them for details.<a href='$siteurl/index.php?pg=profile&usr=" . $uid . "&action=viewbottel&sid=" . session_id() . "' > Click here</a> to go to profile page.<br />Thanks,<br />SocialNightlife.com team<br /><br />";
			$mes .='"Elevating The Social Nightlife Experience!"';
			$params[4] = $mes . "<br/>";

			mailbox($uid, '', $params);
			//
//for nightsite venue
			$params[0] = show_from_members($venue_id);
			$sitename = "SocialNightlife.com";
			$sitemail = "info@socialNightlife.com";
			$params[3] = "(" . getname($uid) . ") is now your Ambassador";
			$mes = "Hi (" . getname($venue_id) . "),<br>(" . getname($uid) . ")is now your Ambassador. Please contact (" . getname($uid) . ") to inform them if there is a reward available for this achievement.<a href='$siteurl/index.php?pg=profile&usr=" . $uid . "&action=viewbottel&sid=" . session_id() . "' > Click here</a> to go to profile page.<br />Thanks,<br />SocialNightlife.com team<br /><br />";
			$mes .='"Elevating The Social Nightlife Experience!"';
			$params[4] = $mes . "<br/>";

			if(mailbox($uid, '', $params) == 1){
				$updatebottelalert = "Update bottel_alert SET  mail_sending='Y' WHERE id=" . $lastinsertid;
				execute_query($updatebottelalert, true, "update");
			}
			//Hotpresspost
			$proname = show_from_members($uid);
			$sqlinsertintohotpress = "INSERT INTO bulletin( `id` , `mem_id` ,  `body` , `date` ,`bottle_id`,`auto_genrate_text` )
			VALUES ( NULL , '" . $uid . "',  'has just earned the Ambassador Bottles. Treat them well. They might be the key to getting into hot events', '" . time() . "','6','Y' )";
			execute_query($sqlinsertintohotpress, true, "insert");
		    }
		}
	    } else {
		$sqlBottelalert = "SELECT * from bottel_alert WHERE mail_sending='Y' AND mem_id=" . $uid . " AND venue_id=" . $venue_id . " AND bottel_type='LAST_90_DAY_3_APP'";
		$resultBottelalert = execute_query($sqlBottelalert, true, "select");
		if (empty($resultBottelalert)) {
		    $sqlinsertbottelalert = "INSERT INTO  bottel_alert(`bottel_type`,`badge_type_id` ,`app_id`,`mem_id`,`alert_text`,`createdate`,`venue_id`,`date_alert`)
		         Value('LAST_90_DAY_3_APP','6','".$announce_id."','" . $uid . "','Congrats! You have just earned the Ambassador badge','".date('Y-m-d H:i:s',time())."','$venue_id','$time')";
		    $resultinsertbottel = execute_query($sqlinsertbottelalert, true, "insert");
		    $lastinsertid = $resultinsertbottel['last_id'];
		    if ($lastinsertid) {
			//push_notification_for_badges('Congrats! You have just earned the Ambassador badge', $uid, $uid);
			$appNotfn[]='Congrats! You have just earned the Ambassador badge';

		    }
//for ambassador
		    $params[0] = show_from_members($uid);
		    $sitename = "SocialNightlife.com";
		    $sitemail = "info@socialNightlife.com";
		    $params[3] = "(" . getname($uid) . ") you are now the Ambassador for (" . getname($venue_id) . ")";
		    $mes = "Hi (" . getname($uid) . "),<br>You are now the Ambassador for (" . getname($venue_id) . ") and might be eligible for a reward.<br>Please visit (" . getname($venue_id) . ") profile to see if a reward is being offered and if it is still available by contacting them for details.<a href='$siteurl/index.php?pg=profile&usr=" . $uid . "&action=viewbottel&sid=" . session_id() . "' > Click here</a> to go to profile page.<br />Thanks,<br />SocialNightlife.com team<br /><br />";
		    $mes .='"Elevating The Social Nightlife Experience!"';
		    $params[4] = $mes . "<br/>";

		    mailbox($uid, '', $params);
//for nightsite venue
		    $params[0] = show_from_members($venue_id);
		    $sitename = "SocialNightlife.com";
		    $sitemail = "info@socialNightlife.com";
		    $params[3] = "(" . getname($uid) . ") is now your Ambassador";
		    $mes = "Hi (" . getname($venue_id) . "),<br>(" . getname($uid) . ")is now your Ambassador. Please contact (" . getname($uid) . ") to inform them if there is a reward available for this achievement.<a href='$siteurl/index.php?pg=profile&usr=" . $uid . "&action=viewbottel&sid=" . session_id() . "' > Click here</a> to go to profile page.<br />Thanks,<br />SocialNightlife.com team<br /><br />";
		    $mes .='"Elevating The Social Nightlife Experience!"';
		    $params[4] = $mes . "<br/>";

		    if(mailbox($uid, '', $params) == 1){
				$updatebottelalert = "Update bottel_alert SET  mail_sending='Y' WHERE id=" . $lastinsertid;
				execute_query($updatebottelalert, true, "update");
			}
		    //Hotpresspost
		    $proname = show_from_members($uid);
		    $sqlinsertintohotpress = "INSERT INTO bulletin( `id` , `mem_id` ,  `body` , `date` ,`bottle_id`,`auto_genrate_text` )
			VALUES ( NULL , '" . $uid . "',  'has just earned the Ambassador Bottles. Treat them well. They might be the key to getting into hot events', '" . time() . "','6','Y' )";
		    execute_query($sqlinsertintohotpress, true, "insert");
		}
	    }
	} else {
	    $last90 = 0;
	}
    } else {
	$last90 = 0;
    }
    /* LAST 90 DAY 3 APP */

    /* (Rock Star) 7 DAY 14 APP */
    $sqlAmb = "SELECT COUNT( DISTINCT venue_id ) AS cnt FROM announce_arrival WHERE user_id = '" . $uid . "' AND announce_arrival.date BETWEEN DATE_SUB(CURDATE(),INTERVAL 14 DAY) AND CURDATE() ORDER BY announce_arrival.date ASC";
    $status = "";
    $day14 = 0;
    $result7day = execute_query($sqlAmb, true, "select");
    if (is_array($result7day) && !empty($result7day) && $result7day['count'] > 0) {
	if ($result7day[0]['cnt'] >= 14) {

	    $day14 = 1;
	    $sqlBottelalert = "SELECT * from bottel_alert WHERE mail_sending='Y' AND mem_id=" . $uid . " AND venue_id=" . $venue_id . " AND bottel_type='rockstar'";
	    $resultBottelalert = execute_query($sqlBottelalert, true, "select");
	    if (empty($resultBottelalert)) {

		$sqlinsertbottelalert = "INSERT INTO  bottel_alert(`bottel_type`,`badge_type_id` ,`app_id`,`mem_id`,  `alert_text`,`createdate`,`venue_id`,`date_alert`)
						  Value('rockstar','8','".$announce_id."','" . $uid . "','Congrats! You have just earned the Rock Star badge','".date('Y-m-d H:i:s',time())."','$venue_id','$time')";
		$resultinsertbottel = execute_query($sqlinsertbottelalert, true, "insert");
		$lastinsertid = $resultinsertbottel['last_id'];
		if ($lastinsertid) {
		    //push_notification_for_badges('Congrats! You have just earned the Rock Star badge', $uid, $uid);
		    $appNotfn[]='Congrats! You have just earned the Rock Star badge';
		    
		}

		$params[0] = show_from_members($uid);
		$sitename = "SocialNightlife.com";
		$sitemail = "info@socialNightlife.com";
		$params[3] = "You have just earned the Rock Star bottle.";

		$mes = "Hello, <br />You have just earned the Rock Star badge.   Not sure how but you have pulled off partying like a rock star. Charlie Sheen can't even keep up with you. You should get some rest, take some vitamins, drink a protein shake and get ready to do it again <a href='$siteurl/index.php?pg=profile&usr=" . $uid . "&action=viewbottel&sid=" . session_id() . "' > Click here</a> to see the full list of bottles<br />";
		$mes .='"Elevating The Social Nightlife Experience!"';
		$params[4] = $mes . "<br/>";
		if(mailbox($uid, '', $params) == 1){
			$updatebottelalert = "Update bottel_alert SET  mail_sending='Y' WHERE id=" . $lastinsertid;
			execute_query($updatebottelalert, true, "update");
		}

		//Hotpresspost
		$proname = show_from_members($uid);
		$sqlinsertintohotpress = "INSERT INTO bulletin( `id` , `mem_id` ,  `body` , `date` ,`bottle_id`,`auto_genrate_text` )
					   VALUES ( NULL , '" . $uid . "',  'has just earned the Rock Star Bottles. This is no joke. They now deserve your respect!', '" . time() . "' ,'8','Y')";
		execute_query($sqlinsertintohotpress, true, "insert");
	    }
	} else {
	    $day14 = 0;
	}
    } else {
	$day14 = 0;
    }

    /* (Rock Star)END 7 DAY 14 APP */

    /* ENTOURAG 7 others */
//20_01    $sqlEntourage = "SELECT * FROM announce_arrival WHERE  user_id = '" . $uid . "' GROUP BY venue_id";
//    $sqlEntourage = "SELECT DISTINCT a.* FROM announce_arrival a INNER JOIN network n ON (n.mem_id = a.user_id) WHERE ('$uid' IN (n.frd_id, a.user_id)) AND a.venue_id='$venue_id' AND a.date=CURDATE() GROUP BY a.user_id ORDER BY a.date ASC";
    $sqlEntourage = "SELECT COUNT(*) AS cnt FROM tag_ent_list tel WHERE tel.announce_id='$announce_id'";
    $resultEntourage = execute_query($sqlEntourage, false, "select");
    if (is_array($resultEntourage) && !empty($resultEntourage) && $resultEntourage['cnt'] >= 7) {
	$entourage = 1;

	$sqlBottelalert = "SELECT * from bottel_alert WHERE mail_sending='Y'  AND mem_id='" . $uid . "' AND bottel_type='Entourage'";
	$resultBottelalert = execute_query($sqlBottelalert, true, "select");

	if (empty($resultBottelalert)) {

	    $sqlinsertbottelalert = "INSERT INTO  bottel_alert(`bottel_type`,`badge_type_id` ,`app_id`,`mem_id`,`alert_text`,`createdate`,`venue_id`,`date_alert`)
					Value('Entourage','9','".$announce_id."','" . $uid . "','Congrats! You have just earned the Entourage badge','".date('Y-m-d H:i:s',time())."','$venue_id','$time')";
	    $resultinsertbottel = execute_query($sqlinsertbottelalert, true, "insert");
	    $lastinsertid = $resultinsertbottel['last_id'];
	    if ($lastinsertid) {
			//push_notification_for_badges('Congrats! You have just earned the Entourage badge', $uid, $uid);
			$appNotfn[]='Congrats! You have just earned the Entourage badge';
			
	    }
	    $params[0] = show_from_members($uid);
	    $sitename = "SocialNightlife.com";
	    $sitemail = "info@socialNightlife.com";
	    $params[3] = "You have just earned the Entourage bottle.";

	    $mes = "Hello, <br />VIP's like you must be used to rolling deep like this. You probably have bottle service waiting. Enjoy! <a href='$siteurl/index.php?pg=profile&usr=" . $uid . "&action=viewbottel&sid=" . session_id() . "' > Click here</a> to see the full list of bottles<br />";
	    $mes .='"Elevating The Social Nightlife Experience!"';
	    $params[4] = $mes . "<br/>";
	    if(mailbox($uid, '', $params) == 1){
			$updatebottelalert = "Update bottel_alert SET  mail_sending='Y' WHERE id=" . $lastinsertid;
			execute_query($updatebottelalert, true, "update");
		}
	    //Hotpresspost
	    $proname = show_from_members($uid);
	    $sqlinsertintohotpress = "INSERT INTO bulletin( `id` , `mem_id` ,  `body` , `date`,`bottle_id`,`auto_genrate_text` )
					   VALUES ( NULL , '" . $uid . "',  'has just earned the Entourage Bottles. Rollin deep is normal for VIPs!', '" . time() . "','9','Y' )";
	    execute_query($sqlinsertintohotpress, true, "insert");
	}
    } else {
	$entourage = 0;
    }
    /* END START ENTOURAG 7 others */

    /* START PArty Animal Ambassodor 4 */
    $arrVenueId = array();
    $toDate = date('Y-m-d');
    $date = strtotime(date("Y-m-d", strtotime($toDate)) . " -90 days");
    $fromDate = date('Y-m-d', $date);

//    $sql = "SELECT DISTINCT(venue_id) FROM announce_arrival WHERE user_id = '" . $uid . "' ";
    $sql = "SELECT COUNT(id) as cnt FROM bottel_alert WHERE mem_id = '$uid' AND bottel_type LIKE '%LAST_90_DAY_3_APP%' AND venue_id !='0' GROUP BY mem_id";
    $result = execute_query($sql, false, "select");

    if (!empty($result) && $result['cnt'] >= 4) {

	$partyAnimal = 1;
	$sqlBottelalert = "SELECT * from bottel_alert WHERE mail_sending='Y'  AND mem_id='" . $uid . "' AND bottel_type='PartyAnimal'";
	$resultBottelalert = execute_query($sqlBottelalert, true, "select");
	if (empty($resultBottelalert)) {
	    $sqlinsertbottelalert = "INSERT INTO  bottel_alert(`bottel_type`,`badge_type_id` ,`app_id`,`mem_id`,  `alert_text`,`createdate`,`venue_id`,`date_alert`)
					Value('PartyAnimal','10','".$announce_id."','" . $uid . "','Congrats! You have just earned the Party Animal badge','".date('Y-m-d H:i:s',time())."','$venue_id','$time')";
	    $resultinsertbottel = execute_query($sqlinsertbottelalert, true, "insert");
	    $lastinsertid = $resultinsertbottel['last_id'];
	    if ($lastinsertid) {
			//push_notification_for_badges('Congrats! You have just earned the Party Animal badge', $uid, $uid);
			$appNotfn[]='Congrats! You have just earned the Party Animal badge';
			
	    }

	    $params[0] = show_from_members($uid);
	    $sitename = "SocialNightlife.com";
	    $sitemail = "info@socialNightlife.com";
	    $params[3] = "You have just earned the Party Animal bottle.";

	    $mes = "Hello, <br />Being an ambassador at multiple venues has its perks, only if you can keep up, but obviously you can. <a href='$siteurl/index.php?pg=profile&usr=" . $uid . "&action=viewbottel&sid=" . session_id() . "' > Click here</a> to see the full list of bottles<br />";
	    $mes .='"Elevating The Social Nightlife Experience!"';
	    $params[4] = $mes . "<br/>";
	    if(mailbox($uid, '', $params) == 1){
			$updatebottelalert = "Update bottel_alert SET  mail_sending='Y' WHERE id=" . $lastinsertid;
			execute_query($updatebottelalert, true, "update");
		}
	    //Hotpresspost
	    $proname = show_from_members($uid);
	    $sqlinsertintohotpress = "INSERT INTO bulletin( `id` , `mem_id` ,  `body` , `date` ,`bottle_id`,`auto_genrate_text` )
					   VALUES ( NULL , '" . $uid . "',  'has just earned the Party Animal Bottles. Can you keep up with them?', '" . time() . "' ,'10','Y')";
	    execute_query($sqlinsertintohotpress, true, "insert");
	}
    } else {
	$partyAnimal = 0;
    }
    /* END Party Animal Ambassodor 4 */

    /* START Party Crasher 4 appearances in 24 hours */
    $sqlAmb = "SELECT COUNT(*) as cnt FROM announce_arrival AS aa WHERE aa.user_id='$uid' AND aa.date=CURDATE() GROUP BY user_id ORDER BY aa.date ASC ";
    $result3 = execute_query($sqlAmb, true, "select");
    if (!empty($result3) && $result3[0]['cnt'] >= 4) {
	$day24 = 1;

	$sqlBottelalert = "SELECT * from bottel_alert WHERE mail_sending='Y' AND mem_id=" . $uid . " AND bottel_type='Party Crasher'";
	$resultBottelalert = execute_query($sqlBottelalert, true, "select");
	if (empty($resultBottelalert)) {

	    $sqlinsertbottelalert = "INSERT INTO  bottel_alert(`bottel_type` ,`badge_type_id`,`app_id`,`mem_id`,  `alert_text`,`createdate`,`venue_id`,`date_alert`)
								Value('Party Crasher','11','".$announce_id."','" . $uid . "','Congrats! You have just earned the Party Crasher badge','".date('Y-m-d H:i:s',time())."','$venue_id','$time')";
	    $resultinsertbottel = execute_query($sqlinsertbottelalert, true, "insert");
	    $lastinsertid = $resultinsertbottel['last_id'];
	    if ($lastinsertid) {
			//push_notification_for_badges('Congrats! You have just earned the Party Crasher badge', $uid, $uid);
			$appNotfn[]='Congrats! You have just earned the Party Crasher badge';
			
	    }
	    $params[0] = show_from_members($uid);
	    $sitename = "SocialNightlife.com";
	    $sitemail = "info@socialNightlife.com";
	    $params[3] = "You have just earned the Party Crasher bottle.";

	    $mes = "Hello, <br /> How were you able to hit up so many parties in one day? Somehow you did crash at least 4 making you a party crasher. <a href='$siteurl/index.php?pg=profile&usr=" . $uid . "&action=viewbottel&sid=" . session_id() . "' > Click here</a> to see the full list of bottles<br />";
	    $mes .='"Elevating The Social Nightlife Experience!"';
	    $params[4] = $mes . "<br/>";
	    if(mailbox($uid, '', $params) == 1){
			$updatebottelalert = "Update bottel_alert SET  mail_sending='Y' WHERE id=" . $lastinsertid;
			execute_query($updatebottelalert, true, "update");
		}
	    //Hotpresspost
	    $proname = show_from_members($uid);
	    $sqlinsertintohotpress = "INSERT INTO bulletin( `id` , `mem_id` ,  `body` , `date` ,`bottle_id`,`auto_genrate_text` )
							   VALUES ( NULL , '" . $uid . "',  'has just earned the Party Crasher Bottles. They obviously have no problem getting in', '" . time() . "','11','Y' )";
	    execute_query($sqlinsertintohotpress, true, "insert");
	}
    } else {
	$day24 = 0;
    }

    /* END Party Crasher  4 appearances in 24 hours */
    /* START Party Crasher 4 appearances in 24 hours 
      $sqlAmb = "select count(user_id) as cnt,user_id,date from announce_arrival WHERE user_id IN (SELECT distinct a.user_id FROM announce_arrival AS a)  AND user_id = '" . $uid . "'    GROUP BY user_id ORDER BY cnt DESC,date ASC LIMIT 0,1";
      $status = "";
      $day24 = 0;
      $result24Hrs = execute_query($sqlAmb, true, "select");
      if (is_array($result24Hrs) && !empty($result24Hrs) && $result24Hrs['count'] > 0) {

      if ($result24Hrs[0]['cnt'] >= 3) {
      $arrFrmTodate = array();
      $arrFrmHrs = array();
      $sql1 = "(SELECT * FROM announce_arrival WHERE user_id = '" . $uid . "' ORDER BY date ASC LIMIT 0,1) UNION (SELECT * FROM announce_arrival WHERE user_id = '" . $uid . "' ORDER BY date DESC LIMIT 0,1) ";
      $result1 = execute_query($sql1, true, "select");
      foreach ($result1 AS $kk => $rs1) {
      if (is_array($rs1) && !empty($rs1)) {
      array_push($arrFrmTodate, $rs1['date']);
      array_push($arrFrmHrs, $rs1['time']);
      }
      }

      $startDate = $arrFrmTodate[0];
      $startHrs = $arrFrmHrs[0];

      $sql2 = "SELECT * FROM announce_arrival WHERE user_id = '" . $uid . "' AND date Between '" . $arrFrmTodate[0] . "'  AND '" . $arrFrmTodate[1] . "' ORDER BY date ASC";
      $result2 = execute_query($sql2, true, "select");

      if (is_array($result2) && !empty($result2) && $result2['count'] > 0) {
      foreach ($result2 AS $kk => $rs2) {
      $startDate = $rs2['date'];
      $next1days = date("Y-m-d", strtotime(date("Y-m-d", strtotime($startDate)) . " +1 day"));
      if ($next1days <= $arrFrmTodate[1] && $next1days <= $arrFrmHrs[1]) {
      $sql3 = "select count(user_id) as cnt,user_id,date from announce_arrival WHERE user_id IN (SELECT distinct a.user_id FROM announce_arrival AS a)  AND user_id = '" . $uid . "'  AND date Between '" . $startDate . "'  AND '" . $next1days . "'  GROUP BY user_id ORDER BY cnt desc,date ASC LIMIT 0,1";
      $result3 = execute_query($sql3, true, "select");
      if (is_array($result3) && !empty($result3) && $result3['count'] > 0) {

      if ($result3[0]['cnt'] >= 4) {
      $day24 = 1;

      $sqlBottelalert = "SELECT * from bottel_alert WHERE mail_sending='Y' AND mem_id=" . $uid . " AND bottel_type='Party Crasher'";
      $resultBottelalert = execute_query($sqlBottelalert);
      if (is_array($resultBottelalert) && !empty($resultBottelalert) && $resultBottelalert['count'] > 0) {

      } else {
      $sqlinsertbottelalert = "INSERT INTO  bottel_alert(`bottel_type` ,`badge_type_id`,`mem_id`,  `alert_text`,`createdate`,`venue_id`)
      Value('Party Crasher','11','" . $uid . "','Congrats! You have just earned the Party Crasher badge',now(),'$venue_id')";
      $resultinsertbottel = execute_query($sqlinsertbottelalert);
      $lastinsertid = $resultinsertbottel['last_id'];
      if ($lastinsertid) {
      //push_notification_for_badges('Congrats! You have just earned the Party Crasher badge', $uid, $uid);
      }
      $params[0] = show_from_members($uid);
      $sitename = "SocialNightlife.com";
      $sitemail = "info@socialNightlife.com";
      $params[3] = "You have just earned the Party Crasher bottle.";

      $mes = "Hello, <br /> How were you able to hit up so many parties in one day? Somehow you did crash at least 4 making you a party crasher. <a href='$siteurl/index.php?pg=profile&usr=" . $uid . "&action=viewbottel&sid=" . session_id() . "' > Click here</a> to see the full list of bottles<br />";
      $mes .='"Elevating The Social Nightlife Experience!"';
      $params[4] = $mes . "<br/>";
      mailbox($uid, '', $params);

      $updatebottelalert = "Update bottel_alert SET  mail_sending='Y' WHERE id=" . $lastinsertid;
      execute_query($updatebottelalert, true, "update");

      //Hotpresspost
      $proname['profilenam'] = show_from_members($uid);
      $sqlinsertintohotpress = "INSERT INTO bulletin( `id` , `mem_id` ,  `body` , `date` ,`auto_genrate_text` )
      VALUES ( NULL , '" . $uid . "',  '" . $proname['profilenam'] . " has just earned the Party Crasher Bottles. They obviously have no problem getting in', '" . $time . "','Y' )";
      execute_query($sqlinsertintohotpress, true, "insert");
      }
      break;
      }
      }
      }
      }
      }
      } else {
      $day24 = 0;
      }
      } else {
      $day24 = 0;
      }

      END Party Crasher  4 appearances in 24 hours

      START Jetsetter Appearances in venues in 3 different countries */
    $statusLasVegas = 0;
    $sql = "SELECT count(a.venue_id) as cnt ,M.country FROM announce_arrival as a,members as M WHERE a.user_id ='" . $uid . "' AND a.venue_id = M.mem_id AND M.country !='' GROUP By M.country";
    $result = execute_query($sql, true, "select");
    if (is_array($result) && !empty($result) && $result['count'] >= 3) {
	$jetsetter = 1;

	$sqlBottelalert = "SELECT * from bottel_alert WHERE mail_sending='Y' AND mem_id=" . $uid . " AND bottel_type='Jetsetter'";
	$resultBottelalert = execute_query($sqlBottelalert, true, "select");
	if (empty($resultBottelalert)) {

	    $sqlinsertbottelalert = "INSERT INTO  bottel_alert(`bottel_type` ,`badge_type_id`,`app_id`,`mem_id`, `alert_text`,`createdate`,`venue_id`,`date_alert`)
							Value('Jetsetter','12','".$announce_id."','" . $uid . "','Congrats! You have just earned the Jetsetter badge','".date('Y-m-d H:i:s',time())."','$venue_id','$time')";
	    $resultinsertbottel = execute_query($sqlinsertbottelalert, true, "insert");
	    $lastinsertid = $resultinsertbottel['last_id'];
	    if ($lastinsertid) {
			//push_notification_for_badges('Congrats! You have just earned the Jetsetter badge', $uid, $uid);
			$appNotfn[]='Congrats! You have just earned the Jetsetter badge';
			
	    }
	    $params[0] = show_from_members($uid);
	    $sitename = "SocialNightlife.com";
	    $sitemail = "info@socialNightlife.com";
	    $params[3] = "You have just earned the Jetsetter bottle.";

	    $mes = "Hello, <br /> You are part of an elite crowd. Multiple hot spots around the globe makes you a jetsetter. <a href='$siteurl/index.php?pg=profile&usr=" . $uid . "&action=viewbottel&sid=" . session_id() . "' > Click here</a> to see the full list of bottles<br />";
	    $mes .='"Elevating The Social Nightlife Experience!"';
	    $params[4] = $mes . "<br/>";
	    if(mailbox($uid, '', $params) == 1){
			$updatebottelalert = "Update bottel_alert SET  mail_sending='Y' WHERE id=" . $lastinsertid;
			execute_query($updatebottelalert, true, "update");
		}

	    //Hotpresspost
	    $proname = show_from_members($uid);
	    $sqlinsertintohotpress = "INSERT INTO bulletin( `id` , `mem_id` ,  `body` , `date` ,`bottle_id`,`auto_genrate_text` )
						   VALUES ( NULL , '" . $uid . "',  'has just earned the Jetsetter Bottles. This global party hopper is on another level.', '" . time() . "','12', 'Y' )";
	    execute_query($sqlinsertintohotpress, true, "insert");
	}
    } else {
	$jetsetter = 0;
    }
 
    /* END Jetsetter Appearances in venues in 3 different countries */
    return $appNotfn;
}

function mailbox($uid, $opt, $params) {

    $to = $params[0]['email'];
    $name = "Mysocialnightlife";
    $from = 'noreply@socialnightlife.com';

    $subject = $params[3];
    $body = $params[4];
    $usr_name = getname($uid);
    $prophoto = show_from_members($uid);
    $prophoto = isset($prophoto['is_facebook_user']) && (strlen($prophoto['photo_b_thumb']) > 7) && ($prophoto['is_facebook_user'] == 'y' || $prophoto['is_facebook_user'] == 'Y') ? $prophoto['photo_b_thumb'] : ((isset($prophoto['photo_b_thumb']) && (strlen($prophoto['photo_b_thumb']) > 7)) ? $prophoto['photo_b_thumb'] : default_images($prophoto['gender'], $prophoto['profile_type']));
    $matter = email_template($usr_name, $subject, $body, $uid, $prophoto);
    if (firemail($to, $name, $subject, $matter))
	return 1;
    else
	return 0;
}

function show_from_members($id) {
    $sql = "select * from members where mem_id='$id'";
    $mem = execute_query($sql, false, "select");
    return $mem;
}

?>