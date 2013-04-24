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
  File-name : entourage.class.php
  Directory Path  : $/MySNL/Deliverables/Code/MySNL_WebServiceV2/hotpress.class.php/
  Author    : Brijesh Kumar
  Date    : 16/08/2011
  Modified By   : N/A
  Date : N/A

  Include Files : none
  CSS File(s)   : none

  Functions Used
  Javascript   :  none
  PHP     : friend_request,friend_request_valid,entourage_list,entourage_list_valid,mutual_entourage_list,mutual_entourage_list_valid,add_friend_request,remove_friend,add_friend_request_valid,advance_search,get_user_briefprofile_info,compare_search_output,advance_search_valid,all_entourage_list_by_name,allEntourageListByName,friendRequests,entourageList,entourageMutualList,addFriendRequest,advanceSearch,removeFriend

  DataBase Table(s)  : members,network

  Global Variable(s)  : LOCAL_FOLDER: Path where all the images save.
  PROFILE_IMAGE_SITEURL:website url

  Description: These Variables are use to store logical path of website.

  Reviwed By  :
  Reviwed Date:
 * ************************************************************************************* */

/*  class Entourage
  Purpose:Search an Entourage.
 *        Add || Remove Entourage.
 *        Search any User By certain constraint age gender,distance etc.

 * Returns : None
 */

class Entourage {

    var $profile_url = PROFILE_IMAGE_SITEURL;
    var $local_folder = LOCAL_FOLDER;
    /*
     * Function:friend_request($xmlrequest)
     * Description: This function is used to show all the friend request which user has received.
     * Parameters: $xmlrequest=>Request sent by user.
      Return: array of friend request.
     *  */

    function friend_request($xmlrequest) {
        if (DEBUG)
            writelog("entourage.class.php:friend_request():", "Start Here", false);
        $userId = mysql_real_escape_string($xmlrequest['FriendRequests']['userId']);
        $error = array();
        $query = "SELECT members.is_facebook_user,members.mem_id,members.fname,members.lname,members.profilenam,members.photo,members.profile_type,members.gender FROM members,messages_system WHERE members.mem_id=messages_system.frm_id AND(messages_system.mem_id='$userId') AND(messages_system.type='friend')";
        if (DEBUG)
            writelog("entourage.class.php:friend_request():", $query, false);
        $error = execute_query($query, true);
        if (DEBUG) {
            writelog("entourage.class.php:friend_request:", $error, true);
            writelog("entourage.class.php:friend_request():", "End Here", false);
        }

        return $error;
    }

    /*
     * Function:friend_request_valid($xmlrequest)
     * Description: to validate user.
     * Parameters: $xmlrequest=>Request sent by user.
      Return: Boolean array.
     *  */

    function friend_request_valid($xmlrequest) {
        if (DEBUG)
            writelog("entourage.class.php:friend_request_valid():", "Start Here", false);
        $error = array();
        $userId = mysql_real_escape_string($xmlrequest['FriendRequests']['userId']);
        $query = "SELECT COUNT(*) FROM members WHERE mem_id='$userId'";
        if (DEBUG)
            writelog("entourage.class.php:friend_request_valid():", $query, false);
        $result = execute_query($query, false);
        $error['successful'] = isset($result['COUNT(*)']) && ($result['COUNT(*)']) ? true : false;
        if (DEBUG) {
            writelog("entourage.class.php:friend_request_valid:", $error, true);
            writelog("entourage.class.php:friend_request_valid():", "End Here", false);
        }
        return $error;
    }

    /*
     * Function:entourage_list($xmlrequest)
     * Description: to display entourage list .
     * Parameters: $xmlrequest=>Request sent by user.
      Return: array of firends.
     *  */

    function entourage_list($xmlrequest) {
        if (DEBUG)
            writelog("entourage.class.php:entourage_list():", "Start Here", false);

        $userId = mysql_real_escape_string($xmlrequest['EntourageList']['userId']);
		$profile = mysql_real_escape_string($xmlrequest['EntourageList']['profileType']);
        $friends = array();
        $friends = get_friend_list($userId,$profile);
		$totalfriend=0;
		if ((mysql_num_rows($friends) > 0)) {
			while ($row = mysql_fetch_array($friends, MYSQL_ASSOC)) {
				$row['photo_b_thumb'] = ((isset($row['is_facebook_user'])) && (strlen($row['photo_b_thumb']) > 7) && ($row['is_facebook_user'] == 'y' || $row['is_facebook_user'] == 'Y')) ? ((strstr($row['photo_b_thumb'],"photos")!=FALSE && strstr($row['photo_b_thumb'],"http")==FALSE) ? $this->profile_url.$row['photo_b_thumb'] : $row['photo_b_thumb']) : ((isset($row['photo_b_thumb']) && (strlen($row['photo_b_thumb']) > 7)) ? $this->profile_url .$row['photo_b_thumb'] : $this->profile_url . default_images($row['gender'], $row['profile_type']));
			
			$row['gender'] = isset($row['gender']) && ($row['gender']) ? $row['gender'] : NULL;
            $row['profile_type'] = isset($row['profile_type']) && ($row['profile_type']) ? $row['profile_type'] : NULL;
            $row['profilenam'] = isset($row['profilenam']) && ($row['profilenam']) ? trim(preg_replace('/\s+/', ' ',$row['profilenam'])) : NULL;
                    $str_temp = ' {
      	      "userId":"' .str_replace('"', '\"',$row['mem_id']). '",
      	      "userName":"' .str_replace('"', '\"',$row['profilenam']). '",
              "gender":"' .str_replace('"', '\"',$row['gender']). '",
              "profileType":"' .str_replace('"', '\"',$row['profile_type']). '",
              "profileImageUrl":"' .str_replace('"', '\"',$row['photo_b_thumb']). '"
       	  }';
                    $str = $str . $str_temp;
                    $str = $str . ',';
                    $totalfriend++;
				
			}
		}
		$data=array();
		$data['str']=$str;
		$data['count']=$totalfriend;
        if (DEBUG) {
            writelog("entourage.class.php:entourage_list:", $friends, true);
            writelog("entourage.class.php:entourage_list():", "End Here", false);
        }

        return $data;
    }

    /*
     * Function:entourage_list_valid($xmlrequest)
     * Description: to validate user.
     * Parameters: $xmlrequest=>Request sent by user.
      Return: boolean array.
     *  */

    function entourage_list_valid($xmlrequest) {
        if (DEBUG)
            writelog("entourage.class.php:entourage_list_valid():", "Start Here", false);
        $userId = mysql_real_escape_string($xmlrequest['EntourageList']['userId']);
        $query = "SELECT COUNT(*) FROM members WHERE mem_id='$userId'";
        if (DEBUG)
            writelog("entourage.class.php:entourage_list_valid():", $query, false);
        $row = execute_query($query, false);
        $error['successful'] = isset($row['COUNT(*)']) && ($row['COUNT(*)'] > 0) ? true : false;
        if (DEBUG) {
            writelog("entourage.class.php:entourage_list_valid:", $error, true);
            writelog("entourage.class.php:entourage_list_valid():", "End Here", false);
        }
        return $error;
    }

    /*
     * Function:mutual_entourage_list($xmlrequest)
     * Description: to display mutual friend list.
     * Parameters: $xmlrequest=>Request sent by user.
      Return:Array of mutual friend List.
     *  */

    function mutual_entourage_list($xmlrequest) {
        if (DEBUG)
            writelog("entourage.class.php:mutual_entourage_list:", "Start Here", false);

        $userId = mysql_real_escape_string($xmlrequest['AllEntourageList']['userId']);
        $entourageId = mysql_real_escape_string($xmlrequest['AllEntourageList']['entourageId']);
        $friends_user = array();
        $friend_entourage = array();
        $mutual_friend_list = array();

        //$query_user = "SELECT mem_id as frnd FROM network WHERE frd_id='$userId'";
        $query_user = "SELECT DISTINCT n2.mem_id as frnd FROM network n1,network n2 WHERE n1.frd_id=n2.mem_id AND n1.mem_id='$userId' AND n2.frd_id='$userId'";
        if (DEBUG)
            writelog("entourage.class.php:mutual_entourage_list:", $query_user, false);

        $user_friends_id = execute_query($query_user, true);
        $query_entourage_privacy = "SELECT COUNT(*) FROM network WHERE mem_id='$userId' AND frd_id='$entourageId'";
        if (DEBUG)
            writelog("entourage.class.php:mutual_entourage_list:", $query_entourage_privacy, false);
        $row_entourage_privacy = execute_query($query_entourage_privacy, false);
        $friends['relation'] = isset($row_entourage_privacy['COUNT(*)']) && ($row_entourage_privacy['COUNT(*)']) ? true : false;

        //$query_entourage = "SELECT frd_id as frnd FROM network WHERE mem_id='$entourageId'";
        $query_entourage = "SELECT DISTINCT n2.mem_id as frnd FROM network n1,network n2 WHERE n1.frd_id=n2.mem_id AND n1.mem_id='$entourageId' AND n2.frd_id='$entourageId'";
        if (DEBUG)
            writelog("entourage.class.php:mutual_entourage_list:", $query_entourage, false);

        $entourage_friends_id = execute_query($query_entourage, true);
        if (!empty($entourage_friends_id)) {
            $mutual = 0;
            for ($i = 0; $i < $entourage_friends_id['count']; $i++)
                for ($j = 0; $j < $user_friends_id['count']; $j++) {
                    $entourage_friends_id[$i]['frnd'] = isset($entourage_friends_id[$i]['frnd']) ? $entourage_friends_id[$i]['frnd'] : 0;
                    $user_friends_id[$j]['frnd'] = isset($user_friends_id[$j]['frnd']) ? $user_friends_id[$j]['frnd'] : 0;
                    if (($entourage_friends_id[$i]['frnd'] == $user_friends_id[$j]['frnd']) && ($entourage_friends_id[$i]['frnd']) && ($user_friends_id[$j]['frnd'])) {
                        $mutual_friend_list[] = $entourage_friends_id[$i]['frnd'];
                        $mutual++;
                    }
                }
            $mutual_friend_list['count'] = $mutual;
            $mutual_friend_list = array_unique($mutual_friend_list);

            for ($i = 0; $i < $mutual_friend_list['count']; $i++) {
                $frd_id = '';
                if (isset($mutual_friend_list[$i]) && ($mutual_friend_list[$i])) {
                    $frd_id = $mutual_friend_list[$i];
                    $query_mutualprof = "SELECT is_facebook_user,profilenam,mem_id,gender,profile_type,photo_thumb,photo_b_thumb FROM members WHERE mem_id='$frd_id'";
                    $friends['MutualFriends'][] = execute_query($query_mutualprof, false);
                }
                if (DEBUG)
                    writelog("entourage.class.php:mutual_entourage_list:", $query_mutualprof, false);
            }
            $friends['MutualFriends']['count'] = $mutual_friend_list['count'];
            $query = "SELECT is_facebook_user,photo_b_thumb,mem_id,profilenam,photo_thumb,gender,profile_type,privacy FROM members WHERE mem_id='$entourageId'";
            if (DEBUG)
                writelog("entourage.class.php:mutual_entourage_list:", $query, false);
            $friends['Entourage'] = execute_query($query, false);
            if (DEBUG) {
                writelog("entourage.class.php:mutual_entourage_list:", $friends, true);
                writelog("entourage.class.php:mutual_entourage_list():", "End Here", false);
            }
            return $friends;
        }
    }

    /*
     * Function:mutual_entourage_list_valid($xmlrequest)
     * Description: to validate user.
     * Parameters: $xmlrequest=>Request sent by user.
      Return:Boolean Array.
     *  */

    function mutual_entourage_list_valid($xmlrequest) {
        if (DEBUG)
            writelog("entourage.class.php:mutual_entourage_list_valid():", "Start Here", false);
        $userId = mysql_real_escape_string($xmlrequest['AllEntourageList']['userId']);
        $entourageId = mysql_real_escape_string($xmlrequest['AllEntourageList']['entourageId']);
        $query_user = "SELECT COUNT(*) FROM members WHERE mem_id='$userId'";
        if (DEBUG)
            writelog("entourage.class.php:mutual_entourage_list_valid():", $query_user, false);
        $query_entourage = "SELECT COUNT(*) FROM members WHERE mem_id='$entourageId'";
        if (DEBUG)
            writelog("entourage.class.php:mutual_entourage_list_valid():", $query_entourage, false);
        $row_user = execute_query($query_user, false);
        $row_entourage = execute_query($query_entourage, false);
        $error['successful'] = isset($row_user['COUNT(*)']) && ($row_user['COUNT(*)']) && isset($row_entourage['COUNT(*)']) && ($row_entourage['COUNT(*)']) ? true : false;
        if (DEBUG) {
            writelog("entourage.class.php:mutual_entourage_list_valid:", $error, true);
            writelog("entourage.class.php:mutual_entourage_list_valid():", "End Here", false);
        }
        return $error;
    }

    /*
     * Function:add_friend_request($xmlrequest)
     * Description: to respond or send friend request by login user.
     * Parameters: $xmlrequest=>Request sent by user.
      Return:Boolean Array.
     *  */

   function add_friend_request($xmlrequest) {
	if (DEBUG)
	    writelog("entourage.class.php:add_friend_request():", "Start Here", false);
	$userId = mysql_real_escape_string($xmlrequest['AddAsFriendRequest']['userId']);
	$friendId = mysql_real_escape_string($xmlrequest['AddAsFriendRequest']['friendId']);
	$status = mysql_real_escape_string($xmlrequest['AddAsFriendRequest']['status']);
	$error = array();
	$query_check = "SELECT COUNT(*) FROM network WHERE (mem_id='$userId' AND frd_id='$friendId') or (mem_id='$friendId' AND frd_id='$userId')";
	$result_check = execute_query($query_check, false, "select");
	if ((isset($userId)) && (isset($friendId) && (!$result_check['COUNT(*)']) && (isset($result_check['COUNT(*)'])))) {
	    if ($status) {
			$check_for_frnd_request = "SELECT COUNT(*) FROM messages_system WHERE mem_id='$userId' AND frm_id='$friendId'";
			$frnd_request_result = execute_query($check_for_frnd_request, false, "select");

			if($frnd_request_result['COUNT(*)'] >0 ){
				$query_add_friend = "INSERT INTO network(mem_id,frd_id)VALUES('$userId','$friendId'),('$friendId','$userId')";
				$result_add_friend = execute_query($query_add_friend, false, "insert");
			}	
			if (DEBUG)
				writelog("entourage.class.php:add_friend_request():", $query_add_friend, false);
			$error['AddAsFriendRequest']['add'] = true;
			/* push_notification('friend_request', $userId, $friendId); */
	    } else {
			$query_add_friend = "DELETE FROM network WHERE (mem_id='$userId' AND frd_id='$friendId')||(mem_id='$friendId' AND frd_id='$userId')";
			$result_add_friend = execute_query($query_add_friend, false, "delete");
			if (DEBUG)
				writelog("entourage.class.php:add_friend_request():", $query_add_friend, false);
			$error['AddAsFriendRequest']['add'] = false;
	    }
		$get_user_email_id = execute_query("select profilenam,email,photo_thumb from members where mem_id='$userId'", false, "select");
	    $get_profile_user_email_id = execute_query("select profilenam,email from members where mem_id='$friendId'", false, "select");
		$query_check = "SELECT COUNT(*) FROM messages_system WHERE (mem_id='$friendId' AND frm_id='$userId' || mem_id='$userId' AND frm_id='$friendId') AND type='friend'";
	    $result_check = execute_query($query_check, false, "select");
	    if ((isset($result_check['COUNT(*)'])) && ($result_check['COUNT(*)'])) {
		$query_add_inv = "DELETE FROM messages_system WHERE (mem_id='$friendId' AND frm_id='$userId' || mem_id='$userId' AND frm_id='$friendId') AND type='friend'"; //||mem_id='$friendId' AND frm_id='$userId'
		$result_add_inv = execute_query($query_add_inv, false, "delete");
	    } else {
		$query_username = "SELECT profilenam FROM members WHERE mem_id='$userId'";
		$result_name = execute_query($query_username, false, "select");
		$name = $result_name['profilenam'] . " has sent you a new request on MySNL";
		$date = time();

		//added by satender to become a fan start
		$check_profile_type="SELECT `profile_type` FROM `members` WHERE `mem_id`=$friendId";
			$check_profile_result=execute_query($check_profile_type, false, "select");
		if($check_profile_result['profile_type']=='C')
			{
				$query_add_friend = "INSERT INTO network(mem_id,frd_id)VALUES('$userId','$friendId')";//,('$friendId','$userId')
				$result_add_friend = execute_query($query_add_friend, false, "insert");
			}else{
				//added by satender to become a fan END

		$query_add_inv = "INSERT INTO messages_system(messages_system.mem_id,messages_system.frm_id,messages_system.subject,messages_system.body,messages_system.type,messages_system.new,messages_system.folder,messages_system.date,messages_system.special,messages_system.read)VALUE('$friendId','$userId','".trim($name)."','','friend','new','inbox','$date','','') "; //||mem_id='$friendId' AND frm_id='$userId'
		
		$result_add_inv = execute_query($query_add_inv, false, "insert");
			}
	    }
	    //$result_add_friend = execute_query($query_add_friend, false, "insert");
	    //send email
	    
	    
	    if ($userId != $friendId) {
			$username = $get_user_email_id['profilenam'];
			$body1 = getname($userId) . " would like to add you to their network.Login to accept this invitation .<a href='http://www.socialnightlife.com/index.php?pg=mailbox&s=view_inv&inv_id=" . $result_add_inv['last_id'] . "' target='_blank'>Login</a> ";
			$matter = email_template($get_user_email_id['profilenam'], "$username would like to add you to their network.", $body1, $userId, $get_user_email_id['photo_thumb']);
			firemail($get_profile_user_email_id['email'], "From: socialNightLife <socialnightlife.com>\r\n", "$username would like to add you to their network.", $matter);
			push_notification('friend_request', $userId, $friendId);
		}


	    $affected_row = isset($result_add_friend['count']) && ($result_add_friend['count']) ? $result_add_friend['count'] : NULL;
	    if ($affected_row) {
		$error['AddAsFriendRequest']['successful_fin'] = true;
	    }
	} else {
	    $error['AddAsFriendRequest']['successful_fin'] = false;
	}
	if (DEBUG) {
	    writelog("entourage.class.php:add_friend_request:", $error, true);
	    writelog("entourage.class.php:add_friend_request():", "End Here", false);
	}
	return $error;
    }

    /*
     * Function:remove_friend($xmlrequest)
     * Description: to remove friend from Entourage List.
     * Parameters: $xmlrequest=>Request sent by user.
      Return:Boolean Array.
     *  */
	 
	function remove_friend($xmlrequest) {
        if (DEBUG)
            writelog("entourage.class.php:remove_friend():", "Start Here", false);
        $userId = mysql_real_escape_string($xmlrequest['RemoveFriend']['userId']);
        $friendId = mysql_real_escape_string($xmlrequest['RemoveFriend']['friendId']);
        $error = array();
        $query_remove_friend = "DELETE FROM network WHERE (mem_id='$userId' AND frd_id='$friendId')||(mem_id='$friendId' AND frd_id='$userId')";
        $result = execute_query($query_remove_friend, false, "delete");
        if (DEBUG)
            writelog("entourage.class.php:remove_friend():", $query_remove_friend, false);
        $error['RemoveFriend']['successful_fin'] = isset($result['count']) && ($result['count']) ? true : false;
        return $error;
    }

    /*
     * Function:add_friend_request_valid($xmlrequest)
     * Description: to validate whether User already in Entourage List or Not.
     * Parameters: $xmlrequest=>Request sent by user.
      Return:Boolean Array.
     *  */

    function add_friend_request_valid($xmlrequest) {

        $userId = mysql_real_escape_string($xmlrequest['AddAsFriendRequest']['userId']);
        $friendId = mysql_real_escape_string($xmlrequest['AddAsFriendRequest']['friendId']);

        $query = "SELECT COUNT(*) FROM members WHERE mem_id='$userId' || mem_id='$friendId'";
        if (DEBUG)
            writelog("entourage.class.php:add_friend_request_valid:", $query, false);
        $result = execute_query($query, false);
        $error['successful'] = isset($result['COUNT(*)']) && ($result['COUNT(*)']) ? true : false;
        if (DEBUG) {
            writelog("entourage.class.php:add_friend_request_valid:", $error, true);
            writelog("entourage.class.php:add_friend_request_valid():", "End Here", false);
        }
        return $error;
    }

    /*
     * Function:advance_search($xmlrequest)
     * Description: to search an Entourage by certain constraint like age,distance,gender.
     * Parameters: $xmlrequest=>Request sent by user.
      Return:Array of users.
     *  */

    function advance_search($xmlrequest, $pagenumber, $limit) {
        if (DEBUG)
            writelog("entourage.class.php:advance_search():", "Start Here", false);
        $lowerlimit = ($pagenumber) ? (($pagenumber - 1) * $limit) : 0;
        $userId = mysql_real_escape_string($xmlrequest['AdvanceSearch']['userId']);
        $category = mysql_real_escape_string(trim($xmlrequest['AdvanceSearch']['category']));
        $profileName = mysql_real_escape_string($xmlrequest['AdvanceSearch']['profileName']);
        $advancedSearch = mysql_real_escape_string($xmlrequest['AdvanceSearch']['advancedSearch']);
        $fromAge = mysql_real_escape_string($xmlrequest['AdvanceSearch']['fromAge']);
        $toAge = mysql_real_escape_string($xmlrequest['AdvanceSearch']['toAge']);
        $searchRadius = mysql_real_escape_string($xmlrequest['AdvanceSearch']['searchRadius']);
        $gender = mysql_real_escape_string($xmlrequest['AdvanceSearch']['gender']);
        $location = isset($xmlrequest['AdvanceSearch']['location']) && ($xmlrequest['AdvanceSearch']['location']) ? mysql_real_escape_string(trim($xmlrequest['AdvanceSearch']['location'])) : NULL;
        $profileName = trim($profileName);
        $location = trim($location);
        $category = trim($category);
        $searchResult = array();

        if (!$advancedSearch) {
            $user_info_criteria = array();
            $query_info_criteria = "SELECT mem_id FROM members WHERE profilenam LIKE '$profileName%' ORDER BY profilenam "; //,profilenam,photo,privacy,gender,profile_type
            if (DEBUG)
                writelog("entourage.class.php:advance_search():", $query_info_criteria, false);
            $result = execute_query($query_info_criteria, true);
            $query_friend = "SELECT a.mem_id FROM members a INNER JOIN (SELECT DISTINCT n2.mem_id FROM network n1,network n2 WHERE n1.frd_id=n2.mem_id AND n1.mem_id='$userId' AND n2.frd_id='$userId') b where a.mem_id=b.mem_id AND a.profilenam LIKE '$profileName%' ORDER BY profilenam"; //, members.profilenam, members.photo, members.privacy, members.gender, members.profile_type
        }
        if ((isset($advancedSearch)) && ($advancedSearch)) {
            $user = array();
            $query_condition = "WHERE";
            if ($category == "C") {
                $query_condition.= ' profile_type="C"';
                if ($location != "")
                    $query_condition.=" AND (country LIKE '$location%' OR state LIKE '$location%'OR city LIKE '$location%' )";
            } else {

                $query_condition.= ' profile_type<>"C"';
                if ($location != "")
                    $query_condition.=" AND (country LIKE '$location%' OR state LIKE '$location%'OR city LIKE '$location%' )";
                if ($gender == "both")
                    $query_condition.=" AND ((gender ='m') OR (gender ='f'))";
                else
                    $query_condition.=" AND (gender ='" . $gender . "') ";
                if ($toAge != "") {
                    $ageto = (date("Y", time())) - ($toAge);
                    $date2 = strtotime("31 december $ageto");
                }
                if ($fromAge != "") {
                    $agefrom = (date("Y", time())) - ($fromAge);
                    $date1 = strtotime("1 january $agefrom");
                }
                $ageCondition = (int) ($date2) - (int) ($date1);
                if ($ageCondition > 0) {
                    $query_condition.=" AND (birthday <'" . $date2 . "') ";
                    $query_condition.=" AND (birthday >'" . $date1 . "') ";
                }
            }
            $query = "SELECT latitude,longitude,mem_id FROM members  " . $query_condition . " ORDER BY profilenam ASC "; //WHERE (mem_id IN(SELECT frd_id FROM network WHERE mem_id ='$userId'))
            $result = execute_query($query, true, "select");
            if (DEBUG) {
                writelog("entourage.class.php :: advance_search() =>advance_search flag=1 :: ", $query, false);
            }
            if (DEBUG) {
                writelog("entourage.class.php:advance_search:", $result, true);
                writelog("entourage.class.php:advance_search():", "End Here", false);
            }
            //$query_friend = "SELECT members.latitude,members.longitude,members.mem_id FROM members " . $query_condition . " AND members.mem_id IN (SELECT DISTINCT n2.mem_id FROM network n1,network n2 WHERE n1.frd_id=n2.mem_id AND n1.mem_id='$userId' AND n2.frd_id='$userId') ORDER BY profilenam";
			$query_friend = "SELECT a.latitude,a.longitude,a.mem_id FROM members a INNER JOIN (SELECT DISTINCT n2.mem_id FROM network n1,network n2 WHERE n1.frd_id=n2.mem_id AND n1.mem_id='$userId' AND n2.frd_id='$userId') b ON a.mem_id = b.mem_id " . $query_condition . " ORDER BY profilenam";
		}
        $result_friend = execute_query($query_friend, true, "select");
        $result_friend['count'] = isset($result_friend['count']) ? $result_friend['count'] : 0;
        $searchResult_id = array();
        $count = 0;
        if (($searchRadius > 0) && ($advancedSearch)) {
            $latitude1 = isset($xmlrequest['AdvanceSearch']['latitude']) && ($xmlrequest['AdvanceSearch']['latitude']) ? mysql_real_escape_string(trim($xmlrequest['AdvanceSearch']['latitude'])) : NULL;
            $longitude1 = isset($xmlrequest['AdvanceSearch']['longitude']) && ($xmlrequest['AdvanceSearch']['longitude']) ? mysql_real_escape_string(trim($xmlrequest['AdvanceSearch']['longitude'])) : NULL;
            for ($i = 0; $i < $result_friend['count']; $i++) {
                $latitude2 = floatval(isset($result_friend[$i]['latitude']) && ($result_friend[$i]['latitude']) ? $result_friend[$i]['latitude'] : NULL);
                $longitude2 = floatval(isset($result_friend[$i]['longitude']) && ($result_friend[$i]['longitude']) ? $result_friend[$i]['longitude'] : NULL);
                if (($latitude1) && ($longitude1) && ($latitude2) && ($longitude2)) {
                    $coord = true;
                } else {
                    $coord = false;
                }
                $distance = getDistanceBetweenPointsNew($latitude1, $longitude1, $latitude2, $longitude2, 'Mi');
                $distance = isset($distance) && ($distance <= $searchRadius) ? $distance : NULL;
                if (($coord) && ($distance))
                    $searchResult_id[] = $result_friend[$i]['mem_id'];
            }
            $result['count'] = isset($result['count']) ? $result['count'] : 0;

            for ($i = 0; $i < $result['count']; $i++) {
                $latitude2 = floatval(isset($result[$i]['latitude']) && ($result[$i]['latitude']) ? $result[$i]['latitude'] : NULL);
                $longitude2 = floatval(isset($result[$i]['longitude']) && ($result[$i]['longitude']) ? $result[$i]['longitude'] : NULL);
                if (($latitude1) && ($longitude1) && ($latitude2) && ($longitude2)) {
                    $coord = true;
                } else {
                    $coord = false;
                }
                $distance = getDistanceBetweenPointsNew($latitude1, $longitude1, $latitude2, $longitude2, 'Mi');
                $distance = isset($distance) && ($distance <= $searchRadius) ? $distance : NULL;
                $result_friend[$i]['mem_id'] = isset($result_friend[$i]['mem_id']) ? $result_friend[$i]['mem_id'] : 0;
                if (($result[$i]['mem_id'] != $result_friend[$i]['mem_id'])) {
                    if (($coord) && ($distance)) {
                        $searchResult_id[] = $result[$i]['mem_id'];
                        $count++;
                    }
                }
            }
        } else {
            for ($i = 0; $i < $result_friend['count']; $i++) {
                $searchResult_id[] = $result_friend[$i]['mem_id'];
            }
            $result['count'] = isset($result['count']) ? $result['count'] : 0;
            $count = $result_friend['count'];
            for ($i = 0; $i < $result['count']; $i++) {
                $result_friend[$i]['mem_id'] = isset($result_friend[$i]['mem_id']) ? $result_friend[$i]['mem_id'] : 0;
                if (($result[$i]['mem_id'] != $result_friend[$i]['mem_id'])) {
                    $searchResult_id[] = $result[$i]['mem_id'];
                    $count++;
                }
            }
        }
        $searchResult_id['count'] = $count;
        $searchResult_id = array_unique($searchResult_id);
        $counter = 0;
        for ($i = 0; $i < $count; $i++) {
            if ((isset($searchResult_id[$i])) && ($searchResult_id[$i])) {
                $id = $searchResult_id[$i];
                $searchResult[] = $this->get_user_briefprofile_info($id);
                $counter++;
            }
        }
        $searchResult['count'] = $counter;
        return $searchResult;
    }

    /*
     * Function:get_user_briefprofile_info($xmlrequest)
     * Description: to get the user information.
     * Parameters: $xmlrequest=>Request sent by user.
      Return:Array of user information.
     *  */

    function get_user_briefprofile_info($userid) {
        if (DEBUG)
            writelog("entourage.class.php:advance_search() for UserId : " . $userid . " :: ", "Start Here ", false);
        $query_userinfo = "SELECT members.fname,members.lname,members.is_facebook_user,members.mem_id,members.profilenam, members.photo, members.privacy, members.gender, members.profile_type,members.photo_b_thumb FROM members WHERE members.mem_id='$userid'";
        if (DEBUG)
            writelog("entourage.class.php:advance_search():: query:", $query_userinfo, false);
        $userinfo = execute_query($query_userinfo, false, "select");
        if (DEBUG)
            writelog("entourage.class.php:advance_search() :: get_user_briefprofile_info() for UserId : " . $userid . " :: ", "End Here ", false);
        return $userinfo;
    }

    /*
     * Function:advance_search_valid($xmlrequest)
     * Description: to validate user.
     * Parameters: $xmlrequest=>Request sent by user.
      Return:Boolean Array.
     *  */

    function advance_search_valid($xmlrequest) {
        if (DEBUG)
            writelog("entourage.class.php:advance_search_valid():", "Start Here", false);

        $userId = mysql_real_escape_string($xmlrequest['AdvanceSearch']['userId']);

        $query = "SELECT COUNT(*) FROM members WHERE mem_id='$userId'";
        if (DEBUG)
            writelog("entourage.class.php:advance_search_valid():", $query, false);

        $result = execute_query($query, false);
        $error['successful'] = isset($result['COUNT(*)']) && ($result['COUNT(*)']) ? true : false;
        if (DEBUG) {
            writelog("entourage.class.php:advance_search_valid():", $error, true);
            writelog("entourage.class.php:advance_search_valid():", "End Here", false);
        }
        return $error;
    }

    /*
     * Function:all_entourage_list_by_name($xmlrequest)
     * Description: to get entourage list by name.
     * Parameters: $xmlrequest=>Request sent by user.
      Return:Array of entourages.
     *  */

    function all_entourage_list_by_name($xmlrequest) {
        if (DEBUG)
            writelog("entourage.class.php:all_entourage_list_by_name():", "Start Here", false);
        $userId = mysql_real_escape_string($xmlrequest['AllEntourageListByName']['userId']);
        $typeText = isset($xmlrequest['AllEntourageListByName']['typeText']) && ($xmlrequest['AllEntourageListByName']['typeText']) ? mysql_real_escape_string($xmlrequest['AllEntourageListByName']['typeText']) : NULL;
        $friends = array();
        if (isset($typeText) && ($typeText)) {
            $query_friends = "SELECT is_facebook_user,fname,lname,profilenam,mem_id ,gender,profile_type,photo_thumb,photo_b_thumb FROM members WHERE mem_id IN(SELECT DISTINCT mem_id AS frnd FROM network WHERE mem_id = '$userId' or frd_id = '$userId') AND profilenam LIKE '$typeText%' ORDER BY profilenam"; //
        } else {
            $query_friends = "SELECT is_facebook_user,fname,lname,profilenam,mem_id ,gender,profile_type,photo_thumb,photo_b_thumb FROM members WHERE mem_id IN(SELECT DISTINCT mem_id AS frnd FROM network WHERE mem_id = '$userId' or frd_id = '$userId') ORDER BY profilenam"; //AND profilenam LIKE '$typeText%'
        }
       // $friends = execute_query($query_friends, true, "select");
        //$friends['count'] = isset($friends['count']) && ($friends['count']) ? $friends['count'] : NULL;
		$totalfriend = 0;
	$str="";
	$result = execute_query_new($query_friends);
	if ((mysql_num_rows($result) > 0)) {
	    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {

		$row['gender'] = isset($row['gender']) && ($row['gender']) ? $row['gender'] : NULL;
		$row['profile_type'] = isset($row['profile_type']) && ($row['profile_type']) ? $row['profile_type'] : NULL;

		$row['photo_b_thumb'] = ((isset($row['is_facebook_user'])) && (strlen($row['photo_b_thumb']) > 7) && ($row['is_facebook_user'] == 'y' || $row['is_facebook_user'] == 'Y')) ? ((strstr($row['photo_b_thumb'], "photos") != FALSE && strstr($row['photo_b_thumb'], "http") == FALSE) ? $this->profile_url . $row['photo_b_thumb'] : $row['photo_b_thumb']) : ((isset($row['photo_b_thumb']) && (strlen($row['photo_b_thumb']) > 7)) ? $this->profile_url . $row['photo_b_thumb'] : $this->profile_url . default_images($row['gender'], $row['profile_type']));


		if (!isset($row['profilenam']) && (!$row['profilenam'])) {
		    $str = '';
		    $row['fname'] = isset($row['fname']) && ($row['fname']) ? $row['fname'] : NULL;
		    $row['lname'] = isset($row['lname']) && ($row['lname']) ? $row['lname'] : NULL;
		    $str = trim($row['fname'] . " " . $row['lname']);
		    $row['profilenam'] = isset($row['profilenam']) && ($row['profilenam']) ? $row['profilenam'] : (isset($str) && ($str) ? $str : NULL);
		}
		$str_temp = ' {
      	      "userId":"' .str_replace('"', '\"',trim($row['mem_id'])). '",
      	      "userName":"' .str_replace('"', '\"',trim($row['profilenam'])). '",
              "gender":"' .str_replace('"', '\"',trim($row['gender'])). '",
              "profileType":"' .str_replace('"', '\"',trim($row['profile_type'])). '",
              "profileImageUrl":"' .str_replace('"', '\"',trim($row['photo_b_thumb'])). '"
       	  }';
		$str = $str . $str_temp . ',';
		$totalfriend++;
	    	    } }
		
	//$friends['count'] = isset($friends['count']) && ($friends['count']) ? $friends['count'] : NULL;
		$friends['count'] = isset($totalfriend) && ($totalfriend) ? $totalfriend : NULL;
		$friends['str']=$str;
        if (DEBUG) {
            writelog("entourage.class.php:all_entourage_list_by_name:", $friends, true);
            writelog("entourage.class.php:all_entourage_list_by_name():", "End Here", false);
        }
        return $friends;
        
    }

    /* ---------------------To get Response string---------------------------------------------- */
    /* Function:allEntourageListByName($response_message,$xmlrequest)
     * Description: to get entourage list by name.
     * Parameters: $xmlrequest=>Request sent by user,
     *             $response_message=>validation array.
      Return:String contain List of entourages.
     *  */

    function allEntourageListByName($response_message, $xmlrequest) {
        if (isset($response_message['AllEntourageListByName']['SuccessCode']) && ( $response_message['AllEntourageListByName']['SuccessCode'] == '000')) {
            $friend_list = array();
            $friend_list = $this->all_entourage_list_by_name($xmlrequest);
            $str = '';
			$str = $friend_list['str'];
			$totalfriend = $friend_list['count'];
            /*$count = isset($friend_list['count']) && ( $friend_list['count']) ? $friend_list['count'] : 0;
            $totalfriend = 0;
            for ($i = 0; $i < $count; $i++) {
                $friend_list[$i]['gender'] = isset($friend_list[$i]['gender']) && ($friend_list[$i]['gender']) ? $friend_list[$i]['gender'] : NULL;
                $friend_list[$i]['profile_type'] = isset($friend_list[$i]['profile_type']) && ($friend_list[$i]['profile_type']) ? $friend_list[$i]['profile_type'] : NULL;

				
				$friend_list[$i]['photo_b_thumb'] = ((isset($friend_list[$i]['is_facebook_user'])) && (strlen($friend_list[$i]['photo_b_thumb']) > 7) && ($friend_list[$i]['is_facebook_user'] == 'y' || $friend_list[$i]['is_facebook_user'] == 'Y')) ? ((strstr($friend_list[$i]['photo_b_thumb'],"photos")!=FALSE && strstr($friend_list[$i]['photo_b_thumb'],"http")==FALSE) ? $this->profile_url.$friend_list[$i]['photo_b_thumb'] : $friend_list[$i]['photo_b_thumb']) : ((isset($friend_list[$i]['photo_b_thumb']) && (strlen($friend_list[$i]['photo_b_thumb']) > 7)) ? $this->profile_url . $friend_list[$i]['photo_b_thumb'] : $this->profile_url . default_images($friend_list[$i]['gender'], $friend_list[$i]['profile_type']));
				
				
                if (!isset($friend_list[$i]['profilenam']) && (!$friend_list[$i]['profilenam'])) {
                    $str = '';
                    $friend_list[$i]['fname'] = isset($friend_list[$i]['fname']) && ($friend_list[$i]['fname']) ? $friend_list[$i]['fname'] : NULL;
                    $friend_list[$i]['lname'] = isset($friend_list[$i]['lname']) && ($friend_list[$i]['lname']) ? $friend_list[$i]['lname'] : NULL;
                    $str = trim($friend_list[$i]['fname'] . " " . $friend_list[$i]['lname']);
                    $friend_list[$i]['profilenam'] = isset($friend_list[$i]['profilenam']) && ($friend_list[$i]['profilenam']) ? $friend_list[$i]['profilenam'] : (isset($str) && ($str) ? $str : NULL);
                }
                $str_temp = ' {
      	      "userId":"'.trim($friend_list[$i]['mem_id']).'",
      	      "userName":"'.trim($friend_list[$i]['profilenam']).'",
              "gender":"'.trim($friend_list[$i]['gender']).'",
              "profileType":"'.trim($friend_list[$i]['profile_type']).'",
              "profileImageUrl":"'.trim($friend_list[$i]['photo_b_thumb']).'"
       	  }';
                $str = $str . $str_temp . ',';
                $totalfriend++;
            }*/
            $str = substr($str, 0, strlen($str) - 1);
            $userinfocode = $response_message['AllEntourageListByName']['SuccessCode'];
            $userinfodesc = $response_message['AllEntourageListByName']['SuccessDesc'];
            $response_str = response_repeat_string();
            $response_mess = '
{
  ' . $response_str . '
   "AllEntourageListByName":{
       "errorCode":"' . $userinfocode . '",
       "errorMsg":"' . $userinfodesc . '",
      "friendsCount":"' . $totalfriend . '",
      "FriendsList":[
        ' . $str . '
      ]
   }
}';
        } else {
            $userinfocode = $response_message['AllEntourageListByName']['ErrorCode'];
            $userinfodesc = $response_message['AllEntourageListByName']['ErrorDesc'];
            $response_mess = get_response_string("AllEntourageListByName", $userinfocode, $userinfodesc);
        }
        return getValidJSON($response_mess);
    }

    /* Function:friendRequests($response_message,$xmlrequest)
     * Description: to display all friend request which user has received.
     * Parameters: $xmlrequest=>Request sent by user,
     *              $response_message=>to get the status of validation.
      Return:String contain List of friend request.
     *  */

    function friendRequests($response_message, $xmlrequest) {

        if (isset($response_message['FriendRequests']['SuccessCode']) && ( $response_message['FriendRequests']['SuccessCode'] == '000')) {
            $friend_req = array();
            $friend_req = $this->friend_request($xmlrequest);
            $count = isset($friend_req['count']) && ($friend_req['count']) ? $friend_req['count'] : 0;
            $str = '';
            for ($i = 0; $i < $count; $i++) {
                $tmpusername = ($friend_req[$i]['profilenam'] != "") ? $friend_req[$i]['profilenam'] : $friend_req[$i]['fname'] . ' ' . $friend_req[$i]['lname'];
                /*$friend_req[$i]['photo'] = ((isset($friend_req[$i]['is_facebook_user'])) && (strlen($friend_req[$i]['photo']) > 7) && ($friend_req[$i]['is_facebook_user'] == 'y' || $friend_req[$i]['is_facebook_user'] == 'Y')) ? $friend_req[$i]['photo'] : ((isset($friend_req[$i]['photo']) && (strlen($friend_req[$i]['photo']) > 7)) ? $this->profile_url . $friend_req[$i]['photo'] : $this->profile_url . default_images($friend_req[$i]['profile_type'], $friend_req[$i]['gender']));*/
				
				$friend_req[$i]['photo'] = ((isset($friend_req[$i]['is_facebook_user'])) && (strlen($friend_req[$i]['photo']) > 7) && ($friend_req[$i]['is_facebook_user'] == 'y' || $friend_req[$i]['is_facebook_user'] == 'Y')) ? ((strstr($friend_req[$i]['photo'],"photos")!=FALSE && strstr($friend_req[$i]['photo'],"http")==FALSE) ? $this->profile_url.$friend_req[$i]['photo'] : $friend_req[$i]['photo']) : ((isset($friend_req[$i]['photo']) && (strlen($friend_req[$i]['photo']) > 7)) ? $this->profile_url . $friend_req[$i]['photo'] : $this->profile_url . default_images($friend_req[$i]['gender'], $friend_req[$i]['profile_type']));
				
				
                $str_temp = '{
            "userId":"' .str_replace('"', '\"',$friend_req[$i]['mem_id']). '",
            "userName":"' .str_replace('"', '\"',$tmpusername). '",
            "userProfileImgUrl":"' .str_replace('"', '\"',$friend_req[$i]['photo']). '",
            "userProfileType":"' .str_replace('"', '\"',$friend_req[$i]['profile_type']). '"
         }';
                $str = $str . $str_temp;
                $str = $str . ',';
            }
            $str = substr($str, 0, strlen($str) - 1);
            $str = stripslashes($str);
            $userinfocode = $response_message['FriendRequests']['SuccessCode'];
            $userinfodesc = $response_message['FriendRequests']['SuccessDesc'];
            $response_str = response_repeat_string();
            $response_mess = '
{
   ' . $response_str . '
   "FriendRequests":{
      "errorCode":"' . $userinfocode . '",
      "errorMsg":"' . $userinfodesc . '",
      "requestsCount":"' . $count . '",
      "Requests":[
         ' . $str . '
      ]
   }
}';
        } else {
            $userinfocode = $response_message['FriendRequests']['ErrorCode'];
            $userinfodesc = $response_message['FriendRequests']['ErrorDesc'];
            $response_mess = $response_mess = get_response_string("FriendRequests", $userinfocode, $userinfodesc);
        }
        if (DEBUG)
            writelog("Response:friendRequests():", $response_mess, false);
        return getValidJSON($response_mess);
    }

    /* Function:entourageList($xmlrequest)
     * Description: to display Entourage List.
     * Parameters: $xmlrequest=>Request sent by user,
     *             $response_message=>to get the array of validation.
      Return:String.
     *  */

    function entourageList($response_message, $xmlrequest) {
        if (isset($response_message['EntourageList']['SuccessCode']) && ( $response_message['EntourageList']['SuccessCode'] == '000')) {
            $friend_list = array();
            $friendType = mysql_real_escape_string($xmlrequest['EntourageList']['profileType']);
            $friend_list = $this->entourage_list($xmlrequest);
            $str = $friend_list['str'];
			$totalfriend = 0;
            $totalfriend = isset($friend_list['count']) && ($friend_list['count']) ? $friend_list['count'] : NULL;
            
           /* for ($i = 0; $i < $count; $i++) {
					$friend_list[$i]['photo_b_thumb'] = ((isset($friend_list[$i]['is_facebook_user'])) && (strlen($friend_list[$i]['photo_b_thumb']) > 7) && ($friend_list[$i]['is_facebook_user'] == 'y' || $friend_list[$i]['is_facebook_user'] == 'Y')) ? ((strstr($friend_list[$i]['photo_b_thumb'],"photos")!=FALSE && strstr($friend_list[$i]['photo_b_thumb'],"http")==FALSE) ? $this->profile_url.$friend_list[$i]['photo_b_thumb'] : $friend_list[$i]['photo_b_thumb']) : ((isset($friend_list[$i]['photo_b_thumb']) && (strlen($friend_list[$i]['photo_b_thumb']) > 7)) ? $this->profile_url .$friend_list[$i]['photo_b_thumb'] : $this->profile_url . default_images($friend_list[$i]['gender'], $friend_list[$i]['profile_type']));
					
					
                    $friend_list[$i]['gender'] = isset($friend_list[$i]['gender']) && ($friend_list[$i]['gender']) ? $friend_list[$i]['gender'] : NULL;
                    $friend_list[$i]['profile_type'] = isset($friend_list[$i]['profile_type']) && ($friend_list[$i]['profile_type']) ? $friend_list[$i]['profile_type'] : NULL;
                    $friend_list[$i]['profilenam'] = isset($friend_list[$i]['profilenam']) && ($friend_list[$i]['profilenam']) ? trim(preg_replace('/\s+/', ' ', $friend_list[$i]['profilenam'])) : NULL;
                    $str_temp = ' {
      	      "userId":"' . $friend_list[$i]['mem_id'] . '",
      	      "userName":"' . $friend_list[$i]['profilenam'] . '",
              "gender":"' . $friend_list[$i]['gender'] . '",
              "profileType":"' . $friend_list[$i]['profile_type'] . '",
              "profileImageUrl":"' . $friend_list[$i]['photo_b_thumb'] . '"
       	  }';
                    $str = $str . $str_temp;
                    $str = $str . ',';
                    $totalfriend++;
               // }
            }*/
            $str = substr($str, 0, strlen($str) - 1);
            $userinfocode = $response_message['EntourageList']['SuccessCode'];
            $userinfodesc = $response_message['EntourageList']['SuccessDesc'];
            $response_str = response_repeat_string();
            $response_mess = '
{
  ' . $response_str . '
   "EntourageList":{
       "errorCode":"' . $userinfocode . '",
       "errorMsg":"' . $userinfodesc . '",
      "friendsCount":"' . $totalfriend . '",
      "FriendsList":[
        ' . $str . '
      ]
   }
}';
        } else {
            $userinfocode = $response_message['EntourageList']['ErrorCode'];
            $userinfodesc = $response_message['EntourageList']['ErrorDesc'];
            $response_mess = get_response_string("EntourageList", $userinfocode, $userinfodesc);
        }
        return $response_mess;
    }

    /* Function:entourageMutualList($response_message,$xmlrequest)
     * Description: to display Mutual Entourage List between two users.
     * Parameters: $xmlrequest=>Request sent by user,
     *              $response_message=>array of validation.
      Return:String.
     *  */

    function entourageMutualList($response_message, $xmlrequest) {
        global $return_codes;
        //if (isset($response_message['AllEntourageList']['SuccessCode']) && ( $response_message['AllEntourageList']['SuccessCode'] == '000')) {
        $friend_list = array();
        $userId = mysql_real_escape_string($xmlrequest['AllEntourageList']['userId']);
        $entourageId = mysql_real_escape_string($xmlrequest['AllEntourageList']['entourageId']);
        $friend_list = $this->mutual_entourage_list($xmlrequest);
        $str = '';
        $str_mut = '';
        $count = isset($friend_list['Friends']['count']) && ($friend_list['Friends']['count']) ? $friend_list['Friends']['count'] : NULL;
        $count_mut = isset($friend_list['MutualFriends']['count']) && ($friend_list['MutualFriends']['count']) ? $friend_list['MutualFriends']['count'] : NULL;
        for ($i = 0; $i < $count_mut; $i++) {
            if ((isset($friend_list['MutualFriends'][$i])) && ($friend_list['MutualFriends'][$i])) {

                /*$friend_list['MutualFriends'][$i]['photo_b_thumb'] = isset($friend_list['MutualFriends'][$i]['is_facebook_user']) && (strlen($friend_list['MutualFriends'][$i]['photo_b_thumb']) > 7) && ($friend_list['MutualFriends'][$i]['is_facebook_user'] == 'y' || $friend_list['MutualFriends'][$i]['is_facebook_user'] == 'Y') ? $friend_list['MutualFriends'][$i]['photo_b_thumb'] : ((isset($friend_list['MutualFriends'][$i]['photo_b_thumb']) && (strlen($friend_list['MutualFriends'][$i]['photo_b_thumb']) > 7)) ? $this->profile_url . $friend_list['MutualFriends'][$i]['photo_b_thumb'] : $this->profile_url . default_images($friend_list['MutualFriends'][$i]['gender'], $friend_list['MutualFriends'][$i]['profile_type']));*/
				
				
				$friend_list['MutualFriends'][$i]['photo_b_thumb'] = ((isset($friend_list['MutualFriends'][$i]['is_facebook_user'])) && (strlen($friend_list['MutualFriends'][$i]['photo_b_thumb']) > 7) && ($friend_list['MutualFriends'][$i]['is_facebook_user'] == 'y' || $friend_list['MutualFriends'][$i]['is_facebook_user'] == 'Y')) ? ((strstr($friend_list['MutualFriends'][$i]['photo_b_thumb'],"photos")!=FALSE && strstr($friend_list['MutualFriends'][$i]['photo_b_thumb'],"http")==FALSE) ? $this->profile_url.$friend_list['MutualFriends'][$i]['photo_b_thumb'] : $friend_list['MutualFriends'][$i]['photo_b_thumb']) : ((isset($friend_list['MutualFriends'][$i]['photo_b_thumb']) && (strlen($friend_list['MutualFriends'][$i]['photo_b_thumb']) > 7)) ? $this->profile_url . $friend_list['MutualFriends'][$i]['photo_b_thumb'] : $this->profile_url . default_images($friend_list['MutualFriends'][$i]['gender'], $friend_list['MutualFriends'][$i]['profile_type']));
				
				
                $friend_list['MutualFriends'][$i]['profilenam'] = isset($friend_list['MutualFriends'][$i]['profilenam']) ? $friend_list['MutualFriends'][$i]['profilenam'] : "";
                $friend_list['MutualFriends'][$i]['profile_type'] = isset($friend_list['MutualFriends'][$i]['profile_type']) ? $friend_list['MutualFriends'][$i]['profile_type'] : "";
                $friend_list['MutualFriends'][$i]['gender'] = isset($friend_list['MutualFriends'][$i]['gender']) ? $friend_list['MutualFriends'][$i]['gender'] : "";

                $str_temp = ' {
      	      "userId":"' .str_replace('"', '\"',$friend_list['MutualFriends'][$i]['mem_id']). '",
      	      "userName":"' .str_replace('"', '\"',$friend_list['MutualFriends'][$i]['profilenam']). '",
              "gender":"' .str_replace('"', '\"',$friend_list['MutualFriends'][$i]['gender']). '",
              "profileType":"' .str_replace('"', '\"',$friend_list['MutualFriends'][$i]['profile_type']). '",
              "profileImageUrl":"' .str_replace('"', '\"',$friend_list['MutualFriends'][$i]['photo_b_thumb']). '"
       	  }';
                $str_mut = $str_mut . $str_temp;
                $str_mut = $str_mut . ',';
            }
        }
        $str_mut = substr($str_mut, 0, strlen($str_mut) - 1);
        if (!empty($str_mut)) {
            $friend_list['Friends'][$i]['profilenam'] = isset($friend_list['Friends'][$i]['profilenam']) ? $friend_list['Friends'][$i]['profilenam'] : "";
            $friend_list['Entourage']['photo_b_thumb'] = ((isset($friend_list['Entourage']['is_facebook_user'])) && (strlen($friend_list['Entourage']['photo_b_thumb']) > 7) && ($friend_list['Entourage']['is_facebook_user'] == 'y' || $friend_list['Entourage']['is_facebook_user'] == 'Y')) ? $friend_list['Entourage']['photo_b_thumb'] : ((isset($friend_list['Entourage']['photo_b_thumb']) && (strlen($friend_list['Entourage']['photo_b_thumb']) > 7)) ? $this->profile_url . $friend_list['Entourage']['photo_b_thumb'] : $this->profile_url . default_images($friend_list['Entourage']['gender'], $friend_list['Entourage']['profile_type']));
            $userinfocode = $return_codes["AllEntourageList"]["SuccessCode"];
            $userinfodesc = $return_codes["AllEntourageList"]["SuccessDesc"];
            $response_str = response_repeat_string();
            $response_mess = '
{
    ' . $response_str . '
   "AllEntourageList":{
      "userId":"' .str_replace('"', '\"',$friend_list['Entourage']['mem_id']). '",
      "userName":"' .str_replace('"', '\"',$friend_list['Entourage']['profilenam']). '",
      "profileImageUrl":"' .str_replace('"', '\"',$friend_list['Entourage']['photo_b_thumb']). '",
      "profileType":"' .str_replace('"', '\"',$friend_list['Entourage']['profile_type']). '",
      "gender":"' .str_replace('"', '\"',$friend_list['Entourage']['gender']). '",
      "Connection":"' .str_replace('"', '\"',$friend_list['relation']). '",
      "Profile":"' .str_replace('"', '\"',$friend_list['Entourage']['privacy']). '",
      "MutualFriendsList":[
                           ' . $str_mut . '
                           ],

      "errorCode":"' . $userinfocode . '",
      "errorMsg":"' . $userinfodesc . '"
   }
}';
            // }
        } else {
            $userinfocode = $return_codes["AllEntourageList"]["FailedToAddRecordCode"];
            $userinfodesc = $return_codes["AllEntourageList"]["FailedToAddRecordDesc"];
            $response_mess = get_response_string("AllEntourageList", $userinfocode, $userinfodesc);
        }
        return getValidJSON($response_mess);
    }

    /* Function:addFriendRequest($response_message,$xmlrequest)
     * Description: to sent and respond friend request by users.
     * Parameters: $xmlrequest=>Request sent by user,
     *             $response_message=>array of validation.
      Return:String.
     *  */

    function addFriendRequest($response_message, $xmlrequest) {
        if (isset($response_message['AddAsFriendRequest']['SuccessCode']) && ( $response_message['AddAsFriendRequest']['SuccessCode'] == '000')) {
            $userinfo = array();
            $userinfo = $this->add_friend_request($xmlrequest);
            if ((isset($userinfo['AddAsFriendRequest']['successful_fin'])) && (!$userinfo['AddAsFriendRequest']['successful_fin'])) {
                $obj_error = new Error();
                $response_message = $obj_error->error_type("AddAsFriendRequest", $userinfo);
                $userinfocode = $response_message['AddAsFriendRequest']['ErrorCode'];
                $userinfodesc = $response_message['AddAsFriendRequest']['ErrorDesc'];
                $response_mess = $response_mess = get_response_string("AddAsFriendRequest", $userinfocode, $userinfodesc);
                return getValidJSON($response_mess);
            }
            $userinfocode = $response_message['AddAsFriendRequest']['SuccessCode'];
            $userinfodesc = isset($userinfo['AddAsFriendRequest']['add']) && ($userinfo['AddAsFriendRequest']['add']) ? 'Friend request added successfully.' : 'Friend request removed successfully.';
            $response_str = response_repeat_string();
            $response_mess = '
{
  ' . $response_str . '
   "AddAsFriendRequest":{
      "errorCode":"' . $userinfocode . '",
      "errorMsg":"' . $userinfodesc . '"
   }
}
';
        } else {
            $userinfocode = $response_message['AddAsFriendRequest']['ErrorCode'];
            $userinfodesc = $response_message['AddAsFriendRequest']['ErrorDesc'];
            $response_mess = get_response_string("AddAsFriendRequest", $userinfocode, $userinfodesc);
        }
        return getValidJSON($response_mess);
    }

    /* Function:advanceSearch($xmlrequest)
     * Description: to search an Entourage by certain criteria Like Age,gender.lat-long.
     * Parameters: $xmlrequest=>Request sent by user,
     *              $response_message=>array of validation.
      Return:String.
     *  */

    function advanceSearch($response_message, $xmlrequest) {
        if (isset($response_message['AdvanceSearch']['SuccessCode']) && ( $response_message['AdvanceSearch']['SuccessCode'] == '000')) {
            $search_result = array();

            $pagenumber = $xmlrequest['AdvanceSearch']['pageNumber'];
            $search_result = $this->advance_search($xmlrequest, $pagenumber, 10);
            $userinfocode = $response_message['AdvanceSearch']['SuccessCode'];
            $userinfodesc = $response_message['AdvanceSearch']['SuccessDesc'];
            $count = isset($search_result['count']) && ($search_result['count']) ? $search_result['count'] : NULL;
            $postcount = 0;
            $str = '';
            for ($i = 0; $i < $count; $i++) {
                $search_result[$i]['fname'] = isset($search_result[$i]['fname']) && ($search_result[$i]['fname']) ? $search_result[$i]['fname'] : NULL;
                $search_result[$i]['lname'] = isset($search_result[$i]['lname']) && ($search_result[$i]['lname']) ? $search_result[$i]['lname'] : NULL;
                $name = $search_result[$i]['fname'] . ' ' . $search_result[$i]['lname'];
                $search_result[$i]['profilenam'] = isset($search_result[$i]['profilenam']) && ($search_result[$i]['profilenam']) ? $search_result[$i]['profilenam'] : (isset($name) && ($name) ? $name : NULL);
                $search_result[$i]['gender'] = isset($search_result[$i]['gender']) ? $search_result[$i]['gender'] : "No";
                $search_result[$i]['profile_type'] = isset($search_result[$i]['profile_type']) ? $search_result[$i]['profile_type'] : "No";
                $search_result[$i]['privacy'] = isset($search_result[$i]['privacy']) ? $search_result[$i]['privacy'] : "No";
				
				
				$search_result[$i]['photo_b_thumb'] = ((isset($search_result[$i]['is_facebook_user'])) && (strlen($search_result[$i]['photo_b_thumb']) > 7) && ($search_result[$i]['is_facebook_user'] == 'y' || $search_result[$i]['is_facebook_user'] == 'Y')) ? ((strstr($search_result[$i]['photo_b_thumb'],"photos")!=FALSE && strstr($search_result[$i]['photo_b_thumb'],"http")==FALSE) ? $this->profile_url.$search_result[$i]['photo_b_thumb'] : $search_result[$i]['photo_b_thumb']) : ((isset($search_result[$i]['photo_b_thumb']) && (strlen($search_result[$i]['photo_b_thumb']) > 7)) ? $this->profile_url . $search_result[$i]['photo_b_thumb'] : $this->profile_url . default_images($search_result[$i]['gender'], $search_result[$i]['profile_type']));
				
                if ((isset($search_result[$i]['mem_id'])) && ($search_result[$i]['mem_id'])) {
                    $str_temp = '{
            "userId":"'.trim(str_replace('"', '\"',$search_result[$i]['mem_id'])).'",
            "userName":"'.trim(str_replace('"', '\"',$search_result[$i]['profilenam'])).'",
            "userProfileImgUrl":"'.trim(str_replace('"', '\"',$search_result[$i]['photo_b_thumb'])).'",
            "gender":"'.trim(str_replace('"', '\"',$search_result[$i]['gender'])).'",
            "profileType":"'.trim(str_replace('"', '\"',$search_result[$i]['profile_type'])).'",
	    "userPrivacySetting":"'.trim(str_replace('"', '\"',$search_result[$i]['privacy'])).'"
         }';
                    $postcount++;
                    $str = $str . $str_temp;
                    $str = $str . ',';
                }
            }
            $str = substr($str, 0, strlen($str) - 1);
            $response_str = response_repeat_string();
            $response_mess = '{
  ' . $response_str . '
   "AdvanceSearch":{
      "errorCode":"' . $userinfocode . '",
      "errorMsg":"' . $userinfodesc . '",
      "totalRecords":"' . $count . '",
      "resultCount":"' . $postcount . '",
      "searchList":[
         ' . $str . '
      ]
   }
}
';
        } else {
            $userinfocode = $response_message['AdvanceSearch']['ErrorCode'];
            $userinfodesc = $response_message['AdvanceSearch']['ErrorDesc'];
            $response_mess = get_response_string("AdvanceSearch", $userinfocode, $userinfodesc);
        }
        return getValidJSON($response_mess);
    }

    /* Function:removeFriend($xmlrequest)
     * Description: to remove an Entourage from users friend List.
     * Parameters: $xmlrequest=>Request sent by user,
     *              $response_message=> validation array.
      Return:String.
     *  */

    function removeFriend($response_message, $xmlrequest) {
        global $return_codes;
        $userinfo = array();
        $userinfo = $this->remove_friend($xmlrequest);
        if ((isset($userinfo['RemoveFriend']['successful_fin'])) && ($userinfo['RemoveFriend']['successful_fin'])) {
            $response_mess = '
               {
   ' . response_repeat_string() . '
    "RemoveFriend":{
           "errorCode":"' . $return_codes["RemoveFriend"]["SuccessCode"] . '",
           "errorMsg":"' . $return_codes["RemoveFriend"]["SuccessDesc"] . '"
   }
	  }';
        } else {

            $response_mess = '
                {
   ' . response_repeat_string() . '
   "RemoveFriend":{
      "errorCode":"' . $return_codes["RemoveFriend"]["NoRecordErrorCode"] . '",
      "errorMsg":"' . $return_codes["RemoveFriend"]["NoRecordErrorDesc"] . '"

   }
	  }';
        }
        return getValidJSON($response_mess);
    }
	/* Function:profileRegistration($xmlrequest)
      Description: to add user information
      Parameters: $xmlrequest-> user input in json format,
      Return:true if user info is updated successful otherwise false.
     */

     private function _profileRegistration($jsonRequest) {


	$profileUpdateStatus = FALSE;
	if (DEBUG)
	    writelog('profileRegistration', $jsonRequest, FALSE);
	$uid = mysql_real_escape_string($jsonRequest['profileRegistration']['user_id']);
	$venueName = isset($jsonRequest['profileRegistration']['venue_name']) ? mysql_real_escape_string($jsonRequest['profileRegistration']['venue_name']):NULL;
	$fname = isset($jsonRequest['profileRegistration']['fname'])?mysql_real_escape_string($jsonRequest['profileRegistration']['fname']):NULL;
	$lname = isset($jsonRequest['profileRegistration']['lname'])?mysql_real_escape_string($jsonRequest['profileRegistration']['lname']):NULL;
	$security_que = mysql_real_escape_string($jsonRequest['profileRegistration']['security_que']);
	$security_ans = mysql_real_escape_string($jsonRequest['profileRegistration']['security_ans']);
	$saddress = mysql_real_escape_string($jsonRequest['profileRegistration']['saddress']);
	$country = mysql_real_escape_string($jsonRequest['profileRegistration']['country']);
	$state = mysql_real_escape_string($jsonRequest['profileRegistration']['state']);
	$city = mysql_real_escape_string(trim($jsonRequest['profileRegistration']['city']));
	$zip = mysql_real_escape_string(trim($jsonRequest['profileRegistration']['zip']));
	$isVenue = mysql_real_escape_string(trim($jsonRequest['profileRegistration']['is_venue']));
	$latitude = mysql_real_escape_string(trim($jsonRequest['profileRegistration']['latitude']));
	$longitude = mysql_real_escape_string(trim($jsonRequest['profileRegistration']['longitude']));
	
	/* check for user existance in members */
	$chkUserExistance = execute_query("select * from members where mem_id = '$uid'", true, "select");

	if (!empty($chkUserExistance) && is_array($chkUserExistance)) {
	    if ($isVenue == 'Y')
			$profileUpdate = "UPDATE members SET zip = '$zip',profilenam = '$venueName',saddress='$saddress',country = '$country',state = '$state',city = '$city',secquestion = '$security_que',secanswer = '$security_ans',steps_completed='y',latitude='$latitude' WHERE mem_id='$uid'";
	    else
			$profileUpdate = "UPDATE members SET zip = '$zip',profilenam = '$profileName',saddress='$saddress',country = '$country',state = '$state',city = '$city',secquestion = '$security_que',secanswer = '$security_ans',steps_completed='y',longitude='$longitude' WHERE mem_id='$uid'";
	    $profileUpdateResult = execute_query($profileUpdate, TRUE, "update");
	    $profileUpdateStatus = TRUE;
	}

	if (DEBUG)
	    writelog("entourage:profileRegistration:", $profileUpdateStatus, TRUE);
	return $profileUpdateStatus;
    }
    
    
   public function userProfileUpdate($responseMessage, $jsonRequest){
	
	global $return_codes;
	$userinfo = array();
	$userinfo = self::_profileRegistration($jsonRequest);
	
	if ($userinfo == TRUE) {
	    $response_mess = '
               {
   ' . response_repeat_string() . '
    "profileRegistration":{
           "errorCode":"' . $return_codes["profileRegistration"]["SuccessCode"] . '",
           "errorMsg":"' . $return_codes["profileRegistration"]["SuccessDesc"] . '"
   }
	  }';
	} else {

	    $response_mess = '
                {
   ' . response_repeat_string() . '
   "profileRegistration":{
      "errorCode":"' . $return_codes["profileRegistration"]["errorCode"] . '",
      "errorMsg":"' . $return_codes["profileRegistration"]["errorDesc"] . '"

   }
	  }';
	}
	return getValidJSON($response_mess);
	
    }
	function profile_upload_photo($xmlrequest) {

	if (DEBUG)
	    writelog("profile.class.php :: photo_upload_valid() : ", "Start Here ", false);

	$error = array();
	$error = photo_upload_valid($xmlrequest);
	
	if (DEBUG) {
	    writelog("profile.class.php :: photo_upload_valid() : ", $error, true);
	    writelog("profile.class.php :: photo_upload_valid() : ", "End Here ", false);
	}
	return $error;
    }
	
    function entourage_photo_upload($xmlrequest){
	
	writelog("entourage.class.php :: entourage_photo_upload() : ", "Start Here ", false);

	$error = array();
	$error = photo_upload($xmlrequest);
	
	writelog("entourage.class.php :: entourage_photo_upload() : ", $error, true);
	writelog("entourage.class.php :: entourage_photo_upload() : ", "End Here ", false);
	
	return $error;
	
    }
}

?>
