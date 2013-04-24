<?php
	
	class BackStage {
		
		var $profile_url = PROFILE_IMAGE_SITEURL;
		var $local_folder = LOCAL_FOLDER;
		
		function validate_user($xmlrequest) {
			
			writelog("backstage.class.php :: event() :: ", "Starts Here ", false);
			
			$memId = mysql_real_escape_string($xmlrequest['BackStageEventList']['userId']);
			
			$event = array();
			$query_event = "SELECT COUNT(*)  FROM members WHERE mem_id ='$memId'";
			writelog("backstage.class.php :: validate_user() :: Query to get events : ", $query_event, false);
			
			$result = mysql_query($query_event);
			
			if (mysql_num_rows($result) > 0) {
				$row = mysql_fetch_array($result);
				if ($row['COUNT(*)'] > 0) {
					$event['successful'] = true;
					} else {
					$event['successful'] = false;
				}
			}
			
			writelog("Events:event:", $event, true);
			writelog("BackStage.class.php :: event() :: ", "End Here ", false);
			
			return $event;
		}
		
		function backstage_event_list($xmlrequest, $pagenumber, $limit) {
			
			$userId = mysql_real_escape_string($xmlrequest['BackStageEventList']['userId']);
			$userProfileType = mysql_real_escape_string($xmlrequest['BackStageEventList']['userProfileType']);
			if (DEBUG)
            writelog("backstage.class.php :: backstage_event_list() : ", "Start Here ", false);
			
			if ($pagenumber) {
				$lowerlimit = ($pagenumber - 1) * $limit;
				} else {
				$lowerlimit = 0;
			}
			
			$events = array();
			if (($userProfileType == 'C') || ($userProfileType == 'c')) {
				$query = "SELECT event_list.even_id as eventId,event_list.even_own as eventOwner,event_list.even_title as eventTitle,
				event_list.even_img as eventImageUrl,event_list.even_desc as eventDescription,
				event_cat.event_nam as eventCategory,event_list.even_org as eventOrganiser,
				event_list.actualdate as eventDate,event_list.actualtime as eventTime,event_list.even_loc,
				even_city,event_list.even_state,event_list.even_country,event_list.even_zip,
				event_list.even_phon,event_list.latitude as eventLatitude,event_list.longitude as eventLongitude FROM event_list,event_cat  WHERE event_cat.event_id=event_list.even_cat
				AND event_list.even_stat >=" . (time() - (2 * 24 * 60 * 60)) . "
				AND event_list.even_own='$userId'  order by event_date ASC LIMIT $lowerlimit,$limit"; // AND event_list.even_dt  >" . time(). " AND event_list.even_stat >" . (time() + (2 * 24 * 60 * 60)) . "
				if (DEBUG)
                writelog("backstage.class.php :: backstage_event_list() : ", "query" . $query, false);
				//AND (event_list.even_active = 'y' OR event_list.even_active = 'Y')
				$result = mysql_query($query);
				$events_json = '';
				$count = 0;
				if (mysql_num_rows($result) > 0) {
					while ($events = mysql_fetch_array($result, MYSQL_ASSOC)) {
						
						// $even_cat=$events['even_cat'];
						// $query = "SELECT event_nam as  FROM event_cat WHERE event_id='$even_cat'"; //
						// if (isset($events['count'])) {
						//   for ($i = 0; $i < $events['count']; $i++) {
						/*   if (is_readable($this->local_folder . $events['eventImageUrl'])) {
							$events['eventImageUrl'] = isset($events['eventImageUrl']) && (strlen($events['eventImageUrl']) > 7) ? event_image_detail($events['eventId'], $events['eventImageUrl'], 1) : NULL;
						} */
						
						if (is_readable($this->local_folder . $events['eventImageUrl'])) {
							$sizee = getimagesize($this->local_folder . $events['eventImageUrl']);
							$width_image_link = $sizee[0];
							$height_image_link = $sizee[1];
							
							$file_extension = substr($events['eventImageUrl'], strrpos($events['eventImageUrl'], '.') + 1);
							$arr = explode('.', $events['eventImageUrl']);
							$Id = isset($events['eventId']) && ($events['eventId']) ? $events['eventId'] : NULL;
							if ((!file_exists($this->local_folder . $arr[0] . "_" . $Id . "." . $file_extension)) && ($Id) && (preg_match('/^image\/(jp[e]?g|png|gif)$/', $sizee['mime']))) {
								thumbanail_for_image($Id, $events['eventImageUrl']);
							}
							if (preg_match('/^image\/(jp[e]?g|png|gif)$/', $sizee['mime'])) {
								$events['eventImageUrl'] = isset($events['eventImageUrl']) && (strlen($events['eventImageUrl']) > 7) ? event_image_detail($Id, $events['eventImageUrl'], 1) : NULL;
								
								list($width_image_link, $height_image_link, $type) = (isset($events['eventImageUrl']) && (strlen($events['eventImageUrl']) > 7)) ? getimagesize($this->local_folder . $events['eventImageUrl']) : NULL;
							}
						}
						
						$even_loc = (isset($events['even_loc']) && !empty($events['even_loc'])) ? (($events['even_loc']) . ',' ) : NULL;
						$even_city = (isset($events['even_city']) && !empty($events['even_city'])) ? (($events['even_city']) . ',') : NULL;
						$even_state = (isset($events['even_state']) && !empty($events['even_state'])) ? (($events['even_state']) . ',') : NULL;
						$even_country = (isset($events['even_country']) && !empty($events['even_country'])) ? (($events['even_country']) . ',' ) : NULL;
						$even_zip = (isset($events['even_zip']) && !empty($events['even_zip'])) ? (('Zip-' . $events['even_zip']) . ',' ) : NULL;
						$even_phon = (isset($events['even_phon']) && !empty($events['even_phon'])) ? (('contact no.-' . $events['even_phon']) . ',' ) : NULL;
						
						// $events['eventTime'] = date("F j, Y, g:i a", $events['eventTime']);
						//                    $events['eventDate'] = date("D, M j, Y", $events['eventDate']);
						//                    $events['eventDate'] = date("D, M j, Y", $events['eventDate']);
						//$str = $even_loc . $even_city . $even_state . $even_country . $even_zip . $even_phon;
						//$str = substr($str, 0, strlen($str) - 1);
						//$events['eventLocation'] = $str;
						//}
						// }
						
						$events['eventDescription'] = str_replace('\\', "", $events['eventDescription']);
						$events['eventDescription'] = str_replace(array("\r\n", "\r", "\n", "<br />", "<br/>"), "\\n", $events['eventDescription']);
						$events['eventDescription'] = strip_tags($events['eventDescription']);
						$events['eventDescription'] = str_replace(array("\""), "", $events['eventDescription']);
						
						
						$events['eventImageUrl'] = ((isset($events['eventImageUrl'])) && (strlen($events['eventImageUrl']) > 7)) ? 'http://www.socialnightlife.com/' . $events['eventImageUrl'] : 0;
						
						$events_json = $events_json . json_encode($events) . ",";
						$count++;
					}
					
					$events_json = substr($events_json, 0, strlen($events_json) - 1);
				}
				} else {
				$events_json = '{}';
			}
			$query = execute_query("SELECT event_list.even_id as eventId,event_list.even_own as eventOwner,event_list.even_title as eventTitle,event_list.even_img as eventImageUrl,event_list.even_desc as eventDescription,event_cat.event_nam as eventCategory,event_list.even_org as eventOrganiser,event_list.even_stat as eventDate,event_list.actualtime as eventTime,event_list.even_loc,even_city,event_list.even_state,event_list.even_country,event_list.even_zip,event_list.even_phon FROM event_list,event_cat  WHERE event_cat.event_id=event_list.even_cat AND event_list.even_own='$userId' AND (event_list.even_active = 'y' OR event_list.even_active = 'Y') order by event_date ASC", true, "select");
			$totalcount = isset($query['count']) ? $query['count'] : 0;
			$events['count'] = $count;
			$events['totalcount'] = $totalcount;
			$events['list'] = $events_json;
			
			return $events;
		}
		
		function bse_view_guest_list($xmlrequest, $pagenumber, $limit) {
			if (DEBUG)
            writelog("backstage.class.php :: bse_view_guest_list() : ", "Start Here", false);
			
			$userId = mysql_real_escape_string($xmlrequest['BSEViewGuestList']['userId']);
			$eventId = mysql_real_escape_string($xmlrequest['BSEViewGuestList']['eventId']);
			$time = isset($xmlrequest['BSEViewGuestList']['time']) && ($xmlrequest['BSEViewGuestList']['time']) ? mysql_real_escape_string($xmlrequest['BSEViewGuestList']['time']) : NULL;
			$device_time_zone = $xmlrequest['BSEViewGuestList']['timezoneName'];//'Asia/Kolkata';//$xmlrequest['BSEViewGuestList']['time'];
			$result = array();
			$lowerlimit = ($pagenumber) ? (($pagenumber - 1) * $limit) : 0;
			
			/* Old Query ::
				$query = "SELECT r.event_id, r.attend, r.no_of_guests, r.full_name, tbl_chkin.id AS checked_in_id, tbl_chkin.entourage AS actual_no_of_guests, tbl_chkin.chk_in_time AS actual_chk_in_time, tbl_chkin.chk_in_time AS actual_chk_in_time, tbl_chkin.profilenam AS actual_full_name, m.is_facebook_user,m.mem_id, m.profilenam,m.fname,m.lname, m.photo_thumb,m.gender,m.profile_type
				FROM rsvp AS r
				LEFT JOIN event_entourage_chkin AS tbl_chkin ON ( r.profileid = tbl_chkin.profileid
				AND r.event_id = tbl_chkin.event_id )
				INNER JOIN members AS m ON r.profileid = m.mem_id
				INNER JOIN event_list AS e ON r.event_id = e.even_id
				WHERE
				(e.even_active = 'y' OR e.even_active = 'Y')
				AND (e.guest = 'y' OR e.guest = 'Y')
				AND r.event_id =" . $eventId . "
				LIMIT $lowerlimit,$limit";
				echo $query = "SELECT  r.id,r.event_id, r.attend, sum(r.no_of_guests) as no_of_guests, r.full_name, tbl_chkin.id AS checked_in_id, tbl_chkin.entourage AS actual_no_of_guests, tbl_chkin.host as actualhost, tbl_chkin.host_type as actualhost_type,tbl_chkin.chk_in_time AS actual_chk_in_time, tbl_chkin.chk_in_time AS actual_chk_in_time, tbl_chkin.profilenam AS actual_full_name, m.is_facebook_user,m.mem_id, m.profilenam,m.fname,m.lname, m.photo_thumb,m.gender,m.profile_type
				FROM rsvp AS r
				LEFT JOIN event_entourage_chkin AS tbl_chkin ON ( r.id = tbl_chkin.rsvp_id
				AND r.event_id = tbl_chkin.event_id )
				INNER JOIN members AS m ON r.profileid = m.mem_id
				INNER JOIN event_list AS e ON r.event_id = e.even_id
				WHERE
				(e.even_active = 'y' OR e.even_active = 'Y')
				AND (e.guest = 'y' OR e.guest = 'Y')
				AND r.event_id =" . $eventId . "
				GROUP BY r. frnd_id
			ORDER BY r.full_name ASC LIMIT $lowerlimit,$limit"; */
			$query = "SELECT r.id,r.event_id, r.attend, r.no_of_guests AS no_of_guests,
			r.full_name, tbl_chkin.id AS checked_in_id, tbl_chkin.entourage AS actual_no_of_guests,
			tbl_chkin.host AS actualhost, tbl_chkin.host_type AS actualhost_type,
			tbl_chkin.chk_in_time AS actual_chk_in_time, tbl_chkin.chk_in_time AS actual_chk_in_time,
			tbl_chkin.profilenam AS actual_full_name, m.is_facebook_user,m.mem_id,
			m.profilenam,m.fname,m.lname, m.photo_thumb,m.gender,m.profile_type
			FROM rsvp AS r LEFT JOIN event_entourage_chkin AS tbl_chkin ON
			( r.id = tbl_chkin.rsvp_id AND r.event_id = tbl_chkin.event_id )
			INNER JOIN members AS m ON r.profileid = m.mem_id INNER JOIN
			event_list AS e ON r.event_id = e.even_id WHERE (e.even_active = 'y'
			OR e.even_active = 'Y') AND (e.guest = 'y' OR e.guest = 'Y') AND
			r.event_id ='" . $eventId . "' AND r.id IN(SELECT MAX(r.id) AS max_id FROM rsvp
			AS r LEFT JOIN event_entourage_chkin AS tbl_chkin ON
			( r.id = tbl_chkin.rsvp_id AND r.event_id = tbl_chkin.event_id )
			INNER JOIN members AS m ON r.profileid = m.mem_id INNER JOIN event_list AS e
			ON r.event_id = e.even_id WHERE (e.even_active = 'y' OR e.even_active = 'Y')
			AND (e.guest = 'y' OR e.guest = 'Y') AND r.event_id ='" . $eventId . "' GROUP BY r.profileid)
			ORDER BY r.id DESC";
			//LEFT JOIN event_nonmem_guestlist AS nm ON (r.event_id=nm.event_id),nm.name,nm.entourage,nm.email,nm.host,nm.host_type,
			/*  $query_non_member = "SELECT  nm.*
				FROM event_nonmem_guestlist AS nm
				LEFT JOIN event_entourage_chkin AS tbl_chkin ON ( nm.id = tbl_chkin.non_mem_id
				AND nm.event_id = tbl_chkin.event_id )
				INNER JOIN event_list AS e ON nm.event_id = e.even_id
				WHERE
				(e.even_active = 'y' OR e.even_active = 'Y')
				AND (e.guest = 'y' OR e.guest = 'Y')
				AND nm.event_id =" . $eventId . "
				LIMIT $lowerlimit,$limit";
				$result['nonMember'] = execute_query($query_non_member, true, "select");
				
				
				
				$query_tot_records = "SELECT COUNT(*) FROM rsvp AS r
				LEFT JOIN event_entourage_chkin AS tbl_chkin ON ( r.id = tbl_chkin.rsvp_id
				AND r.event_id = tbl_chkin.event_id )
				INNER JOIN members AS m ON r.profileid = m.mem_id
				INNER JOIN event_list AS e ON r.event_id = e.even_id
				WHERE
				(e.even_active = 'y' OR e.even_active = 'Y')
				AND (e.guest = 'y' OR e.guest = 'Y')
				AND r.event_id =" . $eventId;
				
			*/
			
			$query_tot_records = "SELECT  r.id FROM rsvp AS r
			LEFT JOIN event_entourage_chkin AS tbl_chkin ON ( r.id = tbl_chkin.rsvp_id
			AND r.event_id = tbl_chkin.event_id )
			INNER JOIN members AS m ON r.profileid = m.mem_id
			INNER JOIN event_list AS e ON r.event_id = e.even_id
			WHERE
			(e.even_active = 'y' OR e.even_active = 'Y')
			AND (e.guest = 'y' OR e.guest = 'Y')
			AND r.event_id =" . $eventId . "
			GROUP BY r. frnd_id ";
			
			if (DEBUG)
            writelog("backstage.class.php :: bse_view_guest_list() : ", "query=" . $query, false);
			if (DEBUG)
            writelog("backstage.class.php :: bse_view_guest_list() : ", "query=" . $query_tot_records, false);
			
			
			$result = execute_query($query, true, "select");
			
			$tot_records = execute_query($query_tot_records, true, "select");
			$result['tot_records'] = (isset($tot_records['count'])) ? $tot_records['count'] : 0;
			
			
			
			if (DEBUG)
            writelog("backstage.class.php :: bse_view_guest_list() : ", "End Here" . $result, false);
			if($this->active_event($eventId,$time,$device_time_zone))
			$result['isset_event'] = "yes";
			else
			$result['isset_event'] = "";
			
			$result['host_promoter_list'] = $this->get_host_promoter_list($userId);
			$result['non_member_host_promoter_list'] = $this->get_host_promoter_non_member_list($userId);
			// print_r($result);
			return $result;
		}
		
		function get_host_promoter_list($userId) {
			$host_sql = "select Distinct tm.id, tm.frd_id, tm.position,mem.is_facebook_user,mem.fname, mem.lname from team_members as tm, members as mem where
			tm.mem_id='$userId' AND tm.frd_id=mem.mem_id";
			
			$result = execute_query($host_sql, true, "select");
			$result['count'] = (isset($result['count'])) ? $result['count'] : 0;
			return $result;
		}
		
		function get_host_promoter_non_member_list($userId) {
			$non_member_host_sql = "select * from team_non_members  where addedby = '" . $userId . "' order by  name ASC";
			
			$result = execute_query($non_member_host_sql, true, "select");
			$result['count'] = (isset($result['count'])) ? $result['count'] : 0;
			return $result;
		}
		
		function bse_view_nonmember_guest_list($xmlrequest, $pagenumber, $limit) {
			if (DEBUG)
            writelog("backstage.class.php :: bse_view_guest_list() : ", "Start Here", false);
			
			$userId = mysql_real_escape_string($xmlrequest['BSEViewNonMemGuestList']['userId']);
			$eventId = mysql_real_escape_string($xmlrequest['BSEViewNonMemGuestList']['eventId']);
			$time = isset($xmlrequest['BSEViewNonMemGuestList']['time']) && ($xmlrequest['BSEViewNonMemGuestList']['time']) ? mysql_real_escape_string($xmlrequest['BSEViewNonMemGuestList']['time']) : NULL;
			$device_time_zone = $xmlrequest['BSEViewNonMemGuestList']['timezoneName'];//'Asia/Kolkata';//$xmlrequest['BSEViewNonMemGuestList']['time'];
			$result = array();
			$lowerlimit = ($pagenumber) ? (($pagenumber - 1) * $limit) : 0;
			
			$query = "SELECT  nongl.id,nongl.event_id,  nongl.name , nongl.entourage , nongl.email,nongl.host,nongl.host_email_address 	, nongl.host_type ,nongl.creationdate,tbl_chkin.id AS checked_in_id, tbl_chkin.entourage AS actual_no_of_guests, tbl_chkin.host as actualhost, tbl_chkin.host_type as actualhost_type,tbl_chkin.chk_in_date AS actual_chk_in_date, tbl_chkin.chk_in_time AS actual_chk_in_time
			
			FROM event_nonmem_guestlist AS nongl
			LEFT JOIN event_entourage_chkin AS tbl_chkin ON ( nongl.id = tbl_chkin.non_mem_id
			AND nongl.event_id = tbl_chkin.event_id )
			INNER JOIN event_list AS e ON nongl.event_id = e.even_id
			WHERE
			(e.even_active = 'y' OR e.even_active = 'Y')
			AND (e.guest = 'y' OR e.guest = 'Y')
			AND nongl.event_id =" . $eventId . "
			LIMIT $lowerlimit,$limit";  // ORDER BY  nongl.creationdate DESC
			
			$query_tot_records = "SELECT COUNT(*) FROM event_nonmem_guestlist AS nongl
			LEFT JOIN event_entourage_chkin AS tbl_chkin ON ( nongl.id = tbl_chkin.non_mem_id
			AND nongl.event_id = tbl_chkin.event_id )
			INNER JOIN event_list AS e ON nongl.event_id = e.even_id
			WHERE
			(e.even_active = 'y' OR e.even_active = 'Y')
			AND (e.guest = 'y' OR e.guest = 'Y')
			AND nongl.event_id =" . $eventId;
			
			if (DEBUG)
            writelog("backstage.class.php :: bse_view_nonmember_guest_list() : ", "query=" . $query, false);
			if (DEBUG)
            writelog("backstage.class.php :: bse_view_nonmember_guest_list() : ", "query=" . $query_tot_records, false);
			
			
			$result = execute_query($query, true, "select");
			
			$tot_records = execute_query($query_tot_records, false, "select");
			$result['tot_records'] = $tot_records['COUNT(*)'];
			
			
			if (DEBUG)
            writelog("backstage.class.php :: bse_view_nonmember_guest_list() : ", "End Here" . $result, false);
			//$result['isset_event'] = $this->active_event($eventId);
			if($this->active_event($eventId,$time,$device_time_zone))
			$result['isset_event'] = "yes";
			else
			$result['isset_event'] = "";
			$result['host_promoter_list'] = $this->get_host_promoter_list($userId);
			$result['non_member_host_promoter_list'] = $this->get_host_promoter_non_member_list($userId);
			
			return $result;
		}
		
		function bck_entourage_search($xmlrequest, $pagenumber, $limit) {
			if (DEBUG)
            writelog("backstage.class.php :: bck_entourage_search() : ", "Start Here", false);
			
			$userId = mysql_real_escape_string($xmlrequest['BSEGLEntourageSearch']['userId']);
			$eventId = mysql_real_escape_string($xmlrequest['BSEGLEntourageSearch']['eventId']);
			$searchKeyword = mysql_real_escape_string($xmlrequest['BSEGLEntourageSearch']['searchKeyword']);
			$time = isset($xmlrequest['BSEGLEntourageSearch']['time']) && ($xmlrequest['BSEGLEntourageSearch']['time']) ? mysql_real_escape_string($xmlrequest['BSEGLEntourageSearch']['time']) : NULL;
			$device_time_zone = $xmlrequest['BSEGLEntourageSearch']['timezoneName'];//'Asia/Kolkata';//$xmlrequest['BSEGLEntourageSearch']['time'];
			$result = array();
			$lowerlimit = ($pagenumber) ? (($pagenumber - 1) * $limit) : 0;
			$query = "SELECT r.id,r.event_id, r.attend, sum(r.no_of_guests) as no_of_guests, r.full_name, tbl_chkin.id AS checked_in_id, tbl_chkin.entourage AS actual_no_of_guests, tbl_chkin.host as actualhost, tbl_chkin.host_type as actualhost_type, tbl_chkin.chk_in_time AS actual_chk_in_time, tbl_chkin.chk_in_time AS actual_chk_in_time, tbl_chkin.profilenam AS actual_full_name, m.is_facebook_user, m.mem_id, m.profilenam, m.photo_thumb
			FROM rsvp AS r
			LEFT JOIN event_entourage_chkin AS tbl_chkin ON ( r.id = tbl_chkin.rsvp_id
			AND r.event_id = tbl_chkin.event_id )
			INNER JOIN members AS m ON r.profileid = m.mem_id
			INNER JOIN event_list AS e ON r.event_id = e.even_id
			WHERE
			(e.even_active = 'y' OR e.even_active = 'Y')
			AND (e.guest = 'y' OR e.guest = 'Y')
			AND r.event_id =" . $eventId . "
			AND r.full_name Like '" . $searchKeyword . "%' GROUP BY r. frnd_id  ORDER BY r.full_name ASC LIMIT $lowerlimit,$limit";
			
			$query_non_members = "SELECT
			nongl.id,nongl.id as mem_id,
			nongl.event_id,
			nongl.name as full_name,
			nongl.entourage,
			nongl.email,
			nongl.host,
			nongl.host_email_address,
			nongl.host_type,
			nongl.creationdate,
			tbl_chkin.id             AS checked_in_id,
			tbl_chkin.entourage      AS actual_no_of_guests,
			tbl_chkin.host           AS actualhost,
			tbl_chkin.host_type      AS actualhost_type,
			tbl_chkin.chk_in_date    AS actual_chk_in_date,
			tbl_chkin.chk_in_time    AS actual_chk_in_time
			FROM event_nonmem_guestlist AS nongl
			LEFT JOIN event_entourage_chkin AS tbl_chkin
			ON (nongl.id = tbl_chkin.non_mem_id
			AND nongl.event_id = tbl_chkin.event_id)
			INNER JOIN event_list AS e
			ON nongl.event_id = e.even_id
			WHERE (e.even_active = 'y'
			OR e.even_active = 'Y')
			AND (e.guest = 'y'
			OR e.guest = 'Y')
			AND nongl.event_id = '$eventId'
			AND nongl.name LIKE '$searchKeyword%'
			ORDER BY nongl.id desc";
			
			$result1 = execute_query($query_non_members, true, "select");
			if (DEBUG)
            writelog("backstage.class.php :: bck_entourage_search() : ", "Query=" . $query, false);
			
			/* $query_tot_records = "SELECT COUNT(*) FROM rsvp AS r
				LEFT JOIN event_entourage_chkin AS tbl_chkin ON ( r.id = tbl_chkin.rsvp_id
				AND r.event_id = tbl_chkin.event_id )
				INNER JOIN members AS m ON r.profileid = m.mem_id
				INNER JOIN event_list AS e ON r.event_id = e.even_id
				WHERE
				(e.even_active = 'y' OR e.even_active = 'Y')
				AND (e.guest = 'y' OR e.guest = 'Y')
				AND r.event_id =" . $eventId . "
			AND m.profilenam Like '" . $searchKeyword . "%'"; */
			
			
			$query_tot_records = "SELECT r.id  FROM rsvp AS r
			LEFT JOIN event_entourage_chkin AS tbl_chkin ON ( r.id = tbl_chkin.rsvp_id
			AND r.event_id = tbl_chkin.event_id )
			INNER JOIN members AS m ON r.profileid = m.mem_id
			INNER JOIN event_list AS e ON r.event_id = e.even_id
			WHERE
			(e.even_active = 'y' OR e.even_active = 'Y')
			AND (e.guest = 'y' OR e.guest = 'Y')
			AND r.event_id =" . $eventId . "
			AND r.full_name Like '" . $searchKeyword . "%' GROUP BY r. frnd_id";
			
			if (DEBUG)
            writelog("backstage.class.php :: bck_entourage_search() : ", "Query=" . $query_tot_records, false);
			
			$result = execute_query($query, true, "select");
			
			$result = array_merge($result, $result1);
			unset($result['count']);
			$result['count'] = count($result);
			$tot_records = execute_query($query_tot_records, true, "select");
			$result['tot_records'] = (isset($tot_records['count'])) ? $tot_records['count'] : 0;
			
			//$result['tot_records'] = $tot_records['COUNT(*)'];
			
			if (DEBUG)
            writelog("backstage.class.php :: bck_entourage_search() : ", "End Here" . $result, false);
			
			//$result['isset_event'] = $this->active_event($eventId);
			if($this->active_event($eventId,$time,$device_time_zone))
			$result['isset_event'] = "yes";
			else
			$result['isset_event'] = "";
			$result['host_promoter_list'] = $this->get_host_promoter_list($userId);
			$result['non_member_host_promoter_list'] = $this->get_host_promoter_non_member_list($userId);
			// print_r($result);
			return $result;
		}
		
		function bck_nonmem_entourage_search($xmlrequest, $pagenumber, $limit) {
			if (DEBUG)
            writelog("backstage.class.php :: bck_nonmem_entourage_search() : ", "Start Here", false);
			
			$userId = mysql_real_escape_string($xmlrequest['BSENonMemGLEntourageSearch']['userId']);
			$eventId = mysql_real_escape_string($xmlrequest['BSENonMemGLEntourageSearch']['eventId']);
			$searchKeyword = mysql_real_escape_string($xmlrequest['BSENonMemGLEntourageSearch']['searchKeyword']);
			$time = isset($xmlrequest['BSENonMemGLEntourageSearch']['time']) && ($xmlrequest['BSENonMemGLEntourageSearch']['time']) ? mysql_real_escape_string($xmlrequest['BSENonMemGLEntourageSearch']['time']) : NULL;
			$device_time_zone = $xmlrequest['BSENonMemGLEntourageSearch']['timezoneName'];//'Asia/Kolkata';//$xmlrequest['BSENonMemGLEntourageSearch']['time'];
			$result = array();
			$lowerlimit = ($pagenumber) ? (($pagenumber - 1) * $limit) : 0;
			
			
			$query = "SELECT  nongl.id,nongl.event_id,  nongl.name , nongl.entourage , nongl.email,nongl.host,nongl.host_email_address 	, nongl.host_type ,nongl.creationdate,tbl_chkin.id AS checked_in_id, tbl_chkin.entourage AS actual_no_of_guests, tbl_chkin.host as actualhost, tbl_chkin.host_type as actualhost_type,tbl_chkin.chk_in_date AS actual_chk_in_date, tbl_chkin.chk_in_time AS actual_chk_in_time
			
			FROM event_nonmem_guestlist AS nongl
			LEFT JOIN event_entourage_chkin AS tbl_chkin ON ( nongl.id = tbl_chkin.non_mem_id
			AND nongl.event_id = tbl_chkin.event_id )
			INNER JOIN event_list AS e ON nongl.event_id = e.even_id
			WHERE
			(e.even_active = 'y' OR e.even_active = 'Y')
			AND (e.guest = 'y' OR e.guest = 'Y')
			AND nongl.event_id =" . $eventId . " AND nongl.name Like '" . $searchKeyword . "%' ORDER BY  nongl.name ASC 
			LIMIT $lowerlimit,$limit";  // ORDER BY  nongl.creationdate DESC
			
			if (DEBUG)
            writelog("backstage.class.php :: bck_nonmem_entourage_search() : ", "Query=" . $query, false);
			
			$query_tot_records = "SELECT COUNT(*) FROM event_nonmem_guestlist AS nongl
			LEFT JOIN event_entourage_chkin AS tbl_chkin ON ( nongl.id = tbl_chkin.non_mem_id
			AND nongl.event_id = tbl_chkin.event_id )
			INNER JOIN event_list AS e ON nongl.event_id = e.even_id
			WHERE
			(e.even_active = 'y' OR e.even_active = 'Y')
			AND (e.guest = 'y' OR e.guest = 'Y')
			AND nongl.event_id =" . $eventId . " AND nongl.name Like '" . $searchKeyword . "%'";
			
			
			if (DEBUG)
            writelog("backstage.class.php :: bck_nonmem_entourage_search() : ", "Query=" . $query_tot_records, false);
			
			$result = execute_query($query, true, "select");
			
			$tot_records = execute_query($query_tot_records, false, "select");
			$result['tot_records'] = $tot_records['COUNT(*)'];
			if (DEBUG)
            writelog("backstage.class.php :: bck_nonmem_entourage_search() : ", "End Here" . $result, false);
			
			//$result['isset_event'] = $this->active_event($eventId);
			if($this->active_event($eventId,$time,$device_time_zone))
			$result['isset_event'] = "yes";
			else
			$result['isset_event'] = "";
			$result['host_promoter_list'] = $this->get_host_promoter_list($userId);
			$result['non_member_host_promoter_list'] = $this->get_host_promoter_non_member_list($userId);
			return $result;
		}
		
		function bsegl_check_in($xmlrequest) {
			
			if (DEBUG)
            writelog("backstage.class.php :: bsegl_check_in() : ", "Start Here", false);
			$rsvpId = ($xmlrequest['BSEGLCheckIn']['rsvpId']);
			$userId = ($xmlrequest['BSEGLCheckIn']['userId']);
			$userName = mysql_real_escape_string($xmlrequest['BSEGLCheckIn']['userName']);
			$eventId = ($xmlrequest['BSEGLCheckIn']['eventId']);
			$host = explode("-", $xmlrequest['BSEGLCheckIn']['host']);
			$time = isset($xmlrequest['BSEGLCheckIn']['time']) && ($xmlrequest['BSEGLCheckIn']['time']) ? mysql_real_escape_string($xmlrequest['BSEGLCheckIn']['time']) : NULL;
			$device_time_zone = isset($xmlrequest['BSEGLCheckIn']['timezoneName']) && ($xmlrequest['BSEGLCheckIn']['timezoneName']) ? mysql_real_escape_string($xmlrequest['BSEGLCheckIn']['timezoneName']) : NULL;//'Asia/Kolkata';
			$hostId = $host[0];
			$hostType = $host[1];
			
			$entourageCount = ($xmlrequest['BSEGLCheckIn']['entourageCount']);
			$error = array();
			
			// $userTimezone = new DateTimeZone('America/Chicago');
			// $myDateTime = new DateTime("$time");
			// $offset = $userTimezone->getOffset($myDateTime);
			// $chk_in_date = $myDateTime->format('U') + $offset;
			// $chk_in_date = date('H:i:s',$chk_in_date);
			//check for time saved in db
			$getRSVP = "select even_dt from event_list where even_id='$eventId'";
			$resultRSVP = execute_query($getRSVP, false, "select");
			//print_r(date('Y-m-d H:i:s', $resultRSVP['even_dt']));
			//set servers time zone
			/* $server_time_zone = date_default_timezone_get();
				$time_conerter = time_translate($device_time_zone, $server_time_zone, $time, $format = 'Y-m-d H:i:s');
				$chk_in_date = substr($time_conerter, 0, strpos($time_conerter, ' '));
				$chk_in_time = substr($time_conerter, strpos($time_conerter, ' '));
			$dateInUnixTimeStamp = execute_query("SELECT UNIX_TIMESTAMP('$time_conerter') as dateTime", false, "select"); */
			$eventInRange=$this->active_event($eventId ,$time,$device_time_zone);
			
			$chk_in_date = substr($eventInRange, 0, strpos($eventInRange, ' '));
			$chk_in_time = substr($eventInRange, strpos($eventInRange, ' '));
			if ($eventInRange !=false) {
				
				$query = "SELECT COUNT(*) FROM event_entourage_chkin WHERE rsvp_id ='$rsvpId'";
				
				if (DEBUG)
                writelog("backstage.class.php :: bsegl_check_in() : ", "Query=" . $query, false);
				
				$result = execute_query($query, false, "select");
				
				if ((isset($result['COUNT(*)'])) && (!$result['COUNT(*)'])) {
					// $query_check_in = "INSERT INTO event_entourage_chkin(event_id,entourage,host,	host_type,chk_in_time,profilenam,profileid)VALUE('$eventId','$entourageCount','$host','member',CURTIME(),'$userName','$userId')";
					//check value in non memners tanle
					$get_non_mem_id = "select COUNT(*) as cnt from event_nonmem_guestlist WHERE id='$rsvpId' AND name='$userName'";
					$get_result = execute_query($get_non_mem_id, false, "select");
					
					if ($get_result['cnt'] > 0)
                    $query_check_in = "INSERT INTO event_entourage_chkin(non_mem_id,event_id,entourage,host,host_type,chk_in_date,chk_in_time,profilenam,profileid) values($rsvpId,$eventId,$entourageCount,'$hostId','$hostType','$chk_in_date','$chk_in_time','$userName',$userId)";
					else
                    $query_check_in = "INSERT INTO event_entourage_chkin(rsvp_id,event_id,entourage,host,host_type,chk_in_date,chk_in_time,profilenam,profileid) values($rsvpId,$eventId,$entourageCount,'$hostId','$hostType','$chk_in_date','$chk_in_time','$userName',$userId)";
					
					$result = execute_query($query_check_in, true, "insert");
					
					if (DEBUG)
                    writelog("backstage.class.php :: bsegl_check_in() : ", "Query=" . $query_check_in, false);
				}
				else {
					$query_check_in = "UPDATE event_entourage_chkin SET entourage='$entourageCount',host='$hostId',chk_in_time='$chk_in_time',profilenam='$userName',profileid='$userId' WHERE rsvp_id='$rsvpId'";
					$result = execute_query($query_check_in, true, "update");
					if (DEBUG)
                    writelog("backstage.class.php :: bsegl_check_in() : ", "Query=" . $query_check_in, false);
				}
				
				$affected_row = $result['count'];
				$error = error_CRUD($xmlrequest, $affected_row);
				
				$query_rsvp_update = "UPDATE rsvp SET no_of_guests='$entourageCount' WHERE id='$rsvpId'";
				$result_rsvp_update = execute_query($query_rsvp_update, true, "update");
				if (DEBUG)
                writelog("backstage.class.php :: bsegl_check_in() : ", "Query RSVP Update=" . $query_rsvp_update, false);
				
				
				if (DEBUG)
                writelog("backstage.class.php :: bsegl_check_in() : ", "End Here :" . $error, true);
				}else {
				$error['BSEGLCheckIn']['errorInDateOfEvent']['successful_fin'] = true;
			}
			return $error;
		}
		
		function bse_nonmember_gl_check_in($xmlrequest) {
			
			if (DEBUG)
            writelog("backstage.class.php :: bsegl_check_in() : ", "Start Here", false);
			$non_mem_gl_id = ($xmlrequest['BSENonMemGLCheckIn']['non_mem_gl_id']);
			$eventId = ($xmlrequest['BSENonMemGLCheckIn']['eventId']);
			
			$host = explode("-", $xmlrequest['BSENonMemGLCheckIn']['host']);
			$hostId = $host[0];
			$hostType = $host[1];
			
			$entourageCount = ($xmlrequest['BSENonMemGLCheckIn']['entourageCount']);
			$error = array();
			$chk_in_date = date("Y-m-d");
			
			$query = "SELECT COUNT(*) FROM event_entourage_chkin WHERE non_mem_id ='$non_mem_gl_id'";
			
			if (DEBUG)
            writelog("backstage.class.php :: bse_nonmember_gl_check_in() : ", "Query=" . $query, false);
			
			$result = execute_query($query, false, "select");
			
			if ((isset($result['COUNT(*)'])) && (!$result['COUNT(*)'])) {
				
				$query_check_in = "INSERT INTO event_entourage_chkin(non_mem_id,event_id,entourage,host,host_type,chk_in_date,chk_in_time,profilenam,profileid) values( $non_mem_gl_id,$eventId,$entourageCount,'$hostId','$hostType','$chk_in_date',CURTIME(),'',0)";
				$result = execute_query($query_check_in, true, "insert");
				
				if (DEBUG)
                writelog("backstage.class.php :: bsegl_check_in() : ", "Query=" . $query_check_in, false);
			}
			else {
				$query_check_in = "UPDATE event_entourage_chkin SET entourage='$entourageCount',host='$hostId',chk_in_time=CURTIME() WHERE non_mem_id='$non_mem_gl_id'";
				$result = execute_query($query_check_in, true, "update");
				if (DEBUG)
                writelog("backstage.class.php :: bsegl_check_in() : ", "Query=" . $query_check_in, false);
			}
			
			$affected_row = $result['count'];
			$error = error_CRUD($xmlrequest, $affected_row);
			
			if (DEBUG)
            writelog("backstage.class.php :: bsegl_check_in() : ", "End Here :" . $error, true);
			
			return $error;
		}
		
		function bse_view_tbl_reservation_list($xmlrequest, $pagenumber, $limit) {
			if (DEBUG)
            writelog("backstage.class.php :: bse_view_tbl_reservation_list() : ", "Start Here :", true);
			
			$userId = mysql_real_escape_string($xmlrequest['BSEViewTblReservationList']['userId']);
			$eventId = mysql_real_escape_string($xmlrequest['BSEViewTblReservationList']['eventId']);
			$time = isset($xmlrequest['BSEViewTblReservationList']['time']) && ($xmlrequest['BSEViewTblReservationList']['time']) ? mysql_real_escape_string($xmlrequest['BSEViewTblReservationList']['time']) : NULL;
			$device_time_zone = isset($xmlrequest['BSEViewTblReservationList']['timezoneName']) && ($xmlrequest['BSEViewTblReservationList']['timezoneName']) ? mysql_real_escape_string($xmlrequest['BSEViewTblReservationList']['timezoneName']) : NULL;//'Asia/Kolkata';
			$result = array();
			$lowerlimit = ($pagenumber) ? (($pagenumber - 1) * $limit) : 0;
			
			$query = "SELECT  tbl_reserv.id as tbl_reservation_id, tbl_reserv.table_no, tbl_reserv.vipguest_fname ,tbl_reserv.vipguest_lname ,tbl_reserv.total_in_group as expected_guest, tbl_reserv.event_id, tbl_reserv.addedby, tbl_chkin.entourage AS actualguest, tbl_chkin.event_id AS checkedin_event,  tbl_chkin.host as actualhost, tbl_chkin.host_type as actualhost_type, tbl_chkin.table_no as actualtable_no, ebt.	table_no as table_display_no, ebt.capacity
			FROM event_backstage_tbl_reservation AS tbl_reserv
			LEFT JOIN event_bs_tbl_chkin AS tbl_chkin ON tbl_reserv.id = tbl_chkin.res_id 
			INNER JOIN event_backstage_tables AS ebt ON tbl_reserv.table_no = ebt.id
			INNER JOIN event_list AS el  ON  tbl_reserv.event_id = el.even_id 
			WHERE tbl_reserv.event_id ='" . $eventId . "'
			AND (
			el.even_active = 'y'
			OR el.even_active = 'Y'
			) order by  tbl_reserv.vipguest_fname ASC,	tbl_reserv.vipguest_lname ASC LIMIT $lowerlimit,$limit";
			
			/*  Query with check in `event_backstage_tables` for event_id :: start here */
			/*     $query = "SELECT tbl_reserv.table_no, tbl_reserv.expected_guest, tbl_reserv.event_id, tbl_reserv.addedby, tbl_chkin.entourage AS actualguest, tbl_chkin.event_id AS checkedin_event, tbl_chkin.host, tbl_chkin.table_no as actualtable_no, ebt.event_id, ebt.capacity
				FROM event_backstage_tbl_reservation AS tbl_reserv
				LEFT JOIN event_bs_tbl_chkin AS tbl_chkin ON tbl_reserv.table_no = tbl_chkin.table_no
				AND tbl_reserv.event_id = tbl_chkin.event_id
				INNER JOIN event_backstage_tables AS ebt ON tbl_reserv.event_id = ebt.event_id
				AND tbl_reserv.table_no = ebt.table_no
				WHERE tbl_reserv.event_id ='" . $eventId . "'
				AND tbl_reserv.table_no
				IN (
				
				SELECT ebtr.table_no
				FROM event_list AS e, event_backstage_tables AS ebt, event_backstage_tbl_reservation AS ebtr
				WHERE e.even_id = ebt.event_id
				AND ebtr.event_id = ebt.event_id
				AND e.even_id = ebtr.event_id
				AND e.even_id ='" . $eventId . "'
				AND ebtr.table_no = ebt.table_no
				AND (
				e.even_active = 'y'
				OR e.even_active = 'Y'
				)
			) LIMIT $lowerlimit,$limit"; */
			/*  Query with check in `event_backstage_tables` for event_id :: end here */
			
			if (DEBUG)
            writelog("backstage.class.php :: bse_view_tbl_reservation_list() : ", "Query=" . $query, false);
			
			$result = execute_query($query, true, "select");
			$count = (isset($result['count'])) ? $result['count'] : 0;
			
			$query_total = "SELECT COUNT(*)FROM event_backstage_tbl_reservation AS tbl_reserv
			LEFT JOIN event_bs_tbl_chkin AS tbl_chkin ON tbl_reserv.id = tbl_chkin.res_id 
			INNER JOIN event_backstage_tables AS ebt ON tbl_reserv.table_no = ebt.id
			INNER JOIN event_list AS el  ON  tbl_reserv.event_id = el.even_id 
			WHERE tbl_reserv.event_id ='" . $eventId . "'
			AND (el.even_active = 'y' OR el.even_active = 'Y')";
			
			if (DEBUG)
            writelog("backstage.class.php :: bse_view_tbl_reservation_list() : ", "Query=" . $query_total, false);
			
			$tot_count = execute_query($query_total, false, "select");
			$result['tot_count'] = $tot_count['COUNT(*)'];
			if (DEBUG)
            writelog("backstage.class.php :: bse_view_tbl_reservation_list() : ", "End Here: " . $result, true);
			$result['isset_event'] = $this->active_event($eventId,$time,$device_time_zone);
			
			$result['host_promoter_list'] = $this->get_host_promoter_list($userId);
			$result['non_member_host_promoter_list'] = $this->get_host_promoter_non_member_list($userId);
			
			return $result;
		}
		
		function bsetr_check_in_notes($xmlrequest) {
			if (DEBUG)
            writelog("backstage.class.php :: bsetr_check_in_notes() : ", "Start Here: ", false);
			
			$userId = mysql_real_escape_string($xmlrequest['BSETRCheckInNotes']['userId']);
			$event_Id = mysql_real_escape_string($xmlrequest['BSETRCheckInNotes']['event_Id']);
			$table_no = mysql_real_escape_string($xmlrequest['BSETRCheckInNotes']['table_no']);
			
			//$lowerlimit = ($pagenumber) ? (($pagenumber - 1) * $limit) : 0;
			
			$query = "SELECT * FROM event_backstage_tables_notes WHERE eve_id = " . $event_Id . " AND table_id = " . $table_no . " "; //LIMIT $lowerlimit,$limit"
			if (DEBUG)
            writelog("backstage.class.php :: bsetr_check_in_notes() : ", "Query=" . $query, false);
			
			$result = execute_query($query, false, "select");
			
			if (DEBUG)
            writelog("backstage.class.php :: bsetr_check_in_notes() : ", "End Here: " . $result, false);
			
			return $result;
		}
		
		function bsetr_view_check_in($xmlrequest) {
			if (DEBUG)
            writelog("backstage.class.php :: bsetr_view_check_in() : ", "Start Here: ", false);
			
			$userId = mysql_real_escape_string($xmlrequest['BSETRViewCheckIn']['userId']);
			$eventId = mysql_real_escape_string($xmlrequest['BSETRViewCheckIn']['eventId']);
			$tblReservationId = mysql_real_escape_string($xmlrequest['BSETRViewCheckIn']['tbl_reservation_id']);
			$table_no = mysql_real_escape_string($xmlrequest['BSETRViewCheckIn']['table_no']);
			// $recordId = mysql_real_escape_string($xmlrequest['BSETRViewCheckIn']['recordId']);
			
			
			$query = "SELECT tbl_reserv.id as table_record,tbl_reserv.vip_host as expectedhost, tbl_reserv.host_type as  expectedhost_type,tbl_reserv.vipguest_fname  as expected_vipguest_fname, tbl_reserv.vipguest_lname  as expected_vipguest_lname, tbl_reserv.table_no as expectedtable_no,
			tbl_reserv.bottle_min as expectedbottle_min, tbl_reserv.min_spend as expectedmin_spend,
			tbl_reserv.total_in_group 	as expected_guest, tbl_reserv.event_id, tbl_reserv.addedby,
			tbl_chkin.entourage AS actualguest, tbl_chkin.event_id AS checkedin_event, tbl_chkin.host as actualhost, tbl_chkin.host_type as actualhost_type , tbl_chkin.bottle_server as actualbottle_server,	tbl_chkin.bottle_server_type as actualbottle_server_type,tbl_chkin.table_no as actualtable_no,
			tbl_chkin.bottle_min as actualbottle_min, tbl_chkin.min_spend as actualmin_spend,tbl_chkin.vip_guest_name as actual_vip_guest_name, 
			ebt.event_id, ebt.capacity,ebt.	table_no as table_display_no
			FROM event_backstage_tbl_reservation AS tbl_reserv
			LEFT JOIN event_bs_tbl_chkin AS tbl_chkin ON tbl_reserv.id = tbl_chkin.res_id 
			
			INNER JOIN event_backstage_tables AS ebt ON tbl_reserv.table_no = ebt.id
			INNER JOIN event_list AS el  ON  tbl_reserv.event_id = el.even_id 
			WHERE tbl_reserv.id ='" . $tblReservationId . "'
			AND (
			el.even_active = 'y'
			OR el.even_active = 'Y'
			) ";
			
			if (DEBUG)
            writelog("backstage.class.php :: bsetr_view_check_in() : ", "Query= " . $query, false);
			
			$result = array();
			$result = execute_query($query, false, "select");
			$result['count'] = (isset($result['count'])) ? $result['count'] : 0;
			
			$result['host_promoter_list'] = $this->get_host_promoter_list($userId);
			$result['non_member_host_promoter_list'] = $this->get_host_promoter_non_member_list($userId);
			
			if (DEBUG)
            writelog("backstage.class.php :: bsetr_view_check_in() : ", "End Here " . $result, true);
			
			return $result;
		}
		
		function bsetr_confirm_message_screen($xmlrequest) {
			if (DEBUG)
            writelog("backstage.class.php :: bsetr_confirm_message_screen() : ", "Start Here ", false);
			
			
			$host = explode("-", $xmlrequest['BSEGLCheckIn']['host']);
			$hostId = $host[0];
			$hostType = $host[1];
			
			
			$tbl_reservation_id = mysql_real_escape_string($xmlrequest['BSETRConfirmMessageScreen']['tableListInfo']['tbl_reservation_id']);
			$userId = mysql_real_escape_string($xmlrequest['BSETRConfirmMessageScreen']['userId']);
			$eventId = mysql_real_escape_string($xmlrequest['BSETRConfirmMessageScreen']['eventId']);
			$table_no = mysql_real_escape_string($xmlrequest['BSETRConfirmMessageScreen']['tableListInfo']['table_no']);
			
			$entourageCount = mysql_real_escape_string($xmlrequest['BSETRConfirmMessageScreen']['tableListInfo']['entourageCount']);
			$date = mysql_real_escape_string($xmlrequest['BSETRConfirmMessageScreen']['time']);
			
			$host = explode("-", $xmlrequest['BSETRConfirmMessageScreen']['tableListInfo']['host']);
			$hostId = $host[0];
			$hostType = $host[1];
			
			$bottle_server = explode("-", $xmlrequest['BSETRConfirmMessageScreen']['tableListInfo']['bottle_server']);
			$bottle_serverId = $bottle_server[0];
			$bottle_serverType = $bottle_server[1];
			
			$bottle_minimum = mysql_real_escape_string($xmlrequest['BSETRConfirmMessageScreen']['tableListInfo']['bottle_minimum']);
			$minimum_spend = mysql_real_escape_string($xmlrequest['BSETRConfirmMessageScreen']['tableListInfo']['minimum_spend']);
			$vipguestname = mysql_real_escape_string($xmlrequest['BSETRConfirmMessageScreen']['tableListInfo']['vipguestname']);
			
			$error = array();
			
			$bottle_server_id = $bottle_server;
			// $time = date("Y-m-d H:i:s");
			// $userTimezone = new DateTimeZone('America/Chicago');
			// $myDateTime = new DateTime("$time");
			// $offset = $userTimezone->getOffset($myDateTime);
			// $chk_in_date = $myDateTime->format('U') + $offset;
			// $creationdate = date("Y-m-d H:i:s",$chk_in_date);
			$creationdate = $date;
			
			$query = "INSERT INTO event_bs_tbl_chkin(res_id,event_id,entourage,host,host_type,creationdate,bottle_server,	bottle_server_type, table_no,addedby,vip_guest_name,bottle_min,min_spend)VALUE('$tbl_reservation_id','$eventId','$entourageCount','$hostId','$hostType','$creationdate','$bottle_serverId','$bottle_serverType','$table_no','$userId','$vipguestname','$bottle_minimum','$minimum_spend')";
			
			
			if (DEBUG)
            writelog("backstage.class.php :: bsetr_confirm_message_screen() : ", "Query=" . $query, false);
			
			$result = execute_query($query, true, "insert");
			
			$affected_row = $result['count'];
			$error = error_CRUD($xmlrequest, $affected_row);
			
			$sql_rsvp = "UPDATE event_backstage_tbl_reservation SET total_in_group='$entourageCount' WHERE id =$tbl_reservation_id ";
			$sql_rsvp_result = execute_query($sql_rsvp, true, "update");
			
			if (DEBUG)
            writelog("backstage.class.php :: bsetr_confirm_message_screen() : ", "End Here:" . $error, true);
			
			return $error;
		}
		
		/* function active_event($eventId) {
			$query = "SELECT even_id, even_stat FROM event_list WHERE even_id='$eventId'";
			$result = execute_query($query, false, "select");
			
			$nextdate = mktime(0, 0, 0, date('m', $result['even_stat']), date('d', $result['even_stat']) + 1, date("Y", $result['even_stat']));
			if (date("Y-m-d") == date("Y-m-d", $result['even_stat']) || date("Y-m-d") == date("Y-m-d", $nextdate))
            return true;
			else
            return false;
		} */
		
		function active_event($eventId,$time,$device_time_zone) {
			$query = "SELECT even_id, even_stat FROM event_list WHERE even_id='$eventId'";
			$resultRSVP = execute_query($query, false, "select");
			
			$server_time_zone = date_default_timezone_get();
			$time_conerter = time_translate($device_time_zone, $server_time_zone, $time, $format = 'Y-m-d H:i:s');
			$chk_in_date = substr($time_conerter, 0, strpos($time_conerter, ' '));
			$chk_in_time = substr($time_conerter, strpos($time_conerter, ' '));
			$dateInUnixTimeStamp = execute_query("SELECT UNIX_TIMESTAMP('$time_conerter') as dateTime", false, "select");
			if ($chk_in_date == date('Y-m-d', $resultRSVP['even_stat']) && ($dateInUnixTimeStamp['dateTime'] < $resultRSVP['even_stat']))
			return $time_conerter;
			else
			return false;
		}
		
		
		function backStageEventList($response_message, $xmlrequest) {
			if (isset($response_message['BackStageEventList']['SuccessCode']) && ( $response_message['BackStageEventList']['SuccessCode'] == '000')) {
				$event_list = array();
				
				$pageNumber = $xmlrequest['BackStageEventList']['pageNumber'];
				$event_list = $this->backstage_event_list($xmlrequest, $pageNumber, 20);
				
				$userinfocode = $response_message['BackStageEventList']['SuccessCode'];
				$userinfodesc = $response_message['BackStageEventList']['SuccessDesc'];
				
				
				
				
				$response_str = response_repeat_string();
				
				$response_mess = '{
				' . $response_str . '
				"BackStageEventList":{
				"errorCode":"' . $userinfocode . '",
				"errorMsg":"' . $userinfodesc . '",
				"currentEventsCount":"' . $event_list['count'] . '",
				"totalEventsCount":"' . $event_list['totalcount'] . '",
				"BackstageEvents":[
				' . $event_list['list'] . '
				]
				}
				}
				';
				} else {
				$userinfocode = $response_message['BackStageEventList']['ErrorCode'];
				$userinfodesc = $response_message['BackStageEventList']['ErrorDesc'];
				$response_mess = get_response_string("BackStageEventList", $userinfocode, $userinfodesc);
			}
			writelog("Response:backStageEventList():", $response_mess, false);
			return getValidJSON($response_mess);
		}
		
		function bckSEViewGuestList($response_message, $xmlrequest) {
			global $return_codes;
			
			$pageNumber = $xmlrequest['BSEViewGuestList']['pageNumber'];
			$backstageUserId = $xmlrequest['BSEViewGuestList']['userId'];
			$GuestListing = array();
			
			$GuestListing = $this->bse_view_guest_list($xmlrequest, $pageNumber, 20);
			/* echo "<pre>";
				print_r($GuestListing);
			echo "</pre>"; */
			
			$GuestListingStr = "";
			$GuestListing['even_id'] = isset($GuestListing['even_id']) ? ($GuestListing['even_id']) : "";
			$GuestListing['count'] = isset($GuestListing['count']) ? ($GuestListing['count']) : "";
			$GuestListing['tot_records'] = isset($GuestListing['tot_records']) ? ($GuestListing['tot_records']) : "";
			
			if (isset($GuestListing['count']) && ($GuestListing['count'] > 0)) {
				
				for ($i = 0; $i < $GuestListing['count']; $i++) {
					
					$GuestListing[$i]['checked_in_id'] = (isset($GuestListing[$i]['checked_in_id']) && ($GuestListing[$i]['checked_in_id'] != NULL)) ? "Yes" : "No";
					$GuestListing[$i]['photo_thumb'] = ((isset($GuestListing[$i]['is_facebook_user'])) && (strlen($GuestListing[$i]['photo_thumb']) > 7) && ($GuestListing[$i]['is_facebook_user'] == 'y' || $GuestListing[$i]['is_facebook_user'] == 'Y')) ? $GuestListing[$i]['photo_thumb'] : ((isset($GuestListing[$i]['photo_thumb']) && (strlen($GuestListing[$i]['photo_thumb']) > 7)) ? $this->profile_url . $GuestListing[$i]['photo_thumb'] : $this->profile_url . default_images($GuestListing[$i]['gender'], $GuestListing[$i]['profile_type']));
					
					$url = $GuestListing[$i]['photo_thumb'];
					
					if ((isset($GuestListing[$i]['attend'])) && ($GuestListing[$i]['attend'] == 1)) {
						$status = 'Yes';
					}
					if ((isset($GuestListing[$i]['attend'])) && ($GuestListing[$i]['attend'] == 2)) {
						$status = 'May be';
					}
					if ((isset($GuestListing[$i]['attend'])) && ($GuestListing[$i]['attend'] == 0)) {
						$status = 'No';
					}
					
					//$status = ($GuestListing[$i]['checked_in_id'] == "Yes") ? "Yes" : $status;
					$entourageCount = ($GuestListing[$i]['checked_in_id'] == "Yes") ? $GuestListing[$i]['actual_no_of_guests'] : $GuestListing[$i]['no_of_guests'];
					/* $name = "";
						if ((isset($GuestListing['fname']) && !empty($GuestListing['fname']) ) ||
						(isset($GuestListing['lname']) && !empty($GuestListing['lname']) )) {
						
						if (!empty($GuestListing['fname']))
						$name = $name . $GuestListing['fname'];
						
						if (!empty($GuestListing['lname']))
						$name = $name . $GuestListing['lname'];
						}
						else {
						$name = $GuestListing['profilenam'];
					} */
					$GuestListing[$i]['fname'] = isset($GuestListing[$i]['fname']) && ($GuestListing[$i]['fname']) ? $GuestListing[$i]['fname'] : NULL;
					$GuestListing[$i]['lname'] = isset($GuestListing[$i]['lname']) && ($GuestListing[$i]['lname']) ? $GuestListing[$i]['lname'] : NULL;
					$name = (isset($GuestListing[$i]['full_name']) && !empty($GuestListing[$i]['full_name'])) ? $GuestListing[$i]['full_name'] : ((isset($GuestListing[$i]['fname']) && !empty($GuestListing[$i]['fname']) || isset($GuestListing[$i]['lname']) && !empty($GuestListing[$i]['lname'])) ? $GuestListing[$i]['fname'] . " " . $GuestListing[$i]['lname'] : (isset($GuestListing[$i]['profilenam']) && ($GuestListing[$i]['profilenam']) ? $GuestListing[$i]['profilenam'] : NULL));
					
					$host = trim($GuestListing[$i]['actualhost']);
					$host_type = trim($GuestListing[$i]['actualhost_type']);
					
					if (!empty($host) && !empty($host_type))
                    $host_name = get_teammember_name($host, $host_type);
					else {
						if (!empty($GuestListing[$i]['host_type']))
                        $host_name = get_teammember_name($GuestListing[$i]['host'], $GuestListing[$i]['host_type']);
						else
                        $host_name = (isset($GuestListing[$i]['host']) && !empty($GuestListing[$i]['host'])) ? $GuestListing[$i]['host'] : "N/A";
					}
					
					
					if (isset($GuestListing[$i]['event_id']))
                    $even_id = $GuestListing[$i]['event_id'];
					
					$GuestListingStr.= '{
					"rsvpId": "' . $GuestListing[$i]['id'] . '",
					"userId":"' . $GuestListing[$i]['mem_id'] . '",
					"userName":"' . $name . '",
					"profileImageUrl":"' . str_replace('"', '\"', $url) . '",
					"entourageCount":"' . str_replace('"', '\"', $entourageCount) . '",
					"host_name":"' . str_replace('"', '\"', $host_name) . '",
					"attendStatus": "' . str_replace('"', '\"', $status) . '",
					"checkinStatus":"' . str_replace('"', '\"', $GuestListing[$i]['checked_in_id']) . '"
					}';
					//if ($i < ($GuestListingStr['count'] - 1))
					$GuestListingStr .= ',';
				} //End of for ($i = 0; $i < $FollowedEvent['count']; $i++)
				
				$GuestListingStr = substr($GuestListingStr, 0, strlen($GuestListingStr) - 1);
				
				$GuestListing['isset_event'] = ((isset($GuestListing['isset_event'])) && ($GuestListing['isset_event'])) ? "yes" :
				"no";
				
				$hostpromoter_str = "";
				if ((isset($GuestListing['host_promoter_list']) && ($GuestListing['host_promoter_list']['count'] > 0)) ||
				(isset($GuestListing['non_member_host_promoter_list']) && ($GuestListing['non_member_host_promoter_list']['count'] > 0))) {
					if (isset($GuestListing['host_promoter_list']) && ($GuestListing['host_promoter_list']['count'] > 0)) {
						
						for ($p = 0; $p < $GuestListing['host_promoter_list']['count']; $p++) {
							$hostpromoter_str.='{ "hostID":"' . $GuestListing['host_promoter_list'][$p]['id'] . '-member",
							"hostName":"' . $GuestListing['host_promoter_list'][$p]['fname'] . " " . $GuestListing['host_promoter_list'][$p]['lname'] . '"} ,';
						}
					}
					
					if (isset($GuestListing['non_member_host_promoter_list']) && ($GuestListing['non_member_host_promoter_list']['count'] > 0)) {
						
						for ($p = 0; $p < $GuestListing['non_member_host_promoter_list']['count']; $p++) {
							$hostpromoter_str.='{ "hostID":"' . $GuestListing['non_member_host_promoter_list'][$p]['id'] . '-nonmember",
							"hostName":"' . ucwords($GuestListing['non_member_host_promoter_list'][$p]['name']) . '"} ,';
						}
					}
					
					$hostpromoter_str = substr($hostpromoter_str, 0, strlen($hostpromoter_str) - 1);
					} else {
					$hostpromoter_str.='';
				}
				//$hostpromoter_str.='{ "hostID":"' . $backstageUserId . '","hostName":"' . getname($backstageUserId) . '"}';
				$response_mess = '
				{
				' . response_repeat_string() . '
				"BSEViewGuestList":{
				"errorCode":"' . $return_codes["BSEViewGuestList"]["SuccessCode"] . '",
				"errorMsg":"' . $return_codes["BSEViewGuestList"]["SuccessDesc"] . '",
				"eventId":"' . $even_id . '",
				"totalRecordsCount":"' . str_replace('"', '\"', $GuestListing['tot_records']) . '",
				"currentListingCount": "' . str_replace('"', '\"', $GuestListing['count']) . '",
				"allowCheckIn":"' . str_replace('"', '\"', $GuestListing['isset_event']) . '",
				"allowCheckInMessage":"The option to check in guests will be active on the day of the event.",
				"host_promoter_list":[' . $hostpromoter_str . '],
				"guestListInfo":[' . $GuestListingStr . ']
				}
				}';
				} else {
				
				$response_mess = '
				{
				' . response_repeat_string() . '
				"BSEViewGuestList":{
				"errorCode":"' . $return_codes["BSEViewGuestList"]["NoRecordErrorCode"] . '",
				"errorMsg":"' . $return_codes["BSEViewGuestList"]["NoRecordErrorDesc"] . '",
				"eventId":"' . $GuestListing['even_id'] . '",
				"totalRecordsCount":"' . str_replace('"', '\"', $GuestListing['tot_records']) . '",
				"currentListingCount": "' . str_replace('"', '\"', $GuestListing['count']) . '",
				"guestListInfo":[
				' . $GuestListingStr . '
				]
				}
				}';
			}
			return getValidJSON($response_mess);
		}
		
		function bckSEViewNonMemGuestList($response_message, $xmlrequest) {
			
			global $return_codes;
			
			$pageNumber = $xmlrequest['BSEViewNonMemGuestList']['pageNumber'];
			$backstageUserId = $xmlrequest['BSEViewNonMemGuestList']['userId'];
			$GuestListing = array();
			
			$GuestListing = $this->bse_view_nonmember_guest_list($xmlrequest, $pageNumber, 20);
			
			
			$GuestListingStr = "";
			$event_id = isset($xmlrequest['BSEViewNonMemGuestList']['eventId']) ? ($xmlrequest['BSEViewNonMemGuestList']['eventId']) : "";
			
			
			$GuestListing['count'] = isset($GuestListing['count']) ? ($GuestListing['count']) : "";
			$GuestListing['tot_records'] = isset($GuestListing['tot_records']) ? ($GuestListing['tot_records']) : "";
			
			if (isset($GuestListing['count']) && ($GuestListing['count'] > 0)) {
				
				for ($i = 0; $i < $GuestListing['count']; $i++) {
					
					$GuestListing[$i]['checked_in_id'] = (isset($GuestListing[$i]['checked_in_id']) && ($GuestListing[$i]['checked_in_id'] != NULL)) ? "Yes" : "No";
					
					$entourageCount = ($GuestListing[$i]['checked_in_id'] == "Yes") ? $GuestListing[$i]['actual_no_of_guests'] : $GuestListing[$i]['entourage'];
					
					$GuestListing[$i]['name'] = isset($GuestListing[$i]['name']) && ($GuestListing[$i]['name']) ? $GuestListing[$i]['name'] : NULL;
					$GuestListing[$i]['email'] = isset($GuestListing[$i]['email']) && ($GuestListing[$i]['email']) ? $GuestListing[$i]['email'] : NULL;
					
					$name = (isset($GuestListing[$i]['name']) && !empty($GuestListing[$i]['name'])) ? $GuestListing[$i]['name'] : $GuestListing[$i]['email'];
					
					$host = trim($GuestListing[$i]['actualhost']);
					$host_type = trim($GuestListing[$i]['actualhost_type']);
					
					if (!empty($host) && !empty($host_type))
                    $host_name = get_teammember_name($host, $host_type);
					else
                    $host_name = "N/A";
					
					$GuestListingStr.= '{
					"non_mem_gl_id": "' . str_replace('"', '\"', $GuestListing[$i]['id']) . '",
					"userName": "' . str_replace('"', '\"', $name) . '",
					"userEmail": "' . str_replace('"', '\"', $GuestListing[$i]['email']) . '",
					"entourageCount":"' . str_replace('"', '\"', $entourageCount) . '",
					"host_name":"' . str_replace('"', '\"', $host_name) . '",
					"checkinStatus":"' . str_replace('"', '\"', $GuestListing[$i]['checked_in_id']) . '"
					}';
					
					$GuestListingStr .= ',';
				} //End of for ($i = 0; $i < $FollowedEvent['count']; $i++)
				
				$GuestListingStr = substr($GuestListingStr, 0, strlen($GuestListingStr) - 1);
				
				$GuestListing['isset_event'] = ((isset($GuestListing['isset_event'])) && ($GuestListing['isset_event'])) ? "yes" :
				"no";
				
				$hostpromoter_str = "";
				if ((isset($GuestListing['host_promoter_list']) && ($GuestListing['host_promoter_list']['count'] > 0)) ||
				(isset($GuestListing['non_member_host_promoter_list']) && ($GuestListing['non_member_host_promoter_list']['count'] > 0))) {
					if (isset($GuestListing['host_promoter_list']) && ($GuestListing['host_promoter_list']['count'] > 0)) {
						
						for ($p = 0; $p < $GuestListing['host_promoter_list']['count']; $p++) {
							$hostpromoter_str.='{ "hostID":"' . $GuestListing['host_promoter_list'][$p]['id'] . '-member",
							"hostName":"' . $GuestListing['host_promoter_list'][$p]['fname'] . " " . $GuestListing['host_promoter_list'][$p]['lname'] . '"} ,';
						}
					}
					
					if (isset($GuestListing['non_member_host_promoter_list']) && ($GuestListing['non_member_host_promoter_list']['count'] > 0)) {
						
						for ($p = 0; $p < $GuestListing['non_member_host_promoter_list']['count']; $p++) {
							$hostpromoter_str.='{ "hostID":"' . $GuestListing['non_member_host_promoter_list'][$p]['id'] . '-nonmember",
							"hostName":"' . ucwords($GuestListing['non_member_host_promoter_list'][$p]['name']) . '"} ,';
						}
					}
					
					$hostpromoter_str = substr($hostpromoter_str, 0, strlen($hostpromoter_str) - 1);
					} else {
					$hostpromoter_str.='{ "hostID":"' . $backstageUserId . '",
					"hostName":"' . getname($backstageUserId) . '"}';
				}
				
				$response_mess = '
				{
				' . response_repeat_string() . '
				"BSEViewNonMemGuestList":{
				"errorCode":"' . $return_codes["BSEViewNonMemGuestList"]["SuccessCode"] . '",
				"errorMsg":"' . $return_codes["BSEViewNonMemGuestList"]["SuccessDesc"] . '",
				"eventId":"' . $event_id . '",
				"totalRecordsCount":"' . $GuestListing['tot_records'] . '",
				"currentListingCount": "' . $GuestListing['count'] . '",
				"allowCheckIn":"' . str_replace('"', '\"', $GuestListing['isset_event']) . '",
				"allowCheckInMessage":"The option to check in guests will be active on the day of the event.",
				"host_promoter_list":[
				' . $hostpromoter_str . '
				],
				"guestListInfo":[
				' . $GuestListingStr . '
				]
				}
				}';
				} else {
				
				$response_mess = '
				{
				' . response_repeat_string() . '
				"BSEViewNonMemGuestList":{
				"errorCode":"' . $return_codes["BSEViewNonMemGuestList"]["NoRecordErrorCode"] . '",
				"errorMsg":"' . $return_codes["BSEViewNonMemGuestList"]["NoRecordErrorDesc"] . '",
				"eventId":"' . $event_id . '",
				"totalRecordsCount":"' . $GuestListing['tot_records'] . '",
				"currentListingCount": "' . $GuestListing['count'] . '",
				"guestListInfo":[
				' . $GuestListingStr . '
				]
				}
				}';
			}
			return getValidJSON($response_mess);
		}
		
		function bSEGLEntourageSearch($response_message, $xmlrequest) {
			global $return_codes;
			
			$pageNumber = $xmlrequest['BSEGLEntourageSearch']['pageNumber'];
			$backstageUserId = $xmlrequest['BSEGLEntourageSearch']['userId'];
			$GuestListing = array();
			
			$GuestListing = $this->bck_entourage_search($xmlrequest, $pageNumber, 20);
			
			$GuestListingStr = "";
			$GuestListing['even_id'] = isset($GuestListing['even_id']) ? ($GuestListing['even_id']) : "";
			$GuestListing['count'] = isset($GuestListing['count']) ? ($GuestListing['count']) : "";
			$GuestListing['tot_records'] = isset($GuestListing['tot_records']) ? ($GuestListing['tot_records']) : "";
			
			if (isset($GuestListing['count']) && ($GuestListing['count'] > 0)) {
				
				for ($i = 0; $i < $GuestListing['count']; $i++) {
					
					$GuestListing[$i]['checked_in_id'] = (isset($GuestListing[$i]['checked_in_id']) && ($GuestListing[$i]['checked_in_id'] != NULL)) ? "Yes" : "No";
					$GuestListing[$i]['photo_thumb'] = ((isset($GuestListing[$i]['is_facebook_user'])) && (strlen($GuestListing[$i]['photo_thumb']) > 7) && ($GuestListing[$i]['is_facebook_user'] == 'y' || $GuestListing[$i]['is_facebook_user'] == 'Y')) ? $GuestListing[$i]['photo_thumb'] : ((isset($GuestListing[$i]['photo_thumb']) && (strlen($GuestListing[$i]['photo_thumb']) > 7)) ? $this->profile_url . $GuestListing[$i]['photo_thumb'] : $this->profile_url . default_images($GuestListing[$i]['gender'], $GuestListing[$i]['profile_type']));
					
					$url = $GuestListing[$i]['photo_thumb'];
					
					if ((isset($GuestListing[$i]['attend'])) && ($GuestListing[$i]['attend'] == 1)) {
						$status = 'Yes';
					}
					if ((isset($GuestListing[$i]['attend'])) && ($GuestListing[$i]['attend'] == 2)) {
						$status = 'May be';
					}
					if ((isset($GuestListing[$i]['attend'])) && ($GuestListing[$i]['attend'] == 0)) {
						$status = 'No';
					}
					
					//$status = ($GuestListing[$i]['checked_in_id'] == "Yes") ? "Yes" : $status;
					$entourageCount = ($GuestListing[$i]['checked_in_id'] == "Yes") ? $GuestListing[$i]['actual_no_of_guests'] : $GuestListing[$i]['no_of_guests'];
					
					$GuestListing[$i]['fname'] = isset($GuestListing[$i]['fname']) && ($GuestListing[$i]['fname']) ? $GuestListing[$i]['fname'] : NULL;
					$GuestListing[$i]['lname'] = isset($GuestListing[$i]['lname']) && ($GuestListing[$i]['lname']) ? $GuestListing[$i]['lname'] : NULL;
					
					/* $name = (isset($GuestListing[$i]['fname']) && !empty($GuestListing[$i]['fname']) || isset($GuestListing[$i]['lname']) && !empty($GuestListing[$i]['lname'])) ? $GuestListing[$i]['fname'] . " " . $GuestListing[$i]['lname'] : (isset($GuestListing[$i]['profilenam']) && ($GuestListing[$i]['profilenam']) ? $GuestListing[$i]['profilenam'] : NULL); */
					
					
					$name = (isset($GuestListing[$i]['full_name']) && !empty($GuestListing[$i]['full_name'])) ? $GuestListing[$i]['full_name'] : ((isset($GuestListing[$i]['fname']) && !empty($GuestListing[$i]['fname']) || isset($GuestListing[$i]['lname']) && !empty($GuestListing[$i]['lname'])) ? $GuestListing[$i]['fname'] . " " . $GuestListing[$i]['lname'] : (isset($GuestListing[$i]['profilenam']) && ($GuestListing[$i]['profilenam']) ? $GuestListing[$i]['profilenam'] : NULL));
					
					
					$host = trim($GuestListing[$i]['actualhost']);
					$host_type = trim($GuestListing[$i]['actualhost_type']);
					
					if (!empty($host) && !empty($host_type))
                    $host_name = get_teammember_name($host, $host_type);
					else
                    $host_name = "N/A";
					
					
					if (isset($GuestListing[$i]['event_id']))
                    $even_id = $GuestListing[$i]['event_id'];
					
					$GuestListingStr.= '{
					"rsvpId": "' . $GuestListing[$i]['id'] . '",
					"userId":"' . $GuestListing[$i]['mem_id'] . '",
					"userName":"' . str_replace('"', '\"', $name) . '",
					"profileImageUrl":"' . str_replace('"', '\"', $url) . '",
					"entourageCount":"' . $entourageCount . '",
					"host_name":"' . str_replace('"', '\"', $host_name) . '",
					"attendStatus": "' . str_replace('"', '\"', $status) . '",
					"checkinStatus":"' . str_replace('"', '\"', $GuestListing[$i]['checked_in_id']) . '"
					}';
					//if ($i < ($GuestListingStr['count'] - 1))
					$GuestListingStr .= ',';
				} //End of for ($i = 0; $i < $FollowedEvent['count']; $i++)
				
				$GuestListingStr = substr($GuestListingStr, 0, strlen($GuestListingStr) - 1);
				
				$GuestListing['isset_event'] = ((isset($GuestListing['isset_event'])) && ($GuestListing['isset_event'])) ? "yes" :
				"no";
				
				$hostpromoter_str = "";
				if ((isset($GuestListing['host_promoter_list']) && ($GuestListing['host_promoter_list']['count'] > 0)) ||
				(isset($GuestListing['non_member_host_promoter_list']) && ($GuestListing['non_member_host_promoter_list']['count'] > 0))) {
					if (isset($GuestListing['host_promoter_list']) && ($GuestListing['host_promoter_list']['count'] > 0)) {
						
						for ($p = 0; $p < $GuestListing['host_promoter_list']['count']; $p++) {
							$hostpromoter_str.='{ "hostID":"' . $GuestListing['host_promoter_list'][$p]['id'] . '-member",
							"hostName":"' . $GuestListing['host_promoter_list'][$p]['fname'] . " " . $GuestListing['host_promoter_list'][$p]['lname'] . '"} ,';
						}
					}
					
					if (isset($GuestListing['non_member_host_promoter_list']) && ($GuestListing['non_member_host_promoter_list']['count'] > 0)) {
						
						for ($p = 0; $p < $GuestListing['non_member_host_promoter_list']['count']; $p++) {
							$hostpromoter_str.='{ "hostID":"' . $GuestListing['non_member_host_promoter_list'][$p]['id'] . '-nonmember",
							"hostName":"' . ucwords($GuestListing['non_member_host_promoter_list'][$p]['name']) . '"} ,';
						}
					}
					
					$hostpromoter_str = substr($hostpromoter_str, 0, strlen($hostpromoter_str) - 1);
					} else {
					$hostpromoter_str.='{ "hostID":"' . $backstageUserId . '",
					"hostName":"' . getname($backstageUserId) . '"}';
				}
				
				$response_mess = '
				{
				' . response_repeat_string() . '
				"BSEGLEntourageSearch":{
				"errorCode":"' . $return_codes["BSEGLEntourageSearch"]["SuccessCode"] . '",
				"errorMsg":"' . $return_codes["BSEGLEntourageSearch"]["SuccessDesc"] . '",
				"eventId":"' . $even_id . '",
				"totalRecordsCount":"' . $GuestListing['tot_records'] . '",
				"currentListingCount": "' . $GuestListing['count'] . '",
				"allowCheckIn":"' . str_replace('"', '\"', $GuestListing['isset_event']) . '",
				"allowCheckInMessage":"The option to check in guests will be active on the day of the event.",
				"host_promoter_list":[
				' . $hostpromoter_str . '
				],
				"guestListInfo":[
				' . $GuestListingStr . '
				]
				}
				}';
				} else {
				
				$response_mess = '
				{
				' . response_repeat_string() . '
				"BSEGLEntourageSearch":{
				"errorCode":"' . $return_codes["BSEGLEntourageSearch"]["NoRecordErrorCode"] . '",
				"errorMsg":"' . $return_codes["BSEGLEntourageSearch"]["NoRecordErrorDesc"] . '",
				"eventId":"' . $GuestListing['even_id'] . '",
				"totalRecordsCount":"' . str_replace('"', '\"', $GuestListing['tot_records']) . '",
				"currentListingCount": "' . str_replace('"', '\"', $GuestListing['count']) . '",
				"guestListInfo":[
				' . $GuestListingStr . '
				]
				}
				}';
			}
			return getValidJSON($response_mess);
		}
		
		/*
			function bSEGLEntourageSearchOld($response_message, $xmlrequest) {
			global $return_codes;
			
			$pageNumber = $xmlrequest['BSEGLEntourageSearch']['pageNumber'];
			$GuestListing = array();
			$obj_event = new BackStage();
			$GuestListing = $obj_event->bck_entourage_search($xmlrequest, $pageNumber, 10);
			
			$GuestListingStr = "";
			//$GuestListing['even_id'],$GuestListing['count']
			$GuestListing['even_id'] = isset($GuestListing['even_id']) ? ($GuestListing['even_id']) : "";
			$GuestListing['count'] = isset($GuestListing['count']) ? ($GuestListing['count']) : "";
			$GuestListing['tot_records'] = isset($GuestListing['tot_records']) ? ($GuestListing['tot_records']) : "";
			if (isset($GuestListing['count']) && ($GuestListing['count'] > 0)) {
			
			
			
			for ($i = 0; $i < $GuestListing['count']; $i++) {
			//                if ((isset($GuestListing[$i]['photo_thumb '])) && (strlen($GuestListing[$i]['photo_thumb ']) > 3)) {
			//                    $url = $this->profile_url . $GuestListing[$i]['photo_thumb '];
			//                } else {
			//                    $url = 'No';
			//                }
			$GuestListing[$i]['photo_thumb'] = ((isset($GuestListing[$i]['is_facebook_user'])) && ($GuestListing[$i]['is_facebook_user'] == 'y' || $GuestListing[$i]['is_facebook_user'] == 'Y')) ? $GuestListing[$i]['photo_thumb'] : ((isset($GuestListing[$i]['photo_thumb']) && (strlen($GuestListing[$i]['photo_thumb']) > 7)) ? $this->profile_url . $GuestListing[$i]['photo_thumb'] : $this->profile_url . default_images($GuestListing[$i]['gender'], $GuestListing[$i]['profile_type']));
			
			if ((isset($GuestListing[$i]['attend'])) && ($GuestListing[$i]['attend'] == 1)) {
			$status = 'Yes';
			}
			if ((isset($GuestListing[$i]['attend'])) && ($GuestListing[$i]['attend'] == 2)) {
			$status = 'May be';
			}
			$GuestListingStr.= '{
			"userId":"' . $GuestListing[$i]['mem_id'] . '",
			"userName":"' . $GuestListing[$i]['profilenam'] . '",
			"profileImageUrl":"' . $GuestListing[$i]['photo_thumb'] . '",
			"entourageCount":"' . $GuestListing[$i]['no_of_guests'] . '",
			"attendStatus": "' . $status . '"
			}';
			// if ($i < ($GuestListingStr['count'] - 1))
			$GuestListingStr .= ',';
			} //End of for ($i = 0; $i < $FollowedEvent['count']; $i++)
			
			$GuestListingStr = substr($GuestListingStr, 0, strlen($GuestListingStr) - 1);
			$response_mess = '
			{
			' . response_repeat_string() . '
			"BSEGLEntourageSearch":{
			"errorCode":"' . $return_codes["BSEGLEntourageSearch"]["SuccessCode"] . '",
			"errorMsg":"' . $return_codes["BSEGLEntourageSearch"]["SuccessDesc"] . '",
			"eventId":"' . $GuestListing['even_id'] . '",
			"totalRecordsCount":"' . $GuestListing['tot_records'] . '",
			"currentListingCount": "' . $GuestListing['count'] . '",
			"guestListInfo":[
			' . $GuestListingStr . '
			]
			}
			}';
			} else {
			
			$response_mess = '
			{
			' . response_repeat_string() . '
			"BSEGLEntourageSearch":{
			"errorCode":"' . $return_codes["BSEGLEntourageSearch"]["NoRecordErrorCode"] . '",
			"errorMsg":"' . $return_codes["BSEGLEntourageSearch"]["NoRecordErrorDesc"] . '",
			"eventId":"' . $GuestListing['even_id'] . '",
			"totalRecordsCount":"' . $GuestListing['tot_records'] . '",
			"currentListingCount": "' . $GuestListing['count'] . '",
			"guestListInfo":[
			' . $GuestListingStr . '
			]
			}
			}';
			}
			return $response_mess;
		} */
		
		function bSENonMemGLEntourageSearch($response_message, $xmlrequest) {
			global $return_codes;
			
			$pageNumber = $xmlrequest['BSENonMemGLEntourageSearch']['pageNumber'];
			$backstageUserId = $xmlrequest['BSENonMemGLEntourageSearch']['userId'];
			$GuestListing = array();
			
			$GuestListing = $this->bck_nonmem_entourage_search($xmlrequest, $pageNumber, 20);
			
			$GuestListingStr = "";
			$event_id = isset($xmlrequest['BSENonMemGLEntourageSearch']['eventId']) ? ($xmlrequest['BSENonMemGLEntourageSearch']['eventId']) : "";
			
			
			$GuestListing['count'] = isset($GuestListing['count']) ? ($GuestListing['count']) : "";
			$GuestListing['tot_records'] = isset($GuestListing['tot_records']) ? ($GuestListing['tot_records']) : "";
			
			if (isset($GuestListing['count']) && ($GuestListing['count'] > 0)) {
				
				for ($i = 0; $i < $GuestListing['count']; $i++) {
					
					$GuestListing[$i]['checked_in_id'] = (isset($GuestListing[$i]['checked_in_id']) && ($GuestListing[$i]['checked_in_id'] != NULL)) ? "Yes" : "No";
					
					$entourageCount = ($GuestListing[$i]['checked_in_id'] == "Yes") ? $GuestListing[$i]['actual_no_of_guests'] : $GuestListing[$i]['entourage'];
					
					$GuestListing[$i]['name'] = isset($GuestListing[$i]['name']) && ($GuestListing[$i]['name']) ? $GuestListing[$i]['name'] : NULL;
					$GuestListing[$i]['email'] = isset($GuestListing[$i]['email']) && ($GuestListing[$i]['email']) ? $GuestListing[$i]['email'] : NULL;
					
					$name = (isset($GuestListing[$i]['name']) && !empty($GuestListing[$i]['name'])) ? $GuestListing[$i]['name'] : $GuestListing[$i]['email'];
					
					$host = trim($GuestListing[$i]['actualhost']);
					$host_type = trim($GuestListing[$i]['actualhost_type']);
					
					if (!empty($host) && !empty($host_type))
                    $host_name = get_teammember_name($host, $host_type);
					else {
						if (!empty($GuestListing[$i]['host_type']))
                        $host_name = get_teammember_name($GuestListing[$i]['host'], $GuestListing[$i]['host_type']);
						else
                        $host_name = (isset($GuestListing[$i]['host']) && !empty($GuestListing[$i]['host'])) ? $GuestListing[$i]['host'] : "N/A";
					}
					
					
					$GuestListingStr.= '{
					"non_mem_gl_id": "' . $GuestListing[$i]['id'] . '",
					"userName": "' . str_replace('"', '\"', $name) . '",
					"userEmail": "' . str_replace('"', '\"', $GuestListing[$i]['email']) . '",
					"entourageCount":"' . $entourageCount . '",
					"host_name":"' . str_replace('"', '\"', $host_name) . '",
					"checkinStatus":"' . str_replace('"', '\"', $GuestListing[$i]['checked_in_id']) . '"
					}';
					
					$GuestListingStr .= ',';
				} //End of for ($i = 0; $i < $FollowedEvent['count']; $i++)
				
				$GuestListingStr = substr($GuestListingStr, 0, strlen($GuestListingStr) - 1);
				
				$GuestListing['isset_event'] = ((isset($GuestListing['isset_event'])) && ($GuestListing['isset_event'])) ? "yes" :
				"no";
				
				$hostpromoter_str = "";
				if ((isset($GuestListing['host_promoter_list']) && ($GuestListing['host_promoter_list']['count'] > 0)) ||
				(isset($GuestListing['non_member_host_promoter_list']) && ($GuestListing['non_member_host_promoter_list']['count'] > 0))) {
					if (isset($GuestListing['host_promoter_list']) && ($GuestListing['host_promoter_list']['count'] > 0)) {
						
						for ($p = 0; $p < $GuestListing['host_promoter_list']['count']; $p++) {
							$hostpromoter_str.='{ "hostID":"' . $GuestListing['host_promoter_list'][$p]['id'] . '-member",
							"hostName":"' . $GuestListing['host_promoter_list'][$p]['fname'] . " " . $GuestListing['host_promoter_list'][$p]['lname'] . '"} ,';
						}
					}
					
					if (isset($GuestListing['non_member_host_promoter_list']) && ($GuestListing['non_member_host_promoter_list']['count'] > 0)) {
						
						for ($p = 0; $p < $GuestListing['non_member_host_promoter_list']['count']; $p++) {
							$hostpromoter_str.='{ "hostID":"' . $GuestListing['non_member_host_promoter_list'][$p]['id'] . '-nonmember",
							"hostName":"' . ucwords($GuestListing['non_member_host_promoter_list'][$p]['name']) . '"} ,';
						}
					}
					
					$hostpromoter_str = substr($hostpromoter_str, 0, strlen($hostpromoter_str) - 1);
					} else {
					$hostpromoter_str.='{ "hostID":"' . $backstageUserId . '",
					"hostName":"' . getname($backstageUserId) . '"}';
				}
				
				$response_mess = '
				{
				' . response_repeat_string() . '
				"BSEViewNonMemGuestList":{
				"errorCode":"' . $return_codes["BSENonMemGLEntourageSearch"]["SuccessCode"] . '",
				"errorMsg":"' . $return_codes["BSENonMemGLEntourageSearch"]["SuccessDesc"] . '",
				"eventId":"' . $event_id . '",
				"totalRecordsCount":"' . str_replace('"', '\"', $GuestListing['tot_records']) . '",
				"currentListingCount": "' . str_replace('"', '\"', $GuestListing['count']) . '",
				"allowCheckIn":"' . str_replace('"', '\"', $GuestListing['isset_event']) . '",
				"allowCheckInMessage":"The option to check in guests will be active on the day of the event.",
				"host_promoter_list":[
				' . $hostpromoter_str . '
				],
				"guestListInfo":[
				' . $GuestListingStr . '
				]
				}
				}';
				} else {
				
				$response_mess = '
				{
				' . response_repeat_string() . '
				"BSENonMemGLEntourageSearch":{
				"errorCode":"' . $return_codes["BSENonMemGLEntourageSearch"]["NoRecordErrorCode"] . '",
				"errorMsg":"' . $return_codes["BSENonMemGLEntourageSearch"]["NoRecordErrorDesc"] . '",
				"eventId":"' . $event_id . '",
				"totalRecordsCount":"' . $GuestListing['tot_records'] . '",
				"currentListingCount": "' . $GuestListing['count'] . '",
				"guestListInfo":[
				' . $GuestListingStr . '
				]
				}
				}';
			}
			return getValidJSON($response_mess);
		}
		
		function bSEGLCheckIn($response_message, $xmlrequest) {
			global $return_codes;
			
			
			$userinfo = array();
			
			$userinfo = $this->bsegl_check_in($xmlrequest);
			
			if ((isset($userinfo['BSEGLCheckIn']['successful_fin'])) && (!$userinfo['BSEGLCheckIn']['successful_fin'])) {
				$obj_error = new Error();
				$response_message = $obj_error->error_type("BSEGLCheckIn", $userinfo);
				
				$userinfocode = $response_message['BSEGLCheckIn']['ErrorCode'];
				$userinfodesc = $response_message['BSEGLCheckIn']['ErrorDesc'];
				$response_mess = $response_mess = get_response_string("BSEGLCheckIn", $userinfocode, $userinfodesc);
				return getValidJSON($response_mess);
			}
			
			
			
			
			if ((isset($userinfo['BSEGLCheckIn']['successful_fin'])) && ($userinfo['BSEGLCheckIn']['successful_fin'])) {
				
				
				$response_mess = '
				{
				' . response_repeat_string() . '
				"BSEGLCheckIn":{
				"errorCode":"' . $return_codes["BSEGLCheckIn"]["SuccessCode"] . '",
				"errorMsg":"' . $return_codes["BSEGLCheckIn"]["SuccessDesc"] . '"
				
				}
				}';
				} else if ((isset($userinfo['BSEGLCheckIn']['errorInDateOfEvent']['successful_fin'])) && ($userinfo['BSEGLCheckIn']['errorInDateOfEvent']['successful_fin'])) {
				$response_mess = '
				{
				' . response_repeat_string() . '
				"BSEGLCheckIn":{
				"errorCode":"' . $return_codes["BSEGLCheckIn"]['errorInDateOfEvent']["SuccessCode"] . '",
				"errorMsg":"' . $return_codes["BSEGLCheckIn"]['errorInDateOfEvent']["SuccessDesc"] . '"
				
				}
				}';
				} else {
				
				$response_mess = '
				{
				' . response_repeat_string() . '
				"BSEGLCheckIn":{
				"errorCode":"' . $return_codes["BSEGLCheckIn"]["NoRecordErrorCode"] . '",
				"errorMsg":"' . $return_codes["BSEGLCheckIn"]["NoRecordErrorDesc"] . '"
				
				}
				}';
			}
			return getValidJSON($response_mess);
		}
		
		function bSENonMemGLCheckIn($response_message, $xmlrequest) {
			global $return_codes;
			
			
			$userinfo = array();
			
			$userinfo = $this->bse_nonmember_gl_check_in($xmlrequest);
			
			if ((isset($userinfo['BSENonMemGLCheckIn']['successful_fin'])) && (!$userinfo['BSENonMemGLCheckIn']['successful_fin'])) {
				$obj_error = new Error();
				$response_message = $obj_error->error_type("BSEGLCheckIn", $userinfo);
				
				$userinfocode = $response_message['BSENonMemGLCheckIn']['ErrorCode'];
				$userinfodesc = $response_message['BSENonMemGLCheckIn']['ErrorDesc'];
				$response_mess = $response_mess = get_response_string("BSENonMemGLCheckIn", $userinfocode, $userinfodesc);
				return getValidJSON($response_mess);
			}
			
			
			
			
			if ((isset($userinfo['BSENonMemGLCheckIn']['successful_fin'])) && ($userinfo['BSENonMemGLCheckIn']['successful_fin'])) {
				
				
				$response_mess = '
				{
				' . response_repeat_string() . '
				"BSENonMemGLCheckIn":{
				"errorCode":"' . $return_codes["BSENonMemGLCheckIn"]["SuccessCode"] . '",
				"errorMsg":"' . $return_codes["BSENonMemGLCheckIn"]["SuccessDesc"] . '"
				
				}
				}';
				} else {
				
				$response_mess = '
				{
				' . response_repeat_string() . '
				"BSENonMemGLCheckIn":{
				"errorCode":"' . $return_codes["BSENonMemGLCheckIn"]["NoRecordErrorCode"] . '",
				"errorMsg":"' . $return_codes["BSENonMemGLCheckIn"]["NoRecordErrorDesc"] . '"
				
				}
				}';
			}
			return getValidJSON($response_mess);
		}
		
		function bSEViewTblReservationList($response_message, $xmlrequest) {
			global $return_codes;
			$eventId = trim($xmlrequest['BSEViewTblReservationList']['eventId']);
			$pageNumber = $xmlrequest['BSEViewTblReservationList']['pageNumber'];
			
			$backstageUserId = $xmlrequest['BSEViewTblReservationList']['userId'];
			$tbl_reservation_list = array();
			
			$tbl_reservation_list = $this->bse_view_tbl_reservation_list($xmlrequest, $pageNumber, 20);
			
			$count = ((isset($tbl_reservation_list['count'])) && ($tbl_reservation_list['count'])) ? $tbl_reservation_list['count'] : 0;
			$tot_count = ((isset($tbl_reservation_list['tot_count'])) && ($tbl_reservation_list['tot_count'])) ? $tbl_reservation_list['tot_count'] : 0;
			
			$reservationListStr = "";
			
			if (isset($tbl_reservation_list['count']) && ($tbl_reservation_list['count'] > 0)) {
				
				for ($i = 0; $i < $tbl_reservation_list['count']; $i++) {
					$checkedInStatus = (isset($tbl_reservation_list[$i]['checkedin_event']) && ($tbl_reservation_list[$i]['checkedin_event'] != NULL)) ? "Yes" : "No";
					
					
					$tbl_reservation_list[$i]['vipfullname'] = isset($tbl_reservation_list[$i]['vipguest_fname']) && ($tbl_reservation_list[$i]['vipguest_fname']) ? $tbl_reservation_list[$i]['vipguest_fname'] : NULL;
					
					$tbl_reservation_list[$i]['vipfullname'] .=isset($tbl_reservation_list[$i]['vipguest_lname']) && ($tbl_reservation_list[$i]['vipguest_lname']) ? " " . $tbl_reservation_list[$i]['vipguest_lname'] : NULL;
					
					if (empty($tbl_reservation_list[$i]['vipfullname']))
                    $tbl_reservation_list[$i]['vipfullname'] = "Not Avail";
					
					
					$tbl_reservation_list[$i]['entourage'] = isset($tbl_reservation_list[$i]['entourage']) && ($tbl_reservation_list[$i]['entourage']) ? $tbl_reservation_list[$i]['entourage'] : NULL;
					
					
					$tableStr = ($checkedInStatus == "Yes") ? $tbl_reservation_list[$i]['actualtable_no'] : $tbl_reservation_list[$i]['table_no'];
					
					$table_display_str = ($checkedInStatus == "Yes") ? $tbl_reservation_list[$i]['actualtable_no'] : $tbl_reservation_list[$i]['table_display_no'];
					
					//  $table_display_str = isset($tbl_reservation_list[$i]['table_display_no']) && !empty($tbl_reservation_list[$i]['table_display_no']) ? $tbl_reservation_list[$i]['table_display_no'] : NULL;
					
					$entourageCountStr = ($checkedInStatus == "Yes") ? $tbl_reservation_list[$i]['actualguest'] : $tbl_reservation_list[$i]['expected_guest'];
					
					$host = trim($tbl_reservation_list[$i]['actualhost']);
					$host_type = trim($tbl_reservation_list[$i]['actualhost_type']);
					
					if (!empty($host) && !empty($host_type))
                    $host_name = get_teammember_name($host, $host_type);
					else
                    $host_name = "N/A";
					
					$reservationListStr.= '{
					"tbl_reservation_id":"' . $tbl_reservation_list[$i]['tbl_reservation_id'] . '",
					"userId":"' . str_replace('"', '\"', $tbl_reservation_list[$i]['addedby']) . '",
					"userName":"",
					"profileImageUrl":"",
					"vipfullname":"' . str_replace('"', '\"', $tbl_reservation_list[$i]['vipfullname']) . '",
					"table_no": "' . str_replace('"', '\"', $tableStr) . '",
					"table_display_no ": "' . str_replace('"', '\"', $table_display_str) . '",
					"entourageCount":"' . str_replace('"', '\"', $entourageCountStr) . '",
					"host_name":"' . str_replace('"', '\"', $host_name) . '",
					"checkedInStatus": "' . str_replace('"', '\"', $checkedInStatus) . '",
					"tableCapacity":"' . str_replace('"', '\"', $tbl_reservation_list[$i]['capacity']) . '"
					
					}';
					// if ($i < ($tbl_reservation_list['count'] - 1))
					$reservationListStr .= ',';
				}
				$reservationListStr = substr($reservationListStr, 0, strlen($reservationListStr) - 1);
				
				$tbl_reservation_list['isset_event'] = ((isset($tbl_reservation_list['isset_event'])) && ($tbl_reservation_list['isset_event'])) ? "yes" : "no";
				
				$hostpromoter_str = "";
				if ((isset($tbl_reservation_list['host_promoter_list']) && ($tbl_reservation_list['host_promoter_list']['count'] > 0)) ||
				(isset($tbl_reservation_list['non_member_host_promoter_list']) && ($tbl_reservation_list['non_member_host_promoter_list']['count'] > 0))) {
					if (isset($tbl_reservation_list['host_promoter_list']) && ($tbl_reservation_list['host_promoter_list']['count'] > 0)) {
						
						for ($p = 0; $p < $tbl_reservation_list['host_promoter_list']['count']; $p++) {
							$hostpromoter_str.='{ "hostID":"' . $tbl_reservation_list['host_promoter_list'][$p]['id'] . '-member",
							"hostName":"' . $tbl_reservation_list['host_promoter_list'][$p]['fname'] . " " . $tbl_reservation_list['host_promoter_list'][$p]['lname'] . '"} ,';
						}
					}
					
					if (isset($tbl_reservation_list['non_member_host_promoter_list']) && ($tbl_reservation_list['non_member_host_promoter_list']['count'] > 0)) {
						
						for ($p = 0; $p < $tbl_reservation_list['non_member_host_promoter_list']['count']; $p++) {
							$hostpromoter_str.='{ "hostID":"' . $tbl_reservation_list['non_member_host_promoter_list'][$p]['id'] . '-nonmember",
							"hostName":"' . ucwords($tbl_reservation_list['non_member_host_promoter_list'][$p]['name']) . '"} ,';
						}
					}
					
					$hostpromoter_str = substr($hostpromoter_str, 0, strlen($hostpromoter_str) - 1);
					} else {
					$hostpromoter_str.='{ "hostID":"' . $backstageUserId . '",
					"hostName":"' . getname($backstageUserId) . '"}';
				}
				
				// "nonMemberList":[
				//      '.$nonMemberList.'
				//            ],
				$response_mess = '
				{
				' . response_repeat_string() . '
				"BSEViewTblReservationList":{
				"errorCode":"' . $return_codes["BSEViewTblReservationList"]["SuccessCode"] . '",
				"errorMsg":"' . $return_codes["BSEViewTblReservationList"]["SuccessDesc"] . '",
				"eventId":"' . $eventId . '",
				"totalRecordsCount":"' . str_replace('"', '\"', $tot_count) . '",
				"allowCheckIn":"' . str_replace('"', '\"', $tbl_reservation_list['isset_event']) . '",
				"allowCheckInMessage":"The option to check in guests will be active on the day of the event.",
				"currentListingCount": "' . str_replace('"', '\"', $tbl_reservation_list['count']) . '",
				"host_promoter_list":[
				' . $hostpromoter_str . '
				],
				"tableListInfo":[
				' . $reservationListStr . '
				],
				"pageNumber":1
				}
				}';
				} else {
				
				$response_mess = '
				{
				' . response_repeat_string() . '
				"BSEViewTblReservationList":{
				"errorCode":"' . $return_codes["BSEViewTblReservationList"]["NoRecordErrorCode"] . '",
				"errorMsg":"' . $return_codes["BSEViewTblReservationList"]["NoRecordErrorDesc"] . '",
				"eventId":"' . $eventId . '",
				"totalRecordsCount":"' . str_replace('"', '\"', $tot_count) . '",
				"currentListingCount": "' . str_replace('"', '\"', $count) . '",
				"tableListInfo":[
				' . $reservationListStr . '
				]
				}
				}';
			}
			return getValidJSON($response_mess);
		}
		
		/*   function bSEViewTblReservationListOld($response_message, $xmlrequest) {
			global $return_codes;
			$eventId = trim($xmlrequest['BSEViewTblReservationList']['eventId']);
			$pageNumber = $xmlrequest['BSEViewTblReservationList']['pageNumber'];
			
			$backstageUserId = $xmlrequest['BSEViewTblReservationList']['userId'];
			$tbl_reservation_list = array();
			$obj_event = new BackStage();
			$tbl_reservation_list = $obj_event->bse_view_tbl_reservation_list($xmlrequest, $pageNumber, 10);
			if ((isset($tbl_reservation_list['count'])) && ($tbl_reservation_list['count'])) {
			$count = $tbl_reservation_list['count'];
			} else {
			$count = 0;
			}
			
			if ((isset($tbl_reservation_list['tot_count'])) && ($tbl_reservation_list['tot_count'])) {
			$tot_count = $tbl_reservation_list['tot_count'];
			} else {
			$tot_count = 0;
			}
			$reservationListStr = "";
			//$nonMemberList="";
			//$GuestListing['even_id'],$GuestListing['count']
			
			if (isset($tbl_reservation_list['count']) && ($tbl_reservation_list['count'] > 0)) {
			
			
			
			for ($i = 0; $i < $tbl_reservation_list['count']; $i++) {
			$checkedInStatus = (isset($tbl_reservation_list[$i]['checkedin_event']) && ($tbl_reservation_list[$i]['checkedin_event'] != NULL)) ? "Yes" : "No";
			//                if (isset($tbl_reservation_list[$i]['user_info']['photo_thumb']) && (strlen($tbl_reservation_list[$i]['user_info']['photo_thumb']) > 3)) {
			//                    $url = $this->profile_url . $tbl_reservation_list[$i]['user_info']['photo_thumb'];
			//                } else {
			//                    $url = 'No';
			//                }
			
			$tbl_reservation_list[$i]['name'] = isset($tbl_reservation_list[$i]['name']) && ($tbl_reservation_list[$i]['name']) ? $tbl_reservation_list[$i]['name'] : NULL;
			$tbl_reservation_list[$i]['entourage'] = isset($tbl_reservation_list[$i]['entourage']) && ($tbl_reservation_list[$i]['entourage']) ? $tbl_reservation_list[$i]['entourage'] : NULL;
			$tbl_reservation_list[$i]['email'] = isset($tbl_reservation_list[$i]['email']) && ($tbl_reservation_list[$i]['email']) ? $tbl_reservation_list[$i]['email'] : NULL;
			$tbl_reservation_list[$i]['host'] = isset($tbl_reservation_list[$i]['host']) && ($tbl_reservation_list[$i]['host']) ? $tbl_reservation_list[$i]['host'] : NULL;
			$tbl_reservation_list[$i]['host_type'] = isset($tbl_reservation_list[$i]['host_type']) && ($tbl_reservation_list[$i]['host_type']) ? $tbl_reservation_list[$i]['host_type'] : NULL;
			
			//                $nonMemberList.= '{
			//                                "Name":"' . $tbl_reservation_list[$i]['name'] . '",
			//                                "entourage":"' . $tbl_reservation_list[$i]['entourage'] . '",
			//                                "email":"' . $tbl_reservation_list[$i]['email'] . '",
			//                                 "host":"' . $tbl_reservation_list[$i]['host'] . '",
			//                                 "hostType":"' . $tbl_reservation_list[$i]['host_type'] . '"
			//                                    }';
			//                $nonMemberList .= ',';
			
			$tbl_reservation_list[$i]['user_info']['photo_thumb'] = ((isset($tbl_reservation_list[$i]['user_info']['is_facebook_user'])) && ($tbl_reservation_list[$i]['user_info']['is_facebook_user'] == 'y' || $tbl_reservation_list[$i]['user_info']['is_facebook_user'] == 'Y')) ? $tbl_reservation_list[$i]['user_info']['photo_thumb'] : ((isset($tbl_reservation_list[$i]['user_info']['photo_thumb']) && (strlen($tbl_reservation_list[$i]['user_info']['photo_thumb']) > 7)) ? $this->profile_url . $tbl_reservation_list[$i]['user_info']['photo_thumb'] : $this->profile_url . default_images($tbl_reservation_list[$i]['user_info']['gender'], $tbl_reservation_list[$i]['user_info']['profile_type']));
			
			$tableStr = ($checkedInStatus == "Yes") ? $tbl_reservation_list[$i]['actualtable_no'] : $tbl_reservation_list[$i]['table_no'];
			
			$table_display_str = isset($tbl_reservation_list[$i]['table_display_no ']) && !empty($tbl_reservation_list[$i]['table_display_no ']) ? $tbl_reservation_list[$i]['table_display_no'] : NULL;
			
			$entourageCountStr = ($checkedInStatus == "Yes") ? $tbl_reservation_list[$i]['actualguest'] : $tbl_reservation_list[$i]['expected_guest'];
			
			$name = '';
			$tbl_reservation_list[$i]['user_info']['fname'] = isset($tbl_reservation_list[$i]['user_info']['fname']) && ($tbl_reservation_list[$i]['user_info']['fname']) ? $tbl_reservation_list[$i]['user_info']['fname'] : NULL;
			$tbl_reservation_list[$i]['user_info']['lname'] = isset($tbl_reservation_list[$i]['user_info']['lname']) && ($tbl_reservation_list[$i]['user_info']['lname']) ? $tbl_reservation_list[$i]['user_info']['lname'] : NULL;
			$name = (isset($tbl_reservation_list[$i]['user_info']['fname']) && ($tbl_reservation_list[$i]['user_info']['fname']) || isset($tbl_reservation_list[$i]['user_info']['lname']) && ($tbl_reservation_list[$i]['user_info']['lname'])) ? ($tbl_reservation_list[$i]['user_info']['fname'] . " " . $GuestListing[$i]['user_info']['lname']) : (isset($GuestListing[$i]['user_info']['profilenam']) && ($GuestListing[$i]['user_info']['profilenam']) ? $GuestListing[$i]['user_info']['profilenam'] : NULL);
			
			
			$reservationListStr.= '{
			"userId":"' . $tbl_reservation_list[$i]['user_info']['mem_id'] . '",
			"userName":"' . $name . '",
			"profileImageUrl":"' . $tbl_reservation_list[$i]['user_info']['photo_thumb'] . '",
			"table_no": "' . $tableStr . '",
			"table_display_no ": "' . $table_display_str . '",
			"entourageCount":"' . $entourageCountStr . '",
			"checkedInStatus": "' . $checkedInStatus . '",
			"tableCapacity":"' . $tbl_reservation_list[$i]['capacity'] . '"
			
			}';
			// if ($i < ($tbl_reservation_list['count'] - 1))
			$reservationListStr .= ',';
			}
			$reservationListStr = substr($reservationListStr, 0, strlen($reservationListStr) - 1);
			
			$tbl_reservation_list['isset_event'] = ((isset($tbl_reservation_list['isset_event'])) && ($tbl_reservation_list['isset_event'])) ? "yes" : "no";
			
			$hostpromoter_str = "";
			if ((isset($tbl_reservation_list['host_promoter_list']) && ($tbl_reservation_list['host_promoter_list']['count'] > 0)) ||
			(isset($tbl_reservation_list['non_member_host_promoter_list']) && ($tbl_reservation_list['non_member_host_promoter_list']['count'] > 0))) {
			if (isset($tbl_reservation_list['host_promoter_list']) && ($tbl_reservation_list['host_promoter_list']['count'] > 0)) {
			
			for ($p = 0; $p < $tbl_reservation_list['host_promoter_list']['count']; $p++) {
			$hostpromoter_str.='{ "hostID":"' . $tbl_reservation_list['host_promoter_list'][$p]['id'] . '",
			"hostName":"' . $tbl_reservation_list['host_promoter_list'][$p]['fname'] . " " . $tbl_reservation_list['host_promoter_list'][$p]['lname'] . '"} ,';
			}
			}
			
			if (isset($tbl_reservation_list['non_member_host_promoter_list']) && ($tbl_reservation_list['non_member_host_promoter_list']['count'] > 0)) {
			
			for ($p = 0; $p < $tbl_reservation_list['non_member_host_promoter_list']['count']; $p++) {
			$hostpromoter_str.='{ "hostID":"' . $tbl_reservation_list['non_member_host_promoter_list'][$p]['id'] . '",
			"hostName":"' . ucwords($tbl_reservation_list['non_member_host_promoter_list'][$p]['name']) . '"} ,';
			}
			}
			
			$hostpromoter_str = substr($hostpromoter_str, 0, strlen($hostpromoter_str) - 1);
			} else {
			$hostpromoter_str.='{ "hostID":"' . $backstageUserId . '",
			"hostName":"' . getname($backstageUserId) . '"}';
			}
			
			// "nonMemberList":[
			//      '.$nonMemberList.'
			//            ],
			$response_mess = '
			{
			' . response_repeat_string() . '
			"BSEViewTblReservationList":{
			"errorCode":"' . $return_codes["BSEViewTblReservationList"]["SuccessCode"] . '",
			"errorMsg":"' . $return_codes["BSEViewTblReservationList"]["SuccessDesc"] . '",
			"eventId":"' . $eventId . '",
			"totalRecordsCount":"' . $tot_count . '",
			"allowCheckIn":"' . $tbl_reservation_list['isset_event'] . '",
			"allowCheckInMessage":"The option to check in guests will be active on the day of the event.",
			"currentListingCount": "' . $tbl_reservation_list['count'] . '",
			"host_promoter_list":[
			' . $hostpromoter_str . '
			],
			"tableListInfo":[
			' . $reservationListStr . '
			],
			"pageNumber":1
			}
			}';
			} else {
			
			$response_mess = '
			{
			' . response_repeat_string() . '
			"BSEViewTblReservationList":{
			"errorCode":"' . $return_codes["BSEViewTblReservationList"]["NoRecordErrorCode"] . '",
			"errorMsg":"' . $return_codes["BSEViewTblReservationList"]["NoRecordErrorDesc"] . '",
			"eventId":"' . $eventId . '",
			"totalRecordsCount":"' . $tot_count . '",
			"currentListingCount": "' . $count . '",
			"tableListInfo":[
			' . $reservationListStr . '
			]
			}
			}';
			}
			return $response_mess;
		} */
		
		function bSETRCheckInNotes($response_message, $xmlrequest) {
			global $return_codes;
			//$eventId = trim($xmlrequest['BSETRCheckInNotes']['eventId']);
			//$pageNumber = $xmlrequest['BSETRCheckInNotes']['pageNumber'];
			$check_in_notes = array();
			
			$check_in_notes = $this->bsetr_check_in_notes($xmlrequest);
			
			if (isset($check_in_notes['notes']) && ($check_in_notes['notes'])) {
				
				$response_mess = ' {
				' . response_repeat_string() . '
				"BSETRCheckInNotes": {
				"errorCode":"' . $return_codes["BSETRCheckInNotes"]["SuccessCode"] . '",
				"errorMsg":"' . $return_codes["BSETRCheckInNotes"]["SuccessDesc"] . '",
				"event_Id":"' . $check_in_notes['eve_id'] . '",
				"table_no":"' . $check_in_notes['table_id'] . '",
				"notes":"' . str_replace('"', '\"', $check_in_notes['notes']) . '"
				}
				}';
				} else {
				
				$response_mess = '
				{
				' . response_repeat_string() . '
				"BSETRCheckInNotes":{
				"errorCode":"' . $return_codes["BSETRCheckInNotes"]["NoRecordErrorCode"] . '",
				"errorMsg":"' . $return_codes["BSETRCheckInNotes"]["NoRecordErrorDesc"] . '"
				}
				}';
			}
			return getValidJSON($response_mess);
		}
		
		function bSETRViewCheckIn($response_message, $xmlrequest) {
			global $return_codes;
			//$eventId = trim($xmlrequest['BSETRCheckInNotes']['eventId']);
			//$pageNumber = $xmlrequest['BSETRCheckInNotes']['pageNumber'];
			$backstageUserId = $xmlrequest['BSETRViewCheckIn']['userId'];
			$check_in = array();
			
			$check_in = $this->bsetr_view_check_in($xmlrequest);
			
			if (isset($check_in['event_id']) && ($check_in['event_id'])) {
				if ((isset($check_in['checkedin_event'])) && ($check_in['checkedin_event'])) {
					
					
					
					$host = trim($check_in['actualhost']);
					$host_type = trim($check_in['actualhost_type']);
					
					if (!empty($host) && !empty($host_type))
                    $host_name = get_teammember_name($host, $host_type);
					else
                    $host_name = "N/A";
					
					
					
					$bottle_server = trim($check_in['actualbottle_server']);
					$bottle_server_type = trim($check_in['actualbottle_server_type']);
					
					if (!empty($bottle_server) && !empty($bottle_server_type))
                    $bottle_server_name = get_teammember_name($bottle_server, $bottle_server_type);
					else
                    $bottle_server_name = "N/A";
					
					$table_no = $check_in['actualtable_no'];
					$bottle_min = $check_in['actualbottle_min'];
					$min_spend = $check_in['actualmin_spend'];
					
					$entourageCount = $check_in['actualguest'];
					$vipguestname = $check_in['actual_vip_guest_name'];
					$checkedInStatus = 'yes';
					} else {
					
					$host = $check_in['expectedhost'];
					$host_type = $check_in['expectedhost_type'];
					$host_name = "N/A";
					$bottle_server = "";
					$bottle_server_name = "N/A";
					$bottle_server_type = "";
					
					$table_no = $check_in['table_display_no'];
					$bottle_min = $check_in['expectedbottle_min'];
					$min_spend = $check_in['expectedmin_spend'];
					
					$entourageCount = $check_in['expected_guest'];
					$vipguestname = $check_in['expected_vipguest_fname'] . " " . $check_in['expected_vipguest_lname'];
					
					$checkedInStatus = 'no';
				}
				
				/*             * ********************************* */
				
				$team_member_str = "";
				if ((isset($check_in['host_promoter_list']) && ($check_in['host_promoter_list']['count'] > 0)) ||
				(isset($check_in['non_member_host_promoter_list']) && ($check_in['non_member_host_promoter_list']['count'] > 0))) {
					if (isset($check_in['host_promoter_list']) && ($check_in['host_promoter_list']['count'] > 0)) {
						
						for ($p = 0; $p < $check_in['host_promoter_list']['count']; $p++) {
							$team_member_str.='{ "hostID":"' . $check_in['host_promoter_list'][$p]['id'] . '-member",
							"hostName":"' . str_replace('"', '\"', $check_in['host_promoter_list'][$p]['fname']) . " " . $check_in['host_promoter_list'][$p]['lname'] . '"} ,';
						}
					}
					
					if (isset($check_in['non_member_host_promoter_list']) && ($check_in['non_member_host_promoter_list']['count'] > 0)) {
						
						for ($p = 0; $p < $check_in['non_member_host_promoter_list']['count']; $p++) {
							$team_member_str.='{ "hostID":"' . $check_in['non_member_host_promoter_list'][$p]['id'] . '-nonmember",
							"hostName":"' . str_replace('"', '\"', ucwords($check_in['non_member_host_promoter_list'][$p]['name'])) . '"} ,';
						}
					}
					
					$team_member_str = substr($team_member_str, 0, strlen($team_member_str) - 1);
					} else {
					$team_member_str.='{ "hostID":"' . $backstageUserId . '",
					"hostName":"' . str_replace('"', '\"', getname($backstageUserId)) . '"}';
				}
				
				/*             * ********************************* */
				
				$response_mess = ' {
				' . response_repeat_string() . '
				"BSETRViewCheckIn": {
				"errorCode":"' . $return_codes["BSETRViewCheckIn"]["SuccessCode"] . '",
				"errorMsg":"' . $return_codes["BSETRViewCheckIn"]["SuccessDesc"] . '",
				"event_Id":"' . $check_in['event_id'] . '",
				"table_no":"' . str_replace('"', '\"', $table_no) . '",
				
				"tableListInfo":
				{	"tbl_reservation_id":"' . str_replace('"', '\"', $check_in['table_record']) . '",
				"eventId":"' . str_replace('"', '\"', $check_in['event_id']) . '",
				"table_no": "' . str_replace('"', '\"', $table_no) . '",
				"entourageCount":"' . str_replace('"', '\"', $entourageCount) . '",
				"checkedInStatus": "' . str_replace('"', '\"', $checkedInStatus) . '",
				"vipguestname":"' . str_replace('"', '\"', $vipguestname) . '",
				"host":"' . str_replace('"', '\"', $host) . '",
				"host_name":"' . str_replace('"', '\"', $host_name) . '",
				"host_type":"' . str_replace('"', '\"', $host_type) . '",
				"bottle_server":"' . str_replace('"', '\"', $bottle_server) . '",
				"bottle_server_name":"' . str_replace('"', '\"', $bottle_server_name) . '",
				"bottle_server_type":"' . str_replace('"', '\"', $bottle_server_type) . '",
				"team_member_list":[
				' . $team_member_str . '
				],
				"bottle_minimum":"' . str_replace('"', '\"', $bottle_min) . '",
				"minimum_spend":"' . str_replace('"', '\"', $min_spend) . '"
				
				}
				}
				}';
				} else {
				
				$response_mess = '
				{
				' . response_repeat_string() . '
				"BSETRViewCheckIn":{
				"errorCode":"' . $return_codes["BSETRViewCheckIn"]["NoRecordErrorCode"] . '",
				"errorMsg":"' . $return_codes["BSETRViewCheckIn"]["NoRecordErrorDesc"] . '"
				}
				}';
			}
			return getValidJSON($response_mess);
		}
		
		function bSETRConfirmMessageScreen($response_message, $xmlrequest) {
			global $return_codes;
			
			$userinfo = array();
			
			$userinfo = $this->bsetr_confirm_message_screen($xmlrequest);
			
			if ((isset($userinfo['BSETRConfirmMessageScreen']['successful_fin'])) && (!$userinfo['BSETRConfirmMessageScreen']['successful_fin'])) {
				$obj_error = new Error();
				$response_message = $obj_error->error_type("BSETRConfirmMessageScreen", $userinfo);
				
				$userinfocode = $response_message['BSETRConfirmMessageScreen']['ErrorCode'];
				$userinfodesc = $response_message['BSETRConfirmMessageScreen']['ErrorDesc'];
				$response_mess = $response_mess = get_response_string("BSETRConfirmMessageScreen", $userinfocode, $userinfodesc);
				return getValidJSON($response_mess);
			}
			
			
			
			
			if ((isset($userinfo['BSETRConfirmMessageScreen']['successful_fin'])) && ($userinfo['BSETRConfirmMessageScreen']['successful_fin'])) {
				
				
				$response_mess = '
				{
				' . response_repeat_string() . '
				"BSETRConfirmMessageScreen":{
				"errorCode":"' . $return_codes["BSETRConfirmMessageScreen"]["SuccessCode"] . '",
				"errorMsg":"' . $return_codes["BSETRConfirmMessageScreen"]["SuccessDesc"] . '"
				
				}
				}';
				} else {
				
				$response_mess = '
				{
				' . response_repeat_string() . '
				"BSETRConfirmMessageScreen":{
				"errorCode":"' . $return_codes["BSETRConfirmMessageScreen"]["NoRecordErrorCode"] . '",
				"errorMsg":"' . $return_codes["BSETRConfirmMessageScreen"]["NoRecordErrorDesc"] . '"
				
				}
				}';
			}
			return getValidJSON($response_mess);
		}
		
	}
	
?>
