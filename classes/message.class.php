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
  File-name     : message.class.php
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

  DataBase Table(s)   : messages_system members

  Global Variable(s)  : $return_codes
  Constant(s)         : PROFILE_IMAGE_SITEURL , LOCAL_FOLDER

  Description         :  File to display the Latest Notifications for the perticular user from different module.
  Reviwed By  :
  Reviwed Date:
 * ************************************************************************************* */
/*
  class Message
  Purpose : With the help of this class we can see the messages coming from diferent users
  ,our sent messages and trashed messages.We can also send the message to our friends
  and can also reply to inbox messages.

 */

class Message {

    var $profile_url = PROFILE_IMAGE_SITEURL;
    var $local_folder = LOCAL_FOLDER;

	function get_message_list($xmlrequest, $pagenumber, $limit) {

        if (DEBUG)
            writelog("message.class.php :: get_message_list() :: ", "Starts Here ", false);

        $lowerlimit = isset($pagenumber) ? ($pagenumber - 1) * $limit : 0;

        $memId = isset($xmlrequest['Messages']['userId']) && ($xmlrequest['Messages']['userId']) ? mysql_real_escape_string($xmlrequest['Messages']['userId']) : NULL;
        $folder = isset($xmlrequest['Messages']['FolderType']) && ($xmlrequest['Messages']['FolderType']) ? mysql_real_escape_string($xmlrequest['Messages']['FolderType']) : NULL;

        $message = array();
        $query_message = "SELECT SQL_CALC_FOUND_ROWS a.new,a.mes_id,a.mem_id,a.frm_id,a.subject,a.body,a.date,a.read FROM messages_system as a WHERE";
        $query_mem_info = "SELECT b.profilenam,b.photo_b_thumb,b.gender,b.profile_type FROM members as b WHERE";

        if ($folder == 'inbox') {
            $query_message1 = $query_message . " a.mem_id='$memId' and a.folder='$folder' and a.type='message' order by a.mes_id desc limit $lowerlimit,$limit";
            $exeMessage = execute_query($query_message1, true, "select");
            $total_message_records = execute_query("SELECT FOUND_ROWS() as TotalRecords ;", true, "select");
            $tmp = array();
            if (isset($exeMessage['count'])) {
                foreach ($exeMessage as $kk => $mem) {

                    $query_message2 = $query_mem_info . " b.mem_id='" . $mem['frm_id'] . "'";
                    $exeMessage1 = execute_query($query_message2, false, "select");

                    if (empty($exeMessage1)) {
                        $tmp = anonymous();
                        $message[$kk] = array_merge((array) $mem, $tmp);
                    } else {
                        $message[$kk] = array_merge((array) $mem, $exeMessage1);
                    }
                }
            }
        } elseif ($folder == 'sent') {
            $query_message1 = $query_message . " a.frm_id='$memId' and a.type='message' order by a.mes_id desc limit $lowerlimit,$limit";
            $exeMessage = execute_query($query_message1, true, "select");
            $total_message_records = execute_query("SELECT FOUND_ROWS() as TotalRecords ;", true, "select");
            $tmp = array();
            if (isset($exeMessage['count'])) {
                foreach ($exeMessage as $kk => $mem) {

                    $query_message2 = $query_mem_info . " b.mem_id='" . $mem['mem_id'] . "'";
                    $exeMessage1 = execute_query($query_message2, false, "select");

                    if (empty($exeMessage1)) {
                        $tmp = anonymous();
                        $message[$kk] = array_merge((array) $mem, $tmp);
                    } else {
                        $message[$kk] = array_merge((array) $mem, $exeMessage1);
                    }
                }
            }
        } elseif ($folder == 'trashed') {
            $query_message1 = $query_message . " a.mem_id='$memId' AND a.type='message' AND a.folder='" . $folder . "' ORDER BY update_date DESC LIMIT $lowerlimit,$limit";
            $exeMessage = execute_query($query_message1, true, "select");
            $total_message_records = execute_query("SELECT FOUND_ROWS() as TotalRecords ;", true, "select");
            $tmp = array();
            if (isset($exeMessage['count'])) {
                foreach ($exeMessage as $kk => $mem) {

                    $query_message2 = $query_mem_info . " b.mem_id='" . $mem['frm_id'] . "'";
                    $exeMessage1 = execute_query($query_message2, false, "select");

                    if (empty($exeMessage1)) {
                        $tmp = anonymous();
                        $message[$kk] = array_merge((array) $mem, $tmp);
                    } else {
                        $message[$kk] = array_merge((array) $mem, $exeMessage1);
                    }
                }
            }
        }
        $message['count'] = isset($exeMessage['count']) && ($exeMessage['count']) ? $exeMessage['count'] : NULL;
        if ($message['count'] > 0) {
            if (DEBUG)
                writelog("message.class.php :: get_message_list() :: Query to get messages", $query_message1, false);

            $message['Total'] = (isset($total_message_records[0]['TotalRecords'])) ? $total_message_records[0]['TotalRecords'] : 0;
            $message['FolderType'] = isset($folder) && ($folder) ? $folder : NULL;
            if (DEBUG) {
                writelog("Message:get_message_list:", $message, true);
                writelog("message.class.php :: get_message_list() :: ", "End Here ", false);
            }
            return $message;
        } else {
            return array();
        }
    }
    /*  function get_message_list()
      Purpose: To get the latest messages
      Parameters : $xmlrequest : Request array for followed events
      $pageNumber : Current page Number
      $limit      : no of results to be display on each page
      Returns : array returning messages 

    function get_message_list($xmlrequest, $pagenumber, $limit) {

	if (DEBUG)
	    writelog("message.class.php :: get_message_list() :: ", "Starts Here ", false);

	$lowerlimit = isset($pagenumber) ? ($pagenumber - 1) * $limit : 0;

	$memId = isset($xmlrequest['Messages']['userId']) && ($xmlrequest['Messages']['userId']) ? mysql_real_escape_string($xmlrequest['Messages']['userId']) : NULL;
	$folder = isset($xmlrequest['Messages']['FolderType']) && ($xmlrequest['Messages']['FolderType']) ? mysql_real_escape_string($xmlrequest['Messages']['FolderType']) : NULL;

	$message = array();
	$query_message = "SELECT SQL_CALC_FOUND_ROWS ms.mem_id,ms.frm_id,mem.profilenam,ms.subject,ms.body FROM messages_system AS ms,members AS mem WHERE ";
	$query_mem_info = "SELECT b.profilenam,b.photo_b_thumb,b.gender,b.profile_type FROM members as b WHERE";
	$query_all_search_response = "select ";
	if ($folder == 'inbox') {
	    $query_message1 = $query_message . "ms.mes_id = ANY(SELECT MAX(a.mes_id) AS a FROM messages_system AS a WHERE a.mem_id = '$memId' AND a.folder = '$folder' AND a.type = 'message' GROUP BY a.frm_id ORDER BY MAX(a.mes_id)DESC) AND ms.frm_id = mem.mem_id GROUP BY ms.frm_id ORDER BY ms.mes_id DESC";
	    $exeMessage = execute_query($query_message1, true, "select");
	    $total_message_records = execute_query("SELECT FOUND_ROWS() as TotalRecords ;", true, "select");
	    $tmp = array();
	    if (isset($exeMessage['count'])) {
		foreach ($exeMessage as $kk => $mem) {
		    if (is_array($mem) && !empty($mem)) {
			$query_message2 = $query_mem_info . " b.mem_id='" . $mem['frm_id'] . "'";
			$exeMessage1 = execute_query($query_message2, false, "select");

			if (empty($exeMessage1)) {
			    $tmp = anonymous();
			    $message[$kk] = array_merge((array) $mem, $tmp);
			} else {
			    $message[$kk] = array_merge((array) $mem, $exeMessage1);
			}
		    }
		    $i++;
		}
	    }
	} elseif ($folder == 'sent') {
	    $query_message1 = $query_message . "ms.mes_id = ANY(SELECT MAX(a.mes_id) AS a FROM messages_system AS a WHERE a.frm_id = '$memId' AND a.type = 'message' GROUP BY a.frm_id ORDER BY MAX(a.mes_id)DESC) AND ms.frm_id = mem.mem_id GROUP BY ms.frm_id ORDER BY ms.mes_id DESC";
	    $exeMessage = execute_query($query_message1, true, "select");
	    $total_message_records = execute_query("SELECT FOUND_ROWS() as TotalRecords ;", true, "select");
	    $tmp = array();
	    if (isset($exeMessage['count'])) {
		foreach ($exeMessage as $kk => $mem) {

		    $query_message2 = $query_mem_info . " b.mem_id='" . $mem['mem_id'] . "'";
		    $exeMessage1 = execute_query($query_message2, false, "select");

		    if (empty($exeMessage1)) {
			$tmp = anonymous();
			$message[$kk] = array_merge((array) $mem, $tmp);
		    } else {
			$message[$kk] = array_merge((array) $mem, $exeMessage1);
		    }
		}
	    }
	} elseif ($folder == 'trashed') {
	    $query_message1 = $query_message . "ms.mes_id = ANY(SELECT MAX(a.mes_id) AS a FROM messages_system AS a WHERE a.mem_id = '$memId' AND a.folder = '$folder' AND a.type = 'message' GROUP BY a.frm_id ORDER BY MAX(a.mes_id)DESC) AND ms.frm_id = mem.mem_id GROUP BY ms.frm_id ORDER BY ms.mes_id DESC";
	    $exeMessage = execute_query($query_message1, true, "select");
	    $total_message_records = execute_query("SELECT FOUND_ROWS() as TotalRecords ;", true, "select");
	    $tmp = array();
	    if (isset($exeMessage['count'])) {
		foreach ($exeMessage as $kk => $mem) {

		    $query_message2 = $query_mem_info . " b.mem_id='" . $mem['frm_id'] . "'";
		    $exeMessage1 = execute_query($query_message2, false, "select");

		    if (empty($exeMessage1)) {
			$tmp = anonymous();
			$message[$kk] = array_merge((array) $mem, $tmp);
		    } else {
			$message[$kk] = array_merge((array) $mem, $exeMessage1);
		    }
		}
	    }
	}
	$message['count'] = isset($exeMessage['count']) && ($exeMessage['count']) ? $exeMessage['count'] : NULL;
	if ($message['count'] > 0) {
	    if (DEBUG)
		writelog("message.class.php :: get_message_list() :: Query to get messages", $query_message1, false);

	    $message['Total'] = (isset($total_message_records[0]['TotalRecords'])) ? $total_message_records[0]['TotalRecords'] : 0;
	    $message['FolderType'] = isset($folder) && ($folder) ? $folder : NULL;
	    if (DEBUG) {
		writelog("Message:get_message_list:", $message, true);
		writelog("message.class.php :: get_message_list() :: ", "End Here ", false);
	    }

	    return $message;
	} else {
	    return array();
	}
    }

    /*  function allMessageList()
      Purpose: To list down all the messages send by individual user/friend
      Parameters : $xmlrequest : Request array for message detail
      Returns : array for message list
     */

    public function allMessageList($xmlrequest, $pagenumber, $limit) {

	if (DEBUG)
	    writelog("message.class.php :: allMessageList() :: ", "Starts Here ", false);

	$lowerlimit = isset($pagenumber) ? ($pagenumber - 1) * $limit : 0;

	$userId = mysql_real_escape_string($xmlrequest['getAllMessageList']['userId']);
	$fromId = mysql_real_escape_string($xmlrequest['getAllMessageList']['fromId']);
	$FolderType = mysql_real_escape_string($xmlrequest['getAllMessageList']['FolderType']);

	$message_detail = array();
	if ($FolderType == 'inbox') {
	    $query_message = "SELECT SQL_CALC_FOUND_ROWS m.mes_id,m.date,m.mem_id as mes_mem,m.subject,m.body,m.frm_id,me.profilenam,me.photo_b_thumb,me.gender,me.profile_type FROM messages_system as m, members as me WHERE (m.mem_id = '" . $userId . "' OR m.frm_id = '" . $userId . "') AND (m.frm_id = '" . $fromId . "' OR m.mem_id = '" . $fromId . "') AND me.mem_id = m.mem_id order by m.mes_id ASC LIMIT $lowerlimit,$limit";
	} elseif ($FolderType == 'sent') {
	    $query_message = "SELECT SQL_CALC_FOUND_ROWS m.mes_id,m.date,m.mem_id as mes_mem,m.subject,m.body,m.frm_id,me.profilenam,me.photo_b_thumb,me.gender,me.profile_type FROM messages_system as m, members as me WHERE (m.mem_id = '" . $userId . "' OR m.frm_id = '" . $userId . "') AND (m.frm_id = '" . $fromId . "' OR m.mem_id = '" . $fromId . "') AND m.frm_id = me.mem_id order by m.mes_id ASC LIMIT $lowerlimit,$limit";
	} elseif ($FolderType == 'trashed') {
	    $query_message = "SELECT SQL_CALC_FOUND_ROWS m.mes_id,m.date,m.mem_id as mes_mem,m.subject,m.body,m.frm_id,me.profilenam,me.photo_b_thumb,me.gender,me.profile_type FROM messages_system as m, members as me WHERE (m.mem_id = '" . $userId . "' OR m.frm_id = '" . $userId . "') AND (m.frm_id = '" . $fromId . "' OR m.mem_id = '" . $fromId . "') AND m.mem_id = me.mem_id order by m.mes_id ASC LIMIT $lowerlimit,$limit";
	}

	if (DEBUG)
	    writelog("message.class.php :: allMessageList() :: Query to view messages detail", $query_message, false);
	$messageList = execute_query($query_message, true, "select");
	$total_message_list = execute_query("SELECT FOUND_ROWS() as TotalRecords ;", true, "select");
	$messageList['Total'] = (isset($total_message_list[0]['TotalRecords'])) ? $total_message_list[0]['TotalRecords'] : 0;
	return $messageList;
    }

    /*  function view_message_detail()
      Purpose: To get the detail information about perticular message
      Parameters : $xmlrequest : Request array for message detail
      Returns : array for message detail */

    function view_message_detail($xmlrequest) {

	if (DEBUG)
	    writelog("message.class.php :: view_message_detail() :: ", "Starts Here ", false);

	$userId = mysql_real_escape_string($xmlrequest['MessageDetails']['userId']);
	$messageId = mysql_real_escape_string($xmlrequest['MessageDetails']['messageId']);
	$FolderType = mysql_real_escape_string($xmlrequest['MessageDetails']['FolderType']);

	$message_detail = array();
        if ($FolderType == 'inbox') {
            $query_message = "SELECT m.mes_id,m.date,m.mem_id as mes_mem,m.subject,m.body,m.frm_id,me.profilenam,me.photo_b_thumb,me.gender,me.profile_type,me.is_facebook_user FROM messages_system as m, members as me WHERE m.mes_id='" . $messageId . "' AND me.mem_id = m.frm_id";
        } elseif ($FolderType == 'sent') {
            $query_message = "SELECT m.mes_id,m.date,m.mem_id as mes_mem,m.subject,m.body,m.frm_id,me.profilenam,me.photo_b_thumb,me.gender,me.profile_type,me.is_facebook_user FROM messages_system as m, members as me WHERE m.mes_id='" . $messageId . "' AND m.frm_id = me.mem_id";
        } elseif ($FolderType == 'trashed') {
            $query_message = "SELECT m.mes_id,m.date,m.mem_id as mes_mem,m.subject,m.body,m.frm_id,me.profilenam,me.photo_b_thumb,me.gender,me.profile_type,me.is_facebook_user FROM messages_system as m, members as me WHERE m.mes_id='" . $messageId . "' AND m.mem_id = me.mem_id";
        }
	if (DEBUG)
	    writelog("message.class.php :: view_message_detail() :: Query to view messages detail", $query_message, false);
	$message_detail = execute_query($query_message, false);

	if (!empty($message_detail)) {
	    $msg_viewed = execute_query("UPDATE messages_system SET messages_system.read='read', messages_system.new='viewed'  WHERE mes_id='" . $messageId . "'", true, "update");
	    $msg_str = str_replace("\n", "", $message_detail);

	    $arrChunk = explode("wrote:",$msg_str['body']);


	    if ($FolderType == 'inbox') {
			$memuser = getname($message_detail['frm_id']);  //replyed id
			$stringAttached=trim($memuser)." wrote:";
			$zinga = explode($stringAttached, strip_tags($msg_str['body']));
	    } else {
			$memuser = getname($message_detail['mes_mem']);  //user name
			$zinga = explode($memuser, $msg_str['body']);
	    }

//            $zinga1 = explode($memuser, $msg_str['body']);
	    if (strpos($arrChunk[0], $memuser, 0)) {
		$message_detail['main_body'] = $zinga[0];
	    } else {
		$message_detail['main_body'] = $msg_str['body'];
	    }

	    $j = 1;
	    $k = 0;

	    for ($i = 0; $i < count($arrChunk) - 1; $i++) {
		$arrUsr = explode("<br />", $arrChunk[$i]);
		$indxUser = count($arrUsr) - 1;
		$arrMsg = explode($arrUsr[$indxUser], $arrChunk[$j]);
		$msg = strip_tags($arrMsg[0]);
		$msg = str_replace($curuser, "", strip_tags($msg));
		$msg = str_replace($memuser, "", strip_tags($msg));

		$sql = execute_query("SELECT mem_id,photo_b_thumb,profilenam,gender,profile_type,is_facebook_user FROM members WHERE profilenam = '" . trim($arrUsr[$indxUser]) . "'", false, "select");
		if (DEBUG)
		    writelog("message.class.php :: view_message_detail() :: Query to view messages detail", $sql, false);

		$result1['reply_id'] = $sql['mem_id'];
		$result1['reply_photo_b_thumb'] = $sql['photo_b_thumb'];
		$result1['reply_profilenam'] = $sql['profilenam'];
		$result1['msg'] = trim($msg);
		$result1['is_facebook_user'] = $sql['is_facebook_user'];
		$message_detail['reply'][$k] = $result1;
		$k++;
		$j++;
	    }

	    if (DEBUG) {
		writelog("Message:view_message_detail:", $message_detail, true);
		writelog("message.class.php :: view_message_detail() :: ", "End Here ", false);
	    }

//            return $message_detail;
	} else {
	    $message = "SELECT m.mes_id,m.date,m.mem_id as mes_mem,m.subject,m.body,m.frm_id FROM messages_system as m WHERE m.mes_id='" . $messageId . "'";
	    $exeMesg = execute_query($message, false, 'select');
	    $img = "images/Anonymous.jpg";
	    $message_detail['mes_id'] = $messageId;
	    $message_detail['date'] = $exeMesg['date'];
	    $message_detail['mes_mem'] = $exeMesg['mes_mem'];
	    $message_detail['subject'] = $exeMesg['subject'];
	    $message_detail['body'] = '';
	    $message_detail['frm_id'] = $exeMesg['frm_id'];
	    $message_detail['profilenam'] = 'Annonymous';
	    $message_detail['photo_b_thumb'] = $img;
	    $message_detail['gender'] = '';
	    $message_detail['profile_type'] = 'C';
	    $message_detail['main_body'] = $exeMesg['body'];

//            return $message_detail;
	}
       // print_r($message_detail);
	return $message_detail;
    }

    /*  function delete_message()
      Purpose: To delete the message owned by the user
      Parameters : $xmlrequest : Request array for deleting message
      Returns : true for successful deletion and false for unsuccessful deletion */

    function delete_message($xmlrequest) {

	if (DEBUG)
	    writelog("message.class.php :: delete_message() :: ", "Starts Here ", false);

	$userId = mysql_real_escape_string($xmlrequest['DeleteMessage']['userId']);
	$messageId = mysql_real_escape_string($xmlrequest['DeleteMessage']['messageId']);
	$Folder = mysql_real_escape_string($xmlrequest['DeleteMessage']['FolderType']);

	$message = array();
	if ($Folder == 'inbox') {
	    $query_message = "UPDATE  messages_system SET folder ='trashed',update_date =  '" . time() . "' WHERE mes_id ='$messageId'  AND mem_id ='$userId'";
	    $result = execute_query($query_message, true, "update");
	} elseif ($Folder == 'sent') {
	    $query_message = "DELETE FROM messages_system WHERE mes_id='$messageId' ";
	    $result = execute_query($query_message, true, "delete");
	} elseif ($Folder == 'trashed') {
	    $query_message = "DELETE FROM messages_system WHERE mes_id='$messageId' AND mem_id='$userId'";
	    $result = execute_query($query_message, true, "delete");
	}
	if (DEBUG) {
	    writelog("message.class.php :: delete_message() :: Query to delete messages", $query_message, false);
	    writelog("Message:delete_message:", $result, true);
	    writelog("message.class.php :: delete_message() :: ", "End Here ", false);
	}
	if ($result >= 0) {
	    return true;
	} else {
	    return false;
	}
    }

    /*  function reply_message()
      Purpose: To reply the message
      Parameters : $xmlrequest : Request array for message reply
      Returns : true for successful reply and false for unsuccessful reply */

    function reply_message($xmlrequest) {
	if (DEBUG)
	    writelog("message.class.php :: reply_message() :: ", "Starts Here ", false);

	$userId = mysql_real_escape_string($xmlrequest['replyMessage']['userId']);
	$memId = mysql_real_escape_string($xmlrequest['replyMessage']['memId']);
	$body = mysql_real_escape_string($xmlrequest['replyMessage']['body']);
	$messageId = mysql_real_escape_string($xmlrequest['replyMessage']['messageId']);

	$reply = "SELECT subject,body FROM messages_system WHERE mes_id = '" . $messageId . "'";
	if (DEBUG)
	    writelog("message.class.php :: reply_message() :: Query to reply message", $reply, false);

	$reslt = execute_query($reply, false, "select");
	if (!empty($reslt)) {

	    $body_org = $reslt['body'];
	    $subject_org = "Re:" . $reslt['subject'];
	    $body = make_reply($body, $userId);
	    $new_body = strip_tags($body . "\n" . $body_org);
	    $query_message = "INSERT INTO messages_system(mem_id,frm_id,subject,body,messages_system.type,new,folder,date,special,messages_system.read) VALUE('$memId','$userId','" . str_replace('nlbr', '<br />', $subject_org) . "','" . str_replace('nlbr', '<br />', $new_body) . "','message','new','inbox','" . time() . "','','')";

	    if (DEBUG)
		writelog("message.class.php :: reply_message() :: Query to reply message", $query_message, false);
	    $message = execute_query($query_message, true, "insert");
	    if ($userId != $memId)
		//push_notification('reply_message', $userId, $memId); //,$message['last_id']
	    return true;
	} else {
	    return false;
	}
    }

    /*  function get_alert_result()
      Purpose: To send the message
      Parameters : $xmlrequest : Request array for sending message
      Returns : true for successful reply and false for unsuccessful reply */

    function send_message($xmlrequest) {
	if (DEBUG)
	    writelog("message.class.php :: send_message() :: ", "Starts Here ", false);
	$message = array();
	$userId = isset($xmlrequest['sendMessage']['userId']) && ($xmlrequest['sendMessage']['userId']) ? mysql_real_escape_string($xmlrequest['sendMessage']['userId']) : NULL;
	$memId = isset($xmlrequest['sendMessage']['memId']) && ($xmlrequest['sendMessage']['memId']) ? mysql_real_escape_string($xmlrequest['sendMessage']['memId']) : NULL;
	$body = isset($xmlrequest['sendMessage']['body']) && ($xmlrequest['sendMessage']['body']) ? mysql_real_escape_string(str_replace('nlbr', '<br />', $xmlrequest['sendMessage']['body'])) : NULL;
	$time = isset($xmlrequest['sendMessage']['time']) && ($xmlrequest['sendMessage']['time']) ? mysql_real_escape_string($xmlrequest['sendMessage']['time']) : NULL;
	$subject = mysql_real_escape_string($xmlrequest['sendMessage']['subject']);
	$memId = explode(',', $memId);

	$userTimezone = new DateTimeZone('America/Chicago');
	$myDateTime = new DateTime("$time");
	$offset = $userTimezone->getOffset($myDateTime);
	$time = $myDateTime->format('U') + $offset;
	foreach ($memId as $kk => $mem) {

	    $query_message = "INSERT INTO messages_system(mem_id,frm_id,subject,body,messages_system.type,new,folder,date,special,messages_system.read) VALUE('$mem','$userId','$subject','$body','message','new','inbox','" . $time . "','','')";

	    if (DEBUG)
		writelog("message.class.php :: send_message() :: Query to send message", $query_message, false);
	    $message = execute_query($query_message, false, "insert");
	    $last_id = $message['last_id'];

	    $get_user_email_id = execute_query("select profilenam,email,photo_thumb from members where mem_id='$userId'", false, "select");
	    $get_profile_user_email_id = execute_query("select profilenam,email from members where mem_id='$mem'", false, "select");

//send email & push notification
	    if ($userId != $mem) {
		//push_notification('send_messages', $userId, $mem);
		$body1 = getname($userId) . " has sent you a message:<br>Click <a href='http://www.socialnightlife.com/index.php?pg=mailbox&s=view_mes&mes_id=$last_id' target='_blank'>here</a> to read or reply " . '<br>"' . $body . '"';
		$matter = email_template($get_user_email_id['profilenam'], 'You have new message on SocialNightlife.', $body1, $userId, $get_user_email_id['photo_thumb']);
		$result = firemail($get_profile_user_email_id['email'], 'From: socialNightLife <socialnightlife.com>\r\n', 'You have new message on SocialNightlife.', $matter);
	    }
	}
	return true;
    }

    /*  function messageList()
      Purpose: To list the messages the user got
      Parameters : $xmlrequest       : Request array for message list
      $response_message : ?
      Returns : JSON formatted response for listing messages 

    function messageList($response_message, $xmlresponse) {

	global $return_codes;
	$message = array();
	$pagenumber = $xmlresponse['Messages']['pageNumber'];
	$message = $this->get_message_list($xmlresponse, $pagenumber, 10);
	$count = isset($message['count']) && ($message['count']) ? (int) $message['count'] : NULL;

	$str = '';
	$strMessage = '';
	if (!empty($message) && ($count > 0)) {
	    date_default_timezone_set("UTC");
	    if ($message['FolderType'] == 'inbox' || $message['FolderType'] == 'trashed') {
		for ($i = 0; $i < $count; $i++) {
		    if (!preg_match('/^(http|https)/i', $message[$i]['photo_b_thumb']))
			$message[$i]['photo_b_thumb'] = (isset($message[$i]['photo_b_thumb']) && (strlen($message[$i]['photo_b_thumb']) > 7)) ? $this->profile_url . $message[$i]['photo_b_thumb'] : $this->profile_url . default_images1($message[$i]['gender'], $message[$i]['profile_type']);

		    $message[$i]['body'] = str_replace('\\', "", $message[$i]['body']);
		    $message[$i]['body'] = str_replace(array("\r\n", "\r", "\n", "<br />", "<br/>"), "\\n", $message[$i]['body']);
		    $message[$i]['body'] = str_replace(array('>', '/'), '', trim($message[$i]['body']));
		    $message[$i]['body'] = str_replace(array('"'), '\"', trim($message[$i]['body']));

		    $message[$i]['subject'] = str_replace('\\', "", $message[$i]['subject']);
		    $message[$i]['subject'] = str_replace(array("\r\n", "\r", "\n", "<br />", "<br/>"), "\\n", $message[$i]['subject']);
		    $message[$i]['subject'] = str_replace(array('>', '/'), '', trim($message[$i]['subject']));
		    $message[$i]['subject'] = str_replace(array('"'), '\"', trim($message[$i]['subject']));
		    $message[$i]['subject'] = strip_tags($message[$i]['subject']);

		    $memuser = getname($message[$i]['mem_id']);
		    $frmuser = getname($message[$i]['frm_id']);
		    $get_latest_message = strpos($message[$i]['body'], 'wrote:');
		    if ($get_latest_message !== FALSE) {
			$remaining_string = substr($message[$i]['body'], 0, $get_latest_message);
			$message_body = strpos($remaining_string, $frmuser);
			if ($message_body == FALSE) {
			    $message_body = strpos($remaining_string, $memuser);
			}
			$message12 = substr($remaining_string, 0, $message_body);
			$message_content = substr($message12, 0, 75);
		    } else {
			$message_content = substr($message[$i]['body'], 0, 75);
		    }

		    //$read = isset($message[$i]['read']) && ($message[$i]['read']) ? $message[$i]['read'] : NULL;//"viewed":"' . $read . '",
		    $str_temp = '{
            "userId":"' . str_replace('"', '\"',trim($message[$i]['mem_id'])) . '",
            "messageTitle":"' . str_replace('"', '\"',trim(preg_replace('/\s+/', ' ', $message[$i]['subject']))) . '",
            "messageContent":"' . str_replace('"', '\"',trim(preg_replace('/\s+/', ' ', $message_content))) . '",
            "senderUserId":"' . str_replace('"', '\"',trim($message[$i]['frm_id'])) . '",
            "messageDate":"' . str_replace('"', '\"',date('Y-m-d H:i:s', $message[$i]['date'])) . '",
            "senderName":"' . str_replace('"', '\"',trim($message[$i]['profilenam'])) . '",
            "senderImageUrl":"' . str_replace('"', '\"',$message[$i]['photo_b_thumb']) . '",
            "senderGender":"' . str_replace('"', '\"',trim($message[$i]['gender'])) . '"
    }'; //m/d/Y, g:i a

		    $str = $str . $str_temp;
		    $str = $str . ',';
		}
		$str = rtrim($str, ',');
	    } else {
		for ($i = 0; $i < $count; $i++) {

		    $message[$i]['body'] = str_replace('\\', "", $message[$i]['body']);
		    if (!preg_match('/^(http|https)/i', $message[$i]['photo_b_thumb']))
			$message[$i]['photo_b_thumb'] = (isset($message[$i]['photo_b_thumb']) && (strlen($message[$i]['photo_b_thumb']) > 7)) ? $this->profile_url . $message[$i]['photo_b_thumb'] : $this->profile_url . default_images($message[$i]['gender'], $message[$i]['profile_type']);
		    $message[$i]['body'] = str_replace(array("\r\n", "\r", "\n", "<br />", "<br/>"), "\\n", $message[$i]['body']);
		    $message[$i]['body'] = str_replace(array('>', '/'), '', $message[$i]['body']);
		    $message[$i]['body'] = str_replace(array('"'), '\"', trim($message[$i]['body']));
		    $message[$i]['body'] = strip_tags($message[$i]['body']);

		    $message[$i]['subject'] = str_replace('\\', "", $message[$i]['subject']);
		    $message[$i]['subject'] = str_replace(array("\r\n", "\r", "\n", "<br />", "<br/>"), "\\n", $message[$i]['subject']);
		    $message[$i]['subject'] = str_replace(array('>', '/'), '', trim($message[$i]['subject']));
		    $message[$i]['subject'] = str_replace(array('"'), '\"', trim($message[$i]['subject']));
		    $message[$i]['subject'] = strip_tags($message[$i]['subject']);
		    $memuser = getname($message[$i]['mem_id']);
		    $frmuser = getname($message[$i]['frm_id']);
		    $get_latest_message = strpos($message[$i]['body'], " wrote:");

		    if ($get_latest_message !== FALSE) {
			$remaining_string = substr($message[$i]['body'], 0, $get_latest_message);
			$message_body = strpos($remaining_string, $frmuser);
			if ($message_body == FALSE) {
			    $message_body = strpos($remaining_string, $memuser);
			}
			$message12 = substr($remaining_string, 0, $message_body);
			$message_content = substr($message12, 0, 75);
		    } else {
			$message_content = substr($message[$i]['body'], 0, 75);
		    }
		    $read = isset($message[$i]['read']) && ($message[$i]['read']) ? $message[$i]['read'] : NULL;
		    $str_temp = '{
            "messageId":"' . str_replace('"', '\"',trim($message[$i]['mes_id'])) . '",
            "viewed":"' . $read . '",
            "messageTitle":"' . str_replace('"', '\"',trim($message[$i]['subject'])) . '",
            "messageContent":"' . str_replace('"', '\"',trim(preg_replace('/\s+/', ' ', $message_content))) . '",
            "senderUserId":"' . str_replace('"', '\"',trim($message[$i]['mem_id'])) . '",
            "messageDate":"' . str_replace('"', '\"',date('Y-m-d H:i:s', $message[$i]['date'])) . '",
            "senderName":"' . str_replace('"', '\"',trim($message[$i]['profilenam'])) . '",
            "senderImageUrl":"' . str_replace('"', '\"',$message[$i]['photo_b_thumb']) . '",
            "senderGender":"' . str_replace('"', '\"',trim($message[$i]['gender'])) . '"
     }';

		    $str = $str . $str_temp;
		    $str = $str . ',';
		}
		$str = rtrim($str, ',');
	    }
	    $response_str = response_repeat_string();
	    $response_mess = '
        {
           ' . $response_str . '
           "Messages":{
              "errorCode":"' . $return_codes["Messages"]["SuccessCode"] . '",
              "errorMsg":"' . $return_codes["Messages"]["SuccessDesc"] . '",
              "messageCount":"' . $count . '",              
              "TotalmessageCount":"' . $message['Total'] . '",
              "FolderType":"' . $message['FolderType'] . '",
               "MessageList":[' . $str . ']
           }
        }';
	} else {
	    $response_mess = '
                {
   ' . response_repeat_string() . '
         "Messages":{
          "errorCode":"' . $return_codes["Messages"]["FailedToAddRecordCode"] . '",
          "errorMsg":"' . $return_codes["Messages"]["FailedToAddRecordDesc"] . '",
          "MessageList":[' . $str . ']
            }
       }';
	}
	return getValidJSON($response_mess);
    }

    /*  function getAllMessageList()
      Purpose: To list the messages the user got from individual friend/user
      Parameters : $xmlrequest       : Request array for message list
      $response_message : ?
      Returns : JSON formatted response for listing messages */
function messageList($response_message, $xmlresponse) {

        global $return_codes;
        $message = array();
        $pagenumber = $xmlresponse['Messages']['pageNumber'];
        $message = $this->get_message_list($xmlresponse, $pagenumber, 10);
        $count = isset($message['count']) && ($message['count']) ? (int) $message['count'] : NULL;

        $str = '';
        if (!empty($message) && ($count > 0)) {
            date_default_timezone_set("UTC");
            if ($message['FolderType'] == 'inbox' || $message['FolderType'] == 'trashed') {
                for ($i = 0; $i < $count; $i++) {
		    if(!preg_match('/^(http|https)/i',$message[$i]['photo_b_thumb']))
                    $message[$i]['photo_b_thumb'] = (isset($message[$i]['photo_b_thumb']) && (strlen($message[$i]['photo_b_thumb']) > 7)) ? $this->profile_url . $message[$i]['photo_b_thumb'] : $this->profile_url . default_images1($message[$i]['gender'], $message[$i]['profile_type']);
                    $message[$i]['body'] = str_replace('\\', "", $message[$i]['body']);
                    $message[$i]['body'] = str_replace(array("\r\n", "\r", "\n", "<br />", "<br/>"), "\\n", $message[$i]['body']);
                    $message[$i]['body'] = str_replace(array('>', '/'), '', trim($message[$i]['body']));
                    $message[$i]['body'] = str_replace(array('"'), '\"', trim($message[$i]['body']));

                    $message[$i]['subject'] = str_replace('\\', "", $message[$i]['subject']);
                    $message[$i]['subject'] = str_replace(array("\r\n", "\r", "\n", "<br />", "<br/>"), "\\n", $message[$i]['subject']);
                    $message[$i]['subject'] = str_replace(array('>', '/'), '', trim($message[$i]['subject']));
                    $message[$i]['subject'] = str_replace(array('"'), '\"', trim($message[$i]['subject']));
                    $message[$i]['subject'] = strip_tags($message[$i]['subject']);

                    $memuser = getname($message[$i]['mem_id']);
                    $frmuser = getname($message[$i]['frm_id']);
                    $get_latest_message = strpos($message[$i]['body'], 'wrote:');
                    if ($get_latest_message !== FALSE) {
                        $remaining_string = substr($message[$i]['body'], 0, $get_latest_message);
                        $message_body = strpos($remaining_string, $frmuser);
                        if ($message_body == FALSE) {
                            $message_body = strpos($remaining_string, $memuser);
                        }
                        $message12 = substr($remaining_string, 0, $message_body);
                        $message_content = substr($message12, 0, 75);
                    } else {
                        $message_content = substr($message[$i]['body'], 0, 75);
                    }

                    $read = isset($message[$i]['read']) && ($message[$i]['read']) ? $message[$i]['read'] : NULL;
                    $str_temp = '{
            "messageId":"' . trim($message[$i]['mes_id']) . '",
            "viewed":"' . $read . '",
            "messageTitle":"' . trim(preg_replace('/\s+/', ' ', $message[$i]['subject'])) . '",
            "messageContent":"' . trim(preg_replace('/\s+/', ' ', $message_content)) . '",
            "senderUserId":"' . trim($message[$i]['frm_id']) . '",
            "messageDate":"' . date('Y-m-d H:i:s', $message[$i]['date']) . '",
            "senderName":"' . trim($message[$i]['profilenam']) . '",
            "senderImageUrl":"' . $message[$i]['photo_b_thumb'] . '",
            "senderGender":"' . trim($message[$i]['gender']) . '"
    }'; //m/d/Y, g:i a

                    $str = $str . $str_temp;
                    $str = $str . ',';
                }
                $str = rtrim($str, ',');
            } else {
                for ($i = 0; $i < $count; $i++) {

                    $message[$i]['body'] = str_replace('\\', "", $message[$i]['body']);
		    if(!preg_match('/^(http|https)/i',$message[$i]['photo_b_thumb']))
                    $message[$i]['photo_b_thumb'] = (isset($message[$i]['photo_b_thumb']) && (strlen($message[$i]['photo_b_thumb']) > 7)) ? $this->profile_url . $message[$i]['photo_b_thumb'] : $this->profile_url . default_images($message[$i]['gender'], $message[$i]['profile_type']);
                    $message[$i]['body'] = str_replace(array("\r\n", "\r", "\n", "<br />", "<br/>"), "\\n", $message[$i]['body']);
                    $message[$i]['body'] = str_replace(array('>', '/'), '', $message[$i]['body']);
                    $message[$i]['body'] = str_replace(array('"'), '\"', trim($message[$i]['body']));
                    $message[$i]['body'] = strip_tags($message[$i]['body']);

                    $message[$i]['subject'] = str_replace('\\', "", $message[$i]['subject']);
                    $message[$i]['subject'] = str_replace(array("\r\n", "\r", "\n", "<br />", "<br/>"), "\\n", $message[$i]['subject']);
                    $message[$i]['subject'] = str_replace(array('>', '/'), '', trim($message[$i]['subject']));
                    $message[$i]['subject'] = str_replace(array('"'), '\"', trim($message[$i]['subject']));
                    $message[$i]['subject'] = strip_tags($message[$i]['subject']);
                    $memuser = getname($message[$i]['mem_id']);
                    $frmuser = getname($message[$i]['frm_id']);
                    $get_latest_message = strpos($message[$i]['body'], " wrote:");

                    if ($get_latest_message !== FALSE) {
                        $remaining_string = substr($message[$i]['body'], 0, $get_latest_message);
                        $message_body = strpos($remaining_string, $frmuser);
                        if ($message_body == FALSE) {
                            $message_body = strpos($remaining_string, $memuser);
                        }
                        $message12 = substr($remaining_string, 0, $message_body);
                        $message_content = substr($message12, 0, 75);
                    } else {
                        $message_content = substr($message[$i]['body'], 0, 75);
                    }
                    $read = isset($message[$i]['read']) && ($message[$i]['read']) ? $message[$i]['read'] : NULL;
                    $str_temp = '{
            "messageId":"' . trim($message[$i]['mes_id']) . '",
            "viewed":"' . $read . '",
            "messageTitle":"' . trim($message[$i]['subject']) . '",
            "messageContent":"' . trim(preg_replace('/\s+/', ' ', $message_content)) . '",
            "senderUserId":"' . trim($message[$i]['mem_id']) . '",
            "messageDate":"' . date('Y-m-d H:i:s', $message[$i]['date']) . '",
            "senderName":"' . trim($message[$i]['profilenam']) . '",
            "senderImageUrl":"' . $message[$i]['photo_b_thumb'] . '",
            "senderGender":"' . trim($message[$i]['gender']) . '"
     }';

                    $str = $str . $str_temp;
                    $str = $str . ',';
                }
                $str = rtrim($str, ',');
            }
            $response_str = response_repeat_string();
            $response_mess = '
        {
           ' . $response_str . '
           "Messages":{
              "errorCode":"' . $return_codes["Messages"]["SuccessCode"] . '",
              "errorMsg":"' . $return_codes["Messages"]["SuccessDesc"] . '",
              "messageCount":"' . $count . '",              
              "TotalmessageCount":"' . $message['Total'] . '",
              "FolderType":"' . $message['FolderType'] . '",
               "MessageList":[' . $str . ']
           }
        }';
        } else {
            $response_mess = '
                {
   ' . response_repeat_string() . '
         "Messages":{
          "errorCode":"' . $return_codes["Messages"]["FailedToAddRecordCode"] . '",
          "errorMsg":"' . $return_codes["Messages"]["FailedToAddRecordDesc"] . '",
          "MessageList":[' . $str . ']
            }
       }';
        }
        return getValidJSON($response_mess);
    }
    public function getAllMessageList($response_message, $xmlresponse) {

	global $return_codes;
	$message = array();
	$pagenumber = $xmlresponse['getAllMessageList']['pageNumber'];
	$message = $this->allMessageList($xmlresponse, $pagenumber, 10);
	$count = isset($message['count']) && ($message['count']) ? (int) $message['count'] : NULL;
	$folderType = $xmlresponse['getAllMessageList']['FolderType'];
	$str = '';
	$strMessage = '';
	if (!empty($message) && ($count > 0)) {
	    date_default_timezone_set("UTC");
	    for ($i = 0; $i < $count; $i++) {
		if (!preg_match('/^(http|https)/i', $message[$i]['photo_b_thumb']))
		    $message[$i]['photo_b_thumb'] = (isset($message[$i]['photo_b_thumb']) && (strlen($message[$i]['photo_b_thumb']) > 7)) ? $this->profile_url . $message[$i]['photo_b_thumb'] : $this->profile_url . default_images1($message[$i]['gender'], $message[$i]['profile_type']);

		$message[$i]['body'] = str_replace('\\', "", $message[$i]['body']);
		$message[$i]['body'] = str_replace(array("\r\n", "\r", "\n", "<br />", "<br/>"), "\\n", $message[$i]['body']);
		$message[$i]['body'] = str_replace(array('>', '/'), '', trim($message[$i]['body']));
		$message[$i]['body'] = str_replace(array('"'), '\"', trim($message[$i]['body']));

		$message[$i]['subject'] = str_replace('\\', "", $message[$i]['subject']);
		$message[$i]['subject'] = str_replace(array("\r\n", "\r", "\n", "<br />", "<br/>"), "\\n", $message[$i]['subject']);
		$message[$i]['subject'] = str_replace(array('>', '/'), '', trim($message[$i]['subject']));
		$message[$i]['subject'] = str_replace(array('"'), '\"', trim($message[$i]['subject']));
		$message[$i]['subject'] = strip_tags($message[$i]['subject']);

		$memuser = getname($message[$i]['mem_id']);
		$frmuser = getname($message[$i]['frm_id']);
		$get_latest_message = strpos($message[$i]['body'], 'wrote:');
		if ($get_latest_message !== FALSE) {
		    $remaining_string = substr($message[$i]['body'], 0, $get_latest_message);
		    $message_body = strpos($remaining_string, $frmuser);
		    if ($message_body == FALSE) {
			$message_body = strpos($remaining_string, $memuser);
		    }
		    $message12 = substr($remaining_string, 0, $message_body);
		    $message_content = substr($message12, 0, 75);
		} else {
		    $message_content = substr($message[$i]['body'], 0, 75);
		}

		//$read = isset($message[$i]['read']) && ($message[$i]['read']) ? $message[$i]['read'] : NULL;//"viewed":"' . $read . '",
		$str_temp = '{
            "messageId":"' . str_replace('"', '\"',trim($message[$i]['mes_id'])) . '",
            "userId":"' . str_replace('"', '\"',trim($message[$i]['mes_mem'])) . '",
            "messageTitle":"' . str_replace('"', '\"',trim(preg_replace('/\s+/', ' ', $message[$i]['subject']))) . '",
            "messageContent":"' . str_replace('"', '\"',trim(preg_replace('/\s+/', ' ', $message_content))) . '",
            "senderUserId":"' . str_replace('"', '\"',trim($message[$i]['frm_id'])) . '",
            "messageDate":"' . str_replace('"', '\"',date('Y-m-d H:i:s', $message[$i]['date'])) . '",
            "senderName":"' . str_replace('"', '\"',trim($message[$i]['profilenam'])) . '",
            "senderImageUrl":"' . str_replace('"', '\"',$message[$i]['photo_b_thumb']) . '",
            "senderGender":"' . str_replace('"', '\"',trim($message[$i]['gender'])) . '"
    }';

		$str = $str . $str_temp;
		$str = $str . ',';
	    }
	    $str = rtrim($str, ',');
	    $response_str = response_repeat_string();
	    $response_mess = '
        {
           ' . $response_str . '
           "getAllMessageList":{
              "errorCode":"' . $return_codes["getAllMessageList"]["SuccessCode"] . '",
              "errorMsg":"' . $return_codes["getAllMessageList"]["SuccessDesc"] . '",
              "messageCount":"' . $count . '",              
              "TotalmessageCount":"' . $message['Total'] . '",
              "FolderType":"' . $folderType . '",
               "MessageList":[' . $str . ']
           }
        }';
	} else {
	    $response_mess = '
                {
   ' . response_repeat_string() . '
         "Messages":{
          "errorCode":"' . $return_codes["Messages"]["FailedToAddRecordCode"] . '",
          "errorMsg":"' . $return_codes["Messages"]["FailedToAddRecordDesc"] . '",
          "MessageList":[' . $str . ']
            }
       }';
	}
	return getValidJSON($response_mess);
    }

    /*  function messageDetails()
      Purpose: To get the detailed information about messages
      Parameters : $xmlrequest       : Request array for message delete
      $response_message : ?
      Returns : JSON formatted response for message detail */

    function messageDetails($response_message, $xmlresponse) {

	global $return_codes;
	$message = array();
	$message = $this->view_message_detail($xmlresponse);
	//print_r($message);
	$reply_count = (isset($message['reply'])) && ($message['reply']) ? count($message['reply']) : 0;
	if (isset($xmlresponse['MessageDetails']['userId'])) {
	    $mainBodyType = NULL;
	    $subjectType = NULL;
	    $replyMsg = NULL;
	    $postcount = 0;
	    $str_temp = "";
	    $str1 = "";
//            print_r($message['main_body']);
	    $message['main_body'] = str_replace('\\', "", $message['main_body']);
	    $message['main_body'] = str_replace(array("\r\n", "\r", "\n", "<br />", "<br/>"), "\\n", $message['main_body']);
	    //$message['photo_b_thumb'] = (isset($message['photo_b_thumb']) && (strlen($message['photo_b_thumb']) > 7)) ? $this->profile_url . $message['photo_b_thumb'] : $this->profile_url . default_images($message['gender'], $message['profile_type']);
	    //$message['photo_b_thumb'] = (isset($message['photo_b_thumb']) && (strlen($message['photo_b_thumb']) > 7)) ? $this->profile_url . $message['photo_b_thumb'] : $this->profile_url . default_images1($message['gender'], $message['profile_type']);
		$message['photo_b_thumb'] = isset($message['is_facebook_user']) && (strlen($message['photo_b_thumb']) > 7) && ($message['is_facebook_user'] == 'y' || $message['is_facebook_user'] == 'Y') ? $message['photo_b_thumb'] : ((isset($message['photo_b_thumb']) && (strlen($message['photo_b_thumb']) > 7)) ? $this->profile_url . $message['photo_b_thumb'] : $this->profile_url . default_images($message['gender'], $message['profile_type']));
	    $input_main_body = $message['main_body'];
//            $input_main_body = str_replace('\\', '', $input_main_body);
	    if (preg_match(REGEX_URL, $input_main_body, $url)) {
		$mainBodyType = extract_url($input_main_body);
		$mainBodyType = strip_tags($mainBodyType);
		$mainBodyType = str_replace(array("\r\n", "\r", "\n", "<br />", "\""), "\\n", $mainBodyType);
	    } else {
		$mainBodyType = 'text';
	    }
//print_r($message['main_body']);
	    $message['main_body'] = isset($message['main_body']) && ($message['main_body']) ? $message['main_body'] : NULL;
//            $message['main_body'] = str_replace('\\', "", $message['main_body']);

	    $message['main_body'] = strip_tags($message['main_body'], "<br />");
//            $message['main_body'] = str_replace(array("\r\n", "\r", "\n", "<br />", "<br/>"), "\\n", $message['main_body']);
	    $message['main_body'] = subanchor($message['main_body']);
//            print_r($message['main_body']);
	    for ($i = 0; $i < $reply_count; $i++) {

		$input_subject = isset($message['subject']) && ($message['subject']) ? $message['subject'] : NULL;
		$input_subject = str_replace('\\', '', $input_subject);
		if (preg_match(REGEX_URL, $input_subject, $url)) {
		    $subjectType = extract_url($input_subject);
		    $subjectType = strip_tags($subjectType);
		    $subjectType = str_replace(array("\r\n", "\r", "\n", "<br />", "\""), "\\n", $subjectType);
		} else {
		    $subjectType = 'text';
		}

		$input_msg = $message['reply'][$i]['msg'];
		$input_msg = str_replace('\\', '', $input_msg);
		if (preg_match(REGEX_URL, $input_msg, $url)) {
		    $replyMsg = extract_url($input_msg);
		    $replyMsg = strip_tags($replyMsg);
		    $replyMsg = str_replace(array("\r\n", "\r", "\n", "<br />", "\""), "\\n", $replyMsg);
		} else {
		    $replyMsg = 'text';
		}
//                $message[$i]['body'] = isset($message[$i]['body']) && ($message[$i]['body']) ? $message[$i]['body'] : NULL;
//                $message[$i]['body'] = str_replace('\\', "", $message[$i]['body']);
//                $message[$i]['body'] = strip_tags($message[$i]['body'], "<br />");
//                $message[$i]['body'] = str_replace(array("\r\n", "\r", "\n", "<br />", "<br/>"), "\\n", $message[$i]['body']);
//print_r($message[$i]['body']);
		$message['reply'][$i]['msg'] = str_replace('\\', "", $message['reply'][$i]['msg']);
		$message['reply'][$i]['msg'] = strip_tags($message['reply'][$i]['msg'], "<br />");
		$message['reply'][$i]['msg'] = str_replace(array("\r\n", "\r", "\n", "<br />", "<br/>"), "\\n", $message['reply'][$i]['msg']);

		//
		$message['subject'] = subanchor($message['subject']);

		$message['reply'][$i]['msg'] = subanchor($message['reply'][$i]['msg']);
		//
		$message['reply'][$i]['reply_photo_b_thumb'] = (isset($message['reply'][$i]['reply_photo_b_thumb']) && (strlen($message['reply'][$i]['reply_photo_b_thumb']) > 7)) ? $this->profile_url . $message['reply'][$i]['reply_photo_b_thumb'] : $this->profile_url . default_images($message['reply'][$i]['gender'], $message['reply'][$i]['profile_type']);

				$str_temp = '{
                     "replyId":"' . str_replace('"', '\"',trim($message['reply'][$i]['reply_id'])) . '",
                     "replyPhotoThumb":"' . str_replace('"', '\"',$message['reply'][$i]['reply_photo_b_thumb']) . '",
                     "replyProfilename":"' . str_replace('"', '\"',trim($message['reply'][$i]['reply_profilenam'])) . '",
                     "replyMsg":"' . str_replace('"', '\"',trim($message['reply'][$i]['msg'])) . '",
                     "replyMsgType":"' . str_replace('"', '\"',$replyMsg) . '"}';

		$str1 = $str1 . $str_temp;
		$str1 = $str1 . ',';
	    }
	    $str1 = rtrim($str1, ',');
	    $mem_id = '';
	    if (($xmlresponse['MessageDetails']['FolderType'] == 'inbox') || ($xmlresponse['MessageDetails']['FolderType'] == 'trashed')) {
		$mem_id = $message['frm_id'];
	    } else {
		$mem_id = $message['mes_mem']; //frm_id
	    }
	    date_default_timezone_set("UTC");
	    $response_str = response_repeat_string();
	    $response_mess = '
      {
     ' . $response_str . '
      "MessageDetails":{
      "errorCode":"' . str_replace('"', '\"',$return_codes["MessageDetails"]["SuccessCode"]) . '",
      "errorMsg":"' . str_replace('"', '\"',$return_codes["MessageDetails"]["SuccessDesc"]) . '",
      "messageId":"' . str_replace('"', '\"',$message['mes_id']) . '",
      "subject":"' . str_replace('"', '\"',trim(preg_replace('/\s+/', ' ', $message['subject']))) . '",
      "subjectType":"' . str_replace('"', '\"',$subjectType) . '",
      "date":"' . date('Y-m-d H:i:s', $message['date']) . '",
      "messagebody":"' . str_replace('"', '\"',trim(preg_replace('/\s+/', ' ', ($message['main_body'])))) . '",
      "mainBodyType":"' . str_replace('"', '\"',$mainBodyType) . '",
      "senderId":"' . str_replace('"', '\"',$mem_id). '",
      "senderName":"' . str_replace('"', '\"',trim($message['profilenam'])) . '",
      "senderImageUrl":"' . str_replace('"', '\"',$message['photo_b_thumb']) . '",
      "totalReply":"' . str_replace('"', '\"',$reply_count) . '",
      "MessageReplies":[' . $str1 . ']
             }
        }';
	} else {
	    $response_mess = '
         {
   ' . response_repeat_string() . '
   "MessageDetails":{
      "errorCode":"' . $return_codes["MessageDetails"]["FailedToAddRecordCode"] . '",
      "errorMsg":"' . $return_codes["MessageDetails"]["FailedToAddRecordDesc"] . '",
      "MessageReplies":[' . $str1 . ']
         }
         }';
	}
	return getValidJSON($response_mess);
    }

    /*  function deleteMessage()
      Purpose: To delete the messages the user got
      Parameters : $xmlrequest       : Request array for delete message
      $response_message : ?
      Returns : JSON formatted response for deleting messages */

    function deleteMessage($response_message, $xmlresponse) {

	global $return_codes;
	$message = array();
	$message = $this->delete_message($xmlresponse);

	if (isset($message) && $message == TRUE) {
	    $response_str = response_repeat_string();
	    $response_mess = '
           {
           ' . $response_str . '
           "DeleteMessage":{
              "errorCode":"' . $return_codes['DeleteMessage']['SuccessCode'] . '",
              "errorMsg":"' . $return_codes['DeleteMessage']['SuccessDesc'] . '"

           }
        }';
	} else {
	    $response_mess = '
            {
       ' . response_repeat_string() . '
       "DeleteMessage":{
          "errorCode":"' . $return_codes["DeleteMessage"]["FailedToAddRecordCode"] . '",
          "errorMsg":"' . $return_codes["DeleteMessage"]["FailedToAddRecordDesc"] . '",
       }
	  }';
	}
	return getValidJSON($response_mess);
    }

    /*  function sendMessage()
      Purpose: To send the message
      Parameters : $xmlrequest       : Request array for message send
      $response_message : ?
      Returns : JSON formatted response for message send */

    function sendMessage($response_message, $xmlresponse) {

	global $return_codes;
	$message = array();
	$message = $this->send_message($xmlresponse);
	if (isset($message) && ($message == TRUE)) {
	    $response_str = response_repeat_string();
	    $response_mess = '
               {
               ' . $response_str . '
               "sendMessage":{
                  "errorCode":"' . $return_codes["sendMessage"]["SuccessCode"] . '",
                  "errorMsg":"' . $return_codes["sendMessage"]["SuccessDesc"] . '"
               }
            }';
	} else {
	    $response_mess = '
                {
               ' . response_repeat_string() . '
               "sendMessage":{
                  "errorCode":"' . $return_codes["SendMessage"]["FailedToAddRecordCode"] . '",
                  "errorMsg":"' . $return_codes["SendMessage"]["FailedToAddRecordDesc"] . '",
                   }
                }';
	}
	return getValidJSON($response_mess);
    }

    /*  function replyMessage()
      Purpose: To send the message
      Parameters : $xmlrequest       : Request array for message reply
      $response_message : ?
      Returns : JSON formatted response for message reply */

    function replyMessage($response_message, $xmlresponse) {

	global $return_codes;
	$message = array();
	$message = $this->reply_message($xmlresponse);
	if (isset($message) && ($message == TRUE)) {
	    $response_str = response_repeat_string();
	    $response_mess = '
                {
                   ' . $response_str . '
                   "replyMessage":{
                      "errorCode":"' . $return_codes['replyMessage']['SuccessCode'] . '",
                      "errorMsg":"' . $return_codes['replyMessage']['SuccessDesc'] . '"
                   }
                }';
	} else {
	    $response_mess = '
                    {
                   ' . response_repeat_string() . '
                   "replyMessage":{
                      "errorCode":"' . $return_codes["replyMessage"]["FailedToAddRecordCode"] . '",
                      "errorMsg":"' . $return_codes["replyMessage"]["FailedToAddRecordDesc"] . '",
                       }
                    }';
	}
	return getValidJSON($response_mess);
    }

}

?>