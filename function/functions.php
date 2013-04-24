<?php

function sort2dimesional_array($array, $index, $order='asc', $natsort=FALSE, $case_sensitive=FALSE) {
    if (is_array($array) && count($array) > 0) {
        foreach (array_keys($array) as $key)
            $temp[$key] = $array[$key][$index];
        if (!$natsort)
            ($order == 'asc') ? asort($temp) : arsort($temp);
        else {
            ($case_sensitive) ? natsort($temp) : natcasesort($temp);
            if ($order != 'asc')
                $temp = array_reverse($temp, TRUE);
        }
        foreach (array_keys($temp) as $key)
            (is_numeric($key)) ? $sorted[] = $array[$key] : $sorted[$key] = $array[$key];
        return $sorted;
    }
    return $array;
}

/* * Function:get_request_type($xmlrequest) =>to get type of Request.
 * Parameters: $xmlrequest=>request by user.
  Return: Request Type Array
 *  */

function get_request_type($xmlrequest) {
    $response_array = array('UserLogout', 'BSEViewNonMemGuestList', 'BSENonMemGLCheckIn', 'BSENonMemGLEntourageSearch', 'AlertsClear', 'DeletePhotoComment', 'DeleteEventComment', 'DeleteAppearanceComment', 'AllEntourageListByName', 'CommentsOnPhotosParentComment', 'RespondPhotoTagAlerts', 'DisplayPhotoTagAlert', 'CommentsOnPhotosParentComment', 'RespondPhotoTagAlerts', 'DisplayPhotoTagAlert', 'AllEntourageListByName', 'DeleteAppearanceComment', 'UserLogout', 'DeleteEventComment', 'UserLogin', 'HotPress', 'CommentsOnHotPressPost', 'HotPressPostComment',
        'FriendRequests', 'Entourage', 'Events', 'SearchEvent', 'EventDetails',
        'EventCommentList', 'EventPostComment', 'EventParentChildComment', 'EventReplyComment', 'EventCommentDelete', 'EventViewGuestList', 'EventAddGuestList', 'EventRemoveGuestList', 'EventComments', 'EventPostComment',
        'Photos', 'PhotoAlbumDetails', 'CommentOnPtaghoto', 'MakeProfilePhoto', "AppEntourageList", "AppearanceVenueList", "AppVenueStatusSave", "AppVenueDetail", "CurrentVenueStatus", "AppReward",
        'DeletePhoto', 'Save411', 'Messages', 'sendMessage', 'MessageDetails', 'replyMessage', 'fowrdMessage', 'DeleteMessage', 'Alerts', "AlertsUpdate", 'Profile',
        'Appearances', 'Venues', 'VenueDetails', 'FlagVenue', 'AnnounceArrival',
        'LikePostList', 'LikeComment', 'EntourageList', 'AllEntourageList',
        'DeletePost', 'AddAsFriendRequest', 'DisplayCommentOnPhoto', 'FullScreenPhoto', 'TagPhoto', 'RemoveTag',
        'TagsOnPhoto', 'NoData', 'PhotoUpload', 'AdvanceSearch', 'CreatePhotoAlbum', 'UserSignUp',
        'ProfileParentComment', 'ProfileSubComments', 'PostCommentOnProfile', 'DeleteProfileMessage',
        'profileInfo', 'BackStageEventList', 'eventSharing', 'FBVerifyUser', 'LikePost', "BSEViewGuestList",
        'BSEGLEntourageSearch', 'BSENonMemGLEntourageSearch', 'BSEGLCheckIn', "BSEViewNonMemGuestList", "BSENonMemGLCheckIn", 'BSEViewTblReservationList', 'BSETRCheckInNotes',
        "BSETRViewCheckIn", "CommentOnPhoto", "BSETRConfirmMessageScreen", "HotPressAlert", "CommentAlert", "AppEntourageStatus", "RemoveFriend", "TakeOverProfile", "AppEntStatusComment", "AppEntStatusCommentList", "AppGetAllEventTag", "DeletePhotoComment", "AlertsClear", "GetBadges", "FBStates", "FBCities", "BadgeDetails", "FbUserVerification", "fbSearchUsers", "inviteContacts", "profileRegistration", "getAllMessageList");

    $request_keys = array_keys($xmlrequest);
    $key = $request_keys[1];
    if (in_array($key, $response_array)) {
        $match = $key;
    } else {
        $match = '';
    }
    return $match;
}

function get_key($key) {
    return $key;
}

/* * Function:get_request_type($xmlrequest) =>to get type of Request.
 * Parameters: $xmlrequest=>request by user.
  Return: Request Type Array
 *  

  function get_friend_list($id) {

  $friend = array();
  $friend_id = array();
  $query_id = "SELECT DISTINCT n2.mem_id FROM network n1,network n2 WHERE n1.frd_id=n2.mem_id AND n1.mem_id='$id' AND n2.frd_id='$id'";
  $friend_id = execute_query($query_id, true, "select");
  $friend_id['count'] = (isset($friend_id['count']) && ($friend_id['count'])) ? $friend_id['count'] : 0;
  for ($i = 0; $i < $friend_id['count']; $i++) {

  $frnd_id = $friend_id[$i]['mem_id'];
  $query_friends = "SELECT is_facebook_user,profilenam  ,mem_id ,gender,profile_type,photo_thumb,photo_b_thumb FROM members WHERE mem_id ='$frnd_id'";
  $friend[] = execute_query($query_friends, false, "select");
  }
  $friend['count'] = $friend_id['count'];
  return $friend;
  } */

function get_friend_list($id, $profile) {

    $friend = array();
    $query_id = "SELECT DISTINCT
		n2.mem_id,
		mem.is_facebook_user,
		mem.profilenam,
		mem.mem_id,
		mem.gender,
		mem.profile_type,
		mem.photo_thumb,
		mem.photo_b_thumb
		FROM network n1
		INNER JOIN network n2
		INNER JOIN members mem
		ON (n1.frd_id = n2.mem_id
        AND n2.mem_id = mem.mem_id
        AND mem.profile_type = '$profile')
		WHERE n1.mem_id = '$id'
		"; //AND n2.frd_id = '$id'

    $friend = execute_query_new($query_id, true, "select");

    return $friend;
}

function writelog($type, $logs, $flag, $count=0, $loglevel=0) {
    //settype($logs, "string");
    //var_dump($logs);
    //$loglevel = 2; //0 = no log , 1 = only string logs, 2= everything 

    $ip = $_SERVER['REMOTE_ADDR'];
    $string = "";
    switch ($loglevel) {
        case 0: $string = 0; //$ip . " - " . date("Y-m-d h:m:s") . "\r\n";
            break;
        case 1: $string = $ip . " - " . date("Y-m-d h:m:s") . " - " . $type . "total Records=" . $count . "\r\n";
            break;
        case 2:
            if ($flag) {
                // $logs=json_encode($logs);//@implode(",", $logs)

                $string = $ip . " - " . date("Y-m-d h:m:s:u") . " - " . $type . " - " . r_implode(',', $logs) . "total Records=" . $count . "\r\n";
                //print_r($logs);
            } else {
                $string = $ip . " - " . date("Y-m-d h:m:s:u") . " - " . $type . " - " . $logs . "\r\ntotal Records=" . $count . "\r\n";
            }
            break;
    } //End of switch ($loglevel)
    //print_r($string);
    if ($loglevel !== 0) {

        $RootLogDir = "Logs";
        if (is_dir($RootLogDir) == false)
            mkdir($RootLogDir, 0777);
        $dateDir = date("Y_m_d");

        $fullDirPath = "./" . $RootLogDir . "/" . $dateDir . "/";
        if (is_dir($fullDirPath) == false)
            mkdir($fullDirPath, 0777);

        $file = date("Y_m_d") . "__" . $ip . ".txt";
        $fh = fopen($fullDirPath . $file, 'a+');
        fwrite($fh, $string);
        fclose($fh);

        if (!is_writable($fullDirPath . $file)) {
            chmod($fullDirPath . $file, 777);
        }
    }
    //Return a value of TRUE to indicate that the function has finished successfully
    return TRUE;
}

function r_implode($glue, $pieces) {
    $retVal = array();
    foreach ($pieces as $r_pieces) {
        if (is_array($r_pieces)) {
            $retVal[] = r_implode($glue, $r_pieces);
        } else {
            $retVal[] = $r_pieces;
        }
    }
    return implode($glue, $retVal);
}

function response_repeat_string() {
    $str = '"GenInfo":{ "appname":"SNL",  "appversion":"1.0.0",   "type":"Response"   },';
    return $str;
}

function get_response_string($case, $errorcode, $errordesc) {

    $str = '{
		' . response_repeat_string() . '
		"' . $case . '":{
		"errorCode":"' . $errorcode . '",
		"errorMsg":"' . $errordesc . '"
		}
		}';
    return $str;
}

function get_no_response_message($errorcode, $errordesc) {

    $str = '{
        "NoData":{
		"errorCode":"' . $errorcode . '",
		"errorMsg":"' . $errordesc . '"
		}
		}';
    return $str;
}

function execute_query($query, $check, $querytype="select") {

    $query_result = array();
    $count = 0;

    $result = mysql_query($query) OR die(mysql_error());

    if ($querytype == "select") {


        if ((mysql_num_rows($result) > 0) && ($check)) {
            while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
                $query_result[] = $row;
                $count++;
            }
            if (($check) && ($count)) {
                $query_result['count'] = $count;
            } else {
                $query_result['count'] = 0;
            }
            if (!isset($query_result['count'])) {
                unset($query_result['count']);
            }
        }
        if ((mysql_num_rows($result) > 0) && (!$check)) {
            $query_result = mysql_fetch_array($result, MYSQL_ASSOC);
        }
    }
    if ($querytype == "insert") {
        $query_result['count'] = mysql_affected_rows();
        $query_result['last_id'] = mysql_insert_id();
    }
    if ($querytype == "delete") {
        $query_result['count'] = mysql_affected_rows();
    }
    if ($querytype == "update") {
        $query_result['count'] = mysql_affected_rows();
    }
    return $query_result;
}

function error_CRUD($xmlrequest, $affected_row) {
    $error = array();
    $request_keys = array_keys($xmlrequest);
    $key = $request_keys[1];
    if ($affected_row > 0) {
        $error[$key]['successful_fin'] = true;
    } else {
        $error[$key]['successful_fin'] = false;
        //writelog("Profile:comment_on_photo", $error, true);
    }
    return $error;
}

function photo_upload($jsonRequestData) {
    //$key = get_key($key);
    //    print_r($jsonRequestData);

    if (DEBUG)
        writelog("photo_upload: ", "Start Here ", false);

    $request_keys = array_keys($jsonRequestData);
    $key = $request_keys[1];
    $albumId = null;
    $error = array();
    $userId = mysql_real_escape_string($jsonRequestData[$key]['userId']);
    $venueId = mysql_real_escape_string($jsonRequestData[$key]['venueId']);
    $entourageId = mysql_real_escape_string($jsonRequestData[$key]['entourageId']);
    $userPrivacySetting = mysql_real_escape_string($jsonRequestData[$key]['userPrivacySetting']);
    $displayAsHotPress = mysql_real_escape_string($jsonRequestData[$key]['displayAsHotPress']);
    $totalImageSize = mysql_real_escape_string($jsonRequestData[$key]['totalImageSize']);
    $uploadfilename = mysql_real_escape_string($jsonRequestData[$key]['filename']);
    $albumId = mysql_real_escape_string($jsonRequestData[$key]['albumId']);
    $mime = mysql_real_escape_string(trim($jsonRequestData[$key]['"mime"']));
    $subj = mysql_real_escape_string($jsonRequestData[$key]['subj']);
    $body = mysql_real_escape_string($jsonRequestData[$key]['body']);
    $loc = trim($jsonRequestData[$key]['uploadLocation']);
    //send email
    //    $toUser = isset($entourageId) && ($entourageId != '') ? $entourageId : $venueId;
    //
		//    $get_user_email_id = execute_query("select profilenam,email,photo_thumb from members where mem_id='$userId'", false, "select");
    //    $get_profile_user_email_id = execute_query("select profilenam,email from members where mem_id='$toUser'", false, "select");
    //
		//    $body = "{$get_user_email_id['profilenam']} has replied comment on your profile. Please login to view. {$post}<a href='http://www.socialnightlife.com/development/index.php?pg=profile&usr={$fromid}&action=replydelmsg&cmmentid={$testimonialId['testo_id']}' target='_blank'>Login</a>";
    //    $matter = email_template($get_user_email_id['profilenam'], 'New comment reply on MySNL.', $body, $mem_id, $get_user_email_id['photo_thumb']);
    //    firemail($get_profile_user_email_id['email'], 'From: socialNightLife <socialnightlife.com>\r\n', 'New comment reply on MySNL.', $matter);
    //    if (isset($jsonRequestData[$key]['displayAsHotPress']) && (!$jsonRequestData[$key]['displayAsHotPress'])) {
    //        $jsonRequestData[$key]['uploadLocation'] = trim("Profile");
    //    }

    if (!isset($jsonRequestData[$key]['subj'])) {
        $subj = null;
    }
    if (!isset($jsonRequestData[$key]['body'])) {
        $body = null;
    }
    //------------------
    // $binaryData = file_get_contents("php://input");
    //     print_r($_FILES);
    $uploaddir = "uploads\\tmp\\";
    $uptmpfile = basename($_FILES['userfile']['name']);
    $uploadtmpfile = $uploaddir . $uptmpfile;

    if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadtmpfile)) {
        $binaryData = file_get_contents($uploadtmpfile);
    } else {
        $binaryData = file_get_contents($_FILES['userfile']['tmp_name']);
    }
    if (DEBUG)
        writelog("photo_upload: ", "binaryData " . $binaryData, false);

    $jsonRequestData[$key]["chunkData"] = ($binaryData);

    if (isset($jsonRequestData) && !empty($jsonRequestData)) {
        $tmpfilename = $jsonRequestData[$key]['userId'] . "_" . $jsonRequestData[$key]['filename'] . date("YmdHis") . ".txt";


        if ($loc != 'Appearance')
            $path = "../photos/";
        else
            $path = "../Appearance/";


        $filename = $path . $tmpfilename;
        //$newfilename = $path . "final" . $jsonRequestData[$key]['userId'] . "__" . date("YmdHis") . "__" . $jsonRequestData[$key]["filename"];
        $rand = rand(0, 10000);
        $file_extension = substr($jsonRequestData[$key]["filename"], strrpos($jsonRequestData[$key]["filename"], '.') + 1);

        $newfilename = $path . md5($jsonRequestData[$key]['userId'] . time() . $rand) . "." . $file_extension;

        $newfilename_db = md5($jsonRequestData[$key]['userId'] . time() . $rand) . "." . $file_extension;


        if ($jsonRequestData[$key]['currentChunk'] == $jsonRequestData[$key]['totalChunks']) {

            // $data = $jsonRequestData[$key]["chunkData"];
            $data = ($binaryData);
            if (DEBUG)
                writelog("photo_upload:writing final chunk binary data ", $data, false);

            $writefile_status = file_put_contents($filename, $data, FILE_APPEND);
            if ($writefile_status === FALSE) {
                if (DEBUG)
                    writelog("photo_upload:writing final chunk binary data ", 'Failed', false);

                $error['write'] = "Failed to write image";
            } else {
                $imageData = (file_get_contents($filename));
                //header('Content-type: ' . $jsonRequestData[$key]["imageInfo"]["mime"]);
                if (file_put_contents($newfilename, $imageData) === FALSE) {
                    $error['write'] = "Failed to write image.' . $newfilename . '";

                    if (DEBUG)
                        writelog("photo_upload:writing final chunk binary data ", 'Failed on writing File ::  ' . $newfilename, false);
                } else {
                    if (DEBUG)
                        writelog("photo_upload:writing final chunk binary data ", 'Success on writing File ::  ' . $newfilename, false);
                    if (isset($jsonRequestData[$key]["filename"])) {
                        //$photos=photo_convert_sizes($jsonRequestData);//
                        ////////////////////////convert into 3-types as per the website///////
                        $old = $newfilename;

                        //$srcImage = @imageCreateFromJPEG($old);
                        $th = md5($jsonRequestData[$key]['userId'] . time() . $rand) . "th";
                        $b_th = md5($jsonRequestData[$key]['userId'] . time() . $rand) . "bth";
                        $bb_th = md5($jsonRequestData[$key]['userId'] . time() . $rand) . "bbth";

                        $newname_th = $path . $th;
                        $newname_b_th = $path . $b_th;
                        $newname_bb_th = $path . $bb_th;

                        $thumb1 = $newname_th . "." . $file_extension;
                        $thumb2 = $newname_b_th . "." . $file_extension;
                        $thumb3 = $newname_bb_th . "." . $file_extension;

                        if ($loc != 'Appearance') {
                            $photo_mid = "photos/" . $th . "." . $file_extension;
                            $photo_small = "photos/" . $b_th . "." . $file_extension;
                            $photo_litbig = "photos/" . $bb_th . "." . $file_extension;
                        } else {
                            $photo_mid = "Appearance/" . $th . "." . $file_extension;
                            $photo_small = "Appearance/" . $b_th . "." . $file_extension;
                            $photo_litbig = "Appearance/" . $bb_th . "." . $file_extension;
                        }

                        $sizee = @getimagesize($newfilename);
                        $srcwidth = $sizee[0];
                        $srcheight = $sizee[1];
                        switch ($sizee['mime']) {
                            case "image/jpeg" :
                                $srcImage = imagecreatefromjpeg($old);
                                break;
                            case "image/png":
                                $srcImage = imagecreatefrompng($old);
                                break;
                            case "image/gif":
                                $srcImage = imagecreatefromgif($old);
                                break;
                        }
                        //landscape
                        if ($srcwidth > $srcheight) {
                            $destwidth1 = 65;
                            $rat = $destwidth1 / $srcwidth;
                            $destheight1 = (int) ($srcheight * $rat);
                            $destwidth2 = 150;
                            $rat2 = $destwidth2 / $srcwidth;
                            $destheight2 = (int) ($srcheight * $rat2);
                        }
                        //portrait
                        elseif ($srcwidth < $srcheight) {
                            $destheight1 = 65; //100;
                            $rat = $destheight1 / $srcheight;
                            $destwidth1 = 65; //(int) ($srcwidth * $rat);
                            $destheight2 = 150;
                            $rat = $destheight2 / $srcheight;
                            $destwidth2 = (int) ($srcwidth * $rat);
                        }
                        //quadro
                        elseif ($srcwidth == $srcheight) {
                            $destwidth1 = 65;
                            $destheight1 = 65;
                            $destwidth2 = 150;
                            $destheight2 = 150;
                        }
                        //$width = imagesx($old );
                        //$height = imagesy( $old );
                        $sizee = @getimagesize($old);
                        $new_width = $sizee[0];
                        $new_height = $sizee[1];

                        if ($new_width > "200" || $new_height > "180") {
                            $ratio = (float) ($new_height / $new_width);
                            if ($new_width >= "200") {
                                $new_width = "200";
                                $new_height = $new_width * $ratio;
                            }
                            if ($new_height > "180" and $new_width > "200") {
                                $new_height = "180";
                                $new_width = $new_width / $ratio;
                            }
                            if ($new_height > "180") {
                                $new_height = "180";
                                $new_width = $new_width / $ratio;
                            }
                        }

                        $destImage1 = @imageCreateTrueColor($destwidth1, $destheight1);
                        $destImage2 = @imageCreateTrueColor($destwidth2, $destheight2);
                        $destImage3 = @imageCreateTrueColor($new_width, $new_height);

                        @imagecopyresampled($destImage1, $srcImage, 0, 0, 0, 0, $destwidth1, $destheight1, $srcwidth, $srcheight);
                        @imagecopyresampled($destImage2, $srcImage, 0, 0, 0, 0, $destwidth2, $destheight2, $srcwidth, $srcheight);
                        @imagecopyresampled($destImage3, $srcImage, 0, 0, 0, 0, $new_width, $new_height, $srcwidth, $srcheight);


                        if ($sizee['mime'] == "image/jpeg") {
                            @imageJpeg($destImage1, $thumb1, 80);
                            @imageJpeg($destImage2, $thumb2, 80);
                            @imageJpeg($destImage3, $thumb3, 80);
                        } elseif ($sizee['mime'] == "image/png") {
                            @imagepng($destImage1, $thumb1, 80);
                            @imagepng($destImage2, $thumb2, 80);
                            @imagepng($destImage3, $thumb3, 80);
                        } elseif ($sizee['mime'] == "image/gif") {
                            @imagegif($destImage1, $thumb1, 80);
                            @imagegif($destImage2, $thumb2, 80);
                            @imagegif($destImage3, $thumb3, 80);
                        }


                        //ImageDestroy($srcImage);
                        @imageDestroy($destImage1);
                        @imageDestroy($destImage2);
                        @imageDestroy($destImage3);
                        unlink($filename);


                        /////////////////////////END/////////////////////////
                        $error['write'] = "Successful to write image";
                    }
                }
            }
        } else {
            //$data = $jsonRequestData[$key]["chunkData"];
            $data = ($binaryData);
            if (DEBUG)
                writelog("photo_upload:writing  chunk binary data for chunk no" . $jsonRequestData[$key]['currentChunk'] . " : ", $data, false);

            $data = urldecode($binaryData);
            if (file_put_contents($filename, $data, FILE_APPEND) === FALSE) {

                // "lastWrittenChunk":"' . $jsonRequestData[$key]['currentChunk'] . '",
                $error['write'] = 'Failed to write image data for part : ' . $jsonRequestData[$key]['currentChunk'];
            } else {

                // "lastWrittenChunk":"' . $jsonRequestData[$key]['currentChunk'] . '",
                $error['write_succesfully'] = "Succesful to write image data for part : " . $jsonRequestData[$key]['currentChunk'];
            }
        }
    }

    //------------------------------
    if ($loc != 'Appearance')
        $newfilename = "photos/" . $newfilename_db;
    else
        $newfilename = "Appearance/" . $newfilename_db;


    $date = strtotime("now");
    if ($userPrivacySetting) {
        $userPrivacySetting = 'public';
        $hotpressPrivacySetting = 'allfriends';
    } else {
        $userPrivacySetting = 'private';
        $hotpressPrivacySetting = '';
    }
    //print_r($error);
    if (($displayAsHotPress) && (isset($error['write'])) && ($error['write'])) {

        //echo "oooooooooo";
        if (($entourageId == 0) && $loc == 'Hotpress')
            $query_album_check = "SELECT id FROM albums WHERE mem_id='$userId' AND title='hotpress'";
        else
            $query_album_check = "SELECT id FROM albums WHERE mem_id='$entourageId' AND title='hotpress'";

        if (DEBUG)
            writelog("photo_upload: ", "query_album_check AND displayAsHotPress=1 " . $query_album_check, false);

        $result_album_check = execute_query($query_album_check, false, "select");


        //$result_album_check['id']=isset($result_album_check['id'])?$result_album_check['id']:0;
        if ((isset($result_album_check)) && (!$result_album_check)) {
            //	    if($loc !='Appearance'){
            if (($entourageId == 0) && $loc == 'Hotpress')
                $query_album = "INSERT INTO albums(mem_id,type,title,albums.desc,album_cover,create_date)VALUE('$userId','$userPrivacySetting','hotpress','auto generated album','$newfilename','$date')";
            else
                $query_album = "INSERT INTO albums(mem_id,type,title,albums.desc,album_cover,create_date)VALUE('$entourageId','$userPrivacySetting','hotpress','auto generated album','$newfilename','$date')";
            //	    }
            if (DEBUG)
                writelog("photo_upload: ", "query_album AND displayAsHotPress=1  " . $query_album, false);


            $result_album = execute_query($query_album, false, "insert");
            $album_id = $result_album['last_id'];
            $affected_row_album = $result_album['count'];
        } else {

            $album_id = $result_album_check['id'];
            $affected_row_album = true;
        }
        //}
        if ($albumId) {
            $album_id = $albumId;
        }
        //	if($loc !='Appearance'){
        if (($entourageId == 0) && $loc == 'Hotpress')
            $query_photo = "INSERT INTO photo_album(album_id,uploaded_by,photo,uploaded,caption,photo_mid,photo_small,photo_lit_big,sell_price,description)VALUE('$album_id','$userId','$newfilename','$date','$body','$photo_mid','$photo_small','$photo_litbig','0','')";
        else
            $query_photo = "INSERT INTO photo_album(album_id,uploaded_by,photo,uploaded,caption,photo_mid,photo_small,photo_lit_big,sell_price,description)VALUE('$album_id','$entourageId','$newfilename','$date','$body','$photo_mid','$photo_small','$photo_litbig','0','')";
        //	}
        if (DEBUG)
            writelog("photo_upload: ", "query_photo AND displayAsHotPress=1  " . $query_photo, false);

        $result_photo = execute_query($query_photo, false, "insert");
        $affected_row = $result_photo['count'];
        $error['photo_id'] = $result_photo['last_id'];
        $case = trim($jsonRequestData[$key]['uploadLocation']);
        //	echo $case;
        $test_id = 0;
        $eventcmmnt_id = 0;
        switch ($case) {
            case "Profile":
                //		echo "okokokok";
                $query_testimonial = "INSERT INTO testimonials(mem_id,from_id,added,image_link,photo_album_id,parent_tst_id,bullet_id,testimonial,publishashotpress,stat,post_via)VALUE('$entourageId','$userId','$date','$newfilename','" . $error['photo_id'] . "','0','0','$subj" . "$body','','a','1')";
                //('$userId','$userId','$date','$newfilename','$album_id','0','0','$subj" . "$body','')
                $result_testimonial = execute_query($query_testimonial, false, "insert");
                $test_id = $result_testimonial['last_id'];
                //		print_r($test_id);
                if (isset($test_id) && ($test_id > 0)) {
                    //send email
                    $get_user_email_id = execute_query("select profilenam,email,photo_thumb from members where mem_id='$userId'", false, "select");
                    //		    print_r($get_user_email_id);
                    $get_entourage_user_email_id = execute_query("select profilenam,email from members where mem_id='$entourageId'", false, "select");
                    //		    print_r($get_entourage_user_email_id);
                    $commentText1 = getname($userId) . ' added a comment to your profile:<br>Please login to view and response.<br><span style="color:#666666">"' . $body . '"</span>' . '' . "<a href='http://www.socialnightlife.com/index.php?pg=profile&usr=$entourageId&action=delmsg&frmid=$userId&cmmentid=$test_id' target='_blank'>Login</a>";
                    //		    print_r($commentText1);
                    $matter = email_template($get_user_email_id['profilenam'], 'You have a new profile comment on SocialNightlife.', $commentText1, $userId, $get_user_email_id['photo_thumb']);
                    firemail($get_entourage_user_email_id['email'], 'From: socialNightLife <socialnightlife.com>\r\n', 'You have a new profile comment on SocialNightlife.', $matter);
                }
                break;

            case "Events":
                //eventId

                $even_id = mysql_real_escape_string($jsonRequestData[$key]['eventId']);
                $error['eventId'] = $even_id;
                if ($even_id) {
                    $query_own = "SELECT even_own FROM event_list WHERE even_id='$even_id'";
                    $result_own = execute_query($query_own, false, "select");
                }
                $event_own = isset($result_own['even_own']) && ($result_own['even_own']) ? $result_own['even_own'] : NULL;
                $query_events = "INSERT INTO events_comments(parent_id,even_id,mem_id,from_id,comment,date,msg_alert,image_link,photo_album_id,post_via)VALUES(0,'$even_id','$event_own','$userId','$subj" . "$body','$date','y','$newfilename','" . $error['photo_id'] . "','1')";
                $result_events = execute_query($query_events, false, "insert");
                //$affected_row = $result_events['count'];
                //$affected_row_album = 1;
                $eventcmmnt_id = $result_events['last_id'];
                break;

            case "Appearance":
                if (!isset($jsonRequestData[$key]["filename"])) {
                    $newfilename = '';
                }
                $album_check_for_app_usr = "SELECT id FROM albums WHERE mem_id='$userId' AND title='Appearances'";
                $result_album_check_usr = execute_query($album_check_for_app_usr, false, "select");
                $album_id_usr = $result_album_check_usr['id'];
                if ((isset($result_album_check_usr)) && ($result_album_check_usr)) {
                    $photo_album_usr = "INSERT INTO photo_album(album_id,uploaded_by,photo,uploaded,caption,photo_mid,photo_small,photo_lit_big,sell_price,description)VALUE('$album_id_usr','$userId','$newfilename','$date','$body','$photo_mid','$photo_small','$photo_litbig','0','')";
                    $photo_album_usr = execute_query($photo_album_usr, false, "insert");
                } else {
                    $album_create = "INSERT INTO albums(mem_id,type,title,albums.desc,album_cover,create_date)VALUE('$userId','$userPrivacySetting','Appearances','auto generated album','$newfilename','$date')";
                    $photo_album_usr = execute_query($album_create, false, "insert");
                    if ($photo_album_usr['last_id'] > 0) {
                        $photo_album_usrIN = "INSERT INTO photo_album(album_id,uploaded_by,photo,uploaded,caption,photo_mid,photo_small,photo_lit_big,sell_price,description)VALUE('{$photo_album_usr['last_id']}','$userId','$newfilename','$date','$body','$photo_mid','$photo_small','$photo_litbig','0','')";
                        $photo_album_usrIN = execute_query($photo_album_usrIN, false, "insert");
                    }
                }
                $album_check_for_app_venue = "SELECT id FROM albums WHERE mem_id='$venueId' AND title='Appearances'";
                $result_album_check_venue = execute_query($album_check_for_app_venue, false, "select");
                $album_id_venue = $result_album_check_venue['id'];
                if ((isset($result_album_check_venue)) && ($result_album_check_venue)) {
                    $photo_album_venue = "INSERT INTO photo_album(album_id,uploaded_by,photo,uploaded,caption,photo_mid,photo_small,photo_lit_big,sell_price,description)VALUE('$album_id_venue','$venueId','$newfilename','$date','$body','$photo_mid','$photo_small','$photo_litbig','0','')";
                    $photo_album_venue = execute_query($photo_album_venue, false, "insert");
                } else {
                    $album_create = "INSERT INTO albums(mem_id,type,title,albums.desc,album_cover,create_date)VALUE('$venueId','$userPrivacySetting','Appearances','auto generated album','$newfilename','$date')";
                    $photo_album_venue = execute_query($album_create, false, "insert");
                    if ($photo_album_venue['last_id'] > 0) {
                        $photo_album_venueVN = "INSERT INTO photo_album(album_id,uploaded_by,photo,uploaded,caption,photo_mid,photo_small,photo_lit_big,sell_price,description)VALUE('{$photo_album_venue['last_id']}','$venueId','$newfilename','$date','$body','$photo_mid','$photo_small','$photo_litbig','0','')";
                        $photo_album_venueVN = execute_query($photo_album_venueVN, false, "insert");
                    }
                }
                break;
            case "ProfilePhoto":
                if (isset($userId)) {
                    if (!isset($jsonRequestData[$key]["filename"])) {
                        $newfilename = '';
                    }
                    $getprofileInformation = execute_query("select * from members where mem_id='$userId'", false, "select");
                    if (!empty($getprofileInformation) && is_array($getprofileInformation)) {
                        $update = execute_query("update members set photo='$newfilename', photo_thumb='$photo_mid', photo_b_thumb='$photo_small',photo_bb_thumb='$photo_litbig' where mem_id='$userId'", true, "update");
                    }
                }
                break;
        }
        if ($jsonRequestData[$key]["filename"] == '') {
            $newfilename = '';
        }
        if (trim($jsonRequestData[$key]['uploadLocation']) == 'Appearance') {
            $query_hotpress = "INSERT INTO bulletin(mem_id,visible_to,date,image_link,link_image,photo_album_id,parentid,from_id,testo_id,subj,body,eventcmmnt_id,post_via)VALUE('$userId','','$date','$newfilename','$newfilename','" . $photo_album_venue['last_id'] . "','0','$venueId','$test_id','$subj','$body','$eventcmmnt_id','1')";
        } else {
            if (($entourageId == 0) && $loc == 'Hotpress')
                $query_hotpress = "INSERT INTO bulletin(mem_id,visible_to,date,image_link,photo_album_id,parentid,from_id,testo_id,subj,body,eventcmmnt_id,post_via)VALUE('$userId','','$date','$newfilename','" . $error['photo_id'] . "','0','$userId','$test_id','$subj','$body','$eventcmmnt_id','1')";
            else
                $query_hotpress = "INSERT INTO bulletin(mem_id,visible_to,date,image_link,photo_album_id,parentid,from_id,testo_id,subj,body,eventcmmnt_id,post_via)VALUE('$userId','','$date','$newfilename','" . $error['photo_id'] . "','0','$entourageId','$test_id','$subj','$body','$eventcmmnt_id','1')";
        }
        if (DEBUG)
            writelog("photo_upload: ", "query_hotpress AND displayAsHotPress=1 " . $query_hotpress, false);

        $result_hotpress = execute_query($query_hotpress, false, "insert");
        $affected_row_hotpress = $result_hotpress['count'];
        $photo_last_id = $result_hotpress['last_id'];


        if (isset($jsonRequestData[$key]['uploadLocation']) && (isset($result_hotpress['last_id'])) && ($result_hotpress['last_id']) && ($test_id || $eventcmmnt_id)) {
            $last_hotpress_id = $result_hotpress['last_id'];
            if (($jsonRequestData[$key]['uploadLocation'] == trim("Profile")))
                $query_test_id = "UPDATE testimonials SET bullet_id='$last_hotpress_id',from_id='$userId' WHERE tst_id='$test_id'";
            else
                $query_test_id = "UPDATE events_comments SET bullet_id='$last_hotpress_id',from_id='$userId' WHERE id='$eventcmmnt_id'";
            mysql_query($query_test_id);

            if ($eventcmmnt_id) {
                $test_id = $eventcmmnt_id;
            }

            $error['last_id'] = $test_id;
            $error['hotpress_last_id'] = $result_hotpress['last_id'];
        } else {
            $error['last_id'] = $photo_last_id;
            $error['hotpress_last_id'] = $result_hotpress['last_id'];
        }
        //$error['last_id']=((isset($jsonRequestData[$key]['uploadLocation']))&&($jsonRequestData[$key]['uploadLocation']))?$result_testimonial['last_id']:$result_hotpress['last_id'];


        if (($affected_row > 0) && ($affected_row_album > 0) && ($affected_row_hotpress > 0)) {
            $error[$key]['successful_fin'] = true;
        } else {
            $error[$key]['successful_fin'] = false;
        }
    }//echo 'dsfsdfs';echo $albumId;print_r($error);
    if ((!$displayAsHotPress) && (isset($error['write'])) && ($error['write'])) {
        if (isset($jsonRequestData[$key]['uploadLocation']) && ($jsonRequestData[$key]['uploadLocation'] == trim("Albums"))) {
            $album_id = $albumId;
        } else {
            if (($entourageId == 0) && $loc == 'Hotpress')
                $query_album_check = "SELECT id FROM albums WHERE mem_id='$userId' AND title='hotpress'";
            else
                $query_album_check = "SELECT id FROM albums WHERE mem_id='$entourageId' AND title='hotpress'";
            if (DEBUG)
                writelog("photo_upload: ", "query_album_check AND displayAsHotPress=1 " . $query_album_check, false);

            $result_album_check = execute_query($query_album_check, false, "select");


            //$result_album_check['id']=isset($result_album_check['id'])?$result_album_check['id']:0;
            if ((isset($result_album_check)) && (!$result_album_check)) {
                if (($entourageId == 0) && ($loc == 'Hotpress'))
                    $query_album = "INSERT INTO albums(mem_id,type,title,albums.desc,album_cover,create_date)VALUE('$userId','$userPrivacySetting','hotpress','auto generated album','$newfilename','$date')";
                else
                    $query_album = "INSERT INTO albums(mem_id,type,title,albums.desc,album_cover,create_date)VALUE('$entourageId','$userPrivacySetting','hotpress','auto generated album','$newfilename','$date')";
                if (DEBUG)
                    writelog("photo_upload: ", "query_album AND displayAsHotPress=1  " . $query_album, false);


                $result_album = execute_query($query_album, false, "insert");
                $album_id = $result_album['last_id'];
                $affected_row_album = $result_album['count'];
            } else {

                $album_id = $result_album_check['id'];
                $affected_row_album = true;
            }
            //}
            if ($albumId) {
                $album_id = $albumId;
            }

            //albums=>
            if (($entourageId == 0) && ($loc == 'Hotpress'))
                $query_photo = "INSERT INTO photo_album(album_id,uploaded_by,photo,uploaded,caption,photo_mid,photo_small,photo_lit_big,sell_price,description)VALUE('$album_id','$userId','$newfilename','$date','$body','$photo_mid','$photo_small','$photo_litbig','0','')";
            else
                $query_photo = "INSERT INTO photo_album(album_id,uploaded_by,photo,uploaded,caption,photo_mid,photo_small,photo_lit_big,sell_price,description)VALUE('$album_id','$entourageId','$newfilename','$date','$body','$photo_small','$photo_mid','$photo_litbig','0','')";
            if (DEBUG)
                writelog("photo_upload: ", "query_photo AND displayAsHotPress=1  " . $query_photo, false);

            $result_photo = execute_query($query_photo, false, "insert");
            $affected_row = $result_photo['count'];
            $error['photo_id'] = $result_photo['last_id'];
        }
        $case = trim($jsonRequestData[$key]['uploadLocation']);
        $test_id = 0;
        $eventcmmnt_id = 0;

        switch ($case) {
            case "Profile":
                $query_testimonial = "INSERT INTO testimonials(mem_id,from_id,added,image_link,photo_album_id,parent_tst_id,bullet_id,testimonial,publishashotpress,stat,post_via)VALUE('$entourageId','$userId','$date','$newfilename','" . $error['photo_id'] . "','0','0','$subj" . "$body','','a','1')";
                //('$userId','$userId','$date','$newfilename','$album_id','0','0','$subj" . "$body','')
                $result_testimonial = execute_query($query_testimonial, false, "insert");
                $test_id = $result_testimonial['last_id'];
                break;

            case "Events":
                //eventId

                $even_id = mysql_real_escape_string($jsonRequestData[$key]['eventId']);
                $error['eventId'] = $even_id;
                if ($even_id) {
                    $query_own = "SELECT even_own FROM event_list WHERE even_id='$even_id'";
                    $result_own = execute_query($query_own, false, "select");
                }
                $event_own = isset($result_own['even_own']) && ($result_own['even_own']) ? $result_own['even_own'] : NULL;
                $query_events = "INSERT INTO events_comments(parent_id,even_id,mem_id,from_id,comment,date,msg_alert,image_link,photo_album_id,post_via)VALUES(0,'$even_id','$event_own','$userId','$subj" . "$body','$date','y','$newfilename','" . $error['photo_id'] . "','1')";
                $result_events = execute_query($query_events, false, "insert");
                //$affected_row = $result_events['count'];
                //$affected_row_album = 1;
                $eventcmmnt_id = $result_events['last_id'];
                break;

            case "Albums":
                $query_photo = "INSERT INTO photo_album(album_id,uploaded_by,photo,uploaded,caption,photo_mid,photo_small,photo_lit_big,sell_price,description)VALUE('$album_id','$userId','$newfilename','$date','$body','$photo_mid','$photo_small','$photo_litbig','0','')";
                if (DEBUG)
                    writelog("photo_upload: ", "displayAsHotPress=0 " . $query_photo, false);

                $result_photo = execute_query($query_photo, false, "insert");
                $error['photo_id'] = $result_photo['last_id'];
                $affected_row = $result_photo['count'];
                break;


                if ($affected_row > 0) {
                    $error[$key]['successful_fin'] = true;
                } else {
                    $error[$key]['successful_fin'] = false;
                }
        }
        if ($eventcmmnt_id) {
            $test_id = $eventcmmnt_id;
        }

        $error['last_id'] = $test_id;
    }

    if (DEBUG)
        writelog("photo_upload:return ", $error, true);
    $error['hotpressAlbumId'] = $album_id;
    //$error['photoId']=;
    $error['file_name'] = $newfilename;
    return $error;
}

function photo_upload_valid($xmlrequest) {

    $request_keys = array_keys($xmlrequest);
    $key = $request_keys[1];
    //$key = get_key($key);echo $key;die();
    $userId = mysql_real_escape_string($xmlrequest[$key]['userId']);

    $query_user = "SELECT COUNT(*) FROM members WHERE mem_id='$userId'";
    $result_user = execute_query($query_user, false, "select");
    $error['successful'] = isset($result_user['COUNT(*)']) && ($result_user['COUNT(*)']) ? true : false;
    return $error;
}

function bubble_sort($arr, $key='date') {
    if ((isset($arr['count'])) && ($arr['count'])) {
        $count = $arr['count'];
    } else {
        $count = 0;
    }
    //$key = 'date';
    for ($i = 0; $i < $count; $i++) {
        for ($j = $i; $j < $count; $j++) {
            $date1 = (int) $arr[$i][$key];
            $date2 = (int) $arr[$j][$key];
            if ($date1 < $date2) {
                $temp = $arr[$i];
                $arr[$i] = $arr[$j];
                $arr[$j] = $temp;
            }
        }
    }
    // print_r($arr);die();
    return $arr;
}

function bubble_sort_reverse($arr, $key='date') {
    if ((isset($arr['count'])) && ($arr['count'])) {
        $count = $arr['count'];
    } else {
        $count = 0;
    }
    //$key = 'date';
    for ($i = 0; $i < $count; $i++) {
        for ($j = $i; $j < $count; $j++) {
            $date1 = (int) $arr[$i][$key];
            $date2 = (int) $arr[$j][$key];
            if ($date2 > $date1) {
                $temp = $arr[$i];
                $arr[$i] = $arr[$j];
                $arr[$j] = $temp;
            }
        }
    }
    // print_r($arr);die();
    return $arr;
}

function time_difference($time) {

    //return timeAgo($time, 4);
    //date_default_timezone_set("UTC");
    $time = (int) ($time);
    $difference = (int) (time()) - $time;

    if ($difference < 60) {
        if ($difference < 0) {
            $difference = 0;
        }
        return $difference . " seconds ago";
    } else {
        $difference = round($difference / 60);
        if ($difference < 60)
            return $difference . " minutes ago";
        else {
            $difference = round($difference / 60);
            if ($difference < 24)
                return $difference . " hours ago";
            else {
                $difference = round($difference / 24);
                if ($difference < 7)
                    return $difference . " days ago";
                else {
                    $difference = round($difference / 7);
                    return $difference . " weeks ago";
                }
            }
        }
    }

    /*
      if (($difference < 60) && ($difference >= 0)) {
      return $difference . " " . 'seconds' . " " . "ago";
      }

      if (($difference >= 60) && ($difference < (60 * 60))) {
      $sec = $difference % (60);
      $min_cal = (int) ($difference / (60));
      $min = round($min_cal % (60));
      // $min = (int) ($difference / (60));
      // $time = $min . " " . 'minute' . ":" . $sec . " " . 'seconds';
      if ($min == 1) {
      $time = $min . " " . 'minute' . " " . "ago";
      } else {
      $time = $min . " " . 'minutes' . " " . "ago";
      }

      return $time;
      }

      if (($difference >= (60 * 60)) && ($difference < (24 * 60 * 60))) {
      $sec = $difference % (60);
      $min_cal = (int) ($difference / (60));
      $min = (int) ($min_cal % (60));
      $hour = round($difference / (60 * 60));
      //$time = $hour . " " . "hour" . ":" . $min . " " . 'minute' . ":" . $sec . " " . 'seconds';
      if ($hour == 1) {
      $time = $hour . " " . "hour" . " " . "ago";
      } else {
      $time = $hour . " " . "hours" . " " . "ago";
      }

      return $time;
      }
      if (($difference >= (24 * 60 * 60)) && ($difference < (7 * 24 * 60 * 60))) {
      $day_cal = (int) ($difference / (24 * 60 * 60));
      $day = round($day_cal % (24 * 60 * 60));
      //$day = $difference % (24 * 60 * 60);
      if ($day == 1) {
      $time = $day . " " . "day" . " " . "ago";
      } else {
      $time = $day . " " . "days" . " " . "ago";
      }

      return $time;
      }
      if (($difference >= (7 * 24 * 60 * 60))) {
      $day_cal = (int) ($difference / (7 * 24 * 60 * 60));
      $day = (int) ($day_cal % (7));
      //$day = $difference % (24 * 60 * 60);
      $week = round($difference / (7 * 24 * 60 * 60));
      //$time = $week . " " . "week" . "  " . $day . "days";
      if ($week == 1) {
      $time = $week . " " . "week" . " " . "ago";
      } else {
      $time = $week . " " . "weeks" . " " . "ago";
      }

      return $time;
      }
     */
}

function timeAgo($timestamp, $granularity=2) {

    $difference = time() - $timestamp;
    if ($difference < 0)
        return '0 seconds ago';
    elseif ($difference < (864000)) {
        $periods = array('week' => 604800, 'day' => 86400, 'hr' => 3600, 'min' => 60, 'sec' => 1);
        $output = '';
        foreach ($periods as $key => $value) {

            if ($difference >= $value) {
                $time = floor($difference / $value);
                $difference %= $value;
                $output .= ( $output ? ' ' : '') . $time . ' ';
                $output .= ( ($time > 1 && $key == 'day') ? $key . 's' : $key);
                $granularity--;
            }
            if ($granularity == 0)
                break;
        }
        return ($output ? $output : '0 seconds') . ' ago';
    }
    else
        return "Posted on : " . date("H:i:s A", $timestamp) . " on " . date("M-d", $timestamp);
}

function time_difference1($timestamp) {

    $difference = time() - $timestamp;

    if ($difference < 60)
        return $difference . " seconds ago";
    else {
        $difference = round($difference / 60);
        if ($difference < 60)
            return $difference . " minutes ago";
        else {
            $difference = round($difference / 60);
            if ($difference < 24)
                return $difference . " hours ago";
            else {
                $difference = round($difference / 24);
                if ($difference < 7)
                    return $difference . " days ago";
                else {
                    $difference = round($difference / 7);
                    return $difference . " weeks ago";
                }
            }
        }
    }
}

function age_criteria($birthday, $fromAge, $toAge) {

    $fromAge = (int) ($fromAge);
    $toAge = (int) ($toAge);



    $difference = (int) (time()) - (int) ($birthday);
    $difference = (int) ($difference / (365 * 24 * 60 * 60));
    if (($difference >= $fromAge) && ($difference <= $toAge)) {
        return true;
    } else {
        return false;
    }
}

function distance($lat1, $lon1, $lat2, $lon2, $unit) {

    $theta = $lon1 - $lon2;
    $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
    $dist = acos($dist);
    $dist = rad2deg($dist);
    $miles = $dist * 60 * 1.1515;
    $unit = strtoupper($unit);

    if ($unit == "K") {
        return ($miles * 1.609344);
    } else if ($unit == "N") {
        return ($miles * 0.8684);
    } else {
        return $miles;
    }
}

function latlong($address) {
    $geoCodeURL = "http://maps.google.com/maps/api/geocode/json?address=" . urlencode($address) . "&sensor=false";
    //$geocode = file_get_contents('http://maps.google.com/maps/api/geocode/json?address=' . urlencode($address) . '&sensor=false');

    $arr = array();
    $output = json_decode(file_get_contents($geoCodeURL), true);
    //573/1,+Jangli+Maharaj+Road,+Deccan+Gymkhana,+Pune,+Maharashtra,+India
    if (!empty($output['results'])) {
        $lat = $output["results"][0]["geometry"]["location"]["lat"];
        $long = $output["results"][0]["geometry"]["location"]["lng"];

        $arr[0] = $lat;
        $arr[1] = $long;
    }
    return $arr;
}

function make_reply($message, $mem_id) {
    if ($message == '')
        return '';
    else {

        $sql = "select profilenam from members where mem_id='$mem_id'";
        $res = execute_query($sql, false, "select");
        $res = $res['profilenam'];
        $authorinfo = "\n\n" . $res . " wrote:";
        $msg1 = $message . $authorinfo;

        return $msg1;
    }
}

function make_fowrd($mem_id) {

    if ($mem_id == '')
        return '';

    else {

        $sql = "select profilenam from members where mem_id='$mem_id'";
        $res = execute_query($sql, false, "");
        $authorinfo = "\n\n" . $res['profilenam'] . " wrote:\n";


        return strip_tags($authorinfo);
    }
}

function getname($uid) {

    $sql = "select profilenam from members where mem_id='$uid'";
    $res = execute_query($sql, false, "select");
    return @$res['profilenam'];
}

function get_teammember_name($tm_id, $type) {

    $mem_name = "";
    if ($type == "member") {

        $host_sql = "select  tm.id,  mem.fname, mem.lname, mem.profilenam from team_members as tm, members as mem where
	tm.frd_id='$tm_id' AND tm.frd_id=mem.mem_id";

        $res = execute_query($host_sql, false, "select");

        $res['fname'] = isset($res['fname']) && ($res['fname']) ? $res['fname'] : NULL;
        $res['lname'] = isset($res['lname']) && ($res['lname']) ? $res['lname'] : NULL;
        $mem_name = $res['fname'] . ' ' . $res['lname'];
        if (empty($mem_name))
            $mem_name = isset($res['profilenam']) && ($res['profilenam']) ? $res['profilenam'] : "";
    }
    else {
        $non_member_host_sql = "select name from team_non_members  where id = '" . $tm_id . "'";
        $res = execute_query($non_member_host_sql, false, "select");
        $mem_name = isset($res['name']) && ($res['name']) ? $res['name'] : "";
    }
    return $mem_name;
}

function is_friend($userId, $entourageId) {

    $query = "SELECT COUNT(*) FROM network WHERE (mem_id='$userId' AND frd_id='$entourageId')||(mem_id='$entourageId' AND frd_id='$userId')";
    //$query_id = "SELECT COUNT(*) FROM network n1,network n2 WHERE n1.frd_id=n2.mem_id AND n1.mem_id='$id' AND n2.frd_id='$id'";
    $result = execute_query($query, false);

    if ((isset($result['COUNT(*)'])) && ($result['COUNT(*)'] > 1)) {
        $friend = true;
    } else {
        $friend = false;
    }
    return $friend;
}

function get_member_info($mem) {

    $alertType = array();
    $rrslt = execute_query("SELECT is_facebook_user,mem_id,profilenam,photo_b_thumb,gender,profile_type FROM members WHERE mem_id='$mem'", true, "select");
    if (!empty($rrslt)) {
        $alertType['alertId'] = $rrslt[0]['mem_id'];
        $alertType['alerttitle'] = $rrslt[0]['profilenam'];
        $alertType['alertImageUrl'] = $rrslt[0]['photo_b_thumb'];
        $alertType['alertUserGender'] = $rrslt[0]['gender'];
        $alertType['alertUserProfileType'] = $rrslt[0]['profile_type'];
        $alertType['count'] = $rrslt['count'];
        return $alertType;
    }
}

function getalerts($alert) {

    $result = array();
    if (isset($alert['badges']) && !empty($alert['badges'])) {

        unset($alert['badges']['count']);
        $badge = array();
        $tmpbadge = 0;
        $i = 0;
        foreach ($alert['badges'] as $kk => $result1) {
            unset($result1['count']);
            $mem = $result1['mem_id'];
            $badge = get_member_info($mem);
            $badge['alertDate'] = isset($result1['DATE']) && ($result1['DATE']) ? strtotime($result1['DATE']) : NULL;
            $badge['alertDescription'] = $result1['alert_text'];
            $badge['alertType'] = "badge";
            $getBadgeUrl = $_SERVER['DOCUMENT_ROOT'] . "/MySNL_WebServiceV2Live/badges/Badges_icon/";

            if (is_dir($getBadgeUrl)) {
                if ($dh = opendir($getBadgeUrl)) {
                    $files1 = scandir($getBadgeUrl);
                    unset($files1[0], $files1[1]);
                    sort($files1);
                    $getMatch = array_search(trim($result1['badge_name']) . '.png', $files1);

                    if (is_int($getMatch)) {
                        $badge['badges']['badge_id'] = $result1['id'];
                        $badge['badges']['badge_name'] = $result1['badge_name'];
                        $badge['badges']['badge_description'] = $result1['public_hint_active'];
                        $badge['badges']['badgeEarned'] = 'yes';
                        $badge['badges']['venueId'] = $result1['venue_id'];
                        $badge['badges']['badgeUnlockedAtVenue'] = getname($result1['venue_id']);
                        $badge['badges']['badgeThumbImageURL'] = ROOT_URL . 'badges/Badges_icon/' . $files1[$getMatch];
                        $badge['badges']['badgeImageURL'] = ROOT_URL . 'badges/images-BlacknWhite/' . $files1[$getMatch];
                        // $badge['badges']['badgeUnlockedAtTime'] = date("l M. d,Y H:i A", $result1['DATE']);
                        $badge['badges']['badgeUnlockedAtTime'] = $result1['DATE'];
                        $badgesList[$i] = $badge; //print_r($badge);
                        unset($files1[$getMatch], $files1[$getMatch + 1]);
                    }
                }
                closedir($dh);
            }
            $result['badge'][$tmpbadge] = $badge;
            $tmpbadge++;
            $i++;
        }
    }

    if (isset($alert['tag']) && !empty($alert['tag'])) {
        unset($alert['tag']['count']);
        $tag = array();
        $tmpcnt = 0;
        $photo_num = 1;
        foreach ($alert['tag'] as $kk => $result1) {
            $mem = $result1['mem_id'];
            $tag = get_member_info($mem);
            $getAlbumId = "SELECT pa.photo_id,pa.album_id FROM photo_album as pa,fn_annotation_rows as far,tagged_photos as tp WHERE tp.annotation_id='{$result1['special']}' AND tp.annotation_id =far.id and far.image_id=pa.photo_id";
            $splUpdateId = execute_query($getAlbumId, false, "select");
            $tag['alertDate'] = isset($result1['date']) && ($result1['date']) ? $result1['date'] : NULL;
            $tag['alertsplId'] = isset($splUpdateId['photo_id']) && ($splUpdateId['photo_id']) ? $splUpdateId['photo_id'] : NULL;
            $tag['alertUpdateId'] = isset($splUpdateId['album_id']) && ($splUpdateId['album_id']) ? $splUpdateId['album_id'] : NULL;
            $tag['alertMainId'] = isset($result1['id']) && ($result1['id']) ? $result1['id'] : NULL;
            $tag['alertDescription'] = " has tagged you in (" . $photo_num . ") photo(s).";
            $tag['alertType'] = "tag"; //getname($result1['mem_id']) .
            $result['tag'][$tmpcnt] = $tag;
            $tmpcnt++;
        }
    }
    if (isset($alert['new_msg']) && !empty($alert['new_msg'])) {
        unset($alert['new_msg']['count']);
        $msg = array();
        $tmpcnt = 0;
        foreach ($alert['new_msg'] as $kk => $result1) {
            $mesId = $result1['mes_id'];
            $memId = $result1['mem_id'];
            $userInfo = get_member_info($result1['frm_id']);
            $msg['alertId'] = $userInfo['alertId'];
            $msg['alertDate'] = $result1['date'];
            $msg['alerttitle'] = $userInfo['alerttitle'];
            $msg['alertsplId'] = $mesId;
            $msg['alertImageUrl'] = $userInfo['alertImageUrl'];
            $msg['alertUserGender'] = $userInfo['alertUserGender'];
            $msg['alertUserProfileType'] = $userInfo['alertUserProfileType'];
            $msg['alertDescription'] = "New Message(s)"; //(" . $userInfo['count'] . ")
            $msg['alertType'] = "New Message";
            $result['msg'][$tmpcnt] = $msg;
            $tmpcnt++;
        }
    }

    if (isset($alert['network']) && !empty($alert['network'])) {
        unset($alert['network']['count']);
        $network = array();
        $tmpcnt = 0;
        foreach ($alert['network'] as $kk => $result1) {
            $mem = $result1['frm_id'];
            $network = get_member_info($mem);
            $network['alertDate'] = $result1['date'];
            $network['alertsplId'] = $result1['mes_id'];
            $network['alertDescription'] = "(" . $network['count'] . ")" . " Network Invitation(s)";
            $network['alertType'] = "Network";
            $result['network'][$tmpcnt] = $network;
            $tmpcnt++;
        }
    }

    if (isset($alert['reply_comment']) && !empty($alert['reply_comment'])) {
        unset($alert['reply_comment']['count']);
        $recm = array();
        $tmpcnt = 0;
        foreach ($alert['reply_comment'] as $kk => $result1) {
            $from = $result1['from_id'];
            $recm = get_member_info($from);
            $recm['alertDate'] = $result1['added'];
            $recm['alertsplId'] = $result1['tst_id'];
            if ($result1['parent_tst_id'] == 0) {
                //echo "okokok";
                $recm['alertDescription'] = "New Profile comment from " . $recm['alerttitle'];
                $recm['alertType'] = "New comment";
            } else {
                $recm['alertsplId'] = $result1['tst_id'];
                $recm['alertDescription'] = "Reply To Profile comment by " . $recm['alerttitle'];
                $recm['alertType'] = "Reply To Comment";
            }
            $result['recm'][$tmpcnt] = $recm;
            $tmpcnt++;
        }
    }

    if (isset($alert['reply_hotpress']) && !empty($alert['reply_hotpress'])) {
        unset($alert['reply_hotpress']['count']);
        $rehp = array();
        $tmpcnt = 0;
        foreach ($alert['reply_hotpress'] as $kk => $result1) {
            $from = $result1['mem_id'];
            $rehp = get_member_info($from);
            $rehp['alertDate'] = $result1['date'];
            $rehp['alertsplId'] = $result1['parentid'];
            $rehp['alertUpdateId'] = $result1['id'];
            //$getParentId = execute_query("SELECT body FROM bulletin WHERE id='" . $result1['parentid'] . "'", false, 'select');
            // $rehp['alertsplText'] = $getParentId;
            $rehp['alertDescription'] = "Reply To Hotpress From " . $rehp['alerttitle'];
            $rehp['alertType'] = "Reply To Hotpress";
            $result['rehp'][$tmpcnt] = $rehp;
            $tmpcnt++;
        }
    }



    if (isset($alert['photo_comments']) && !empty($alert['photo_comments'])) {
        unset($alert['photo_comments']['count']);
        $phcomm = array();
        $tmpcnt = 0;
        foreach ($alert['photo_comments'] as $kk => $result1) {
            $from = $result1['mem_id'];
            $phcomm = get_member_info($from);
            $phcomm['alertDate'] = $result1['date'];
            $phcomm['alertsplId'] = $result1['photo_id'];
            $phcomm['alertUpdateId'] = $result1['id'];
            $phcomm['alertsplImage'] = $result1['photo_small'];
            $phcomm['alertMainId'] = $result1['album_id'];
            $phcomm['alertDescription'] = "Photo commented by " . $phcomm['alerttitle'] . "";
            $phcomm['alertType'] = "photo comments";
            $result['phcomm'][$tmpcnt] = $phcomm;
            $tmpcnt++;
        }
    }

    if (isset($alert['reply_event_comments']) && !empty($alert['reply_event_comments'])) {
        unset($alert['reply_event_comments']['count']);
        $reecomm = array();
        $tmpcnt = 0;
        foreach ($alert['reply_event_comments'] as $kk => $result1) {
            $from = $result1['from_id'];
            $reecomm = get_member_info($from);
            $reecomm['event_profilename'] = $result1['profilenam'];
            $reecomm['alertDate'] = $result1['date'];
            $reecomm['alertsplId'] = $result1['even_id'];
            $reecomm['alertUpdateId'] = $result1['id']; //parent_id
            $reecomm['alertDescription'] = " replied to a event comment"; //$result1['profilenam'] .
            $reecomm['alertType'] = "reply event comments";
            $result['everecomm'][$tmpcnt] = $reecomm;
            $tmpcnt++;
        }
    }

    if (isset($alert['event_comments']) && !empty($alert['event_comments'])) {
        unset($alert['event_comments']['count']);
        $evcomm = array();
        $tmpcnt = 0;
        foreach ($alert['event_comments'] as $kk => $result1) {
            $from = $result1['from_id'];
            $evcomm = get_member_info($from);
            $evcomm['event_profilename'] = $result1['profilenam'];
            $evcomm['alertDate'] = $result1['date'];
            $evcomm['alertsplId'] = $result1['even_id'];
            $evcomm['alertUpdateId'] = $result1['id'];
            $evcomm['alertDescription'] = "New Event comment from " . $result1['profilenam'] . "";
            $evcomm['alertType'] = "event_comments";
            $result['evecomm'][$tmpcnt] = $evcomm;
            $tmpcnt++;
        }
    }

    if (isset($alert['announce_arrival']) && !empty($alert['announce_arrival'])) {
        unset($alert['announce_arrival']['count']);
        $annarr = array();
        $tmpcnt = 0;
        foreach ($alert['announce_arrival'] as $kk => $result1) {
            if (!empty($result1) && ($kk !== 'count')) {
                $from = $result1['user_id'];
                $annarr = get_member_info($from);
                $annarr['event_profilename'] = $result1['profilenam'];
                $annarr['alertDate'] = $result1['date'];
                $annarr['alertsplId'] = $result1['mem_id'];
                $annarr['alertUpdateId'] = $result1['special'];
                $annarr['alertMainId'] = $result1['mes_id'];
                $annarr['alertDescription'] = "You have new announce arrival from " . $result1['profilenam'] . "";
                $annarr['alertType'] = "announce_arrival";
                $result['evecomm'][$tmpcnt] = $annarr;
                $tmpcnt++;
            }
        }
    }

    if (isset($alert['tagged_entourage_list']) && !empty($alert['tagged_entourage_list'])) {
        unset($alert['tagged_entourage_list']['count']);
        $taggedEnt = array();
        $tmpcnt = 0;
        foreach ($alert['tagged_entourage_list'] as $kk => $result1) {
            $venueId = $result1['venue_id'];
            $usrId = $result1['user_id'];
            $entId = $result1['ent_id'];
            $taggedEvent = get_member_info($venueId);
            $taggedEnt = get_member_info($usrId);
            $taggedEnt['tagged_profilename'] = $result1['profilenam'];
            $date = $result1['date'];
            $time = $result1['time'];
            $timeElapsed = strtotime("$date $time");
            $taggedEnt['alertDate'] = $timeElapsed;
            $taggedEnt['alertsplId'] = $result1['id'];
            $taggedEnt['alertUpdateId'] = $result1['announce_id'];
            $taggedEnt['alertMainId'] = $venueId;
            $taggedEnt['alertDescription'] = " has tagged you in {$taggedEvent['alerttitle']}";
            $taggedEnt['alertType'] = "taggedEntourage"; //{$taggedEnt['alerttitle']}
            $result['taggedEntourage'][$tmpcnt] = $taggedEnt;
            $tmpcnt++;
        }
    }
    //     print_r($result);//die();

    return $result;
}

// end of getalerts()
/* dated 21_mar_2012
  function default_images($gender, $profiletype) {
  $img = ''; //echo "\n";echo $gender ;echo "\n";echo $profiletype;echo "\n";
  $profiletype = trim($profiletype);
  if (($profiletype == "C") || ($profiletype == "c")) {
  $img = "images/my-profile-img3-big.gif";
  return $img;
  } else {
  if (($gender == 'm') || ($gender == 'M')) {
  $img = "images/my-profile-img4-big.gif";
  return $img;
  } elseif (($gender == 'f') || ($gender == 'F')) {
  $img = "images/my-profile-img2-big.gif";
  return $img;
  } else {
  //$img = "images/my-profile-img1-big.gif";
  $img = "images/Placeholder_thumb.png";
  return $img;
  }
  }
  } */

function default_images($gender, $profiletype, $photo_thumb = NULL) {
    $img = ''; //echo "\n";echo $gender ;echo "\n";echo $profiletype;echo "\n";
    $profiletype = trim($profiletype);
    //    if ($photo_thumb != 'no') {
    //        $img = "images/'" . $photo_thumb . "'";
    //        return $img;
    //    } else {

    if (($profiletype == "C") || ($profiletype == "c")) {
        $img = "images/my-profile-img3-big.gif";
        return $img;
    } else {
        if (($gender == 'm') || ($gender == 'M')) {
            $img = "images/my-profile-img4-big.gif";
            return $img;
        } else {
            $img = "images/my-profile-img2-big.gif";
            return $img;
        }
        //        }
    }
}

function default_images1($gender, $profiletype, $photo_thumb = NULL) {
    $img = ''; //echo "\n";echo $gender ;echo "\n";echo $profiletype;echo "\n";
    $profiletype = trim($profiletype);
    //    if ($photo_thumb != 'no') {
    //        $img = "images/'" . $photo_thumb . "'";
    //        return $img;
    //    } else {

    if (($profiletype == "C") || ($profiletype == "c")) {
        $img = "images/my-profile-img3-big.gif";
        return $img;
    } else {
        if (($gender == 'm') || ($gender == 'M')) {
            $img = "images/my-profile-img4-big.gif";
            return $img;
        } else {
            $img = "images/my-profile-img2-big.gif";
            return $img;
        }
        //        }
    }
}

//
//if ($photo_thumb != 'no') {
//    $img = "images/'" . $photo_thumb . "'";
//    return $img;
//} else {
//    if (($profiletype == "C") || ($profiletype == "c")) {
//        $img = "images/my-profile-img3-big.gif";
//        return $img;
//    } else {
//        if (($gender == 'm') || ($gender == 'M')) {
//            $img = "images/my-profile-img4-big.gif";
//        } else {
//            $img = "images/my-profile-img2-big.gif";
//        }
//    }
//}

/*   Announce Arrival image on hotpress:  starts here  */
/*
  function appearance_hotpress_image($appearanceInfo=array()) {


  $font_3_width = imagefontwidth(4);
  $appearanceTextLength = strlen($appearanceInfo["appearanceText"]);
  $appearanceTextLengthPixel = $appearanceTextLength * $font_3_width;

  $eventAttendTextLength = strlen($appearanceInfo["eventAttendText"]);
  $eventAttendTextLengthPixel = ($eventAttendTextLength * $font_3_width);

  $waitInLineLengthPixel = (strlen("Wait in line:") * $font_3_width) + 20 + (strlen("Short") * $font_3_width) + 10 + 150 + (strlen("Long") * $font_3_width) + 10;
  $maxTextLength = max($appearanceTextLengthPixel, $eventAttendTextLengthPixel, $waitInLineLengthPixel);
  $canvasWidth = $maxTextLength + 180;
  $canvasHeight = 190;
  //320-252= 68;
  // Create a 200 x 200 image
  $canvas = imagecreate($canvasWidth, $canvasHeight);

  $background_color = imagecolorallocate($canvas, 255, 255, 255);

  $black = imagecolorallocate($canvas, 0, 0, 0);

  imagerectangle($canvas, 0, 0, ($canvasWidth - 1), ($canvasHeight - 1), $black);

  $white = imagecolorallocate($canvas, 255, 255, 255);
  $green = imagecolorallocate($canvas, 132, 135, 28);
  $textcolor = imagecolorallocate($canvas, 0, 0, 0);
  imagestring($canvas, 4, 10, 10, $appearanceInfo["appearanceText"], $textcolor);
  imagestring($canvas, 4, 10, 30, $appearanceInfo["eventAttendText"], $textcolor);
  imagestring($canvas, 4, 10, 50, $appearanceInfo["entourage"], $textcolor);


  imagestring($canvas, 4, 10, 75, "Wait in line:", $textcolor);

  $nextElementPos = (strlen("Wait in line:") * $font_3_width) + 20;
  imagestring($canvas, 4, $nextElementPos, 75, "Short", $textcolor);

  $waitInLineRectStart = $nextElementPos + (strlen("Short") * $font_3_width) + 10;
  $waitInLineRectEnd = $waitInLineRectStart + 150;

  imagerectangle($canvas, $waitInLineRectStart, 75, ($waitInLineRectEnd), 90, $black);

  $nextElementPos = ($waitInLineRectEnd) + 10;
  imagestring($canvas, 4, $nextElementPos, 75, "Long", $textcolor);

  //echo (($waitInLineRectEnd-$waitInLineRectStart)*$appearanceInfo["waitinline"]/100);
  $slider_X = (($waitInLineRectEnd - $waitInLineRectStart) * $appearanceInfo["waitinline"] / 100) + ($waitInLineRectStart - 5);
  imagefilledrectangle($canvas, $slider_X, 72, ($slider_X + 10), 92, $black);




  imagestring($canvas, 4, 10, 100, "Ratio: ", $textcolor);

  $nextElementPos = (strlen("Ratio: ") * $font_3_width) + 75;
  imagestring($canvas, 4, $nextElementPos, 100, "Guys", $textcolor);

  $ratioRectStart = $nextElementPos + (strlen("Guys") * $font_3_width) + 10;
  $ratioRectEnd = $ratioRectStart + 145;

  imagerectangle($canvas, $ratioRectStart, 100, ($ratioRectEnd), 115, $black);

  $nextElementPos = ($ratioRectEnd) + 8;
  imagestring($canvas, 4, $nextElementPos, 100, "Girls", $textcolor);

  //echo (($waitInLineRectEnd-$waitInLineRectStart)*$appearanceInfo["waitinline"]/100);
  $slider_X = (($ratioRectEnd - $ratioRectStart) * $appearanceInfo["ratio"] / 100) + ($ratioRectStart - 5);
  imagefilledrectangle($canvas, $slider_X, 98, ($slider_X + 10), 117, $black);


  imagestring($canvas, 4, 10, 125, "Music: ", $textcolor);

  $nextElementPos = (strlen("Music: ") * $font_3_width) + 62;
  imagestring($canvas, 4, $nextElementPos, 125, "So so", $textcolor);

  $musicRectStart = $nextElementPos + (strlen("So so") * $font_3_width) + 10;
  $musicRectEnd = $musicRectStart + 150;

  imagerectangle($canvas, $musicRectStart, 125, ($musicRectEnd), 140, $black);

  $nextElementPos = ($musicRectEnd) + 10;
  imagestring($canvas, 4, $nextElementPos, 125, "Amazing", $textcolor);

  //echo (($waitInLineRectEnd-$waitInLineRectStart)*$appearanceInfo["waitinline"]/100);
  $slider_X = (($musicRectEnd - $musicRectStart) * $appearanceInfo["music"] / 100) + ($musicRectStart - 5);
  imagefilledrectangle($canvas, $slider_X, 122, ($slider_X + 10), 142, $black);

  imagestring($canvas, 4, 10, 150, "Enegry: ", $textcolor);

  $nextElementPos = (strlen("Enegry: ") * $font_3_width) + 70;
  imagestring($canvas, 4, $nextElementPos, 150, "Low", $textcolor);

  $enegryRectStart = $nextElementPos + (strlen("Low") * $font_3_width) + 10;
  $enegryRectEnd = $enegryRectStart + 150;

  imagerectangle($canvas, $enegryRectStart, 150, ($enegryRectEnd), 165, $black);

  $nextElementPos = ($enegryRectEnd) + 10;
  imagestring($canvas, 4, $nextElementPos, 150, "High", $textcolor);

  //echo (($waitInLineRectEnd-$waitInLineRectStart)*$appearanceInfo["waitinline"]/100);
  $slider_X = (($enegryRectEnd - $enegryRectStart) * $appearanceInfo["enegry"] / 100) + ($enegryRectStart - 5);
  imagefilledrectangle($canvas, $slider_X, 148, ($slider_X + 10), 167, $black);


  // Output and free from memory
  //@header('Content-Type: image/gif');
  //imagejpeg($canvas,NULL,90);
  //@imagejpeg($canvas,$appearanceInfo["hotpress_image_filename"],90);
  //@imagegif($canvas,$appearanceInfo["hotpress_image_filename"]);
  //@header("Content-type:image/jpg");
  @imagejpeg($canvas, $appearanceInfo["hotpress_image_filename"], 90);
  @chmod($appearanceInfo["hotpress_image_filename"], 0777);
  return $appearanceInfo["hotpress_image_filename"];
  }
 */

function appearance_hotpress_image($appearanceInfo=array()) {


    $font_3_width = imagefontwidth(3);
    $font_number = 3;
    $appearanceTextLength = strlen($appearanceInfo["appearanceText"]);
    $appearanceTextLengthPixel = $appearanceTextLength * $font_3_width;

    $eventAttendTextLength = strlen($appearanceInfo["eventAttendText"]);
    $eventAttendTextLengthPixel = ($eventAttendTextLength * $font_3_width);

    $waitInLineLengthPixel = (strlen("Wait in line:") * $font_3_width) + 20 + (strlen("Short") * $font_3_width) + 10 + 150 + (strlen("Long") * $font_3_width) + 10;
    $maxTextLength = max($appearanceTextLengthPixel, $eventAttendTextLengthPixel, $waitInLineLengthPixel);
    $canvasWidth = $maxTextLength + 10;
    //180
    $canvasHeight = 70; //70

    if (!empty($appearanceInfo["waitinline"]))
        $canvasHeight+=26;

    if (!empty($appearanceInfo["ratio"]))
        $canvasHeight+=26;

    if (!empty($appearanceInfo["music"]))
        $canvasHeight+=26;

    if (!empty($appearanceInfo["enegry"]))
        $canvasHeight+=26;

    //320-252= 68;
    // Create a 200 x 200 image
    $canvas = imagecreate($canvasWidth, $canvasHeight);
    $background_color = imagecolorallocate($canvas, 255, 255, 255);
    $radius = 10;
    $sliderradius = 5;
    $black = imagecolorallocate($canvas, 0, 0, 0);

    //imagerectangle($canvas, 0, 0, ($canvasWidth-1),($canvasHeight-1), $black);
    draw_roundrectangle($canvas, 0, 0, ($canvasWidth - 1), ($canvasHeight - 1), $radius, $black, $filled = 0);

    $white = imagecolorallocate($canvas, 255, 255, 255);
    $green = imagecolorallocate($canvas, 132, 135, 28);
    $red = imagecolorallocate($canvas, 255, 0, 0);
    $palewhite = imagecolorallocate($canvas, 120, 120, 120);
    $grey = imagecolorallocate($canvas, 115, 113, 115);

    $textcolor = imagecolorallocate($canvas, 0, 0, 0);
    imagestring($canvas, $font_number, 10, 10, $appearanceInfo["appearanceText"], $textcolor);
    imagestring($canvas, $font_number, 10, 30, $appearanceInfo["eventAttendText"], $textcolor);
    imagestring($canvas, $font_number, 10, 50, $appearanceInfo["entourage"], $textcolor);

    $RectangleY1 = 75;

    $RectangleY2 = 90;

    /* Wait in Line Slider :: start here */
    if (!empty($appearanceInfo["waitinline"])) {
        imagestring($canvas, $font_number, 10, $RectangleY1, "Wait in line:", $textcolor);

        $nextElementPos = (strlen("Wait in line:") * $font_3_width) + 20;
        imagestring($canvas, $font_number, $nextElementPos, $RectangleY1, "Short", $textcolor);

        $waitInLineRectStart = $nextElementPos + (strlen("Short") * $font_3_width) + 10;
        $waitInLineRectEnd = $waitInLineRectStart + 150;

        //imagerectangle($canvas, $waitInLineRectStart, $RectangleY1,($waitInLineRectEnd), $RectangleY2, $black);
        draw_roundrectangle($canvas, $waitInLineRectStart, $RectangleY1, ($waitInLineRectEnd), $RectangleY2, $sliderradius, $black, $filled = 0);

        $nextElementPos = ($waitInLineRectEnd) + 10;
        imagestring($canvas, $font_number, $nextElementPos, $RectangleY1, "Long", $textcolor);

        //echo (($waitInLineRectEnd-$waitInLineRectStart)*$appearanceInfo["waitinline"]/100);
        $slider_X = (($waitInLineRectEnd - $waitInLineRectStart) * $appearanceInfo["waitinline"] / 100) + ($waitInLineRectStart - 5);

        draw_roundrectangle($canvas, $waitInLineRectStart, $RectangleY1, ($slider_X + 3), $RectangleY2, $sliderradius - 2, $red, $filled = 1);
        //imagefilledrectangle($canvas, $slider_X, ($RectangleY1-2), ($slider_X+10), ($RectangleY2+2), $black);
        draw_roundrectangle($canvas, $slider_X, ($RectangleY1 - 2), ($slider_X + 8), ($RectangleY2 + 2), $sliderradius - 4, $black, $filled = 1);
        $RectangleY1+=25;

        $RectangleY2+=25;
    } //End of if(!empty($appearanceInfo["waitinline"]))

    /* Wait in Line Slider :: end here */


    /* Ratio Slider :: start here */
    if (!empty($appearanceInfo["ratio"])) {
        imagestring($canvas, $font_number, 10, $RectangleY1, "Ratio: ", $textcolor);

        $nextElementPos = (strlen("Ratio: ") * $font_3_width) + 20;
        imagestring($canvas, $font_number, $nextElementPos, $RectangleY1, "Guys", $textcolor);

        $ratioRectStart = $nextElementPos + (strlen("Guys") * $font_3_width) + 10;
        $ratioRectEnd = $ratioRectStart + 150;

        //imagerectangle($canvas, $ratioRectStart, $RectangleY1,($ratioRectEnd), $RectangleY2, $black);
        draw_roundrectangle($canvas, $ratioRectStart, $RectangleY1, ($ratioRectEnd), $RectangleY2, $sliderradius, $black, $filled = 0);

        $nextElementPos = ($ratioRectEnd) + 10;
        imagestring($canvas, $font_number, $nextElementPos, $RectangleY1, "Girls", $textcolor);

        //echo (($waitInLineRectEnd-$waitInLineRectStart)*$appearanceInfo["waitinline"]/100);
        $slider_X = (($ratioRectEnd - $ratioRectStart) * $appearanceInfo["ratio"] / 100) + ($ratioRectStart - 5);

        draw_roundrectangle($canvas, $ratioRectStart, $RectangleY1, ($slider_X + 3), $RectangleY2, $sliderradius - 2, $red, $filled = 1);
        //imagefilledrectangle($canvas, $slider_X, ($RectangleY1-2), ($slider_X+10), ($RectangleY2+2), $black);
        draw_roundrectangle($canvas, $slider_X, ($RectangleY1 - 2), ($slider_X + 8), ($RectangleY2 + 2), $sliderradius - 4, $black, $filled = 1);

        $RectangleY1+=25;

        $RectangleY2+=25;
    } //End of if(!empty($appearanceInfo["ratio"]))
    /* Ratio Slider :: end here */


    /* Music Slider :: start here */
    if (!empty($appearanceInfo["music"])) {
        imagestring($canvas, $font_number, 10, $RectangleY1, "Music: ", $textcolor);

        $nextElementPos = (strlen("Music: ") * $font_3_width) + 20;
        imagestring($canvas, $font_number, $nextElementPos, $RectangleY1, "So so", $textcolor);

        $musicRectStart = $nextElementPos + (strlen("So so") * $font_3_width) + 10;
        $musicRectEnd = $musicRectStart + 150;

        //imagerectangle($canvas, $musicRectStart, $RectangleY1,($musicRectEnd), $RectangleY2, $black);
        draw_roundrectangle($canvas, $musicRectStart, $RectangleY1, ($musicRectEnd), $RectangleY2, $sliderradius, $black, $filled = 0);

        $nextElementPos = ($musicRectEnd) + 10;
        imagestring($canvas, $font_number, $nextElementPos, $RectangleY1, "Amazing", $textcolor);


        //echo (($waitInLineRectEnd-$waitInLineRectStart)*$appearanceInfo["waitinline"]/100);
        $slider_X = (($musicRectEnd - $musicRectStart) * $appearanceInfo["music"] / 100) + ($musicRectStart - 5);

        draw_roundrectangle($canvas, $musicRectStart, $RectangleY1, ($slider_X + 3), $RectangleY2, $sliderradius - 2, $red, $filled = 1);

        //imagefilledrectangle($canvas, $slider_X, ($RectangleY1-2), ($slider_X+10), ($RectangleY2+2), $black);
        draw_roundrectangle($canvas, $slider_X, ($RectangleY1 - 2), ($slider_X + 8), ($RectangleY2 + 2), $sliderradius - 4, $black, $filled = 1);

        $RectangleY1+=25;

        $RectangleY2+=25;
    } //End of if(!empty($appearanceInfo["music"]))

    /* Music Slider :: end here */

    /* Enegry Slider :: start here */
    if (!empty($appearanceInfo["enegry"])) {
        imagestring($canvas, $font_number, 10, $RectangleY1, "Enegry: ", $textcolor);


        $nextElementPos = (strlen("Enegry: ") * $font_3_width) + 20;
        imagestring($canvas, $font_number, $nextElementPos, $RectangleY1, "Low", $textcolor);

        $enegryRectStart = $nextElementPos + (strlen("Low") * $font_3_width) + 10;
        $enegryRectEnd = $enegryRectStart + 150;

        //imagerectangle($canvas, $enegryRectStart, $RectangleY1,($enegryRectEnd), $RectangleY2, $black);
        draw_roundrectangle($canvas, $enegryRectStart, $RectangleY1, ($enegryRectEnd), $RectangleY2, $sliderradius, $black, $filled = 0);

        $nextElementPos = ($enegryRectEnd) + 10;
        imagestring($canvas, $font_number, $nextElementPos, $RectangleY1, "High", $textcolor);

        //echo (($waitInLineRectEnd-$waitInLineRectStart)*$appearanceInfo["waitinline"]/100);
        $slider_X = (($enegryRectEnd - $enegryRectStart) * $appearanceInfo["enegry"] / 100) + ($enegryRectStart - 5);

        draw_roundrectangle($canvas, $enegryRectStart, $RectangleY1, ($slider_X + 3), $RectangleY2, $sliderradius - 2, $red, $filled = 1);

        //imagefilledrectangle($canvas, $slider_X, ($RectangleY1-2), ($slider_X+10), ($RectangleY2+2), $black);
        draw_roundrectangle($canvas, $slider_X, ($RectangleY1 - 2), ($slider_X + 8), ($RectangleY2 + 2), $sliderradius - 4, $black, $filled = 1);
    } //End of if(!empty($appearanceInfo["enegry"]))
    /* Enegry Slider :: end here */

    // Output and free from memory
    //@header('Content-Type: image/gif');
    //imagejpeg($canvas,NULL,90);
    //@imagejpeg($canvas,$appearanceInfo["hotpress_image_filename"],90);
    //@imagegif($canvas,$appearanceInfo["hotpress_image_filename"]);
    //@header("Content-type:image/jpg");
    //imagejpeg($canvas,NULL,90);
    @imagejpeg($canvas, $appearanceInfo["hotpress_image_filename"], 90);
    @chmod($appearanceInfo["hotpress_image_filename"], 0777);
    return $appearanceInfo["hotpress_image_filename"];
}

function appearance_hotpress_image_new($appearanceInfo=array()) {
    $font_3_width = imagefontwidth(3);
    $font_number = 3;
    $appearanceTextLength = strlen($appearanceInfo["appearanceText"]);
    $appearanceTextLengthPixel = $appearanceTextLength * $font_3_width;

    $eventAttendTextLength = strlen($appearanceInfo["eventAttendText"]);
    $eventAttendTextLengthPixel = ($eventAttendTextLength * $font_3_width);

    $waitInLineLengthPixel = (strlen("Wait in line:") * $font_3_width) + 20 + (strlen("Short") * $font_3_width) + 10 + 150 + (strlen("Long") * $font_3_width) + 10;
    $maxTextLength = max($appearanceTextLengthPixel, $eventAttendTextLengthPixel, $waitInLineLengthPixel);
    $canvasWidth = $maxTextLength + 10;
    //180
    //$canvasHeight = 70; //70
    $canvasHeight = 0;
    if (!empty($appearanceInfo["waitinline"]))
        $canvasHeight+=26;

    if (!empty($appearanceInfo["ratio"]))
        $canvasHeight+=26;

    if (!empty($appearanceInfo["music"]))
        $canvasHeight+=26;

    if (!empty($appearanceInfo["enegry"]))
        $canvasHeight+=26;

    //320-252= 68;
    // Create a 200 x 200 image
    $canvas = imagecreate($canvasWidth, $canvasHeight);
    $background_color = imagecolorallocate($canvas, 255, 255, 255);
    $radius = 10;
    $sliderradius = 5;
    $black = imagecolorallocate($canvas, 0, 0, 0);
    $grey = imagecolorallocate($canvas, 115, 113, 115);
    //imagerectangle($canvas, 0, 0, ($canvasWidth-1),($canvasHeight-1), $black);
    draw_roundrectangle($canvas, 0, 0, ($canvasWidth - 1), ($canvasHeight - 1), $radius, $grey, $filled = 1);

    $white = imagecolorallocate($canvas, 255, 255, 255);
    $green = imagecolorallocate($canvas, 132, 135, 28);
    $red = imagecolorallocate($canvas, 255, 0, 0);
    $palewhite = imagecolorallocate($canvas, 120, 120, 120);

    //draw_roundrectangle($canvas, 0, 0, ($canvasWidth - 1), ($canvasHeight - 1)/2.5, $radius, $grey, $filled = 1);
    $textcolor = imagecolorallocate($canvas, 0, 0, 0);
    /* imagestring($canvas, $font_number, 10, 10, $appearanceInfo["appearanceText"], $textcolor);
      imagestring($canvas, $font_number, 10, 30, $appearanceInfo["eventAttendText"], $textcolor);
      imagestring($canvas, $font_number, 10, 50, $appearanceInfo["entourage"], $textcolor); */

    //$RectangleY1 = 75;
    //$RectangleY2 = 90;
    $RectangleY1 = 5;
    $RectangleY2 = 20;
    /* Wait in Line Slider :: start here */
    if (!empty($appearanceInfo["waitinline"])) {
        imagestring($canvas, $font_number, 10, $RectangleY1, "Wait in line:", $white);

        $nextElementPos = (strlen("Wait in line:") * $font_3_width) + 15;
        imagestring($canvas, 2, $nextElementPos, $RectangleY1, "Short", $white);

        $waitInLineRectStart = $nextElementPos + (strlen("Short") * $font_3_width) + 10;
        $waitInLineRectEnd = $waitInLineRectStart + 150;


        draw_roundrectangle($canvas, $waitInLineRectStart, $RectangleY1, ($waitInLineRectEnd), $RectangleY2, $sliderradius, $white, $filled = 1);

        $nextElementPos = ($waitInLineRectEnd) + 10;
        imagestring($canvas, 2, $nextElementPos, $RectangleY1, "Long", $white);


        $slider_X = (($waitInLineRectEnd - $waitInLineRectStart) * $appearanceInfo["waitinline"] / 100) + ($waitInLineRectStart - 5);

        draw_roundrectangle($canvas, $waitInLineRectStart, $RectangleY1, ($slider_X + 3), $RectangleY2, $sliderradius - 2, $red, $filled = 1);

        draw_roundrectangle($canvas, $slider_X, ($RectangleY1 - 2), ($slider_X + 6), ($RectangleY2 + 2), $sliderradius - 4, $black, $filled = 1);
        $RectangleY1+=25;

        $RectangleY2+=25;
    }  //End of if(!empty($appearanceInfo["waitinline"]))

    /* Wait in Line Slider :: end here */


    /* Ratio Slider :: start here */
    if (!empty($appearanceInfo["ratio"])) {
        imagestring($canvas, $font_number, 10, $RectangleY1, "Ratio: ", $white);

        $nextElementPos = (strlen("Ratio: ") * $font_3_width) + 63;
        imagestring($canvas, 2, $nextElementPos, $RectangleY1, "Guys", $white);

        $ratioRectStart = $nextElementPos + (strlen("Guys") * $font_3_width) + 10;
        $ratioRectEnd = $ratioRectStart + 150;


        draw_roundrectangle($canvas, $ratioRectStart, $RectangleY1, ($ratioRectEnd), $RectangleY2, $sliderradius, $white, $filled = 1);

        $nextElementPos = ($ratioRectEnd) + 10;
        imagestring($canvas, 2, $nextElementPos, $RectangleY1, "Girls", $white);


        $slider_X = (($ratioRectEnd - $ratioRectStart) * $appearanceInfo["ratio"] / 100) + ($ratioRectStart - 5);

        draw_roundrectangle($canvas, $ratioRectStart, $RectangleY1, ($slider_X + 3), $RectangleY2, $sliderradius - 2, $red, $filled = 1);

        draw_roundrectangle($canvas, $slider_X, ($RectangleY1 - 2), ($slider_X + 6), ($RectangleY2 + 2), $sliderradius - 4, $black, $filled = 1);

        $RectangleY1+=25;

        $RectangleY2+=25;
    }  //End of if(!empty($appearanceInfo["ratio"]))
    /* Ratio Slider :: end here */


    /* Music Slider :: start here */
    if (!empty($appearanceInfo["music"])) {
        imagestring($canvas, $font_number, 10, $RectangleY1, "Music: ", $white);

        $nextElementPos = (strlen("Music: ") * $font_3_width) + 55;
        imagestring($canvas, 2, $nextElementPos, $RectangleY1, "So so", $white);

        $musicRectStart = $nextElementPos + (strlen("So so") * $font_3_width) + 10;
        $musicRectEnd = $musicRectStart + 150;


        draw_roundrectangle($canvas, $musicRectStart, $RectangleY1, ($musicRectEnd), $RectangleY2, $sliderradius, $white, $filled = 1);

        $nextElementPos = ($musicRectEnd) + 10;
        imagestring($canvas, 2, $nextElementPos, $RectangleY1, "Amazing", $white);



        $slider_X = (($musicRectEnd - $musicRectStart) * $appearanceInfo["music"] / 100) + ($musicRectStart - 5);

        draw_roundrectangle($canvas, $musicRectStart, $RectangleY1, ($slider_X + 3), $RectangleY2, $sliderradius - 2, $red, $filled = 1);


        draw_roundrectangle($canvas, $slider_X, ($RectangleY1 - 2), ($slider_X + 6), ($RectangleY2 + 2), $sliderradius - 4, $black, $filled = 1);

        $RectangleY1+=25;

        $RectangleY2+=25;
    } //End of if(!empty($appearanceInfo["music"]))

    /* Music Slider :: end here */

    /* Enegry Slider :: start here */
    if (!empty($appearanceInfo["enegry"])) {
        imagestring($canvas, $font_number, 10, $RectangleY1, "Enegry: ", $white);


        $nextElementPos = (strlen("Enegry: ") * $font_3_width) + 62;
        imagestring($canvas, 2, $nextElementPos, $RectangleY1, "Low", $white);

        $enegryRectStart = $nextElementPos + (strlen("Low") * $font_3_width) + 10;
        $enegryRectEnd = $enegryRectStart + 150;


        draw_roundrectangle($canvas, $enegryRectStart, $RectangleY1, ($enegryRectEnd), $RectangleY2, $sliderradius, $white, $filled = 1);

        $nextElementPos = ($enegryRectEnd) + 10;
        imagestring($canvas, 2, $nextElementPos, $RectangleY1, "High", $white);


        $slider_X = (($enegryRectEnd - $enegryRectStart) * $appearanceInfo["enegry"] / 100) + ($enegryRectStart - 5);

        draw_roundrectangle($canvas, $enegryRectStart, $RectangleY1, ($slider_X + 3), $RectangleY2, $sliderradius - 2, $red, $filled = 1);


        draw_roundrectangle($canvas, $slider_X, ($RectangleY1 - 2), ($slider_X + 6), ($RectangleY2 + 2), $sliderradius - 4, $black, $filled = 1);
    } //End of if(!empty($appearanceInfo["enegry"]))
    /* Enegry Slider :: end here */

    // Output and free from memory
    //@header('Content-Type: image/gif');
    //imagejpeg($canvas,NULL,90);
    //@imagejpeg($canvas,$appearanceInfo["hotpress_image_filename"],90);
    //@imagegif($canvas,$appearanceInfo["hotpress_image_filename"]);
    //@header("Content-type:image/jpg");
    //imagejpeg($canvas,NULL,90);
    @imagejpeg($canvas, $appearanceInfo["hotpress_image_filename"], 90);
    @chmod($appearanceInfo["hotpress_image_filename"], 0777);
    return $appearanceInfo["hotpress_image_filename"];
}

function draw_roundrectangle($img, $x1, $y1, $x2, $y2, $radius, $color, $filled=1) {
    if ($filled == 1) {
        imagefilledrectangle($img, $x1 + $radius, $y1, $x2 - $radius, $y2, $color);
        imagefilledrectangle($img, $x1, $y1 + $radius, $x1 + $radius - 1, $y2 - $radius, $color);
        imagefilledrectangle($img, $x2 - $radius + 1, $y1 + $radius, $x2, $y2 - $radius, $color);

        imagefilledarc($img, $x1 + $radius, $y1 + $radius, $radius * 2, $radius * 2, 180, 270, $color, IMG_ARC_PIE);
        imagefilledarc($img, $x2 - $radius, $y1 + $radius, $radius * 2, $radius * 2, 270, 360, $color, IMG_ARC_PIE);
        imagefilledarc($img, $x1 + $radius, $y2 - $radius, $radius * 2, $radius * 2, 90, 180, $color, IMG_ARC_PIE);
        imagefilledarc($img, $x2 - $radius, $y2 - $radius, $radius * 2, $radius * 2, 360, 90, $color, IMG_ARC_PIE);
    } else {
        imageline($img, $x1 + $radius, $y1, $x2 - $radius, $y1, $color);
        imageline($img, $x1 + $radius, $y2, $x2 - $radius, $y2, $color);
        imageline($img, $x1, $y1 + $radius, $x1, $y2 - $radius, $color);
        imageline($img, $x2, $y1 + $radius, $x2, $y2 - $radius, $color);

        imagearc($img, $x1 + $radius, $y1 + $radius, $radius * 2, $radius * 2, 180, 270, $color);
        imagearc($img, $x2 - $radius, $y1 + $radius, $radius * 2, $radius * 2, 270, 360, $color);
        imagearc($img, $x1 + $radius, $y2 - $radius, $radius * 2, $radius * 2, 90, 180, $color);
        imagearc($img, $x2 - $radius, $y2 - $radius, $radius * 2, $radius * 2, 360, 90, $color);
    }
}

/*   Announce Arrival image on hotpress:  ends here  */

function extract_url($text) {
    $text = preg_replace("/www\./", "http://www.", $text);
    // eliminate duplicates after force
    $text = preg_replace("-http://http://www\.-", "http://www.", $text);
    $text = preg_replace("-https://http://www\.-", "https://www.", $text);


    // The Regular Expression filter
    $reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*?([^>\s]*))/i";
    // Check if there is a url in the text
    if (preg_match($reg_exUrl, $text, $url)) {
        // make the urls hyper links
        $text = preg_replace($reg_exUrl, $url[0], $text);
    }    // if no urls in the text just return the text

    if (!empty($url[0]))
        return ($url[0]);
    else
        return ($text);
}

function subanchor($url) {

    $match = extract_url($url);

    if (!$match) {
        return $url;
    }
    $url = preg_replace("#<a\s*[^>]*href=\"(.*)\".*>(.*)</a>#i", "\\n\\n\\n", $url);
    $url = preg_replace("/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/", "\\n\\n\\n", $url);
    return $url; //str_replace('&url', '', $str); //' &url ' . $match . ' &url '
}

function subanchor_hotpress($url) {

    $match = extract_url($url);

    if (!$match) {
        return $url;
    }
    $url = preg_replace("#<a\s*[^>]*href=\"(.*)\".*>(.*)</a>\s*#i", "\n\n\n", $url);
    $url = preg_replace("/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/", "\n\n\n", $url);
    return $url; //str_replace('&url', '', $str); //' &url ' . $match . ' &url '
}

function url_info($linkurl) {
    $link_arr = array();
    $pos = strpos($linkurl, "http");
    if ($pos === false) {
        $wwwpos = strpos($linkurl, "www");
        if ($wwwpos === false)
            $linkurl = "http://www." . $linkurl;
        else
            $linkurl = "http://" . $linkurl;
    }
    $link_arr['url'] = $linkurl;


    $youtubepos = strpos($linkurl, "youtube");

    $watchpos = strpos($linkurl, "watch");

    if ($youtubepos === false && $watchpos === false) {
        //if Url is not a youtube video link
        try {
            $ch = curl_init() or die(curl_error());
            curl_setopt($ch, CURLOPT_URL, $linkurl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $data1 = curl_exec($ch) or die(exit);
        } catch (Exception $e) {
            echo "0";
        }

        $dom = new DOMDocument();
        @$dom->loadHTML($data1);
        //var_dump($data1);
        $meta = $dom->getElementsByTagName("meta");
        foreach ($meta as $node) {
            //echo $node->getAttribute('name');
            if ($node->getAttribute('name') == "Description") {
                $description = $node->getAttribute('content');
            }

            $link_arr['description'] = $description;
        }
        $title = $dom->getElementsByTagName("title");
        foreach ($title as $node) {
            $page_title = $node->textContent;
        }
        $link_arr['title'] = $page_title;
        $img = $dom->getElementsByTagName("img");
        $img_src_arr = array();

        foreach ($img as $node) {
            $img_src = $node->getAttribute('src');
            if ($img_src != '')
                array_push($img_src_arr, $img_src);
        }
        $img_src_arr = array_reverse(array_unique($img_src_arr));
        $linkurl = formatURL($linkurl);
        $imgCount = count($img_src_arr);

        for ($i = 0; $i < $imgCount; $i++) {

            $img_src_arr[$i] = getImageUrl($linkurl, $img_src_arr[$i]);
        }
        $link_arr['image'] = $img_src_arr[0];
        curl_close($ch);
        //echo "<pre>"; print_r($_POST); print_r($img_src_arr);
    } else {
        //if URL is a YouTube Video Link
        //$debug= "PHP_URL_QUERY:: ".PHP_URL_QUERY;
        //$debug.= "<BR>linkurl:: ".$linkurl;
        $urlParts = parse_url($linkurl, PHP_URL_QUERY);
        $urlParts = html_entity_decode($urlParts);
        $urlParts = explode('&', $urlParts);
        $arr = array();

        foreach ($urlParts as $val) {
            $x = explode('=', $val);
            //$debug.= "<BR>urlParts x :: ".$x;
            $arr[$x[0]] = $x[1];
        }
        $youtubeVideoId = $arr['v'];
        $img_src_arr[0] = "http://img.youtube.com/vi/$youtubeVideoId/0.jpg";
        //$debug.= "<BR>youtubeVideoId:: ".$youtubeVideoId;
        $link_arr['image'] = $img_src_arr[0];
        try {
            $youtubeLinkUrl = "http://gdata.youtube.com/feeds/api/videos/$youtubeVideoId";
            //$youtubeLinkUrl=  str_replace("\n"," ", $youtubeLinkUrl);
            $ch = curl_init() or die(curl_error());
            curl_setopt($ch, CURLOPT_URL, $youtubeLinkUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            // curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            $data1 = curl_exec($ch) or die(exit);
        } catch (Exception $e) {
            echo "0";
        }

        $dom = new DOMDocument();
        //$debug.= "<BR>data1:: ".$data1;



        @$dom->loadHTML($data1);

        /*
          $content 		= 		$dom->getElementsByTagName( "content" );
          foreach($content as $node){
          if($node->getAttribute('type')=="text"){
          $description = $node->textContent;

          if(strlen($description)>200){
          $description= substr($description,0,200)."...";
          }
          }
          }
         */
        $description = '';
        $title = $dom->getElementsByTagName("title");
        foreach ($title as $node) {
            $page_title = $node->textContent;
        }
        $link_arr['title'] = $page_title;
        $link_arr['description'] = NULL;
        //echo "<input type=\"hidden\" name=\"youtubeVideoId\" id=\"youtubeVideoId\" value=\"$youtubeVideoId\">";
        //echo $debug;
        return $link_arr;
    }
}

function formatURL($url) {

    $urlParts = parse_url($url);
    //echo "<pre>"; print_r($urlParts);
    if ($urlParts['scheme'] == "")
        $urlParts['scheme'] = "http";
    if ($urlParts['host'] == '')
        return '';
    $baseUrl = $urlParts['scheme'] . "://" . $urlParts['host'];

    if (isset($urlParts['path'])) {
        $dotPosition = strpos($urlParts['path'], '.');
        if ($dotPosition) {
            $lastSlash = strrpos($urlParts['path'], '/');
            $urlpart = substr($urlParts['path'], 0, $lastSlash);
            $baseUrl = $baseUrl . $urlpart;
        }
        else
            $baseUrl = $baseUrl . $urlParts['path'];
    }
    //echo strrpos($baseUrl,'/')."---".strlen($baseUrl)."<br>";
    if (strrpos($baseUrl, '/') + 1 < strlen($baseUrl)) {
        $baseUrl.="/";
    }
    return $baseUrl;
}

function getImageUrl($baseUrl, $imageUrl) {
    $slashPosition = strpos($imageUrl, 'ttp://');
    if ($slashPosition == 1)
        return $imageUrl;
    $slashPosition = strpos($imageUrl, './');
    if ($slashPosition == 1) {
        $lastSlash = strrpos($baseUrl, '/');
        if ($lastSlash + 1 == strlen($baseUrl)) {
            $tempBaseUrl = substr($baseUrl, 0, $lastSlash);
            $lastSlash = strrpos($tempBaseUrl, '/');
        }
        $tempBaseUrl = substr($baseUrl, 0, $lastSlash);

        $tempImageUrl = substr($imageUrl, 3, strlen($imageUrl));
        return getImageUrl($tempBaseUrl, $tempImageUrl);
    } else {
        return "$baseUrl" . "$imageUrl";
    }
}

function is_url($text) {
    $text = ereg_replace("www\.", "http://www.", $text);
    // eliminate duplicates after force
    $text = ereg_replace("http://http://www\.", "http://www.", $text);
    $text = ereg_replace("https://http://www\.", "https://www.", $text);

    // The Regular Expression filter
    $reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
    // Check if there is a url in the text
    if (preg_match($reg_exUrl, $text, $url)) {
        // make the urls hyper links
        $text = preg_replace($reg_exUrl, $url[0], $text);
    }    // if no urls in the text just return the text

    if (isset($url[0]) && ($url[0]))
        return true;
    else
        return false;
}

function getDistanceBetweenPointsNew($latitude1, $longitude1, $latitude2, $longitude2, $unit = 'Mi') {
    $theta = $longitude1 - $longitude2;
    $distance = (sin(deg2rad($latitude1)) * sin(deg2rad($latitude2))) + (cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta)));
    $distance = acos($distance);
    $distance = rad2deg($distance);
    $distance = $distance * 60 * 1.1515;
    switch ($unit) {
        case 'Mi': break;
        case 'Km' : $distance = $distance * 1.609344;
    }
    return (round($distance, 2));
}

function user_privacy_settings($userId) {
    $query = "SELECT privacy FROM members WHERE mem_id='$userId'";
    $result = execute_query($query, false, "select");
    return $result['privacy'];
}

function thumbanail_for_image($Id, $newfilename, $size=NULL) {

    $file_extension = substr($newfilename, strrpos($newfilename, '.') + 1);
    $arr = explode('.', $newfilename);

    //$folder = explode('/', $newfilename);. $folder[0]
    //$path = "../development/";

    $thumb1 = LOCAL_FOLDER . $arr[0] . "_" . $Id . "." . $file_extension;
    $thumb2 = LOCAL_FOLDER . $arr[0] . "_" . $Id . "b" . "." . $file_extension;
    //$file_extension = substr($newfilename, strrpos($newfilename, '.') + 1);

    $old = LOCAL_FOLDER . $newfilename;

    $newfilename = LOCAL_FOLDER . $newfilename;

    $srcImage = ""; //@imageCreateFromJPEG($old);
    // $rand = rand(0, 10000);
    //$newname_th = $path . $newfilename . "_" . $userId;
    //$thumb1 = $newname_th . "." . $file_extension;

    $sizee = @getimagesize($newfilename);

    switch ($sizee['mime']) {
        case "image/jpeg" :
            $srcImage = imagecreatefromjpeg($old);
            break;
        case "image/png":
            $srcImage = imagecreatefrompng($old);
            break;
        case "image/gif":
            $srcImage = imagecreatefromgif($old);
            break;
    }


    $srcwidth = $sizee[0];
    $srcheight = $sizee[1];

    //landscape
    if ($srcwidth > $srcheight || $srcwidth < $srcheight) {
        $destwidth1 = 65;
        $rat = $destwidth1 / $srcwidth;
        $destheight1 = (int) ($srcheight * $rat);
        //        $destwidth2 = 150;
        //        $rat2 = $destwidth2 / $srcwidth;
        //        $destheight2 = (int) ($srcheight * $rat2);
    }
    //portrait
    /*  elseif ($srcwidth < $srcheight) {
      $destheight1 = 100;
      $rat = $destheight1 / $srcheight;
      $destwidth1 = (int) ($srcwidth * $rat);
      } */
    //quadro
    elseif ($srcwidth == $srcheight) {
        $destwidth1 = 65;
        $destheight1 = 65;
    }

    if ($srcwidth > $srcheight || $srcwidth < $srcheight) {
        $destwidth2 = 300;
        $rat = $destwidth2 / $srcwidth;
        $destheight2 = (int) ($srcheight * $rat);
        //        $destwidth2 = 250;
        //        $rat2 = $destwidth2 / $srcwidth;
        //        $destheight2 = (int) ($srcheight * $rat2);
    }
    //portrait
    /* elseif ($srcwidth < $srcheight) {
      $destheight2 = 300;
      $rat = $destheight2 / $srcheight;
      $destwidth2 = (int) ($srcwidth * $rat);
      } */
    //quadro
    elseif ($srcwidth == $srcheight) {
        $destwidth2 = 300;
        $destheight2 = 300;
    }

    $destImage1 = @imagecreatetruecolor($destwidth1, $destheight1);
    $destImage2 = @imagecreatetruecolor($destwidth2, $destheight2);

    @imagecopyresampled($destImage1, $srcImage, 0, 0, 0, 0, $destwidth1, $destheight1, $srcwidth, $srcheight);
    @imagecopyresampled($destImage2, $srcImage, 0, 0, 0, 0, $destwidth2, $destheight2, $srcwidth, $srcheight);

    if ($sizee['mime'] == "image/jpeg") {
        @imagejpeg($destImage1, $thumb1, 80);
        @imagejpeg($destImage2, $thumb2, 80);
    } elseif ($sizee['mime'] == "image/png") {
        @imagepng($destImage1, $thumb1, 80);
        @imagepng($destImage2, $thumb2, 80);
    } elseif ($sizee['mime'] == "image/gif") {
        @imagegif($destImage1, $thumb1, 80);
        @imagegif($destImage2, $thumb2, 80);
    }


    //ImageDestroy($srcImage);
    @imagedestroy($destImage1);
    @imagedestroy($destImage2);
    @chmod($destImage1, 0777);
    @chmod($destImage2, 0777);
    return $destImage1;
}

function event_image_detail($Id, $image, $size) {
    $file_extension = substr($image, strrpos($image, '.') + 1);
    $arr = explode('.', $image);
    //if (preg_match("/^(photos|events){1}\//", $image)) {
    if ($size) {
        if (file_exists(LOCAL_FOLDER . $arr[0] . "_" . $Id . "b" . "." . $file_extension))
            return $arr[0] . "_" . $Id . "b" . "." . $file_extension;
        else
            return $image;
    } else {
        if (file_exists(LOCAL_FOLDER . $arr[0] . "_" . $Id . "." . $file_extension))
            return $arr[0] . "_" . $Id . "." . $file_extension;
        else
            return $image;
    }
    //} else {
    //   return $image;
    //}
}

//function distanceByApi($latitude1, $longitude1, $latitude2, $longitude2, $unit = 'Mi') {
//    $location = @file_get_contents('http://www.mapquestapi.com/directions/v1/routematrix?key=Dmjtd|lu612hurng%2Cas%3Do5-50zah&json={locations:[{latLng:{lat:' . $latitude1 . ',lng:' . $longitude1 . '}},{latLng:{lat:' . $latitude2 . ',lng:' . $longitude2 . '}},]}');
//    $location = json_decode($location, true);
//    $distance = end($location['distance']);
//    return (float) $distance;
//}
/*
  function distanceByApi($userLat, $userLong, $userLat1, $userLong1) {
  $result = array();
  echo 'http://maps.googleapis.com/maps/api/directions/json?origin=' . $userLat . ',' . $userLong . '&destination=' . $userLat1 . ',' . $userLong1 . '&sensor=false&units=imperial';
  $final_distance = @file_get_contents('http://maps.googleapis.com/maps/api/directions/json?origin=' . $userLat . ',' . $userLong . '&destination=' . $userLat1 . ',' . $userLong1 . '&sensor=false&units=imperial');
  $distance = json_decode($final_distance, true);
  print_r($distance);die();
  $result[] = $distance['routes'][0]['legs'][0]['distance']['text'];
  $result[] = $distance['status'];

  return (array) $result;
  }
 */
function LatLongInRange($userLat, $userLong) {
    //Top - Left: 51.545705,-124.764405 Bottom - Right: 24.58709,-81.795044
    $left_top_x = 51.545705;
    $left_top_y = -124.764405;
    $right_bottom_x = 24.686952; //24.58709
    $right_bottom_y = -53.613281; //-81.79504
    $top_right_x = substr($left_top_x, 0, (strpos($left_top_x, '.'))) . '.' . substr($right_bottom_x, strpos($right_bottom_x, '.') + 1);
    $top_right_y = -53.613281; //-81.79504
    $left_bottom_x = substr($right_bottom_x, 0, (strpos($right_bottom_x, '.'))) . '.' . substr($left_top_x, strpos($left_top_x, '.') + 1);
    $left_bottom_y = -124.764405;
    //    print_r($left_top_x . ' ' . $left_top_y . '               ' . $top_right_x . ' ' . $top_right_y . '<br>');
    //    print_r($left_bottom_x . ' ' . $left_bottom_y . '               ' . $right_bottom_x . ' ' . $right_bottom_y . '<br>');

    $test_x = $userLat;
    $test_y = $userLong;
    if (($test_x <= ($left_top_x) && $test_x >= ($left_bottom_x)) && ($test_y >= $left_top_y && $test_y <= $top_right_y)) {
        return TRUE;
    } else {
        return FALSE;
    }
}

function distanceByApi2($userLat, $userLong, $arr) {
    //print_r(count($arr));
    $getLatLongInRange = LatLongInRange($userLat, $userLong);
    $distance = array();
    if ($getLatLongInRange === TRUE && USAVENUES) {

        $display = '';
        if (($userLat == 0) && ($userLong == 0)) {
            $userLat = '';
            $userLong = '';
        }
        //    foreach ($arr as $kk => $lat) {
        $get_actual_arr = array();
        $j = 0;
        for ($i = 0; $i < count($arr); $i++) {
            if (!empty($arr) && is_array($arr)) {
                $latitude = isset($arr[$i]['latitude']) && ($arr[$i]['latitude']) ? $arr[$i]['latitude'] : 0;
                $longitude = isset($arr[$i]['longitude']) && ($arr[$i]['longitude']) ? $arr[$i]['longitude'] : 0;
                $getLatLongInRange = LatLongInRange($latitude, $longitude);
                if ($getLatLongInRange) {
                    $display = '{latLng:{lat:' . $latitude . ',lng:' . $longitude . '}}';
                    $location = @file_get_contents('http://www.mapquestapi.com/directions/v1/routematrix?key=Dmjtd|lu612hurng%2Cas%3Do5-50zah&json={locations:[{latLng:{lat:' . $userLat . ',lng:' . $userLong . '}},' . $display . ']}');
                    $location = json_decode($location, true);
                    $distance = $location['distance'];
                    //                    $distance[] = $location['info']['statuscode'];
                    $get_actual_arr[$i] = $arr[$i];
                    $get_actual_arr[$i]['distance_new'] = $distance;
                } else {
                    $get_actual_arr[$i] = $arr[$i];
                    $get_actual_arr[$i]['distance_new'] = 'no distance';
                }
                $j++;
            }
        }
        //print_r($get_actual_arr);
        //        $display = rtrim($display, ',');
        //        print_r($distance);
        die();
        return (array) $distance;
    } else {
        return FALSE;
    }
}

function distanceByApi($userLat, $userLong, $user) {
    //    print_r($user);
    //    print_r($userLong1);
    //$getLatLongInRange = LatLongInRange($user['latitude'], $user['longitude']);

    $distance = array();
    //if ($getLatLongInRange === TRUE) {
    //        print_r('<'.$getLatLongInRange.'>');
    $display = '{latLng:{lat:' . trim($user['latitude']) . ',lng:' . trim($user['longitude']) . '}}';
    // echo 'http://www.mapquestapi.com/directions/v1/routematrix?key=Fmjtd%7Cluua2lub21%2Cax%3Do5-hy80h&json={locations:[{latLng:{lat:' . trim($userLat) . ',lng:' . trim($userLong) . '}},' . $display . ']}';
    $location1 = file_get_contents('http://www.mapquestapi.com/directions/v1/routematrix?key=Fmjtd%7Cluua2lub21%2Cax%3Do5-hy80h&json={locations:[{latLng:{lat:' . $userLat . ',lng:' . $userLong . '}},' . $display . ']}');
    //        $location = @file_get_contents('http://www.mapquestapi.com/directions/v1/routematrix?key=Dmjtd|lu612hurng%2Cas%3Do5-50zah&json={locations:[{latLng:{lat:' . $userLat . ',lng:' . $userLong . '}},' . $display . ']}');
    $location = json_decode($location1, true);

    $distance['distance'] = $location['distance'][1];
    $distance['statusCode'] = $location['info']['statuscode'];
    $distance[] = $user;

    return $distance;
    //} else {
    //$distance[] = '';
    //return $distance;
    //}
}

function distanceByApi1($userLat, $userLong, $arr) {
    writelog("Appearance:DistanceByAPI:", "Start of the process ---> ", false, 0, 2);
    $display = '';
    $arrVenues = array();
    for ($i = 0; $i < count($arr); $i++) {

        $latitude = isset($arr[$i]['latitude']) && ($arr[$i]['latitude']) ? $arr[$i]['latitude'] : NULL;
        $longitude = isset($arr[$i]['longitude']) && ($arr[$i]['longitude']) ? $arr[$i]['longitude'] : NULL;
        $getLatLongInRange = LatLongInRange($latitude, $longitude);
        if ($getLatLongInRange === TRUE) {
            $display .= '{latLng:{lat:' . trim($latitude) . ',lng:' . trim($longitude) . '}},';
            $arrVenues[] = $arr[$i];
        }
    }
    $display = trim($display, ',');
    writelog("Appearance:Maquest:", "Request Start ---> ", false, 0, 2);
    //echo $userLat.'----'.$userLong;
    //echo 'http://www.mapquestapi.com/directions/v1/routematrix?key=Fmjtd%7Cluua2lub21%2Cax%3Do5-hy80h&json={locations:[{latLng:{lat:' . $userLat . ',lng:' . $userLong . '}},' . $display . ']}';
    $location = @file_get_contents('http://www.mapquestapi.com/directions/v1/routematrix?key=Fmjtd%7Cluua2lub21%2Cax%3Do5-hy80h&json={locations:[{latLng:{lat:' . trim($userLat) . ',lng:' . trim($userLong) . '}},' . $display . ']}');
    writelog("Appearance:Mapquest:", "Request End ---> ", false, 0, 2);
    $location = json_decode($location, true);
    $distance = $location['distance'];

    //print_r($distance);
    //echo '<br>';
    //print_r($arrVenues);
    foreach ($arrVenues as $index => $array) {

        $index1 = $index + 1;
        $arrVenues[$index]['distance_new'] = $distance[$index1];
    }
    //echo '<br>';
    //print_r($arrVenues);	
    writelog("Appearance:DistanceByAPI:", "End of the process ---> ", false, 0, 2);
    return $arrVenues;
}

function response_string($object, $function, $error, $xmlrequest, $requesttype) {
    $obj_error = new Error();
    if (isset($error['successful']) && $error['successful']) {
        $response_message = $obj_error->error_type($requesttype, $error);

        if (isset($object) && ($object)) {
            $response = $object->$function($response_message, $xmlrequest);
        } else {
            $response = $function($response_message, $xmlrequest);
        }
        return $response;
    } else if (isset($error['facebookLoginError']) && $error['facebookLoginError']) {
        return $response_message = $obj_error->error_type($requesttype, $error);
    } else {
        return $response_message = $obj_error->error_type($requesttype, $error);
    }
}

function photoUploadLocation($xmlrequest) {
    $match = trim($xmlrequest['PhotoUpload']['uploadLocation']);

    $userinfo = array();
    switch ($match) {
        case "Profile":
            $obj_photo_upload = new Profile();
            $userinfo = $obj_photo_upload->profile_photo_upload($xmlrequest);
            break;

        case "Albums":
            $obj_photo_upload = new Profile();
            $userinfo = $obj_photo_upload->album_photo_upload($xmlrequest);
            break;

        case "Hotpress":
            $obj_photo_upload = new HotPress();
            $userinfo = $obj_photo_upload->hotpress_photo_upload($xmlrequest);
            break;

        case "Events":
            $obj_photo_upload = new Events();
            $userinfo = $obj_photo_upload->event_photo_upload($xmlrequest);
            break;

        case"Appearance":
            $obj_photo_upload = new Appearance();
            $userinfo = $obj_photo_upload->appearance_photo_upload($xmlrequest);
            break;

        case"ProfilePhoto":
            $obj_photo_upload = new Entourage();
            $userinfo = $obj_photo_upload->entourage_photo_upload($xmlrequest);
            break;
    }
    return $userinfo;
}

function photoUpload($response_message, $xmlrequest) {

    if (isset($response_message['PhotoUpload']['SuccessCode']) && ( $response_message['PhotoUpload']['SuccessCode'] == '000')) {
        $userinfo = array();
        $userinfo = photoUploadLocation($xmlrequest);
        $final_check = false;
        if ((isset($userinfo['successful_fin'])) && (!$userinfo['successful_fin'])) {
            $final_check = true;
        }
        if ((isset($userinfo['chunk_name'])) && ($userinfo['chunk_name'])) {
            $final_check = true;
        }
        if ((isset($userinfo['write'])) && ($userinfo['write'])) {
            $final_check = false;
        }
        $hotpressId = isset($userinfo['hotpress_last_id']) && ($userinfo['hotpress_last_id']) ? $userinfo['hotpress_last_id'] : NULL;
        $lastId = isset($userinfo['last_id']) && ($userinfo['last_id']) ? $userinfo['last_id'] : NULL;
        $photo_id = isset($userinfo['photo_id']) && ($userinfo['photo_id']) ? $userinfo['photo_id'] : NULL;
        $eventId = isset($userinfo['eventId']) && ($userinfo['eventId']) ? $userinfo['eventId'] : NULL;
        $hotpressAlbumId = isset($userinfo['hotpressAlbumId']) && ($userinfo['hotpressAlbumId']) ? $userinfo['hotpressAlbumId'] : NULL;

        if ($final_check) {
            $obj_error = new Error();
            $response_message = $obj_error->error_type("PhotoUpload", $userinfo);

            $userinfocode = $response_message['PhotoUpload']['ErrorCode'];
            $userinfodesc = $response_message['PhotoUpload']['ErrorDesc'];
            $response_mess = $response_mess = get_response_string("PhotoUpload", $userinfocode, $userinfodesc);
            return $response_mess;
        }

        $userinfocode = $response_message['PhotoUpload']['SuccessCode'];
        $userinfodesc = $response_message['PhotoUpload']['SuccessDesc'];
        $response_str = response_repeat_string();

        // $last_id = ((isset($userinfo['last_id'])) && ($userinfo['last_id'])) ? $userinfo['last_id'] : NULL;
        $response_mess = '
	{
	' . $response_str . '
	"PhotoUpload":{
	"hotpressId":"' . $hotpressId . '",
	"lastId":"' . $lastId . '",
	"photoId":"' . $photo_id . '",
	"eventId":"' . $eventId . '",
	"hotpressAlbumId":"' . $hotpressAlbumId . '",
	"errorCode":"' . $userinfocode . '",
	"errorMsg":"' . $userinfodesc . '"
	}
	}
	';
    } else {
        $userinfocode = $response_message['PhotoUpload']['ErrorCode'];
        $userinfodesc = $response_message['PhotoUpload']['ErrorDesc'];
        $response_mess = get_response_string("PhotoUpload", $userinfocode, $userinfodesc);
    }

    writelog("Response:profilePhotoUpload():", $response_mess, false);
    return $response_mess;
}

function anonymous() {
    $tmp = array();
    $img = "images/Anonymous.jpg";
    $tmp['profilenam'] = 'Annonymous';
    $tmp['photo_b_thumb'] = $img;
    $tmp['gender'] = '';
    $tmp['profile_type'] = '';
    return $tmp;
}

//for push notification
function push_notification($msg_type, $mem_id, $receiver_id=NULL) {

    if (($msg_type == 'appearance_tag_entourage') || ($msg_type == 'event_post_comment') || ($msg_type == 'comment_on_photo') || ($msg_type == 'send_messages') || ($msg_type == 'reply_event_comment') || ($msg_type == 'reply_message') || ($msg_type == 'tag_photo')) {
        $get_online_info = "SELECT id FROM user_push_notification WHERE mem_id='$receiver_id' AND showonline='y'";
        $exe_get_online_info = execute_query($get_online_info, true, "select");
        $get_total_no_of_alerts = get_total_alerts($receiver_id);
        foreach ($exe_get_online_info as $kk => $online_info) {
            $get_token = execute_query("SELECT token_id from iphone_push_notfn WHERE user_notification_id='" . $online_info['id'] . "'", false, "select");
            $txt = '';
            if (!empty($get_token)) {
                if ($msg_type == 'send_messages') {
                    $txt = 'a new message from ';
                } elseif ($msg_type == 'reply_message') {
                    $txt = 'a new message reply from ';
                } elseif ($msg_type == 'tag_photo') {
                    $txt = 'been tagged in a photo by ';
                } elseif ($msg_type == 'reply_event_comment') {
                    $txt = 'a new event comment reply by ';
                } elseif ($msg_type == 'comment_on_photo') {
                    $txt = 'a new photo comment by ';
                } elseif ($msg_type == 'event_post_comment') {
                    $txt = 'a new event comment by ';
                } elseif ($msg_type == 'appearance_tag_entourage') {
                    $txt = 'been tagged in an appearance by ';
                }
                $msg = "You have " . $txt . getname($mem_id) . "";
                $token_key = $get_token['token_id'];
                push_notfn($msg_type, $token_key, $msg, $mem_id, $get_total_no_of_alerts);
            }
        }
    } elseif (($msg_type == 'post_comment_on_profile') || ($msg_type == 'post_comment_on_hotpress') || ($msg_type == 'comment_on_appearance')) {

        $get_online_info = "SELECT id FROM user_push_notification WHERE mem_id='$mem_id' AND showonline='y'";
        $exe_get_online_info = execute_query($get_online_info, true, "select");
        $get_total_no_of_alerts = get_total_alerts($mem_id);
        foreach ($exe_get_online_info as $kk => $online_info) {
            $get_token = execute_query("SELECT token_id from iphone_push_notfn WHERE user_notification_id='" . $online_info['id'] . "'", false, "select");

            if (!empty($get_token)) {
                if ($msg_type == 'post_comment_on_hotpress') {
                    $text = 'a new hotpress comment by ';
                } elseif ($msg_type == 'post_comment_on_profile') {
                    $text = 'a new profile comment by ';
                } elseif ($msg_type == 'comment_on_appearance') {
                    $text = 'a new appearance comment by ';
                }
                $msg = "You have " . $text . getname($receiver_id) . "";
                $token_key = $get_token['token_id'];
                push_notfn($msg_type, $token_key, $msg, $mem_id, $get_total_no_of_alerts);
            }
        }
    } elseif ($msg_type == 'friend_request') {

        $get_online_info = "SELECT id FROM user_push_notification WHERE mem_id='$receiver_id' AND showonline='y'";
        $exe_get_online_info = execute_query($get_online_info, true, "select");
        $get_total_no_of_alerts = get_total_alerts($mem_id);
        foreach ($exe_get_online_info as $kk => $online_info) {
            $get_token = execute_query("SELECT token_id from iphone_push_notfn WHERE user_notification_id='" . $online_info['id'] . "'", false, "select");
            if (!empty($get_token)) {
                $msg = "You have a new Friend Request from " . getname($mem_id) . "";
                $token_key = $get_token['token_id'];
                push_notfn($msg_type, $token_key, $msg, $mem_id, $get_total_no_of_alerts);
            }
        }
    }
}

function push_notification_for_badges($msg, $mem_id, $receiver_id=NULL) {
    $get_online_info = "SELECT id FROM user_push_notification WHERE mem_id='$receiver_id' AND showonline='y'";
    $exe_get_online_info = execute_query($get_online_info, true, "select");
    $get_total_no_of_alerts = get_total_alerts($receiver_id);
    foreach ($exe_get_online_info as $kk => $online_info) {
        $get_token = execute_query("SELECT token_id from iphone_push_notfn WHERE user_notification_id='" . $online_info['id'] . "'", false, "select");
        $msg_type = 'badges';
        $token_key = $get_token['token_id'];
        push_notfn($msg_type, $token_key, $msg, $mem_id, $get_total_no_of_alerts);
    }
}

function get_total_alerts($mem_id) {

    $xmlrequest = array();
    $xmlrequest['Alerts']['userId'] = (int) $mem_id;
    require_once ('classes/alert.class.php');
    $obj_alert = new Alerts();
    // get alerts from different database

    $alert = $obj_alert->get_alert_result((array) $xmlrequest);

    //get the alerts in array format
    $alert_rslt = getalerts($alert);

    //take the count of the alerts
    $count = 0;
    if (!empty($alert_rslt)) {
        $alerts_list = false;
        foreach ($alert_rslt as $xx => $get_values) {
            if (is_array($get_values)) {
                foreach ($get_values as $yy => $get_vals) {
                    if (!empty($get_vals)) {
                        $count++;
                        $alerts_list = true;
                    }
                }
            } else {
                $alerts_list = false;
            }
        }
    }
    return $count;
}

// function push_notfn($msg_type, $token_key, $msg, $mem_id, $no_of_notfn) {
// }
//push notification function
function push_notfn($msg_type, $token_key, $msg, $mem_id, $no_of_notfn) {
    // Put your device token here (without spaces):
    $token_key = preg_replace('/\s+/', '', $token_key);
    $token_key = str_replace(array('<', '>'), '', $token_key);
    $deviceToken = "$token_key";

    // Put your private key's passphrase here:
    $passphrase = 'csun2003'; //Epicomm1
    // Put your alert message here:
    $message = "$msg";
    if (($no_of_notfn == '(null)') || ($no_of_notfn == 'null') || ($no_of_notfn == '')) {
        $no_of_notfn = 0;
    }
    ////////////////////////////////////////////////////////////////////////////////

    $ctx = stream_context_create();
    stream_context_set_option($ctx, 'ssl', 'local_cert', '../MySNL_WebServicev2/PushNotification/Certkey.pem');
    stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

    // Open a connection to the APNS server
    $fp = stream_socket_client(
            'ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_ASYNC_CONNECT | STREAM_CLIENT_CONNECT, $ctx);

    if (!$fp)
        exit("Failed to connect: $err $errstr" . PHP_EOL);
    //body array
    $payload['aps'] = array(
        'alert' => array('body' => $message),
        'badge' => $no_of_notfn,
        'sound' => 'default');
    $payload['server'] = array('notification_type' => $msg_type);

    // Encode the payload as JSON
    $payload = json_encode($payload);
    // Build the binary notification
    //$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
    $msg = chr(0) . chr(0) . chr(32) . pack('H*', str_replace(' ', '', $deviceToken)) . chr(0) . chr(strlen($payload)) . $payload;
    // Send it to the server
    $result = fwrite($fp, $msg, strlen($msg));

    if ($result) {
        $query_log = execute_query("INSERT INTO query_log(id,messg_type,msg,mem_id,token_key,date,push_from) VALUES('','$msg_type','Message successfully delivered','$mem_id','$token_key','" . time() . "','device')", true, "select");

        // Close the connection to the server
        fclose($fp);
    }
}

//    elseif($msg_type == 'appearance_comment'){
//        $get_online_info = "SELECT id FROM user_push_notification WHERE mem_id='$mem_id' AND showonline='y'";
//        $exe_get_online_info = execute_query($get_online_info, true, "select");
//        $get_total_no_of_alerts = get_total_alerts($mem_id);
//        foreach ($exe_get_online_info as $kk => $online_info) {
//            $get_token = execute_query("SELECT token_id from iphone_push_notfn WHERE user_notification_id='" . $online_info['id'] . "'", false, "select");
//            if (!empty($get_token)) {
//                 if ($msg_type == 'post_comment_on_profile') {
//                    $text = 'a new profile comment by ';
//                }
//                $msg = "You have ".$text . getname($receiver_id) . "";
//                $token_key = $get_token['token_id'];
//                push_notfn($msg_type, $token_key, $msg, $mem_id, $get_total_no_of_alerts);
//            }
//        }
//    }
//function for sorting the alert by date
function compare_date($a, $b) {
    return strnatcmp($b['alertDate'], $a['alertDate']);
}

function compare_date_for_venues($a, $b) {
    return strnatcmp($a['distance_new'], $b['distance_new']);
}

//function for alerts pagination
function pagination_array($array, $page = 1, $limit_page = 20) {

    if (empty($page) or !$limit_page) {
        $page = 1;
    }
    $num_rows = count($array);
    if (!$num_rows || $limit_page >= $num_rows) {
        $limit_page = $num_rows;
    }
    $page_offset = ($page - 1) * $limit_page;

    $output = array_slice($array, $page_offset, $limit_page, true); //Array of current page results.
    $output['count'] = count($output);
    $output['begin'] = $page_offset;
    $end = $output['begin'] + $output['count'];
    $output['end'] = $end;
    return $output;
}

function follow_redirect($url) {
    $redirect_url = null;

    if (function_exists("curl_init")) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
    } else {
        $url_parts = parse_url($url);
        $sock = fsockopen($url_parts['host'], (isset($url_parts['port']) ? (int) $url_parts['port'] : 80));
        $request = "HEAD " . $url_parts['path'] . (isset($url_parts['query']) ? '?' . $url_parts['query'] : '') . " HTTP/1.1\r\n";
        $request .= 'Host: ' . $url_parts['host'] . "\r\n";
        $request .= "Connection: Close\r\n\r\n";
        fwrite($sock, $request);
        $response = fread($sock, 2048);
        fclose($sock);
    }

    $header = "Location: ";
    $pos = strpos($response, $header);
    if ($pos === false) {
        return false;
    } else {
        $pos += strlen($header);
        $redirect_url = substr($response, $pos, strpos($response, "\r\n", $pos) - $pos);
        return $redirect_url;
    }
}

function firemail($to, $name, $subj, $body) {
    $subj = nl2br($subj);
    $body = nl2br($body);
    $recipient = $to;

    /*    $headers = "From: noreply@socialnightlife.com" . "\r\n";
      $headers .= "X-Sender: <" . "$to" . ">\r\n";
      $headers .= "Return-Path: <" . "$to" . ">\r\n";
      $headers .= "Error-To: <" . "$to" . ">\r\n";
      $headers .= "Content-Type: text/html\r\n"; */
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
    $headers .= "To: $to\n";
    $headers .= "Error-To: <" . "$to" . ">\r\n";
    $headers .= 'From: socialnightlife.com <noreply@socialnightlife.com>' . "\r\n";

    if (mail("$recipient", "$subj", "$body", "$headers"))
        return 1;
    else
        return 0;
}

function email_template($usr_name, $subject, $body, $uid, $prophoto) {
    //	global $siteemail,$sitename,$siteurl;
    $siteurl = 'http://www.socialnightlife.com';
    $siteemail = 'http://www.socialnightlife.com';
    $sitename = 'socialNightlife.com';
    return $mail_body = "<table width=\"560\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"border:1px solid #C8C8C8;\">
	<tr align=\"left\" valign=\"top\"><td align=\"left\" valign=\"top\"><table ><tr><td><a href=\"http://www.facebook.com/home.php#!/pages/MySocialNightlifecom/161512920550724\" ><img src=\"$siteurl/images/facebook_email.gif\"  border=\"0\"></a></td><td><a href=\"http://www.twitter.com/mysnlfeed\" ><img src=\"$siteurl/images/twitter_email.png\"  border=\"0\"></a></td><td><a href=\"http://www.youtube.com/mysocialnightlife\" ><img src=\"$siteurl/images/youtube_email.png\"  border=\"0\"></a></td>
    <td><a href=\"http://www.myspace.com/myexoticfriends\" ><img src=\"$siteurl/images/myspace_email.png\"  border=\"0\"></a></td>
    <td><a href=\"#\" ><img src=\"$siteurl/images/blogger_email.png\"  border=\"0\"></a> </td>
	</tr></table></td></tr><tr align=\"left\" valign=\"top\"><td align=\"left\" valign=\"top\"><a href=\"$siteurl\" >
	<img src=\"$siteurl/images/email_header.jpg\" alt=\"socialnightlife.com\" border=\"0\"></a></td></tr>
    <tr><td colspan=\"3\" valign=\"top\" style=\"padding:10px 10px\">
    <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">
	<tr><td width=\"15%\" valign=\"top\"><a href=\"$siteurl/index.php?pg=profile&usr=$uid\" ><img src=\"$siteurl/$prophoto \"  ></a></td>
    <td width=\"85%\" valign=\"top\" style=\"font-family: arial,sans-serif,helvetica;font-size: 12px\">
	<p><a href=\"$siteurl/index.php?pg=profile&usr=$uid\" ><strong>$usr_name</strong> </a></p>
	<p><strong>Subject:</strong><span style=\"color:#65161C;text-decoration:none;font-family:arial,sans-serif,helvetica;font-size:12px\"> $subject </span></p>
	<p>$body</p><p>\"Elevating The Social Nightlife Experience!\"</p><p>Thanks and Regards,<br>Site Administrator<br><a href=\"$siteurl\">$siteurl </a></p>
	<p>The message was brought to you as a courtesy of $sitename.</p>
    </td></tr></table>
    </td></tr></table>";
}

/* * ************************ Code Added by Aarya onwards 24 Nov 2011 :: Start Here ******************************************* */

function clean_spaces($input_ele) {
    return trim($input_ele);
}

function get_organized_comment_data($commentstr, $hotpress_username=NULL) {

    $ret_comment_blocks = array();

    $ret_comment_blocks["comment_text"] = ''; //"NULL";
    $ret_comment_blocks["comment_type"] = ''; //"NULL";
    $ret_comment_blocks["comment_title"] = ''; //"NULL";
    $ret_comment_blocks["comment_url"] = ''; //"NULL";
    $ret_comment_blocks["comment_desc"] = ''; //"NULL";
    $ret_comment_blocks["is_appearance_comment"] = "false";
    $ret_comment_blocks["appearance_comment_text"] = ''; //"NULL";

    $replace_tag_list = array("\r\n", "\r", "\n", "<br />", "<br/>", "<br>");

    $commentstr = trim($commentstr);
    $commentstr = str_replace("<br/>", "<br>", $commentstr);
    $comment_blocks = explode("<br><br>", $commentstr); //Break comment stored in DB on 2 <BR> tags
    $total_blocks = count($comment_blocks);

    $comment_blocks = array_map("clean_spaces", $comment_blocks);

    if ($total_blocks > 0) {
        if (strpos($comment_blocks[0], "an appearance @") === false) { //check if it is comment made for announce arrival
            $ret_comment_blocks["is_appearance_comment"] = "false";

            switch ($total_blocks) {
                case 1: //If only comment text is present.


                    $ret_comment_blocks["comment_text"] = str_replace($replace_tag_list, "\\n", $comment_blocks[0]);
                    $ret_comment_blocks["comment_text"] = strip_tags($ret_comment_blocks["comment_text"]);

                    $ret_comment_blocks["comment_type"] = "plaintext";

                    break;

                case 2: //If event is shared without any comment.
                    $ret_comment_blocks["comment_text"] = ''; //"NULL";
                    $ret_comment_blocks["comment_title"] = strip_tags($comment_blocks[0]);

                    $ret_comment_blocks["comment_desc"] = str_replace($replace_tag_list, "\\n", $comment_blocks[1]);
                    $ret_comment_blocks["comment_desc"] = strip_tags($ret_comment_blocks["comment_desc"]);

                    $ret_comment_blocks["comment_type"] = "event";


                    break;

                case 3: //If event with comment or video/url is shared without comment text.

                    if (strpos($comment_blocks[1], "index.php?pg=events") === false)
                        $ret_comment_blocks["comment_type"] = "weburl";
                    else
                        $ret_comment_blocks["comment_type"] = "event";

                    if (isset($comment_blocks[2]) && !empty($comment_blocks[2])) {
                        $ret_comment_blocks["comment_text"] = str_replace($replace_tag_list, "\\n", $comment_blocks[0]);
                        $ret_comment_blocks["comment_text"] = strip_tags($ret_comment_blocks["comment_text"]);

                        $ret_comment_blocks["comment_desc"] = str_replace($replace_tag_list, "\\n", $comment_blocks[2]);
                        $ret_comment_blocks["comment_desc"] = strip_tags($ret_comment_blocks["comment_desc"]);
                        $ret_comment_blocks["comment_title"] = strip_tags($comment_blocks[1]);
                        $ret_comment_blocks["comment_url"] = ''; //"NULL";
                    } else {
                        $ret_comment_blocks["comment_text"] = ''; //"NULL";
                        $ret_comment_blocks["comment_desc"] = ''; //"NULL";
                        $ret_comment_blocks["comment_url"] = strip_tags($comment_blocks[1]);
                        $ret_comment_blocks["comment_title"] = strip_tags($comment_blocks[0]);
                    }

                    break;

                case 4: //If event/video/url is shared with comment text.

                    $ret_comment_blocks["comment_text"] = str_replace($replace_tag_list, "\\n", $comment_blocks[0]);
                    $ret_comment_blocks["comment_text"] = strip_tags($ret_comment_blocks["comment_text"]);


                    $ret_comment_blocks["comment_title"] = strip_tags($comment_blocks[1]);

                    if (strpos($comment_blocks[2], "index.php?pg=events") === false) {
                        $ret_comment_blocks["comment_type"] = "weburl";
                        $ret_comment_blocks["comment_url"] = strip_tags($comment_blocks[2]);
                    } else {
                        $ret_comment_blocks["comment_type"] = "event";
                        $ret_comment_blocks["comment_url"] = strip_tags($comment_blocks[2]);
                    }

                    $ret_comment_blocks["comment_desc"] = str_replace($replace_tag_list, "\\n", $comment_blocks[3]);
                    $ret_comment_blocks["comment_desc"] = strip_tags($ret_comment_blocks["comment_desc"]);
                    break;
            }
        } else {
            $ret_comment_blocks["is_appearance_comment"] = "true";
            $ret_comment_blocks["comment_type"] = "appearance";

            if ($hotpress_username !== NULL) {
                $ret_comment_blocks["comment_text"] = str_replace($hotpress_username, '', $comment_blocks[0]);
            }

            $ret_comment_blocks["comment_text"] = strip_tags(trim($ret_comment_blocks["comment_text"]));
            $ret_comment_blocks["comment_text"] = str_replace($replace_tag_list, "\\n", $ret_comment_blocks["comment_text"]);

            $ret_comment_blocks["comment_text"] = substr($ret_comment_blocks["comment_text"], 0, strpos($ret_comment_blocks["comment_text"], '@') + 1);

            if (isset($comment_blocks[1]) && !empty($comment_blocks[1])) {
                $ret_comment_blocks["appearance_comment_text"] = strip_tags(trim(str_replace($hotpress_username, '', $comment_blocks[1])));
                $ret_comment_blocks["appearance_comment_text"] = str_replace($replace_tag_list, "\\n", $ret_comment_blocks["appearance_comment_text"]);
            }
        }
        /* print_r($ret_comment_blocks["is_appearance_comment"]); echo "----";
          print_r($ret_comment_blocks["comment_text"]); echo "----";
          print_r($ret_comment_blocks["comment_title"]); echo "----";
          print_r($ret_comment_blocks["comment_desc"]); echo "----";
          print_r($ret_comment_blocks["appearance_comment_text"]); echo "<br/>"; */

        return $ret_comment_blocks["is_appearance_comment"] . COMMENT_DELIMITER . $ret_comment_blocks["comment_type"] . COMMENT_DELIMITER . $ret_comment_blocks["comment_text"] . COMMENT_DELIMITER . $ret_comment_blocks["comment_title"] . COMMENT_DELIMITER . $ret_comment_blocks["comment_desc"] . COMMENT_DELIMITER . $ret_comment_blocks["appearance_comment_text"];
    } else {
        return false;
    }
}

/* * ************************ Code Added by Aarya onwards 24 Nov 2011 :: End Here ******************************************* */
/* function for multidimensional implode in hotpress todays top */

function multi_implode($glue, $pieces) {
    $string = '';

    if (is_array($pieces)) {
        reset($pieces);
        while (list($key, $value) = each($pieces)) {
            $string.=$glue . multi_implode($glue, $value);
        }
    } else {
        return $pieces;
    }

    return trim($string, $glue);
}

function dateDiff($invite_time) {
    $current_time = time();
    return round(abs($current_time - $invite_time) / (60 * 60 * 24));
}

/* * Added by anusha* */

function insertShow_Alert($member_id, $bulletin_id, $parent_bulletin_id) {
    /* $sql="select * from comment_alert_notification where mem_id='$member_id' and bulletin_id='$bulletin_id'";
      $comment_alert_notification=execute_query($sql,false,"select");
      if($bulletin_id != 0 && $comment_alert_notification['mem_id'] != $member_id)
      {
      $insert_query="INSERT INTO `comment_alert_notification` (`mem_id` , `bulletin_id` , `show_alert`)VALUES ('$member_id', '$bulletin_id', 'Y');";

      execute_query($insert_query,false,"insert");
      } */
    if ($parent_bulletin_id != 0) {
        $sql = "select * from bulletin where (parentid='" . $parent_bulletin_id . "' or id='" . $parent_bulletin_id . "') and mem_id !=" . $member_id . " group by mem_id";

        $bulletin_data = mysql_query($sql);

        while ($row = mysql_fetch_assoc($bulletin_data)) {

            $insert_query = "INSERT INTO `comment_alert_notification` (`mem_id`,`bulletin_id`,`show_alert`)VALUES (" . $row['mem_id'] . "," . $bulletin_id . ",'Y');";

            execute_query($insert_query, false, "insert");
        }
    }
}

/* @param str: String which contains special symbol
 * @return String: replaced String 
 */

function getValidJSON($str) {
    if (strpos($str, "�") != FALSE) {
        $str = str_replace("�", "(mysnl)mDash;", $str);
    }
    if (strpos($str, "�") != FALSE) {
        $str = str . str_replace("-", "(mysnl)sDash;", $str);
    }
    if (strpos($str, "'") != FALSE) {
        $str = str_replace("'", "(mysnl)aphos;", $str);
    }
    if (strpos($str, "�") != FALSE) {
        $str = str_replace("�", "(mysnl)iexcl;", $str);
    }
    if (strpos($str, "�") != FALSE) {
        $str = str_replace("�", "(mysnl)iquest;", $str);
    }
    if (strpos($str, "�") != FALSE) {
        $str = str_replace("�", "(mysnl)ldquo;", $str);
    }
    if (strpos($str, "�") != FALSE) {
        $str = str_replace("�", "(mysnl)rdquo;", $str);
    }
    if (strpos($str, "�") != FALSE) {
        $str = str_replace("�", "(mysnl)lsquo;", $str);
    }
    if (strpos($str, "�") != FALSE) {
        $str = str_replace("�", "(mysnl)rsquo;", $str);
    }
    if (strpos($str, "�") != FALSE) {
        $str = str_replace("�", "(mysnl)laquo;", $str);
    }
    if (strpos($str, "�") != FALSE) {
        $str = str_replace("�", "(mysnl)raquo;", $str);
    }
    if (strpos($str, "�") != FALSE) {
        $str = str_replace("�", "(mysnl)cent;", $str);
    }
    if (strpos($str, "�") != FALSE) {
        $str = str_replace("�", "(mysnl)copy;", $str);
    }
    if (strpos($str, "�") != FALSE) {
        $str = str_replace("�", "(mysnl)divide;", $str);
    }
    if (strpos($str, ">") != FALSE) {
        $str = str_replace(">", "(mysnl)gt;", $str);
    }
    if (strpos($str, "<") != FALSE) {
        $str = str_replace("<", "(mysnl)lt;", $str);
    }
    if (strpos($str, "�") != FALSE) {
        $str = str_replace("�", "(mysnl)micro;", $str);
    }
    if (strpos($str, "�") != FALSE) {
        $str = str_replace("�", "(mysnl)middot;", $str);
    }
    if (strpos($str, "�") != FALSE) {
        $str = str_replace("�", "(mysnl)para;", $str);
    }
    if (strpos($str, "�") != FALSE) {
        $str = str_replace("�", "(mysnl)plusmn;", $str);
    }
    if (strpos($str, "�") != FALSE) {
        $str = str_replace("�", "(mysnl)euro;", $str);
    }
    if (strpos($str, "�") != FALSE) {
        $str = str_replace("�", "(mysnl)pound;", $str);
    }
    if (strpos($str, "�") != FALSE) {
        $str = str_replace("�", "(mysnl)reg;", $str);
    }
    if (strpos($str, "�") != FALSE) {
        $str = str_replace("�", "(mysnl)sect;", $str);
    }
    if (strpos($str, "�") != FALSE) {
        $str = str_replace("�", "(mysnl)trade;", $str);
    }
    if (strpos($str, "�") != FALSE) {
        $str = str_replace("�", "(mysnl)yen;", $str);
    }
    if (strpos($str, "&") != FALSE) {
        $str = str_replace("&", "(mysnl)amp;", $str);
    }
    if (strpos($str, "-") != FALSE) {
        $str = str_replace("-", "(mysnl)nDash;", $str);
    }
    /* if(strpos($str,"\"")!=FALSE)
      {
      $str = str_replace("\"","(mysnl)quot;",$str);
      } */
    if (strpos($str, "�") != FALSE) {
        $str = str_replace("�", "(mysnl)degrees;", $str);
    }
    if (strpos($str, "�") != FALSE) {
        $str = str_replace("�", "(mysnl)a;", $str);
    }
    if (strpos($str, "�") != FALSE) {
        $str = str_replace("�", "(mysnl)e;", $str);
    }
    if (strpos($str, "�") != FALSE) {
        $str = str_replace("�", "(mysnl)e1;", $str);
    }
    /* if(strpos($str,"�") != FALSE)
      {
      $str = str_replace("�","(mysnl)a1;",$str);
      }
      if(strpos($str,"�") != FALSE)
      {
      $str = str_replace("�","(mysnl)a2;",$str);
      } */
    return $str;
}

function getActualJson($str) {
    if (strpos($str, "(mysnl)rdquo;") != FALSE) {
        $str = str_replace("(mysnl)rdquo;", "�", $str);
    }

    if (strpos($str, "(mysnl)quot;") != FALSE) {
        $str = str_replace("(mysnl)quot;", "\\\"", $str);
    }

    if (strpos($str, "(mysnl)nDash;") != FALSE) {

        $str = str_replace("(mysnl)nDash;", "-", $str);
    }

    if (strpos($str, "(mysnl)sDash;") != FALSE) {
        $str = str_replace("(mysnl)sDash;", "-", $str);
    }

    if (strpos($str, "(mysnl)aphos;") != FALSE) {
        $str = str_replace("(mysnl)aphos;", "'", $str);
    }

    if (strpos($str, "(mysnl)mDash;") != FALSE) {
        $str = str_replace("(mysnl)mDash;", "�", $str);
    }

    if (strpos($str, "(mysnl)iexcl;") != FALSE) {
        $str = str_replace("(mysnl)iexcl;", "�", $str);
    }

    if (strpos($str, "(mysnl)iquest;") != FALSE) {
        $str = str_replace("(mysnl)iquest;", "�", $str);
    }

    if (strpos($str, "(mysnl)ldquo;") != FALSE) {
        $str = str_replace("(mysnl)ldquo;", "�", $str);
    }

    if (strpos($str, "(mysnl)lsquo;") != FALSE) {
        $str = str_replace("(mysnl)lsquo;", "�", $str);
    }

    if (strpos($str, "(mysnl)rsquo;") != FALSE) {
        $str = str_replace("(mysnl)rsquo;", "�", $str);
    }

    if (strpos($str, "(mysnl)laquo;") != FALSE) {
        $str = str_replace("(mysnl)laquo;", "�", $str);
    }

    if (strpos($str, "(mysnl)raquo;") != FALSE) {
        $str = str_replace("(mysnl)raquo;", "�", $str);
    }

    if (strpos($str, "(mysnl)amp;") != FALSE) {
        $str = str_replace("(mysnl)amp;", "&", $str);
    }

    if (strpos($str, "(mysnl)cent;") != FALSE) {
        $str = str_replace("(mysnl)cent;", "�", $str);
    }

    if (strpos($str, "(mysnl)copy;") != FALSE) {
        $str = str_replace("(mysnl)copy;", "�", $str);
    }

    if (strpos($str, "(mysnl)divide;") != FALSE) {
        $str = str_replace("(mysnl)divide;", "�", $str);
    }

    if (strpos($str, "(mysnl)gt;") != FALSE) {
        $str = str_replace("(mysnl)gt;", ">", $str);
    }
    if (strpos($str, "(mysnl)lt;") != FALSE) {
        $str = str_replace("(mysnl)lt;", "<", $str);
    }

    if (strpos($str, "(mysnl)micro;") != FALSE) {
        $str = str_replace("(mysnl)micro;", "�", $str);
    }
    if (strpos($str, "(mysnl)middot;") != FALSE) {
        $str = str_replace("(mysnl)middot;", "�", $str);
    }
    if (strpos($str, "(mysnl)para;") != FALSE) {
        $str = str_replace("(mysnl)para;", "�", $str);
    }

    if (strpos($str, "(mysnl)plusmn;") != FALSE) {
        $str = str_replace("(mysnl)plusmn;", "�", $str);
    }
    if (strpos($str, "(mysnl)euro;") != FALSE) {
        $str = str_replace("(mysnl)euro;", "�", $str);
    }
    if (strpos($str, "(mysnl)pound;") != FALSE) {
        $str = str_replace("(mysnl)pound;", "�", $str);
    }
    if (strpos($str, "(mysnl)reg;") != FALSE) {
        $str = str_replace("(mysnl)reg;", "�", $str);
    }

    if (strpos($str, "(mysnl)sect;") != FALSE) {
        $str = str_replace("(mysnl)sect;", "�", $str);
    }
    if (strpos($str, "(mysnl)trade;") != FALSE) {
        $str = str_replace("(mysnl)trade;", "�", $str);
    }
    if (strpos($str, "(mysnl)yen;") != FALSE) {
        $str = str_replace("(mysnl)yen;", "�", $str);
    }
    if (strpos($str, "(mysnl)degrees;") != FALSE) {
        $str = str_replace("(mysnl)degrees;", "�", $str);
    }
    if (strpos($str, "(mysnl)a;") != FALSE) {
        $str = str_replace("(mysnl)a;", "�", $str);
    }
    if (strpos($str, "(mysnl)e;") != FALSE) {
        $str = str_replace("(mysnl)e;", "�", $str);
    }
    if (strpos($str, "(mysnl)e1;") != FALSE) {
        $str = str_replace("(mysnl)e1;", "�", $str);
    }
    /* if(strpos($str,"(mysnl)a1;") != FALSE)
      {
      $str = str_replace("(mysnl)a1;","�",$str);
      }
      if(strpos($str,"(mysnl)a2;") != FALSE)
      {
      $str = str_replace("(mysnl)a2;","�",$str);
      } */

    return $str;
}

function execute_query_new($query) {
    $result = mysql_query($query) OR die(mysql_error());
    return $result;
}

?>	