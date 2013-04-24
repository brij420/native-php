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
  File-name : facebook_register.class.php
  Directory Path  : $/MySNL/Deliverables/Code/MySNL_WebServiceV2/hotpress.class.php/
  Author    : Brijesh Kumar
  Date    : 12/08/2011
  Modified By   : N/A
  Date : N/A

  Include Files : none
  CSS File(s)   : none

  Functions Used
  Javascript   :  none
  PHP     :  facebook_registration,valid_fields,fb_verify_user,userSignUp,fbVerifyUser

  DataBase Table(s)  : members

  Global Variable(s)  : LOCAL_FOLDER: Path where all the images save.
  PROFILE_IMAGE_SITEURL:website url

  Description:to registered from facebook if user has an account on facebook.

  Reviwed By  :
  Reviwed Date:
 * ************************************************************************************* */

/*  class FacebookConnect
  Purpose:to registered from facebook if user has an account on facebook.

 * Returns : None
 */

class FacebookConnect {

    var $profile_url = PROFILE_IMAGE_SITEURL;
    var $local_folder = LOCAL_FOLDER;
    /* Function:facebook_registration($xmlrequest)
     * Description: to registered on MySNL if user has an facebook account.
     * Parameters: $xmlrequest=>request by user.
      Return: Boolean Array
     */

    function facebook_registration($xmlrequest) {

	if (DEBUG)
	    writelog('UserSignUp', $xmlrequest, false);
	$userId = mysql_real_escape_string($xmlrequest['UserSignUp']['emailId']);
	$firstName = mysql_real_escape_string($xmlrequest['UserSignUp']['firstName']);
	$lastName = mysql_real_escape_string($xmlrequest['UserSignUp']['lastName']);
	$address = mysql_real_escape_string($xmlrequest['UserSignUp']['address']);
	$password = mysql_real_escape_string($xmlrequest['UserSignUp']['password']);
	$birthday = mysql_real_escape_string($xmlrequest['UserSignUp']['birthday']);
	$accttype = mysql_real_escape_string(trim($xmlrequest['UserSignUp']['accttype']));
	$accountNo = mysql_real_escape_string($xmlrequest['UserSignUp']['fb_id']);
	$gender = mysql_real_escape_string($xmlrequest['UserSignUp']['gender']);
	$userName = mysql_real_escape_string($xmlrequest['UserSignUp']['userName']);
	$country = mysql_real_escape_string($xmlrequest['UserSignUp']['Country']);
	$state = mysql_real_escape_string($xmlrequest['UserSignUp']['State']);
	$city = mysql_real_escape_string($xmlrequest['UserSignUp']['City']);
	$venueLoc = mysql_real_escape_string($xmlrequest['UserSignUp']['isVenueLocationPlottedOnMap']);
	$inv_code = mysql_real_escape_string($xmlrequest['UserSignUp']['InvitationCode']);
	$secquestion = mysql_real_escape_string($xmlrequest['UserSignUp']['Security Question']);
	$secanswer = mysql_real_escape_string($xmlrequest['UserSignUp']['Your Answer']);
	$latitude = isset($xmlrequest['UserSignUp']['Latitude']) && ($xmlrequest['UserSignUp']['Latitude']) ? mysql_real_escape_string($xmlrequest['UserSignUp']['Latitude']) : NULL;
	$longitude = isset($xmlrequest['UserSignUp']['Longitude']) && ($xmlrequest['UserSignUp']['Longitude']) ? mysql_real_escape_string($xmlrequest['UserSignUp']['Longitude']) : NULL;
	$token = isset($xmlrequest['UserSignUp']['device_token']) && ($xmlrequest['UserSignUp']['device_token']) ? mysql_real_escape_string($xmlrequest['UserSignUp']['device_token']) : NULL;
	$fb_profile_image = mysql_real_escape_string($xmlrequest['UserSignUp']['fb_profile_image']);
//$fb_profile_image =follow_redirect($fb_profile_image);

	if (($fb_profile_image == 'https://fbcdn-profile-a.akamaihd.net/static-ak/rsrc.php/v1/yh/r/C5yt7Cqf3zU.jpg') || ($fb_profile_image == 'https://fbcdn-profile-a.akamaihd.net/static-ak/rsrc.php/v1/yV/r/Xc3RyXFFu-2.jpg'))
	    $fb_profile_image = 'no';
	$firstName = isset($firstName) && ($firstName) ? $firstName : NULL;
	$lastName = isset($lastName) && ($lastName) ? $lastName : NULL;
	$password = md5($password);
	if ($accttype == trim('Fan')) {
	    $accttype = 'N';
	}
	if ($accttype == trim('Talent')) {
	    $accttype = 'H';
	}
	if ($accttype == trim('Industry ')) {
	    $accttype = 'T';
	}
	if ($accttype == trim('Nightsite/Venue')) {
	    $accttype = 'C';
	}

	if (($gender == 'male') || ($gender == 'Male')) {
	    $gender = 'm';
	}
	if (($gender == 'female') || ($gender == 'Female')) {
	    $gender = 'f';
	}
	$profilebg_repeat = 'y'; //y/n
	$feedbackScore = '0';
	$positiveScore = '0';
	$negativeScore = '0';
	$neutralScore = '0';
	$subscribe = '';
	$rating = '';
	$quote = '';
	$widget = '';
	$spam_list = '';
	$bgpic = '';
	$video = '';
	$audio = '';
	$interests = '';
	$ad_notes = '';
	$tribes = '';
	$ignore_list = '';
	$photo_bb_thumb = '';
	$error = array();
	$birthday = strtotime($birthday);
	//if ($inv_code == 'MySNL12842') {
	$fAddress='';
	if ($venueLoc == 'no' && ($accttype == 'C')) {
	   if (isset($address) && ($address)) {
		$address1 = str_replace(" ", "%20", $address);
		$fAddress.=$address1 . ',';
	    }
	   if (isset($city) && ($city)) {
		$city1 = str_replace(" ", "%20", $city);
		$fAddress.=$city1 . ',';
	    }
	   if (isset($state) && ($state)) {
		$state1 = str_replace(" ", "%20", $state);
		$fAddress.=$state1 . ',';
	    }
	   if (isset($country) && ($country)) {
		$country1 = str_replace(" ", "%20", $country);
		$fAddress.=$country1;
	    }

	    $homepage = file_get_contents("http://maps.google.com/maps/geo?q=$fAddress&output=json");
	    $decode = json_decode($homepage, TRUE);

	    $longitude = $decode['Placemark'][0]['Point']['coordinates'][0];
	    $latitude = $decode['Placemark'][0]['Point']['coordinates'][1];
	}
	    $query = "INSERT INTO members(snid,fname,lname,email,password,birthday,profile_type,gender,photo,photo_thumb,photo_b_thumb,profilenam,country,verified,is_facebook_user,accountNo,profilebg_repeat,feedbackScore,positiveScore,negativeScore,neutralScore,subscribe,rating,quote,widget,spam_list,bgpic,video,audio,saddress,state,city,interests,ad_notes,tribes,ignore_list,secquestion,secanswer,photo_bb_thumb,latitude,longitude)VALUE('MySNL12842','$firstName','$lastName','$userId','$password','$birthday','$accttype','$gender','$fb_profile_image','$fb_profile_image','$fb_profile_image','$userName','$country','y','y','$accountNo','$profilebg_repeat','$feedbackScore','$positiveScore','$negativeScore','$neutralScore','$subscribe','$rating','$quote','$widget','$spam_list','$bgpic','$video','$audio','$address','$state','$city','$interests','$ad_notes','$tribes','$ignore_list','$secquestion','$secanswer','$photo_bb_thumb','$latitude','$longitude')";

	    if (DEBUG)
		writelog('UserSignUp', $query, false);
	    $now = time();
	    $sqls = "insert into validate (email,password,val_key) values ('$userId','$password','$now')";
	    if (DEBUG)
		writelog('UserSignUp', $sqls, false);
	    $result_registration = execute_query($query, false, "insert");
	    if (isset($result_registration['last_id']) && ($result_registration['last_id'])) {
		$mem_id = $result_registration['last_id'];
//get user information
		$get_new_user_info = execute_query("select profilenam,photo_b_thumb,profile_type,email,photo_thumb FROM members WHERE mem_id='$mem_id'", false, "select");
		$now = time();
		$now = $now . $userName;
		$now = md5($now);
//send email
		//$get_user_email_id = execute_query("select profilenam,email,photo_thumb from members where mem_id='$mem_id'", false, "select");

		$body1 = "Please confirm your email by clicking the link below. After confirmation of the email you will be able to login to the site and start using the site and Your account number is:$accountNo <a href='http://www.socialnightlife.com/development/index.php?pg=register&s=val&key=$now' target='_blank'>Verify Mail</a>";
		$matter = email_template($get_new_user_info['profilenam'], "Confirm Your Email ", $body1, $mem_id, $get_new_user_info['photo_thumb']);
		firemail($get_new_user_info['email'], "From: noreply@socialnightlife.com\r\n", "Confirm Your Email ", $matter);

//catch log-in time
		$get_log_in_time = "INSERT INTO user_push_notification(id,mem_id,log_in_time,log_out_time,showonline) VALUES ('','$mem_id','" . time() . "','','y')";
		$exe_get_log_in_time = execute_query($get_log_in_time, true, "insert");

		//for push notification
		$get_token_id = "select token_id from iphone_push_notfn as ipn , members as mem WHERE ipn.mem_id='$mem_id' and ipn.mem_id=mem.mem_id and ipn.token_id='$token'";
		$exe_get_token_id = execute_query($get_token_id, true, "select");

		if (empty($exe_get_token_id)) {
		    $query_push_ntfn = "INSERT INTO iphone_push_notfn(id,mem_id,token_id,date) VALUES ('','$mem_id','$token','" . time() . "')";
		    $exe_push_ntfn = execute_query($query_push_ntfn, true, "insert");
		}
	    }
	    $result_sqls = execute_query($sqls, false, "insert");
	    $user_id = isset($result_registration['last_id']) && ($result_registration['last_id']) ? $result_registration['last_id'] : NULL;
	    $last_id = $result_sqls['last_id'];
	    $affected_row = isset($result_registration['count']) && ($result_registration['count']) ? $result_registration['count'] : NULL;
	    $error = error_CRUD($xmlrequest, $affected_row);

	    $error['last_id'] = isset($result_registration['last_id']) && ($result_registration['last_id']) ? $result_registration['last_id'] : NULL;
	    $error['new_mem_profile_name'] = isset($get_new_user_info['profilenam']) && ($get_new_user_info['profilenam']) ? $get_new_user_info['profilenam'] : NULL;
	    $error['new_mem_image'] = isset($get_new_user_info['photo_b_thumb']) && ($get_new_user_info['photo_b_thumb']) ? $get_new_user_info['photo_b_thumb'] : NULL;
	    $error['new_mem_profile_type'] = isset($get_new_user_info['profile_type']) && ($get_new_user_info['profile_type']) ? $get_new_user_info['profile_type'] : NULL;

	    if ((isset($error['UserSignUp']['successful_fin'])) && (!$error['UserSignUp']['successful_fin'])) {
		return $error;
	    }

	    if ($user_id) {
		$auto_friend1 = execute_query("insert into network(mem_id,frd_id)values('$user_id','51')", false, 'insert');
		$auto_friend2 = execute_query("insert into network(mem_id,frd_id)values('$user_id','48')", false, 'insert');
		$auto_friend3 = execute_query("insert into network (mem_id,frd_id)values('$user_id','318')", false, 'insert');
		$auto_friend1 = execute_query("insert into network (mem_id,frd_id)values('51','" . $user_id . "')", false, 'insert');
		$auto_friend2 = execute_query("insert into network(mem_id,frd_id)values('48','" . $user_id . "')", false, 'insert');
		$auto_friend3 = execute_query("insert into network(mem_id,frd_id)values('318','" . $user_id . "')", false, 'insert');
	    }
	    // $error['UserSignUp']['successful_fin'] = isset($result_registration['last_id']) && ($result_registration['last_id']) ? true : false;

	    if (DEBUG)
		writelog("FacebookConnect:facebook_registration:", $error, true);
	    return $error;
	//}
	// else {
	    // $error['UserSignUp']['successful_fin'] = "InvalidIntCode";
	    // return ($error);
	// }
    }

    /* Function:valid_fields($xmlrequest)
     * Description:to validate user.
     * Parameters: $xmlrequest=>request by user.
      Return: Boolean Array
     */

    function valid_fields($xmlrequest) {
        if (DEBUG)
            writelog("FacebookConnect:valid_fields:", 'Start', false);
        if (isset($xmlrequest['FBVerifyUser']['emailId']) && ($xmlrequest['FBVerifyUser']['emailId'])) {
            $userId = mysql_real_escape_string($xmlrequest['FBVerifyUser']['emailId']);
        }
        if (isset($xmlrequest['UserSignUp']['emailId'])) {
            $userId = mysql_real_escape_string($xmlrequest['UserSignUp']['emailId']);
        }
        $error = array();
        $query = "SELECT COUNT(*) FROM members WHERE email='$userId'";
        $result = execute_query($query, false, "select");

        if (isset($xmlrequest['FBVerifyUser']['emailId']) && ($xmlrequest['FBVerifyUser']['emailId'])) {
            $error['successful'] = isset($result['COUNT(*)']) && ($result['COUNT(*)']) ? true : false;
        }
        if (isset($xmlrequest['UserSignUp']['emailId'])) {
            $error['successful'] = isset($result['COUNT(*)']) && (!$result['COUNT(*)']) ? true : false;
        }

        if (DEBUG)
            writelog("FacebookConnect:valid_fields:", $error, true);
        return $error;
    }

    /* Function:fb_verify_user($xmlrequest)
     * Description:to varify that user already registered with given facebook account or not.
     * Parameters: $xmlrequest=>request sent by user.
      Return: Boolean Array
     */

    function fb_verify_user($xmlrequest) {
        $emailId = mysql_real_escape_string($xmlrequest['FBVerifyUser']['emailId']);
        $latitude = isset($xmlrequest['FBVerifyUser']['latitude']) && ($xmlrequest['FBVerifyUser']['latitude']) ? mysql_real_escape_string($xmlrequest['FBVerifyUser']['latitude']) : NULL;
        $longitude = isset($xmlrequest['FBVerifyUser']['longitude']) && ($xmlrequest['FBVerifyUser']['longitude']) ? mysql_real_escape_string($xmlrequest['FBVerifyUser']['longitude']) : NULL;
        $token = isset($xmlrequest['FBVerifyUser']['device_token']) && ($xmlrequest['FBVerifyUser']['device_token']) ? mysql_real_escape_string($xmlrequest['FBVerifyUser']['device_token']) : NULL;
        $query = "SELECT gender,profilenam,mem_id,profile_type,photo_thumb FROM members WHERE email='$emailId'";
        if (DEBUG)
            writelog("facebook_register.class.php :: fb_verify_user() : query : ", $query, false);
        $result = execute_query($query, false);
        if (isset($result['mem_id']) && ($result['mem_id'])) {
            $error = array();
            $id = $result['mem_id'];
            $query_lat_long = "UPDATE members SET latitude='$latitude',longitude='$longitude' WHERE mem_id='$id' AND (profile_type !='C' OR profile_type !='c')";
            $result_lat_long = execute_query($query_lat_long, false, "update");
            $result_lat_long['count'] = isset($result_lat_long['count']) && ($result_lat_long['count'] >= 0) ? true : NULL;
            $error = error_CRUD($xmlrequest, $result_lat_long['count']);

/* for total no of friend request */
	    $query_friends_req = "SELECT COUNT(*) FROM members,messages_system WHERE members.mem_id=messages_system.frm_id AND(messages_system.mem_id='$id') AND(messages_system.type='friend')";
	    $result_friends_req = execute_query($query_friends_req, false, "select");
	    $result['friends_req'] = isset($result_friends_req['COUNT(*)']) && ($result_friends_req['COUNT(*)']) ? $result_friends_req['COUNT(*)'] : NULL;
/* for total no of messages */
	    $query_msg_count = "SELECT COUNT(*) from messages_system as msg where msg.mem_id='$id' and msg.folder='inbox' and msg.type='message' AND msg.read<>'read'";
	    $result_msg_count = execute_query($query_msg_count, false, "select");
	    $result['msg'] = isset($result_msg_count['COUNT(*)']) && ($result_msg_count['COUNT(*)']) ? $result_msg_count['COUNT(*)'] : NULL;
/* for total no of appearances */
//	    $result_appearance_read_msg = execute_query("SELECT COUNT(a.read) AS appearanceCount FROM (SELECT MAX(announce_arrival.id) AS id FROM announce_arrival WHERE user_id IN(SELECT DISTINCT n2.mem_id AS frnd FROM network n1,network n2 WHERE n1.frd_id = n2.mem_id AND n1.mem_id = '$id' AND n2.frd_id = '$id') AND announce_arrival.user_id != '$id' GROUP BY announce_arrival.user_id ORDER BY id DESC) AS t JOIN announce_arrival AS a ON t.id = a.id WHERE a.read='0' AND a.user_id != '$id'", false, "select");
//	    $result['appearance_read_msg'] = isset($result_appearance_read_msg['appearanceCount']) && ($result_appearance_read_msg['appearanceCount']) ? ($result_appearance_read_msg['appearanceCount']) : NULL;

/* for total no of appearances */

	    $appList = execute_query("SELECT a.* FROM (SELECT MAX(announce_arrival.id) AS id FROM announce_arrival WHERE user_id IN(SELECT DISTINCT n2.mem_id AS frnd FROM network n1,network n2 WHERE n1.frd_id = n2.mem_id AND n1.mem_id = '$id' AND n2.frd_id = '$id') AND announce_arrival.user_id != '$id' GROUP BY announce_arrival.user_id ORDER BY id DESC) AS t JOIN announce_arrival AS a ON t.id = a.id WHERE a.user_id != '$id'", TRUE, "select");
	    $appReadCount=0;

	    foreach ($appList as $kk => $appListInfo) {
			if (!empty($appListInfo['user_id']) && is_array($appListInfo)) {
/* start of user read page */
		    $getAppearanceInfo = execute_query("select user_update_read from app_user_update where app_id='" . $appListInfo['id'] . "'", FALSE, "select");
		    $getAppUsersUpdate = $getAppearanceInfo['user_update_read'];
				if (isset($getAppUsersUpdate)) {
				$explode = explode(",", $getAppUsersUpdate);
				$arrCount = count($explode);
				unset($explode[$arrCount - 1]);
				$searchExistanceOfUserId = array_search($id, $explode);
					if ($searchExistanceOfUserId === FALSE) {
						$appReadCount++;
					}
				}
			}
	    }
		/*$result_appearance_read_msg = execute_query("SELECT a.id
FROM (SELECT MAX(announce_arrival.id) AS id FROM announce_arrival WHERE user_id IN (SELECT DISTINCT n2.mem_id AS frnd FROM network n1,network n2 WHERE n1.frd_id=n2.mem_id AND n1.mem_id='$mem_id' AND n2.frd_id='$mem_id') AND announce_arrival.user_id !='$mem_id' GROUP BY announce_arrival.user_id ORDER BY id DESC)
AS t JOIN announce_arrival AS a ON t.id=a.id", true, "select");
   $read = 0;
   foreach ($result_appearance_read_msg AS $kk => $appRead) {
		if (is_array($appRead) && !empty($appRead)) {
		    $getAppearanceInfo = execute_query("select user_update_read from app_user_update where app_id='" . $appRead['id'] . "'", FALSE, "select");
		    
		    if (!empty($getAppearanceInfo)) {
		    if (!empty($getAppearanceInfo['user_update_read'])) {
			$getAppUsersUpdate = $getAppearanceInfo['user_update_read'];
			$explode = explode(",", $getAppUsersUpdate);
			$arrCount = count($explode);
			unset($explode[$arrCount - 1]);
			$searchExistanceOfUserId = array_search($mem_id, $explode);
			
			if (!is_int($searchExistanceOfUserId)) {
			    $read++;
			}
		    } else {
			$read++;
		    }
		}else{$read++;}
		}
	    }*/
	    $result['appearance_read_msg']=$appReadCount;
/* for total no of appearances END */	    

	    
	    //catch log-in time
            $get_log_in_time = "INSERT INTO user_push_notification(id,mem_id,log_in_time,log_out_time,showonline) VALUES ('','$mem_id','" . time() . "','','y')";
            $exe_get_log_in_time = execute_query($get_log_in_time, true, "insert");

            //for push notification
            $get_token_id = "select token_id from iphone_push_notfn as ipn , members as mem WHERE ipn.mem_id='$mem_id' and ipn.mem_id=mem.mem_id and ipn.token_id='$token'";
            $exe_get_token_id = execute_query($get_token_id, true, "select");

            if (empty($exe_get_token_id)) {
                $query_push_ntfn = "INSERT INTO iphone_push_notfn(id,mem_id,token_id,date) VALUES ('','$mem_id','$token','" . time() . "')";
                $exe_push_ntfn = execute_query($query_push_ntfn, true, "insert");
            }
            if ((isset($error['FBVerifyUser']['successful_fin'])) && (!$error['FBVerifyUser']['successful_fin'])) {
                return $error;
            }
        }
        if (DEBUG)
            writelog("facebook_register.class.php :: fb_verify_user() : ", "End Here ", false);
        return $result;
    }

    /* ---------------------To get Response string---------------------------------------------- */
    /* Function:userSignUp($response_message, $xmlrequest)
     * Description: to get the response string while user registration.
     * Parameters: $xmlrequest=>request by user.
      Return: String.
     */

    function userSignUp($response_message, $xmlrequest) {

	if (isset($response_message['UserSignUp']['SuccessCode']) && ( $response_message['UserSignUp']['SuccessCode'] == '000')) {
	    $userinfo = array();
	    $userinfo = $this->facebook_registration($xmlrequest);
	    

	    $last_id = isset($userinfo['last_id']) && ($userinfo['last_id']) ? $userinfo['last_id'] : NULL;
	    if (DEBUG)
		writelog("Response:userSignUp():", $userinfo, true);

	    if ((isset($userinfo['UserSignUp']['successful_fin'])) && (!$userinfo['UserSignUp']['successful_fin'])) {
		$obj_error = new Error();
		$response_message = $obj_error->error_type("UserSignUp", $userinfo);

		$userinfocode = $response_message['UserSignUp']['ErrorCode'];
		$userinfodesc = $response_message['UserSignUp']['ErrorDesc'];
		$response_mess = $response_mess = get_response_string("UserSignUp", $userinfocode, $userinfodesc);
		return $response_mess;
	    } else if ($userinfo['UserSignUp']['successful_fin'] == 'InvalidIntCode' && (is_string($userinfo['UserSignUp']['successful_fin']))) {
		$obj_error = new Error();
		$response_message = $obj_error->error_type("UserSignUp", $userinfo);

		$userinfocode = $response_message['UserSignUp']['ErrorCode'];
		$userinfodesc = $response_message['UserSignUp']['ErrorDesc'];
		$response_mess = $response_mess = get_response_string("UserSignUp", $userinfocode, $userinfodesc);
		return $response_mess;
	    }

	    $userinfocode = $response_message['UserSignUp']['SuccessCode'];
	    $userinfodesc = $response_message['UserSignUp']['SuccessDesc'];
	    $response_str = response_repeat_string();
	    $response_mess = '
	    {
	       ' . $response_str . '
	       "UserSignUp":{
		   "newMemberId":"' . $last_id . '",
		   "newMemberProfileName":"' . $userinfo['new_mem_profile_name'] . '",
		   "newMemberProfileImage":"' . $userinfo['new_mem_image'] . '",
		   "newMemberProfileType":"' . $userinfo['new_mem_profile_type'] . '",
		   "errorCode":"' . $userinfocode . '",
		   "errorMsg":"' . $userinfodesc . '"
	       }
}
';
	} else {
	    $userinfocode = $response_message['UserSignUp']['ErrorCode'];
	    $userinfodesc = $response_message['UserSignUp']['ErrorDesc'];
	    $response_mess = get_response_string("UserSignUp", $userinfocode, $userinfodesc);
	}
	if (DEBUG)
	    writelog("Response:userSignUp():", $response_mess, false);
	return $response_mess;
    }

    /* Function:fbVerifyUser($response_message, $xmlrequest)
     * Description: to verify whther user has already have account with given FB-Id.
     * Parameters: $xmlrequest=>request by user,
     *             $response_message=>to boolean Array after validation.
      Return: String.
     */

    function fbVerifyUser($response_message, $xmlrequest) {
        if (isset($response_message['FBVerifyUser']['SuccessCode']) && ( $response_message['FBVerifyUser']['SuccessCode'] == '000')) {
            $userinfo = array();

            $userinfo = $this->fb_verify_user($xmlrequest);

            if ((isset($userinfo['FBVerifyUser']['successful_fin'])) && (!$userinfo['FBVerifyUser']['successful_fin'])) {
                $obj_error = new Error();
                $response_message = $obj_error->error_type("FBVerifyUser", $userinfo);

                $userinfocode = $response_message['FBVerifyUser']['ErrorCode'];
                $userinfodesc = $response_message['FBVerifyUser']['ErrorDesc'];
                $response_mess = $response_mess = get_response_string("FBVerifyUser", $userinfocode, $userinfodesc);
                return $response_mess;
            }

            $loginId = $userinfo['mem_id'];
            $username = $xmlrequest['FBVerifyUser']['emailId'];
            if (isset($userinfo['mem_id'])) {

                $userinfo_code = $response_message['FBVerifyUser']['SuccessCode'];
                $userinfo_message = $response_message['FBVerifyUser']['SuccessDesc'];
		if(!preg_match('/^(http|https)/i',$userinfo['photo_thumb']))
                $userinfo['photo_thumb'] = ((isset($userinfo['is_facebook_user'])) && (strlen($userinfo['photo_thumb']) > 7) && ($userinfo['is_facebook_user'] == 'y' || $userinfo['is_facebook_user'] == 'Y')) ? $userinfo['photo_thumb'] : ((isset($userinfo['photo_thumb']) && (strlen($userinfo['photo_thumb']) > 7) ) ? $this->profile_url . $userinfo['photo_thumb'] : $this->profile_url . default_images($userinfo['gender'], $userinfo['profile_type']));

                $response_str = response_repeat_string();
                $response_mess = '
	{
  	 ' . $response_str . '
		"FBVerifyUser":{
		   "userId":"' . $userinfo['mem_id'] . '",
                    "userName":"' . $userinfo['profilenam'] . '",
                    "profileImageUrl":"' . $userinfo['photo_thumb'] . '",
                    "profileType":"' . $userinfo['profile_type'] . '",
					"totalMessages":"' . $userinfo['msg'] . '",
                    "totalAppearances":"' . $userinfo['appearance_read_msg'] . '",
                    "totalFriendRequest":"' . $userinfo['friends_req'] . '",
                    "totalAlerts":"' . get_total_alerts($userinfo['mem_id']) . '",
                     "errorCode":"' . $userinfo_code . '",
                     "errorMsg":"' . $userinfo_message . '"
   	}
}';
            }
        } else {
            $userinfocode = $response_message['FBVerifyUser']['ErrorCode'];
            $userinfodesc = $response_message['FBVerifyUser']['ErrorDesc'];
            $response_mess = get_response_string("FBVerifyUser", $userinfocode, $userinfodesc);
        }
        if (DEBUG) {
            writelog("response.class.php :: fbVerifyUser():", $response_mess, false);
            writelog("response.class.php :: fbVerifyUser() : ", "End Here ", false);
        }
        return $response_mess;
    }

     /* Function:FBStates($xmlrequest)
     * Description: to get the locations /states
     * Parameters: $xmlrequest=>request by user,
     *             $response_message=>to boolean Array after validation.
      Return: array.
     */
     private function FBStates($xmlrequest) {
	if (DEBUG)
	    writelog("FacebookConnect.class.php :: FBStates() :: ", "Starts Here ", false);

	$countryCode = trim($xmlrequest['FBStates']['countryCode']);

	$getStateList = "SELECT state_code,state_name FROM  states WHERE country_code = '$countryCode' group by state_name ORDER BY state_name ASC";
	$getStateInfo = execute_query($getStateList, true, "select");
	if (DEBUG) {
	    writelog("FacebookConnect:FBStates:", $getStateInfo, true);
	    writelog("FacebookConnect.class.php :: FBStates() :: ", $getStateList, false);
	}
	if (!empty($getStateInfo) && (is_array($getStateInfo)) && ($getStateInfo['count'] > 0)) {
	    return $getStateInfo;
	} else {
	    return false;
	}
    }

//end of FBStates()

/* Function:fbStateList($response_message,$xmlrequest)
     * Description: to get the locations /states
     * Parameters: $xmlrequest=>request by user,
     *             $response_message=>to boolean Array after validation.
      Return: array.
     */
    public function fbStateList($response_message, $xmlrequest) {

	global $return_codes;
	$states = array();
	$stateList = $this->FBStates($xmlrequest);
	$str = '';
	if (!empty($stateList) && (is_array($stateList)) && ($stateList['count'] > 0)) {
	    for ($i = 0; $i < $stateList['count']; $i++) {
		$getStateCode = isset($stateList[$i]['state_code']) && ($stateList[$i]['state_code']) ? trim($stateList[$i]['state_code']) : NULL;
		$getStateName = isset($stateList[$i]['state_name']) && ($stateList[$i]['state_name']) ? trim(str_replace('"','\"',$stateList[$i]['state_name'])) : NULL;

		$str_temp = '{
		    "stateCode":"' . $getStateCode . '",
		    "stateName":"' . $getStateName . '"
		}';
		$str = $str . $str_temp;
		$str = $str . ',';
	    }
	    $str = rtrim($str, ',');
	    $response_str = response_repeat_string();
	    $response_mess = '
	    {
           ' . $response_str . '
           "FBStates":{
              "errorCode":"' . $return_codes["FBStates"]["SuccessCode"] . '",
              "errorMsg":"' . $return_codes["FBStates"]["SuccessDesc"] . '",
              "stateCount":"' . $stateList['count'] . '",              
              "stateList":[' . $str . ']
           }
        }';
	} else {
	    $response_mess = '
                {
   ' . response_repeat_string() . '
         "FBStates":{
          "errorCode":"' . $return_codes["FBStates"]["NoRecordErrorCode"] . '",
          "errorMsg":"' . $return_codes["FBStates"]["NoRecordErrorDesc"] . '"
            }
       }';
	}
	return $response_mess;
    }

//end of fbLocationStateList()

     /* Function:fbLocations($response_message, $xmlrequest)
     * Description: to get the locations /states
     * Parameters: $xmlrequest=>request by user,
     *             $response_message=>to boolean Array after validation.
      Return: array.
     */
     private function FBCities($xmlrequest) {
	if (DEBUG)
	    writelog("FacebookConnect.class.php :: FBCities() :: ", "Starts Here ", false);

	$countryCode = isset($xmlrequest['FBCities']['countryCode']) && ($xmlrequest['FBCities']['countryCode']) ? mysql_real_escape_string(trim($xmlrequest['FBCities']['countryCode'])) : NULL;
	$stateCode = isset($xmlrequest['FBCities']['stateCode']) && ($xmlrequest['FBCities']['stateCode']) ? mysql_real_escape_string(trim($xmlrequest['FBCities']['stateCode'])) : NULL;

	$getCityList = "SELECT city_name FROM  weblocations WHERE country_code = '$countryCode' AND state_code = '$stateCode' group by city_name ORDER BY city_name ASC";
	$getCityInfo = execute_query($getCityList, true, "select");
	if (DEBUG) {
	    writelog("FacebookConnect:FBCities:", $getCityInfo, true);
	    writelog("FacebookConnect.class.php :: FBCities() :: ", $getCityList, false);
	}
	if (!empty($getCityInfo) && (is_array($getCityInfo)) && ($getCityInfo['count'] > 0)) {
	    return $getCityInfo;
	} else {
	    return false;
	}
    }

//end of fbLocations()

/* Function:FBCityList($response_message, $xmlrequest)
     * Description: to get the locations /states
     * Parameters: $xmlrequest=>request by user,
     *             $response_message=>to boolean Array after validation.
      Return: array.
     */
    public function FBCityList($response_message, $xmlrequest) {

	global $return_codes;
	$city = array();
	$cityList = $this->FBCities($xmlrequest);
	$str = '';
	if (!empty($cityList) && (is_array($cityList)) && ($cityList['count'] > 0)) {
	    for ($i = 0; $i < $cityList['count']; $i++) {

		$getCityName = isset($cityList[$i]['city_name']) && ($cityList[$i]['city_name']) ? trim(str_replace('"','\"',$cityList[$i]['city_name'])) : NULL;

		$str_temp = '{
		    "cityName":"' . $getCityName . '"
		}';
		$str = $str . $str_temp;
		$str = $str . ',';
	    }
	    $str = rtrim($str, ',');
	    $response_str = response_repeat_string();
	    $response_mess = '
	    {
           ' . $response_str . '
           "FBCities":{
              "errorCode":"' . $return_codes["FBCities"]["SuccessCode"] . '",
              "errorMsg":"' . $return_codes["FBCities"]["SuccessDesc"] . '",
              "cityCount":"' . $cityList['count'] . '",
              "cityList":[' . $str . ']
           }
        }';
	} else {
	    $response_mess = '
                {
   ' . response_repeat_string() . '
         "FBCities":{
          "errorCode":"' . $return_codes["FBCities"]["NoRecordErrorCode"] . '",
          "errorMsg":"' . $return_codes["FBCities"]["NoRecordErrorDesc"] . '"
            }
       }';
	}
	return $response_mess;
    }

//end of fbLocationStateList()
public function fbSearchUser($jsonRequest){
		// print_r($jsonRequest);
	if (DEBUG)
	    writelog("facebook_register.class.php :: fbSearchUsers() :: ", "Starts Here ", false);

	$userId = mysql_real_escape_string(trim($jsonRequest['fbSearchUsers']['userId']));
	$fbName = mysql_real_escape_string(trim($jsonRequest['fbSearchUsers']['fbName']));

/* query for searching users */	
	mysql_query('SET character_set_results=utf8');
			mysql_query('SET names=utf8');
			mysql_query('SET character_set_client=utf8');
			mysql_query('SET character_set_connection=utf8');
			mysql_query('SET character_set_results=utf8');
			mysql_query('SET collation_connection=utf8_general_ci');
 
	$searchFacebookUser = "SELECT * FROM facebook_friends WHERE mem_id='$userId' and fb_user_name REGEXP '^[^a-z]+' UNION ALL ";
	$searchFacebookUser .= "select * from (SELECT * FROM facebook_friends WHERE fb_user_name LIKE '%$fbName%' AND mem_id='$userId' ORDER BY fb_user_name ASC) as t";
	$searchFacebookUserResult = execute_query($searchFacebookUser,true,"select");
	
	/* STARTED BY SATENDER  */
	$result=array();
	$kk=0;
	if(!empty($searchFacebookUserResult)){
	foreach ($searchFacebookUserResult as $users) {
	if(!empty($users) && is_array($users)){
	$getAllusers="select mem_id,fb_id as facebook_id, 'invite' as type,invitation_date from contact_list where mem_id='".$userId."' and status=0 and fb_id='".$users["fb_id"]."' ";
	    //$getAllusers = "select mem_id,facebook_id from members where facebook_id='{$users['fb_id']}' AND mem_id !='$userId'";
		$getAllusers .=" UNION ALL  select mem_id,facebook_id, 'member' as type,'' as invitation_date from members where is_facebook_user='y' and facebook_id not in(0,'') and facebook_id='".$users["fb_id"]."'";
	    $getAllusersList1 = execute_query($getAllusers, true, "select");

if(!empty($getAllusersList1)){
		foreach($getAllusersList1 as $getAllusersList){//print_r($getAllusersList);
		if(!empty($getAllusersList) && is_array($getAllusersList)){
		if($getAllusersList['type']=='invite'){

// For Facebook friend request already sent or not  START 
		$chkFBInvitation = "select COUNT(*) as cnt from contact_list where mem_id='$userId' and status=0 and fb_id='{$users['fb_id']}' ";
	    $chkFBInvitationResult = execute_query($chkFBInvitation,false,"select");
// For Facebook friend request already sent or not END 

		//$result[$kk]['mem_id'] = $getAllusersList['mem_id'];
		$result[$kk]['fb_id'] = mysql_real_escape_string($users['fb_id']);
		$result[$kk]['fb_user_name']= mysql_real_escape_string($users['fb_user_name']);
		$result[$kk]['fb_img_url']= $users['fb_img_url'];
		$result[$kk]['user_exists'] = 'N';
		//$result[$kk]['fb_frnd_request_sent'] = 'Y';
		$result[$kk]['already_friend'] ='N';
		$result[$kk]['fb_invitation_sent'] ='Y';
		 // For Facebook friend request already sent or not  START 
		$chkFBInvitationDate = "select invitation_date from contact_list where mem_id='$userId' and status=0 and fb_id='{$users['fb_id']}' ";
		$chkFBInvitationDateResult = execute_query($chkFBInvitationDate,false,"select");
		if(isset($chkFBInvitationDateResult['invitation_date']) && dateDiff($chkFBInvitationDateResult['invitation_date'])>7)
		{
			$result[$kk]['fb_frnd_request_sent'] = 'N';
		}else{
			$result[$kk]['fb_frnd_request_sent'] = 'Y';
		}
	     }else{
		$result[$kk]['fb_id'] = mysql_real_escape_string($users['fb_id']);
		$result[$kk]['fb_user_name']= mysql_real_escape_string($users['fb_user_name']);
		$result[$kk]['fb_img_url']= $users['fb_img_url'];
		$chkInvitation = "SELECT COUNT(*) as cnt FROM messages_system WHERE mem_id = '{$getAllusersList['mem_id']}' AND frm_id='$userId' AND messages_system.type='friend'";
	    $chkInvitationResult = execute_query($chkInvitation,false,"select");
//For friend request already sent or not END 

			$result[$kk]['mem_id'] = $getAllusersList['mem_id'];
			$result[$kk]['user_exists'] = 'Y';
			$result[$kk]['frnd_request_sent'] = $chkInvitationResult['cnt'] > 0?'Y':'N';
			
// For user is friend or not START 
		$chkAlreadyFriend = "SELECT COUNT(*) FROM network WHERE (mem_id='{$getAllusersList['mem_id']}' AND frd_id='$userId') AND (mem_id='$userId' AND frd_id='{$getAllusersList['mem_id']}')";
		$chkAlreadyFriendResult = execute_query($chkAlreadyFriend,false,"select");
// For user is friend or not END 	
			$result[$kk]['already_friend'] = isset($chkAlreadyFriendResult['COUNT(*)']) && $chkAlreadyFriendResult['COUNT(*)'] > 0?'Y':'N';
// For Facebook friend request already sent or not  START 
		$chkFBInvitation = "select COUNT(*) from contact_list where mem_id='$userId' and status=0 and fb_id='{$users['fb_id']}' ";
	    $chkFBInvitationResult = execute_query($chkFBInvitation,false,"select");
// For Facebook friend request already sent or not END 		
		$result[$kk]['fb_invitation_sent'] =isset($chkFBInvitationResult['COUNT(*)']) && $chkFBInvitationResult['COUNT(*)'] > 0?'Y':'N';
// For Facebook friend request already sent or not  START 
		$chkFBInvitationDate = "select invitation_date from contact_list where mem_id='$userId' and status=0 and fb_id='{$users['fb_id']}' ";
		$chkFBInvitationDateResult = execute_query($chkFBInvitationDate,false,"select");
		if(isset($chkFBInvitationDateResult['invitation_date']) && dateDiff($chkFBInvitationDateResult['invitation_date'])>7)
		{
			$result[$kk]['fb_frnd_request_sent'] = 'N';
		}else{
			$result[$kk]['fb_frnd_request_sent'] = 'Y';
		}		
		} 
		}
		}
		}else {
		if(isset($users['mem_id']) && $users['mem_id']!='')
		{
		$result[$kk]['mem_id'] = $users['mem_id'];
		$result[$kk]['fb_id'] = mysql_real_escape_string($users['fb_id']);
		$result[$kk]['fb_user_name']= mysql_real_escape_string($users['fb_user_name']);
		$result[$kk]['fb_img_url']= $users['fb_img_url'];
		$result[$kk]['fb_invitation_sent'] ='N';
		$result[$kk]['user_exists'] = 'N';
		$result[$kk]['frnd_request_sent'] = 'N';
		$result[$kk]['already_friend'] = 'N';
		$result[$kk]['fb_frnd_request_sent']='N';
		
		}
	    }
		$kk++;
		}
	}
	$result['count']=$kk;

	//echo '<pre>';print_r($result);echo '</pre>';exit;
	
	if (DEBUG)
	    writelog("FacebookConnect:facebook_registration:", $searchFacebookUserResult, true);
	
	//if(!empty($searchFacebookUserResult) && is_array($searchFacebookUserResult)){
	if(!empty($result) && is_array($result)){
	
		return $result;
	}else{
		return false;
	}
	}else{
		return false;
	}
	}
 
 /* Function:fbVerification($jsonResponse)
     * Description: to verify whther user has already have account with given FB-Id.
     * Parameters: $jsonResponse=>request by user
	   Return: String.
     */
	 
	private function fbVerification($jsonResponse) {

    if (DEBUG)
	writelog("facebook_register.class.php :: fbUserVerification() :: ", "Starts Here ", false);
	$result=array();
    $userId = mysql_real_escape_string(trim($jsonResponse['FbUserVerification']['userId']));
    $memFBId = mysql_real_escape_string(trim($jsonResponse['FbUserVerification']['userFBId']));
    $memFBEmail = mysql_real_escape_string(trim($jsonResponse['FbUserVerification']['userFBEmailId']));

    /* $lowerlimit = isset($pageNumber) ? (($pageNumber - 1) * $limit) : 0; */
/* for fb verification START */
	 $chkFacebookUser = "SELECT * FROM facebook_friends WHERE mem_id='$userId' LIMIT 0,1";
     $chkFBUsersResponse = execute_query($chkFacebookUser,false,"select");
	 if(empty($chkFBUsersResponse)){
    foreach ($jsonResponse['FbUserVerification']['FbFriends'] AS $kk => $users) {
	if (!empty($users) && is_array($users)) {
	    $userName = mysql_real_escape_string(addslashes(trim($users['fbUserName'])));
	    $getAllusers = "select mem_id,facebook_id,'member' as type from members where facebook_id='{$users['facebook_id']}' AND mem_id !='$userId'";
	    $getAllusers .=" UNION ALL  select mem_id,fb_id as facebook_id,'invite' as type from contact_list where mem_id='$userId' and status=0 and fb_id='{$users['facebook_id']}' ";
	    $getAllusersList1 = execute_query($getAllusers, true, "select");

	    if (!empty($getAllusersList1)) {
		foreach ($getAllusersList1 as $getAllusersList) {
		    if (!empty($getAllusersList) && is_array($getAllusersList)) {
			if ($getAllusersList['type'] == 'invite') {

/* For Facebook friend request already sent or not  START */
			    $chkFBInvitation = "select COUNT(*) as cnt from contact_list where mem_id='$userId' and status=0 and fb_id='{$users['facebook_id']}' ";
			    $chkFBInvitationResult = execute_query($chkFBInvitation, false, "select");
/* For Facebook friend request already sent or not END */

			    $result['fbUsers'][$kk]['facebook_id'] = mysql_real_escape_string($users['facebook_id']);
			    $result['fbUsers'][$kk]['user_exists'] = 'N';
			    $result['fbUsers'][$kk]['already_friend'] = 'N';
			    //$result['fbUsers'][$kk]['user_id'] = $getAllusersList['mem_id'];
			    //$result['fbUsers'][$kk]['fb_invitation_sent'] ='Y';
			    $result['fbUsers'][$kk]['frnd_request_sent'] = 'N';
			    $result['fbUsers'][$kk]['fb_frnd_request_sent'] = $chkFBInvitationResult['cnt'] > 0 ? 'Y' : 'N';
// For Facebook friend request already sent or not  START 
			    if ($result['fbUsers'][$kk]['fb_frnd_request_sent'] == 'Y') {
				$chkFBInvitationDate = "select invitation_date from contact_list where mem_id='$userId' and status=0 and fb_id='{$users['facebook_id']}' ";
				$chkFBInvitationDateResult = execute_query($chkFBInvitationDate, false, "select");
				if (isset($chkFBInvitationDateResult['invitation_date']) && dateDiff($chkFBInvitationDateResult['invitation_date']) > 7) {
				    $result['fbUsers'][$kk]['fb_frnd_request_sent'] = 'N';
				} else {
				    $result['fbUsers'][$kk]['fb_frnd_request_sent'] = 'Y';
				}
			    }
/* For Facebook friend request already sent or not END */
			} else { 
			    $result['fbUsers'][$kk]['facebook_id'] = mysql_real_escape_string($users['facebook_id']);
			    $chkInvitation = "SELECT COUNT(*) as cnt FROM messages_system WHERE mem_id = '{$getAllusersList['mem_id']}' AND frm_id='$userId' AND messages_system.type='friend'";
			    $chkInvitationResult = execute_query($chkInvitation, false, "select");
			    $result['fbUsers'][$kk]['frnd_request_sent'] = $chkInvitationResult['cnt'] > 0 ? 'Y' : 'N';
			    $result['fbUsers'][$kk]['user_id'] = $getAllusersList['mem_id'];
			    $result['fbUsers'][$kk]['user_exists'] = 'Y';
// For user is friend or not START 
			   // $chkAlreadyFriend = "SELECT COUNT(*) FROM network WHERE (mem_id='{$getAllusersList['mem_id']}' AND frd_id='$userId') AND (mem_id='$userId' AND frd_id='{$getAllusersList['mem_id']}')";
			   $chkAlreadyFriend = "SELECT COUNT(*) FROM network n1,network n2 WHERE n1.mem_id='{$getAllusersList['mem_id']}' AND n1.frd_id='$userId' AND n2.mem_id='$userId' AND n2.frd_id='{$getAllusersList['mem_id']}'";
			    $chkAlreadyFriendResult = execute_query($chkAlreadyFriend, false, "select");
// For user is friend or not END 	
			    $result['fbUsers'][$kk]['already_friend'] = isset($chkAlreadyFriendResult['COUNT(*)']) && $chkAlreadyFriendResult['COUNT(*)'] > 0 ? 'Y' : 'N';
// For Facebook friend request already sent or not  START 
			    $chkFBInvitation = "select COUNT(*) from contact_list WHERE  `mem_id` =689 AND `fb_id` LIKE '%{$users['facebook_id']}%'AND `status` =0";
			    $chkFBInvitationResult = execute_query($chkFBInvitation, false, "select");
// For Facebook friend request already sent or not END 		
			    $result['fbUsers'][$kk]['fb_frnd_request_sent'] = isset($chkFBInvitationResult['COUNT(*)']) && $chkFBInvitationResult['COUNT(*)'] > 0 ? 'Y' : 'N';
// For Facebook friend request already sent or not  START 
			    if ($result['fbUsers'][$kk]['fb_frnd_request_sent'] == 'Y') {
				$chkFBInvitationDate = "select invitation_date from contact_list where mem_id='$userId' and status=0 and fb_id='{$users['facebook_id']}' ";
				$chkFBInvitationDateResult = execute_query($chkFBInvitationDate, false, "select");

					if (isset($chkFBInvitationDateResult['invitation_date']) && dateDiff($chkFBInvitationDateResult['invitation_date']) > 7) {
						$result['fbUsers'][$kk]['fb_frnd_request_sent'] = 'N';
					} else {
						$result['fbUsers'][$kk]['fb_frnd_request_sent'] = 'Y';
					}
			      }
			    }
		      }
		   }
	    } else {  
		$result['fbUsers'][$kk]['facebook_id'] = mysql_real_escape_string($users['facebook_id']);
		$result['fbUsers'][$kk]['user_exists'] = 'N';
		$result['fbUsers'][$kk]['user_id'] = '';
		$result['fbUsers'][$kk]['frnd_request_sent'] = 'N';
		$result['fbUsers'][$kk]['already_friend'] = 'N';
		$result['fbUsers'][$kk]['fb_frnd_request_sent'] = 'N';
	    }
	    /* for saving facebook users */
	    $getFacebokUsers = "SELECT * FROM facebook_friends WHERE mem_id='$userId' AND fb_id = '{$users['facebook_id']}'";
	    $facebookUsersEntry = execute_query($getFacebokUsers, false, "select");
	    if (empty($facebookUsersEntry)) {
		$facebook_user_img_url = "https://graph.facebook.com/" . "{$users['facebook_id']}" . "/picture";
		
		mysql_query('SET character_set_results=utf8');
			mysql_query('SET names=utf8');
			mysql_query('SET character_set_client=utf8');
			mysql_query('SET character_set_connection=utf8');
			mysql_query('SET character_set_results=utf8');
			mysql_query('SET collation_connection=utf8_general_ci');
		
		
		$getfbUser = "INSERT INTO facebook_friends(id,mem_id,mem_fb_id,mem_fb_email,fb_id,fb_user_name,fb_img_url) VALUES (DEFAULT,'$userId','$memFBId','$memFBEmail','{$users['facebook_id']}','$userName','$facebook_user_img_url')";
		$getfbUserEntry = execute_query($getfbUser, true, "insert");
	    }
	}
    }
    if (DEBUG) {
	writelog("facebook_register.class.php :: fbVerification()", $result, true);
    }

/* for fb verification END   */
	
	
		if (!empty($result) && is_array($result)) {
		return $result;
		} else {
			return false;
		}
	}else{
		 $chkFacebookUser = "SELECT * FROM facebook_friends WHERE mem_fb_id='$memFBId' AND mem_fb_email='$memFBEmail' LIMIT 0,1";
     $chkFBUsersResponse = execute_query($chkFacebookUser,false,"select");
	 if(!empty($chkFBUsersResponse)){
	 foreach ($jsonResponse['FbUserVerification']['FbFriends'] AS $kk => $users) {
	if (!empty($users) && is_array($users)) {
	    $userName = mysql_real_escape_string(addslashes(trim($users['fbUserName'])));
	    $getAllusers = "select mem_id,facebook_id,'member' as type from members where facebook_id='{$users['facebook_id']}' AND mem_id !='$userId'";
	    $getAllusers .=" UNION ALL  select mem_id,fb_id as facebook_id,'invite' as type from contact_list where mem_id='$userId' and status=0 and fb_id='{$users['facebook_id']}' ";
	    $getAllusersList1 = execute_query($getAllusers, true, "select");

	    if (!empty($getAllusersList1)) {
		foreach ($getAllusersList1 as $getAllusersList) {
		    if (!empty($getAllusersList) && is_array($getAllusersList)) {
			if ($getAllusersList['type'] == 'invite') {

/* For Facebook friend request already sent or not  START */
			    $chkFBInvitation = "select COUNT(*) as cnt from contact_list where mem_id='$userId' and status=0 and fb_id='{$users['facebook_id']}' ";
			    $chkFBInvitationResult = execute_query($chkFBInvitation, false, "select");
/* For Facebook friend request already sent or not END */

			    $result['fbUsers'][$kk]['facebook_id'] = mysql_real_escape_string($users['facebook_id']);
			    $result['fbUsers'][$kk]['user_exists'] = 'N';
			    $result['fbUsers'][$kk]['already_friend'] = 'N';
			    //$result['fbUsers'][$kk]['user_id'] = $getAllusersList['mem_id'];
			    //$result['fbUsers'][$kk]['fb_invitation_sent'] ='Y';
			    $result['fbUsers'][$kk]['frnd_request_sent'] = 'N';
			    $result['fbUsers'][$kk]['fb_frnd_request_sent'] = $chkFBInvitationResult['cnt'] > 0 ? 'Y' : 'N';
// For Facebook friend request already sent or not  START 
			    if ($result['fbUsers'][$kk]['fb_frnd_request_sent'] == 'Y') {
				$chkFBInvitationDate = "select invitation_date from contact_list where mem_id='$userId' and status=0 and fb_id='{$users['facebook_id']}' ";
				$chkFBInvitationDateResult = execute_query($chkFBInvitationDate, false, "select");
				if (isset($chkFBInvitationDateResult['invitation_date']) && dateDiff($chkFBInvitationDateResult['invitation_date']) > 7) {
				    $result['fbUsers'][$kk]['fb_frnd_request_sent'] = 'N';
				} else {
				    $result['fbUsers'][$kk]['fb_frnd_request_sent'] = 'Y';
				}
			    }
/* For Facebook friend request already sent or not END */
			} else { 
			    $result['fbUsers'][$kk]['facebook_id'] = mysql_real_escape_string($users['facebook_id']);
			    $chkInvitation = "SELECT COUNT(*) as cnt FROM messages_system WHERE mem_id = '{$getAllusersList['mem_id']}' AND frm_id='$userId' AND messages_system.type='friend'";
			    $chkInvitationResult = execute_query($chkInvitation, false, "select");
			    $result['fbUsers'][$kk]['frnd_request_sent'] = $chkInvitationResult['cnt'] > 0 ? 'Y' : 'N';
			    $result['fbUsers'][$kk]['user_id'] = $getAllusersList['mem_id'];
			    $result['fbUsers'][$kk]['user_exists'] = 'Y';
// For user is friend or not START 
			   // $chkAlreadyFriend = "SELECT COUNT(*) FROM network WHERE (mem_id='{$getAllusersList['mem_id']}' AND frd_id='$userId') AND (mem_id='$userId' AND frd_id='{$getAllusersList['mem_id']}')";
			   $chkAlreadyFriend = "SELECT COUNT(*) FROM network n1,network n2 WHERE n1.mem_id='{$getAllusersList['mem_id']}' AND n1.frd_id='$userId' AND n2.mem_id='$userId' AND n2.frd_id='{$getAllusersList['mem_id']}'";
			    $chkAlreadyFriendResult = execute_query($chkAlreadyFriend, false, "select");
// For user is friend or not END 	
			    $result['fbUsers'][$kk]['already_friend'] = isset($chkAlreadyFriendResult['COUNT(*)']) && $chkAlreadyFriendResult['COUNT(*)'] > 0 ? 'Y' : 'N';
// For Facebook friend request already sent or not  START 
			    $chkFBInvitation = "select COUNT(*) from contact_list WHERE  `mem_id` =689 AND `fb_id` LIKE '%{$users['facebook_id']}%'AND `status` =0";
			    $chkFBInvitationResult = execute_query($chkFBInvitation, false, "select");
// For Facebook friend request already sent or not END 		
			    $result['fbUsers'][$kk]['fb_frnd_request_sent'] = isset($chkFBInvitationResult['COUNT(*)']) && $chkFBInvitationResult['COUNT(*)'] > 0 ? 'Y' : 'N';
// For Facebook friend request already sent or not  START 
			    if ($result['fbUsers'][$kk]['fb_frnd_request_sent'] == 'Y') {
				$chkFBInvitationDate = "select invitation_date from contact_list where mem_id='$userId' and status=0 and fb_id='{$users['facebook_id']}' ";
				$chkFBInvitationDateResult = execute_query($chkFBInvitationDate, false, "select");

					if (isset($chkFBInvitationDateResult['invitation_date']) && dateDiff($chkFBInvitationDateResult['invitation_date']) > 7) {
						$result['fbUsers'][$kk]['fb_frnd_request_sent'] = 'N';
					} else {
						$result['fbUsers'][$kk]['fb_frnd_request_sent'] = 'Y';
					}
			      }
			    }
		      }
		   }
	    } else {  
		$result['fbUsers'][$kk]['facebook_id'] = mysql_real_escape_string($users['facebook_id']);
		$result['fbUsers'][$kk]['user_exists'] = 'N';
		$result['fbUsers'][$kk]['user_id'] = '';
		$result['fbUsers'][$kk]['frnd_request_sent'] = 'N';
		$result['fbUsers'][$kk]['already_friend'] = 'N';
		$result['fbUsers'][$kk]['fb_frnd_request_sent'] = 'N';
	    }
/* for saving facebook users */
	    $getFacebokUsers = "SELECT * FROM facebook_friends WHERE mem_id='$userId' AND fb_id = '{$users['facebook_id']}'";
	    $facebookUsersEntry = execute_query($getFacebokUsers, false, "select");
	    if (empty($facebookUsersEntry)) {
		$facebook_user_img_url = "https://graph.facebook.com/" . "{$users['facebook_id']}" . "/picture";
		
		mysql_query('SET character_set_results=utf8');
			mysql_query('SET names=utf8');
			mysql_query('SET character_set_client=utf8');
			mysql_query('SET character_set_connection=utf8');
			mysql_query('SET character_set_results=utf8');
			mysql_query('SET collation_connection=utf8_general_ci');
		
		
		$getfbUser = "INSERT INTO facebook_friends(id,mem_id,mem_fb_id,mem_fb_email,fb_id,fb_user_name,fb_img_url) VALUES (DEFAULT,'$userId','$memFBId','$memFBEmail','{$users['facebook_id']}','$userName','$facebook_user_img_url')";
		$getfbUserEntry = execute_query($getfbUser, true, "insert");
	    }
	}
    }
		 return $result;
		 }else{
			$errArray = array();
			$errArray['error'] = 'notAFacebookUser';
			$chkUsersToLogin = "SELECT mem_fb_email FROM facebook_friends WHERE mem_id='$userId' limit 0,1";
			$chkUsersToLoginResponse = execute_query($chkUsersToLogin,false,"select");
			if(!empty($chkUsersToLoginResponse)){
				$errArray['email'] = $chkUsersToLoginResponse['mem_fb_email'];
				return $errArray;
			}else{
				return FALSE;
			}
			}
	}
}
// end of fbVerification()

   public function FbUserVerify($response_message, $jsonRequest) {

	global $return_codes;
	$fbUsers = array();
	$pageNumber = $jsonRequest['FbUserVerification']['pageNumber'];
	$fbUsersList = self::fbVerification($jsonRequest);

	$count = count($fbUsersList['fbUsers']);
	$str = '';
	if(!empty($fbUsersList['error'])){ 
	 $response_mess = '
                {
	' . response_repeat_string() . '
         "FbUserVerification":{
          "errorCode":"' . $return_codes["FbUserVerification"]['error']["NoRecordCode"] . '",
          "errorMsg":"' . $return_codes["FbUserVerification"]['error']["NoRecordDesc"] . '"
		  "emailId":"'.$fbUsersList['email'].'"
		  }
       }';
	}
	 else if (!empty($fbUsersList) && (is_array($fbUsersList)) ) {
//	    $fbUsersList = pagination_array($fbUsersList['fbUsers'], $pageNumber, 20);

	    for ($i = 0; $i < $count; $i++) {
		$getFacebookId = isset($fbUsersList['fbUsers'][$i]['facebook_id']) && ($fbUsersList['fbUsers'][$i]['facebook_id']) ? trim($fbUsersList['fbUsers'][$i]['facebook_id']) : NULL;
		$userExists = isset($fbUsersList['fbUsers'][$i]['user_exists']) && ($fbUsersList['fbUsers'][$i]['user_exists']) ? trim(str_replace('"', '\"', $fbUsersList['fbUsers'][$i]['user_exists'])) : NULL;
		$userId = isset($fbUsersList['fbUsers'][$i]['user_id']) && ($fbUsersList['fbUsers'][$i]['user_id']) ? trim($fbUsersList['fbUsers'][$i]['user_id']) : NULL;
		$frndRequestSent = isset($fbUsersList['fbUsers'][$i]['frnd_request_sent']) && ($fbUsersList['fbUsers'][$i]['frnd_request_sent']) ? trim($fbUsersList['fbUsers'][$i]['frnd_request_sent']) : NULL;
		$fbfrndRequestSent = isset($fbUsersList['fbUsers'][$i]['fb_frnd_request_sent']) && ($fbUsersList['fbUsers'][$i]['fb_frnd_request_sent']) ? trim($fbUsersList['fbUsers'][$i]['fb_frnd_request_sent']) : NULL;
		$alreadyFriend = isset($fbUsersList['fbUsers'][$i]['already_friend']) && ($fbUsersList['fbUsers'][$i]['already_friend']) ? trim($fbUsersList['fbUsers'][$i]['already_friend']) : NULL;
		
		$str_temp = '{
		    "facebook_id":"' . $getFacebookId . '",
		    "user_exists":"' . $userExists . '",
		    "user_id":"' . $userId . '",
		    "invitation_sent":"' . $frndRequestSent . '",
			"fb_invitation_sent":"' . $fbfrndRequestSent . '",
		    "friendship_status":"' . $alreadyFriend . '"
		}';
		$str = $str . $str_temp;
		$str = $str . ',';
	    }
	    $str = rtrim($str, ',');
	    $response_str = response_repeat_string();
	    $response_mess = '
	    {
           ' . $response_str . '
           "FbUserVerification":{
              "errorCode":"' . $return_codes["FbUserVerification"]["SuccessCode"] . '",
              "errorMsg":"' . $return_codes["FbUserVerification"]["SuccessDesc"] . '",
              "FbFriends":[' . $str . ']
           }
        }';
	} else {
	    $response_mess = '
                {
	' . response_repeat_string() . '
         "FbUserVerification":{
          "errorCode":"' . $return_codes["FbUserVerification"]["NoRecordErrorCode"] . '",
          "errorMsg":"' . $return_codes["FbUserVerification"]["NoRecordErrorDesc"] . '"
            }
       }';
	}
	if (DEBUG) {
	    writelog("facebook_register.class.php :: FbUserVerify():", $response_mess, false);
	    writelog("facebook_register.class.php :: FbUserVerify() : ", "End Here ", false);
	}
	return $response_mess;
    }
// end of FbUserVerify()
    
    private function inviteContacts($jsonRequest) {

	if (DEBUG)
	    writelog("facebook_register.class.php :: inviteContacts() :: ", "Starts Here ", false);

	$userId = mysql_real_escape_string(trim($jsonRequest['inviteContacts']['user_id']));
	$fbUser = mysql_real_escape_string(trim($jsonRequest['inviteContacts']['is_facebook_user']));
	$inviteAll = mysql_real_escape_string(trim($jsonRequest['inviteContacts']['invite_all']));

	if (is_array($jsonRequest['inviteContacts']['contacts']) && (!empty($jsonRequest['inviteContacts']['contacts']))) {
	    $contact = TRUE;
	    foreach ($jsonRequest['inviteContacts']['contacts'] AS  $contacts) {

		if (is_array($contacts) && (!empty($contacts))) {
		    if ($inviteAll == 'N') {
			$cond="";
			if($fbUser=='Y')
			{
				$cond=" fb_id='{$contacts['fb_id']}'";
			}else{
				$cond=" friend_email='{$contacts['email_id']}'";
			}
			$getAllPreviousEmails = "SELECT c_id FROM contact_list WHERE $cond AND mem_id='$userId'";
			$getAllPreviousEmailsInfo = execute_query($getAllPreviousEmails, false, "select");
			if (empty($getAllPreviousEmailsInfo['c_id'])) {
			    $fb_user = ($fbUser == 'Y') ? 'Y' : 'N';
			    $fb_ids = ($fbUser == 'Y') ? $contacts['fb_id'] : '';
			    $insertEmail = "insert into contact_list(c_id,mem_id,friend_email,fb_id,invitation_date,status,is_fb_user) values(DEFAULT,'$userId','{$contacts['email_id']}','$fb_ids','" . time() . "',DEFAULT,'$fb_user')";
			    $insertEmailResult = execute_query($insertEmail, true, "insert");
			    $contact = FALSE;
			} else {
				$fb_user = ($fbUser == 'Y') ? 'Y' : 'N';
			    $fb_ids = ($fbUser == 'Y') ? $contacts['fb_id'] : '';
				if($fb_user=='Y')
				{
					$updateEmail="update contact_list set `invitation_date`='".time()."' where  fb_id ='$fb_ids' and mem_id='$userId'";
					$updateEmailResult = execute_query($updateEmail, true, "insert");
				}
			    $contact = "already_added";
			}
		    } else {
			$cond="";
			if($fbUser=='Y')
			{
				$cond=" fb_id='{$contacts['fb_id']}'";
			}else{
				$cond=" friend_email='{$contacts['email_id']}'";
			}
			$getAllPreviousEmails = "SELECT c_id FROM contact_list WHERE $cond AND mem_id='$userId'";
			$getAllPreviousEmailsInfo = execute_query($getAllPreviousEmails, false, "select");
			if (empty($getAllPreviousEmailsInfo['c_id'])) {
			    $fb_user = ($fbUser == 'Y') ? 'Y' : 'N';
			    $fb_ids = ($fbUser == 'Y') ? $contacts['fb_id'] : '';
			    $insertEmail = "insert into invite_device_contacts(c_id,mem_id,friend_email,fb_id,invitation_date,status,is_fb_user) values(DEFAULT,'$userId','{$contacts['email_id']}','$fb_ids','" . time() . "',DEFAULT,'$fb_user')";
			    $insertEmailResult = execute_query($insertEmail, true, "insert");
			    $contact = FALSE;
			} else {
				$fb_user = ($fbUser == 'Y') ? 'Y' : 'N';
			    $fb_ids = ($fbUser == 'Y') ? $contacts['fb_id'] : '';
				if($fb_user=='Y')
				{
					$updateEmail="update contact_list set `invitation_date`='".time()."' where  fb_id ='$fb_ids' and mem_id='$userId'";
					$updateEmailResult = execute_query($updateEmail, true, "insert");
				}
			    $contact = "already_added";
			}
		    }
		}
	    }
	    if ($contact == FALSE)
		return TRUE;
	    else
		return $contact;
	} else {
	    return "no_contacts";
	}
    }

    public function friendsContactInvitation($response_message, $jsonResponse) {

	global $return_codes;
	$inviteContacts = array();
	$inviteContacts = self::inviteContacts($jsonResponse);
	
	$str = '';
	if (!is_string($inviteContacts) && ($inviteContacts == TRUE)) {
	    $response_str = response_repeat_string();
	    $response_mess = '
	    {
           ' . $response_str . '
           "inviteContacts":{
              "errorCode":"' . $return_codes["inviteContacts"]["SuccessCode"] . '",
              "errorMsg":"' . $return_codes["inviteContacts"]["SuccessDesc"] . '"
           }
        }';
	} else {
	    $response_mess = '
                {
	' . response_repeat_string() . '
         "inviteContacts":{
          "errorCode":"' . $return_codes["inviteContacts"][$inviteContacts]["NoRecordErrorCode"] . '",
          "errorMsg":"' . $return_codes["inviteContacts"][$inviteContacts]["NoRecordErrorDesc"] . '"
            }
       }';
	}
	if (DEBUG) {
	    writelog("facebook_register.class.php :: FbUserVerify():", $response_mess, false);
	    writelog("facebook_register.class.php :: FbUserVerify() : ", "End Here ", false);
	}
	return $response_mess;
    }
	
	/* Function:fbSearchUserListing($response_message, $jsonRequest)
     * Description: json response for searching users
     * Parameters: $jsonRequest=>request by user,
     *             $response_message=>to boolean Array after validation.
      Return: json response for searching facebook user.
     */
	 
	public function fbSearchUserListing($response_message, $jsonRequest){
	
	global $return_codes;
	$fbUsers = array();
	$fbUsers = self::fbSearchUser($jsonRequest);
	
	//print_r($fbUsers);
	$str = '';
	if (!empty($fbUsers) && (is_array($fbUsers)) && ($fbUsers['count'] > 0)) {
	    for ($i = 0; $i < $fbUsers['count']; $i++) {
		$memId = isset($fbUsers[$i]['mem_id']) && ($fbUsers[$i]['mem_id']) ? trim($fbUsers[$i]['mem_id']) : NULL;
		$fbID = isset($fbUsers[$i]['fb_id']) && ($fbUsers[$i]['fb_id']) ? trim($fbUsers[$i]['fb_id']) : NULL;
		$fbUserName = isset($fbUsers[$i]['fb_user_name']) && ($fbUsers[$i]['fb_user_name']) ? trim(str_replace('"', '\"', $fbUsers[$i]['fb_user_name'])) : NULL;
		$fbUserImageURL = isset($fbUsers[$i]['fb_img_url']) && ($fbUsers[$i]['fb_img_url']) ? trim($fbUsers[$i]['fb_img_url']) : NULL;
		
		$userExists = isset($fbUsers[$i]['user_exists']) && ($fbUsers[$i]['user_exists']) ? trim(str_replace('"', '\"', $fbUsers[$i]['user_exists'])) : NULL;
		$frndRequestSent = isset($fbUsers[$i]['frnd_request_sent']) && ($fbUsers[$i]['frnd_request_sent']) ? trim($fbUsers[$i]['frnd_request_sent']) : NULL;
		$fbfrndRequestSent = isset($fbUsers[$i]['fb_frnd_request_sent']) && ($fbUsers[$i]['fb_frnd_request_sent']) ? trim($fbUsers[$i]['fb_frnd_request_sent']) : NULL;
		$alreadyFriend = isset($fbUsers[$i]['already_friend']) && ($fbUsers[$i]['already_friend']) ? trim($fbUsers[$i]['already_friend']) : NULL;


		$str_temp = '{
		    "mem_id":"' . $memId . '",
		    "fb_id":"' . $fbID . '",
		    "fb_user_name":"' . $fbUserName . '",
		    "fb_img_url":"' . $fbUserImageURL . '",
			"user_exists":"' . $userExists . '",
		    "invitation_sent":"' . $frndRequestSent . '",
			"fb_invitation_sent":"' . $fbfrndRequestSent . '",
		    "friendship_status":"' . $alreadyFriend . '"
		}';
		$str = $str . $str_temp;
		$str = $str . ',';
	    }
	    $str = rtrim($str, ',');
	    $response_str = response_repeat_string();
	    $response_mess = '
	    {
           ' . $response_str . '
           "fbSearchUsers":{
              "errorCode":"' . $return_codes["fbSearchUsers"]["SuccessCode"] . '",
              "errorMsg":"' . $return_codes["fbSearchUsers"]["SuccessDesc"] . '",
              "userCount":"' . $fbUsers['count'] . '",              
              "userList":[' . $str . ']
           }
        }';
	} else {
	    $response_mess = '
                {
   ' . response_repeat_string() . '
         "fbSearchUsers":{
          "errorCode":"' . $return_codes["fbSearchUsers"]["NoRecordErrorCode"] . '",
          "errorMsg":"' . $return_codes["fbSearchUsers"]["NoRecordErrorDesc"] . '"
            }
       }';
	}
	if (DEBUG)
	    writelog("FacebookConnect:fbSearchUsers:", $response_mess, true);
		
	return $response_mess;
	
	}

}

?>
