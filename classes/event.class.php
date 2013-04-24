<?php

/* VSS SETTINGS
  $Archive:
  $Header:
  $Log:
  $Modtime:
  $Revision:
  $Workfile: */

/* * **********************************************************************************
  Identification
  File-name     : event.class.php
  Directory Path: $/MySNL/Deliverables/Code/MySNL_WebServiceV2/classes/event.class.php
  Author        : Rajesh Bakade
  Date          : 11/08/2011
  Modified By   : N/A
  Date          : N/A

  Include Files : none
  CSS File(s)   : none

  Functions Used :
  Javascript     :  none
  PHP            :  get_followed_event(),get_all_event(),get_nearby_event(),get_calender_event(),get_search_event(),get_event_details(),
  event_add_guest_list(),event_remove_guest_list(),event_view_guest_list(),event_comments_list(),get_total_comment(),
  get_user_briefprofile_info(),event_sharing(),event_sharing_valid(),error_CRUD(),display_parent_child_comments(),
  event_post_comments(),event_reply_comments(),event_photo_upload(),event_photo_upload_valid(),delete_event_comment(),
  eventList(),searchEvent(),eventDetails(),eventCommentList(),eventPostComment(),EventParentChildComments(),
  eventReplyComment(),eventCommentDelete(),eventViewGuestList(),eventAddGuestList(),eventRemoveGuestList(),eventSharing(),
  deleteEventComment()

  DataBase Table(s)   : members,tag_ent_list,tag_event_list bulletin,event_entourage_chkin,event_list

  Global Variable(s)  : $return_codes
  Constant(s)         : PROFILE_IMAGE_SITEURL , LOCAL_FOLDER

  Description         :  File to display the Latest Notifications for the perticular user from different module.
  Reviwed By  :
  Reviwed Date:
 * ************************************************************************************** */
/*
  class Events
  Purpose : with the help of event class user can see all/followed/nearby events .He/she can also
  also add himself/herself to that event as guest which after that will come in its followed event
  .User can also comment and also search event based on event name or event city.
 */

class Events {

    var $profile_url = PROFILE_IMAGE_SITEURL;
    var $local_folder = LOCAL_FOLDER;

//    private $_latitude1 = '34.102223';
//    private $_longitude1 = '-118.329125';

    /*  function get_followed_event()
      Purpose    : To get followed events by the user
      Parameters : $xmlrequest : Request array for followed events
      $pageNumber : Current page Number
      $limit      : no of results to be display on each page
      Returns    : the list of events which are followed by user */

    function get_followed_event($xmlrequest, $pagenumber, $limit) {

        if (DEBUG)
            writelog("event.class.php :: get_followed_event() :: ", "Starts Here ", false);

        $lowerlimit = isset($pagenumber) ? ($pagenumber - 1) * $limit : 0;

        $mem_id = isset($xmlrequest['Events']['userId']) && ($xmlrequest['Events']['userId']) ? mysql_real_escape_string($xmlrequest['Events']['userId']) : NULL;
        $latitude1 = isset($xmlrequest['Events']['latitude']) && ($xmlrequest['Events']['latitude']) ? mysql_real_escape_string($xmlrequest['Events']['latitude']) : NULL;
        $longitude1 = isset($xmlrequest['Events']['longitude']) && ($xmlrequest['Events']['longitude']) ? mysql_real_escape_string($xmlrequest['Events']['longitude']) : NULL;
        $latestEvent = isset($xmlrequest['Events']['latestEvent']) && ($xmlrequest['Events']['latestEvent']) ? mysql_real_escape_string($xmlrequest['Events']['latestEvent']) : NULL;
        $querycondn = (isset($latestEvent) && ($latestEvent > 0)) ? " AND (even_id > '" . $latestEvent . "')" : "";
		$count=0;
// to fetch the followed events
//06_02_2012      $query_event = "select even_id,even_title,even_img,even_desc,even_loc,guest,actualdate,actualtime,latitude,longitude from event_list where (even_own in (select frd_id from network where mem_id=$mem_id group by frd_id) or even_own = $mem_id ) and even_active = 'y' AND latitude !='' AND longitude !='' order by even_stat ASC limit $lowerlimit,$limit";
      $query_event = "SELECT SQL_CALC_FOUND_ROWS event_list.even_id,event_list.even_title,event_list.even_img,event_list.even_desc,event_list.even_loc,event_list.guest,event_list.actualdate,event_list.actualtime,members.latitude,members.longitude
FROM event_list INNER JOIN members ON members.profilenam=event_list.even_loc
WHERE (even_own IN (SELECT frd_id FROM network WHERE mem_id='$mem_id' GROUP BY frd_id) OR even_own = '$mem_id' ) AND event_list.even_active = 'y' GROUP BY event_list.even_id ORDER BY event_list.even_stat ASC
 LIMIT $lowerlimit,$limit";
 		//echo $query_event;
        if (DEBUG)
            writelog("event.class.php :: get_followed_event() :: Query to get events : ", $query_event, false);
        //$event = execute_query($query_event, true, "select");
		$result = execute_query_new($query_event);
		$total_event_records = execute_query("SELECT FOUND_ROWS() as TotalRecords;", true, "select");
		
        $event['totalrecords'] = (isset($total_event_records[0]['TotalRecords'])) ? $total_event_records[0]['TotalRecords'] : 0;
		$str="";
		if ((mysql_num_rows($result) > 0)) {
	    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
				$str.=$this->formEventResponse($row,$xmlrequest);
				$str=rtrim($str, ',');
				$str.= ',';
				$count++;
			}
		}
        
        $event['count'] = (isset($count)) ? $count : 0;
		$event['str']=$str;
		
        if (!empty($event) || $event['count'] > 0) {
		
            if (DEBUG) {
                writelog("Events:get_followed_event:", $event, true);
                writelog("event.class.php :: get_followed_event() :: ", "End Here ", false);
            }
            return $event;
        } else {
            return array();
        }
    }

    /*  function get_all_event()
      Purpose    : To get list of all the futured events
      Parameters : $xmlrequest : Request array for followed events
      $pageNumber : Current page Number
      $limit      : no of results to be display on each page
      Returns    : the list of events which are future */

    function get_all_event($xmlrequest, $pagenumber, $limit) {


        if (DEBUG)
            writelog("event.class.php :: get_all_event() :: ", "Starts Here ", false);
        $event = array();
        $lowerlimit = isset($pagenumber) ? ($pagenumber - 1) * $limit : 0;

        $latitude1 = isset($xmlrequest['Events']['latitude']) && ($xmlrequest['Events']['latitude']) ? mysql_real_escape_string($xmlrequest['Events']['latitude']) : NULL;
        $longitude1 = isset($xmlrequest['Events']['longitude']) && ($xmlrequest['Events']['longitude']) ? mysql_real_escape_string($xmlrequest['Events']['longitude']) : NULL;

        $latestEvent = isset($xmlrequest['Events']['latestEvent']) && ($xmlrequest['Events']['latestEvent']) ? trim($xmlrequest['Events']['latestEvent']) : NULL;
        $querycondn = (isset($latestEvent) && ($latestEvent > 0)) ? " AND (even_id > '" . $latestEvent . "')" : "";
        $mem_id = isset($xmlrequest['Events']['userId']) && ($xmlrequest['Events']['userId']) ? mysql_real_escape_string($xmlrequest['Events']['userId']) : NULL;
//to fetch all events
//        if (is_null($latitude1) && (is_null($longitude1))) {
//            $query_event = "SELECT latitude,longitude,even_id,even_title,even_img,even_desc,even_loc,guest,actualdate,actualtime FROM event_list WHERE  even_stat >='" . (time()) . "' order by even_dt ASC LIMIT $lowerlimit,$limit"; //SQL_CALC_FOUND_ROWS
//        } else {
//06_02_2012         $query_event = "SELECT latitude,longitude,even_id,even_title,even_img,even_desc,even_loc,guest,actualdate,actualtime FROM event_list WHERE  even_stat >='" . (time()) . "' order by even_stat ASC LIMIT $lowerlimit,$limit"; //SQL_CALC_FOUND_ROWS
         $query_event = "SELECT SQL_CALC_FOUND_ROWS event_list.even_id,members.latitude,members.longitude,event_list.even_title,event_list.even_img,event_list.even_desc,event_list.even_loc,event_list.guest,event_list.actualdate,event_list.actualtime
FROM event_list INNER JOIN members ON members.profilenam=event_list.even_loc
WHERE event_list.even_stat >='".time()."' AND event_list.even_active='y' GROUP BY event_list.even_id
ORDER BY event_list.even_stat ASC LIMIT $lowerlimit,$limit"; //SQL_CALC_FOUND_ROWS
//        }
        if (DEBUG)
            writelog("event.class.php :: get_all_event() :: Query to get events : ", $query_event, false);
        //$event = execute_query($query_event, true, "select");
		$result = execute_query_new($query_event);
		$total_event_records = execute_query("SELECT FOUND_ROWS() as TotalRecords ;", true, "select");
		$event['totalrecords'] = (isset($total_event_records[0]['TotalRecords'])) ? $total_event_records[0]['TotalRecords'] : 0;
		$str="";
		$count=0;
		if ((mysql_num_rows($result) > 0)) {
	    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
				$str.=$this->formEventResponse($row,$xmlrequest);
				$str=rtrim($str, ',');
				$str.= ',';
				$count++;
			}
		}
		 $event['count'] = (isset($count)) ? $count : 0;
		$event['str']=$str;
		
		 if (!empty($event) || $event['count'] > 0) {
		
            if (DEBUG) {
                   writelog("Events:get_all_event:", $event, true);
           writelog("event.class.php :: get_all_event() :: ", "End Here ", false);
            }
            return $event;
        }
        else {
            return array();
        }
    }

// end of get_All_event()

    /*  function get_all_event()
      Purpose    : To get list of all the nearby events of that user
      Parameters : $xmlrequest : Request array for followed events
      $pageNumber : Current page Number
      $limit      : no of results to be display on each page
      Returns    : the list of events which are nearby to user */

    function get_nearby_event($xmlrequest, $pagenumber, $limit) {

        if (DEBUG)
            writelog("event.class.php :: get_nearby_event() :: ", "Starts Here ", false);
        $lowerlimit = isset($pagenumber) ? ($pagenumber - 1) * $limit : 0;
        $latitude1 = isset($xmlrequest['Events']['latitude']) && ($xmlrequest['Events']['latitude']) ? mysql_real_escape_string($xmlrequest['Events']['latitude']) : NULL;
        $longitude1 = isset($xmlrequest['Events']['longitude']) && ($xmlrequest['Events']['longitude']) ? mysql_real_escape_string($xmlrequest['Events']['longitude']) : NULL;
        $latestEvent = isset($xmlrequest['Events']['latestEvent']) && ($xmlrequest['Events']['latestEvent']) ? mysql_real_escape_string($xmlrequest['Events']['latestEvent']) : NULL;
        $querycondn = (isset($latestEvent) && ($latestEvent > 0)) ? " AND (even_id > '" . $latestEvent . "')" : "";

        $mem_id = isset($xmlrequest['Events']['userId']) && ($xmlrequest['Events']['userId']) ? mysql_real_escape_string($xmlrequest['Events']['userId']) : NULL;
//to fetch the nearby events

//06_02_2012        $query_event = "SELECT latitude,longitude,even_id,even_title,even_img,even_desc,even_loc,guest,actualdate,actualtime FROM event_list WHERE even_stat >='" . (time()) . "' {$querycondn} order by even_dt ASC LIMIT $lowerlimit,$limit"; //SQL_CALC_FOUND_ROWS
        $query_event = "SELECT SQL_CALC_FOUND_ROWS event_list.even_id,( 3956 *2 * ASIN( SQRT( POWER( SIN( ( $latitude1 - ABS( members.latitude ) ) * PI( ) /180 /2 ) , 2 ) + COS( $latitude1 * PI( ) /180 ) * COS( ABS( members.latitude ) * PI( ) /180 ) * POWER( SIN( (
   ABS( $longitude1 ) - ABS( members.longitude ) ) * PI( ) /180 /2 ) , 2 ) ) )
 ) AS distance,event_list.even_title,event_list.even_img,event_list.even_desc,event_list.even_loc,event_list.guest,event_list.actualdate,event_list.actualtime,members.*
FROM event_list INNER JOIN members ON members.profilenam=event_list.even_loc
WHERE event_list.even_stat >='".time()."' GROUP BY event_list.even_id HAVING distance < 20
ORDER BY event_list.even_dt ASC LIMIT $lowerlimit,$limit"; //SQL_CALC_FOUND_ROWS

        if (DEBUG)
            writelog("event.class.php :: get_nearby_event() :: Query to get events :", $query_event, false);
        //$event = execute_query($query_event, true, 'select');
		$result = execute_query_new($query_event);
		$total_event_records = execute_query("SELECT FOUND_ROWS() as TotalRecords ;", true, "select");
		$event['totalrecords'] = (isset($total_event_records[0]['TotalRecords'])) ? $total_event_records[0]['TotalRecords'] : 0;
		$str="";
		$count=0;
		if ((mysql_num_rows($result) > 0)) {
	    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
				$str.=$this->formEventResponse($row,$xmlrequest);
				$str=rtrim($str, ',');
				$str.= ',';
				$count++;
			}
		}
		$event['count'] = (isset($count)) ? $count : 0;
		$event['str']=$str;
		 if (!empty($event) || $event['count'] > 0) {
		
            if (DEBUG) {
                   writelog("Events:get_nearby_event:", $event, true);
           writelog("event.class.php :: get_nearby_event() :: ", "End Here ", false);
            }
            return $event;
        }else {
            return array();
        }
    }

// end of get_NearBy_event()

    /*  function get_calender_event()
      Purpose    : To get list of all the calender events of that user
      Parameters : $xmlrequest : Request array for followed events
      $pageNumber : Current page Number
      $limit      : no of results to be display on each page
      Returns    : the list of events which are in calender of that user */

    function get_calender_event($xmlrequest, $pagenumber, $limit) {
        if (DEBUG)
            writelog("event.class.php :: get_calender_events() :: ", "Starts Here ", false);

        $lowerlimit = isset($pagenumber) ? ($pagenumber - 1) * $limit : 0;
        $latitude1 = isset($xmlrequest['Events']['latitude']) && ($xmlrequest['Events']['latitude']) ? $xmlrequest['Events']['latitude'] : NULL;
        $longitude1 = isset($xmlrequest['Events']['longitude']) && ($xmlrequest['Events']['longitude']) ? $xmlrequest['Events']['longitude'] : NULL;
        $latestEvent = isset($xmlrequest['Events']['latestEvent']) && ($xmlrequest['Events']['latestEvent']) ? trim($xmlrequest['Events']['latestEvent']) : NULL;
        $querycondn = (isset($latestEvent) && ($latestEvent > 0)) ? " AND (even_id > '" . $latestEvent . "')" : "";

        $toDate = date('Y-m-d');
        $time = (strlen(date('G')) > 1) ? date('G') . ':' . date('i A') : '0' . date('G') . ':' . date('i A');
        $mem_id = isset($xmlrequest['Events']['userId']) && ($xmlrequest['Events']['userId']) ? mysql_real_escape_string($xmlrequest['Events']['userId']) : NULL;
//to fetch the calender events
        $query_event = "SELECT SQL_CALC_FOUND_ROWS DISTINCT el.even_own,el.even_id,el.even_title,
            el.even_img,el.even_desc, el.even_loc,el.guest,el.actualdate,el.actualtime,
            el.latitude,el.longitude,el.even_active FROM calendar_events AS ce,
            event_list AS el WHERE ce.mem_id='$mem_id' AND ce.eve_id = el.even_id AND
            el.even_active='y' order by el.even_stat ASC LIMIT $lowerlimit,$limit";
			
		if (DEBUG)
            writelog("event.class.php :: get_calender_event() :: Query to get calender events :", $query_event, false);
			
       $result = execute_query_new($query_event);
		$total_event_records = execute_query("SELECT FOUND_ROWS() as TotalRecords ;", true, "select");
		$event['totalrecords'] = (isset($total_event_records[0]['TotalRecords'])) ? $total_event_records[0]['TotalRecords'] : 0;
		$str="";
		$count=0;
		if ((mysql_num_rows($result) > 0)) {
	    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
				$str.=$this->formEventResponse($row,$xmlrequest);
				$str.= ',';
				$count++;
			}
		}
		$event['count'] = (isset($count)) ? $count : 0;
		$event['str']=$str;
		 if (!empty($event) || $event['count'] > 0) {
		
            if (DEBUG) {
                   writelog("Events:get_calender_event:", $event, true);
           writelog("event.class.php :: get_calender_event() :: ", "End Here ", false);
            }
            return $event;
        } else {
            return array();
        }
    }

//end of get_calender_events()

    /*  function get_search_event()
      Purpose    : To get list of all the events of that user search by either name or city name
      Parameters : $xmlrequest : Request array for search events
      $pageNumber : Current page Number
      $limit      : no of results to be display on each page
      Returns    : the list of events which are search by either title or city */

    function get_search_event($xmlrequest, $pagenumber, $limit) {
		
        if (DEBUG)
            writelog("event.class.php :: event_search_result() :: ", "Starts Here ", false);

        $lowerlimit = isset($pagenumber) ? ($pagenumber - 1) * $limit : 0;

        $EventTitle = isset($xmlrequest['SearchEvent']['searchEventTitle']) && ($xmlrequest['SearchEvent']['searchEventTitle']) ? mysql_real_escape_string(trim($xmlrequest['SearchEvent']['searchEventTitle'])) : NULL;
        $EventLocation = isset($xmlrequest['SearchEvent']['searchEventLocation']) && ($xmlrequest['SearchEvent']['searchEventLocation']) ? mysql_real_escape_string(trim($xmlrequest['SearchEvent']['searchEventLocation'])) : NULL;
        $mem_id = isset($xmlrequest['SearchEvent']['userId']) && ($xmlrequest['SearchEvent']['userId']) ? mysql_real_escape_string($xmlrequest['SearchEvent']['userId']) : NULL;
        $latitude1 = isset($xmlrequest['SearchEvent']['latitude']) && ($xmlrequest['SearchEvent']['latitude']) ? mysql_real_escape_string($xmlrequest['SearchEvent']['latitude']) : NULL;
        $longitude1 = isset($xmlrequest['SearchEvent']['longitude']) && ($xmlrequest['SearchEvent']['longitude']) ? mysql_real_escape_string($xmlrequest['SearchEvent']['longitude']) : NULL;
        $latestEvent = isset($xmlrequest['SearchEvent']['latestEvent']) && ($xmlrequest['SearchEvent']['latestEvent']) ? trim(mysql_real_escape_string($xmlrequest['SearchEvent']['latestEvent'])) : NULL;
        $querycondn = (isset($latestEvent) && ($latestEvent > 0)) ? " AND (even_id > '" . $latestEvent . "')" : "";

        $whereClause = '';
			if (is_null($latitude1) && (is_null($longitude1))) {
				$query_event1 = "SELECT SQL_CALC_FOUND_ROWS even_id,even_title,even_img,even_desc,even_loc,guest,actualdate,actualtime,members.latitude,members.longitude FROM event_list,members WHERE"; //SQL_CALC_FOUND_ROWS
				} else {
				$query_event1 = "SELECT SQL_CALC_FOUND_ROWS ( 3959 * acos( cos( radians($latitude1) ) * cos( radians( members.latitude ) ) * cos( radians(members.longitude) - radians($longitude1)) + sin(radians($latitude1)) * sin( radians(members.latitude)))) AS distance,even_id,even_title,even_img,even_desc,even_loc,guest,actualdate,actualtime,members.latitude,members.longitude FROM event_list,members WHERE"; //SQL_CALC_FOUND_ROWS
			}
			
			if ($EventTitle == '' && $EventLocation != '') {
				//to fetch the events with only location filled
				
				if (is_null($latitude1) && (is_null($longitude1))) {
					$whereClause .= " members.profilenam = event_list.even_loc AND event_list.even_active='y' AND members.city LIKE '%" . mysql_real_escape_string($EventLocation) . "%' " . $querycondn . " GROUP BY event_list.even_id LIMIT $lowerlimit,$limit"; //SQL_CALC_FOUND_ROWS
					} else {
					$whereClause .= " members.profilenam = event_list.even_loc AND event_list.even_active='y' AND members.city LIKE '%" . mysql_real_escape_string($EventLocation) . "%' " . $querycondn . " GROUP BY event_list.even_id LIMIT $lowerlimit,$limit"; //SQL_CALC_FOUND_ROWS
				}
				} elseif ($EventLocation == '' && $EventTitle != '') {
				//to fetch the events with only title filled 
				if (is_null($latitude1) && (is_null($longitude1))) {
					$whereClause .= " members.profilenam = event_list.even_loc AND event_list.even_active='y' AND event_list.even_title like '%" . mysql_real_escape_string($EventTitle) . "%' " . $querycondn . " GROUP BY event_list.even_id LIMIT $lowerlimit,$limit"; //SQL_CALC_FOUND_ROWS
					} else {
					$whereClause .= " members.profilenam = event_list.even_loc AND event_list.even_active='y' AND event_list.even_title like '%" . mysql_real_escape_string($EventTitle) . "%' " . $querycondn . " GROUP BY event_list.even_id LIMIT $lowerlimit,$limit"; //SQL_CALC_FOUND_ROWS
				}
				} elseif ($EventTitle != '' && $EventLocation != '') {
				
				//to fetch the events with both title and location filled
				if (is_null($latitude1) && (is_null($longitude1))) {
					$whereClause .= " members.profilenam = event_list.even_loc AND event_list.even_active='y' AND event_list.even_title like '%" . mysql_real_escape_string($EventTitle) . "%' AND even_city like '%" . mysql_real_escape_string($EventLocation) . "%' " . $querycondn . " GROUP BY event_list.even_id LIMIT $lowerlimit,$limit"; //SQL_CALC_FOUND_ROWS
					} else {
					$whereClause .= " members.profilenam = event_list.even_loc AND event_list.even_active='y' AND event_list.even_title like '%" . mysql_real_escape_string($EventTitle) . "%' AND even_city like '%" . mysql_real_escape_string($EventLocation) . "%' " . $querycondn . " GROUP BY event_list.even_id LIMIT $lowerlimit,$limit"; //SQL_CALC_FOUND_ROWS
				}
			}
        if (DEBUG)
            writelog("event.class.php :: event_search_result() :: Query to get events :", $query_event, false);

        $query_event = $query_event1 . $whereClause;
		$result = execute_query_new($query_event);
		$total_event_records = execute_query("SELECT FOUND_ROWS() as TotalRecords ;", true, "select");
        $event['totalrecords'] = (isset($total_event_records[0]['TotalRecords'])) ? $total_event_records[0]['TotalRecords'] : 0;
		$str="";
		$count=0;
		$latitude1 = floatval(isset($xmlrequest['SearchEvent']['latitude']) && ($xmlrequest['SearchEvent']['latitude']) ? $xmlrequest['SearchEvent']['latitude'] : NULL);
        $longitude1 = floatval(isset($xmlrequest['SearchEvent']['longitude']) && ($xmlrequest['SearchEvent']['longitude']) ? $xmlrequest['SearchEvent']['longitude'] : NULL);
		
		if ((mysql_num_rows($result) > 0)) {
	    while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
				if (isset($row['latitude']) && ($row['latitude']) && isset($row['longitude']) && ($row['longitude'])) {
                    //$getLatLongInRange = LatLongInRange($row['latitude'], $row['longitude']);
                    $eventList = distanceByApi($latitude1, $longitude1, $row);
                } else {
                    $eventList = NULL;
                }
				
				 $distance = isset($eventList['distance']) && ($eventList['distance']) ? round($eventList['distance'], 2) . ' miles' : 'Distance Not Present';
                $eventList[0]['even_desc'] = str_replace('\\', "", $eventList[0]['even_desc']);
                $eventList[0]['even_desc'] = str_replace(array("\r\n", "\r", "\n", "<br />", "<br/>"), "\\n", $eventList[0]['even_desc']);
                $eventList[0]['even_desc'] = strip_tags($eventList[0]['even_desc']);
                $eventList[0]['even_desc'] = str_replace(array("\"", "\'"), "", $eventList[0]['even_desc']);
				$width_even_img = NULL;
                $height_even_img = NULL;
                if (is_readable($this->local_folder . $eventList[0]['even_img'])) {
                    list($width_even_img, $height_even_img) = (isset($eventList[0]['even_img']) && (strlen($eventList[0]['even_img']) > 7)) ? getimagesize($this->local_folder . $eventList[0]['even_img']) : NULL;

                    $sizee = getimagesize($this->local_folder . $eventList[0]['even_img']);
                    if (preg_match('/^image\/(jp[e]?g|png|gif)$/', $sizee['mime'])) {
                        $eventList[0]['even_img'] = isset($eventList[0]['even_img']) && (strlen($eventList[0]['even_img']) > 7) ? event_image_detail($eventList[0]['even_id'], $eventList[0]['even_img'], 1) : NULL;
                        list($width_even_img, $height_even_img) = (isset($eventList[0]['even_img']) && (strlen($eventList[0]['even_img']) > 7)) ? getimagesize($this->local_folder . $eventList[0]['even_img']) : NULL;
                    }
                }
				$eventList[0]['even_img'] = isset($eventList[0]['even_img']) && (strlen($eventList[0]['even_img']) > 7) ? $this->profile_url . $eventList[0]['even_img'] : NULL;
                $counter++;
                $str .= '{
                    "eventId":"' .str_replace('"', '\"',$eventList[0]['even_id']). '",
                    "eventTitle":"' .str_replace('"', '\"',strtoupper(trim(preg_replace('/\s+/', ' ',$eventList[0]['even_title'])))) . '",
                    "eventImageUrl":"' .str_replace('"', '\"',$eventList[0]['even_img']). '",
                    "height":"' .str_replace('"', '\"',$height_even_img). '",
                    "width":"' .str_replace('"', '\"',$width_even_img). '",
                    "eventDescription":"' .str_replace('"', '\"',trim(preg_replace('/\s+/', ' ', $eventList[0]['even_desc']))) . '",
                    "eventLocation":"' .str_replace('"', '\"',$eventList[0]['even_loc']) . '",
                    "eventDate":"' .str_replace('"', '\"',$eventList[0]['actualdate']) . '",
                    "eventGuestListExists":"' . (($eventList[0]['guest'] == 'y') ? "true" : "false") . '",
                    "eventTime":"' .str_replace('"', '\"',$eventList[0]['actualtime']) . '",
                    "distance":"' .str_replace('"', '\"',$distance) . '"
                             }';

                $str = $str . ',';
				$count++;
			}
		}

        $event['count'] = (isset($count)) ? $count : 0;
		$event['str']=$str;
		       //print_r($event);
		if ($event['count'] > 0 || !empty($event)) {
            if (DEBUG) {
                writelog("Events:event_search_result:", $event, true);
                writelog("event.class.php :: event_search_result() :: ", "End Here ", false);
            }
	    
            return $event;
        } else {
            return array();
        }
    }

// end of get_search_event()

    /*  function get_event_details()
      Purpose    : To get detail information about event
      Parameters : $xmlrequest : Request array for event detail
      Returns    : detail information about event */

    function get_event_details($xmlrequest) {

        if (DEBUG)
            writelog("event.class.php :: get_event_details() :: ", "Starts Here ", false);
        $event = array();
        $mem_id = isset($xmlrequest['EventDetails']['userId']) && ($xmlrequest['EventDetails']['userId']) ? mysql_real_escape_string($xmlrequest['EventDetails']['userId']) : NULL;
        $eventId = isset($xmlrequest['EventDetails']['eventId']) && ($xmlrequest['EventDetails']['eventId']) ? mysql_real_escape_string($xmlrequest['EventDetails']['eventId']) : NULL;
        //getting events information details
        $query_event = "SELECT event_cat.event_nam,event_list. * , members.mem_id, members.profilenam, members.fname, members.lname, members.photo as profileimageurl,members.gender,members.profile_type FROM event_list, members,event_cat WHERE event_list.even_id = '" . $eventId . "' AND event_list.even_cat=event_cat.event_id AND event_list.even_own = members.mem_id";
        if (DEBUG)
            writelog("event.class.php :: get_event_details() :: ", $query_event, false);
        $event = execute_query($query_event, false, "select");

        if (!empty($event)) {
            /*             * **** Get Music List Start ******* */
            //for selecting music type
            $getMusic = "select event_music_nam FROM event_music WHERE eventmusic_id IN ({$event['even_music']})";
            $exeGetMusic = execute_query($getMusic, true, "select");
            $exeGetMusic1 = array();
            foreach ($exeGetMusic as $kk => $musicList) {
                if (!empty($musicList) && is_numeric($kk)) {
                    $exeGetMusic1[] = isset($exeGetMusic[$kk]['event_music_nam']) && ($exeGetMusic[$kk]['event_music_nam']) ? $exeGetMusic[$kk]['event_music_nam'] : NULL;
                }
            }
            $event['music'] = implode(',', $exeGetMusic1);

            /*             * **** Get Music List End ******* */
            //for whether user is added to guest list or not
            $query_guest_list = "SELECT COUNT(*) FROM rsvp WHERE event_id = '" . $eventId . "' AND profileid='$mem_id'";
            $result = execute_query($query_guest_list, false, "select");
            $event['addGuestList'] = (isset($result['COUNT(*)'])) && ($result['COUNT(*)']) ? 'yes' : 'no';
            if (DEBUG) {
                writelog("Events:get_event_details:", $event, true);
                writelog("event.class.php :: get_event_details() :: ", "End Here ", false);
            }
	/* for events who expired */
	    $getEvents = "SELECT COUNT(*) as totalcount FROM event_list WHERE even_id='$eventId' and even_stat >='" . time() . "'";
	    $getValidEvent = execute_query($getEvents, true, "select");
	   if ($getValidEvent[0]['totalcount'] > 0) {
	       $event['guest'] = 'y';
	   }else{
	       $event['guest'] = 'n';
	   }
	    return $event;
        } else {
            return array();
        }
    }

//end of get_event_Details()

    /*  function event_add_guest_list()
      Purpose    : To add urself in guest list
      Parameters : $xmlrequest : Request array for event add guest list
      Returns    : array returning informaion about the user added in guestlist */

    function event_add_guest_list($xmlrequest) {
	if (DEBUG)
	    writelog("event.class.php :: event_add_guest_list() :: ", "Starts Here ", false);

	$mem_id = isset($xmlrequest['EventAddGuestList']['userId']) && ($xmlrequest['EventAddGuestList']['userId']) ? mysql_real_escape_string($xmlrequest['EventAddGuestList']['userId']) : NULL;
	$fName = isset($xmlrequest['EventAddGuestList']['fullName']) && ($xmlrequest['EventAddGuestList']['fullName']) ? mysql_real_escape_string($xmlrequest['EventAddGuestList']['fullName']) : NULL;
	$Guests = isset($xmlrequest['EventAddGuestList']['noOfGuest']) && ($xmlrequest['EventAddGuestList']['noOfGuest']) ? mysql_real_escape_string($xmlrequest['EventAddGuestList']['noOfGuest']) : NULL;
	$attend = isset($xmlrequest['EventAddGuestList']['attendStatus']) && ($xmlrequest['EventAddGuestList']['attendStatus']) ? mysql_real_escape_string($xmlrequest['EventAddGuestList']['attendStatus']) : NULL;
	$eventId = isset($xmlrequest['EventAddGuestList']['eventId']) && ($xmlrequest['EventAddGuestList']['eventId']) ? mysql_real_escape_string($xmlrequest['EventAddGuestList']['eventId']) : NULL;

	    if ($attend == 'Y') {
		$attend = 1;
	    } elseif ($attend == 'N') {
		$attend = 0;
	    } elseif ($attend == 'M') {
		$attend = 2;
	    }
	    //for information about user
	    $profile_info = "SELECT mem_id,profilenam FROM members WHERE mem_id= '$mem_id'";
	    $profile = execute_query($profile_info, true, "select");
	    $profile_id = isset($profile[0]['mem_id']) && ($profile[0]['mem_id']) ? $profile[0]['mem_id'] : NULL;
	    $profile_name = isset($profile[0]['profilenam']) && ($profile[0]['profilenam']) ? $profile[0]['profilenam'] : NULL;
	    if ($mem_id != $profile_id) {
		$event['count'] = 0;
		$event['error'] = 'Invalid member Id';
	    } else {
		//for inserting the user in guestlist
		$query_event = "INSERT INTO rsvp(frnd_id,event_id,full_name,attend,no_of_guests,profilenam,profileid) VALUES ('$mem_id', '$eventId', '$fName', $attend,$Guests,'$profile_name','$profile_id')";
		if (DEBUG)
		    writelog("event.class.php :: get_event_details() :: ", $query_event, false);
		$event = execute_query($query_event, true, "insert");
		$event['count'] = (isset($event['count'])) ? $event['count'] : 0;
	    }
	    if (DEBUG) {
		writelog("event.class.php :: event_add_guest_list:", $event, true);
		writelog("event.class.php :: event_add_guest_list() :: ", "End Here ", false);
	    }
	    $event['Status'] = $attend;
	    return $event;

    }

//end of event_add_guest_list()

    /*  function event_remove_guest_list()
      Purpose    : To remove urself from guest list
      Parameters : $xmlrequest : Request array for remove from guest list
      Returns    : count whether guest is removed or not */

    function event_remove_guest_list($xmlRequest) {

        if (DEBUG)
            writelog("event.class.php :: event_remove_guest_list() :: ", "Starts Here ", false);

        $removeGuest = array();
        $userId = mysql_real_escape_string($xmlRequest['EventRemoveGuestList']['userId']);
        $eventId = mysql_real_escape_string($xmlRequest['EventRemoveGuestList']['eventId']);
        //get the no of times the user added to guest list
        $getrsvpGuest = "SELECT COUNT(*) FROM rsvp WHERE event_id = '" . $eventId . "' AND profileid='" . $userId . "'";
        $result = execute_query($getrsvpGuest, true, "select");

        if (!empty($result)) {
//delete from rsvp table
            $queryremoveGuest = "DELETE FROM rsvp WHERE event_id='$eventId' AND profileid='$userId'";
            $removeGuest = execute_query($queryremoveGuest, false, "delete");
//delete from event entourage chkin table
            $query_entourage_chkin = "DELETE FROM event_entourage_chkin WHERE event_id='$eventId' AND profileid='$userId'";
            $remove_entourage_chkin = execute_query($query_entourage_chkin, false, "delete");
            if (DEBUG) {
                writelog("event.class.php :: event_remove_guest_list() :: ", $queryremoveGuest, false);
                writelog("Events:event_remove_guest_list:", $removeGuest, true);
                writelog("event.class.php :: event_remove_guest_list() :: ", "End Here ", false);
            }
            return $removeGuest;
        } else {
            return array();
        }
    }

    /*  function event_view_guest_list()
      Purpose    : To view guest list
      Parameters : $xmlrequest : Request array for view guest list
      Returns    : count for no of guest added */

    function event_view_guest_list($xmlrequest) {
        if (DEBUG)
            writelog("event.class.php :: ViewGuest() :: ", "Starts Here ", false);
        $eventId = isset($xmlrequest['EventViewGuestList']['eventId']) && ($xmlrequest['EventViewGuestList']['eventId']) ? mysql_real_escape_string($xmlrequest['EventViewGuestList']['eventId']) : NULL;
//for getting guest list
        $event = "SELECT rsvp.id,rsvp.profileid,rsvp.attend,rsvp.profilenam,rsvp.no_of_guests,
                    members.photo_thumb,members.profile_type,members.gender
                    FROM rsvp
                    LEFT JOIN event_list ON (event_list.even_id=rsvp.event_id AND event_list.even_active='y'),
                    members WHERE
                    rsvp.event_id='$eventId' AND rsvp.profileid=members.mem_id
                    AND rsvp.id IN(
                    SELECT MAX(rsvp.id) AS max_id FROM rsvp LEFT JOIN event_list ON (event_list.even_id=rsvp.event_id AND event_list.even_active='y'),
                    members
                    WHERE rsvp.event_id='$eventId'
                    AND rsvp.profileid=members.mem_id
                    GROUP BY rsvp.profileid) ORDER BY rsvp.id DESC";

        $event = execute_query($event, true, "select");
        $event['count'] = (isset($event['count'])) ? $event['count'] : 0;
        if ($event['count'] > 0) {
            if (DEBUG) {
                writelog("event.class.php :: ViewGuest() :: ", $event, false);
                writelog("Events:get_event_details:", $event, true);
                writelog("event.class.php :: get_event_details() :: ", "End Here ", false);
            }
            return $event;
        } else {
            return FALSE;
        }
    }

    /*  function event_comments_list()
      Purpose    : To view the comment list
      Parameters : $xmlrequest : Request array for event comment list
      Returns    : array formatted list of comments */

    function event_comments_list($xmlresponse, $pageNumber, $limit) {
        if (DEBUG)
            writelog("event.class.php :: Event_Comments() :: ", "Starts Here ", false);

        $lowerlimit = isset($pageNumber) ? ($pageNumber - 1) * $limit : 0;
        $mem_id = mysql_real_escape_string($xmlresponse['EventComments']['userId']);
        $eventId = mysql_real_escape_string($xmlresponse['EventComments']['eventId']);
        //get event comment list
        $event_comment = "select SQL_CALC_FOUND_ROWS pa.album_id,ec.id,ec.photo_album_id,ec.post_via,ec.date,ec.comment,mem.mem_id,mem.profile_type,mem.photo_b_thumb,mem.is_facebook_user,mem.profilenam,ec.link_image,ec.link_url,ec.image_link,ec.youtubeLink from event_list as el,members as mem,events_comments as ec LEFT JOIN photo_album as pa ON (ec.photo_album_id = pa.photo_id) where el.even_id=" . $eventId . " and el.even_id = ec.even_id AND ec.from_id = mem.mem_id and ec.parent_id=0 order by ec.date desc LIMIT $lowerlimit,$limit";
        $event = execute_query($event_comment, true, "select");
        $count = isset($event['count']) && ($event['count']) ? $event['count'] : 0;
        $totalComment = execute_query("SELECT FOUND_ROWS() as TotalRecords ;", false);
        $event['total'] = $totalComment['TotalRecords'];
        for ($i = 0; $i < $count; $i++) {
            if ((isset($event[$i]['id'])) && ($event[$i]['id']))
                $event[$i]['totalCommentCount'] = $this->get_total_comment($event[$i]['id']);
        }
        if (DEBUG) {
            writelog("event.class.php :: Event_Comments() :: ", $event, false);
            writelog("Events:Event_Comments:", $event, true);
            writelog("event.class.php :: Event_Comments() :: ", "End Here ", false);
        }
        return $event;
    }

    /*  function event_comments_list()
      Purpose    : To get the total comment list
      Parameters : $xmlrequest : Request array for total of event comment list
      Returns    : list of comments */

    function get_total_comment($id) {
        if (DEBUG)
            writelog("event.class.php :: get_total_comment()  : " . $id . " :: ", "Start Here ", false);
        $query = "SELECT COUNT(*) FROM events_comments WHERE parent_id='$id'";
        if (DEBUG)
            writelog("event.class.php :: get_total_comment() :: query:", $query, false);
        $result = execute_query($query, false, "select");

        $result['COUNT(*)'] = isset($result['COUNT(*)']) && ($result['COUNT(*)']) ? $result['COUNT(*)'] : NULL;
        if (DEBUG)
            writelog("event.class.php :: get_total_comment() for HotPressId : " . $id . " :: ", "End Here ", false, $result['COUNT(*)']);
        return $result['COUNT(*)'];
    }

    /*  function get_user_briefprofile_info()
      Purpose    : ?
      Parameters : $xmlrequest : Request array for total of event comment list
      Returns    : ? */

    function get_user_briefprofile_info($userid) {
        if (DEBUG)
            writelog("event.class.php :: get_user_briefprofile_info() for UserId : " . $userid . " :: ", "Start Here ", false);
        $userinfo = array();
        $query_userinfo = "SELECT is_facebook_user,mem_id,profilenam,photo_thumb,photo_b_thumb,gender,profile_type  FROM members WHERE mem_id='$userid'";
        if (DEBUG)
            writelog("event.class.php :: get_user_briefprofile_info() :: query:", $query_userinfo, false);
        $result_userinfo = mysql_query($query_userinfo);
        if ((mysql_num_rows($result_userinfo) > 0)) {
            $row_userinfo = mysql_fetch_array($result_userinfo, MYSQL_ASSOC);
        }

        if (DEBUG)
            writelog("event.class.php :: get_user_briefprofile_info() for UserId : " . $userid . " :: ", "End Here ", false);
        return $row_userinfo;
    }

//end of get_user_briefprofile_info()

    /*  function event_sharing()
      Purpose    : ?
      Parameters : $xmlrequest : Request array for total of event comment list
      Returns    : ? */

    function event_sharing($xmlrequest) {

        $userId = mysql_real_escape_string($xmlrequest['eventSharing']['userId']);
        $eventId = mysql_real_escape_string($xmlrequest['eventSharing']['eventId']);
        $displayAsHotPress = mysql_real_escape_string($xmlrequest['eventSharing']['displayAsHotPress']);
        $commentText = mysql_real_escape_string($xmlrequest['eventSharing']['commentText']);
        $displayAsHotPress = '';

        if ((isset($xmlrequest['eventSharing']['displayAsHotPress'])) && ($xmlrequest['eventSharing']['displayAsHotPress'])) {
            $displayAsHotPress = $xmlrequest['eventSharing']['displayAsHotPress'];
        } else {
            $displayAsHotPress = null;
            $photo_url = '';
            $link_url = '';
        }
        $error = array();

        //$query_calender = "SELECT COUNT(*) FROM calendar_events WHERE mem_id='$userId' AND eve_id='$eventId'";
        //$result_calender = execute_query($query_calender, false, "select");
//        if ((isset($result_calender['COUNT(*)'])) && ($result_calender['COUNT(*)']) && ($displayAsHotPress)) {

        $query = "SELECT even_title,even_desc,even_img,even_id FROM event_list WHERE even_id='$eventId'"; //AND even_own='$userId'
        $result = execute_query($query, false, "select");
        if (!empty($result) && is_array($result)) {
            $date = strtotime("now");
            $photo_url = $result['even_img'];
            $even_title = trim($result['even_title']);
            $commentText = str_replace(array("\'", "\,", "\""), "", $commentText);
            $result['even_desc'] = trim(str_replace(array("\'", "\,", "\""), "", $result['even_desc']));
            $even_title = trim(str_replace(array("\'", "\,", "\""), "", $even_title));

            $error['comment'] = $commentText;
            $error['even_desc'] = $result['even_desc'];
            $error['even_title'] = $even_title;

            //$even_desc = $commentText . "<br /><br />" . $error['even_title'] . "<br /><br />" . $result['even_desc'];
            $even_desc = $commentText . '<br /><br /><a href="index.php?pg=events&s=view&eve_id=' . $result['even_id'] . '" target="_blank">' . $error['even_title'] . "</a><br /><br />" . $result['even_desc'];
            $even_desc = str_replace('\'', "", $even_desc);
            $link_url = 'index.php?pg=events&s=view&eve_id=' . $result['even_id'];

            $privacy = user_privacy_settings($userId);
            if (isset($privacy) && ($privacy == 'private')) {
                $visible = 'allfriends'; //allfriends';
            } else {
                $visible = '';
            }

            $get_event_share = $this->get_event_share_info($userId, $eventId);

            if ($get_event_share === TRUE) {
                $query_event = "INSERT INTO bulletin(mem_id,subj,body,visible_to,bulletin.date,parentid,from_id,link_image,testo_id,link_url,post_via)VALUE('$userId','" . $even_title . "','" . $even_desc . "','$visible','" . $date . "',0,0,'" . $photo_url . "','0','" . $link_url . "','1')"; //$displayAsHotPress
                $result = execute_query($query_event, false, "insert");
                $affected_row_testimonial = $result['count'];
                if (isset($affected_row_testimonial)) {
                    $insert_into_hotpress_share = execute_query("INSERT INTO share_event_hotpress(mem_id,eve_id,even_title) VALUES('$userId','$eventId','$even_title')", true, "insert");
                }
                $error['hotpressid'] = $result['last_id'];
                $error['even_id'] = $eventId;
                $error['eventSharing'] = $this->error_CRUD($xmlrequest, $affected_row_testimonial);

                if ((isset($error['eventSharing']['successful_fin'])) && (!$error['eventSharing']['successful_fin'])) {
                    return $error;
                } else {
//                    $error['calender'] = true;
                    $error['eventSharing']['successful_fin'] = true;
                    $error['eventSharing']['event_share_out_of_bound'] = false;
                    return $error;
                }
                if (DEBUG)
                    writelog("Profile:event_sharing", $error, true);
                return $error;
            }else {
                $error['eventSharing']['event_share_out_of_bound'] = true;
                return $error;
            }
        }
    }

    function get_event_share_info($userid, $event_id) {

        $query_hotpress = "SELECT COUNT(*) as cnt FROM share_event_hotpress WHERE mem_id='$userid' AND eve_id='$event_id'"; //AND(from_id =0)
        $get_share_event_result = execute_query($query_hotpress, true, "select");
        if ($get_share_event_result[0]['cnt'] >= 3) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    /*  function event_sharing_valid()
      Purpose    : ?
      Parameters : $xmlrequest : Request array for event sharing valid
      Returns    : ? */

    function event_sharing_valid($xmlrequest) {

        $userId = mysql_real_escape_string($xmlrequest['eventSharing']['userId']);
        $eventId = mysql_real_escape_string($xmlrequest['eventSharing']['eventId']);
        $error = array();
        //get information about event
        $query = "SELECT COUNT(*) FROM event_list WHERE even_id='$eventId'";
        $result = execute_query($query, false);

        if ((isset($result['COUNT(*)'])) && ($result['COUNT(*)'])) {
            $error['successful'] = true;
        } else {
            $error['successful'] = false;
        }
        return $error;
    }

    /*  function error_CRUD()
      Purpose    : ?
      Parameters : $xmlrequest  : ?
      $affected_row: ?
      Returns    : ? */

    function error_CRUD($xmlrequest, $affected_row) {
        $request_keys = array_keys($xmlrequest);
        $key = $request_keys[1];
        if ($affected_row > 0) {
            $error['successful_fin'] = true;
        } else {
            $error['successful_fin'] = false;
        }
        return $error;
    }

    /*  function display_parent_child_comments()
      Purpose    : to display parent child comment
      Parameters : $xmlrequest  : request array for parent child comment
      Returns    : array containing parent child comment list */

    function display_parent_child_comments($xmlresponse,$pagenumber,$limit) {

	if (DEBUG)
	    writelog("event.class.php :: display_parent_child_comments() :: ", "Starts Here ", false);
	if (isset($xmlresponse['EventParentChildComment']['userId']) && $xmlresponse['EventParentChildComment']['userId'] != ''
		&& isset($xmlresponse['EventParentChildComment']['eventId']) && $xmlresponse['EventParentChildComment']['eventId'] != '') {
	    $lowerlimit = isset($pagenumber) ? ($pagenumber - 1) * $limit : 0;
	    $usr_id = mysql_real_escape_string($xmlresponse['EventParentChildComment']['userId']);
	    $eventId = mysql_real_escape_string($xmlresponse['EventParentChildComment']['eventId']);
	    $commentId = mysql_real_escape_string($xmlresponse['EventParentChildComment']['commentId']);
	    //get the information about parent comment

	    $queryParentComment['parent'] = execute_query("SELECT ec.id,ec.post_via,ec.photo_album_id,mem.mem_id,mem.photo_b_thumb,mem.profilenam, ec.comment,mem.profile_type,mem.gender,ec.date,ec.link_url,ec.youtubeLink,ec.link_image FROM events_comments as ec,members as mem WHERE id='" . $commentId . "' AND ec.from_id=mem.mem_id", false, "select");

	    $ent = array();
	    $event_comment_count = "select COUNT(*) as cnt from events_comments where even_id='" . $eventId . "' and parent_id='" . $queryParentComment['parent']['id'] . "'";
	    $eventCount = execute_query($event_comment_count, false, "select");
	    //get the info. about photo
	    $queryPhotoId = "select photo_id,album_id,photo_mid from photo_album where photo_id='" . $queryParentComment['parent']['photo_album_id'] . "'";
	    $exePhotoId = execute_query($queryPhotoId, false, "select");
	    $ent['parentPhotoId'] = isset($exePhotoId['photo_id']) && ($exePhotoId['photo_id']) ? $exePhotoId['photo_id'] : NULL;
	    $ent['parentAlbumId'] = isset($exePhotoId['album_id']) && ($exePhotoId['album_id']) ? $exePhotoId['album_id'] : NULL;
	    $ent['parentPhotoMid'] = isset($exePhotoId['photo_mid']) && ($exePhotoId['photo_mid']) ? $exePhotoId['photo_mid'] : NULL;
	    $queryParentComment['parent']['parentPhotoInfo'] = $ent;
	    $queryParentComment['parent']['totalCommentCount'] = $eventCount['cnt'];

	    $parentId = $queryParentComment['parent']['id'];
	    if (isset($parentId) && $parentId != '') {
//get the information about child comment
		$queryChildComment = "SELECT ec.id,mem.mem_id,mem.photo_b_thumb,ec.post_via,mem.profilenam, ec.comment,mem.profile_type,mem.gender,ec.date,ec.link_url,ec.youtubeLink,ec.link_image FROM events_comments as ec,members as mem WHERE parent_id='" . $parentId . "' AND ec.from_id=mem.mem_id LIMIT $lowerlimit,$limit";
		$exeChildComment = execute_query($queryChildComment, true, "select");
		$queryParentComment['parent']['currentCommentCount'] = $exeChildComment['count'];
		$ent1 = array();
		$getChildComments['child'] = $exeChildComment;
		foreach ($getChildComments['child'] as $kk => $photoId1) {
		    //get the info. about photo
		    $queryPhotoId = "select photo_id,album_id,photo_mid from photo_album where photo_id='" . $photoId1['photo_album_id'] . "'";
		    $exePhotoId = execute_query($queryPhotoId, false, "select");

		    if (!empty($exePhotoId)) {
			$ent1['childPhotoId'] = isset($exePhotoId['photo_id']) && ($exePhotoId['photo_id']) ? $exePhotoId['photo_id'] : NULL;
			$ent1['childAlbumId'] = isset($exePhotoId['album_id']) && ($exePhotoId['album_id']) ? $exePhotoId['album_id'] : NULL;
			$ent1['childPhotoMid'] = isset($exePhotoId['photo_mid']) && ($exePhotoId['photo_mid']) ? $exePhotoId['photo_mid'] : NULL;
			$getChildComments['child'][$kk]['childPhotoInfo'] = $ent1;
		    }
		}
	    }
	    $parentChild = array();
	    $parentChild[] = $queryParentComment;
	    $parentChild[] = $getChildComments;

	    if (DEBUG) {
		writelog("Events:display_parent_child_comments:", $parentChild, true);
		writelog("event.class.php :: display_parent_child_comments() :: ", "End Here ", false);
	    }
	    
	    return $parentChild;
	} else {
	    return array();
	}
    }

    /*  function event_post_comments()
      Purpose    : to post comment on event
      Parameters : $xmlrequest  : request array for posting comment on event
      Returns    : comment id */

    function event_post_comments($xmlresponse) {
        if (DEBUG)
            writelog("event.class.php :: Event_Post_Comments() :: ", "Starts Here ", false);

        $usr_id = mysql_real_escape_string($xmlresponse['EventPostComment']['userId']);
        $comment = mysql_real_escape_string($xmlresponse['EventPostComment']['comment']);
        $eventId = mysql_real_escape_string($xmlresponse['EventPostComment']['eventId']);
        $displayasHotpress = mysql_real_escape_string($xmlresponse['EventPostComment']['displayAsHotPress']);

        $privacy = user_privacy_settings($usr_id);
        if (isset($privacy) && ($privacy == 'private')) {
            $visible = 'allfriends'; //allfriends';
        } else {
            $visible = '';
        }
        //get event owner
        $event_check = execute_query("SELECT even_own,even_title FROM event_list WHERE even_id='" . $eventId . "'", true, "select");
        $event_own = $event_check[0]['even_own'];
        $event_title = $event_check[0]['even_title'];

        if ($event_check['count'] > 0) {
            //insert comment in event comment
            $event_data = execute_query("INSERT into events_comments(parent_id,even_id,mem_id,from_id,comment,date,msg_alert,post_via) values ('','$eventId','$event_own','$usr_id','$comment','" . time() . "','Y','1')", true, "insert");
            $comment_id = $event_data['last_id'];
//send email
            $get_user_email_id = execute_query("select profilenam,email,photo_thumb from members where mem_id='$usr_id'", false, "select");
            $get_profile_user_email_id = execute_query("select profilenam,email from members where mem_id='$event_own'", false, "select");
            if ($usr_id != $event_own) {
                $userName = getname($usr_id);
                push_notification('event_post_comment', $usr_id, $event_own);
                $body1 = getname($usr_id) . " posted a new comment for " . $event_title . "<a href='http://www.socialnightlife.com/development/index.php?pg=events&s=view&eve_id=$eventId' target='_blank'> Click here</a> to read and respond.";
                $matter = email_template($get_user_email_id['profilenam'], "$userName posted a new comment for $event_title.", $body1, $usr_id, $get_user_email_id['photo_thumb']);
                firemail($get_profile_user_email_id['email'], "From: socialNightLife <socialnightlife.com>\r\n", "$userName posted a new comment for $event_title.", $matter);
            }
            if ($displayasHotpress) {
                //insert comment in bulleting to display on hotpress
                $hotpress_data = execute_query("INSERT INTO bulletin(mem_id,subj,body,visible_to,date,parentid,from_id,image_link,photo_album_id,msg_alert,eventcmmnt_id,post_via) VALUES('$usr_id','','$comment','$visible','" . time() . "','','$event_own','','','Y','$comment_id','1')", false, "insert");
                $hotpress_comment_id = $hotpress_data['last_id'];
                // update the event comment table with bulletin id
                $query_hotpress = "UPDATE events_comments SET bullet_id='$hotpress_comment_id' WHERE id='$comment_id'";
                $event_result = execute_query($query_hotpress, false, "update");
            }

            if (DEBUG) {
                writelog("event.class.php :: Event_Post_Comments() :: ", $event_data, false);
                writelog("Events:Event_Post_Comments:", $event_data, true);
                writelog("event.class.php :: Event_Post_Comments() :: ", "End Here ", false);
            }
            return $comment_id;
        }
    }

    /*  function event_reply_comments()
      Purpose    : to reply on comment in event
      Parameters : $xmlresponse  : request array for posting comment on event
      Returns    : comment id */

    function event_reply_comments($xmlresponse) {

        if (DEBUG)
            writelog("event.class.php :: event_reply_comments() :: ", "Starts Here ", false);
        if (isset($xmlresponse['EventReplyComment']['userId']) && $xmlresponse['EventReplyComment']['userId'] != ''
                && isset($xmlresponse['EventReplyComment']['eventId']) && $xmlresponse['EventReplyComment']['eventId'] != '') {

            $usr_id = mysql_real_escape_string($xmlresponse['EventReplyComment']['userId']);
            $comment = mysql_real_escape_string($xmlresponse['EventReplyComment']['comment']);
            $eventId = mysql_real_escape_string($xmlresponse['EventReplyComment']['eventId']);
            $commentId = mysql_real_escape_string($xmlresponse['EventReplyComment']['commentId']);

            $privacy = user_privacy_settings($usr_id);
            if (isset($privacy) && ($privacy == 'private')) {
                $visible = 'allfriends'; //allfriends';
            } else {
                $visible = '';
            }
            //for selecting event owner
            $event_check = execute_query("SELECT even_own FROM event_list WHERE even_id='" . $eventId . "'", false, "select");
            $event_own = isset($event_check['even_own']) && $event_check['even_own'] ? $event_check['even_own'] : NULL;

            $hotpress_id = NULL;
            //for selecting bulletin id
            $check_hotpress_id = execute_query("SELECT id FROM bulletin WHERE eventcmmnt_id='$commentId'", false, "select");
            if (isset($check_hotpress_id['id']) && ($check_hotpress_id['id'])) {
                //inserting comment in butteting to display on hotpress
                $hotpress_data = execute_query("INSERT INTO bulletin(mem_id,subj,body,visible_to,date,parentid,from_id,image_link,photo_album_id,msg_alert,post_via) VALUES('$usr_id','','$comment','$visible','" . time() . "','" . $check_hotpress_id['id'] . "','','','','Y','1')", false, "insert");
                $hotpress_id = isset($hotpress_data['last_id']) && ($hotpress_data['last_id']) ? $hotpress_data['last_id'] : NULL;
            }
            //inserting comment in event comment
            $event_data = execute_query("INSERT into events_comments(parent_id,even_id,mem_id,from_id,comment,date,msg_alert,post_via,bullet_id) values ('$commentId','$eventId','$event_own','$usr_id','$comment','" . time() . "','Y','1','$hotpress_id')", false, "insert");
            $comment_id = isset($event_data['last_id']) && ($event_data['last_id']) ? $event_data['last_id'] : NULL;

            $get_parent_comment_owner = execute_query("select from_id from events_comments where id='$commentId'", false, "select");
            if ($usr_id != $get_parent_comment_owner['from_id']) {
                push_notification('reply_event_comment', $usr_id, $get_parent_comment_owner['from_id']);
            }
            $hotpress_comment_id = 0;

            if (DEBUG) {
                writelog("event.class.php :: event_reply_comments() :: ", $event_data, false);
                writelog("Events:event_reply_comments:", $event_data, true);
                writelog("event.class.php :: event_reply_comments() :: ", "End Here ", false);
            }

            return $comment_id;
        } else {
            return array();
        }
    }

//end of event_reply_comments()

    /*  function event_photo_upload()
      Purpose    : ?
      Parameters : $xmlresponse  : request array for posting comment on event
      Returns    : ? */


    function event_photo_upload($xmlrequest) {
        if (DEBUG)
            writelog("event.class.php :: event_photo_upload() : ", "Start Here ", false);

        $error = array();
        $error = photo_upload($xmlrequest);
        if (DEBUG) {
            writelog("event.class.php :: event_photo_upload() : ", $error, true);
            writelog("event.class.php :: event_photo_upload() : ", "End Here ", false);
        }
        return $error;
    }

    /*  function event_photo_upload_valid()
      Purpose    : ?
      Parameters : $xmlresponse  : ?
      Returns    : ? */

    function event_photo_upload_valid($xmlrequest) {
        if (DEBUG)
            writelog("event.class.php :: photo_upload_valid() : ", "Start Here ", false);

        $error = array();
        $error = photo_upload_valid($xmlrequest);
        if (DEBUG) {
            writelog("event.class.php :: photo_upload_valid() : ", $error, true);
            writelog("event.class.php :: photo_upload_valid() : ", "End Here ", false);
        }
        return $error;
    }

    /*  function delete_event_comment()
      Purpose    : to delete comment on event
      Parameters : $xmlresponse  : request array for delete event comment
      Returns    : successful message for successful deletion otherwise unsuccessful message */

    function delete_event_comment($xmlrequest) {

        $userId = mysql_real_escape_string($xmlrequest['DeleteEventComment']['userId']);
        $commentId = mysql_real_escape_string($xmlrequest['DeleteEventComment']['commentId']);

        $error = array();
//get bulletin id from event comment table
        $query_hotpress = "SELECT bullet_id FROM events_comments WHERE id='$commentId'";
        if (DEBUG)
            writelog("Events:delete_event_comment()", $query_hotpress, false);
        $result_hotpress = execute_query($query_hotpress, false, "select");

        if ((isset($result_hotpress['bullet_id'])) && ($result_hotpress['bullet_id'])) {
            $id = $result_hotpress['bullet_id'];
        } else {
            $id = 0;
        }
//for deleting comment from events comments table
        $query = "DELETE FROM events_comments WHERE (from_id='$userId' AND id='$commentId')||(parent_id='$commentId' AND parent_id>0)";
        if (DEBUG)
            writelog("Events:delete_event_comment()", $query, false);

        $result = execute_query($query, false, "delete");
        $affected_row = $result['count'];
        $error = error_CRUD($xmlrequest, $affected_row);

        if ((isset($error['DeleteEventComment']['successful_fin'])) && (!$error['DeleteEventComment']['successful_fin'])) {
            return $error;
        }

        if (($affected_row) && (isset($id)) && ($id)) {
//for deleting comment from hotpress
            $query_hotpress_del = "DELETE FROM bulletin WHERE (id='$id' AND mem_id='$userId')||(parentid='$id' AND parentid>0)"; //(parentid='$id') ||
            if (DEBUG)
                writelog("Events:delete_event_comment()", $query_hotpress_del, false);
            $result_hotpress_del = execute_query($query_hotpress_del, false, "delete");
            $affected_row_hotpress = $result_hotpress_del['count'];
            $error = error_CRUD($xmlrequest, $affected_row_hotpress);
            if ((isset($error['DeleteEventComment']['successful_fin'])) && (!$error['DeleteEventComment']['successful_fin'])) {
                if (DEBUG)
                    writelog("Events:delete_event_comment():", $error, true);
                return $error;
            }

            if (DEBUG) {
                writelog("Events:delete_event_comment():", $error, true);
                writelog("Events:delete_event_comment():", "End Here", false);
            }
        }

        return $error;
    }

    /*  function eventList()
      Purpose    : to delete comment on event
      Parameters : $xmlresponse       : request array for event list
      $response_message  : ?
      Returns    : Json formatted event list */

    function eventList($response_message, $xmlresponse) {

        global $return_codes;
        $pageNumber = $xmlresponse['Events']['pageNumber'];
        $EventListing = array();
        

        if (isset($xmlresponse['Events']['eventType']) && ($xmlresponse['Events']['eventType'] == "followed")) {
            $EventListing = $this->get_followed_event($xmlresponse, $pageNumber, 10);
        } else if (isset($xmlresponse['Events']['eventType']) && ($xmlresponse['Events']['eventType'] == "all")) {
            $EventListing = $this->get_all_event($xmlresponse, $pageNumber, 10);
			
        } else if (isset($xmlresponse['Events']['eventType']) && ($xmlresponse['Events']['eventType'] == "nearby")) {
            $EventListing = $this->get_nearby_event($xmlresponse, $pageNumber, 10);
            $range = true;
        } else if (isset($xmlresponse['Events']['eventType']) && ($xmlresponse['Events']['eventType'] == "calender")) {
            $EventListing = $this->get_calender_event($xmlresponse, $pageNumber, 10);
        } else {
            $EventListing['totalrecords'] = 0;
            $EventListing['count'] = 0;
			$EventListing['str'] = "";
        }

        $eventListingStr = "";
		$eventListingStr=isset($EventListing['str'])?$EventListing['str']:"";
        $counter = 0;
        $counter_total = 0;
		$counter=isset($EventListing['count'])?$EventListing['count']:0;
		$counter_total=isset($EventListing['totalrecords'])?$EventListing['totalrecords']:0;
      
        if (!empty($EventListing) && ($EventListing['count'] > 0)) {

            $eventListingStr = rtrim($eventListingStr, ',');
			$eventListingStr = ltrim($eventListingStr, ',');
            if ($xmlresponse['Events']['eventType'] == 'calender') {
                $counter_total = $EventListing['totalrecords'];
            }

            $response_mess = '
                    {
                ' . response_repeat_string() . '
                "Events":{
                "errorCode":"' . $return_codes['Events']['SuccessCode'] . '",
                "errorMsg":"' . $return_codes['Events']['SuccessDesc'] . '",
                "totalRecordsCount":"' . $counter_total . '",
                "currentListingCount":"' . $counter . '",
                "eventType":"' .str_replace('"', '\"',$xmlresponse['Events']['eventType']). '",
                "EventListing":[' . $eventListingStr . '],
                "pagenumber":"' . $pageNumber . '"
                }
                }';
        } else {
            $response_mess = '
                {
                ' . response_repeat_string() . '
                "Events":{
                "errorCode":"' . $return_codes['Events']['NoRecordErrorCode'] . '",
                "errorMsg":"' . $return_codes['Events']['NoRecordErrorDesc'] . '",
                "totalRecordsCount":"' . $counter . '",
                "currentListingCount":"' . $counter . '",
                "eventType":"' .str_replace('"', '\"',$xmlresponse['Events']['eventType']). '",
                "EventListing":[ ' . $eventListingStr . ' ],
                "pagenumber":"' . $pageNumber . '"
                }
                }';
        }
        return getValidJSON($response_mess);
    }

//end of events()

    /*  function searchEvent()
      Purpose    : to display the list of events searched by either title or city
      Parameters : $xmlresponse       : request array for search in events
      $response_message  : ?
      Returns    : Json formatted searched event list */

    function searchEvent($response_message, $xmlrequest) {

        global $return_codes;
        $pageNumber = $xmlrequest["SearchEvent"]["pageNumber"];
        $event = array();
        $event = $this->get_search_event($xmlrequest, $pageNumber, 10);

        $str_temp = "";
        $counter = 0;
        $range = true;

        if ($event['count'] > 0) {

			$str_temp=$event['str'];
			$counter=$event['totalrecords'];
			$count=$event['count'];
            $str_temp = rtrim($str_temp, ',');
            if (isset($event['count']) && ($event['count'] > 0) && ($counter)) {
                $response_mess = '
                {
                         ' . response_repeat_string() . '
                   "SearchEvent":{
                      "errorCode":"' . $return_codes['SearchEvent']['SuccessCode'] . '",
                      "errorMsg":"' . $return_codes['SearchEvent']['SuccessDesc'] . '",
                          "totalRecordsCount":"' . $counter . '",
                          "currentListingCount":"' . $count. '",
                      "pagenumber":"' . $pageNumber . '",
                      "EventsList":[' . $str_temp . ']
                    }
        }';
            }
        } else {
            $response_mess = '
                {
                 ' . response_repeat_string() . '
                          "SearchEvent":{
                  "errorCode":"' . $return_codes['SearchEvent']['NoRecordErrorCode'] . '",
                  "errorMsg":"' . $return_codes['SearchEvent']['NoRecordErrorDesc'] . '",
                  "totalRecordsCount":"' . $counter . '",
                  "currentListingCount":"' . $counter . '",
                   "SearchEventInfo":[' . $str_temp . ']
                      }
                }';
        }

        return getValidJSON($response_mess);
    }

    /*  function eventDetails()
      Purpose    : to display the detail information of event
      Parameters : $xmlresponse       : request array for event detail
      $response_message  : ?
      Returns    : Json formatted event detail */

    function eventDetails($response_message, $xmlrequest) {

        global $return_codes;
        $event = array();
        $obj_event = new Events();
        $event = $obj_event->get_event_Details($xmlrequest);

        $str_temp = "";
        if (is_array($event) && !empty($event)) {
            $event['even_desc'] = str_replace('\\', "", $event['even_desc']);
            $event['even_desc'] = str_replace(array("\r\n", "\r", "\n", "<br />", "<br/>"), "\\n", $event['even_desc']);
            $event['even_desc'] = strip_tags($event['even_desc']);
            $event['even_desc'] = str_replace(array("\"", "\'"), "", $event['even_desc']);

            $event['even_bdesc'] = str_replace('\\', "", $event['even_bdesc']);
            $event['even_bdesc'] = str_replace(array("\r\n", "\r", "\n", "<br />", "<br/>"), "\\n", $event['even_bdesc']);
            $event['even_bdesc'] = strip_tags($event['even_bdesc']);
            $event['even_bdesc'] = str_replace(array("\"", "\'"), "", $event['even_bdesc']);

            $event['even_addr'] = str_replace('\\', "", $event['even_addr']);
            $event['even_addr'] = str_replace(array("\r\n", "\r", "\n", "<br />", "<br/>"), "\\n", $event['even_addr']);
            $event['even_addr'] = strip_tags($event['even_addr']);
            $event['even_addr'] = str_replace(array("\"", "\'"), "", $event['even_addr']);
            $name = NULL;
            $event['fname'] = isset($event['fname']) && ($event['fname']) ? $event['fname'] : NULL;
            $event['lname'] = isset($event['lname']) && ($event['lname']) ? $event['lname'] : NULL;
            $name = $event['fname'] . ' ' . $event['lname'];
            $event['profilenam'] = isset($event['profilenam']) && ($event['profilenam']) ? $event['profilenam'] : (isset($name) && ($name) ? $name : NULL);

            $width_even_img = NULL;
            $height_even_img = NULL;
            if (is_readable($this->local_folder . $event['even_img'])) {
                list($width_even_img, $height_even_img) = (isset($event['even_img']) && (strlen($event['even_img']) > 7)) ? getimagesize($this->local_folder . $event['even_img']) : NULL;
                $sizee = getimagesize($this->local_folder . $event['even_img']);
                if (preg_match('/^image\/(jp[e]?g|png|gif)$/', $sizee['mime'])) {
                    $event['even_img'] = isset($event['even_img']) && (strlen($event['even_img']) > 7) ? event_image_detail($event['even_id'], $event['even_img'], 1) : NULL;

                    list($width_even_img, $height_even_img) = (isset($event['even_img']) && (strlen($event['even_img']) > 7)) ? getimagesize($this->local_folder . $event['even_img']) : NULL;
                }
            }
            $event['even_img'] = isset($event['even_img']) && (strlen($event['even_img']) > 7) ? $this->profile_url . $event['even_img'] : NULL;
            $event['even_phon'] = isset($event['even_phon']) && ($event['even_phon']) ? $event['even_phon'] : NULL;
            $event['profileimageurl'] = (isset($event['profileimageurl']) && (strlen($event['profileimageurl']) > 7)) ? $this->profile_url . $event['profileimageurl'] : $this->profile_url . default_images($event['gender'], $event['profile_type']);

            $str_temp = '{
                "eventId":"' . $event['even_id'] . '",
                "eventTitle":"' .str_replace('"', '\"',strtoupper(trim(preg_replace('/\s+/', ' ', $event['even_title'])))). '",
                "eventFullDescription":"' .str_replace('"', '\"',trim(preg_replace('/\s+/', ' ', $event['even_desc']))) . '",
                "eventImageUrl":"' .str_replace('"', '\"',($event['even_img'])). '",
                "eventDistance":"0.00",
                "eventOwnerUserID":"' . $event['mem_id'] . '",
                "eventOwnerName":"' .str_replace('"', '\"',$event['profilenam']). '",
                "eventOwnerProfileImageURL":"' .str_replace('"', '\"',$event['profileimageurl']). '",
                "eventGuestListExists":"' . (($event['guest'] == 'y') ? "true" : "false") . '",
                "eventPurchaseTicket":"' .str_replace('"', '\"',trim(urldecode($event['purchase_ticket']))). '",
                "eventDate":"' .str_replace('"', '\"',$event['actualdate']). '",
                "eventTime":"' .str_replace('"', '\"',$event['actualtime']). '",
                "eventLocation":"' .str_replace('"', '\"',trim($event['even_loc'])). '",
                "eventDescription":"' .str_replace('"', '\"',trim(preg_replace('/\s+/', ' ', $event['even_bdesc']))). '",
                "evenAddress":"' .str_replace('"', '\"',trim($event['even_addr'])). '",
                "eventAttendMinAge":"' .str_replace('"', '\"',$event['event_age']). '",
                "eventCity":"' .str_replace('"', '\"',trim($event['even_city'])) . '",
                "eventState":"' .str_replace('"', '\"',trim($event['even_state'])). '",
                "eventCountry":"' .str_replace('"', '\"',trim($event['even_country'])). '",
                "eventZip":"' .str_replace('"', '\"',$event['even_zip']). '",
                "eventContactNo":"' .str_replace('"', '\"',$event['even_phon']). '",
                "eventMusicType":"' .str_replace('"', '\"',$event['music']). '",
                "eventOrganizer":"' .str_replace('"', '\"',$event['even_org']). '",
                "eventType":"' .str_replace('"', '\"',$event['event_nam']). '"
                }';

            $event['addGuestList'] = isset($event['addGuestList']) ? $event['addGuestList'] : 0;
            $response_str = response_repeat_string();
            $response_mess = '
                {
                 ' . $response_str . '
                         "EventDetails":{ "errorCode":"' . $return_codes['EventDetails']['SuccessCode'] . '",
                         "errorMsg":"' . $return_codes['EventDetails']['SuccessDesc'] . '",
                         "addGuestList":"' .str_replace('"', '\"',$event['addGuestList']). '",
                         "EventInfo":[ ' . $str_temp . ' ]}
                }';
        } else {
            $response_mess = '
                {
                 ' . response_repeat_string() . '
                          "EventDetails":{
                  "errorCode":"' . $return_codes['EventDetails']['NoRecordErrorCode'] . '",
                  "errorMsg":"' . $return_codes['EventDetails']['NoRecordErrorDesc'] . '",
                   "EventInfo":[' . $str_temp . ']
                    }
                }';
        }
        return getValidJSON($response_mess);
    }

//end of eventDetails()

    /*  function eventCommentList()
      Purpose    : to display the comment list in event
      Parameters : $xmlresponse       : request array for event comment list
      $response_message  : ?
      Returns    : Json formatted event comment list */

    function eventCommentList($response_message, $xmlresponse) {

        global $return_codes;
        $event = array();
        $pageNumber = $xmlresponse['EventComments']['pageNumber'];
        $event = $this->event_comments_list($xmlresponse, $pageNumber, 20);
        $count = isset($event['count']) ? $event['count'] : NULL;
        $str = '';
        if ($count > 0) {
            $commentcount = 0;

            for ($i = 0; $i < $count; $i++) {
                $width_even_img = NULL;
                $height_even_img = NULL;
                $width_uploadedImage = NULL;
                $height_uploadedImage = NULL;
                if (is_readable($this->local_folder . $event[$i]['image_link'])) {
                    $sizee = getimagesize($this->local_folder . $event[$i]['image_link']);
                    $width_uploadedImage = $sizee[0];
                    $height_uploadedImage = $sizee[1];
                    $file_extension = substr($event[$i]['image_link'], strrpos($event[$i]['image_link'], '.') + 1);
                    $arr = explode('.', $event[$i]['image_link']);
                    $Id = isset($event[$i]['even_id']) && ($event[$i]['even_id']) ? $event[$i]['even_id'] : NULL;
                    if (!$Id)
                        $Id = isset($event[$i]['bullet_id']) && $event[$i]['bullet_id'] ? $event[$i]['bullet_id'] : NULL;

                    if (!file_exists($this->local_folder . $arr[0] . "_" . $Id . "." . $file_extension) && (preg_match('/^image\/(jp[e]?g|png|gif)$/', $sizee['mime']))) {
                        thumbanail_for_image($Id, $event[$i]['image_link']);
                    }
                    if (preg_match('/^image\/(jp[e]?g|png|gif)$/', $sizee['mime'])) {
                        $event[$i]['image_link'] = isset($event[$i]['image_link']) && (strlen($event[$i]['image_link']) > 7) ? event_image_detail($Id, $event[$i]['image_link'], 1) : NULL;
                        list($width_uploadedImage, $height_uploadedImage) = (isset($event[$i]['image_link']) && (strlen($event[$i]['image_link']) > 7)) ? getimagesize($this->local_folder . $event[$i]['image_link']) : NULL;
                    }
                }
                if (is_readable($this->local_folder . $event[$i]['link_image'])) {
                    $sizee = getimagesize($this->local_folder . $event[$i]['link_image']);
                    $width_even_img = $sizee[0];
                    $height_even_img = $sizee[1];
                    $file_extension = substr($event[$i]['link_image'], strrpos($event[$i]['link_image'], '.') + 1);
                    $arr = explode('.', $event[$i]['link_image']);
                    $Id = isset($event[$i]['even_id']) && ($event[$i]['even_id']) ? $event[$i]['even_id'] : NULL;
                    if (!$Id)
                        $Id = isset($event[$i]['bullet_id']) && $event[$i]['bullet_id'] ? $event[$i]['bullet_id'] : NULL;

                    if (!file_exists($this->local_folder . $arr[0] . "_" . $Id . "." . $file_extension) && (preg_match('/^image\/(jp[e]?g|png|gif)$/', $sizee['mime']))) {
                        thumbanail_for_image($Id, $event[$i]['link_image']);
                    }
                    if (preg_match('/^image\/(jp[e]?g|png|gif)$/', $sizee['mime'])) {
                        $event[$i]['link_image'] = isset($event[$i]['link_image']) && (strlen($event[$i]['link_image']) > 7) ? event_image_detail($Id, $event[$i]['link_image'], 1) : NULL;

                        list($width_even_img, $height_even_img) = (isset($event[$i]['link_image']) && (strlen($event[$i]['link_image']) > 7)) ? getimagesize($this->local_folder . $event[$i]['link_image']) : NULL;
                    }
                }

                $event[$i]['comment'] = str_replace('"', '\"', $event[$i]['comment']);
                $event[$i]['profile_type'] = (isset($event[$i]['profile_type']) && ($event[$i]['profile_type'])) ? $event[$i]['profile_type'] : NULL;
                $event[$i]['gender'] = (isset($event[$i]['gender']) && ($event[$i]['gender'])) ? $event[$i]['gender'] : NULL;
                $event[$i]['photo_b_thumb'] = isset($event[$i]['is_facebook_user']) && (strlen($event[$i]['photo_b_thumb']) > 7) && ($event[$i]['is_facebook_user'] == 'y' || $event[$i]['is_facebook_user'] == 'Y') ? $event[$i]['photo_b_thumb'] : ((isset($event[$i]['photo_b_thumb']) && (strlen($event[$i]['photo_b_thumb']) > 7)) ? $this->profile_url . $event[$i]['photo_b_thumb'] : $this->profile_url . default_images1($event[$i]['gender'], $event[$i]['profile_type']));
//$event[$i]['photo_b_thumb'] = (isset($event[$i]['photo_b_thumb']) && (strlen($event[$i]['photo_b_thumb']) > 7)) ? $this->profile_url . $event[$i]['photo_b_thumb'] : $this->profile_url . default_images($event[$i]['gender'], $event[$i]['profile_type']);
                $event[$i]['mem_id'] = (isset($event[$i]['mem_id']) && ($event[$i]['mem_id'])) ? $event[$i]['mem_id'] : NULL;
                $event[$i]['profilenam'] = (isset($event[$i]['profilenam']) && ($event[$i]['profilenam'])) ? $event[$i]['profilenam'] : NULL;
                $event[$i]['album_id'] = (isset($event[$i]['album_id']) && ($event[$i]['album_id'])) ? $event[$i]['album_id'] : NULL;
                $event[$i]['link_image'] = (isset($event[$i]['link_image']) && (strlen($event[$i]['link_image']) > 7)) ? $this->profile_url . $event[$i]['link_image'] : NULL;
                $event[$i]['link_url'] = (isset($event[$i]['link_url']) && (strlen($event[$i]['link_url']) > 7)) ? $event[$i]['link_url'] : NULL;
                $event[$i]['image_link'] = (isset($event[$i]['image_link']) && (strlen($event[$i]['image_link']) > 7)) ? $this->profile_url . $event[$i]['image_link'] : NULL;
                $input = $event[$i]['comment'];
                $input = str_replace('\\', '', $input);
                if (preg_match(REGEX_URL, $input, $url)) {
                    $postType = extract_url($input);
                    $postType = strip_tags($postType);
                    $postType = str_replace(array("\r\n", "\r", "\n", "<br />", "\"", "\<a"), "\\n", $postType);
                } else {
                    $postType = 'text';
                }

                 /* commented on 25 nov 2011 :: aarya ::  $event[$i]['comment'] = isset($event[$i]['comment']) && ($event[$i]['comment']) ? trim(str_replace($url, '\n\n', $event[$i]['comment'])) : NULL;
				
                $event[$i]['comment'] = str_replace(array("\r\n", "\r", "\n", "<br />", "<br/>"), "\\n", $event[$i]['comment']);
                $event[$i]['comment'] = strip_tags($event[$i]['comment']);
                $event[$i]['comment'] = str_replace(array("\""), "", $event[$i]['comment']);
                $event[$i]['comment'] = subanchor($event[$i]['comment']); */
				
				/* Added below line on 25 Nov 2011 :: aarya */ 
				$event[$i]['comment']=get_organized_comment_data($event[$i]['comment'],NULL);
				
                if (isset($event[$i]['post_via']) && ($event[$i]['post_via'])) {
                    $post_via = 'iPhone';
                } else {
                    $post_via = NULL;
                }
                $str_temp = '{
            "commentId":"' .str_replace('"', '\"',$event[$i]['id']). '",
            "authorID":"' .str_replace('"', '\"',$event[$i]['mem_id']). '",
            "authorProfileImgURL":"' .str_replace('"', '\"',$event[$i]['photo_b_thumb']). '",
            "authorName":"' .str_replace('"', '\"',trim($event[$i]['profilenam'])) . '",
            "commentText":"' .str_replace('"', '\"',preg_replace('/\s+/', ' ', $event[$i]['comment'])). '",
            "postType":"' .str_replace('"', '\"',$postType). '",
            "authorProfile_type":"' .str_replace('"', '\"',$event[$i]['profile_type']) . '",
            "authorGender":"' .str_replace('"', '\"',$event[$i]['gender']). '",
            "commentTimestamp":"' .str_replace('"', '\"',time_difference($event[$i]['date'])). '",
            "photoId": "' .str_replace('"', '\"',$event[$i]['photo_album_id']). '",
            "albumId": "' .str_replace('"', '\"',$event[$i]['album_id']). '",
            "uploadedImage": "' .str_replace('"', '\"',$event[$i]['image_link']). '",
            "width_uploadedImage":"' .str_replace('"', '\"',$width_uploadedImage). '",
            "height_uploadedImage":"' .str_replace('"', '\"',$height_uploadedImage). '",
            "link_url": "' .str_replace('"', '\"',$event[$i]['link_url']). '",
            "youtubeLink": "' .str_replace('"', '\"',$event[$i]['youtubeLink']). '",
            "link_image": "' .str_replace('"', '\"',$event[$i]['link_image']). '",
            "width_link_image":"' .str_replace('"', '\"',$width_even_img). '",
            "height_link_image":"' .str_replace('"', '\"',$height_even_img). '",
            "postVia":"' .str_replace('"', '\"',$post_via). '",
            "commentsCount": "' .str_replace('"', '\"',$event[$i]['totalCommentCount']). '"

        }';
                $commentcount++;
                $str .= $str_temp;
                $str .= ',';
            }
            $str = substr($str, 0, strlen($str) - 1);

            $response_str = response_repeat_string();
            $response_mess = '
            {
               ' . $response_str . '
               "EventComments":{
                  "errorCode":"' . $return_codes["EventComments"]["SuccessCode"] . '",
                  "errorMsg":"' . $return_codes["EventComments"]["SuccessDesc"] . '",
                  "commentCount":"' . $commentcount . '",
                  "totalcommentCount":"' . $event['total'] . '",
                  "comments":[' . $str . ']
                }
            }';
        } else {
            $response_mess = '
                {
                 ' . response_repeat_string() . '
                          "EventComments":{
                  "errorCode":"' . $return_codes["EventComments"]["FailedToAddRecordCode"] . '",
                  "errorMsg":"' . $return_codes["EventComments"]["FailedToAddRecordDesc"] . '",
                   "comments":[' . $str . ']
                   }
                }';
        }

        return getValidJSON($response_mess);
    }

//end of eventCommentList()

    /*  function eventPostComment()
      Purpose    : to post the comment on event
      Parameters : $xmlresponse       : request array for event post comment
      $response_message  : ?
      Returns    : Json formatted event post comment */

    function eventPostComment($response_message, $xmlresponse) {

        global $return_codes;
        $event = array();
        $event = $this->event_post_comments($xmlresponse);
        if (!empty($event)) {
            $response_str = response_repeat_string();
            $response_mess = '
        {
           ' . $response_str . '
           "EventPostComment":{
           "commentId":"' . $event . '",
              "errorCode":"' . $return_codes["EventPostComment"]["SuccessCode"] . '",
              "errorMsg":"' . $return_codes["EventPostComment"]["SuccessDesc"] . '"
            }
        }';
        } else {
            $response_mess = '
                {
                 ' . response_repeat_string() . '
                          "EventPostComment":{
                  "errorCode":"' . $return_codes["EventPostComment"]["FailedToAddRecordCode"] . '",
                  "errorMsg":"' . $return_codes["EventPostComment"]["FailedToAddRecordDesc"] . '"
                   }
                }';
        }
        return getValidJSON($response_mess);
    }

    /*  function EventParentChildComments()
      Purpose    : to display parent child comment on event
      Parameters : $xmlresponse       : request array for event parent child comment
      $response_message  : ?
      Returns    : Json formatted event parent child comment */

    public

    function EventParentChildComments($response_message, $xmlresponse) {

        global $return_codes;
        $eventComment = array();
	$pageNumber = $xmlresponse['EventParentChildComment']['pageNumber'];
        $eventParentChildCommentList = $this->display_parent_child_comments($xmlresponse,$pageNumber,20);
        if (!empty($eventParentChildCommentList)) {
            $count = $eventParentChildCommentList[1]['child']['count'];

            $width_even_img = NULL;
            $height_even_img = NULL;
            $strChild = '';
            if (is_readable($this->local_folder . $eventParentChildCommentList[0]['parent']['link_image'])) {
                list($width_even_img, $height_even_img) = (isset($eventParentChildCommentList[0]['parent']['link_image']) && (strlen($eventParentChildCommentList[0]['parent']['link_image']) > 7)) ? getimagesize($this->local_folder . $eventParentChildCommentList[0]['parent']['link_image']) : NULL;
            }
            if (isset($eventParentChildCommentList[0]['parent']['post_via']) && ($eventParentChildCommentList[0]['parent']['post_via'])) {
                $post_via = 'iPhone';
            } else {
                $post_via = NULL;
            }
            if (is_readable($this->local_folder . $eventParentChildCommentList[0]['parent']['parentPhotoInfo']['parentPhotoMid'])) {
                $sizee = getimagesize($this->local_folder . $eventParentChildCommentList[0]['parent']['parentPhotoInfo']['parentPhotoMid']);
                $width_parentPhotoMid = $sizee[0];
                $height_parentPhotoMid = $sizee[1];

                $file_extension = substr($eventParentChildCommentList[0]['parent']['parentPhotoInfo']['parentPhotoMid'], strrpos($eventParentChildCommentList[0]['parent']['parentPhotoInfo']['parentPhotoMid'], '.') + 1);
                $arr = explode('.', $eventParentChildCommentList[0]['parent']['id']);
                $Id = isset($eventParentChildCommentList[0]['parent']['id']) && ($eventParentChildCommentList[0]['parent']['id']) ? $eventParentChildCommentList[0]['parent']['id'] : NULL;
                if (!$Id)
                    $Id = isset($eventParentChildCommentList[0]['parent']['id']) && $eventParentChildCommentList[0]['parent']['id'] ? $eventParentChildCommentList[0]['parent']['id'] : NULL;

                if (!file_exists($this->local_folder . $arr[0] . "_" . $Id . "." . $file_extension) && (preg_match('/^image\/(jp[e]?g|png|gif)$/', $sizee['mime']))) {
                    thumbanail_for_image($Id, $eventParentChildCommentList[0]['parent']['parentPhotoInfo']['parentPhotoMid']);
                }
                if (preg_match('/^image\/(jp[e]?g|png|gif)$/', $sizee['mime'])) {
                    $eventParentChildCommentList[0]['parent']['parentPhotoInfo']['parentPhotoMid'] = isset($eventParentChildCommentList[0]['parent']['parentPhotoInfo']['parentPhotoMid']) && (strlen($eventParentChildCommentList[0]['parent']['parentPhotoInfo']['parentPhotoMid']) > 7) ? event_image_detail($Id, $eventParentChildCommentList[0]['parent']['parentPhotoInfo']['parentPhotoMid'], 1) : NULL;

                    list($width_parentPhotoMid, $height_parentPhotoMid) = (isset($eventParentChildCommentList[0]['parent']['parentPhotoInfo']['parentPhotoMid']) && (strlen($eventParentChildCommentList[0]['parent']['parentPhotoInfo']['parentPhotoMid']) > 7)) ? getimagesize($this->local_folder . $eventParentChildCommentList[0]['parent']['parentPhotoInfo']['parentPhotoMid']) : NULL;
                }
            }
            $eventParentChildCommentList[0]['parent']['link_image'] = isset($eventParentChildCommentList[0]['parent']['link_image']) && (strlen($eventParentChildCommentList[0]['parent']['link_image']) > 7) ? $this->profile_url . $eventParentChildCommentList[0]['parent']['link_image'] : NULL;
            $eventParentChildCommentList[0]['parent']['parentPhotoInfo']['parentPhotoMid'] = isset($eventParentChildCommentList[0]['parent']['parentPhotoInfo']['parentPhotoMid']) && (strlen($eventParentChildCommentList[0]['parent']['parentPhotoInfo']['parentPhotoMid']) > 7) ? $this->profile_url . $eventParentChildCommentList[0]['parent']['parentPhotoInfo']['parentPhotoMid'] : NULL;
            $eventParentChildCommentList[0]['parent']['photo_b_thumb'] = (isset($eventParentChildCommentList[0]['parent']['photo_b_thumb']) && (strlen($eventParentChildCommentList[0]['parent']['photo_b_thumb']) > 7)) ? $this->profile_url . $eventParentChildCommentList[0]['parent']['photo_b_thumb'] : $this->profile_url . default_images($eventParentChildCommentList[0]['gender'], $eventParentChildCommentList[0]['parent']['profile_type']);
//	    $eventParentChildCommentList[0]['parent']['photo_b_thumb'] = isset($eventParentChildCommentList[0]['parent']['photo_b_thumb']) && (strlen($eventParentChildCommentList[0]['parent']['photo_b_thumb']) > 7) ? $this->profile_url . $eventParentChildCommentList[0]['parent']['photo_b_thumb'] : NULL;
            $input = $eventParentChildCommentList[0]['parent']['comment'];
            $input = str_replace('\\', '', $input);
            if (preg_match(REGEX_URL, $input, $url)) {
                $postType = extract_url($input);
                $postType = strip_tags($postType);
                $postType = str_replace(array("\r\n", "\r", "\n", "<br />", "\""), "\\n", $postType);
            } else {
                $postType = 'text';
            }
            $eventParentChildCommentList[0]['parent']['comment'] = str_replace('\\', "", $eventParentChildCommentList[0]['parent']['comment']);
            $eventParentChildCommentList[0]['parent']['comment'] = str_replace(array("\r\n", "\r", "\n", "<br />", "<br/>"), "\\n", $eventParentChildCommentList[0]['parent']['comment']);
            $eventParentChildCommentList[0]['parent']['comment'] = strip_tags($eventParentChildCommentList[0]['parent']['comment']);
            $eventParentChildCommentList[0]['parent']['comment'] = str_replace(array("\""), "", $eventParentChildCommentList[0]['parent']['comment']);
            $eventParentChildCommentList[0]['parent']['comment'] = subanchor($eventParentChildCommentList[0]['parent']['comment']);
            $parentStr = '{
            "commentId":"' .str_replace('"', '\"',$eventParentChildCommentList[0]['parent']['id']). '",
            "authorID":"' .str_replace('"', '\"',$eventParentChildCommentList[0]['parent']['mem_id']). '",
            "authorProfileImgURL":"' .str_replace('"', '\"',$eventParentChildCommentList[0]['parent']['photo_b_thumb']). '",
            "authorName":"' .str_replace('"', '\"',$eventParentChildCommentList[0]['parent']['profilenam']). '",
            "commentText":"' . str_replace('"', '\"',trim(preg_replace('/\s+/', ' ', $eventParentChildCommentList[0]['parent']['comment']))) . '",
            "postType":"' .str_replace('"', '\"',$postType). '",
            "authorProfile_type":"' .str_replace('"', '\"',$eventParentChildCommentList[0]['parent']['profile_type']). '",
            "authorGender":"' .str_replace('"', '\"',$eventParentChildCommentList[0]['parent']['gender']). '",
            "commentTimestamp":"' .str_replace('"', '\"',time_difference($eventParentChildCommentList[0]['parent']['date'])). '",
            "photoId": "' .str_replace('"', '\"',$eventParentChildCommentList[0]['parent']['parentPhotoInfo']['parentPhotoId']) . '",
            "albumId": "' .str_replace('"', '\"',$eventParentChildCommentList[0]['parent']['parentPhotoInfo']['parentAlbumId']). '",
            "uploadedImage": "' .str_replace('"', '\"',$eventParentChildCommentList[0]['parent']['parentPhotoInfo']['parentPhotoMid']). '",
            "widthUploadedImage":"' .str_replace('"', '\"',$width_parentPhotoMid). '",
            "heightUploadedImage":"' .str_replace('"', '\"',$height_parentPhotoMid). '",
            "link_url": "' .str_replace('"', '\"',$eventParentChildCommentList[0]['parent']['link_url']). '",
            "youtubeLink": "' .str_replace('"', '\"',$eventParentChildCommentList[0]['parent']['youtubeLink']). '",
            "link_image": "' .str_replace('"', '\"',$eventParentChildCommentList[0]['parent']['link_image']). '",
            "width":"' .str_replace('"', '\"',$width_even_img). '",
            "height":"' .str_replace('"', '\"',$height_even_img). '",
            "postVia":"' .str_replace('"', '\"',$post_via). '",
            "commentsCount": "' .str_replace('"', '\"',$eventParentChildCommentList[0]['parent']['totalCommentCount']). '"
            "currentCommentsCount": "' .str_replace('"', '\"',$eventParentChildCommentList[0]['parent']['currentCommentCount']). '"
            }';

            for ($i = 0; $i < $count; $i++) {
                if (isset($eventParentChildCommentList[1]['child'][$i]['post_via']) && ($eventParentChildCommentList[1]['child'][$i]['post_via'])) {
                    $post_via = 'iPhone';
                } else {
                    $post_via = NULL;
                }
                if (is_readable($this->local_folder . $eventParentChildCommentList[1]['child'][$i]['childPhotoInfo']['childPhotoMid'])) {
                    list($width_childPhotoMid, $height_childPhotoMid) = isset($eventParentChildCommentList[1]['child'][$i]['childPhotoInfo']['childPhotoMid']) && (strlen($eventParentChildCommentList[1]['child'][$i]['childPhotoInfo']['childPhotoMid']) > 7) ? $this->local_folder . $eventParentChildCommentList[1]['child'][$i]['childPhotoInfo']['childPhotoMid'] : NULL;
                }
                $eventParentChildCommentList[1]['child'][$i]['childPhotoInfo']['childPhotoMid'] = isset($eventParentChildCommentList[1]['child'][$i]['childPhotoInfo']['childPhotoMid']) && (strlen($eventParentChildCommentList[1]['child'][$i]['childPhotoInfo']['childPhotoMid']) > 7) ? $this->profile_url . $eventParentChildCommentList[1]['child'][$i]['childPhotoInfo']['childPhotoMid'] : NULL;
                $eventParentChildCommentList[1]['child'][$i]['photo_b_thumb'] = (isset($eventParentChildCommentList[1]['child'][$i]['photo_b_thumb']) && (strlen($eventParentChildCommentList[1]['child'][$i]['photo_b_thumb']) > 7)) ? $this->profile_url . $eventParentChildCommentList[1]['child'][$i]['photo_b_thumb'] : $this->profile_url . default_images($eventParentChildCommentList[1]['child'][$i]['gender'], $eventParentChildCommentList[1]['child'][$i]['profile_type']);
//		$eventParentChildCommentList[1]['child'][$i]['photo_b_thumb'] = isset($eventParentChildCommentList[1]['child'][$i]['photo_b_thumb']) && (strlen($eventParentChildCommentList[1]['child'][$i]['photo_b_thumb']) > 7) ? $this->profile_url . $eventParentChildCommentList[1]['child'][$i]['photo_b_thumb'] : NULL;
                $eventParentChildCommentList[1]['child'][$i]['childPhotoInfo']['childPhotoId'] = isset($eventParentChildCommentList[1]['child'][$i]['childPhotoInfo']['childPhotoId']) && ($eventParentChildCommentList[1]['child'][$i]['childPhotoInfo']['childPhotoId']) ? $eventParentChildCommentList[1]['child'][$i]['childPhotoInfo']['childPhotoId'] : NULL;
                $eventParentChildCommentList[1]['child'][$i]['childPhotoInfo']['childAlbumId'] = isset($eventParentChildCommentList[1]['child'][$i]['childPhotoInfo']['childAlbumId']) && ($eventParentChildCommentList[1]['child'][$i]['childPhotoInfo']['childAlbumId']) ? $eventParentChildCommentList[1]['child'][$i]['childPhotoInfo']['childAlbumId'] : NULL;
                $eventParentChildCommentList[1]['child'][$i]['childPhotoInfo']['childPhotoMid'] = isset($eventParentChildCommentList[1]['child'][$i]['childPhotoInfo']['childPhotoMid']) && ($eventParentChildCommentList[1]['child'][$i]['childPhotoInfo']['childPhotoMid']) ? $eventParentChildCommentList[1]['child'][$i]['childPhotoInfo']['childPhotoMid'] : NULL;
                $input = $eventParentChildCommentList[1]['child'][$i]['comment'];
                $input = str_replace('\\', '', $input);
                if (preg_match(REGEX_URL, $input, $url)) {
                    $postType = extract_url($input);
                    $postType = strip_tags($postType);
                    $postType = str_replace(array("\r\n", "\r", "\n", "<br />", "\""), "\\n", $postType);
                } else {
                    $postType = 'text';
                }
                $eventParentChildCommentList[1]['child'][$i]['comment'] = str_replace('\\', "", $eventParentChildCommentList[1]['child'][$i]['comment']);
                $eventParentChildCommentList[1]['child'][$i]['comment'] = str_replace(array("\r\n", "\r", "\n", "<br />", "<br/>"), "\\n", $eventParentChildCommentList[1]['child'][$i]['comment']);
                $eventParentChildCommentList[1]['child'][$i]['comment'] = strip_tags($eventParentChildCommentList[1]['child'][$i]['comment']);
                $eventParentChildCommentList[1]['child'][$i]['comment'] = str_replace(array("\""), "", $eventParentChildCommentList[1]['child'][$i]['comment']);
                $eventParentChildCommentList[1]['child'][$i]['comment'] = subanchor($eventParentChildCommentList[1]['child'][$i]['comment']);

                $str_temp1 = '{
           "commentId":"' .str_replace('"', '\"',$eventParentChildCommentList[1]['child'][$i]['id']). '",
            "authorID":"' .str_replace('"', '\"',$eventParentChildCommentList[1]['child'][$i]['mem_id']). '",
            "authorProfileImgURL":"' .str_replace('"', '\"',$eventParentChildCommentList[1]['child'][$i]['photo_b_thumb']). '",
            "authorName":"' .str_replace('"', '\"',$eventParentChildCommentList[1]['child'][$i]['profilenam']). '",
            "commentText":"' .str_replace('"', '\"',trim(preg_replace('/\s+/', ' ', $eventParentChildCommentList[1]['child'][$i]['comment']))). '",
            "postType":"' .str_replace('"', '\"',$postType). '",
            "authorProfile_type":"' .str_replace('"', '\"',$eventParentChildCommentList[1]['child'][$i]['profile_type']). '",
            "authorGender":"' .str_replace('"', '\"',$eventParentChildCommentList[1]['child'][$i]['gender']). '",
            "commentTimestamp":"' .time_difference($eventParentChildCommentList[1]['child'][$i]['date']) . '",
            "photoId": "' .str_replace('"', '\"',$eventParentChildCommentList[1]['child'][$i]['childPhotoInfo']['childPhotoId']). '",
            "albumId": "' .str_replace('"', '\"',$eventParentChildCommentList[1]['child'][$i]['childPhotoInfo']['childAlbumId']). '",
            "uploadedImage": "' .str_replace('"', '\"',$eventParentChildCommentList[1]['child'][$i]['childPhotoInfo']['childPhotoMid']). '",
            "widthUploadedImage":"' .str_replace('"', '\"',$width_childPhotoMid). '",
            "heightUploadedImage":"' .str_replace('"', '\"',$height_childPhotoMid). '",
            "link_url": "' .str_replace('"', '\"',$eventParentChildCommentList[1]['child'][$i]['link_url']). '",
            "youtubeLink": "' .str_replace('"', '\"',$eventParentChildCommentList[1]['child'][$i]['youtubeLink']). '",
            "link_image": "' .str_replace('"', '\"',$eventParentChildCommentList[1]['child'][$i]['link_image']). '",
            "postVia":"' .str_replace('"', '\"',$post_via). '"
            }';

                $strChild .= $str_temp1;
                $strChild .=',';
            }
            $strChild = substr($strChild, 0, strlen($strChild) - 1);

            $response_str = response_repeat_string();
            $response_mess = '
                {
                   ' . $response_str . '
                   "EventParentChildComment":{
                        "errorCode":"' . $return_codes["EventParentChildComment"]["SuccessCode"] . '",
                        "errorMsg":"' . $return_codes["EventParentChildComment"]["SuccessDesc"] . '",
                        "parent":' . $parentStr . ',
                        "child":[' . $strChild . ']
                   }
                }';
        } else {
            $response_mess = '
                    {
       ' . response_repeat_string() . '
       "EventParentChildComment":{
          "errorCode":"' . $return_codes["EventParentChildComment"]["FailedToAddRecordCode"] . '",
          "errorMsg":"' . $return_codes["EventParentChildComment"]["FailedToAddRecordDesc"] . '",
       }
              }';
        }

        return getValidJSON($response_mess);
    }

    /*  function eventReplyComment()
      Purpose    : to reply on event comment
      Parameters : $xmlresponse       : request array for event reply comment
      $response_message  : ?
      Returns    : Json formatted event reply comment */

    function eventReplyComment($response_message, $xmlresponse) {

        global $return_codes;
        $eventComment = array();
        $event = $this->event_reply_comments($xmlresponse);
        if (!empty($event)) {
            $response_str = response_repeat_string();
            $response_mess = '
            {
               ' . $response_str . '
               "EventReplyComment":{
               "commentId":"' . $event . '",
                  "errorCode":"' . $return_codes["EventReplyComment"]["SuccessCode"] . '",
                  "errorMsg":"' . $return_codes["EventReplyComment"]["SuccessDesc"] . '"
                }
            }';
        } else {
            $response_mess = '
                {
                 ' . response_repeat_string() . '
                          "EventReplyComment":{
                  "errorCode":"' . $return_codes["EventReplyComment"]["FailedToAddRecordCode"] . '",
                  "errorMsg":"' . $return_codes["EventReplyComment"]["FailedToAddRecordDesc"] . '"
                }
             }';
        }

        return getValidJSON($response_mess);
    }

    /*  function eventCommentDelete()
      Purpose    : to delete event comment
      Parameters : $xmlresponse       : request array for event delete comment
      $response_message  : ?
      Returns    : Json formatted event delete comment */

    function eventCommentDelete($response_message, $xmlresponse) {

        global $return_codes;
        $event = array();
        $event = $this->delete_event_comment($xmlresponse);
        if (!empty($event)) {

            $response_str = response_repeat_string();
            $response_mess = '
        {
           ' . $response_str . '
           "EventCommentDelete":{
           "commentId":"' . $event . '",
              "errorCode":"' . $return_codes["EventCommentDelete"]["SuccessCode"] . '",
              "errorMsg":"' . $return_codes["EventCommentDelete"]["SuccessDesc"] . '"
            }
        }';
        } else {
            $response_mess = '
                {
                 ' . response_repeat_string() . '
                          "EventCommentDelete":{
                  "errorCode":"' . $return_codes["EventCommentDelete"]["FailedToAddRecordCode"] . '",
                  "errorMsg":"' . $return_codes["EventCommentDelete"]["FailedToAddRecordDesc"] . '"
                   }
                }';
        }
        return getValidJSON($response_mess);
    }

//end of eventPostComment()

    /*  function eventViewGuestList()
      Purpose    : to view event comment guest list
      Parameters : $xmlresponse       : request array to view event guest list
      $response_message  : ?
      Returns    : Json formatted event guest list */

    function eventViewGuestList($response_message, $xmlrequest) {

        global $return_codes;
        $event = array();
        $str_temp = '';
        $event = $this->event_view_guest_list($xmlrequest);
        if (($event != FALSE) && ($event['count'] > 0) && (is_array($event))) {
            for ($i = 0; $i < $event['count']; $i++) {
                if ($event[$i]['attend'] == 1) {
                    $attend = 'Yes';
                } elseif ($event[$i]['attend'] == 0) {
                    $attend = 'No';
                } elseif ($event[$i]['attend'] == 2) {
                    $attend = 'May Be';
                } else {
                    $attend = '';
                }
//                $event[$i]['photo_thumb'] = (isset($event[$i]['photo_thumb']) && (strlen($event[$i]['photo_thumb']) > 7)) ? $this->profile_url . $event[$i]['photo_thumb'] : $this->profile_url . default_images($event[$i]['gender'], $event[$i]['profile_type']);
                $name = NULL;
                $event[$i]['fname'] = isset($event[$i]['fname']) && ($event[$i]['fname']) ? $event[$i]['fname'] : NULL;
                $event[$i]['lname'] = isset($event[$i]['lname']) && ($event[$i]['lname']) ? $event[$i]['lname'] : NULL;
//                $name = $event[$i]['fname'] . ' ' . $event[$i]['lname'];
                $event[$i]['profilenam'] = isset($event[$i]['profilenam']) && ($event[$i]['profilenam']) ? $event[$i]['profilenam'] : (isset($name) && ($name) ? $name : NULL);
                $event[$i]['photo_thumb'] = (isset($event[$i]['photo_thumb']) && (strlen($event[$i]['photo_thumb']) > 7)) ? $this->profile_url . $event[$i]['photo_thumb'] : $this->profile_url . default_images($event[$i]['gender'], $event[$i]['profile_type']);

                $str_temp .= '{
                    "userId":"' . $event[$i]['profileid'] . '",
                    "userName":"' . $event[$i]['profilenam'] . '",
                    "profileImageUrl":"' . $event[$i]['photo_thumb'] . '",
                    "guestAttendStatus":"' . $attend . '",
                    "guestInNumber":"' . $event[$i]['no_of_guests'] . '"
               }';
                $str_temp .= ',';
            }
            $str_temp = substr($str_temp, 0, strlen($str_temp) - 1);
            $response_mess = '
                    {
               ' . response_repeat_string() . '
               "EventViewGuestList":{
                      "errorCode":"' . $return_codes['EventViewGuestList']['SuccessCode'] . '",
                      "errorMsg":"' . $return_codes['EventViewGuestList']['SuccessDesc'] . '",
                       "GuestCount":"' . $event['count'] . '",
                       "GuestListInfo":[' . $str_temp . ']
                     }

                    }';
        } else {
            $response_mess = '
                    {
                     ' . response_repeat_string() . '
                              "EventViewGuestList":{
                      "errorCode":"' . $return_codes['EventViewGuestList']['NoRecordErrorCode'] . '",
                      "errorMsg":"' . $return_codes['EventViewGuestList']['NoRecordErrorDesc'] . '"
                       "GuestListInfo":[' . $str_temp . ']
                      }
                    }';
        }
        return getValidJSON($response_mess);
    }

    /*  function eventAddGuestList()
      Purpose    : to user add in event comment guest list
      Parameters : $xmlresponse       : request array to view event guest list
      $response_message  : ?
      Returns    : Json formatted add in event guest list */

    function eventAddGuestList($response_message, $xmlrequest) {
        global $return_codes;
        $event = array();

        $event = $this->event_add_guest_list($xmlrequest);

        $sts = '';

        if (!empty($event) && is_array($event)) {

            $response_mess = '
                        {
                         ' . response_repeat_string() . '
                                  "EventAddGuestList":{
                          "errorCode":"' . $return_codes['EventAddGuestList']['SuccessCode'] . '",
                          "errorMsg":"' . $return_codes['EventAddGuestList']['SuccessDesc'] . '"
                                  }

                        }';
        } else {
            $response_mess = '
                        {
                         ' . response_repeat_string() . '
                                  "EventAddGuestList":{
                          ' . $sts . '
                  }
           }';
        }
        return getValidJSON($response_mess);
    }

    /*  function eventRemoveGuestList()
      Purpose    : to remove user from event comment guest list
      Parameters : $xmlresponse       : request array to view event guest list
      $response_message  : ?
      Returns    : Json formatted response for remove guest from event guest list */

    function eventRemoveGuestList($response_message, $xmlrequest) {

        global $return_codes;
        $event = array();
        $event = $this->event_remove_guest_list($xmlrequest);
        if ($event['count'] > 0) {
            $response_mess = '
                {
                 ' . response_repeat_string() . '
                          "EventRemoveGuestList":{
                  "errorCode":"' . $return_codes['EventRemoveGuestList']['SuccessCode'] . '",
                  "errorMsg":"' . $return_codes['EventRemoveGuestList']['SuccessDesc'] . '"
                          }

                }';
        } else {
            $response_mess = '
                {
                 ' . response_repeat_string() . '
                          "EventRemoveGuestList":{
                  "errorCode":"' . $return_codes['EventRemoveGuestList']['FailedToAddRecordCode'] . '",
                  "errorMsg":"' . $return_codes['EventRemoveGuestList']['FailedToAddRecordDesc'] . '"
                 }
           }';
        }
        return getValidJSON($response_mess);
    }

    /*  function eventSharing()
      Purpose    : ?
      Parameters : $xmlresponse       : request array ?
      $response_message  : ?
      Returns    : Json formatted response ? */

    function eventSharing($response_message, $xmlrequest) {

        if (isset($response_message['eventSharing']['SuccessCode']) && ( $response_message['eventSharing']['SuccessCode'] == '000')) {
            $userinfo = array();
            $userinfo = $this->event_sharing($xmlrequest);
            if ((isset($userinfo['eventSharing']['successful_fin'])) && (!$userinfo['eventSharing']['successful_fin'])) {
                $obj_error = new Error ();
                $response_message = $obj_error->error_type("eventSharing", $userinfo);

                $userinfocode = $response_message['eventSharing']['ErrorCode'];
                $userinfodesc = $response_message['eventSharing']['ErrorDesc'];
                $response_mess = $response_mess = get_response_string("eventSharing", $userinfocode, $userinfodesc);
                return $response_mess;
            } elseif ($userinfo['eventSharing']['event_share_out_of_bound'] === TRUE) {
                $obj_error = new Error ();
                $response_message = $obj_error->error_type("eventSharing", $userinfo);
                $userinfocode = $response_message['eventSharing']['ErrorCodeOutOfBound'];
                $userinfodesc = $response_message['eventSharing']['ErrorDescOutOfBound'];
                $response_mess = $response_mess = get_response_string("eventSharing", $userinfocode, $userinfodesc);
                return getValidJSON($response_mess);
            }
            $userinfocode = $response_message['eventSharing']['SuccessCode'];
            $userinfodesc = $response_message['eventSharing']['SuccessDesc'];
            $response_str = response_repeat_string();

            if ((isset($userinfo['hotpressid'])) && ($userinfo['hotpressid'])) {
                $str_id = '"eventHotpressId":"' . $userinfo['hotpressid'] . '",';
            } else {
                $str_id = null;
            }
            $commentText = isset($userinfo['comment']) && ($userinfo['comment']) ? $userinfo['comment'] : NULL;
            $even_desc = isset($userinfo['even_desc']) && ($userinfo['even_desc']) ? $userinfo['even_desc'] : NULL;
            $even_title = isset($userinfo['even_title']) && ($userinfo['even_title']) ? $userinfo['even_title'] : NULL;

            $userinfo['comment'] = str_replace('\\', "", $userinfo['comment']);
            $userinfo['comment'] = str_replace(array("\r\n", "\r", "\n", "<br />", "<br/>"), "\\n", $userinfo['comment']);
            $userinfo['comment'] = strip_tags($userinfo['comment']);
            $userinfo['comment'] = str_replace(array("\""), "", $userinfo['comment']);

            $userinfo['even_desc'] = str_replace('\\', "", $userinfo['even_desc']);
            $userinfo['even_desc'] = str_replace(array("\r\n", "\r", "\n", "<br />", "<br/>"), "\\n", $userinfo['even_desc']);
            $userinfo['even_desc'] = strip_tags($userinfo['even_desc']);
            $userinfo['even_desc'] = str_replace(array("\""), "", $userinfo['even_desc']);

            $userinfo['even_title'] = str_replace('\\', "", $userinfo['even_title']);
            $userinfo['even_title'] = str_replace(array("\r\n", "\r", "\n", "<br />", "<br/>"), "\\n", $userinfo['even_title']);
            $userinfo['even_title'] = strip_tags($userinfo['even_title']);
            $userinfo['even_title'] = str_replace(array("\""), "", $userinfo['even_title']);
            $commentText = isset($userinfo['comment']) && ($userinfo['comment']) ? $userinfo['comment'] : NULL;
            $even_desc = isset($userinfo['even_desc']) && ($userinfo['even_desc']) ? $userinfo['even_desc'] : NULL;
            $even_title = isset($userinfo['even_title']) && ($userinfo['even_title']) ? $userinfo['even_title'] : NULL;
            $eventId = isset($userinfo['even_id']) && ($userinfo['even_id']) ? $userinfo['even_id'] : NULL;
            $response_mess = '
            {
               ' . $response_str . '
               "eventSharing":{
               ' . $str_id . '
               "eventId":"' .$eventId . '",
               "eventDescription":"' .str_replace('"', '\"',$even_desc). '",
               "eventTitle":"' .str_replace('"', '\"',$even_title). '",
               "commentText":"' .str_replace('"', '\"',preg_replace('/\s+/', ' ', $commentText)). '",
               "errorCode":"' . $userinfocode . '",
               "errorMsg":"' . $userinfodesc . '"

               }
            }
 	';
        } else {
            $userinfocode = $response_message['eventSharing']['ErrorCode'];
            $userinfodesc = $response_message['eventSharing']['ErrorDesc'];
            $response_mess = $response_mess = get_response_string("eventSharing", $userinfocode, $userinfodesc);
        }
        if (DEBUG)
            writelog("Response:eventSharing():", $response_mess, false);
        return getValidJSON($response_mess);
    }

    /*  function deleteEventComment()
      Purpose    : to remove event comment
      Parameters : $xmlresponse       : request array for event comment remove
      $response_message  : ?
      Returns    : Json formatted response for event comment remove */

    function deleteEventComment($response_message, $xmlrequest) {

        global $return_codes;
        $userinfo = array();
        $userinfo = $this->delete_event_comment($xmlrequest);
        if ((isset($userinfo['DeleteEventComment']['successful_fin'])) && (!$userinfo['DeleteEventComment']['successful_fin'])) {
            $obj_error = new Error();
            $response_message = $obj_error->error_type("DeleteEventComment", $userinfo);
            $userinfocode = $response_message['DeleteEventComment']['ErrorCode'];
            $userinfodesc = $response_message['DeleteEventComment']['ErrorDesc'];
            $response_mess = $response_mess = get_response_string("DeleteEventComment", $userinfocode, $userinfodesc);
            return getValidJSON($response_mess);
        }
        if ((isset($userinfo['DeleteEventComment']['successful_fin'])) && ($userinfo['DeleteEventComment']['successful_fin'])) {

            $response_mess = '
                   {
       ' . response_repeat_string() . '
        "DeleteEventComment":{
               "errorCode":"' . $return_codes["DeleteEventComment"]["SuccessCode"] . '",
               "errorMsg":"' . $return_codes["DeleteEventComment"]["SuccessDesc"] . '"
       }
	  }';
        } else {

            $response_mess = '
                {
       ' . response_repeat_string() . '
       "DeleteEventComment":{
          "errorCode":"' . $return_codes["DeleteEventComment"]["NoRecordErrorCode"] . '",
          "errorMsg":"' . $return_codes["DeleteEventComment"]["NoRecordErrorDesc"] . '"

       }
	  }';
        }
        return getValidJSON($response_mess);
    }
	
	
function formEventResponse($row,$xmlresponse)
{
	 $range = false;
	 if (isset($xmlresponse['Events']['eventType']) && ($xmlresponse['Events']['eventType'] == "nearby")) {
	 	$range = true;
	 }
	 $latitude1 = floatval(isset($xmlresponse['Events']['latitude']) && ($xmlresponse['Events']['latitude']) ? $xmlresponse['Events']['latitude'] : NULL);
        $longitude1 = floatval(isset($xmlresponse['Events']['longitude']) && ($xmlresponse['Events']['longitude']) ? $xmlresponse['Events']['longitude'] : NULL);
	if ($xmlresponse['Events']['eventType'] != 'calender') {
                    if (isset($row['latitude']) && ($row['latitude']) && isset($row['longitude']) && ($row['longitude'])) {
                        $eventList = distanceByApi($latitude1, $longitude1, $row);
                    }
					
		    $tesmp = ' "distance":"' . (($eventList['statusCode'] == 0) ? round($eventList['distance'], 2) . ' miles' : 'Distance Not Present' ) . '"';
                }
                
                $eventList[0]['even_desc'] = str_replace('\\', "", $eventList[0]['even_desc']);
                $eventList[0]['even_desc'] = str_replace(array("\r\n", "\r", "\n", "<br />", "<br/>"), "\\n", $eventList[0]['even_desc']);
                $eventList[0]['even_desc'] = strip_tags($eventList[0]['even_desc']);
                $eventList[0]['even_desc'] = str_replace(array("\"", "\'"), "", $eventList[0]['even_desc']);
                $width_even_img = NULL;
                $height_even_img = NULL;
                if ($xmlresponse['Events']['eventType'] != 'calender') {
                    if (is_readable($this->local_folder . $eventList[0]['even_img'])) {
                        $sizee = getimagesize($this->local_folder . $eventList[0]['even_img']);
                        $width_even_img = $sizee[0];
                        $height_even_img = $sizee[1];
                        $file_extension = substr($eventList[0]['even_img'], strrpos($eventList[0]['even_img'], '.') + 1);

                        $arr = explode('.', $eventList[0]['even_img']);

                        if (!file_exists($this->local_folder . $arr[0] . "_" . $eventList[0]['even_id'] . "." . $file_extension) && (preg_match('/^image\/(jp[e]?g|png|gif)$/', $sizee['mime']))) {
                            thumbanail_for_image($eventList[0]['even_id'], $eventList[0]['even_img']);
                        }
                    }
                }else {
                    if (is_readable($this->local_folder . $row['even_img'])) {
                        $sizee = getimagesize($this->local_folder . $row['even_img']);
                        $width_even_img = $sizee[0];
                        $height_even_img = $sizee[1];
                        $file_extension = substr($row['even_img'], strrpos($row['even_img'], '.') + 1);

                        $arr = explode('.', $row['even_img']);

                        if (!file_exists($this->local_folder . $arr[0] . "_" . $row['even_id'] . "." . $file_extension) && (preg_match('/^image\/(jp[e]?g|png|gif)$/', $sizee['mime']))) {
                            thumbanail_for_image($row['even_id'], $row['even_img']);
                        }
                    }
                }
                 $sizee = isset($sizee) ? $sizee : NULL;
                if (preg_match('/^image\/(jp[e]?g|png|gif)$/', $sizee['mime'])) {
                    if ($xmlresponse['Events']['eventType'] != 'calender') {
                        list($width_even_img, $height_even_img) = (isset($eventList[0]['even_img']) && (strlen($eventList[0]['even_img']) > 7)) ? getimagesize($this->local_folder . $eventList[0]['even_img']) : NULL;
                        $eventList[0]['even_img'] = isset($eventList[0]['even_img']) && (strlen($eventList[0]['even_img']) > 7) ? $this->profile_url . event_image_detail($eventList[0]['even_id'], $eventList[0]['even_img'], 1) : NULL;
                    } else {
                        list($width_even_img, $height_even_img) = (isset($row['even_img']) && (strlen($row['even_img']) > 7)) ? getimagesize($this->local_folder . $row['even_img']) : NULL;
                        $row['even_img'] = isset($row['even_img']) && (strlen($row['even_img']) > 7) ? $this->profile_url . event_image_detail($row['even_id'], $row['even_img'], 1) : NULL;
                        $distance['distance'] = 1;
                        $tesmp = '"distance":""';
                        $counter = $xmlresponse['count'];
                    }
                }
                if (($eventList[0]['even_id'] != '') && ($eventList['statusCode'] == 0) && ($range == TRUE) && ($eventList['distance'] <= 20) && ($eventList['distance'] >= 0 )) {

                    $counter++;
                    $counter_total++;
                    $eventListingStr.= '{
            	"eventId":"' .str_replace('"', '\"',$eventList[0]['even_id']). '",
                "eventTitle":"' .str_replace('"', '\"',strtoupper(trim(preg_replace('/\s+/', ' ', $eventList[0]['even_title'])))). '",
                "eventImageUrl":"' .str_replace('"', '\"',$eventList[0]['even_img']). '",
                "width_even_img":"' .str_replace('"', '\"',$width_even_img). '",
                "height_even_img":"' .str_replace('"', '\"',$height_even_img). '",
                "eventDescription":"' .str_replace('"', '\"',trim(preg_replace('/\s+/', ' ', $eventList[0]['even_desc']))). '",
                "eventLocation":"' .str_replace('"', '\"',$eventList[0]['even_loc']). '",
                "eventGuestListExists":"' . (($eventList[0]['guest'] == 'y') ? "true" : "false") . '",
                "eventDate":"' .str_replace('"', '\"',$eventList[0]['actualdate']). '",
                "eventTime":"' .str_replace('"', '\"',$eventList[0]['actualtime']). '",
                ' . $tesmp . '
	         }';
                    
                }
                //echo $eventListingStr;
              if (($range == FALSE) && ($eventList[0]['even_id'] != '') && ($xmlresponse['Events']['eventType'] == "all" || ($xmlresponse['Events']['eventType'] == "followed"))) {

                    $counter++;
                    $counter_total++;
                    $eventListingStr.= '{
            	"eventId":"' .str_replace('"', '\"',$eventList[0]['even_id']). '",
                "eventTitle":"' .str_replace('"', '\"',strtoupper(trim(preg_replace('/\s+/', ' ', $eventList[0]['even_title'])))) . '",
                "eventImageUrl":"' .str_replace('"', '\"',$eventList[0]['even_img']). '",
                "width_even_img":"' .str_replace('"', '\"',$width_even_img). '",
                "height_even_img":"' .str_replace('"', '\"',$height_even_img). '",
                "eventDescription":"' .str_replace('"', '\"',trim(preg_replace('/\s+/', ' ', $eventList[0]['even_desc']))). '",
                "eventLocation":"' .str_replace('"', '\"',$eventList[0]['even_loc']). '",
                "eventGuestListExists":"' . (($eventList[0]['guest'] == 'y') ? "true" : "false") . '",
                "eventDate":"' .str_replace('"', '\"',$eventList[0]['actualdate']). '",
                "eventTime":"' .str_replace('"', '\"',$eventList[0]['actualtime']). '",
                "eventTime":"' .str_replace('"', '\"',$eventList[0]['actualtime']). '",
                ' . $tesmp . '
	         }';
                   
                }
                
                if (($range == FALSE) && $xmlresponse['Events']['eventType'] == 'calender') {
                    $eventListingStr.= '{
            	"eventId":"' .str_replace('"', '\"',$row['even_id']). '",
                "eventTitle":"' .str_replace('"', '\"',strtoupper(trim(preg_replace('/\s+/', ' ', $row['even_title'])))). '",
                "eventImageUrl":"' .str_replace('"', '\"',$row['even_img']). '",
                "width_even_img":"' .str_replace('"', '\"',$width_even_img). '",
                "height_even_img":"' .str_replace('"', '\"',$height_even_img). '",
                "eventDescription":"' .str_replace('"', '\"',trim(preg_replace('/\s+/', ' ', $row['even_desc']))). '",
                "eventLocation":"' .str_replace('"', '\"',$row['even_loc']). '",
                "eventGuestListExists":"' . (($row['guest'] == 'y') ? "true" : "false") . '",
                "eventDate":"' .str_replace('"', '\"',$row['actualdate']). '",
                "eventTime":"' .str_replace('"', '\"',$row['actualtime']). '",
                "eventTime":"' .str_replace('"', '\"',$row['actualtime']). '",
                ' . $tesmp . '
	         }';
                    
                }
				
				return $eventListingStr;
				
      }      

}

?>