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
  File-name     : appearance.class.php
  Directory Path: $/MySNL/Deliverables/Code/MySNL_WebServiceV2/classes/
  Author        : Rajesh Bakade
  Date          : 11/08/2011
  Modified By   : N/A
  Date          : N/A

  Include Files : none
  CSS File(s)   : none

  Functions Used
  Javascript   :  none
  PHP          :

  DataBase Table(s)   : announce_arrival,members,app_entourage_comments,tag_ent_list,tag_event_list bulletin,
  event_entourage_chkin,users_reward event_list

  Global Variable(s)  : $return_codes
  Constant(s)         : PROFILE_IMAGE_SITEURL , LOCAL_FOLDER

  Description         :  File to display the Latest Notifications for the perticular user from different module.
  Reviwed By  :
  Reviwed Date:
 * ************************************************************************************** */
/*
  class Appearance is mainly used for displaying recently appearances made by nightsite profileusers
 */

class Appearance {

    var $profile_url = PROFILE_IMAGE_SITEURL;
    var $local_folder = LOCAL_FOLDER;

//    var $latitude1 = '34.102223';
//    var $longitude1 = '-118.329125';

    /*  function appearance_list()
      Purpose    : To get the latest appearances made by users on different venues
      Parameters : $xmlrequest : Request array for appearances made
      $pageNumber : Current page Number
      $limit      : no. of results to be display on each page
      Returns    : list of appearances made by nightsite profile users */

    function appearance_list($xmlRequest, $pagenumber, $limit) {

        //if (DEBUG)
        writelog("appearance.class.php :: appearance_list() :: ", "Starts Here ", false);

        $lowerLimit = isset($pagenumber) ? ($pagenumber - 1) * $limit : 0;

        $appList = array();
        $userId = mysql_real_escape_string($xmlRequest['AppEntourageList']['userId']);
        //get various appearances
//        echo $queryAppearance = "select DISTINCT announce_arrival.id,announce_arrival.user_id,announce_arrival.date,announce_arrival.time,announce_arrival.venue_id,announce_arrival.read from announce_arrival WHERE user_id IN (SELECT distinct a.user_id FROM announce_arrival AS a WHERE a.user_id !=  '$userId') ORDER BY date DESC,time DESC LIMIT $lowerLimit,$limit";
//07_02_2012       $queryAppearance = "SELECT DISTINCT announce_arrival.id,announce_arrival.user_id,announce_arrival.date,announce_arrival.time,announce_arrival.venue_id FROM announce_arrival WHERE user_id IN (SELECT DISTINCT n2.mem_id AS frnd FROM network n1,network n2 WHERE n1.frd_id=n2.mem_id AND n1.mem_id='$userId' AND n2.frd_id='$userId') AND announce_arrival.user_id !='$userId' ORDER BY date DESC,time DESC LIMIT $lowerLimit,$limit";
       $queryAppearance = "SELECT a.id,a.user_id,a.date,a.time,a.venue_id
FROM (SELECT MAX(announce_arrival.id) AS id FROM announce_arrival WHERE user_id IN (SELECT DISTINCT n2.mem_id AS frnd FROM network n1,network n2 WHERE n1.frd_id=n2.mem_id AND n1.mem_id='$userId' AND n2.frd_id='$userId') AND announce_arrival.user_id !='$userId' GROUP BY announce_arrival.user_id ORDER BY id DESC)
AS t JOIN announce_arrival AS a ON t.id=a.id LIMIT $lowerLimit,$limit";

        //if (DEBUG) 
        writelog("appearance.class.php :: appearance_list() :: Query to get Appearances list : ", $queryAppearance, false);
        $appList = execute_query($queryAppearance, true, "select");

        if (!empty($appList)) {
            //get total appearance count
//07_02_2012            $queryTotalAppearance = "select COUNT(DISTINCT user_id,date,venue_id) as cnt from announce_arrival WHERE user_id IN (SELECT distinct a.user_id FROM announce_arrival AS a) ORDER BY date DESC,time DESC ";
            $queryTotalAppearance = "SELECT COUNT(a.id) as cnt FROM (SELECT MAX(announce_arrival.id) AS id FROM announce_arrival WHERE user_id IN (SELECT DISTINCT n2.mem_id AS frnd FROM network n1,network n2 WHERE n1.frd_id=n2.mem_id AND n1.mem_id='$userId' AND n2.frd_id='$userId') AND announce_arrival.user_id !='$userId' GROUP BY announce_arrival.user_id ORDER BY id DESC)
		AS t JOIN announce_arrival AS a ON t.id=a.id";
            //if (DEBUG)
            writelog("appearance.class.php :: appearance_list() :: Query to get Total count of Appearances list : ", $queryTotalAppearance, false);
            $exeTotal = execute_query($queryTotalAppearance, false, "select");
            $user_detail = array();
            foreach ($appList as $kk => $appListInfo) {
                if (!empty($appListInfo['user_id']) && is_array($appListInfo)) {
                    /* start of user read page */
                    $getAppearanceInfo = execute_query("select user_update_read from app_user_update where app_id='" . $appListInfo['id'] . "'", FALSE, "select");
                    if (empty($getAppearanceInfo['user_update_read'])) {
                        $appList[$kk]['read'] = '0';
                    } else {
                        $getAppUsersUpdate = $getAppearanceInfo['user_update_read'];
                        if (isset($getAppUsersUpdate)) {
                            $explode = explode(",", $getAppUsersUpdate);
                            $arrCount = count($explode);
                            unset($explode[$arrCount - 1]);
                            $searchExistanceOfUserId = array_search($userId, $explode);
						if (is_int($searchExistanceOfUserId)) {
                                $appList[$kk]['read'] = '1';
                            } else {
                                $appList[$kk]['read'] = '0';
                            }
                        }
                    }
                    /* end of user read page */
                    $queryAppinfo = "select m1.is_facebook_user,m1.gender,m1.profile_type,m1.profilenam,m1.photo_b_thumb FROM members as m1 WHERE m1.mem_id='" . $appListInfo['user_id'] . "'";
                    $queryAppinfo1 = "select m1.is_facebook_user,m1.gender,m1.profile_type,m1.profilenam,m1.photo_b_thumb FROM members as m1 WHERE m1.mem_id='" . $appListInfo['venue_id'] . "'";
                    //$queryAppinfo2 = "select id FROM announce_arrival WHERE venue_id='" . $appListInfo['venue_id'] . "' AND user_id='" . $appListInfo['user_id'] . "' AND date = '" . $appListInfo['date'] . "' ORDER BY time DESC LIMIT 0,1";

                    $exeAppinfo = execute_query($queryAppinfo, false, "select");
                    $exeAppinfo1 = execute_query($queryAppinfo1, false, "select");
                   // $exeAppinfo2 = execute_query($queryAppinfo2, false, "select");

                    $user_detail['gender'] = isset($exeAppinfo['gender']) && ($exeAppinfo['gender']) ? $exeAppinfo['gender'] : NULL;
                    $user_detail['profile_type'] = isset($exeAppinfo['profile_type']) && ($exeAppinfo['profile_type']) ? $exeAppinfo['profile_type'] : NULL;
                    $user_detail['user_profilename'] = isset($exeAppinfo['profilenam']) && ($exeAppinfo['profilenam']) ? $exeAppinfo['profilenam'] : NULL;
                    $user_detail['user_photo_thumb'] = isset($exeAppinfo['photo_b_thumb']) && ($exeAppinfo['photo_b_thumb']) ? $exeAppinfo['photo_b_thumb'] : NULL;
                    $user_detail['user_is_facebook_user'] = isset($exeAppinfo['is_facebook_user']) && ($exeAppinfo['is_facebook_user']) ? $exeAppinfo['is_facebook_user'] : NULL;
                    $user_detail['venue_profilename'] = isset($exeAppinfo1['profilenam']) && ($exeAppinfo1['profilenam']) ? $exeAppinfo1['profilenam'] : NULL;
                    $user_detail['venue_photo_thumb'] = isset($exeAppinfo1['photo_b_thumb']) && ($exeAppinfo1['photo_b_thumb']) ? $exeAppinfo1['photo_b_thumb'] : NULL;
                    $appList[$kk]['id'] = $appListInfo['id'];//isset($exeAppinfo2['id']) && ($exeAppinfo2['id']) ? $exeAppinfo2['id'] : NULL;
                    $queryStatusCommentList = "SELECT COUNT(aec.id) as cnt FROM app_entourage_comments as aec , members as mem WHERE aec.announce_arrival_id='" . $appListInfo['id'] . "' and aec.comment_by_id=mem.mem_id ORDER BY date DESC , time DESC ";
                    $exeStatusCommentList['cnt'] = execute_query($queryStatusCommentList, false, "select");
                    $appList[$kk]['comment'] = isset($exeStatusCommentList['cnt']) && $exeStatusCommentList['cnt'] ? $exeStatusCommentList['cnt'] : NULL;
                    $appList[$kk]['userInfo'] = $user_detail;
                }
            }
            $appList['Total'] = isset($exeTotal['cnt']) && ($exeTotal['cnt']) ? $exeTotal['cnt'] : NULL;

            return $appList;
        } else {
            return array();
        }
    }

//end of appearance_list()

    /*  function appearance_entourage_status()
      Purpose    : display the information about appearances made
      Parameters : $xmlrequest : Request array for notifications
      Returns    : appearance detail information */

    function appearance_entourage_status($xmlRequest) {

	//if (DEBUG)
	writelog("appearance.class.php :: appearance_entourage_status() :: ", "Starts Here ", false);

	$appEntourageStatus = array();
	$checkedInUserid = mysql_real_escape_string($xmlRequest['AppEntourageStatus']['checkedInUserId']);
	$venueId = mysql_real_escape_string($xmlRequest['AppEntourageStatus']['venueId']);
	$userId = mysql_real_escape_string($xmlRequest['AppEntourageStatus']['userId']);
	$announceId = mysql_real_escape_string($xmlRequest['AppEntourageStatus']['announceId']);
// get information about appearance
	$queryAnnounceArrival = "SELECT distinct aa.id,aa.venue_id,aa.user_id,aa.wait_in_line,aa.ratio,aa.music,aa.energy,aa.date,aa.time,aa.comment, m.profilenam,m.photo_b_thumb,m.is_facebook_user,m.profile_type,m.gender FROM announce_arrival as aa,members as m WHERE aa.user_id='$checkedInUserid' AND aa.venue_id='$venueId' AND aa.id='$announceId' AND aa.user_id=m.mem_id ORDER BY date DESC , time DESC Limit 0,1";


	// if (DEBUG)
	writelog("appearance.class.php :: appearance_entourage_status() :: Query to get Appearances Entourage Status : ", $queryAnnounceArrival, false);
	$appEntourageStatus = execute_query($queryAnnounceArrival, false, "select");
	// *****Query for read and unread messages.*****

	if (!empty($appEntourageStatus)) {
//get information about venue
	    $queryVenueName = "SELECT is_facebook_user,gender,profile_type,profilenam,photo_b_thumb FROM members WHERE mem_id='$venueId'";

	    // if (DEBUG)
	    writelog("appearance.class.php :: appearance_entourage_status() :: Query to get Appearances Venue Info : ", $appEntourageStatus, false);

	    $exeVenueName = execute_query($queryVenueName, false, "select");
	    $appEntourageStatus['venueName'] = isset($exeVenueName['profilenam']) && ($exeVenueName['profilenam']) ? $exeVenueName['profilenam'] : NULL;
//get tag entourage
	    $tagEntList = "SELECT mm.is_facebook_user,mm.mem_id,mm.profilenam,mm.photo_b_thumb,mm.gender,mm.profile_type FROM tag_ent_list as tel, members as mm WHERE tel.venue_id='$venueId' AND tel.user_id='$checkedInUserid' AND tel.ent_id=mm.mem_id ORDER BY date DESC";
	    $appEntourageStatus['tag_ent'] = execute_query($tagEntList, true, "select");

	    //if (DEBUG)
	    writelog("appearance.class.php :: appearance_entourage_status() :: Query to get Appearances : ", $tagEntList, false);
//get tag events
	    $tagEventList = "SELECT el.even_id,el.even_title,el.even_img FROM tag_event_list as tel,event_list as el WHERE tel.venue_id='$venueId' AND tel.user_id='$checkedInUserid' AND tel.event_id=el.even_id ORDER BY date DESC";
	    $appEntourageStatus['tag_event'] = execute_query($tagEventList, true, "select");

	    //if (DEBUG)
	    writelog("appearance.class.php :: appearance_entourage_status() :: Query to get Appearances : ", $tagEventList, false);

	    /*	     * ****comment start****** */
//get comments on that appearance
	    $get_comment_id = execute_query('SELECT id from app_entourage_comments WHERE announce_arrival_id = "' . $announceId . '"', false, 'select');
	    if (!empty($get_comment_id)) {
		//get comments on that appearance
		$queryStatusCommentList = "SELECT aec.id,aec.comment,aec.date,aec.time,aec.post_via,mem.profilenam,mem.photo_b_thumb,mem.mem_id,mem.profile_type,mem.is_facebook_user,mem.gender FROM app_entourage_comments as aec , members as mem WHERE announce_arrival_id = '" . $announceId . "' and aec.comment_by_id=mem.mem_id ORDER BY date DESC , time DESC";
		$exeStatusCommentList = execute_query($queryStatusCommentList, true, "select");

		$invt = array();
		$commentcount = 0;
		foreach ($exeStatusCommentList as $kk => $commentList) {
		    if (isset($commentList['id'])) {
//get total comments
			$totalChildComment = "select COUNT(aec.id) as cnt from app_entourage_comments as aec where aec.parent_id='" . $commentList['id'] . "'";
			$exefinalTotalReplies = execute_query($totalChildComment, false, "select");
		    }

		    if (isset($commentList['mem_id']) && ($commentList['mem_id'])) {
			$commentcount++;
			$invt['id'] = isset($commentList['id']) && ($commentList['id']) ? $commentList['id'] : NULL;
			$invt['mem_id'] = isset($commentList['mem_id']) && ($commentList['mem_id']) ? $commentList['mem_id'] : NULL;
			$invt['comment'] = isset($commentList['comment']) && ($commentList['comment']) ? $commentList['comment'] : NULL;
			$invt['profilenam'] = isset($commentList['profilenam']) && ($commentList['profilenam']) ? $commentList['profilenam'] : NULL;
			$invt['photo_b_thumb'] = isset($commentList['photo_b_thumb']) && ($commentList['photo_b_thumb']) ? $commentList['photo_b_thumb'] : NULL;
			$invt['date'] = isset($commentList['date']) && ($commentList['date']) ? $commentList['date'] : NULL;
			$invt['time'] = isset($commentList['time']) && ($commentList['time']) ? $commentList['time'] : NULL;
			$invt['post_via'] = isset($commentList['post_via']) && ($commentList['post_via']) ? $commentList['post_via'] : NULL;
			$invt['totalReply']['cnt'] = $exefinalTotalReplies['cnt'];
			$appEntourageStatus['usercomment'][$kk] = $invt;
		    }
		}
		$appEntourageStatus['usercomment']['count'] = isset($commentcount) ? $commentcount : 0;
	    } else {
		$appEntourageStatus['usercomment'] = array();
		$appEntourageStatus['usercomment']['count'] = 0;
	    }

	    /*	     * ****comment end****** */
	    /*	     * ******uploaded image +original comment if any   Start******** */
	    $get_uploaded_image = execute_query("SELECT pa.photo_id,pa.album_id,bt.link_image FROM bulletin AS bt,photo_album AS pa WHERE bt.appearance_id = '$announceId' AND bt.photo_album_id = pa.photo_id", false, "select");
	    $appEntourageStatus['uploaded_image'] = $get_uploaded_image['link_image'];
	    $appEntourageStatus['uploadedImage_albumID'] = $get_uploaded_image['album_id'];
	    $appEntourageStatus['uploadedImage_photoID'] = $get_uploaded_image['photo_id'];
	    /*	     * ******uploaded image +original comment if any   Stop ******** */

	    writelog("Appearance:appearance_list:", $exeVenueName, true);
	    writelog("Appearance:appearance_list:", $appEntourageStatus['tag_ent'], true);
	    writelog("Appearance:appearance_list:", $appEntourageStatus['tag_event'], true);
	    writelog("appearance.class.php :: appearance_list() :: ", "End Here ", false);
	    $getAppearanceInfo = execute_query("select user_update_read from app_user_update where app_id='$announceId'", FALSE, "select");
	    if (empty($getAppearanceInfo)) {
		$insertUser = execute_query("update app_user_update SET user_update_read='$userId,' where app_id='$announceId'", TRUE, "insert");
	    } else {
		$getAppUsersUpdate = $getAppearanceInfo['user_update_read'];
		$explode = explode(",", $getAppUsersUpdate);
		$arrCount = count($explode);
		unset($explode[$arrCount - 1]);

		$searchExistanceOfUserId = array_search($userId, $explode);
		if (is_int($searchExistanceOfUserId)) {
		    return $appEntourageStatus;
		} else {
		    $arrayInsert = count($explode);
		    $explode[$arrayInsert+1] = $userId;
		    $usersRead = join(",", $explode);
		    $usersRead = $usersRead . ",";
		    $insertUser = execute_query("update app_user_update SET user_update_read='$usersRead' where app_id='$announceId'", TRUE, "insert");
		}
	    }
	    return $appEntourageStatus;
	} else {
	    return array();
	}
    }

//end of appearance_entourage_status()

    /*  function app_ent_status_comment()
      Purpose    : To comment on the appearance made
      Parameters : $xmlrequest : Request array for comment posting
      Returns    : last id of the comment inserted */

    function app_ent_status_comment($xmlRequest) {
        // if (DEBUG)
        writelog("appearance.class.php :: app_ent_status_comment() :: ", "Starts Here ", false);

        $appEntStatusComment = array();
        $userId = mysql_real_escape_string($xmlRequest['AppEntStatusComment']['userId']);
        $id = mysql_real_escape_string($xmlRequest['AppEntStatusComment']['id']);
        $checkedInUserId = mysql_real_escape_string($xmlRequest['AppEntStatusComment']['checkedInUserId']);
        $commentText = mysql_real_escape_string($xmlRequest['AppEntStatusComment']['commentText']);
        $privacy = user_privacy_settings($userId);
        if (isset($privacy) && ($privacy == 'private')) {
            $visible = 'allfriends';
        } else {
            $visible = '';
        }
//inserting data in app_entourage_comments table
        $queryStatusComment = "INSERT INTO app_entourage_comments(parent_id,announce_arrival_id,comment_by_id,comment,date,time,post_via)VALUES('$id','$id','$userId','$commentText','" . date('Y-m-d') . "','" . date('H:i:s') . "','1')";
        // if (DEBUG)
        writelog("appearance.class.php :: app_ent_status_comment() :: Query to get Appearances entourage status comment: ", $queryStatusComment, false);

        $appEntStatusComment = execute_query($queryStatusComment, false, "insert");


        $comment_id = $appEntStatusComment['last_id'];

        if ($comment_id) {
//inserting comment in db bulletin
            $get_hotpress_id = execute_query("SELECT id FROM bulletin WHERE appearance_id='$id'", false, "select");
            $hotpress_data = execute_query("INSERT INTO bulletin(mem_id,subj,body,visible_to,date,parentid,from_id,image_link,photo_album_id,appearance_id,msg_alert,post_via) VALUES('$userId','','$commentText','$visible','" . time() . "','" . $get_hotpress_id['id'] . "','$checkedInUserId','','','0','Y','1')", false, "insert");
            $hotpress_comment_id = $hotpress_data['last_id'];
//update app_entourage_comments table witth bulletin id
            $query_hotpress = "UPDATE app_entourage_comments SET bullet_id='$hotpress_comment_id' WHERE id='$comment_id'"; //
            $appearance_comment_result = execute_query($query_hotpress, false, "update");
            $exeStatusComment['last_id'] = $hotpress_comment_id;
// for email and push notification
//get user email id
            $get_user_email_id = execute_query("SELECT email,profilenam FROM members WHERE mem_id='$userId'", false, "select");
//get checkedin user email id
            $get_checkedin_email_id = execute_query("SELECT email FROM members WHERE mem_id='$checkedInUserId'", false, "select");
            $get_hotpress_id = execute_query("SELECT id FROM bulletin WHERE appearance_id='$id'", false, "select");
            $hot_id = $get_hotpress_id['id'];

            // $headers = "From: {$get_user_email_id['email']} " . "\r\n";
            // $headers.= "MIME-version: 1.0\n";
            // $headers.= "Content-type: text/html; charset= iso-8859-1\n";
            if ($checkedInUserId != $userId) {
                // $mail = mail($get_checkedin_email_id['email'], 'You have a new appearance comment.', "{$get_user_email_id['profilenam']} has commented on your Appearance.<a href='http://www.socialnightlife.com/index.php?pg=login&usr=$checkedInUserId&hotpress_id=$hot_id&email_from_appearance=1&hotpressContainer$hotpress_comment_id' >Click here</a> to log in and view details.", $headers);
                $matter = email_template($get_user_email_id['profilenam'], 'You have a new appearance comment on SocialNightlife.', "{$get_user_email_id['profilenam']} has commented on your Appearance.<a href='http://www.socialnightlife.com/index.php?pg=profile&usr=$checkedInUserId&gotohtpreepage=1'>Click here</a> to log in and view details.", $userId, $get_user_email_id['photo_thumb']);
				$result = firemail($get_checkedin_email_id['email'], 'From: noreply@socialnightlife.com\r\n', 'You have a new appearance comment on SocialNightlife.', $matter);
				push_notification('comment_on_appearance', $checkedInUserId, $userId);
            }
            // if (DEBUG) {

            writelog("appearance.class.php :: app_ent_status_comment() :: ", "End Here ", false);
            //  }

            return $exeStatusComment;
        } else {
            return array();
        }
    }

//end of app_ent_status_comment()

    /*  function appearance_venue_listing()
      Purpose    : To comment on the appearance made
      Parameters : $xmlrequest : Request array for venue list within given range
      $pageNumber : Current page Number
      $limit      : no. of results to be display on each page
      Returns    : List of venues available in perticular range */

    function appearance_venue_listing($xmlRequest, $pageNumber, $limit) {
	// if (DEBUG)
	writelog("appearance.class.php :: appearance_venue_listing() :: ", "Starts Here ", false);

	$lowerLimit = isset($pagenumber) ? ($pagenumber - 1) * $limit : 0;
	$appVenueList = array();

	$userid = mysql_real_escape_string($xmlRequest['AppearanceVenueList']['userId']);
	$latitude1 = isset($xmlRequest['AppearanceVenueList']['latitude']) && ($xmlRequest['AppearanceVenueList']['latitude']) ? mysql_real_escape_string($xmlRequest['AppearanceVenueList']['latitude']) : NULL;
	$longitude1 = isset($xmlRequest['AppearanceVenueList']['longitude']) && ($xmlRequest['AppearanceVenueList']['longitude']) ? mysql_real_escape_string($xmlRequest['AppearanceVenueList']['longitude']) : NULL;
	if (is_null($latitude1) && (is_null($longitude1))) {
//get all venues(nightsite profile users)
	    $queryAppearance = "SELECT SQL_CALC_FOUND_ROWS mem_id,profilenam,photo_b_thumb, city,zip,country, state,latitude,longitude,gender,profile_type,is_facebook_user FROM members WHERE profile_type = 'C' and mem_id !='$userid' GROUP BY mem_id"; // LIMIT $lowerLimit, $limit
	} else {
//get all venues(nightsite profile users)
	    $queryAppearance = "SELECT SQL_CALC_FOUND_ROWS mem_id,profilenam,photo_b_thumb, city,zip,country, state,latitude,longitude,gender,profile_type,is_facebook_user, ( 3959 * acos( cos( radians($latitude1) ) * cos( radians( latitude ) ) * cos( radians(longitude) - radians($longitude1)) + sin(radians($latitude1)) * sin( radians(latitude)))) AS distance FROM members WHERE profile_type = 'C' and mem_id !='$userid' GROUP BY mem_id  having distance < 10"; // LIMIT $lowerLimit, $limit
	}
	if (DEBUG)
	    writelog("appearance.class.php :: appearance_venue_listing() :: Query to get Appearance Venues : ", $queryAppearance, false);

	$appAppearanceQuery = mysql_query($queryAppearance);

	if ((mysql_num_rows($appAppearanceQuery) > 0)) {
	    $i = 0;
	    while ($appAppearance = mysql_fetch_array($appAppearanceQuery, MYSQL_ASSOC)) {

		$appVenueList[$i]['mem_id'] = (isset($appAppearance['mem_id']) && ($appAppearance['mem_id']) ? $appAppearance['mem_id'] : NULL);
		$appVenueList[$i]['profileName'] = (isset($appAppearance['profilenam']) && ($appAppearance['profilenam']) ? $appAppearance['profilenam'] : NULL);

		$appVenueList[$i]['city'] = (isset($appAppearance['city']) && ($appAppearance['city']) ? $appAppearance['city'] : NULL);
		$appVenueList[$i]['zip'] = (isset($appAppearance['zip']) && ($appAppearance['zip']) ? $appAppearance['zip'] : NULL);
		$appVenueList[$i]['country'] = (isset($appAppearance['country']) && ($appAppearance['country']) ? $appAppearance['country'] : NULL);
		$appVenueList[$i]['state'] = (isset($appAppearance['state']) && ($appAppearance['state']) ? $appAppearance['state'] : NULL);
		$appVenueList[$i]['latitude'] = (isset($appAppearance['latitude']) && ($appAppearance['latitude']) ? $appAppearance['latitude'] : NULL);
		$appVenueList[$i]['longitude'] = (isset($appAppearance['longitude']) && ($appAppearance['longitude']) ? $appAppearance['longitude'] : NULL);
		$appVenueList[$i]['gender'] = (isset($appAppearance['gender']) && ($appAppearance['gender']) ? $appAppearance['gender'] : NULL);
		$appVenueList[$i]['profile_type'] = (isset($appAppearance['profile_type']) && ($appAppearance['profile_type']) ? $appAppearance['profile_type'] : NULL);
		$appVenueList[$i]['is_facebook_user'] = (isset($appAppearance['is_facebook_user']) && ($appAppearance['is_facebook_user']) ? $appAppearance['is_facebook_user'] : NULL);
		$appVenueList[$i]['photo_b_thumb'] = isset($appVenueList[$i]['is_facebook_user']) && (strlen($appAppearance['photo_b_thumb']) > 7) && ($appVenueList[$i]['is_facebook_user'] == 'y' || $appVenueList[$i]['is_facebook_user'] == 'Y') ? $appAppearance['photo_b_thumb'] : ((isset($appAppearance['photo_b_thumb']) && (strlen($appAppearance['photo_b_thumb']) > 7)) ? $this->profile_url . $appAppearance['photo_b_thumb'] : $this->profile_url . default_images($appVenueList[$i]['gender'], $appVenueList[$i]['profile_type']));
		$appVenueList[$i]['distance'] = (isset($appAppearance['distance']) && ($appAppearance['distance']) ? $appAppearance['distance'] : NULL);

		$totalVenue = mysql_query("SELECT FOUND_ROWS() as TotalRecords ;");
		$venueList = mysql_fetch_array($totalVenue, MYSQL_ASSOC);
		$appVenueList['Total'] = $venueList['TotalRecords'];

		/*		 * *****ambassador start****** */
		$toDate = date('Y-m-d');
		$date = strtotime(date("Y-m-d", strtotime($toDate)) . " -90 days");
		$fromDate = date('Y-m-d', $date);
		//$i = 0;
		//if (!empty($appAppearance)) {
		/* $appVenueList['LatLong'][$i]['latitude'] = $appVenueList[$i]['latitude'];
		  $appVenueList['LatLong'][$i]['longitude'] = $appVenueList[$i]['longitude']; */
		//unset($appVenueList['LatLong']['Total']);
		//}

		$venueId = isset($appVenueList[$i]['userId']) && ($appVenueList[$i]['userId']) ? $appVenueList[$i]['userId'] : NULL;
		if ($venueId) {
		    //information about venue
		    $queryAppearance1 = "SELECT DISTINCT a.date,a.user_id,b.is_facebook_user,b.gender,b.profile_type,b.photo_b_thumb FROM announce_arrival as a,members as b WHERE a.venue_id = '" . $venueId . "' AND a.user_id = b.mem_id AND (b.photo_b_thumb != 'no' && b.photo_b_thumb != '') AND b.profile_type='C' AND date Between '$fromDate' AND '$toDate' ORDER BY a.user_id ASC";

		    $unique = array();
		    if (DEBUG)
			writelog("appearance.class.php :: app_venue_details() :: Query to get Appearance Venue Detail: ", $queryAppearance1, false);

		    $appVenueAmbassador = mysql_query($queryAppearance1);
		    if ((mysql_num_rows($appVenueAmbassador) > 0)) {
			while ($appVenueAmbssdor = mysql_fetch_array($appVenueAmbassador, MYSQL_ASSOC)) {
			    if (isset($appVenueAmbssdor['user_id'])) {
				$unique[] = $appVenueAmbssdor['user_id'];
			    }
			}
		    }

		    if (!empty($unique)) {
			$list_users = array_count_values($unique);
			$ambasaddor = array_search($maxOne = max(array_values($list_users)), $list_users);
			$no_of_attend = $list_users[$ambasaddor];

			$get_user_info = "SELECT is_facebook_user,gender,profile_type,mem_id,profilenam,photo_b_thumb FROM members WHERE mem_id = $ambasaddor";
			$appVenueUserInfo = mysql_query($get_user_info);
			if ((mysql_num_rows($appVenueUserInfo) > 0)) {
			    while ($appVenueResult = mysql_fetch_array($appVenueUserInfo, MYSQL_ASSOC)) {
				$appVenueList[$i]['Ambassador']['AmbassadorName'] = isset($appVenueResult['profilenam']) && ($appVenueResult['profilenam']) ? $appVenueResult['profilenam'] : NULL;
				$appVenueList[$i]['Ambassador']['AmbassadorPhoto'] = isset($appVenueResult['photo_b_thumb']) && ($appVenueResult['photo_b_thumb']) ? $appVenueResult['photo_b_thumb'] : NULL;
				$appVenueList[$i]['Ambassador']['AmbassadorId'] = isset($appVenueResult['mem_id']) && ($appVenueResult['mem_id']) ? $appVenueResult['mem_id'] : NULL;
			    }
			}
		    }
		} $i++;
		$appVenueList['LatLong'][$i]['latitude'] = $appAppearance['latitude'];
		$appVenueList['LatLong'][$i]['longitude'] = $appAppearance['longitude'];
	    }
	    $appVenueList['count'] = isset($appVenueList) ? mysql_num_rows($appAppearanceQuery) : NULL;

	    /*	     * ****ambassador end******* */

	    // if (DEBUG) {
	    writelog("Appearance:appearance_venue_listing:", $appVenueList, true);
	    writelog("Appearance:appearance_venue_listing:", $totalVenue, true);
	    writelog("appearance.class.php :: appearance_venue_listing() :: ", "End Here ", false);
	    //  }
	    //die();


	    return $appVenueList;
	} else {
	    return array();
	}
    }

//end of appearance_venue_listing()

    /*  function app_venue_details()
      Purpose    : To get the venue detail such as ambassador, map and profile
      Parameters : $xmlrequest : Request array for appearance venue detail
      Returns    : information about venue ambassador */

    public function app_venue_details($xmlRequest) {
        //if (DEBUG)
        writelog("appearance.class.php :: app_venue_details() :: ", "Starts Here ", false);

        $appAmbassador = array();
        $venueId = mysql_real_escape_string($xmlRequest['AppVenueDetail']['venueId']);
        $latitude = mysql_real_escape_string($xmlRequest['AppVenueDetail']['latitude']);
        $longitude = mysql_real_escape_string($xmlRequest['AppVenueDetail']['longitude']);
        $unique = array();
        $toDate = date('Y-m-d');
        $date = strtotime(date("Y-m-d", strtotime($toDate)) . " -90 days");
        $fromDate = date('Y-m-d', $date);
//venue detail
        $queryAppearance = "SELECT DISTINCT a.date,a.user_id,b.is_facebook_user,b.photo_b_thumb,b.gender,b.profile_type FROM announce_arrival as a,members as b WHERE a.venue_id = '" . $venueId . "' AND a.user_id = b.mem_id AND (b.photo_thumb != 'no' && b.photo_thumb != '') AND b.profile_type='C' AND date Between '$fromDate' AND '$toDate' ORDER BY a.user_id ASC";

        // if (DEBUG)
        writelog("appearance.class.php :: app_venue_details() :: Query to get Appearance Venue Detail: ", $querAppearance, false);
        $appVenueAmbassador = execute_query($queryAppearance, true, "select");
        if (!empty($appVenueAmbassador)) {
            foreach ($appVenueAmbassador as $kk => $venue) {
                if (isset($venue['user_id'])) {
                    $unique[] = $venue['user_id'];
                }
            }
            if (!empty($unique)) {
                $list_users = array_count_values($unique);
                $ambasaddor = array_search($maxOne = max(array_values($list_users)), $list_users);
                $no_of_attend = $list_users[$ambasaddor];
//ambassador detail
                $get_user_info = "SELECT mem_id,profilenam,photo_b_thumb,is_facebook_user,gender,profile_type FROM members WHERE mem_id = $ambasaddor";
                $appVenueUserInfo = execute_query($get_user_info, false, "select");

                $appAmbassador['Ambassador'] = isset($appVenueUserInfo['profilenam']) && ($appVenueUserInfo['profilenam']) ? $appVenueUserInfo['profilenam'] : NULL;
                $appAmbassador['Amb_photo'] = isset($appVenueUserInfo['photo_b_thumb']) && ($appVenueUserInfo['photo_b_thumb']) ? $appVenueUserInfo['photo_b_thumb'] : NULL;
                $appAmbassador['Amb_id'] = isset($appVenueUserInfo['mem_id']) && ($appVenueUserInfo['mem_id']) ? $appVenueUserInfo['mem_id'] : NULL;
                $appAmbassador['gender'] = isset($appVenueUserInfo['gender']) && ($appVenueUserInfo['gender']) ? $appVenueUserInfo['gender'] : NULL;
                $appAmbassador['profile_type'] = isset($appVenueUserInfo['profile_type']) && ($appVenueUserInfo['profile_type']) ? $appVenueUserInfo['profile_type'] : NULL;
                $appAmbassador['ambMsgType'] = 'Y';
                $appAmbassador['ambMsg'] = "Ambassador Listed";
            } else {
                $appAmbassador['ambMsgType'] = 'N';
                $appAmbassador['ambmsg'] = "We need an Ambassador. Help!";
            }
            $venue_info = "SELECT mem_id,profilenam,photo_b_thumb,latitude,longitude,city,state,zip,country,email,gender,is_facebook_user,profile_type FROM members WHERE mem_id=$venueId";
            $exe_venue_info = execute_query($venue_info, false, "select");
            $appAmbassador['profile_id'] = isset($exe_venue_info['mem_id']) && ($exe_venue_info['mem_id']) ? $exe_venue_info['mem_id'] : NULL;
            $appAmbassador['profilename'] = isset($exe_venue_info['profilenam']) && ($exe_venue_info['profilenam']) ? $exe_venue_info['profilenam'] : NULL;
            $appAmbassador['photo_b_thumb'] = isset($exe_venue_info['photo_b_thumb']) && ($exe_venue_info['photo_b_thumb']) ? $exe_venue_info['photo_b_thumb'] : NULL;
            $appAmbassador['gender'] = isset($exe_venue_info['gender']) && ($exe_venue_info['gender']) ? $exe_venue_info['gender'] : NULL;
            $appAmbassador['profile_type'] = isset($exe_venue_info['profile_type']) && ($exe_venue_info['profile_type']) ? $exe_venue_info['profile_type'] : NULL;
            $appAmbassador['city'] = isset($exe_venue_info['city']) && ($exe_venue_info['city']) ? $exe_venue_info['city'] : NULL;
            $appAmbassador['state'] = isset($exe_venue_info['state']) && ($exe_venue_info['state']) ? $exe_venue_info['state'] : NULL;
            $appAmbassador['zip'] = isset($exe_venue_info['zip']) && ($exe_venue_info['zip']) ? $exe_venue_info['zip'] : NULL;
            $appAmbassador['country'] = isset($exe_venue_info['country']) && ($exe_venue_info['country']) ? $exe_venue_info['country'] : NULL;
            $appAmbassador['email'] = isset($exe_venue_info['email']) && ($exe_venue_info['email']) ? $exe_venue_info['email'] : NULL;
            $appAmbassador['latitude'] = isset($exe_venue_info['latitude']) && ($exe_venue_info['latitude']) ? $exe_venue_info['latitude'] : NULL;
            $appAmbassador['longitude'] = isset($exe_venue_info['longitude']) && ($exe_venue_info['longitude']) ? $exe_venue_info['longitude'] : NULL;
//for user not in range
            $displayMsg = "SELECT COUNT(*) AS cnt, ( 3956 *2 * ASIN( SQRT( POWER( SIN( ( $latitude - abs( latitude ) ) * pi( ) /180 /2 ) , 2 ) +  COS($latitude * pi( ) /180 ) * COS( abs( latitude ) * pi( ) /180 ) *  POWER( SIN( ( abs($longitude) - abs( longitude ) ) * pi( ) /180 /2 ) , 2 ) ) ) ) AS distance FROM members WHERE profile_type = 'C' AND mem_id='$venueId' GROUP BY mem_id HAVING distance < 20 ";
            $exe_display_msg = execute_query($displayMsg, false, "select");

            if (isset($exe_display_msg['cnt']) && ($exe_display_msg['cnt'] >= 0)) {
                $appAmbassador['msgType'] = 'Y';
                $appAmbassador['msg'] = "WelCome To Venue";
            } else {
                $appAmbassador['msgType'] = 'N';
                $appAmbassador['msg'] = "you need to be at venue to announce your arrival . Please try again when you are nearby or inside the venue";
            }
            $queryAppGeneral = "SELECT distinct reward_type,reward_title,reward_description,start_time,exp_time FROM users_reward WHERE nightlife_profile_id IN (SELECT mem_id FROM members WHERE mem_id='$venueId') AND reward_type='0' ORDER BY created DESC LIMIT 0,1";
            $queryAppAmbss = "SELECT distinct reward_type,reward_title,reward_description,start_time,exp_time FROM users_reward WHERE nightlife_profile_id IN (SELECT mem_id FROM members WHERE mem_id='$venueId') AND reward_type='1' ORDER BY created DESC LIMIT 0,1";
            $get_general_reward = execute_query($queryAppGeneral, true, "select");
            $get_ambassador_reward = execute_query($queryAppAmbss, true, "select");
            if ((!$get_ambassador_reward) || (!$get_general_reward)) {
                $appAmbassador['noRewardsMessage'] = 'No rewards available. Please contact venue so they can participate and reward their guests';
            }
// if (DEBUG) {
            writelog("Appearance:app_venue_details:", $appAmbassador, true);
            writelog("appearance.class.php :: app_venue_details() :: ", "End Here ", false);
            // }
            return $appAmbassador;
        } else {
            return array();
        }
    }

//end of app_venue_details()

    /*  function app_announce_arr_list()
      Purpose    : to announce the arrival of the user
      Parameters : $xmlrequest : Request array for appearance announce
      Returns    : display suggestion on various activity on that event such as music ,boys/girls ratio etc */

    function app_announce_arr_list($xmlRequest) {
        // if (DEBUG)
        writelog("appearance.class.php :: app_announce_arr_list() :: ", "Starts Here ", false);

        $appList = array();
        $venue_id1 = mysql_real_escape_string($xmlRequest['AnnounceArrival']['venueId']);
        $userid = mysql_real_escape_string($xmlRequest['AnnounceArrival']['userId']);
        $wait_time = mysql_real_escape_string($xmlRequest['AnnounceArrival']['waitInLine']);
        $genderRatio = mysql_real_escape_string($xmlRequest['AnnounceArrival']['ratio']);
        $music = mysql_real_escape_string($xmlRequest['AnnounceArrival']['music']);
        $energy = mysql_real_escape_string($xmlRequest['AnnounceArrival']['energy']);
        $body = mysql_real_escape_string($xmlRequest['AnnounceArrival']['PhotoUpload']['body']);
        $time = mysql_real_escape_string($xmlRequest['AnnounceArrival']['PhotoUpload']['time']);
        $userid = (isset($xmlRequest['AnnounceArrival']['userId'])) ? $xmlRequest['AnnounceArrival']['userId'] : NULL;
        $wait_time = (isset($xmlRequest['AnnounceArrival']['waitInLine'])) ? $xmlRequest['AnnounceArrival']['waitInLine'] : NULL;
        $genderRatio = (isset($xmlRequest['AnnounceArrival']['ratio'])) ? $xmlRequest['AnnounceArrival']['ratio'] : NULL;
        $music = (isset($xmlRequest['AnnounceArrival']['music'])) ? $xmlRequest['AnnounceArrival']['music'] : NULL;
        $energy = (isset($xmlRequest['AnnounceArrival']['energy'])) ? $xmlRequest['AnnounceArrival']['energy'] : NULL;

		$curDate=substr($time,0,strpos($time, ' '));
		$curTime=substr($time,strpos($time, ' '));
		$timeElapsed = strtotime($time);
	   
	if (($xmlRequest['AnnounceArrival']['isFoursquare']) == 'Y' || ($xmlRequest['AnnounceArrival']['isFoursquare']) == 'y') {
			
            $venueName = isset($xmlRequest['AnnounceArrival']['fourSquareData']['venueName']) && ($xmlRequest['AnnounceArrival']['fourSquareData']['venueName']) ? $xmlRequest['AnnounceArrival']['fourSquareData']['venueName'] : NULL;
			
            $venueZip = isset($xmlRequest['AnnounceArrival']['fourSquareData']['venueZip']) && ($xmlRequest['AnnounceArrival']['fourSquareData']['venueZip']) ? $xmlRequest['AnnounceArrival']['fourSquareData']['venueZip'] : NULL;
			
            $venuecountry = isset($xmlRequest['AnnounceArrival']['fourSquareData']['venueCountry']) && ($xmlRequest['AnnounceArrival']['fourSquareData']['venueCountry']) ? $xmlRequest['AnnounceArrival']['fourSquareData']['venueCountry'] : NULL;
			
            $venueState = isset($xmlRequest['AnnounceArrival']['fourSquareData']['venueState']) && ($xmlRequest['AnnounceArrival']['fourSquareData']['venueState']) ? $xmlRequest['AnnounceArrival']['fourSquareData']['venueState'] : NULL;
			
            $venueCity = isset($xmlRequest['AnnounceArrival']['fourSquareData']['venueCity']) && ($xmlRequest['AnnounceArrival']['fourSquareData']['venueCity']) ? $xmlRequest['AnnounceArrival']['fourSquareData']['venueCity'] : NULL;
			
            $venueLatitude = isset($xmlRequest['AnnounceArrival']['fourSquareData']['venueLatitude']) && ($xmlRequest['AnnounceArrival']['fourSquareData']['venueLatitude']) ? $xmlRequest['AnnounceArrival']['fourSquareData']['venueLatitude'] : NULL;
			
            $venueLongitude = isset($xmlRequest['AnnounceArrival']['fourSquareData']['venueLongitude']) && ($xmlRequest['AnnounceArrival']['fourSquareData']['venueLongitude']) ? $xmlRequest['AnnounceArrival']['fourSquareData']['venueLongitude'] : NULL;
			
            $venueUniqueId = isset($xmlRequest['AnnounceArrival']['fourSquareData']['venueUniqueId']) && ($xmlRequest['AnnounceArrival']['fourSquareData']['venueUniqueId']) ? $xmlRequest['AnnounceArrival']['fourSquareData']['venueUniqueId'] : NULL;
			
            $venueAddress = isset($xmlRequest['AnnounceArrival']['fourSquareData']['venueAddress']) && ($xmlRequest['AnnounceArrival']['fourSquareData']['venueAddress']) ? $xmlRequest['AnnounceArrival']['fourSquareData']['venueAddress'] : NULL;
			
            $response_mess = '
               {
   ' . response_repeat_string() . '
    "TakeOverProfile":{
           "profileName":"' .str_replace('"', '\"', $venueName). '",
           "zip":"' .str_replace('"', '\"', $venueZip). '",
           "deviceId":"<UDID>",
            "address":"' .str_replace('"', '\"', $venueAddress). '",
            "country":"' .str_replace('"', '\"', $venuecountry). '",
            "photo":"' .str_replace('"', '\"', $this->profile_url . default_images('', 'C')) . '",
            "state":"' .str_replace('"', '\"', $venueState). '",
            "city":"' .str_replace('"', '\"', $venueCity). '",
            "latitude":"' .str_replace('"', '\"', $venueLatitude). '",
            "longitude":"' .str_replace('"', '\"', $venueLongitude). '",
            "interests":"",
            "gender":"",
            "birthday":""
            }
	  }';
			
            $response_mess = json_decode($response_mess, true);
			//ini_set('display_errors',1);
            require_once './classes/profile.class.php';
		
            $obj_take_over_profile = new Profile();
			
            $get_take_over = $obj_take_over_profile->take_over_profile($response_mess);
        }
        if ($venue_id1 == '') {
            $venue_id = isset($get_take_over['last_id']) && ($get_take_over['last_id']) ? $get_take_over['last_id'] : NULL;
        } else {
            $venue_id = $venue_id1;
        }

//        $queryUpdate = "SELECT id,venue_id,user_id FROM announce_arrival WHERE venue_id='$venue_id' AND user_id='$userid'";
//        $exeUpdate = execute_query($queryUpdate, true, "select");
//        if (!empty($exeUpdate)) {
//            $querAppearance = "UPDATE announce_arrival SET wait_in_line='$wait_time',ratio='$genderRatio',music='$music',energy='$energy',comment='$body',date='" . date('Y-m-d') . "',time='" . date('H:i:s') . "' WHERE venue_id='$venue_id' AND user_id='$userid'";
//            $appVenueStatus = execute_query($querAppearance, true, "update");
////clear older comments
//            foreach ($exeUpdate as $kk => $delete_old_comment) {
//                $clear_comments = execute_query("DELETE FROM app_entourage_comments WHERE announce_arrival_id='" . $delete_old_comment['id'] . "'", true, "delete");
//            }
//        } else {
        $querAppearance = "INSERT INTO announce_arrival (announce_arrival.venue_id,announce_arrival.user_id,announce_arrival.wait_in_line,announce_arrival.ratio,announce_arrival.music,announce_arrival.energy,announce_arrival.comment,announce_arrival.date,announce_arrival.time,announce_arrival.read)
	VALUES('$venue_id','$userid','$wait_time','$genderRatio','$music','$energy','$body','$curDate','$curTime','0')"; //'" . date('Y-m-d') . "','" . date("h:i:s") . "'
        $appVenueStatus = execute_query($querAppearance, true, "insert");
//        }
        //$getAnnounceId = (!empty($appVenueStatus)) && (is_array($appVenueStatus)) ? $appVenueStatus['last_id'] : NULL;

	if(!empty($appVenueStatus) && (is_array($appVenueStatus))){
	    $getAnnounceId=$appVenueStatus['last_id'];
	}else{
	    return false;
	}

        //if (DEBUG)
        writelog("appearance.class.php :: app_announce_arr_list() :: Query to get Appearance Venues Status: ", $querAppearance, false);

        $announce_arr_hotpress = "SELECT el.even_title,el.even_loc,el.even_city,ecm.event_id,ecm.profilenam as entourage,mem.profilenam,mem.is_facebook_user,mem.gender,mem.profile_type as appName FROM event_entourage_chkin AS ecm,members AS mem,event_list AS el WHERE ecm.profileid='$userid' AND ecm.event_id=el.even_id AND el.even_own=mem.mem_id";
        // if (DEBUG)
        writelog("appearance.class.php :: app_announce_arr_list() :: Query to get Appearance Venues Status: ", $announce_arr_hotpress, false);

        $arr_hotpress = execute_query($announce_arr_hotpress, false, "select");
        $querySelectEvent = "SELECT COUNT(*) FROM tag_event_list WHERE venue_id='$venue_id' AND user_id='$userid'";
        $exeTagEventList = execute_query($querySelectEvent, false, "select");

        if (isset($exeTagEventList['COUNT(*)']) && ($exeTagEventList['COUNT(*)'])) {
            $queryDelete = "DELETE  FROM tag_event_list WHERE venue_id='$venue_id'AND user_id='$userid'"; //id='" . $delPrevousEvent['id'] . "'
            $exeDelete = execute_query($queryDelete, true, "delete");
        }
        $querySelectEntourage = "SELECT COUNT(*) FROM tag_ent_list WHERE venue_id='$venue_id' AND user_id='$userid'";
        $exeTagEntourageList = execute_query($querySelectEntourage, false, "select");

        if (isset($exeTagEntourageList['COUNT(*)']) && ($exeTagEntourageList['COUNT(*)'])) {
            $queryDelete1 = "DELETE FROM tag_ent_list WHERE venue_id='$venue_id' AND user_id='$userid'"; //" . $delPrevousEntourage['id'] . "'
            $exeDelete1 = execute_query($queryDelete1, true, "delete");
        }

        /* tag entourage list start */
        $tag_ent = array();
        foreach ($xmlRequest['AnnounceArrival']['taggedEntourage'] as $kk => $tagEntourage) {

            $entId = $tagEntourage['entourageId'];
            $tag_ent[] = getname($entId);
            $queryUpdate = "SELECT date,time FROM tag_ent_list WHERE announce_id='$getAnnounceId' AND venue_id ='$venue_id' AND user_id ='$userid' AND ent_id ='$entId'";
            $exeTagEntourage = execute_query($queryUpdate, true, "select");
            $getUserInfo = "SELECT email,profilenam FROM members WHERE mem_id='$userid'";
            $exegetUserInfo = execute_query($getUserInfo, false, "select");
            $querySendMail = "select email FROM members WHERE mem_id='" . $entId . "'";
            $exeSendMail = execute_query($querySendMail, false, "select");
            $headers = 'From: "' . $exegetUserInfo['email'] . '"' . "\r\n";
            $headers.= "MIME-version: 1.0\n";
            $headers.= "Content-type: text/html; charset= iso-8859-1\n";
			
	    $matter = email_template($exegetUserInfo['profilenam'], 'You have been tagged in a appearance.', "{$exegetUserInfo['profilenam']} has tagged you in an Appearance.<a href='http://www.socialnightlife.com/index.php?pg=login' >Click here</a> to log in and view details.", $userid, $exegetUserInfo['photo_thumb']);
	    $result = firemail($exeSendMail['email'], 'From: noreply@socialnightlife.com\r\n', 'You have been tagged in a appearance.', $matter);			
						
            // $mail = mail($exeSendMail['email'], 'You have been tagged in a appearance.', "{$exegetUserInfo['profilenam']} has tagged you in an Appearance.<a href='http://www.socialnightlife.com/index.php?pg=login' >Click here</a> to log in and view details.", $headers);
            push_notification('appearance_tag_entourage', $userid, $entId);
	    //if condition will no longer use because new field is added to tag_ent_list *announce_id
            if (!empty($exeTagEntourage)) {
                $queryTagEntourage = "UPDATE tag_ent_list SET date='$curDate',time='$curTime' where venue_id ='$venue_id' AND user_id ='$userid' AND ent_id ='$entId' AND msg_alert='Y' AND announce_id='$getAnnounceId' ";
                $exeTagEntourage = execute_query($queryTagEntourage, true, "insert");
            } else {
                $queryTagEntourage = "INSERT INTO tag_ent_list(venue_id,user_id,ent_id,announce_id,date,time,msg_alert)VALUES('$venue_id','$userid','$entId','$getAnnounceId','$curDate','$curTime','Y')";
                $exeTagEntourage = execute_query($queryTagEntourage, true, "insert");
            }
        }
        /* tag entourage list end */

        /* tag event list start */
        $tag_event = array();
        foreach ($xmlRequest['AnnounceArrival']['taggedEvent'] as $kk => $tagEvent) {

            $eventId = $tagEvent['eventId'];
            $get_event_name = execute_query("SELECT even_title FROM event_list WHERE even_id='$eventId'", false, "select");
            $tag_event[] = $get_event_name['even_title'];
            $queryUpdate = "SELECT date,time FROM tag_event_list WHERE venue_id='$venue_id' AND user_id='$userid' AND event_id ='$eventId'";
            $exeTagEntourage = execute_query($queryUpdate, true, "select");

            if (!empty($exeTagEntourage)) {
                $queryTagEntourage = "UPDATE tag_event_list SET date='$curDate',time='$curTime' where venue_id ='$venue_id' AND user_id ='$userid' AND ent_id ='$entId' ";
                $exeTagEntourage = execute_query($queryTagEntourage, true, "insert");
            } else {
                $queryTagEvent = "INSERT INTO tag_event_list(venue_id,user_id,event_id,date,time)VALUES('$venue_id','$userid','$eventId','$curDate','$curTime')";
                $exeTagEvent = execute_query($queryTagEvent, true, "insert");
            }
        }
        /* tag event list end */

        if ((isset($xmlRequest['AnnounceArrival']['PhotoUpload']['filename'])) && ($xmlRequest['AnnounceArrival']['PhotoUpload']['filename']) && (isset($xmlRequest['AnnounceArrival']['PhotoUpload']['uploadLocation'])) && ($xmlRequest['AnnounceArrival']['PhotoUpload']['uploadLocation'] != '')) {
            $xmlRequest1['GenInfo'] = $xmlRequest['GenInfo'];
            $xmlRequest['AnnounceArrival']['PhotoUpload']['venueId'] = $venue_id;
            $xmlRequest1['PhotoUpload'] = $xmlRequest['AnnounceArrival']['PhotoUpload'];
            $xmlRequest1['PhotoUpload']['subj'] = NULL;
            $xmlRequest1['PhotoUpload']['body'] = NULL;

            $photo_upload = photo_upload($xmlRequest1);
			//print_r($photo_upload);
            $appVenueStatus['upload'] = $photo_upload;
        }
        $arr_hotpress['entourage'] = isset($arr_hotpress['entourage']) && ($arr_hotpress['entourage']) ? $arr_hotpress['entourage'] : 0;
        $get_announce_arrival = isset($get_announce_arrival) && ($get_announce_arrival) ? $get_announce_arrival : 0;
        $record = array(
            "waitinline" => $wait_time,
            "ratio" => $genderRatio,
            "music" => $music,
            "enegry" => $energy,
            "entourage" => "Entourage : " . implode(',', $tag_ent),
            "eventAttendText" => "Attending : " . implode(',', $tag_event),
            "appearanceText" => getname($userid) . " has made an appearance at " . getname($venue_id),
            "hotpress_image_filename" => '../photos/' . md5(date("YmdHis")) . ".jpeg");
        $image = 'photos/' . md5(date("YmdHis")) . ".jpeg";
        //$image_hotpress = appearance_hotpress_image($record);
		$image_hotpress=NULL;
		if($wait_time != 0 || $genderRatio != 0 || $music != 0 || $energy != 0)
		{
			$image_hotpress = appearance_hotpress_image_new($record);
			//$image_hotpress = @ltrim($image_hotpress,'../');
		}
        $appVenueStatus['upload']['last_id'] = isset($appVenueStatus['upload']['last_id']) && ($appVenueStatus['upload']['last_id']) ? $appVenueStatus['upload']['last_id'] : NULL;
//        $body = " has made an appearance @" . '<br><br>' . $body;
		if(strlen($body) > 0)
			$body = '\\n\\n' . $body;
			
        $body = str_replace("'","\'",getname($userid)) . " has made an appearance @ " . str_replace("'","\'",getname($venue_id)) . $body;
        if (isset($appVenueStatus['upload']['last_id']) && ($appVenueStatus['upload']['last_id'])) {
           $hotpress_data = "UPDATE bulletin SET image_link='',link_image='$image_hotpress',body='$body',appearance_id='$getAnnounceId',auto_genrate_text='Y',from_id='$venue_id',appearance_flag='Y' WHERE id='" . $appVenueStatus['upload']['last_id'] . "'";
            $exe_hotpress_data = execute_query($hotpress_data, false, "update");
            $appVenueStatus['last_id'] = isset($appVenueStatus['upload']['last_id']) && ($appVenueStatus['upload']['last_id']) ? $appVenueStatus['upload']['last_id'] : NULL; //isset($exe_hotpress_data['last_id']) && $exe_hotpress_data['last_id']?$exe_hotpress_data['last_id']:0;
        } else {
			$upload_file=$appVenueStatus['upload']['file_name'];
            $hotpress_data = "INSERT INTO bulletin(mem_id,visible_to,date,image_link,link_image,photo_album_id,parentid,from_id,testo_id,subj,body,appearance_id,msg_alert,post_via,auto_genrate_text,appearance_flag)VALUE('$userid','','".time()."','$upload_file','$image_hotpress','0','0','$venue_id','0','','$body','" . $getAnnounceId . "','Y','1','Y','Y')";
            $exe_hotpress_data = execute_query($hotpress_data, false, "insert");
            $appVenueStatus['last_id'] = isset($exe_hotpress_data['last_id']) && $exe_hotpress_data['last_id'] ? $exe_hotpress_data['last_id'] : NULL;
        }
        
        writelog("appearance.class.php :: app_announce_arr_list() :: Query to get Appearance Venues Status: ", $announce_arr_hotpress, false);
        writelog("Appearance:app_announce_arr_list:", $exe_hotpress_data, true);
        writelog("Appearance:app_announce_arr_list:", $appVenueStatus, true);
        writelog("appearance.class.php :: app_announce_arr_list() :: ", "End Here ", false);
        
        if (isset($appVenueStatus['last_id']) && ($appVenueStatus['last_id'] != 0)) {
//for inserting appearance into read unread table
            $insertIntoReadUnrad = execute_query("INSERT INTO app_user_update(id,app_id,user_update_read) VALUES (DEFAULT,'" . $getAnnounceId . "','')", true, "insert");
//for sending mail
            $get_venue_id = execute_query("SELECT email,profilenam FROM members WHERE mem_id='$venue_id'", false, "select");
            $getUserInformation = execute_query("SELECT email,profilenam FROM members WHERE mem_id='$userid'", false, "select");
            $getUserInformation['profilenam']=str_replace("'","\'",$getUserInformation['profilenam']);
	    $message_alert = execute_query("INSERT INTO messages_system (mes_id,mem_id,frm_id,subject,body,type,new,folder,date,special,messages_system.read,update_date,skip_alert) VALUES ('','$venue_id','$userid','You have a new Announce Arrival.','{$getUserInformation['profilenam']} has made an appearance @ ".str_replace("'","\'",$get_venue_id['profilenam'])."','appearance','new','inbox','$timeElapsed','$getAnnounceId','','0','1')", false, "insert");
            $headers = "From: {$getUserInformation['email']} " . "\r\n";
            $headers.= "MIME-version: 1.0\n";
            $headers.= "Content-type: text/html; charset= iso-8859-1\n";
	    $getUserInformation['profilenam'] = str_replace("'", "\'", $getUserInformation['profilenam']);
	    $get_venue_id['profilenam'] = str_replace("'", "\'", $get_venue_id['profilenam']);
//            $mail = mail($get_venue_id['email'], 'You have a new Announce Arrival.', "{$getUserInformation['profilenam']} has made an appearance @ {$get_venue_id['profilenam']}.<a href='http://www.socialnightlife.com/index.php?pg=userhome&usr=$userid&hotpress_id={$appVenueStatus['last_id']}&email_from_appearance=1' >Click here</a> to log in and view details.", $headers);
	    

	    $appVenueStatus['user_id'] = $userid;
	    $appVenueStatus['venue_id'] = $venue_id;
		$appVenueStatus['announce_id'] = $getAnnounceId;
		$appVenueStatus['time'] = $time;
		
		/* get badges future */
	    $badgesResult = badgesFeature($appVenueStatus['user_id'],$appVenueStatus['venue_id'],$appVenueStatus['time'],$appVenueStatus['announce_id']);
	    $appVenueStatus['badges']=$badgesResult;
	}
	
        return $appVenueStatus;
    }

//end of app_announce_arr_list()

    /*  function app_reward()
      Purpose    : to disply reward for perticular venue
      Parameters : $xmlrequest : Request array for appearance reward
      Returns    : array of reward */

    function app_reward($xmlRequest) {
        // if (DEBUG)
        writelog("appearance.class.php :: app_reward() :: ", "Starts Here ", false);

        $appCurrStatus = array();
        $userId = mysql_real_escape_string($xmlRequest['AppReward']['userId']);
        $venueId = mysql_real_escape_string($xmlRequest['AppReward']['venueId']);

        $queryAppGeneral = "SELECT distinct reward_type,reward_title,reward_description,start_time,exp_time,no_of_app_required FROM users_reward WHERE nightlife_profile_id IN (SELECT mem_id FROM members WHERE mem_id='$venueId') AND reward_type='0' AND exp_time >= NOW() ORDER BY created DESC LIMIT 0,1";
		$queryAppAmbss = "SELECT distinct reward_type,reward_title,reward_description,start_time,exp_time,no_of_app_required FROM users_reward WHERE nightlife_profile_id IN (SELECT mem_id FROM members WHERE mem_id='$venueId') AND reward_type='1' AND exp_time >= NOW() ORDER BY created DESC LIMIT 0,1";

        //if (DEBUG) {
        writelog("appearance.class.php :: app_reward() :: Query to get app reward : ", $queryAppearance, false);
        writelog("appearance.class.php :: app_reward() :: Query to get app reward : ", $queryAppAmbss, false);
        //  }
        $appCurrStatusGen = execute_query($queryAppGeneral, false, "select");
        $appCurrStatusAmbb = execute_query($queryAppAmbss, false, "select");
        if (!empty($appCurrStatusGen) && (!empty($appCurrStatusAmbb))) {
            $appCurrStatus['gen']['reward_type'] = $appCurrStatusGen['reward_type'];
            $appCurrStatus['gen']['reward_title'] = isset($appCurrStatusGen['reward_title']) && ($appCurrStatusGen['reward_title']) ? $appCurrStatusGen['reward_title'] : NULL;
            $appCurrStatus['gen']['reward_description'] = isset($appCurrStatusGen['reward_description']) && ($appCurrStatusGen['reward_description']) ? $appCurrStatusGen['reward_description'] : NULL;
            $appCurrStatus['gen']['app_required'] = isset($appCurrStatusGen['no_of_app_required']) && ($appCurrStatusGen['no_of_app_required']) ? $appCurrStatusGen['no_of_app_required'] : NULL;
            $appCurrStatus['gen']['start_time'] = isset($appCurrStatusGen['start_time']) && ($appCurrStatusGen['start_time']) ? $appCurrStatusGen['start_time'] : NULL;
            $appCurrStatus['gen']['exp_time'] = isset($appCurrStatusGen['exp_time']) && ($appCurrStatusGen['exp_time']) ? $appCurrStatusGen['exp_time'] : NULL;
            $appCurrStatus['gen']['note'] = '*Appearance during this period will not count towards the next rewards.';
            $appCurrStatus['ambss']['reward_type'] = isset($appCurrStatusAmbb['reward_type']) && ($appCurrStatusAmbb['reward_type']) ? $appCurrStatusAmbb['reward_type'] : NULL;
            $appCurrStatus['ambss']['reward_title'] = isset($appCurrStatusAmbb['reward_title']) && ($appCurrStatusAmbb['reward_title']) ? $appCurrStatusAmbb['reward_title'] : NULL;
            $appCurrStatus['ambss']['reward_description'] = isset($appCurrStatusAmbb['reward_description']) && ($appCurrStatusAmbb['reward_description']) ? $appCurrStatusAmbb['reward_description'] : NULL;
            $appCurrStatus['ambss']['app_required'] = isset($appCurrStatusAmbb['no_of_app_required']) && ($appCurrStatusAmbb['no_of_app_required']) ? $appCurrStatusAmbb['no_of_app_required'] : NULL;
            $appCurrStatus['ambss']['start_time'] = isset($appCurrStatusAmbb['start_time']) && ($appCurrStatusAmbb['start_time']) ? $appCurrStatusAmbb['start_time'] : NULL;
            $appCurrStatus['ambss']['exp_time'] = isset($appCurrStatusAmbb['exp_time']) && ($appCurrStatusAmbb['exp_time']) ? $appCurrStatusAmbb['exp_time'] : NULL;
            //  if (DEBUG) {
            writelog("Appearance:app_reward:", $appCurrStatusGen, true);
            writelog("Appearance:app_reward:", $appCurrStatusAmbb, true);
            writelog("appearance.class.php :: app_reward() :: ", "End Here ", false);
            //  }
//	    print_r($appCurrStatus);
            return $appCurrStatus;
        } else {
            return array();
        }
    }

//end of app_reward()

    /*  function app_get_event_tag()
      Purpose    : to disply reward for perticular venue
      Parameters : $xmlrequest : Request array for events for tagging
      $pageNumber : Current page Number
      $limit      : no. of results to be display on each page
      Returns    : array of events for tagging */

    function app_get_event_tag($xmlRequest, $pageNumber, $limit) {
        // if (DEBUG)
        writelog("appearance.class.php :: app_get_event_tag() :: ", "Starts Here ", false);

        if ($pageNumber) {
            $lowerLimit = ($pageNumber - 1) * $limit;
        } else {
            $lowerLimit = 0;
        }

        $appEventList = array();
        $venueId = mysql_real_escape_string($xmlRequest['AppGetAllEventTag']['venueId']);
        $latitude = mysql_real_escape_string($xmlRequest['AppGetAllEventTag']['venueLatitude']);
        $longitude = mysql_real_escape_string($xmlRequest['AppGetAllEventTag']['venueLongitude']);

//            $queryAppEvents = "SELECT SQL_CALC_FOUND_ROWS *,( 3956 *2 * ASIN( SQRT( POWER( SIN( ( $latitude - abs( latitude ) ) * pi( ) /180 /2 ) , 2 ) + COS( $latitude * pi( ) /180 ) * COS( abs( latitude ) * pi( ) /180 ) * POWER( SIN( (
//          abs( $longitude ) - abs( longitude ) ) * pi( ) /180 /2 ) , 2 ) ) )
//          ) AS distance FROM event_list GROUP BY even_id LIMIT $lowerLimit,$limit";
        //$queryAppEvents = "SELECT SQL_CALC_FOUND_ROWS * FROM event_list WHERE even_stat >='" . (time()) . "' AND even_dt >='" . (time()) . "' order by even_dt ASC LIMIT $lowerLimit,$limit";
        $queryAppEvents = "SELECT SQL_CALC_FOUND_ROWS * FROM event_list WHERE even_stat >='" . (time()) . "' AND even_own='$venueId' order by even_dt ASC LIMIT $lowerLimit,$limit";

        // if (DEBUG) {
        writelog("appearance.class.php :: app_get_event_tag() :: Query to get app events list : ", $queryAppEvents, false);
        // }
        $appEventList = execute_query($queryAppEvents, true, "select");
        $appEventList['Total'] = execute_query("SELECT FOUND_ROWS() as TotalRecords ;", false, 'select');

        //  if (DEBUG) {
        writelog("Appearance:app_get_event_tag:", $appEventList, true);
        writelog("appearance.class.php :: app_get_event_tag() :: ", "End Here ", false);
        //  }
        if (!empty($appEventList)) {
            return $appEventList;
        } else {
            return array();
        }
    }

//end of app_get_event_tag()

    /*  function appearance_photo_upload()
      Purpose    : ?
      Parameters : $xmlrequest : Request array for apearance photo upload

      Returns    : ? */

    function appearance_photo_upload($xmlrequest) {
        // if (DEBUG)
        writelog("appearance.class.php :: appearance_photo_upload() : ", "Start Here ", false);

        $error = array();
        $error = photo_upload($xmlrequest);
        //  if (DEBUG) {
        writelog("appearance.class.php :: appearance_photo_upload() : ", $error, true);
        writelog("appearance.class.php :: appearance_photo_upload() : ", "End Here ", false);
        //  }
        return $error;
    }

//end of appearance_photo_upload()

    /*  function appearance_photo_upload_valid()
      Purpose    : ?
      Parameters : $xmlrequest : Request array for appearance photo upload valid
      Returns    : ? */

    function appearance_photo_upload_valid($xmlrequest) {
        // if (DEBUG)
        writelog("appearance.class.php :: appearance_photo_upload_valid() : ", "Start Here ", false);

        $error = array();
        $error = photo_upload_valid($xmlrequest);
        //  if (DEBUG) {
        writelog("appearance.class.php :: appearance_photo_upload_valid() : ", $error, true);
        writelog("appearance.class.php :: appearance_photo_upload_valid() : ", "End Here ", false);
        // }
        return $error;
    }

// end of appearance_photo_upload_valid()

    /*  function delete_appearance_comment()
      Purpose    : to delete the comment made on appearance
      Parameters : $xmlrequest : Request array for deleting appearance status comment
      Returns    : success status */

    function delete_appearance_comment($xmlrequest) {
        $userId = mysql_real_escape_string($xmlrequest['DeleteAppearanceComment']['userId']);
        $commentId = mysql_real_escape_string($xmlrequest['DeleteAppearanceComment']['commentId']);

        $error = array();
        $query = "DELETE FROM app_entourage_comments WHERE (comment_by_id ='$userId' AND id='$commentId')";
        // if (DEBUG)
        writelog("Appearance:delete_appearance_comment()", $query, false);

        $result = execute_query($query, false, "delete");
        $affected_row = isset($result['count']) && ($result['count']) ? $result['count'] : 0;
        $error = error_CRUD($xmlrequest, $affected_row);

        if ((isset($error['DeleteAppearanceComment']['successful_fin'])) && (!$error['DeleteAppearanceComment']['successful_fin'])) {
            return $error;
        }
        return $error;
    }

// end of delete_appearance_comment()

    /*  function appearanceList()
      Purpose    : to display the appearances made by nightsite profile type users
      Parameters : $xmlrequest       : Request array for appearance made list
      $response_message : ?
      Returns    : response for appearances list in JSON fromat */

    function appearanceList($response_message, $xmlrequest) {

        global $return_codes;
        $pageNumber = $xmlrequest['AppEntourageList']['pageNumber'];
        $appearanceListing = array();
        $appearanceListing = $this->appearance_list($xmlrequest, $pageNumber, 20);
        if (isset($appearanceListing['count']) && ($appearanceListing['count'])) {
            $count = $appearanceListing['count'];
        } else {
            $count = 0;
        }
        $str = '';
        if (!empty($appearanceListing)) {

            for ($i = 0; $i < $count; $i++) {
                $appearanceListing[$i]['userInfo']['user_photo_thumb'] = isset($appearanceListing[$i]['userInfo']['user_is_facebook_user']) && (strlen($appearanceListing[$i]['userInfo']['user_photo_thumb']) > 7) && ($appearanceListing[$i]['userInfo']['user_is_facebook_user'] == 'y' || $appearanceListing[$i]['userInfo']['user_is_facebook_user'] == 'Y') ? $appearanceListing[$i]['userInfo']['user_photo_thumb'] : ((isset($appearanceListing[$i]['userInfo']['user_photo_thumb']) && (strlen($appearanceListing[$i]['userInfo']['user_photo_thumb']) > 7)) ? $this->profile_url . $appearanceListing[$i]['userInfo']['user_photo_thumb'] : $this->profile_url . default_images1($appearanceListing[$i]['userInfo']['gender'], $appearanceListing[$i]['userInfo']['profile_type']));
                //$hotpress[$i]['user_profile']['profileImageUrl1'] = ((isset($hotpress[$i]['user_profile']['is_facebook_user'])) && (strlen($hotpress[$i]['user_profile']['profileImageUrl']) > 7) && ($hotpress[$i]['user_profile']['is_facebook_user'] == 'y' || $hotpress[$i]['user_profile']['is_facebook_user'] == 'Y')) ? $hotpress[$i]['user_profile']['profileImageUrl'] : ((isset($hotpress[$i]['user_profile']['profileImageUrl']) && (strlen($hotpress[$i]['user_profile']['profileImageUrl']) > 7)) ? $this->profile_url . $hotpress[$i]['user_profile']['profileImageUrl'] : $this->profile_url . default_images($hotpress[$i]['user_profile']['gender'], $hotpress[$i]['user_profile']['profile_type']));
                $date = isset($appearanceListing[$i]['date']) && ($appearanceListing[$i]['date']) ? $appearanceListing[$i]['date'] : NULL;
                $time = isset($appearanceListing[$i]['time']) && ($appearanceListing[$i]['time']) ? $appearanceListing[$i]['time'] : NULL;
                $appearanceListing[$i]['read'] = isset($appearanceListing[$i]['read']) && ($appearanceListing[$i]['read']) ? $appearanceListing[$i]['read'] : 0;
                $timeElapsed = strtotime("$date $time");
                $arrDate = time_difference($timeElapsed);
                if (isset($appearanceListing[$i]['user_id']) && ( $appearanceListing[$i]['user_id'])) {

                    $str_temp = '{
            "checkedInUserId":"' .str_replace('"', '\"', $appearanceListing[$i]['user_id']). '",
            "announceId":"' .str_replace('"', '\"', $appearanceListing[$i]['id']). '",
            "venueId":"' .str_replace('"', '\"', $appearanceListing[$i]['venue_id']). '",
            "isRead":"' .str_replace('"', '\"', $appearanceListing[$i]['read']). '",
            "checkedInUserName":"' .str_replace('"', '\"', $appearanceListing[$i]['userInfo']['user_profilename']). '",
            "checkedInUserImageUrl":"' .str_replace('"', '\"', $appearanceListing[$i]['userInfo']['user_photo_thumb']). '",
            "date":"' .str_replace('"', '\"', $arrDate). '",
            "text":"' . " has made an appearance @ " .str_replace('"', '\"', $appearanceListing[$i]['userInfo']['venue_profilename']). '",
            "commentCount":"' .str_replace('"', '\"', $appearanceListing[$i]['comment']['cnt']). '"
}';

                    $str .= $str_temp;  //$appearanceListing[$i]['userInfo']['user_profilename']
                    $str .=',';
                }
            }
            $str = rtrim($str, ',');

            $appearanceListing['Total'] = isset($appearanceListing['Total']) && ($appearanceListing['Total']) ? $appearanceListing['Total'] : NULL;
            $response_str = response_repeat_string();
            $response_mess = '
{
   ' . $response_str . '
   "AppEntourageList":{
        "errorCode":"' . $return_codes["AppEntourageList"]["SuccessCode"] . '",
        "errorMsg":"' . $return_codes["AppEntourageList"]["SuccessDesc"] . '",
        "currentAppearancesCount":"' . $count . '",
        "totalAppearancesCount":"' . $appearanceListing['Total'] . '",
        "AppearanceList":[' . $str . ']
   }
}';
        } else {
            $response_mess = '
                {
   ' . response_repeat_string() . '
   "AppEntourageList":{
      "errorCode":"' . $return_codes["AppEntourageList"]["FailedToAddRecordCode"] . '",
      "errorMsg":"' . $return_codes["AppEntourageList"]["FailedToAddRecordDesc"] . '",
      "AppearanceList":[' . $str . ']
   }
	  }';
        }
        writelog("Appearance:appearance_list:", $response_mess, false);
        writelog("appearance.class.php :: appearance_list() :: ", "End Here ", false);
        return getValidJSON($response_mess);
    }

//end of appearanceList()

    /*  function appearanceEntourageStatus()
      Purpose    : to display the detail information about appearance
      Parameters : $xmlrequest       : Request array for detail of the appearance made
      $response_message : ?
      Returns    : response for detail of appearances Entourage in JSON fromat */

    function appearanceEntourageStatus($response_message, $xmlrequest) {

        global $return_codes;
        $appearanceEntStatus = $this->appearance_entourage_status($xmlrequest);
        $countComment = isset($appearanceEntStatus['usercomment']['count']) && ($appearanceEntStatus['usercomment']['count']) ? $appearanceEntStatus['usercomment']['count'] : 0;

        $str = '';
        $comments = '';
        $strTagEvent = '';
        $strTag = '';
        if (isset($appearanceEntStatus['tag_ent']['count']) && ($appearanceEntStatus['tag_ent']['count'])) {
            $countEnt = $appearanceEntStatus['tag_ent']['count'];
        } else {
            $countEnt = 0;
        }
        if (isset($appearanceEntStatus['tag_event']['count']) && ($appearanceEntStatus['tag_event']['count'])) {
            $countEvent = $appearanceEntStatus['tag_event']['count'];
        } else {
            $countEvent = 0;
        }

        if (!empty($appearanceEntStatus)) {

            $appearanceEntStatus['gender'] = isset($appearanceEntStatus['gender']) && ($appearanceEntStatus['gender']) ? $appearanceEntStatus['gender'] : NULL;
            $appearanceEntStatus['profile_type'] = isset($appearanceEntStatus['profile_type']) && ($appearanceEntStatus['profile_type']) ? $appearanceEntStatus['profile_type'] : NULL;
	    if(!preg_match('/^(http|https)/i',$appearanceEntStatus['photo_b_thumb']))
            $appearanceEntStatus['photo_b_thumb'] = isset($appearanceEntStatus['is_facebook_user']) && (strlen($appearanceEntStatus['photo_b_thumb']) > 7) && ($appearanceEntStatus['is_facebook_user'] == 'y' || $appearanceEntStatus['is_facebook_user'] == 'Y') ? $appearanceEntStatus['photo_b_thumb'] : ((isset($appearanceEntStatus['photo_b_thumb']) && (strlen($appearanceEntStatus['photo_b_thumb']) > 7)) ? $this->profile_url . $appearanceEntStatus['photo_b_thumb'] : $this->profile_url . default_images($appearanceEntStatus['gender'], $appearanceEntStatus['profile_type']));
            $arrDt = isset($appearanceEntStatus['date']) && ($appearanceEntStatus['date']) ? $appearanceEntStatus['date'] : NULL;
            $arrTm = isset($appearanceEntStatus['time']) && ($appearanceEntStatus['time']) ? $appearanceEntStatus['time'] : NULL;
            if (($arrDt) && ($arrTm)) {
                $timeElapsed = strtotime("$arrDt $arrTm");
                $fnTime = time_difference($timeElapsed);
            } else {
                $timeElapsed = NULL;
                $fnTime = NULL;
            }

            for ($i = 0; $i < $countEnt; $i++) {

                $appearanceEntStatus['tag_ent'][$i]['gender'] = isset($appearanceEntStatus['tag_ent'][$i]['gender']) && ($appearanceEntStatus['tag_ent'][$i]['gender']) ? $appearanceEntStatus['tag_ent'][$i]['gender'] : NULL;
                $appearanceEntStatus['tag_ent'][$i]['profile_type'] = isset($appearanceEntStatus['tag_ent'][$i]['profile_type']) && ($appearanceEntStatus['tag_ent'][$i]['profile_type']) ? $appearanceEntStatus['tag_ent'][$i]['profile_type'] : NULL;
                if(!preg_match('/^(http|https)/i',$appearanceEntStatus['tag_ent'][$i]['photo_b_thumb']))
		$appearanceEntStatus['tag_ent'][$i]['photo_b_thumb'] = isset($appearanceEntStatus['tag_ent'][$i]['is_facebook_user']) && (strlen($appearanceEntStatus['tag_ent'][$i]['photo_b_thumb']) > 7) && ($appearanceEntStatus['tag_ent'][$i]['is_facebook_user'] == 'y' || $appearanceEntStatus['tag_ent'][$i]['is_facebook_user'] == 'Y') ? $appearanceEntStatus['tag_ent'][$i]['photo_b_thumb'] : ((isset($appearanceEntStatus['tag_ent'][$i]['photo_b_thumb']) && (strlen($appearanceEntStatus['tag_ent'][$i]['photo_b_thumb']) > 7)) ? $this->profile_url . $appearanceEntStatus['tag_ent'][$i]['photo_b_thumb'] : $this->profile_url . default_images($appearanceEntStatus['tag_ent'][$i]['gender'], $appearanceEntStatus['tag_ent'][$i]['profile_type']));
                if (isset($appearanceEntStatus['tag_ent'][$i]['mem_id']) && ($appearanceEntStatus['tag_ent'][$i]['mem_id'])) {
                    $str_temp = '{
            "userId":"' .str_replace('"', '\"', $appearanceEntStatus['tag_ent'][$i]['mem_id']). '",
            "userName":"' .str_replace('"', '\"', $appearanceEntStatus['tag_ent'][$i]['profilenam']). '",
            "userPhoto":"' .str_replace('"', '\"', $appearanceEntStatus['tag_ent'][$i]['photo_b_thumb']) . '"
}';

                    $strTag .= $str_temp;
                    $strTag .=',';
                }
            }
            $strTag = rtrim($strTag, ',');

            for ($i = 0; $i < $countEvent; $i++) {

                $appearanceEntStatus['tag_event'][$i]['gender'] = isset($appearanceEntStatus['tag_event'][$i]['gender']) && ($appearanceEntStatus['tag_event'][$i]['gender']) ? $appearanceEntStatus['tag_event'][$i]['gender'] : NULL;
                $appearanceEntStatus['tag_event'][$i]['profile_type'] = isset($appearanceEntStatus['tag_event'][$i]['profile_type']) && ($appearanceEntStatus['tag_event'][$i]['profile_type']) ? $appearanceEntStatus['tag_event'][$i]['profile_type'] : NULL;

                if (is_readable($this->local_folder . $appearanceEntStatus['tag_event'][$i]['even_img'])) {
                    $sizee = getimagesize($this->local_folder . $appearanceEntStatus['tag_event'][$i]['even_img']);
                    $file_extension = substr($appearanceEntStatus['tag_event'][$i]['even_img'], strrpos($appearanceEntStatus['tag_event'][$i]['even_img'], '.') + 1);
                    $arr = explode('.', $appearanceEntStatus['tag_event'][$i]['even_img']);
                    $Id = isset($appearanceEntStatus['tag_event'][$i]['even_id']) && ( $appearanceEntStatus['tag_event'][$i]['even_id']) ? $appearanceEntStatus['tag_event'][$i]['even_id'] : NULL;

                    if (!file_exists($this->local_folder . $arr[0] . "_" . $Id . "." . $file_extension) && (preg_match('/^image\/(jp[e]?g|png|gif)$/', $sizee['mime']))) {
                        thumbanail_for_image($Id, $appearanceEntStatus['tag_event'][$i]['even_img']);
                    }

                    if (preg_match('/^image\/(jp[e]?g|png|gif)$/', $sizee['mime'])) {
                        $appearanceEntStatus['tag_event'][$i]['even_img'] = isset($appearanceEntStatus['tag_event'][$i]['even_img']) && (strlen($appearanceEntStatus['tag_event'][$i]['even_img']) > 7) ? event_image_detail($appearanceEntStatus['tag_event'][$i]['even_id'], $appearanceEntStatus['tag_event'][$i]['even_img'], 0) : NULL;
                        list($width_even_img, $height_even_img) = (isset($appearanceEntStatus['tag_event'][$i]['even_img']) && (strlen($appearanceEntStatus['tag_event'][$i]['even_img']) > 7)) ? getimagesize($this->local_folder . $appearanceEntStatus['tag_event'][$i]['even_img']) : NULL;
                    }
                }
		if(!preg_match('/^(http|https)/i',$appearanceEntStatus['tag_event'][$i]['photo_b_thumb']))
                $appearanceEntStatus['tag_event'][$i]['photo_b_thumb'] = isset($appearanceEntStatus['tag_event'][$i]['is_facebook_user']) && (strlen($appearanceEntStatus['tag_event'][$i]['photo_b_thumb']) > 7) && ($appearanceEntStatus['tag_event'][$i]['is_facebook_user'] == 'y' || $appearanceEntStatus['tag_event'][$i]['is_facebook_user'] == 'Y') ? $appearanceEntStatus['tag_event'][$i]['photo_b_thumb'] : ((isset($appearanceEntStatus['tag_event'][$i]['photo_b_thumb']) && (strlen($appearanceEntStatus['tag_event'][$i]['photo_b_thumb']) > 7)) ? $this->profile_url . $appearanceEntStatus['tag_event'][$i]['photo_b_thumb'] : $this->profile_url . default_images($appearanceEntStatus['tag_event'][$i]['gender'], $appearanceEntStatus['tag_event'][$i]['profile_type']));
                if (isset($appearanceEntStatus['tag_event'][$i]['even_id']) && ($appearanceEntStatus['tag_event'][$i]['even_id'])) {
                    $str_temp = '{
            "eventId":"' .str_replace('"', '\"', $appearanceEntStatus['tag_event'][$i]['even_id']). '",
            "eventName":"' .str_replace('"', '\"', $appearanceEntStatus['tag_event'][$i]['even_title']). '",
            "eventPhoto":"' .str_replace('"', '\"', $this->profile_url . $appearanceEntStatus['tag_event'][$i]['even_img']). '"
}';

                    $strTagEvent .= $str_temp;
                    $strTagEvent .=',';
                }
            }
            $strTagEvent = rtrim($strTagEvent, ',');

            for ($j = 0; $j < $countComment; $j++) {

                $appearanceEntStatus['usercomment'][$j]['gender'] = isset($appearanceEntStatus['usercomment'][$j]['gender']) && ($appearanceEntStatus['usercomment'][$j]['gender']) ? $appearanceEntStatus['usercomment'][$j]['gender'] : NULL;
                $appearanceEntStatus['usercomment'][$j]['profile_type'] = isset($appearanceEntStatus['usercomment'][$j]['profile_type']) && ($appearanceEntStatus['usercomment'][$j]['profile_type']) ? $appearanceEntStatus['usercomment'][$j]['profile_type'] : NULL;
		if(!preg_match('/^(http|https)/i',$appearanceEntStatus['usercomment'][$j]['photo_b_thumb']))
		$appearanceEntStatus['usercomment'][$j]['photo_b_thumb'] = isset($appearanceEntStatus['usercomment'][$j]['is_facebook_user']) && (strlen($appearanceEntStatus['usercomment'][$j]['photo_b_thumb']) > 7) && ($appearanceEntStatus['usercomment'][$j]['is_facebook_user'] == 'y' || $appearanceEntStatus['usercomment'][$j]['is_facebook_user'] == 'Y') ? $appearanceEntStatus['usercomment'][$j]['photo_b_thumb'] : ((isset($appearanceEntStatus['usercomment'][$j]['photo_b_thumb']) && (strlen($appearanceEntStatus['usercomment'][$j]['photo_b_thumb']) > 7)) ? $this->profile_url . $appearanceEntStatus['usercomment'][$j]['photo_b_thumb'] : $this->profile_url . default_images($appearanceEntStatus['usercomment'][$j]['gender'], $appearanceEntStatus['usercomment'][$j]['profile_type']));
                $appearanceEntStatus['usercomment'][$j]['id'] = isset($appearanceEntStatus['usercomment'][$j]['id']) && ($appearanceEntStatus['usercomment'][$j]['id']) ? $appearanceEntStatus['usercomment'][$j]['id'] : NULL;

                $arrDt = isset($appearanceEntStatus['usercomment'][$j]['date']) && ( $appearanceEntStatus['usercomment'][$j]['date']) ? $appearanceEntStatus['usercomment'][$j]['date'] : NULL;
                $arrTm = isset($appearanceEntStatus['usercomment'][$j]['time']) && ($appearanceEntStatus['usercomment'][$j]['time']) ? $appearanceEntStatus['usercomment'][$j]['time'] : NULL;
                if (($arrDt) && ($arrTm)) {
                    $commentElapsed = strtotime("$arrDt $arrTm");
                    $commTime = time_difference($commentElapsed);
                } else {
                    $commTime = NULL;
                }
                $postVia = isset($appearanceEntStatus['usercomment'][$j]['post_via']) && ($appearanceEntStatus['usercomment'][$j]['post_via']) ? "iPhone" : "";

                if (isset($appearanceEntStatus['usercomment'][$j]['mem_id']) && ($appearanceEntStatus['usercomment'][$j]['mem_id']) && (isset($appearanceEntStatus['usercomment'][$j]['id']) && ($appearanceEntStatus['usercomment'][$j]['id']))) {
                    $str_temp1 = '{
            "commentId":"' .str_replace('"', '\"', $appearanceEntStatus['usercomment'][$j]['id']). '",
            "commentMemId":"' .str_replace('"', '\"', $appearanceEntStatus['usercomment'][$j]['mem_id']). '",
            "comment":"' .str_replace('"', '\"', trim(preg_replace('/\s+/', ' ', $appearanceEntStatus['usercomment'][$j]['comment']))) . '",
            "commentImageUrl":"' .str_replace('"', '\"', $appearanceEntStatus['usercomment'][$j]['photo_b_thumb']). '",
            "commentUserName":"' .str_replace('"', '\"', trim(preg_replace('/\s+/', ' ', $appearanceEntStatus['usercomment'][$j]['profilenam']))) . '",
            "commentPostVia":"' .str_replace('"', '\"', $postVia). '",
            "commentTime":"' .str_replace('"', '\"', $commTime). '",
            "commentTotalReply":"' .str_replace('"', '\"', $appearanceEntStatus['usercomment'][$j]['totalReply']['cnt']). '"
}';

                    $comments .= $str_temp1;
                    $comments .=',';
                }
            }
            $comments = rtrim($comments, ',');
            $width_image_link = NULL;
            $height_image_link = NULL;
            if (is_readable($this->local_folder . $appearanceEntStatus['uploaded_image'])) {
                $sizee = getimagesize($this->local_folder . $appearanceEntStatus['uploaded_image']);
                $width_image_link = $sizee[0];
                $height_image_link = $sizee[1];
                $file_extension = substr($appearanceEntStatus['uploaded_image'], strrpos($appearanceEntStatus['uploaded_image'], '.') + 1);
                $arr = explode('.', $appearanceEntStatus['uploaded_image']);
                $Id = isset($appearanceEntStatus['id']) && ($appearanceEntStatus['id']) ? $appearanceEntStatus['id'] : NULL;

                if ((!file_exists($this->local_folder . $arr[0] . "_" . $Id . "." . $file_extension)) && ($Id) && (preg_match('/^image\/(jp[e]?g|png|gif)$/', $sizee['mime']))) {
                    thumbanail_for_image($Id, $appearanceEntStatus['uploaded_image']);
                }
                if (preg_match('/^image\/(jp[e]?g|png|gif)$/', $sizee['mime'])) {
                    $appearanceEntStatus['uploaded_image'] = isset($appearanceEntStatus['uploaded_image']) && (strlen($appearanceEntStatus['uploaded_image']) > 7) ? event_image_detail($Id, $appearanceEntStatus['uploaded_image'], 1) : NULL;

                    list($width_image_link, $height_image_link, $type) = (isset($appearanceEntStatus['uploaded_image']) && (strlen($appearanceEntStatus['uploaded_image']) > 7)) ? getimagesize($this->local_folder . $appearanceEntStatus['uploaded_image']) : NULL;
                }
            }
            $appearanceEntStatus['id'] = isset($appearanceEntStatus['id']) && ($appearanceEntStatus['id']) ? $appearanceEntStatus['id'] : NULL;
            $appearanceEntStatus['venue_id'] = isset($appearanceEntStatus['venue_id']) && ($appearanceEntStatus['venue_id']) ? $appearanceEntStatus['venue_id'] : NULL;
            $appearanceEntStatus['profilenam'] = isset($appearanceEntStatus['profilenam']) && ($appearanceEntStatus['profilenam']) ? $appearanceEntStatus['profilenam'] : NULL;
            $appearanceEntStatus['photo_b_thumb'] = isset($appearanceEntStatus['photo_b_thumb']) && ($appearanceEntStatus['photo_b_thumb']) ? $appearanceEntStatus['photo_b_thumb'] : NULL;
            $appearanceEntStatus['venueName'] = isset($appearanceEntStatus['venueName']) && ($appearanceEntStatus['venueName']) ? $appearanceEntStatus['venueName'] : NULL;
            $appearanceEntStatus['wait_in_line'] = isset($appearanceEntStatus['wait_in_line']) && ($appearanceEntStatus['wait_in_line']) ? $appearanceEntStatus['wait_in_line'] : NULL;
            $appearanceEntStatus['ratio'] = isset($appearanceEntStatus['ratio']) && ($appearanceEntStatus['ratio']) ? $appearanceEntStatus['ratio'] : NULL;
            $appearanceEntStatus['music'] = isset($appearanceEntStatus['music']) && ($appearanceEntStatus['music']) ? $appearanceEntStatus['music'] : NULL;
            $appearanceEntStatus['energy'] = isset($appearanceEntStatus['energy']) && ($appearanceEntStatus['energy']) ? $appearanceEntStatus['energy'] : NULL;
            $appearanceEntStatus['user_comment'] = isset($appearanceEntStatus['comment']) && ($appearanceEntStatus['comment']) ? $appearanceEntStatus['comment'] : NULL;
            $appearanceEntStatus['uploaded_img'] = isset($appearanceEntStatus['uploaded_image']) && ($appearanceEntStatus['uploaded_image']) ? $this->profile_url . $appearanceEntStatus['uploaded_image'] : NULL;
			$appearanceEntStatus['uploadedImage_photoID'] = isset($appearanceEntStatus['uploadedImage_photoID']) && ($appearanceEntStatus['uploadedImage_photoID']) ? $appearanceEntStatus['uploadedImage_photoID'] : NULL;
			$appearanceEntStatus['uploadedImage_albumID'] = isset($appearanceEntStatus['uploadedImage_albumID']) && ($appearanceEntStatus['uploadedImage_albumID']) ? $appearanceEntStatus['uploadedImage_albumID'] : NULL;

            if (isset($appearanceEntStatus['id']) && ($appearanceEntStatus['id'])) {
                $str = '{
            "id":"' .str_replace('"', '\"', $appearanceEntStatus['id']). '",
            "checkedInUserId":"' .str_replace('"', '\"', $appearanceEntStatus['user_id']). '",
            "announceId":"' .str_replace('"', '\"', $appearanceEntStatus['id']). '",
            "venueId":"' .str_replace('"', '\"', $appearanceEntStatus['venue_id']). '",
            "checkedInUserName":"' .str_replace('"', '\"', trim(preg_replace('/\s+/', ' ', $appearanceEntStatus['profilenam']))). '",
            "checkedInUserImageUrl":"' .str_replace('"', '\"', $appearanceEntStatus['photo_b_thumb']). '",
            "venueName":"' .str_replace('"', '\"', trim($appearanceEntStatus['venueName'])). '",
            "taggedEntourage":[' . $strTag . '],
            "taggedEvent":[' . $strTagEvent . '],
            "timeElapsed":"' .str_replace('"', '\"', $fnTime). '",
            "waitInLine":"' .str_replace('"', '\"', $appearanceEntStatus['wait_in_line']). '",
            "ratio":"' .str_replace('"', '\"', $appearanceEntStatus['ratio']). '",
            "music":"' .str_replace('"', '\"', $appearanceEntStatus['music']). '",
            "enegry":"' .str_replace('"', '\"', $appearanceEntStatus['energy']). '",
            "usr_comment":"' .str_replace('"', '\"', $appearanceEntStatus['user_comment']). '",
            "uploaded_image":"' .str_replace('"', '\"', $appearanceEntStatus['uploaded_img']). '",
			"uploadedImage_photoID":"' .str_replace('"', '\"', $appearanceEntStatus['uploadedImage_photoID']). '",
            "uploadedImage_albumID":"' .str_replace('"', '\"', $appearanceEntStatus['uploadedImage_albumID']). '",
            "width_uploaded_image":"' .str_replace('"', '\"', $width_image_link). '",
            "height_uploaded_image":"' .str_replace('"', '\"', $height_image_link). '",
            "comment":[' . $comments . ']

}';
            }

            $response_str = response_repeat_string();
            $response_mess = '
        {
           ' . $response_str . '
           "AppEntourageStatus":{
                "errorCode":"' . $return_codes["AppEntourageStatus"]["SuccessCode"] . '",
                "errorMsg":"' . $return_codes["AppEntourageStatus"]["SuccessDesc"] . '",
                "currentEntCommentCount":"' . $countComment . '",
                "AppEntourageStatus":[' . $str . ']
           }
        }';
        } else {
            $response_mess = '
                {
       ' . response_repeat_string() . '
       "AppEntourageStatus":{
          "errorCode":"' . $return_codes["AppEntourageStatus"]["FailedToAddRecordCode"] . '",
          "errorMsg":"' . $return_codes["AppEntourageStatus"]["FailedToAddRecordDesc"] . '",
          "AppEntourageStatus":[' . $str . ']
             }
	  }';
        }
        writelog("Appearance:appearance_list:", $response_mess, false);
        return getValidJSON($response_mess);
    }

//end of appearanceEntourageStatus()

    /*  function appearanceEntourageStatus()
      Purpose    : to comment on the appearance
      Parameters : $xmlrequest       : Request array for appearances Entourage status comment
      $response_message : ?
      Returns    : response for appearances Entourage status comment in JSON fromat */

    function appearanceEntStatusComment($response_message, $xmlrequest) {

        global $return_codes;
        $appStatusComment = $this->app_ent_status_comment($xmlrequest);

        if (!empty($appStatusComment)) {
            $response_str = response_repeat_string();
            $response_mess = '
        {
           ' . $response_str . '
           "AppEntStatusComment":{
                "errorCode":"' . $return_codes["AppEntStatusComment"]["SuccessCode"] . '",
                "errorMsg":"' . $return_codes["AppEntStatusComment"]["SuccessDesc"] . '",
                "insertedCommentId":"' . $appStatusComment['last_id'] . '"
           }
        }';
        } else {
            $response_mess = '
        {
           ' . response_repeat_string() . '
           "AppEntStatusComment":{
                "errorCode":"' . $return_codes["AppEntStatusComment"]["FailedToAddRecordCode"] . '",
                "errorMsg":"' . $return_codes["AppEntStatusComment"]["FailedToAddRecordDesc"] . '"
            }
        }';
        }
        writelog("Appearance:appearanceEntStatusComment:", $response_mess, false);
        return getValidJSON($response_mess);
    }

//end of appearanceEntStatusComment()

    /*  function appearanceVenueList()
      Purpose    : to display the venue list
      Parameters : $xmlrequest       : Request array for venue list
      $response_message : ?
      Returns    : response for venue list in JSON fromat */

    function appearanceVenueList($response_message, $xmlrequest) {
	$logid = $xmlrequest['AppearanceVenueList']['userId'];
	writelog("Appearance:appearanceVenueList:", "Start of the process ---> " . $logid, false, 0, 2);
	global $return_codes;
	$currPageNumber = isset($xmlrequest['AppearanceVenueList']['pageNumber']) && ($xmlrequest['AppearanceVenueList']['pageNumber']) ? $xmlrequest['AppearanceVenueList']['pageNumber'] : NULL;
	$appVenueListing = array();

	$appVenueListing = $this->appearance_venue_listing($xmlrequest, $currPageNumber, 10); //, $currPageNumber, 10
	$latitude1 = (isset($xmlrequest['AppearanceVenueList']['latitude']) && ($xmlrequest['AppearanceVenueList']['latitude']) ? $xmlrequest['AppearanceVenueList']['latitude'] : NULL);
	$longitude1 = (isset($xmlrequest['AppearanceVenueList']['longitude']) && ($xmlrequest['AppearanceVenueList']['longitude']) ? $xmlrequest['AppearanceVenueList']['longitude'] : NULL);
	$count = isset($appVenueListing['count']) && ($appVenueListing['count']) ? $appVenueListing['count'] : NULL;
	
	$str = '';
	$counter = 0;
	$j = 1;
	
	unset($appVenueListing['count']);
	unset($appVenueListing['Total']);
	unset($appVenueListing['LatLong']);
	
	$getUserInRange = LatLongInRange($latitude1, $longitude1);

	if (isset($latitude1) && ($longitude1) && isset($appVenueListing) && ($getUserInRange === TRUE)) {
	    $distance = distanceByApi1($latitude1, $longitude1, $appVenueListing);
	}

	usort($distance, 'compare_date_for_venues');
	$count_array = count($distance);
	$appVenueListing = pagination_array($distance, $currPageNumber, 20);

	if (($count_array) && ($getUserInRange === TRUE )) {
	    for ($i = $appVenueListing['begin']; $i < $appVenueListing['end']; $i++) {
		$distance = isset($appVenueListing[$i]['distance_new']) && ($appVenueListing[$i]['distance_new']) ? round(($appVenueListing[$i]['distance_new'] * 1609.344), 4) : 'Distance Not Present ';
		if (($distance <= 16093.44)) {
		    if (isset($appVenueListing[$i]['Ambassador'])) {
			$isAmbassador = '{ "AmbassadorName":"' . str_replace('"', '\"', $appVenueListing[$i]['Ambassador']['AmbassadorName']) . '",
				"AmbassadorPhoto":"' . str_replace('"', '\"', $appVenueListing[$i]['Ambassador']['AmbassadorPhoto']) . '",
				"AmbassadorId":"' . str_replace('"', '\"', $appVenueListing[$i]['Ambassador']['AmbassadorId']) . '"}';
		    } else {
			$isAmbassador = '';
		    }

		    if (isset($appVenueListing[$i]['mem_id']) && ($appVenueListing[$i]['mem_id']) && ($appVenueListing[$i]['profileName'])) {// && ($appVenueListing[$i]['city']) && ($appVenueListing[$i]['state']) && ($appVenueListing[$i]['zip']) && ($appVenueListing[$i]['country'])
			$counter++;
			$str_temp = '{
            "venueId":"' . str_replace('"', '\"', $appVenueListing[$i]['mem_id']) . '",
            "VenueName":"' . str_replace('"', '\"', trim(preg_replace('/\s+/', ' ', $appVenueListing[$i]['profileName']))) . '",
            "VenueImage":"' . str_replace('"', '\"', $appVenueListing[$i]['photo_b_thumb']) . '",
            "venueCity":"' . str_replace('"', '\"', trim($appVenueListing[$i]['city'])) . '",
            "venueState":"' . str_replace('"', '\"', trim($appVenueListing[$i]['state'])) . '",
            "venueZip":"' . str_replace('"', '\"', $appVenueListing[$i]['zip']) . '",
            "venueCountry":"' . str_replace('"', '\"', trim($appVenueListing[$i]['country'])) . '",
            "latitude":"' . str_replace('"', '\"', $appVenueListing[$i]['latitude']) . '",
            "longitude":"' . str_replace('"', '\"', $appVenueListing[$i]['longitude']) . '",
            "distance":"' . str_replace('"', '\"', $distance) . '",
            "Ambassador":[' . $isAmbassador . ']}';

			$str .= $str_temp;
			$str .=',';
		    }
		}
	    }
	    $str = rtrim($str, ',');

	    $response_str = response_repeat_string();
	    $response_mess = '
        {
	' . $response_str . '
                "AppearanceVenueList":{
                "errorCode":"' . $return_codes["AppearanceVenueList"]["SuccessCode"] . '",
                "errorMsg":"' . $return_codes["AppearanceVenueList"]["SuccessDesc"] . '",
                "CurrentVenueCount":"' . str_replace('"', '\"', $counter) . '",
                "TotalVenueCount":"' . str_replace('"', '\"', $counter) . '",
				"AppearanceLimit":"750",
                "AppearanceVenues":[' . $str . ']
	    }
        }';
	} else {
	    $response_mess = '
                {
	   ' . response_repeat_string() . '
		  "AppearanceVenueList":{
		  "errorCode":"' . $return_codes["AppearanceVenueList"]["FailedToAddRecordCode"] . '",
		  "errorMsg":"' . $return_codes["AppearanceVenueList"]["FailedToAddRecordDesc"] . '",
		  "AppearanceLimit":"750",
		  "AppearanceVenues":[' . $str . ']
	      }
	  }';
	}
	writelog("Appearance:appearanceVenueList:", "End of the process ---> ", false, 0, 2);
	writelog("Appearance:appearanceVenueList:", $response_mess, false);
	return getValidJSON($response_mess);
    }

//end of appearanceVenueList()

    /*  function appVenueDetail()
      Purpose    : to display the ambassador of the venue
      Parameters : $xmlrequest       : Request array for venue detail
      $response_message : ?
      Returns    : response for venue detail in JSON fromat */

    function appVenueDetail($response_message, $xmlrequest) {

        global $return_codes;
        $ambassador = $this->app_venue_details($xmlrequest);
        $tmmp = '';
        if (!empty($ambassador)) {
            $ambassador['gender'] = isset($ambassador['gender']) && ($ambassador['gender']) ? $ambassador['gender'] : NULL;
            $ambassador['profile_type'] = isset($ambassador['profile_type']) && ($ambassador['profile_type']) ? $ambassador['profile_type'] : NULL;
            $ambassador['Amb_photo'] = isset($ambassador['is_facebook_user']) && (strlen($ambassador['Amb_photo']) > 7) && ($ambassador['is_facebook_user'] == 'y' || $ambassador['is_facebook_user'] == 'Y') ? $ambassador['Amb_photo'] : ((isset($ambassador['Amb_photo']) && (strlen($ambassador['Amb_photo']) > 7)) ? $this->profile_url . $ambassador['Amb_photo'] : $this->profile_url . default_images($ambassador['gender'], $ambassador['profile_type']));

            if (isset($ambassador['Amb_id'])) {
                $tmmp = '{"AmbassadorId":"' . $ambassador['Amb_id'] . '",
                                "ambassadorName":"' .str_replace('"', '\"', $ambassador['Ambassador']). '",
                                "ambassadorImage":"' .str_replace('"', '\"', $ambassador['Amb_photo']). '"}';
            }

            $ambassador['photo_thumb'] = isset($ambassador['is_facebook_user']) && (strlen($ambassador['photo_thumb']) > 7) && ($ambassador['is_facebook_user'] == 'y' || $ambassador['is_facebook_user'] == 'Y') ? $ambassador['photo_thumb'] : ((isset($ambassador['photo_thumb']) && (strlen($ambassador['photo_thumb']) > 7)) ? $this->profile_url . $ambassador['photo_thumb'] : $this->profile_url . default_images($ambassador['gender'], $ambassador['profile_type']));
            $response_str = response_repeat_string();
            $ambassador['latitude'] = isset($ambassador['latitude']) && ($ambassador['latitude']) ? $ambassador['latitude'] : NULL;
            $ambassador['longitude'] = isset($ambassador['longitude']) && ($ambassador['longitude']) ? $ambassador['longitude'] : NULL;
            if (isset($ambassador['noRewardsMessage'])) {
                $rewardds = '"noRewardsMessage":"' . $ambassador['noRewardsMessage'] . '",';
            }
            $response_mess = '
                {
                   ' . $response_str . '
                        "AppVenueDetail":{
                        "errorCode":"' . $return_codes["AppVenueDetail"]["SuccessCode"] . '",
                        "errorMsg":"' . $return_codes["AppVenueDetail"]["SuccessDesc"] . '",
                            "venueId":"' .str_replace('"', '\"', $ambassador['profile_id']) . '",
                            "venueName":"' . str_replace('"', '\"', $ambassador['profilename']) . '",
                            "photoThumb":"' . str_replace('"', '\"', $ambassador['photo_thumb']). '",
                            "venueCity":"' .str_replace('"', '\"', $ambassador['city']). '",
                            "venueState":"' .str_replace('"', '\"', $ambassador['state']). '",
                            "venueZip":"' .str_replace('"', '\"', $ambassador['zip']). '",
                            "venueCountry":"' .str_replace('"', '\"', $ambassador['country']). '",
                            "venueEmail":"' .str_replace('"', '\"', $ambassador['email']). '",
                            "latitude":"' .str_replace('"', '\"', $ambassador['latitude']). '",
                            "longitude":"' .str_replace('"', '\"', $ambassador['longitude']). '",
                            "nearToVenueFlag":"' .str_replace('"', '\"', $ambassador['msgType']). '",
                            "nearToMessage":"' .str_replace('"', '\"', $ambassador['msg']). '",
                            "ambMsgType":"' .str_replace('"', '\"', $ambassador['ambMsgType']). '",
                            "ambMsg":"' .str_replace('"', '\"', $ambassador['ambMsg']). '",
                                ' . $rewardds . '
                            "Ambassador":[' .str_replace('"', '\"', $tmmp). ']
                   }
                }';
        } else {

            $response_mess = '
				{
			   ' . response_repeat_string() . '
				  "AppVenueDetail":{
				  "errorCode":"' . $return_codes["AppVenueDetail"]["FailedToAddRecordCode"] . '",
				  "errorMsg":"' . $return_codes["AppVenueDetail"]["FailedToAddRecordDesc"] . '"
			   }
				}';
        }
        writelog("Appearance:appVenueDetail:", $response_mess, false);
        return getValidJSON($response_mess);
    }

//end of appVenueDetail()

    /*  function appVenueSave()
      Purpose    : to save the information about the appearance
      Parameters : $xmlrequest       : Request array for venue appearance
      $response_message : ?
      Returns    : response for appearance made by users in JSON fromat */

    function appVenueSave($response_message, $xmlrequest) {
	
	global $return_codes;
	$appVenueListing = array();
	$appVenueListing = $this->app_announce_arr_list($xmlrequest);
	
	$str = '';
	if (isset($appVenueListing['last_id']) && ($appVenueListing['last_id'])) {
	    $last_id = isset($appVenueListing['upload']['last_id']) && $appVenueListing['upload']['last_id'] ? $appVenueListing['upload']['last_id'] : $appVenueListing['last_id'];
	    $str ='';
	    if(!empty($appVenueListing['badges']) && (is_array($appVenueListing['badges']))){
		foreach($appVenueListing['badges'] AS $kk=> $badgesWin){
		    $str .= '"'.$badgesWin.'",';
		}
		$str = rtrim($str,',');
	    }
	    $response_str = response_repeat_string();
	    $response_mess = '{
			       ' . $response_str . '
					"AnnounceArrival":{
					"errorCode":"' . $return_codes["AnnounceArrival"]["SuccessCode"] . '",
					"errorMsg":"' . $return_codes["AnnounceArrival"]["SuccessDesc"] . '",
					"autoGenrateText":"Y",
					"lastId":"' . $last_id . '",
					"badges":['.$str.']   
		}
	}';
	} else {
	    $response_mess = '{
		       ' . response_repeat_string() . '
			      "AnnounceArrival":{
			      "errorCode":"' . $return_codes["AnnounceArrival"]["FailedToAddRecordCode"] . '",
			      "errorMsg":"' . $return_codes["AnnounceArrival"]["FailedToAddRecordDesc"] . '"
		}
	}';
	}
	writelog("Appearance:appVenueSave:", $response_mess, false);
	return getValidJSON($response_mess);
    }
//end of appVenueSave()

    /*  function appRewards()
      Purpose    : to display the reward for perticular venue
      Parameters : $xmlrequest       : Request array for venue reward
      $response_message : ?
      Returns    : response for appearance reward in JSON fromat */

   function appRewards($response_message, $xmlrequest) {

        global $return_codes;
        $appReward = $this->app_reward($xmlrequest);
        if (isset($appReward) && ($appReward)) {
	    $appRewardGeneral = ($appReward['gen']['app_required']== 1)? $appReward['gen']['app_required']." appearance":$appReward['gen']['app_required']." appearances";
	    $appRewardAmb = ($appReward['ambss']['app_required']== 1)? $appReward['ambss']['app_required']." appearance":$appReward['ambss']['app_required']." appearances";
            $response_str = response_repeat_string();
            $response_mess = '
                {
                   ' . $response_str . '
                                "AppReward":{
                                "errorCode":"' . $return_codes["AppReward"]["SuccessCode"] . '",
                                "errorMsg":"' . $return_codes["AppReward"]["SuccessDesc"] . '",

            "General":[
                        {
			"rewardTypeTitle":"Loyal Fan Reward",
                        "rewardType":"' .str_replace('"', '\"', $appReward['gen']['reward_type']). '",
                        "rewardTitle":"' .str_replace('"', '\"', $appReward['gen']['reward_title']).' for '.str_replace('"', '\"', $appRewardGeneral). '",
                        "rewardDescription":"' .str_replace('"', '\"', $appReward['gen']['reward_description']). '",
                        "appRequired":"' .str_replace('"', '\"', $appRewardGeneral). '",
                        "startTime":"' .str_replace('"', '\"', date('d M Y',strtotime($appReward['gen']['start_time']))). '",
                        "expTime":"' .str_replace('"', '\"', date('d M Y',strtotime($appReward['gen']['exp_time']))). '",
                        "note":"' . str_replace('"', '\"', $appReward['gen']['note']). '"
                        }
                        ],
            "Ambassador":[
                        {
                        "rewardType":"' .str_replace('"', '\"', $appReward['ambss']['reward_type']). '",
                        "rewardTitle":"' . str_replace('"', '\"', $appReward['ambss']['reward_title']).' for our Ambassador ",
                        "rewardDescription":"' .str_replace('"', '\"', $appReward['ambss']['reward_description']). '",
                        "rewardsPerMonth":"' .str_replace('"', '\"', $appReward['ambss']['app_required']). ' rewards given out each month",
						"appRequired":"' .str_replace('"', '\"', $appRewardAmb). '",
						"startTime":"' .str_replace('"', '\"', date('d M Y',strtotime($appReward['ambss']['start_time']))) . '",
                        "expTime":"' .str_replace('"', '\"', date('d M Y',strtotime($appReward['ambss']['exp_time']))) . '",
                        "ambassadorImage":"http://www.socialnightlife.com/development/images/crown.png"
                        }
                        ]
                   }
                }';
        } else {
            $response_mess = '
				{
			   ' . response_repeat_string() . '
				  "AppReward":{
				  "errorCode":"' . $return_codes["AppReward"]["FailedToAddRecordCode"] . '",
				  "errorMsg":"' . $return_codes["AppReward"]["FailedToAddRecordDesc"] . '"
			   }
				}';
        }
        writelog("Appearance:appRewards:", $response_mess, false);
        return getValidJSON($response_mess);
    }
//end of appRewards()

    /*  function appGetEventTag()
      Purpose    : to get the list of Events for that venue
      Parameters : $xmlrequest       : Request array for appearance events for tag
      $response_message : ?
      Returns    : response for tagging events in JSON fromat */


    function appGetEventTag($response_message, $xmlrequest) {

        global $return_codes;
        $pageNumber = $xmlrequest['AppGetAllEventTag']['pageNumber'];
        $appEventsList = $this->app_get_event_tag($xmlrequest, $pageNumber, 10);
        $count = isset($appEventsList['count']) && ($appEventsList['count']) ? $appEventsList['count'] : NULL;
        if (!empty($appEventsList)) {
            $str = '';
            for ($i = 0; $i < $count; $i++) {
                $width_even_img = NULL;
                $height_even_img = NULL;
                if (is_readable($this->local_folder . $appEventsList[$i]['even_img'])) {
                    $sizee = getimagesize($this->local_folder . $appEventsList[$i]['even_img']);
                    $width_even_img = $sizee[0];
                    $height_even_img = $sizee[1];

                    $file_extension = substr($appEventsList[$i]['even_img'], strrpos($appEventsList[$i]['even_img'], '.') + 1);
                    $arr = explode('.', $appEventsList[$i]['even_img']);
                    $Id = isset($appEventsList[$i]['even_id']) && ($appEventsList[$i]['even_id']) ? $appEventsList[$i]['even_id'] : NULL;

                    if (!file_exists($this->local_folder . $arr[0] . "_" . $Id . "." . $file_extension) && (preg_match('/^image\/(jp[e]?g|png|gif)$/', $sizee['mime']))) {
                        thumbanail_for_image($Id, $appEventsList[$i]['even_img']);
                    }

                    if (preg_match('/^image\/(jp[e]?g|png|gif)$/', $sizee['mime'])) {
                        $appEventsList[$i]['even_img'] = isset($appEventsList[$i]['even_img']) && (strlen($appEventsList[$i]['even_img']) > 7) ? event_image_detail($appEventsList[$i]['even_id'], $appEventsList[$i]['even_img'], 1) : NULL;
                        list($width_even_img, $height_even_img) = (isset($appEventsList[$i]['even_img']) && (strlen($appEventsList[$i]['even_img']) > 7)) ? getimagesize($this->local_folder . $appEventsList[$i]['even_img']) : NULL;
                    }
                }
                $appEventsList[$i]['even_addr'] = strip_tags($appEventsList[$i]['even_addr'], "<br />");
                $appEventsList[$i]['even_addr'] = str_replace(array("\r\n", "\r", "\n", "<br />", "<br/>"), "\\n", $appEventsList[$i]['even_addr']);
                $appEventsList[$i]['even_img'] = $this->profile_url . $appEventsList[$i]['even_img'];

                if (isset($appEventsList[$i]['even_id']) && ($appEventsList[$i]['even_id'])) {
                    $str_temp = '{
            "eventId":"' .str_replace('"', '\"', $appEventsList[$i]['even_id']). '",
            "eventName":"' .str_replace('"', '\"', $appEventsList[$i]['even_title']). '",
            "eventPhoto":"' .str_replace('"', '\"', $appEventsList[$i]['even_img']). '",
            "width_even_img":"' .str_replace('"', '\"', $width_even_img). '",
            "height_even_img":"' .str_replace('"', '\"', $height_even_img). '",
            "eventCity":"' .str_replace('"', '\"', $appEventsList[$i]['even_city']). '",
            "eventState":"' .str_replace('"', '\"', $appEventsList[$i]['even_state']). '",
            "eventPhone":"' .str_replace('"', '\"', $appEventsList[$i]['even_phon']) . '",
            "eventzip":"' .str_replace('"', '\"', $appEventsList[$i]['even_zip']). '",
            "eventCountry":"' .str_replace('"', '\"', $appEventsList[$i]['even_country']). '",
            "eventAddress":"' .str_replace('"', '\"', trim(preg_replace('/\s+/', ' ', $appEventsList[$i]['even_addr']))). '",
            "eventLatitude":"' .str_replace('"', '\"', $appEventsList[$i]['latitude']). '",
            "eventLongitude":"' .str_replace('"', '\"', $appEventsList[$i]['longitude']). '"
        }';
                    $str .= $str_temp;
                    $str .=',';
                }
            }
            $str = substr($str, 0, strlen($str) - 1);
            $response_str = response_repeat_string();
            $response_mess = '
        {
		   ' . $response_str . '
                        "AppearanceVenueList":{
                        "errorCode":"' . $return_codes["AppGetAllEventTag"]["SuccessCode"] . '",
                        "errorMsg":"' . $return_codes["AppGetAllEventTag"]["SuccessDesc"] . '",
                        "CurrentVenueCount":"' . $count . '",
                        "TotalVenueCount":"' . $appEventsList['Total']['TotalRecords'] . '",
                        "AppearanceVenues":[' . $str . ']
		   }
        }';
        } else {

            $response_mess = '
                    {
               ' . response_repeat_string() . '
                      "AppGetAllEventTag":{
                      "errorCode":"' . $return_codes["AppGetAllEventTag"]["FailedToAddRecordCode"] . '",
                      "errorMsg":"' . $return_codes["AppGetAllEventTag"]["FailedToAddRecordDesc"] . '"
               }
	}';
        }
        writelog("Appearance:appGetEventTag:", $response_mess, false);
        return getValidJSON($response_mess);
    }

//end of appGetEventTag()

    /*  function deleteAppearanceComment()
      Purpose    : to delete comments on appearance
      Parameters : $xmlrequest       : Request array for deleting appearance comments
      $response_message : ?
      Returns    : response for deleting appearance comment in JSON fromat */


    function deleteAppearanceComment($response_message, $xmlrequest) {

        global $return_codes;
        $userinfo = array();
        $userinfo = $this->delete_appearance_comment($xmlrequest);

        if ((isset($userinfo['DeleteAppearanceComment']['successful_fin'])) && (!$userinfo['DeleteAppearanceComment']['successful_fin'])) {
            $obj_error = new Error();
            $response_message = $obj_error->error_type("DeleteAppearanceComment", $userinfo);

            $userinfocode = $response_message['DeleteAppearanceComment']['ErrorCode'];
            $userinfodesc = $response_message['DeleteAppearanceComment']['ErrorDesc'];
            $response_mess = $response_mess = get_response_string("DeleteAppearanceComment", $userinfocode, $userinfodesc);
            return $response_mess;
        }

        if ((isset($userinfo['DeleteAppearanceComment']['successful_fin'])) && ($userinfo['DeleteAppearanceComment']['successful_fin'])) {

            $response_mess = '
               {
       ' . response_repeat_string() . '
        "DeleteAppearanceComment":{
               "errorCode":"' . $return_codes["DeleteAppearanceComment"]["SuccessCode"] . '",
               "errorMsg":"' . $return_codes["DeleteAppearanceComment"]["SuccessDesc"] . '"
       }
              }';
        } else {
            $response_mess = '
                    {
       ' . response_repeat_string() . '
       "DeleteAppearanceComment":{
          "errorCode":"' . $return_codes["DeleteAppearanceComment"]["NoRecordErrorCode"] . '",
          "errorMsg":"' . $return_codes["DeleteAppearanceComment"]["NoRecordErrorDesc"] . '"

       }
              }';
        }
        writelog("Appearance:deleteAppearanceComment:", $response_mess, false);
        return $response_mess;
    }

//end of deleteAppearanceComment()
}

?>