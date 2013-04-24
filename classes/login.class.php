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
		File-name : login.class.php
		Directory Path  : $/MySNL/Deliverables/Code/MySNL_WebServiceV2/hotpress.class.php/
		Author    : Brijesh Kumar
		Date    : 12/08/2011
		Modified By   : N/A
		Date : N/A
		
		Include Files : none
		CSS File(s)   : none
		
		Functions Used
		Javascript   :  none
		PHP     :  login_check,login_info,logout,userlogin,userLogout
		
		DataBase Table(s)  : members
		
		Global Variable(s)  : LOCAL_FOLDER: Path where all the images save.
		PROFILE_IMAGE_SITEURL:website url
		
		Description:  These Variables are use to store logical path of website.
		
		Reviwed By  :
		Reviwed Date:
	* ************************************************************************************* */
	
	/*  class Login
		Purpose:User Login-Logout information.
		
		* Returns : None
	*/
	
	class Login {
		
		var $profile_url = PROFILE_IMAGE_SITEURL;
		var $local_folder = LOCAL_FOLDER;
		/* Function:login_check($xmlrequest)
			* Description: to validate user is registered or not.
			* Parameters: $xmlrequest=>request by user.
			Return: Boolean Array
		*/
		
		function login_check($xmlrequest) {
			if (DEBUG)
			writelog("login.class.php :: login_check() : ", "Start Here ", false);
			$error = array();
			$email = mysql_real_escape_string($xmlrequest['UserLogin']['emailId']); //put array variable into isolated php variable explicitly.
			$password = md5($xmlrequest['UserLogin']['password']);
			$query = "SELECT COUNT(*) as totalusers,verified FROM members WHERE email='$email' AND password='$password'"; // AND verified='y'
			if (DEBUG)
			writelog("login.class.php :: login_check() :: query: ", $query, false);
			$row = execute_query($query, false, "select");
			if ($row['totalusers'] == 0) {
				$query = "SELECT is_facebook_user FROM members WHERE email='$email' AND verified='y'";
				$row1 = execute_query($query, false, "select");
				if (!empty($row1) && (($row1['is_facebook_user'] == 'y') OR ($row1['is_facebook_user'] == 'Y'))) {
					$error['facebookLoginError'] = true;
					} else {
					$error['successful'] = false;
				}
				return $error;
			}else if($row['verified'] == 'n')
			{
				$error['verified'] = false;
				return $error;
			}
			$error['successful'] = isset($row['totalusers']) && ($row['totalusers']) ? true : false;
			//$error['verified'] = false;
			
			if (DEBUG)
			writelog("login.class.php :: login_check() : ", "End Here ", false);
			
			return $error;
			/*
				if (DEBUG)
				writelog("login.class.php :: login_check() : ", "Start Here ", false);
				$error = array();
				$email = mysql_real_escape_string($xmlrequest['UserLogin']['emailId']); //put array variable into isolated php variable explicitly.
				$password = md5($xmlrequest['UserLogin']['password']);
				$query = "SELECT steps_completed,verified FROM members WHERE email='$email' AND password='$password' AND (is_facebook_user='0' OR (is_facebook_user IS NULL))";
				if (DEBUG)
				writelog("login.class.php :: login_check() :: query: ", $query, false);
				$row = execute_query($query, false, "select");
				if (!empty($row) && $row['steps_completed'] == 'y' && $row['verified'] == 'y') {
				$error['successful'] = true;
				return $error;
				} else if (!empty($row) && $row['steps_completed'] == 'n' && $row['verified'] == 'y') {
				$error['verification_url'] = PROFILE_IMAGE_SITEURL . 'index.php?pg=invite_mobile&ref=y';
				$error['verified'] = true;
				return $error;
				} else if (!empty($row) && $row['verified'] == 'n') {
				$error['verification_url'] = PROFILE_IMAGE_SITEURL . 'index.php?pg=invite_mobile&ref=y';
				$error['verified'] = true;
				return $error;
				}
				if (empty($row) && is_array($row)) {
				$query = "SELECT is_facebook_user FROM members WHERE email='$email' AND password='$password'";
				$row1 = execute_query($query, false, "select");
				if (!empty($row1) && (($row1['is_facebook_user'] == 'y') OR ($row1['is_facebook_user'] == 'Y'))) {
				$error['facebookLoginError'] = true;
				} else {
				$error['successful'] = false;
				}
				return $error;
				}
				if (DEBUG)
				writelog("login.class.php :: login_check() : ", "End Here ", false);
			*/
		}
		
		/* Function:login_info($xmlrequest)
			* Description: to get Log-in info of an user.
			* Parameters: $xmlrequest=>request by user.
			Return: Array contains the information related to user-Login.
		*/
		
		function login_info($xmlrequest) {
			if (DEBUG)
			writelog("login.class.php :: login_info() : ", "Start Here ", false);
			$row=array();
			$email = mysql_real_escape_string($xmlrequest['UserLogin']['emailId']); //put array variable into isolated php variable explicitly.
			$password = md5($xmlrequest['UserLogin']['password']);
			$longitude = mysql_real_escape_string(isset($xmlrequest['UserLogin']['longitude']) && ($xmlrequest['UserLogin']['longitude']) ? $xmlrequest['UserLogin']['longitude'] : NULL);
			$latitude = mysql_real_escape_string(isset($xmlrequest['UserLogin']['latitude']) && ($xmlrequest['UserLogin']['latitude']) ? $xmlrequest['UserLogin']['latitude'] : NULL);
			$token = isset($xmlrequest['UserLogin']['device_token']) && ($xmlrequest['UserLogin']['device_token']) ? mysql_real_escape_string($xmlrequest['UserLogin']['device_token']) : NULL;
			
			$query = "SELECT showonline,is_facebook_user,profilenam,mem_id,gender,profile_type,photo,photo_b_thumb,photo_thumb,steps_completed,verified,(SELECT id FROM albums WHERE mem_id=members.mem_id and title='hotpress') as id,(SELECT COUNT(*) from messages_system as msg where msg.mem_id=members.mem_id and msg.folder='inbox' and msg.type='message' AND msg.read<>'read') as message_count FROM members WHERE email='$email'AND password='$password'";
			//echo $query;
			if (DEBUG)
			writelog("login.class.php :: login_info() : query : ", $query, false);
			$row = execute_query($query, false, "select");
			/*$query = execute_query("SELECT id FROM albums WHERE mem_id='{$row['mem_id']}' AND type<>'$type' and title='hotpress'",false,"select");*/
			//echo "SELECT id FROM albums WHERE mem_id='{$row['mem_id']}' AND type<>'$type' and title='hotpress'";
			if (!empty($row) && $row['verified'] == 'y') {
				switch ($row['steps_completed']) {
					case 'y':
					$row['is_steps_completed'] = 'Y';
					$row['errorMsg'] = '';
					$row['errorCode'] = '';
					break;
					case 'n':
					$row['is_steps_completed'] = 'N';
					$row['errorMsg'] = 'Please complete 3-step process.';
					$row['errorCode'] = '005';
					$row['albumId'] = isset($row['id'])?$row['id']:NULL;
					//$row['verification_url']=PROFILE_IMAGE_SITEURL . 'index.php?pg=invite_mobile&ref=y';
					break;
				}
				$mem_id = isset($row['mem_id']) && ($row['mem_id']) ? $row['mem_id'] : NULL;
				//for previous log-in delete
				$this->deleteOldLogIns($mem_id, $token);
				//$query_msg_count = "SELECT COUNT(*) from messages_system as msg where msg.mem_id='$mem_id' and msg.folder='inbox' and msg.type='message' AND msg.read<>'read'";
				//echo $query_msg_count;
				$query_friends_req = "SELECT COUNT(*) FROM members,messages_system WHERE members.mem_id=messages_system.frm_id AND(messages_system.mem_id='$mem_id') AND(messages_system.type='friend')";
				
				//total appearances count
				//            $result_appearance_read_msg = execute_query("SELECT COUNT(*) FROM announce_arrival WHERE announce_arrival.read=0", false, "select");
				//07_02)2012            $result_appearance_read_msg = execute_query("select  count(announce_arrival.read) as appearanceCount from announce_arrival where user_id IN (SELECT DISTINCT n2.mem_id as frnd FROM network n1,network n2 WHERE n1.frd_id=n2.mem_id AND n1.mem_id='$mem_id' AND n2.frd_id='$mem_id') and announce_arrival.read='0' AND announce_arrival.user_id !='$mem_id'", false, "select");
				/* for total no of appearances */
				/*$appList = execute_query("SELECT a.* FROM (SELECT MAX(announce_arrival.id) AS id FROM announce_arrival WHERE user_id IN(SELECT DISTINCT n2.mem_id AS frnd FROM network n1,network n2 WHERE n1.frd_id = n2.mem_id AND n1.mem_id = '$mem_id' AND n2.frd_id = '$mem_id') AND announce_arrival.user_id != '$mem_id' GROUP BY announce_arrival.user_id ORDER BY id DESC) AS t JOIN announce_arrival AS a ON t.id = a.id WHERE a.user_id != '$mem_id'", TRUE, "select");*/
				
				
				$appList = execute_query("SELECT MAX(announce_arrival.id) AS id FROM announce_arrival WHERE user_id IN(SELECT DISTINCT n2.mem_id AS frnd FROM network n1,network n2 WHERE n1.frd_id=n2.mem_id AND n1.mem_id='$mem_id' AND n2.frd_id='$mem_id') AND announce_arrival.user_id != '$mem_id' GROUP BY announce_arrival.user_id ORDER BY id DESC", TRUE, "select");
				
				$appReadCount = 0;
				foreach ($appList as $kk => $appListInfo) {
					if (!empty($appListInfo) && is_array($appListInfo)) {
						//print_r($appListInfo);
						/* start of user read page */
						$getAppearanceInfo = execute_query("select user_update_read from app_user_update where app_id='" . $appListInfo['id'] . "'", FALSE, "select");
						$getAppUsersUpdate = $getAppearanceInfo['user_update_read'];
						if (isset($getAppUsersUpdate) && $getAppUsersUpdate != "") {
							$explode = explode(",", $getAppUsersUpdate);
							$arrCount = count($explode);
							unset($explode[$arrCount - 1]);
							//$searchExistanceOfUserId = array_search($mem_id, $explode);
							$searchExistanceOfUserId = in_array($mem_id, $explode);
							//var_dump($searchExistanceOfUserId);
							if ($searchExistanceOfUserId != true) {
								$appReadCount++;
							}
							/* $found=strpos($getAppUsersUpdate,$mem_id.",");
								if ($found !== false) {
								$appReadCount++;
							} */
							}else{
							$appReadCount++;
						}
					}
				}
				
				$result_friends_req = execute_query($query_friends_req, false, "select");
				//$result_msg_count = execute_query($query_msg_count, false, "select");
				//for nightsite.
				$profile_type = isset($row['profile_type']) && ($row['profile_type']) ? $row['profile_type'] : NULL;
				if ($profile_type == 'C' || $profile_type == 'c') {
					$query_online = "UPDATE members SET showonline =0,online='on' WHERE mem_id='$mem_id'";
					} else {
					if (($longitude) && ($latitude)) {
						$query_online = "UPDATE members SET showonline =0,online='on',latitude='$latitude',longitude='$longitude' WHERE mem_id='$mem_id' AND (profile_type !='C' OR profile_type !='c')";
						} else {
						$query_online = "UPDATE members SET showonline =0,online='on' WHERE mem_id='$mem_id'";
					}
				}
			$result_online = execute_query($query_online, false, "update");
			$row['friends_req'] = isset($result_friends_req['COUNT(*)']) && ($result_friends_req['COUNT(*)']) ? $result_friends_req['COUNT(*)'] : NULL;
			$row['msg'] = isset($row['message_count']) && ($row['message_count']) ? $row['message_count'] : NULL;
			$row['showonline'] = isset($result_online['count']) && ($result_online['count']) ? ($result_online['count']) : NULL;
			//07_02_2012            $row['appearance_read_msg'] = isset($result_appearance_read_msg['appearanceCount']) && ($result_appearance_read_msg['appearanceCount']) ? ($result_appearance_read_msg['appearanceCount']) : NULL;
			$row['appearance_read_msg'] = isset($appReadCount) && ($appReadCount) ? ($appReadCount) : 0;
			
			//catch log-in time
			if ($token == '(null)') {
			$token = 0;
			}
			$get_log_in_time = "INSERT INTO user_push_notification(id,mem_id,log_in_time,log_out_time,showonline) VALUES ('','$mem_id','" . time() . "','','y')";
			$exe_get_log_in_time = execute_query($get_log_in_time, false, "insert");
			
			//for push notification
			$get_token_id = "select token_id from iphone_push_notfn as ipn , members as mem WHERE ipn.mem_id='$mem_id' and ipn.mem_id=mem.mem_id and ipn.token_id='$token'";
			$exe_get_token_id = execute_query($get_token_id, true, "select");
			
			if (empty($exe_get_token_id)) {
			$query_push_ntfn = "INSERT INTO iphone_push_notfn(id,mem_id,user_notification_id,token_id,date) VALUES ('','$mem_id','" . $exe_get_log_in_time['last_id'] . "','$token','" . time() . "')";
			$exe_push_ntfn = execute_query($query_push_ntfn, true, "insert");
			}
			if (DEBUG)
			writelog("login.class.php :: login_info() : user info exists ", "true", false);
			
			/* invite feature */
			$row['invite']['inviteSubject'] = "Invitation from {$row['profilenam']} to join SocialNightlife.com";
			$row['invite']['inviteBody'] = "Hello,\\n{$row['profilenam']} is inviting you to SocialNightlife.com \\nCreate your free profile Now. #LINK#JOIN#LINK#\\nSocialNightlife.com is a web-based technology platform providing a 360-degree solution for the nightclub and bar industry.\\nBy combining forward facing social networking features with professional venue and team management tools and going mobile with location based technology, SocialNightlife.com is determined to positively impact how business is conducted, increase foot traffic, improve customer experience and #bold#Elevate the Social Nightlife Experience#bold#\\n\\nView Demo video on Youtube: #youtube# \\n 'Like' us on facebook: #facebook# \\n 'Follow' us on Twitter: #twitter# \\n\\nCheers!\\n\\n{$row['profilenam']}";
			$row['invite']['inviteJoin'] = "http://www.socialnightlife.com/index.php?pg=register&referer=$email&uid=$mem_id";
			$row['invite']['inviteYoutube'] = 'http://Youtube.com/socialnightlife';
			$row['invite']['inviteFacebook'] = 'http://Facebook.com/snightlife';
			$row['invite']['inviteTwitter'] = 'http://Twitter.com/SNFeed';
			}
			/* import contacts */
			$appVersion = mysql_real_escape_string($xmlrequest['GenInfo']['appversion']);
			$deviceToken = mysql_real_escape_string($xmlrequest['UserLogin']['deviceId']);
			//checking for device is registered with that device token
			$getDeviceQuery = "SELECT * FROM import_contacts WHERE ic_user_id='$mem_id' ORDER BY ic_id DESC LIMIT 1";
			$getDeviceResult = execute_query($getDeviceQuery, false, "select");
			
			if (!empty($getDeviceResult) && is_array($getDeviceResult)) {
            if ($getDeviceResult['ic_is_import'] == 'N') {
			$row['is_import'] = 'N';
            } else {
			$row['is_import'] = 'Y';
            }
			} else {
            $row['is_import'] = 'N';
			}
			if (DEBUG) {
			writelog("login.class.php :: login_info():", $row, true);
			writelog("login.class.php :: login_info() : ", "End Here ", false);
			}
			
			return $row;
			}
			
			public function deleteOldLogIns($userId, $token) {
			$get_user_info = execute_query("select * from user_push_notification as upn,iphone_push_notfn as ipn where ipn.token_id='$token' and ipn.user_notification_id=upn.id AND ipn.mem_id='$userId' AND upn.mem_id='$userId'", true, "select");
			foreach ($get_user_info as $kk => $delete_prev_entry) {
			$delete_entry = execute_query("DELETE upn,ipn FROM user_push_notification as upn,iphone_push_notfn as ipn WHERE ipn.token_id='$token' and ipn.user_notification_id=upn.id AND ipn.mem_id='$userId' AND upn.mem_id='$userId'", true, "delete");
			}
			}
			
			/* Function:logout($xmlrequest)
			* Description:to end the session of a user.
			* Parameters: $xmlrequest=>request by user.
			Return: Boolean Array.
			*/
			
			function logout($xmlrequest) {
			if (DEBUG)
            writelog("logout.class.php :: logout() : ", "Start Here ", false);
			$userId = mysql_real_escape_string($xmlrequest['UserLogout']['userId']); //put array variable into isolated php variable explicitly.
			$error = array();
			$query_online = "UPDATE members SET showonline ='0',online='offline' WHERE mem_id='$userId'";
			$result_online = execute_query($query_online, false, "update");
			$showonline = $result_online['count'];
			$error['successful_fin'] = isset($result_online['count']) && ($result_online['count']) ? true : false;
			//push notification logout
			$get_user_id = "select id from user_push_notification where mem_id='$userId' ORDER BY log_in_time DESC limit 0,1";
			$exe_user_id = execute_query($get_user_id, false, "select");
			//catch log-out time
			$get_log_in_time = "UPDATE user_push_notification SET log_out_time='" . time() . "' ,showonline='n' WHERE id='" . $exe_user_id['id'] . "'";
			$exe_get_log_in_time = execute_query($get_log_in_time, true, "insert");
			
			if (DEBUG) {
            writelog("logout.class.php :: logout() : query : ", $query, false);
            writelog("login.class.php :: logout() : ", "End Here ", false);
			}
			return $error;
			}
			
			/* ---------------------To get Response string---------------------------------------------- */
			/* Function:userlogin($xmlrequest)
			* Description:to response string while Login.
			* Parameters: $xmlrequest=>request sent by user,
			*             $response_message=>validation array.
			Return: String.
			*/
			
			function userlogin($response_message, $xmlrequest) {
			global $return_codes;
			if (DEBUG)
			writelog("response.class.php :: login() : ", "Start Here ", false);
			//	if (isset($response_message['UserLogin']['SuccessCode']) && ( $response_message['UserLogin']['SuccessCode'] == '000')) {
			$userinfo = array();
			$userinfo = $this->login_info($xmlrequest);
			
			//$userinfo['showonline'] = isset($userinfo['showonline']) && ($userinfo['showonline']) ? $userinfo['showonline'] : NULL;
			$loginId = $userinfo['mem_id'];
			$username = $xmlrequest['UserLogin']['emailId'];
			if (isset($userinfo['mem_id']) && $userinfo['verified'] == 'y') {
			if (isset($userinfo['errorCode']) && $userinfo['errorCode'] == '005') {
			$userinfo_code = '005';
			$userinfo_message = $userinfo['errorMsg'];
			} else {
			$userinfo_code = $response_message['UserLogin']['SuccessCode'];
			$userinfo_message = $response_message['UserLogin']['SuccessDesc'];
			}
			
			//if (is_readable($this->local_folder . $userinfo['photo_b_thumb']))
			//list($width, $height) = (isset($userinfo['photo_b_thumb']) && (strlen($userinfo['photo_b_thumb']) > 7)) ? getimagesize($this->local_folder . $userinfo['photo_b_thumb']) : NULL;
			$userinfo['photo_b_thumb'] = isset($userinfo['is_facebook_user']) && (strlen($userinfo['photo_b_thumb']) > 7) && ($userinfo['is_facebook_user'] == 'y' || $userinfo['is_facebook_user'] == 'Y') ? $userinfo['photo_b_thumb'] : ((isset($userinfo['photo_b_thumb']) && (strlen($userinfo['photo_b_thumb']) > 7)) ? $this->profile_url . $userinfo['photo_b_thumb'] : $this->profile_url . default_images($userinfo['gender'], $userinfo['profile_type']));
			$userinfo['photo'] = isset($userinfo['is_facebook_user']) && (strlen($userinfo['photo']) > 7) && ($userinfo['is_facebook_user'] == 'y' || $userinfo['is_facebook_user'] == 'Y') ? $userinfo['photo'] : ((isset($userinfo['photo']) && (strlen($userinfo['photo']) > 7)) ? $this->profile_url . $userinfo['photo'] : $this->profile_url . default_images($userinfo['gender'], $userinfo['profile_type']));
			$userinfo['albumId'] = isset($userinfo['albumId']) && ($userinfo['albumId'])?($userinfo['albumId'])  :NULL ;
			$width = NULL;
			$height = NULL;
			$invite = '"invite":{"inviteSubject":"' .str_replace('"', '\"', $userinfo['invite']['inviteSubject']). '","inviteBody":"' . str_replace('"', '\"', $userinfo['invite']['inviteBody']). '","inviteJoin":"' .str_replace('"', '\"', $userinfo['invite']['inviteJoin']). '",
			"inviteYoutube":"' .str_replace('"', '\"', $userinfo['invite']['inviteYoutube']). '","inviteFacebook":"' .str_replace('"', '\"',$userinfo['invite']['inviteFacebook']). '","badgeEarned":"' .str_replace('"', '\"', $userinfo['invite']['inviteTwitter']). '"}';
			$response_str = response_repeat_string();
			$response_mess = '
			{
			' . $response_str . '
			"UserLogin":{
			"userId":"' . $userinfo['mem_id'] . '",
			"showonline":"' .str_replace('"', '\"', $userinfo['showonline']). '",
			"totalMessages":"' .str_replace('"', '\"', $userinfo['msg']). '",
			"totalAppearances":"' .str_replace('"', '\"', $userinfo['appearance_read_msg']). '",
			"totalFriendRequest":"' .str_replace('"', '\"', $userinfo['friends_req']). '",
			"totalAlerts":"'.str_replace('"', '\"', get_total_alerts($loginId)).'",
			"userName":"' .str_replace('"', '\"', $userinfo['profilenam']). '",
			"width":"' .str_replace('"', '\"', $width). '",
			"height":"' .str_replace('"', '\"', $height). '",
			"profileImageUrl":"' .str_replace('"', '\"', $userinfo['photo_b_thumb']). '",
			"profileLargeImageUrl":"' .str_replace('"', '\"', $userinfo['photo']). '",
			"profileType":"' .str_replace('"', '\"', $userinfo['profile_type']). '",
			"albumId":"' .str_replace('"', '\"', $userinfo['albumId']). '",
			"is_import":"' . str_replace('"', '\"', $userinfo['is_import']) . '",
			' . $invite . ',
			"errorCode":"' .str_replace('"', '\"', $userinfo_code). '",
			"errorMsg":"' .str_replace('"', '\"', $userinfo_message). '"
			}
			}';
			
			} else {
			$userinfocode = $return_codes["UserLogin"]["errorCode"];
			$userinfodesc = $return_codes["UserLogin"]["errorDesc"];
			$response_mess = get_response_string("UserLogin", $userinfocode, $userinfodesc);
			}
			if (DEBUG) {
			writelog("response.class.php :: login():", $response_mess, false);
			writelog("response.class.php :: login() : ", "End Here ", false);
			}
			return getValidJSON($response_mess);
			}
			
			
			/* Function:userLogout($xmlrequest)
			* Description: to get response string while Logout.
			* Parameters: $xmlrequest=>request by user,
			*             $response_message=>validation array.
			Return: String.
			*/
			
			function userLogout($response_message, $xmlrequest) {
			global $return_codes;
			if (isset($response_message['UserLogout']['SuccessCode']) && ( $response_message['UserLogout']['SuccessCode'] == '000')) {
            $userinfo = array();
            $userinfo = $this->logout($xmlrequest);
            if ((isset($userinfo['successful_fin'])) && (!$userinfo['successful_fin'])) {
			$obj_error = new Error();
			$response_message = $obj_error->error_type("UserLogout", $userinfo);
			
			$userinfocode = $response_message['UserLogout']['ErrorCode'];
			$userinfodesc = $response_message['UserLogout']['ErrorDesc'];
			$response_mess = $response_mess = get_response_string("UserLogout", $userinfocode, $userinfodesc);
			return getValidJSON($response_mess);
            }
			
            $userinfocode = $response_message['UserLogout']['SuccessCode'];
            $userinfodesc = $response_message['UserLogout']['SuccessDesc'];
            $response_str = response_repeat_string();
			
			
            $response_mess = '
			{
			' . $response_str . '
			"UserLogout":{
			"errorCode":"' . $userinfocode . '",
			"errorMsg":"' .str_replace('"', '\"', $userinfodesc). '"
			}
			}
			';
			} else {
            $userinfocode = $response_message['UserLogout']['ErrorCode'];
            $userinfodesc = $response_message['UserLogout']['ErrorDesc'];
            $response_mess = get_response_string("UserLogout", $userinfocode, $userinfodesc);
			}
			if (DEBUG)
            writelog("Response:userLogout():", $response_mess, false);
			return getValidJSON($response_mess);
			}
			
			}
			function totalAlerts($mem_id) {
			
			if (DEBUG)
			writelog("login.class.php :: totalAlerts() :: ", "Starts Here ", false);
			$alert = array();
			$count=0;
			
			$sql_network = "SELECT mes_id,frm_id,messages_system.date FROM messages_system WHERE TYPE='friend' AND mem_id='$mem_id' AND messages_system.read=''";
			$alert['network'] = execute_query($sql_network, true, "select");
			$count=$alert['network']['count'];
			$query_new_msg = "SELECT mes_id,mem_id,messages_system.date,frm_id FROM messages_system WHERE mem_id='$mem_id' AND folder='inbox' AND TYPE='message' AND messages_system.new='new' ORDER BY messages_system.date DESC";
			$alert['new_msg'] = execute_query($query_new_msg, true, "select");
			$count=$count+$alert['new_msg']['count'];
			/*$query_reply_hotpress = "SELECT t.mem_id,t.from_id,t.id,t.date,t.parentid,m.profilenam,m.gender,m.profile_type FROM bulletin AS t, members AS m WHERE t.from_id='$uid' AND t.msg_alert='Y' AND t.mem_id = m.mem_id AND t.parentid!=0 AND t.from_id != t.mem_id";*/
			$query_reply_hotpress = "select t.mem_id,t.from_id,t.id,t.parentid,m.profilenam from bulletin as t, members as m where t.parentid!=0 AND t.id in (select bulletin_id  from comment_alert_notification where mem_id='$mem_id' and show_alert = 'Y') AND t.mem_id = m.mem_id";
			$alert['reply_hotpress'] = execute_query($query_reply_hotpress, true, "select");
			$count=$count+$alert['reply_hotpress']['count'];
			
			$query_reply_comment = "SELECT t.tst_id,t.added,t.mem_id,t.from_id,t.parent_tst_id,t.added,m.profilenam,m.gender,m.profile_type FROM testimonials AS t, members AS m WHERE t.mem_id='$mem_id' AND t.msg_alert='Y' AND t.from_id = m.mem_id AND t.from_id != t.mem_id";
			$alert['reply_comment'] = execute_query($query_reply_comment, true, "select");
			$count=$count+$alert['reply_comment']['count'];
			$query_tag = "select ms.special,m.profilenam,ms.mem_id ,ms.frm_id,ms.date,ms.mes_id as id,m.gender,m.profile_type FROM messages_system AS ms, members AS m WHERE ms.type = 'tagged' AND ms.frm_id =" . $mem_id . " AND ms.mem_id = m.mem_id";
			$alert['tag'] = execute_query($query_tag, true, "select");
			$count=$count+$alert['tag']['count'];
			
			$query_photo_comments = "SELECT pc.id,pc.mem_id, pc.date, pc.photo_id, pa.photo_small ,pa.album_id FROM photo_comments AS pc, photo_album AS pa WHERE pc.msg_alert ='Y' AND pc.from_id ='$mem_id' AND pc.mem_id !='$mem_id' AND pc.photo_id = pa.photo_id";
			$alert['photo_comments'] = execute_query($query_photo_comments, true, "select");
			$count=$count+$alert['photo_comments']['count'];
			
			$query_event_comments = "SELECT ec.id,ec.even_id,ec.from_id,ec.date,ms.profilenam,ms.photo_b_thumb,ms.profile_type,ms.gender FROM events_comments AS ec,members AS ms WHERE ec.mem_id='$mem_id' AND ec.from_id !='$mem_id' AND parent_id = '0' AND ec.from_id =ms.mem_id AND msg_alert='Y' ORDER BY date";
			$alert['photo_comments'] = execute_query($query_event_comments, true, "select");
			$count=$count+$alert['photo_comments']['count'];
			
			$query_reply_event_comments = "SELECT a.id,a.even_id,a.from_id,a.date,a.parent_id,b.profilenam, b.photo_b_thumb,b.profile_type,b.gender FROM events_comments AS a, members AS b WHERE a.mem_id ='$mem_id' AND a.from_id !='$mem_id' AND parent_id != '0' AND a.from_id = b.mem_id AND msg_alert = 'Y'";
			$alert['reply_event_comments'] = execute_query($query_reply_event_comments, true, "select");
			$count=$count+$alert['reply_event_comments']['count'];
			
			$query_announce_arrival = "SELECT aa.id,aa.user_id,m.profilenam,ms.mem_id ,ms.frm_id,ms.date,ms.mes_id AS id,m.gender,m.profile_type,ms.special,ms.mes_id FROM messages_system AS ms LEFT JOIN announce_arrival AS aa ON (ms.special=aa.id), members AS m WHERE ms.type = 'appearance' AND ms.mem_id ='$mem_id' AND ms.frm_id = m.mem_id AND skip_alert='1' AND ms.new='new' AND aa.user_id IN(SELECT DISTINCT n2.mem_id FROM network n1,network n2 WHERE n1.frd_id=n2.mem_id AND n1.mem_id='$mem_id' AND n2.frd_id='$uid')";
			$alert['announce_arrival'] = execute_query($query_announce_arrival, true, "select");
			$count=$count+$alert['announce_arrival']['count'];
			
			$query_tagged_entourage_list = "SELECT aa.id as announce_id,tel.id,tel.venue_id,tel.date,tel.time,tel.user_id,tel.ent_id,mem.mem_id,mem.profilenam,mem.profile_type,mem.gender,mem.photo_b_thumb FROM tag_ent_list AS tel LEFT JOIN announce_arrival AS aa ON (tel.user_id=aa.user_id AND tel.venue_id=aa.venue_id),members AS mem WHERE tel.ent_id='$mem_id' AND tel.ent_id = mem.mem_id AND tel.msg_alert = 'Y'";
			$alert['tagged_entourage_list'] = execute_query($query_tagged_entourage_list, true, "select");
			$count=$count+$alert['tagged_entourage_list']['count'];
			
			$query_sqlBottle = "SELECT bt.id,bt.mem_id,bt.alert_text,UNIX_TIMESTAMP(bt.createdate) AS DATE,date_alert as Badge_date,bdg.badge_name,bdg.public_hint_active,bdg.badge_id,mem.profilenam,bt.venue_id FROM bottel_alert AS bt,badges AS bdg ,members AS mem WHERE bt.alert_status ='N' AND bt.mem_id = '$mem_id' AND bt.mem_id=mem.mem_id AND (mem.profile_type !='C' OR mem.profile_type !='c') AND bt.bottel_type=bdg.badge_name";
			$alert['badges'] = execute_query($query_sqlBottle, true, "select");
			$count=$count+$alert['badges']['count'];
			
			return $count;
			}
			?>
						