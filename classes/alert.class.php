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
  File-name     : alert.class.php
  Directory Path: $/MySNL/Deliverables/Code/MySNL_WebServiceV2/classes/
  Author        : Rajesh Bakade
  Date          : 08/08/2011
  Modified By   : N/A
  Date          : N/A

  Include Files : none
  CSS File(s)   : none

  Functions Used
  Javascript   :  none
  PHP          :

  DataBase Table(s)   : messages_system , bulletin , members , testimonials ,tagged_photos ,
  tag_ent_list, photo_album tagged_photos photo_comments,events_comments,likehot,fn_annotation_rows

  Global Variable(s)  : $return_codes
  Constant(s)         : PROFILE_IMAGE_SITEURL , LOCAL_FOLDER

  Description         :  File to display the Latest Notifications for the perticular user from different module.
  Reviwed By  :
  Reviwed Date:
 * ************************************************************************************* */

final class Alerts {

    var $profile_url = PROFILE_IMAGE_SITEURL;
    var $local_folder = LOCAL_FOLDER;

    /*  function get_alert_result()
      Purpose: To get the latest notifications from different module
      Parameters : $xmlrequest : Request array for notifications
      Returns : array for different module from DB */

    public function get_alert_result($xmlrequest) {

        if (DEBUG)
            writelog("alert.class.php :: get_alert_result() :: ", "Starts Here ", false);
        $alert = array();
        $uid = mysql_real_escape_string($xmlrequest['Alerts']['userId']);

        $sql_network = "select mes_id,frm_id,date from messages_system where type='friend' and mem_id='$uid' and messages_system.read=''";
        $alert['network'] = execute_query($sql_network, true, "select");

        $query_new_msg = "select mes_id,mem_id,date,frm_id from messages_system where mem_id='$uid' and folder='inbox' and type='message' and messages_system.new='new' ORDER BY date DESC";
        $alert['new_msg'] = execute_query($query_new_msg, true, "select");

        $query_reply_hotpress = "select t.mem_id,t.from_id,t.id,t.date,t.parentid,m.profilenam,m.gender,m.profile_type from bulletin as t, members as m where t.from_id='$uid' and t.msg_alert='Y' AND t.mem_id = m.mem_id AND t.parentid!=0 AND t.from_id != t.mem_id";
		//$query_reply_hotpress = "select t.mem_id,t.from_id,t.id,t.parentid,m.profilenam from bulletin as t, members as m where t.parentid!=0 AND t.id in (select bulletin_id  from comment_alert_notification where mem_id='$uid' and show_alert = 'Y') AND t.mem_id = m.mem_id";
        $alert['reply_hotpress'] = execute_query($query_reply_hotpress, true, "select");

        $query_reply_comment = "select t.tst_id,t.added,t.mem_id,t.from_id,t.parent_tst_id,t.added,m.profilenam,m.gender,m.profile_type from testimonials as t, members as m where t.mem_id='$uid' and t.msg_alert='Y' AND t.from_id = m.mem_id AND t.from_id != t.mem_id";
        $alert['reply_comment'] = execute_query($query_reply_comment, true, "select");

        $query_tag = "select ms.special,m.profilenam,ms.mem_id ,ms.frm_id,ms.date,ms.mes_id as id,m.gender,m.profile_type FROM messages_system AS ms, members AS m WHERE ms.type = 'tagged' AND ms.frm_id =" . $uid . " AND ms.mem_id = m.mem_id";
        //SELECT count(*) AS cnt, m.profilenam,ms.mem_id FROM messages_system AS ms, members AS m WHERE TYPE = 'tagged' AND frm_id =".$uid." AND ms.mem_id = m.mem_id GROUP BY ms.mem_id
        $alert['tag'] = execute_query($query_tag, true, "select");

        $query_photo_comments = "SELECT pc.id,pc.mem_id, pc.date, pc.photo_id, pa.photo_small ,pa.album_id FROM photo_comments AS pc, photo_album AS pa WHERE pc.msg_alert ='Y' AND pc.from_id ='$uid' AND pc.mem_id !='$uid' AND pc.photo_id = pa.photo_id";
        $alert['photo_comments'] = execute_query($query_photo_comments, true, "select");

        $query_event_comments = "SELECT ec.id,ec.even_id,ec.from_id,ec.date,ms.profilenam,ms.photo_b_thumb,ms.profile_type,ms.gender FROM events_comments AS ec,members AS ms WHERE ec.mem_id='$uid' AND ec.from_id !='$uid' AND parent_id = '0' AND ec.from_id =ms.mem_id AND msg_alert='Y' ORDER BY date";
        $alert['event_comments'] = execute_query($query_event_comments, true, "select");

        $query_reply_event_comments = "SELECT a.id,a.even_id,a.from_id,a.date,a.parent_id,b.profilenam, b.photo_b_thumb,b.profile_type,b.gender FROM events_comments AS a, members AS b WHERE a.mem_id ='$uid' AND a.from_id !='$uid' AND parent_id != '0' AND a.from_id = b.mem_id AND msg_alert = 'Y'";
        $alert['reply_event_comments'] = execute_query($query_reply_event_comments, true, "select");

        $query_announce_arrival = "SELECT aa.id,aa.user_id,m.profilenam,ms.mem_id ,ms.frm_id,ms.date,ms.mes_id AS id,m.gender,m.profile_type,ms.special,ms.mes_id FROM messages_system AS ms LEFT JOIN announce_arrival AS aa ON (ms.special=aa.id), members AS m WHERE ms.type = 'appearance' AND ms.mem_id ='$uid' AND ms.frm_id = m.mem_id AND skip_alert='1' AND ms.new='new' AND aa.user_id IN(SELECT DISTINCT n2.mem_id FROM network n1,network n2 WHERE n1.frd_id=n2.mem_id AND n1.mem_id='$uid' AND n2.frd_id='$uid')";
        $alert['announce_arrival'] = execute_query($query_announce_arrival, true, "select");

        $query_tagged_entourage_list = "SELECT aa.id as announce_id,tel.id,tel.venue_id,tel.date,tel.time,tel.user_id,tel.ent_id,mem.mem_id,mem.profilenam,mem.profile_type,mem.gender,mem.photo_b_thumb FROM tag_ent_list AS tel LEFT JOIN announce_arrival AS aa ON (tel.user_id=aa.user_id AND tel.venue_id=aa.venue_id),members AS mem WHERE tel.ent_id='$uid' AND tel.ent_id = mem.mem_id AND tel.msg_alert = 'Y'";
        $alert['tagged_entourage_list'] = execute_query($query_tagged_entourage_list, true, "select");

		$query_sqlBottle="SELECT bt.id,bt.mem_id,bt.alert_text,bt.createdate AS DATE,bdg.badge_name,bdg.public_hint_active,bdg.badge_id,mem.profilenam,bt.venue_id FROM bottel_alert AS bt,badges AS bdg ,members AS mem WHERE bt.alert_status ='N' AND bt.mem_id = '$uid' AND bt.mem_id=mem.mem_id AND (mem.profile_type !='C' OR mem.profile_type !='c') AND bt.bottel_type=bdg.badge_name";
		$alert['badges']=execute_query($query_sqlBottle,true,"select");

	if (DEBUG) {
            writelog("alert.class.php :: get_alert_result() :: Query to get alert result : ", $alert, false);
            writelog("alert.class.php :: get_alert_result() :: ", "End Here ", false);
        }
        return $alert;
    }

    /*  function clear_alert()
      Purpose: To completely clear the alerts from all modules
      Parameters : $xmlrequest : Request array for alert clear
      Returns : count whether all alerts are clear or not */

    public function clear_alert($xmlrequest) {

        if (DEBUG)
            writelog("alert.class.php :: clear_alert() :: ", "Starts Here ", false);
        $uid = mysql_real_escape_string($xmlrequest['AlertsClear']['userId']);
        $exeUpdate = array();
        $test = 0;
       $query_network = "select ms.mes_id,ms.frm_id,ms.date from messages_system as ms where ms.type='friend' and ms.mem_id='$uid' GROUP BY ms.frm_id";
        $alert['network'] = execute_query($query_network, true, "select");
        if (isset($alert['network']) && $alert['network'] != '') {
            foreach ($alert['network'] AS $kk => $network) {
		if(!empty($network) && (is_array($network))){
                $queryUpdate = "DELETE FROM messages_system where mes_id='{$network['mes_id']}'";
                $exeUpdate = execute_query($queryUpdate, false, "update");
                $test += 1;
		}
            }
        }
        $query_new_msg = "select mes_id,mem_id,date from messages_system where mem_id='$uid' and folder='inbox' and type='message' and messages_system.new='new'";
        $alert['new_msg'] = execute_query($query_new_msg, true, "select");
        if (isset($alert['new_msg']) && $alert['new_msg'] != '') {
            foreach ($alert['new_msg'] AS $kk => $message) {
		if(!empty($message) && (is_array($message))){
                $queryUpdate = "update messages_system set messages_system.new='viewed'  where mes_id={$message['mes_id']}";
                $exeUpdate = execute_query($queryUpdate, false, "update");
                $test += 1;
		}
            }
        }

        $query_reply_hotpress = "select t.mem_id,t.from_id,t.id,t.date,t.parentid,m.profilenam,m.gender,m.profile_type from bulletin as t, members as m where t.from_id='$uid' and t.msg_alert='Y' AND t.mem_id = m.mem_id AND t.parentid!=0 AND t.from_id != t.mem_id";
	$alert['reply_hotpress'] = execute_query($query_reply_hotpress, true, "select");
	if (isset($alert['reply_hotpress']) && $alert['reply_hotpress'] != '') {
	    foreach ($alert['reply_hotpress'] AS $kk => $replyToHotpress) {
		if (!empty($replyToHotpress) && (is_array($replyToHotpress))) {
		    $queryUpdate = "update bulletin set msg_alert='N' where id={$replyToHotpress['id']}";
		    $exeUpdate = execute_query($queryUpdate, false, "update");
			//Added by anusha
			$sql="DELETE from comment_alert_notification where bulletin_id ='".$replyToHotpress['id']."' and mem_id='".$uid."'";
		  	$exeUpdate = execute_query($sql, false, "update");
			
		    $test += 1;
		}
	    }
	}

       $query_reply_comment = "select t.tst_id,t.mem_id,t.from_id,t.tst_id,t.parent_tst_id,t.added,m.profilenam,m.gender,m.profile_type from testimonials as t, members as m where t.mem_id='$uid' and t.msg_alert='Y' AND t.from_id = m.mem_id AND t.from_id != t.mem_id";
        $alert['reply_comment'] = execute_query($query_reply_comment, true, "select");
        if (isset($alert['reply_comment']) && $alert['reply_comment'] != '') {
            foreach ($alert['reply_comment'] AS $kk => $replyToComment) {
		if(!empty($replyToComment) && (is_array($replyToComment))){
                $queryUpdate = "update testimonials set testimonials.msg_alert='N'  where tst_id={$replyToComment['tst_id']}";
                $exeUpdate = execute_query($queryUpdate, false, "update");
                $test += 1;
		}
            }
        }

        $query_tag = "select m.profilenam,ms.mem_id ,ms.frm_id,ms.date,ms.mes_id as id,m.gender,m.profile_type,ms.mes_id FROM messages_system AS ms, members AS m WHERE ms.type = 'tagged' AND ms.frm_id ='$uid' AND ms.mem_id = m.mem_id";
        $alert['tag'] = execute_query($query_tag, true, "select");
        if (isset($alert['tag']) && $alert['tag'] != '') {
            foreach ($alert['tag'] AS $kk => $tag) {
                if (isset($tag['mes_id']) && ($tag['mes_id'])) {
                   $queryUpdate = "DELETE FROM messages_system WHERE mes_id='{$tag['mes_id']}'";
                    $exeUpdate = execute_query($queryUpdate, false, "update");
                    $test += 1;
                }
            }
        }

       $query_photo_comments = "SELECT pc.id,pc.mem_id, pc.date, pc.photo_id, pa.photo_small FROM photo_comments AS pc, photo_album AS pa WHERE pc.msg_alert =  'Y' AND pc.from_id =  '$uid' AND pc.photo_id = pa.photo_id";
        $alert['photo_comments'] = execute_query($query_photo_comments, true, "select");
        if (isset($alert['photo_comments']) && $alert['photo_comments'] != '') {
            foreach ($alert['photo_comments'] AS $kk => $photoComment) {
		if(!empty($photoComment) && (is_array($photoComment))){
               $queryUpdate = "update photo_comments set msg_alert='N'  where id='{$photoComment['id']}'";
                $exeUpdate = execute_query($queryUpdate, false, "update");
                $test += 1;
		}
            }
        }

        $query_event_comments = "SELECT ec.id,ec.even_id,ec.from_id,ec.date,ms.profilenam,ms.photo_b_thumb,ms.profile_type,ms.gender FROM events_comments AS ec,members AS ms WHERE ec.mem_id='$uid' AND ec.from_id !='$uid' AND ec.from_id =ms.mem_id AND msg_alert='Y' ORDER BY date";
        $alert['event_comments'] = execute_query($query_event_comments, true, "select");
        if (isset($alert['event_comments']) && $alert['event_comments'] != '') {
            foreach ($alert['event_comments'] AS $kk => $eventsComment) {
		if(is_array($eventsComment) && (!empty($eventsComment))){
                $queryUpdate = "update events_comments set msg_alert='N'  where id='{$eventsComment['id']}'";
                $exeUpdate = execute_query($queryUpdate, false, "update");
                $test += 1;
		}
            }
        }

        $query_reply_event_comments = "SELECT a.id,a.even_id,a.from_id,a.date,b.profilenam, b.photo_b_thumb,b.profile_type,b.gender FROM events_comments AS a, members AS b WHERE a.mem_id ='$uid' AND a.from_id !='$uid' AND parent_id != '0' AND a.from_id = b.mem_id AND msg_alert = 'Y'";
        $alert['reply_event_comments'] = execute_query($query_reply_event_comments, true, "select");
        if (isset($alert['reply_event_comments']) && $alert['reply_event_comments'] != '') {
            foreach ($alert['reply_event_comments'] AS $kk => $reEventsComment) {
		if(!empty($reEventsComment) && (is_array($reEventsComment))){
                $queryUpdate = "update events_comments set msg_alert='N'  where id='{$reEventsComment['id']}'";
                $exeUpdate = execute_query($queryUpdate, false, "update");
                $test += 1;
		}
            }
        }

       $query_tagged_entourage_list = "SELECT tel.id,tel.venue_id,tel.user_id,tel.ent_id,tel.date,mem.mem_id,mem.profilenam,mem.profile_type,mem.gender,mem.photo_b_thumb FROM tag_ent_list AS tel,members AS mem WHERE tel.ent_id='$uid' AND tel.ent_id = mem.mem_id AND tel.msg_alert = 'Y'";
        $alert['taggedEntourageList'] = execute_query($query_tagged_entourage_list, true, "select");

        if (isset($alert['taggedEntourageList']) && $alert['taggedEntourageList'] != '') {
            foreach ($alert['taggedEntourageList'] AS $kk => $taggedEntList) {
		if(!empty($taggedEntList) && (is_array($taggedEntList))){
                $queryUpdate = "update tag_ent_list set msg_alert='N'  where id='{$taggedEntList['id']}'";
                $exeUpdate = execute_query($queryUpdate, false, "update");
                $test += 1;
		}
            }
        }
		
		$query_badges = "SELECT * FROM bottel_alert WHERE mem_id = '$uid' AND alert_status='N'";
		$alert['badges'] = execute_query($query_badges, true, "select");

	if (isset($alert['badges']) && $alert['badges'] != '') {
	    foreach ($alert['badges'] AS $kk => $badges) {
		if (!empty($badges) && (is_array($badges))) {
		    $queryUpdate = "update bottel_alert set alert_status='Y'  where id='{$badges['id']}'";
		    $exeUpdate = execute_query($queryUpdate, false, "update");
		    $test += 1;
		}
	    }
	}
	
        writelog("Appearance:appearanceVenueList:", json_encode($test, true), false);
        return $test;
    }

    /*  function update_alert()
      Purpose    : To remove the alerts from current alerts list when clicked
      Parameters : $xmlrequest : Request array for removing alert from list
      Returns    : count whether alert is removed or not */

    public function update_alert($xmlrequest) {

        if (DEBUG)
            writelog("alert.class.php :: update_alert() :: ", "Starts Here ", false);
        $exeUpdate = array();
		$member_id = mysql_real_escape_string($xmlrequest['AlertsUpdate']['user_id']);
        $alertsplUpdateId = mysql_real_escape_string($xmlrequest['AlertsUpdate']['alertsUpdateId']);
        $alertsplId = mysql_real_escape_string($xmlrequest['AlertsUpdate']['alertsplId']);
        $alertType = mysql_real_escape_string($xmlrequest['AlertsUpdate']['alertType']);

        if (($alertType == 'tag') || ($alertType == 'Network') || ($alertType == 'New Message')) {
            $queryUpdate = "update messages_system set messages_system.new='viewed',messages_system.read='' where mes_id='$alertsplId'";
            $exeUpdate = execute_query($queryUpdate, false, "update");
        } elseif ($alertType == 'New comment') {
            $queryUpdate = "update testimonials set testimonials.msg_alert='N'  where tst_id='$alertsplId'";
            $exeUpdate = execute_query($queryUpdate, false, "update");
        } elseif ($alertType == 'Reply To Comment') {
//	    $getMainId = execute_query("select parent_tst_id FROM testimonials WHERE tst_id='$alertsplId'");
            $queryUpdate = "update testimonials set testimonials.msg_alert='N'  where tst_id='$alertsplId'";
            $exeUpdate = execute_query($queryUpdate, false, "update");
        } elseif ($alertType == 'Reply To Hotpress') {   // no specialid is available as it is occupied by parentid
            $queryUpdate = "update bulletin set msg_alert='N' where id='$alertsplUpdateId'";
            $exeUpdate = execute_query($queryUpdate, false, "update");
			$sql="DELETE from comment_alert_notification where bulletin_id ='".$alertsplUpdateId."' and mem_id='".$member_id."'";
		  	$exeUpdate = execute_query($sql, false, "update");
			$exeUpdate['count']=1;
        } elseif ($alertType == 'photo comments') {      // no specialid is available as it is occupied by photo_id
            $queryUpdate = "update photo_comments set msg_alert='N'  where id='$alertsplUpdateId'";
            $exeUpdate = execute_query($queryUpdate, false, "update");
        } elseif (($alertType == 'event_comments') || ($alertType == 'reply event comments')) {      //no specialid is available as it is occupied by even_id
            $queryUpdate = "update events_comments set msg_alert='N'  where id='$alertsplUpdateId'";
            $exeUpdate = execute_query($queryUpdate, false, "update");
        } elseif (($alertType == 'taggedEntourage')) {
            $queryUpdate = "update tag_ent_list set msg_alert='N'  where id='$alertsplId'";
            $exeUpdate = execute_query($queryUpdate, false, "update");
        }

        $exeUpdate['msgType'] = $alertType;
        $exeUpdate['count'] = $exeUpdate['count'];
        if ($exeUpdate['count'] > 0) {
            return $exeUpdate;
        } else {
            array();
        }
    }

//end of update_alert()

    /*  function get_hotpress_alert()
      Purpose    : To move from alerts module to hotpress
      Parameters : $xmlrequest : Request array for hotpress alert
      Returns    : array for information from bulletin */

    public function get_hotpress_alert($xmlrequest,$pagenumber,$limit) {

        if (DEBUG)
            writelog("alert.class.php :: hotpress_alert() :: ", "Starts Here ", false);
        $alert = array();

        $uid = mysql_real_escape_string($xmlrequest['HotPressAlert']['userId']);
        $bulletinId = mysql_real_escape_string($xmlrequest['HotPressAlert']['bulletinId']);

	$lowerLimit = isset($pagenumber) ? ($pagenumber - 1) * $limit : 0;
        $query = "SELECT bl.parentid,bl.id,bl.body,bl.image_link,bl.date,bl.link_url,bl.youtubeLink,bl.link_image,mem.mem_id,mem.profilenam,
        mem.photo_b_thumb,bl.post_via FROM bulletin AS bl,members AS mem WHERE (bl.id='$bulletinId')  AND bl.mem_id=mem.mem_id ORDER BY bl.date ASC";
	$alertinfo['parent'] = execute_query($query, true, "select");

	$query_for_current_total = "SELECT SQL_CALC_FOUND_ROWS bl.parentid,bl.id,bl.body,bl.image_link,bl.date,bl.link_url,bl.youtubeLink,bl.link_image,mem.mem_id,mem.profilenam,
        mem.photo_b_thumb,bl.post_via FROM bulletin AS bl,members AS mem WHERE bl.parentid='$bulletinId' AND bl.mem_id=mem.mem_id ORDER BY bl.date ASC LIMIT $lowerLimit,$limit";
	$alertinfo['child'] = execute_query($query_for_current_total, true, "select");
	$total = execute_query("SELECT FOUND_ROWS() as TotalRecords ;", true, "select");
        $alertinfo['parent']['totalCount'] = (isset($total[0]['TotalRecords'])) ? $total[0]['TotalRecords'] : 0;
	$alertinfo['parent']['currentCount']=$alertinfo['child']['count'];

        if (!empty($alertinfo)) {
            $likehot = "SELECT COUNT(*) as user_like_cnt FROM likehot WHERE hotpressid='$bulletinId' AND user_id='$uid'";
            $likeCount = execute_query($likehot, false, "select");
            $alertinfo[0]['likecount'] = $likeCount['user_like_cnt'];

	    $query_update = "UPDATE bulletin SET msg_alert ='N' WHERE id ='$bulletinId' ";
            $alertUpdate = execute_query($query_update, true, "update");

            if (DEBUG) {
                writelog("alert.class.php :: hotpress_alert() :: Query to get hotpress alert : ", $alertinfo, false);
                writelog("alert.class.php :: hotpress_alert() :: ", "End Here ", false);
            }
            return $alertinfo;
        } else {
            array();
        }
    }

//end of get_hotpress_alert

    /*  function get_comment_alert()
      Purpose    : To move from alerts module to profile module
      Parameters : $xmlrequest : Request array for profile comments
      Returns    : array for information from testimonial */

    public function get_comment_alert($xmlrequest,$pagenumber,$limit) {

	if (DEBUG)
	    writelog("alert.class.php :: get_comment_alert() :: ", "Starts Here ", false);
	$alertinfo = array();
	if (isset($xmlrequest['CommentAlert']['userId']) && $xmlrequest['CommentAlert']['userId'] != '') {
	    $uid = mysql_real_escape_string($xmlrequest['CommentAlert']['userId']);
	    $testimonialId = mysql_real_escape_string($xmlrequest['CommentAlert']['testimonialId']);
	}

	$lowerLimit = isset($pagenumber) ? ($pagenumber - 1) * $limit : 0;
	$getMainId = execute_query("select parent_tst_id FROM testimonials WHERE tst_id='$testimonialId'", false, "select");
	if (isset($getMainId) && ($getMainId['parent_tst_id'] != 0)) {
	    $testMnlId = $getMainId['parent_tst_id'];
	}else{
	    $testMnlId = $testimonialId;
	}
	
	$query = "SELECT tm.parent_tst_id,tm.post_via,tm.added,tm.tst_id,tm.testimonial,tm.image_link,tm.link_url,tm.youtubeLink,tm.link_image,mem.mem_id,mem.photo_b_thumb,mem.profilenam,mem.gender,mem.profile_type FROM testimonials AS tm,members AS mem WHERE (tm.tst_id='$testMnlId')  AND tm.from_id=mem.mem_id ORDER BY tm.added ASC";
	$alertinfo['parent'] = execute_query($query, true, "select");

	$queryChild = "SELECT SQL_CALC_FOUND_ROWS tm.parent_tst_id,tm.post_via,tm.added,tm.tst_id,tm.testimonial,tm.image_link,tm.link_url,tm.youtubeLink,tm.link_image,mem.mem_id,mem.photo_b_thumb,mem.profilenam,mem.gender,mem.profile_type FROM testimonials AS tm,members AS mem WHERE (tm.parent_tst_id='$testMnlId')  AND tm.from_id=mem.mem_id ORDER BY tm.added ASC LIMIT $lowerLimit,$limit";
	$alertinfo['child'] = execute_query($queryChild, true, "select");
	$total = execute_query("SELECT FOUND_ROWS() as TotalRecords ;", true, "select");
	$alertinfo['parent']['totalCount'] = (isset($total[0]['TotalRecords'])) ? $total[0]['TotalRecords'] : 0;
	$alertinfo['parent']['currentCount'] = $alertinfo['child']['count'];


	if (!empty($alertinfo)) {
	    $queryTotComment = "SELECT COUNT(*) AS comments FROM testimonials AS tm,members AS mem WHERE tm.parent_tst_id='$testMnlId' AND tm.mem_id=mem.mem_id ORDER BY tm.added ASC";
	    $totComment = execute_query($queryTotComment, false, "select");
	    $alertinfo[0]['total_comment'] = $totComment['comments'];

	    if (DEBUG) {
		writelog("alert.class.php :: get_comment_alert() :: Query to get comment alert : ", $alertinfo, false);
		writelog("alert.class.php :: get_comment_alert() :: ", "End Here ", false);
	    }
	    return $alertinfo;
	} else {
	    array();
	}
    }

//end of get_comment_alert()

    /*  function display_photo_tag_alert
      Purpose    : To display photo tag alerts
      Parameters : $xmlrequest : Request array for displaying photo tag alerts
      Returns    : array for information about photo */

    public function display_photo_tag_alert($xmlrequest) {

        $userId = mysql_real_escape_string($xmlrequest['DisplayPhotoTagAlert']['userId']);
        $alertId = mysql_real_escape_string($xmlrequest['DisplayPhotoTagAlert']['alertId']);
        $photoId = mysql_real_escape_string($xmlrequest['DisplayPhotoTagAlert']['photoId']);
        $error = array();
		// Added on 25 Nov 2011 :: aarya to get photo-album owner_id;
		$query_photo = "SELECT p.photo_mid, p.photo_small, p.photo_id, a.id AS album_id, a.mem_id AS album_owner_id
FROM photo_album AS p, albums AS a
WHERE p.photo_id ='".$photoId."'
AND p.album_id = a.id";

        // Commented on 25 Nov 2011 :: aarya $query_photo = "SELECT photo_mid,photo_small FROM photo_album WHERE photo_id='$photoId'";
        $result_photo = execute_query($query_photo, false, "select");
        $query_msg = "SELECT mem_id,frm_id,subject,date FROM messages_system WHERE mes_id='$alertId'";
        $result_msg = execute_query($query_msg, false, "select");
        $error['photo'] = $result_photo;
        $error['message_info'] = $result_msg;

        if (DEBUG)
            writelog("Profile:full_screen_photo_valid()", $error, true);
        return $error;
    }

    /*  function respond_photo_tag_alerts()
      Purpose    : To display photo tag alerts
      Parameters : $xmlrequest : Request array for photo alert tag
      Returns    : success response if true otherwise unsuccessful */

    public function respond_photo_tag_alerts($xmlrequest) {

        $userId = mysql_real_escape_string($xmlrequest['RespondPhotoTagAlerts']['userId']);
        $alertId = mysql_real_escape_string($xmlrequest['RespondPhotoTagAlerts']['alertId']);
        $photoId = mysql_real_escape_string($xmlrequest['RespondPhotoTagAlerts']['photoId']);
        $status = mysql_real_escape_string(isset($xmlrequest['RespondPhotoTagAlerts']['status']) && ($xmlrequest['RespondPhotoTagAlerts']['status']) ? $xmlrequest['RespondPhotoTagAlerts']['status'] : NULL);
        $taggedEntourageId = mysql_real_escape_string(isset($xmlrequest['RespondPhotoTagAlerts']['taggedEntourageId']) && ($xmlrequest['RespondPhotoTagAlerts']['taggedEntourageId']) ? $xmlrequest['RespondPhotoTagAlerts']['taggedEntourageId'] : NULL);

        $error = array();
        if (!$status) {
            $get_status_id = execute_query("SELECT special FROM messages_system WHERE mes_id='$alertId'", false, "select");
            $query_fn_annotation_rows = "DELETE FROM fn_annotation_rows WHERE (mem_id='$taggedEntourageId' AND frnd_id='$userId' || mem_id='$userId' AND frnd_id='$taggedEntourageId') AND id='" . $get_status_id['special'] . "'";
            $query_tagged_photos = "DELETE FROM tagged_photos WHERE (tagged_to='$taggedEntourageId' AND tagged_by='$userId' || tagged_to='$userId' AND tagged_by='$taggedEntourageId')  AND annotation_id='" . $get_status_id['special'] . "'";
            $tagged_photos = execute_query($query_tagged_photos, false, "delete");
            $tagged_photos['count'] = isset($tagged_photos['count']) && ($tagged_photos['count']) ? $tagged_photos['count'] : NULL;

            $fn_annotation_rows = execute_query($query_fn_annotation_rows, false, "delete");
            $fn_annotation_rows['count'] = isset($fn_annotation_rows['count']) && ($fn_annotation_rows['count']) ? $fn_annotation_rows['count'] : NULL;

            $error = error_CRUD($xmlrequest, $tagged_photos['count']);
            if ((isset($error['RespondPhotoTagAlerts']['successful_fin'])) && (!$error['RespondPhotoTagAlerts']['successful_fin'])) {
                return $error;
            }
            $error = error_CRUD($xmlrequest, $fn_annotation_rows['count']);
            if ((isset($error['RespondPhotoTagAlerts']['successful_fin'])) && (!$error['RespondPhotoTagAlerts']['successful_fin'])) {
                return $error;
            }
        }
        $query_messages_system = "DELETE FROM messages_system WHERE mes_id='$alertId'";
        $messages_system = execute_query($query_messages_system, false, "delete");
        $messages_system['count'] = isset($messages_system['count']) && ($messages_system['count']) ? $messages_system['count'] : NULL;

        $error = error_CRUD($xmlrequest, $messages_system['count']);
        if ((isset($error['RespondPhotoTagAlerts']['successful_fin'])) && (!$error['RespondPhotoTagAlerts']['successful_fin'])) {
            return $error;
        }
        if (DEBUG)
            writelog("Profile:full_screen_photo_valid()", $error, true);
        return $error;
    }

    /*  function alertsList()
      Purpose    : To display alerts in json format
      Parameters : $xmlrequest       : Request array for notifications ,
      $response_message : ?
      Returns    : response in json format for alerts */

    public function alertsList($response_message, $xmlresponse) {

        global $return_codes;
        $alerts_from_query = array();
        $page = $xmlresponse['Alerts']['pageNumber'];
        $alerts_from_query = $this->get_alert_result($xmlresponse);
        $query_alert = 0;
        foreach ($alerts_from_query as $kk => $alert_list_from_query) {
            if (!empty($alert_list_from_query) && is_array($alert_list_from_query)) {
                $query_alert = 1;
            }
        }
        //if ($query_alert == 1) {
        $alert_rslt = getalerts($alerts_from_query);
		//date_default_timezone_set("UTC");
        //   if (!empty($alert_rslt)) {
        $total_count = 0;
        $valss1 = array();
        $alerts_list = false;
        foreach ($alert_rslt as $xx => $get_values) {
            if (is_array($get_values)) {
                foreach ($get_values as $yy => $get_vals) {
                    if (!empty($get_vals)) {
                        $valss1[] = $get_vals;
                        $total_count++;
                        $alerts_list = true;
                    }
                }
            } else {
                $alerts_list = false;
            }
        }

        // sort by date in descending
        usort($valss1, 'compare_date');
        $valss = pagination_array($valss1, $page, 20);

        if (isset($valss['count']) && $valss['count'] != 0) {
            // if (isset($xmlresponse['Alerts']['userId'])) {
            $str = '';
            for ($i = $valss['begin']; $i < $valss['end']; $i++) {
		if(!preg_match('/^(http|https)/i',$valss[$i]['alertImageUrl']))
                $valss[$i]['alertImageUrl'] = (isset($valss[$i]['alertImageUrl']) && (strlen($valss[$i]['alertImageUrl']) > 7)) ? $this->profile_url . $valss[$i]['alertImageUrl'] : $this->profile_url . default_images1($valss[$i]['alertUserGender'], $valss[$i]['alertUserProfileType']);

//                        $valss[$i]['alertsplImage'] = (isset($valss[$i]['alertsplImage']) && (strlen($valss[$i]['alertsplImage']) > 7)) ? $this->profile_url . $valss[$i]['alertsplImage'] : $this->profile_url . default_images($valss[$i]['alertUserGender'], $valss[$i]['alertUserProfileType']);

                $id = isset($valss[$i]['alertId']) && $valss[$i]['alertId'] ? $valss[$i]['alertId'] : NULL;
                $alerttitle = isset($valss[$i]['alerttitle']) && ($valss[$i]['alerttitle']) ? $valss[$i]['alerttitle'] : NULL;
                $alertDescription = isset($valss[$i]['alertDescription']) && ($valss[$i]['alertDescription']) ? $valss[$i]['alertDescription'] : NULL;
                $alertImageUrl = isset($valss[$i]['alertImageUrl']) && ($valss[$i]['alertImageUrl']) ? $valss[$i]['alertImageUrl'] : NULL;

                $alertType = isset($valss[$i]['alertType']) && ($valss[$i]['alertType']) ? $valss[$i]['alertType'] : NULL;
                $alertDate = isset($valss[$i]['alertDate']) && ($valss[$i]['alertDate']) ? time_difference($valss[$i]['alertDate']) : NULL;
                $alertsplId = isset($valss[$i]['alertsplId']) && ($valss[$i]['alertsplId']) ? $valss[$i]['alertsplId'] : NULL;
                $alertsplImg = isset($valss[$i]['alertsplImage']) && ($valss[$i]['alertsplImage']) ? $this->profile_url . $valss[$i]['alertsplImage'] : NULL;
                $valss[$i]['alertsplText'] = isset($valss[$i]['alertsplText']) && ($valss[$i]['alertsplText']) ? preg_replace('/\s+/', ' ', $valss[$i]['alertsplText']) : NULL;
                //$alertsplText = preg_replace('/\s+/', ' ', $valss[$i]['alertsplText']);
                $alertIdUpdate = isset($valss[$i]['alertUpdateId']) && ($valss[$i]['alertUpdateId']) ? $valss[$i]['alertUpdateId'] : NULL;
                $alertMainId = isset($valss[$i]['alertMainId']) && ($valss[$i]['alertMainId']) ? $valss[$i]['alertMainId'] : NULL;
		
//                        if (isset($alertsplId) && isset($alertsplImg) && isset($alertsplText['body']) && isset($alertIdUpdate)) {
//
//                            $tesmp = ' "alertsplId":"' . $alertsplId . '","alertsplText":"' . $alertsplText['body'] . '",
//                    "alertsplImage":"' . $alertsplImg . '","alertUpdateId":"' . $alertIdUpdate . '",';
//                        } elseif (isset($alertsplId) && isset($alertsplText['body']) && isset($alertIdUpdate)) {
//
//                            $tesmp = ' "alertsplId":"' . $alertsplId . '","alertsplText":"' . $alertsplText['body'] . '",
//                            "alertUpdateId":"' . $alertIdUpdate . '"';
//                        } elseif (isset($alertsplId) && isset($alertIdUpdate) && isset($alertsplImg) && isset($alertMainId)) {
//                            $tesmp = ' "alertsplId":"' . $alertsplId . '", "alertUpdateId":"' . $alertIdUpdate . '","alertsplImage":"' . $alertsplImg . '","alertMainId":"' . $alertMainId . '",';
//                        } elseif (isset($alertsplId) && isset($alertIdUpdate) && isset($alertsplImg) && isset($alertMainId)) {
//
//                            $tesmp = ' "alertsplId":"' . $alertsplId . '", "alertUpdateId":"' . $alertIdUpdate . '","alertsplImage":"' . $alertsplImg . '","alertMainId":"' . $alertMainId . '",';
//                        } elseif ((isset($alertsplId) && isset($alertIdUpdate)) || isset($alertIdUpdate)) {
//
//                            $tesmp = ' "alertsplId":"' . $alertsplId . '","alertUpdateId":"' . $alertIdUpdate . '",';
//                        } elseif (isset($alertsplId)) {
//
//                            $tesmp = ' "alertsplId":"' . $alertsplId . '",';
//                        } else {
//                            $tesmp = '';
//                        }
		
                if (isset($alertsplId) && ($alertsplId !=0) && isset($alertIdUpdate) && isset($alertsplImg) && isset($alertMainId)) {
                    $tesmp = ' "alertsplId":"' . $alertsplId . '", "alertUpdateId":"' . $alertIdUpdate . '","alertsplImage":"' . $alertsplImg . '","alertMainId":"' . $alertMainId . '",';
                } elseif (isset($alertsplId) && isset($alertIdUpdate) && isset($alertMainId)) {
                    $tesmp = ' "alertsplId":"' . $alertsplId . '", "alertUpdateId":"' . $alertIdUpdate . '","alertMainId":"' . $alertMainId . '",';
                } elseif (isset($alertsplId) && isset($alertIdUpdate)) {
                    $tesmp = ' "alertsplId":"' . $alertsplId . '","alertUpdateId":"' . $alertIdUpdate . '",';
                } elseif (isset($alertsplId) && isset($alertIdUpdate)) {
                    $tesmp = ' "alertsplId":"' . $alertsplId . '","alertUpdateId":"' . $alertIdUpdate . '",';
                } elseif (isset($alertsplId)) {
                    $tesmp = ' "alertsplId":"' . $alertsplId . '",';
                } 
				
				if(isset($valss[$i]['badges'])){
		    $badges = ',"badge":{"badgeId":"'.str_replace('"', '\"',$valss[$i]['badges']['badge_id']).'","badgeName":"'.str_replace('"', '\"',$valss[$i]['badges']['badge_name']).'","badgeDescription":"'.str_replace('"', '\"',$valss[$i]['badges']['badge_description']).'",
			"badgeThumbImageURL":"'.str_replace('"', '\"',$valss[$i]['badges']['badgeThumbImageURL']).'","badgeImageURL":"'.str_replace('"', '\"',$valss[$i]['badges']['badgeImageURL']).'","badgeEarned":"'.str_replace('"', '\"',$valss[$i]['badges']['badgeEarned']).'","venueId":"'.str_replace('"', '\"',$valss[$i]['badges']['venueId']).'","badgeUnlockedAtVenue":"'.str_replace('"', '\"',$valss[$i]['badges']['badgeUnlockedAtVenue']).'","badgeUnlockedAtTime":"'.str_replace('"', '\"',$valss[$i]['badges']['badgeUnlockedAtTime']).'"}';
		    $tesmp = '';//for unnecessary splid coming in response

		} else {
                    //$tesmp = '';
                    $badges = '';
                }

                $str_temp = '{
                        "id":"' . $id . '",
                        ' . $tesmp . '
                        "alertTitle":"' .str_replace('"', '\"',trim(preg_replace('/\s+/', ' ', $alerttitle))). '",
                        "alertDate":"' .str_replace('"', '\"',trim($alertDate)). '",
                        "alertDescription":"' .str_replace('"', '\"',trim(preg_replace('/\s+/', ' ', $alertDescription))). '",
                        "alertImageUrl":"' .str_replace('"', '\"',trim($alertImageUrl)). '",
                        "alertType":"' .str_replace('"', '\"',trim($alertType)). '"
			    '.$badges.'
                    }';
                $str = $str . $str_temp;
                $str = $str . ',';
            }
            $str = substr($str, 0, strlen($str) - 1);
            //}


            $response_str = response_repeat_string();
            $response_mess = '
            {
               ' . $response_str . '
               "Alerts":{
                   "errorCode":"' . $return_codes['Alerts']['SuccessCode'] . '",
                   "errorMsg":"' . $return_codes['Alerts']['SuccessDesc'] . '",
                   "CurrentAlertsCount":"' . $valss['count'] . '",
                   "TotalAlertsCount":"' . $total_count . '",
                   "Alerts":[' . $str . ']
               }
            }';
            // }
            // }
        } else {
            $response_mess = '
                {
           ' . response_repeat_string() . '
           "Alerts":{
              "errorCode":"' . $return_codes["Alerts"]["FailedToAddRecordCode"] . '",
              "errorMsg":"' . $return_codes["Alerts"]["FailedToAddRecordDesc"] . '"
              "CurrentAlertsCount":"' . $valss['count'] . '"
           }
        }';
        }
        return getValidJSON($response_mess);
    }

    /*  public function alertsClear
      Purpose    : To display alerts in json format
      Parameters : $xmlrequest       : Request array for alerts clear ,
      $response_message : ?
      Returns    : response in json format for alerts clear */

    public function alertsClear($response_message, $xmlrequest) {

        global $return_codes;
        $clearAlert = $this->clear_alert($xmlrequest);

        if ($clearAlert >= 1) {
            $response_str = response_repeat_string();
            $response_mess = '
                {
                   ' . $response_str . '
                   "AlertsClear":{
                      "errorCode":"' . $return_codes['AlertsClear']['SuccessCode'] . '",
                      "errorMsg":"' . $return_codes['AlertsClear']['SuccessDesc'] . '"
                   }
                }';
        } else {
            $response_mess = '
                                {
                   ' . response_repeat_string() . '
                   "AlertsClear":{
                      "errorCode":"' . $return_codes["AlertsClear"]["FailedToAddRecordCode"] . '",
                      "errorMsg":"' . $return_codes["AlertsClear"]["FailedToAddRecordDesc"] . '"
                       }
                    }';
        }
        writelog("Appearance:AlertsClear:", $response_mess, false);
        return getValidJSON($response_mess);
    }

    /*  function alerts_update()
      Purpose    : To remove the alert from list
      Parameters : $xmlrequest       : Request array of alerts update ,
      $response_message : ?
      Returns    : response in json format for alerts update */

    public function alerts_update($response_message, $xmlrequest) {

        global $return_codes;
        $updateAlert = $this->update_alert($xmlrequest);

        if ($updateAlert > 0) {

            $response_str = response_repeat_string();
            $response_mess = '
           {
           ' . $response_str . '
           "AlertsUpdate":{
                "errorCode":"' .str_replace('"', '\"',$return_codes["AlertsUpdate"]["SuccessCode"]). '",
                "errorMsg":"' .str_replace('"', '\"',$return_codes["AlertsUpdate"]["SuccessDesc"]). '",
                "AlertUpdated":"' .str_replace('"', '\"',$updateAlert['msgType']). ' is updated successfully' . '"
                }
        }';
        } else {
            $response_mess = '
                        {
           ' . response_repeat_string() . '
           "AlertsUpdate":{
              "errorCode":"' . $return_codes["AlertsUpdate"]["FailedToAddRecordCode"] . '",
              "errorMsg":"' . $return_codes["AlertsUpdate"]["FailedToAddRecordDesc"] . '"
           }
        }';
        }
        return getValidJSON($response_mess);
    }

    /*  function hotpress_alert()
      Purpose    : To move from alerts to hotpress
      Parameters : $xmlrequest       : Request array of hotpress alerts ,
      $response_message : ?
      Returns    : response in json format for hotpress alerts */

    public function hotpress_alert($response_message, $xmlrequest) {

        global $return_codes;
	$pageNumber = $xmlrequest['HotPressAlert']['pageNumber'];
        $hotpressAlert = $this->get_hotpress_alert($xmlrequest,$pageNumber,20);

        $count = $hotpressAlert['child']['count'];
        $str = '';
        $strp = '';
        if (!empty($hotpressAlert) && ($count > 0)) {
	//for parent comment
	$input = isset($hotpressAlert['parent'][0]['body']) && ($hotpressAlert['parent'][0]['body']) ? $hotpressAlert['parent'][0]['body'] : NULL;
                $input = str_replace('\\', '', $input);
                if (preg_match(REGEX_URL, $input, $url)) {
                    $postType = 'url';
                } else {
                    $postType = 'text';
                }
                $date = isset($hotpressAlert['parent'][0]['date']) && ($hotpressAlert['parent'][0]['date']) ? time_difference($hotpressAlert['parent'][0]['date']) : NULL; //date("d/m/y : H:i:s", $postinfo[$i]['date'])


                    $width_link_image = NULL;
                    $height_link_image = NULL;
                    if (is_readable($this->local_folder . $hotpressAlert['parent'][0]['link_image']))
                        list($width_link_image, $height_link_image) = (isset($hotpressAlert['parent'][0]['link_image']) && (strlen($hotpressAlert['parent'][0]['link_image']) > 7)) ? getimagesize($this->local_folder . $hotpressAlert['parent'][0]['link_image']) : NULL;

                    $width_image_link = NULL;
                    $height_image_link = NULL;
                    if (is_readable($this->local_folder . $hotpressAlert['parent'][0]['image_link']))
                        list($width_image_link, $height_image_link) = (isset($hotpressAlert['parent'][0]['image_link']) && (strlen($hotpressAlert['parent'][0]['image_link']) > 7)) ? getimagesize($this->local_folder . $hotpressAlert['parent'][0]['image_link']) : NULL;

						$hotpressAlert['parent'][0]['body']=str_replace("\\","",$hotpressAlert['parent'][0]['body']);
						$hotpressAlert['parent'][0]['body']=htmlspecialchars_decode($hotpressAlert['parent'][0]['body']); 
                    $hotpressAlert['parent'][0]['link_image'] = (isset($hotpressAlert['parent'][0]['link_image']) && (strlen($hotpressAlert['parent'][0]['link_image']) > 7)) ? $this->profile_url . $hotpressAlert['parent'][0]['link_image'] : NULL;
                    $hotpressAlert['parent'][0]['image_link'] = (isset($hotpressAlert['parent'][0]['image_link']) && (strlen($hotpressAlert['parent'][0]['image_link']) > 7)) ? $this->profile_url . $hotpressAlert['parent'][0]['image_link'] : NULL;
                    $hotpressAlert['parent'][0]['photo_b_thumb'] = (isset($hotpressAlert['parent'][0]['photo_b_thumb']) && (strlen($hotpressAlert['parent'][0]['photo_b_thumb']) > 7)) ? $this->profile_url . $hotpressAlert['parent'][0]['photo_b_thumb'] : $this->profile_url . default_images($hotpressAlert['parent'][0]['gender'], $hotpressAlert['parent'][0]['profile_type']);
		    $postVia = ((isset($hotpressAlert['parent'][0]['post_via'])) && ($hotpressAlert['parent'][0]['post_via'])) ? "iPhone" : "";
	    $str_temp = '{
            "authorID":"' .str_replace('"', '\"',$hotpressAlert['parent'][0]['mem_id']). '",
            "postId":"' .str_replace('"', '\"',$hotpressAlert['parent'][0]['id']). '",
            "authorName":"' .str_replace('"', '\"',$hotpressAlert['parent'][0]['profilenam']). '",
            "authorImgURL":"' .str_replace('"', '\"',$hotpressAlert['parent'][0]['photo_b_thumb']). '",
            "likeCount":"' .str_replace('"', '\"',$hotpressAlert['parent'][0]['likecount']). '",
            "postBody":"' .str_replace('"', '\"',$hotpressAlert['parent'][0]['body']). '",
            "postType":"' .str_replace('"', '\"',$postType). '",
            "uploadedImage":"' .str_replace('"', '\"',$hotpressAlert['parent'][0]['image_link']). '",
            "width_image_link":"' .str_replace('"', '\"',$width_image_link). '",
            "height_image_link":"' .str_replace('"', '\"',$height_image_link ). '",
            "link_url":"' .str_replace('"', '\"',$hotpressAlert['parent'][0]['link_url']). '",
            "postTimestamp":"' .str_replace('"', '\"',$date). '",
            "youtubeLink":"' .str_replace('"', '\"',$hotpressAlert['parent'][0]['youtubeLink']). '",
            "link_image":"' .str_replace('"', '\"',$hotpressAlert['parent'][0]['link_image']). '",
            "width_link_image":"' .str_replace('"', '\"',$width_link_image). '",
            "height_link_image":"' .str_replace('"', '\"',$height_link_image). '",
            "postVia":"'.str_replace('"', '\"',$postVia).'",
	    "commentsCount": "' .str_replace('"', '\"',$hotpressAlert['parent']['totalCount']). '",
            "currentCommentsCount": "' .str_replace('"', '\"',$hotpressAlert['parent']['currentCount']). '"
        }';
                    $strp = $strp . $str_temp;
//for child count
		    if ($count > 0) {
		for ($i = 0; $i < $count; $i++) {
		$input = isset($hotpressAlert['child'][$i]['body']) && ($hotpressAlert['child'][$i]['body']) ? $hotpressAlert['child'][$i]['body'] : NULL;
                $input = str_replace('\\', '', $input);
                if (preg_match(REGEX_URL, $input, $url)) {
                    $postType = 'url';
                } else {
                    $postType = 'text';
                }
                $date = isset($hotpressAlert['child'][$i]['date']) && ($hotpressAlert['child'][$i]['date']) ? time_difference($hotpressAlert['child'][$i]['date']) : NULL; //date("d/m/y : H:i:s", $postinfo[$i]['date'])


                    $width_link_image = NULL;
                    $height_link_image = NULL;
                    if (is_readable($this->local_folder . $hotpressAlert['child'][$i]['link_image']))
                        list($width_link_image, $height_link_image) = (isset($hotpressAlert['child'][$i]['link_image']) && (strlen($hotpressAlert['child'][$i]['link_image']) > 7)) ? getimagesize($this->local_folder . $hotpressAlert['child'][$i]['link_image']) : NULL;

                    $width_image_link = NULL;
                    $height_image_link = NULL;
                    if (is_readable($this->local_folder . $hotpressAlert['child'][$i]['image_link']))
                        list($width_image_link, $height_image_link) = (isset($hotpressAlert['child'][$i]['image_link']) && (strlen($hotpressAlert['child'][$i]['image_link']) > 7)) ? getimagesize($this->local_folder . $hotpressAlert['child'][$i]['image_link']) : NULL;

                    $hotpressAlert['child'][$i]['link_image'] = (isset($hotpressAlert['child'][$i]['link_image']) && (strlen($hotpressAlert['child'][$i]['link_image']) > 7)) ? $this->profile_url . $hotpressAlert['child'][$i]['link_image'] : NULL;
                    $hotpressAlert['child'][$i]['image_link'] = (isset($hotpressAlert['child'][$i]['image_link']) && (strlen($hotpressAlert['child'][$i]['image_link']) > 7)) ? $this->profile_url . $hotpressAlert['child'][$i]['image_link'] : NULL;
                    $hotpressAlert['child'][$i]['photo_b_thumb'] = (isset($hotpressAlert['child'][$i]['photo_b_thumb']) && (strlen($hotpressAlert['child'][$i]['photo_b_thumb']) > 7)) ? $this->profile_url . $hotpressAlert['child'][$i]['photo_b_thumb'] : $this->profile_url . default_images($hotpressAlert[$i]['gender'], $hotpressAlert[$i]['profile_type']);
		    $postViaChild = ((isset($hotpressAlert['child'][$i]['post_via'])) && ($hotpressAlert['child'][$i]['post_via'])) ? "iPhone" : "";
		    $str_temp = '{
            "commentId":"'.str_replace('"', '\"',$hotpressAlert['child'][$i]['id']).'",
            "authorID":"'.str_replace('"', '\"',$hotpressAlert['child'][$i]['mem_id']).'",
            "authorProfileImgURL":"'.str_replace('"', '\"',$hotpressAlert['child'][$i]['photo_b_thumb']).'",
            "authorName":"'.str_replace('"', '\"',$hotpressAlert['child'][$i]['profilenam']).'",
            "commentBody":"'.str_replace('"', '\"',trim($hotpressAlert['child'][$i]['body'])).'",
            "commentType":"' .str_replace('"', '\"',$postType). '",
            "commentTimestamp":"' .str_replace('"', '\"',$date). '",
	    "postVia":"'.str_replace('"', '\"',$postViaChild).'"
        }';
		    $str = $str . $str_temp;
		    $str = $str . ',';
		}
	    }
            $str = substr($str, 0, strlen($str) - 1);
            $response_str = response_repeat_string();
            $response_mess = '
        {
           ' . $response_str . '
           "HotPressAlert":{
                "errorCode":"' . $return_codes["HotPressAlert"]["SuccessCode"] . '",
                "errorMsg":"' . $return_codes["HotPressAlert"]["SuccessDesc"] . '",                
                "HotpressParentAlert":[' . $strp . '],
                "Hotpresschildcomment":[' . $str . ']
           }
        }';
        } else {
            $response_mess = '
                {
   ' . response_repeat_string() . '
       "HotPressAlert":{
          "errorCode":"' . $return_codes["HotPressAlert"]["FailedToAddRecordCode"] . '",
          "errorMsg":"' . $return_codes["HotPressAlert"]["FailedToAddRecordDesc"] . '",
           "HotpressParentAlert":[' . $strp . '],
        "Hotpresschildcomment":[' . $str . ']
            }
	  }';
        }
        return getValidJSON($response_mess);
    }

    /*  function comment_alert()
      Purpose    : To move from alerts to profile
      Parameters : $xmlrequest       : Request array of profile photo alerts ,
      $response_message : ?
      Returns    : response in json format for profile photo alerts */

    public function comment_alert($response_message, $xmlrequest) {

        global $return_codes;
	$pageNumber = $xmlrequest['CommentAlert']['pageNumber'];
        $commentAlert = $this->get_comment_alert($xmlrequest,$pageNumber,20);
//        print_r($commentAlert);
	$count = $commentAlert['child']['count'];
        $str = '';
        $strp = '';
        if (!empty($commentAlert)) {

                $input = isset($commentAlert['parent'][0]['testimonial']) && ($commentAlert['parent'][0]['testimonial']) ? $commentAlert['parent'][0]['testimonial'] : NULL;
                $input = str_replace('\\', '', $input);
                if (preg_match(REGEX_URL, $input, $url)) {
                    $postType = 'url';
                } else {
                    $postType = 'text';
                }

                $date = isset($commentAlert['parent'][0]['added']) && ($commentAlert['parent'][0]['added']) ? time_difference($commentAlert['parent'][0]['added']) : NULL; //date("d/m/y : H:i:s", $postinfo[$i]['date'])
                if ($commentAlert['parent'][0]['parent_tst_id'] == 0) {
                    $commentAlert['parent'][0]['testimonial'] = strip_tags($commentAlert['parent'][0]['testimonial'], "<br />");
                    $commentAlert['parent'][0]['testimonial'] = str_replace(array("\r\n", "\r", "\n", "<br />", "<br/>"), "\\n", $commentAlert['parent'][0]['testimonial']);

                    $width_link_image = NULL;
                    $height_link_image = NULL;
                    if (is_readable($this->local_folder . $commentAlert['parent'][0]['link_image']))
                        list($width_link_image, $height_link_image) = (isset($commentAlert['parent'][0]['link_image']) && (strlen($commentAlert['parent'][0]['link_image']) > 7)) ? getimagesize($this->local_folder . $commentAlert['parent'][0]['link_image']) : NULL;

                    $width_image_link = NULL;
                    $height_image_link = NULL;
                    if (is_readable($this->local_folder . $commentAlert['parent'][0]['image_link']))
                        list($width_image_link, $height_image_link) = (isset($commentAlert['parent'][0]['image_link']) && (strlen($commentAlert['parent'][0]['image_link']) > 7)) ? getimagesize($this->local_folder . $commentAlert['parent'][0]['image_link']) : NULL;

                    $commentAlert['parent'][0]['image_link'] = (isset($commentAlert['parent'][0]['image_link']) && (strlen($commentAlert['parent'][0]['image_link']) > 7)) ? $this->profile_url . $commentAlert['parent'][0]['image_link'] : NULL;
                    $commentAlert['parent'][0]['link_image'] = (isset($commentAlert['parent'][0]['link_image']) && (strlen($commentAlert['parent'][0]['link_image']) > 7)) ? $this->profile_url . $commentAlert['parent'][0]['link_image'] : NULL;
                    $commentAlert['parent'][0]['photo_b_thumb'] = $commentAlert['parent'][0]['photo_b_thumb'] = (isset($commentAlert['parent'][0]['photo_b_thumb']) && (strlen($commentAlert['parent'][0]['photo_b_thumb']) > 7)) ? $this->profile_url . $commentAlert['parent'][0]['photo_b_thumb'] : $this->profile_url . default_images($commentAlert['parent'][0]['gender'], $commentAlert['parent'][0]['profile_type']);
                    $postVia = ((isset($commentAlert['parent'][0]['post_via'])) && ($commentAlert['parent'][0]['post_via'])) ? "iPhone" : "";
		     
		    $str_temp = '{
            "postId":"' .str_replace('"', '\"',$commentAlert['parent'][0]['tst_id']). '",
            "authorID":"' .str_replace('"', '\"',$commentAlert['parent'][0]['mem_id']). '",
            "authorProfileImgURL":"' .str_replace('"', '\"',$commentAlert['parent'][0]['photo_b_thumb']). '",
            "authorName":"' .str_replace('"', '\"',$commentAlert['parent'][0]['profilenam']). '",
            "gender":"' .str_replace('"', '\"',$commentAlert['parent'][0]['gender']). '",
            "profileType":"' .str_replace('"', '\"',$commentAlert['parent'][0]['profile_type']). '",
            "postText":"' .str_replace('"', '\"',trim(preg_replace('/\s+/', ' ', $commentAlert['parent'][0]['testimonial']))). '",
            "postType":"' .str_replace('"', '\"',$postType). '",
            "uploadedImage":"' .str_replace('"', '\"',$commentAlert['parent'][0]['image_link']). '",
            "width_image_link":"' .str_replace('"', '\"',$width_image_link). '",
            "height_image_link":"' .str_replace('"', '\"',$height_image_link). '",
            "link_url":"' .str_replace('"', '\"',$commentAlert['parent'][0]['link_url']). '",
            "youtubeLink":"' .str_replace('"', '\"',$commentAlert['parent'][0]['youtubeLink']). '",
            "link_image":"' .str_replace('"', '\"',$commentAlert['parent'][0]['link_image']). '",
            "width_link_image":"' .str_replace('"', '\"',$width_link_image). '",
            "height_link_image":"' .str_replace('"', '\"',$height_link_image). '",
            "postTimestamp":"' .str_replace('"', '\"',$date). '",
            "postVia":"'.str_replace('"', '\"',$postVia).'",
            "commentsCount":"' .str_replace('"', '\"',$commentAlert['parent']['totalCount']). '"
            "currentCommentsCount":"' .str_replace('"', '\"',$commentAlert['parent']['totalCount']). '"
        }';
		}
                    $strp = $strp . $str_temp;
		
               for ($i = 0; $i < $count; $i++) {
		   
		   $input = isset($commentAlert['child'][$i]['testimonial']) && ($commentAlert['child'][$i]['testimonial']) ? $commentAlert['child'][$i]['testimonial'] : NULL;
                
               
		   $input = str_replace('\\', '', $input);
                if (preg_match(REGEX_URL, $input, $url)) {
                    $postType = 'url';
                } else {
                    $postType = 'text';
                }

                $date = isset($commentAlert['child'][$i]['added']) && ($commentAlert['child'][$i]['added']) ? time_difference($commentAlert['child'][$i]['added']) : NULL; //date("d/m/y : H:i:s", $postinfo[$i]['date'])
                
                    $commentAlert['child'][$i]['testimonial'] = strip_tags($commentAlert['child'][$i]['testimonial'], "<br />");
                    $commentAlert['child'][$i]['testimonial'] = str_replace(array("\r\n", "\r", "\n", "<br />", "<br/>"), "\\n", $commentAlert['child'][$i]['testimonial']);
//print_r($commentAlert['child']);
                    $width_link_image = NULL;
                    $height_link_image = NULL;
                    if (is_readable($this->local_folder . $commentAlert['child'][$i]['link_image']))
                        list($width_link_image, $height_link_image) = (isset($commentAlert['child'][$i]['link_image']) && (strlen($commentAlert['child'][$i]['link_image']) > 7)) ? getimagesize($this->local_folder . $commentAlert['child'][$i]['link_image']) : NULL;

                    $width_image_link = NULL;
                    $height_image_link = NULL;
                    if (is_readable($this->local_folder . $commentAlert['child'][$i]['image_link']))
                        list($width_image_link, $height_image_link) = (isset($commentAlert['child'][$i]['image_link']) && (strlen($commentAlert[$i]['image_link']) > 7)) ? getimagesize($this->local_folder . $commentAlert[$i]['image_link']) : NULL;

                    $commentAlert['child'][$i]['image_link'] = (isset($commentAlert[$i]['image_link']) && (strlen($commentAlert[$i]['image_link']) > 7)) ? $this->profile_url . $commentAlert[$i]['image_link'] : NULL;
                    $commentAlert['child'][$i]['link_image'] = (isset($commentAlert[$i]['link_image']) && (strlen($commentAlert[$i]['link_image']) > 7)) ? $this->profile_url . $commentAlert[$i]['link_image'] : NULL;
                    $commentAlert['child'][$i]['testimonial'] = strip_tags($commentAlert['child'][$i]['testimonial'], "<br />");
                    $commentAlert['child'][$i]['testimonial'] = str_replace(array("\r\n", "\r", "\n", "<br />", "<br/>"), "\\n", $commentAlert['child'][$i]['testimonial']);
                    $commentAlert['child'][$i]['photo_b_thumb'] = (isset($commentAlert['child'][$i]['photo_b_thumb']) && (strlen($commentAlert['child'][$i]['photo_b_thumb']) > 7)) ? $this->profile_url . $commentAlert['child'][$i]['photo_b_thumb'] : $this->profile_url . default_images($commentAlert['child'][$i]['gender'], $commentAlert['child'][$i]['profile_type']);
                    $postVia = ((isset($commentAlert['child'][$i]['post_via'])) && ($commentAlert['child'][$i]['post_via'])) ? "iPhone" : "";

		    $str_temp = '{
            "postId":"' .str_replace('"', '\"',$commentAlert['child'][$i]['tst_id']). '",
            "authorID":"' .str_replace('"', '\"',$commentAlert['child'][$i]['mem_id']). '",
            "authorProfileImgURL":"' .str_replace('"', '\"',$commentAlert['child'][$i]['photo_b_thumb']). '",
            "authorName":"' .str_replace('"', '\"',$commentAlert['child'][$i]['profilenam']). '",
            "gender":"' .str_replace('"', '\"',$commentAlert['child'][$i]['gender']). '",
            "profileType":"' .str_replace('"', '\"',$commentAlert['child'][$i]['profile_type']). '",
            "postText":"' .str_replace('"', '\"',trim(preg_replace('/\s+/', ' ', $commentAlert['child'][$i]['testimonial']))). '",
            "postType":"' .str_replace('"', '\"',$postType). '",
            "postTimestamp":"' .str_replace('"', '\"',$date). '",
            "postVia":"'.str_replace('"', '\"',$postVia).'"
        }';
                    $str = $str . $str_temp;
                    $str = $str . ',';
                
            }
            $str = substr($str, 0, strlen($str) - 1);
            $response_str = response_repeat_string();
            $response_mess = '
        {
           ' . $response_str . '
           "CommentAlert":{
                "errorCode":"' . $return_codes["CommentAlert"]["SuccessCode"] . '",
                "errorMsg":"' . $return_codes["CommentAlert"]["SuccessDesc"] . '",                
                "CommentParentAlert":[' . $strp . '],
                "Commentchildcomment":[' . $str . ']
           }
        }';
        } else {
            $response_mess = '
                {
       ' . response_repeat_string() . '
       "CommentAlert":{
           "errorCode":"' . $return_codes["CommentAlert"]["FailedToAddRecordCode"] . '",
           "errorMsg":"' . $return_codes["CommentAlert"]["FailedToAddRecordDesc"] . '",
           "CommentParentAlert":[' . $strp . '],
           "Commentchildcomment":[' . $str . ']
   }
	  }';
        }
        return getValidJSON($response_mess);
    }

    /*  function update_table()
      Purpose    : ?
      Parameters : $xmlrequest : Request array of update table ,
      $response_message : ?
      Returns    : ? */

    public function update_table($response_message, $xmlrequest) {

        global $return_codes;
        $uTable = $this->update_table($xmlrequest);
    }

    /*  function displayPhotoTagAlert()
      Purpose    : to display tag alerts detail information
      Parameters : $xmlrequest       : Request array of tag alerts ,
      $response_message : ?
      Returns    : json response for tag alerts */

    public function displayPhotoTagAlert($response_message, $xmlrequest) {

        global $return_codes;
        $userinfo = array();
        $userinfo = $this->display_photo_tag_alert($xmlrequest);

        if (isset($response_message['DisplayPhotoTagAlert']['SuccessCode']) && ($response_message['DisplayPhotoTagAlert']['SuccessCode'] == '000')) {

            $width = NULL;
            $height = NULL;
            if (is_readable($this->local_folder . $userinfo['photo']['photo_mid']))
                list($width, $height) = (isset($userinfo['photo']['photo_mid']) && (strlen($userinfo['photo']['photo_mid']) > 7)) ? getimagesize($this->local_folder . $userinfo['photo']['photo_mid']) : NULL;


            $userinfo['photo']['photo_mid'] = isset($userinfo['photo']['photo_mid']) && (strlen($userinfo['photo']['photo_mid']) > 7) ? $this->profile_url . $userinfo['photo']['photo_mid'] : NULL;
            $userinfo['message_info']['subject'] = str_replace('\\', "", $userinfo['message_info']['subject']);
            $userinfo['message_info']['subject'] = str_replace(array("\r\n", "\r", "\n", "<br />", "<br/>"), "\\n", $userinfo['message_info']['subject']);
            $userinfo['message_info']['subject'] = strip_tags($userinfo['message_info']['subject']);
            $userinfo['message_info']['subject'] = str_replace(array("\""), "", $userinfo['message_info']['subject']);

		$userinfo['photo']['photo_owner_id'] = (isset($userinfo['photo']['album_owner_id']) && !empty($userinfo['photo']['album_owner_id'])) ? $userinfo['photo']['album_owner_id'] : NULL;

			 $str = '
             "photo":"' .str_replace('"', '\"',$userinfo['photo']['photo_mid']). '",
			 "photo_owner_id":"' .str_replace('"', '\"',$userinfo['photo']['photo_owner_id']). '",
             "width":"' .str_replace('"', '\"',$width). '",
             "height":"' .str_replace('"', '\"',$height). '",
             "subject":"' .str_replace('"', '\"',$userinfo['message_info']['subject']). '",
             "date":"' .str_replace('"', '\"',date("F j, Y", $userinfo['message_info']['date'])). '",
';
            $response_mess = '
               {
   ' . response_repeat_string() . '
    "DisplayPhotoTagAlert":{
           ' . $str . '
           "errorCode":"' . $response_message["DisplayPhotoTagAlert"]["SuccessCode"] . '",
           "errorMsg":"' . $response_message["DisplayPhotoTagAlert"]["SuccessDesc"] . '"

   }
	  }';
        } else {

            $response_mess = '
                {
   ' . response_repeat_string() . '
   "DisplayPhotoTagAlert":{
      "errorCode":"' . $return_codes["DisplayPhotoTagAlert"]["NoRecordErrorCode"] . '",
      "errorMsg":"' . $return_codes["DisplayPhotoTagAlert"]["NoRecordErrorDesc"] . '"

             }
	  }';
        }
        return getValidJSON($response_mess);
    }

    /*  function respondPhotoTagAlerts()
      Purpose    : ?
      Parameters : $xmlrequest : Request array of respond Photo Tag Alerts ,
      $response_message : ?
      Returns    : json response for respond Photo Tag Alerts */

    public function respondPhotoTagAlerts($response_message, $xmlrequest) {

        global $return_codes;
        $userinfo = array();
        $userinfo = $this->respond_photo_tag_alerts($xmlrequest);

        if ((isset($userinfo['RespondPhotoTagAlerts']['successful_fin'])) && (!$userinfo['RespondPhotoTagAlerts']['successful_fin'])) {
            $obj_error = new Error();
            $response_message = $obj_error->error_type("RespondPhotoTagAlerts", $userinfo);

            $userinfocode = $response_message['RespondPhotoTagAlerts']['ErrorCode'];
            $userinfodesc = $response_message['RespondPhotoTagAlerts']['ErrorDesc'];
            $response_mess = $response_mess = get_response_string("RespondPhotoTagAlerts", $userinfocode, $userinfodesc);
            return getValidJSON($response_mess);
        }

        if ((isset($userinfo['RespondPhotoTagAlerts']['successful_fin'])) && ($userinfo['RespondPhotoTagAlerts']['successful_fin'])) {
            $response_mess = '
               {
           ' . response_repeat_string() . '
            "RespondPhotoTagAlerts":{
                   "errorCode":"' . $response_message["RespondPhotoTagAlerts"]["SuccessCode"] . '",
                   "errorMsg":"' . $response_message["RespondPhotoTagAlerts"]["SuccessDesc"] . '"
           }
	  }';
        } else {
            $response_mess = '
                {
           ' . response_repeat_string() . '
           "RespondPhotoTagAlerts":{
              "errorCode":"' . $return_codes["RespondPhotoTagAlerts"]["NoRecordErrorCode"] . '",
              "errorMsg":"' . $return_codes["RespondPhotoTagAlerts"]["NoRecordErrorDesc"] . '"
           }
	  }';
        }
        return getValidJSON($response_mess);
    }

}

?>