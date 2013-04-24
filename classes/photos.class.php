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
  File-name : photos.class.php
  Directory Path  : $/MySNL/Deliverables/Code/MySNL_WebServiceV2/hotpress.class.php/
  Author    : Brijesh Kumar
  Date    : 16/08/2011
  Modified By   : N/A
  Date : N/A

  Include Files : none
  CSS File(s)   : none

  Functions Used
  Javascript   :  none
  PHP     : profile_photo,profile_photo_valid,profile_album_details,profile_album_details_valid,comment_on_photo,comment_on_photo_valid,make_profile_photo,make_profile_photo_valid,delete_photo,delete_photo_valid,photo_sub_comments,display_comment_on_photo,get_total_comment_count_photo,display_comment_on_photo_valid,full_screen_photo,full_screen_photo_valid,tag_photo,tag_photo_valid,tags_on_photo,profile_photo_upload,profile_photo_upload_valid,album_photo_upload,album_photo_upload_valid,create_photo_album,create_photo_album_valid,createAlbum,photos,photoAlbumDetails,commentOnPhoto,makeProfilePhoto,deletePhoto,fullScreenPhoto,tagPhoto,tagsOnPhoto,displayCommentOnPhoto,commentsOnPhotosParentComment,delete_photo_comment,deletePhotoComment

  DataBase Table(s)  : albums, photo_albums,tagged_photos,fn_annotation_rows,members,network,bulletin

  Global Variable(s)  : LOCAL_FOLDER: Path where all the images save.
  PROFILE_IMAGE_SITEURL:website url

  Description:  These Variables are use to store logical path of website.

  Reviwed By  :
  Reviwed Date:
 * ************************************************************************************* */

/*  class AlbumPhotos
  Purpose:Create Photo Albums.
 *        Add photos.Post Comment on photos
 *        Shared photo on Hotpress.
 *        change profile pic.
 *        Display Photos
 *        Tag photos.
 *        Show tagged entourages.
 *        Delete Photos and Albums
 *   .

 * Returns : None
 */

class AlbumPhotos {

    var $profile_url = PROFILE_IMAGE_SITEURL;
    var $local_folder = LOCAL_FOLDER;

    /*
     * Function:profile_photo($xmlrequest)
     * Description : to get the details of albums which user has in their Profile.
     * Parameters: $xmlrequest=>request sent by user
      Return: Array of album Details.
     *  */

     function profile_photo($xmlrequest) {
        $userId = mysql_real_escape_string($xmlrequest['Photos']['userId']);
        $album = array();
		$str="";
        $type = 'vip';
        $query = "SELECT id, title, albums.desc, album_cover, create_date,(SELECT COUNT(*) FROM photo_album WHERE album_id=albums.id) as count FROM albums WHERE mem_id='$userId' AND type<>'$type'";
        if (DEBUG)
            writelog("profile.class.php :: profile_photo() :: query:", $query, false);
		$postcount=0;
        $result = execute_query_new($query);
		if ((mysql_num_rows($result) > 0)) {
	    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {

                    $check_set_var = (isset($row['id']) ? true : "");
                    $row['title'] = (isset($row['title']) ? $row['title'] : "");
                    $row['desc'] = (isset($row['desc']) ? $row['desc'] : "");
                    $row['album_cover'] = ((isset($row['album_cover']) && (strlen($row['album_cover']) > 7)) ? $this->profile_url . $row['album_cover'] : $this->profile_url . "photos/1273660058.jpg");
                    $row['count'] = (isset($row['count']) ? $row['count'] : "");

                    if (($check_set_var) && ($postcount < 20)) {
                        $str_temp = '{
            "albumId":"' .str_replace('"', '\"',$row['id']). '",
            "albumTitle":"' .str_replace('"', '\"',ucfirst($row['title'])). '",
            "albumImageUrl":"' .str_replace('"', '\"',$row['album_cover']). '",
            "albumDescription":"' .str_replace('"', '\"',$row['desc']). '",
            "albumPhotoCount":"' .str_replace('"', '\"',$row['count']). '",
            "albumCreated":"' . date("j F Y", $row['create_date']) . '",
            "albumCommentCount":8,
            "albumLikeCount":2
         }';
                        $postcount++;
                        $str = $str . $str_temp;
                        $str = $str . ',';
                    }
              
		
		}
		}
		
        $album['count'] = isset($postcount) && ($postcount) ? $postcount : 0;
		$album['str']=$str;
        if (DEBUG)
            writelog("profile.class.php :: profile_photo() :: query:", "Total albums", false, $count);

        /*for ($i = 0; $i < $album['count']; $i++) {
            $album_id = $album[$i]['id'];
            $query_photo_count = "SELECT COUNT(*) FROM photo_album WHERE album_id='$album_id'";
            if (DEBUG)
                writelog("profile.class.php :: profile_photo() :: query:", $query_photo_count, false);

            $result_photo_count = execute_query($query_photo_count, false, "select");
            $album[$i]['tot_photo'] = isset($result_photo_count['COUNT(*)']) && ($result_photo_count['COUNT(*)']) ? $result_photo_count['COUNT(*)'] : NULL;
        }*/
        if (DEBUG)
            writelog("Profile:profile_photo", $album, true);
        return $album;
    }

    /*
     * Function:profile_photo_valid($xmlrequest)
     * Description:to validate the user.
     * Parameters: $xmlrequest=>request sent by user
      Return: boolean Array.
     *  */

    function profile_photo_valid($xmlrequest) {
        $userId = mysql_real_escape_string($xmlrequest['Photos']['userId']);
        $error = array();

        $query = "SELECT COUNT(*) FROM members WHERE mem_id='$userId'";
        if (DEBUG)
            writelog("profile.class.php :: profile_photo_valid() :: query:", $query, false);

        $result = execute_query($query, false, "select");
        $error['successful'] = isset($result['COUNT(*)']) && ($result['COUNT(*)']) ? true : false;
        if (DEBUG)
            writelog("Profile:profile_photo_valid", $error, true);
        return $error;
    }

    /*
     * Function:profile_album_details($xmlrequest)
     * Description: to get the details of albums user have. Like number of photos+comment count on photo,Album cover.
     * Parameters: $xmlrequest=>request sent by user
      Return:Array containing specific album details.
     *  */

    function profile_album_details($xmlrequest) {

        $albumId = mysql_real_escape_string($xmlrequest['PhotoAlbumDetails']['albumId']);
        $photo = array();
        $query = "SELECT caption,photo_id,photo,photo_mid FROM photo_album WHERE album_id='$albumId'";
        $photo = execute_query($query, true);
        $type = 'vip';
        $count = isset($photo['count']) && ($photo['count']) ? $photo['count'] : 0;
        for ($i = 0; $i < $count; $i++) {
            $id = isset($photo[$i]['photo_id']) && ($photo[$i]['photo_id']) ? $photo[$i]['photo_id'] : 0;
            $query_comment = "SELECT COUNT(*) FROM photo_comments WHERE photo_id='$id'AND parent_id=0";
            $result_comment_count = execute_query($query_comment, false, "select");
            $photo[$i]['commentCount'] = $result_comment_count['COUNT(*)'];
        }
        $query_album_name = "SELECT title FROM albums WHERE id='$albumId' AND type<>'$type'";
        if (DEBUG)
            writelog("profile.class.php :: profile_album_details() :: query:", $query_album_name, false);

        $photo['name'] = execute_query($query_album_name, false);
        if (DEBUG)
            writelog("profile.class.php:profile_album_details", $photo, true);
        return $photo;
    }

    /*
     * Function:profile_album_details_valid($xmlrequest)
     * Description : to validate details related to album.
     * Parameters: $xmlrequest=>request by user
      Return:boolean Array
     *  */

    function profile_album_details_valid($xmlrequest) {
        $albumId = mysql_real_escape_string($xmlrequest['PhotoAlbumDetails']['albumId']);
        $error = array();
        $query = "SELECT COUNT(*) FROM albums WHERE id='$albumId'"; //AND mem_id='$userId'";
        if (DEBUG)
            writelog("profile.class.php :: profile_album_details_valid() :: query:", $query, false);

        $result = execute_query($query, false, "select");
        $error['successful'] = isset($result['COUNT(*)']) && ($result['COUNT(*)']) ? true : false;
        if (DEBUG)
            writelog("Profile:profile_album_details_valid", $error, true);
        return $error;
    }

    /*
     * Function:comment_on_photo($xmlrequest)
     * Description: to post comment on photo.
     * Parameters: $xmlrequest=>request sent by user
      Return:Array having the info related to comment has been saved or not+last Id.
     *  */

    function comment_on_photo($xmlrequest) {
        if (DEBUG)
            writelog("profile.class.php :: post_comment_on_profile() : ", "Start Here", false);

        $userId = mysql_real_escape_string($xmlrequest['CommentOnPhoto']['userId']);
        $albumId = mysql_real_escape_string($xmlrequest['CommentOnPhoto']['albumId']);
        $photoId = mysql_real_escape_string($xmlrequest['CommentOnPhoto']['photoId']);
        $comment = mysql_real_escape_string($xmlrequest['CommentOnPhoto']['comment']);
        $time = mysql_real_escape_string($xmlrequest['CommentOnPhoto']['time']);
        $displayAsHotpress = mysql_real_escape_string(isset($xmlrequest['CommentOnPhoto']['displayAsHotpress']) && ($xmlrequest['CommentOnPhoto']['displayAsHotpress']) ? $xmlrequest['CommentOnPhoto']['displayAsHotpress'] : NULL);
        $postId = mysql_real_escape_string(isset($xmlrequest['CommentOnPhoto']['postId']) && ($xmlrequest['CommentOnPhoto']['postId']) ? $xmlrequest['CommentOnPhoto']['postId'] : NULL);

	$userTimezone = new DateTimeZone('America/Chicago');
        $myDateTime = new DateTime("$time");
        $offset = $userTimezone->getOffset($myDateTime);
        $date = $myDateTime->format('U') + $offset;
//        $date = time();
        $error = array();
        if ((isset($xmlrequest['CommentOnPhoto']['postId'])) && ($xmlrequest['CommentOnPhoto']['postId'])) {
            $query_public = "SELECT publishashotpress FROM photo_comments WHERE id='$postId'";
            $result_public = execute_query($query_public, true, "select");
            $displayAsHotpress = ((isset($result_public['publishashotpress'])) && ($result_public['publishashotpress'])) ? 1 : 0;
        }
        $query_from_id = "SELECT photo_album.photo_mid,albums.mem_id FROM albums LEFT JOIN photo_album on(photo_album.album_id=albums.id) WHERE photo_album.photo_id='$photoId'";
        if (DEBUG)
            writelog("profile.class.php :: comment_on_photo() :: query:", $query_from_id, false);

        $result_from_id = execute_query($query_from_id, false, "select");
        $from_id = $result_from_id['mem_id'];
        if ($from_id == $userId) {
            $from_id = 0;
        }
        $photo_mid = $result_from_id['photo_mid'];
        $query_photo = "INSERT INTO photo_comments(photo_id, mem_id,from_id,comment, date, msg_alert,post_via,parent_id,publishashotpress)VALUE('$photoId', '$userId','$from_id','$comment', '$date', 'Y',1,'$postId','$displayAsHotpress')";
        //for email
        $get_user_email_id = execute_query("select profilenam,email,photo_thumb from members where mem_id='$userId'", false, "select");
        $get_profile_user_email_id = execute_query("select profilenam,email from members where mem_id='$from_id'", false, "select");
	if ($from_id != $userId) {
	    $comment1 = getname($userId) . ' added a comment to one of your photos.<br>To view photo comment click' . "<a href='http://www.socialnightlife.com/index.php?pg=photos&pid=$photoId&s=v&usr=$userId'  target='_blank'>  here</a>";
	    $matter = email_template($get_user_email_id['profilenam'], 'You have a new picture comment on MEF', $comment1, $userId, $get_user_email_id['photo_thumb']);
	    firemail($get_profile_user_email_id['email'], 'From: socialNightLife <socialnightlife.com>\r\n', 'You have a new picture comment on MEF', $matter);
	}
//end email
        if (DEBUG)
            writelog("profile.class.php :: comment_on_photo() :: query:", $query_photo, false);

        $result_photo = execute_query($query_photo, true, "insert");
        //for push notification

        $get_photo_owner_id = execute_query("SELECT uploaded_by FROM photo_album WHERE photo_id='$photoId' AND album_id='$albumId'", false, "select");

        if (isset($get_photo_owner_id) && ($get_photo_owner_id['uploaded_by'] != $userId)) {

            push_notification('comment_on_photo', $userId, $get_photo_owner_id['uploaded_by']);
        }
        $comment_id = isset($result_photo['last_id']) && ($result_photo['last_id']) ? $result_photo['last_id'] : NULL;
        $result_photo['count'] = isset($result_photo['count']) && $result_photo['count'] ? $result_photo['count'] : 0; // mysql_affected_rows();
        $error = error_CRUD($xmlrequest, $result_photo['count']);

        if ((isset($error['CommentOnPhoto']['successful_fin'])) && (!$error['CommentOnPhoto']['successful_fin'])) {
            return $error;
        }

        if (isset($xmlrequest['CommentOnPhoto']['displayAsHotpress']) && ($xmlrequest['CommentOnPhoto']['displayAsHotpress']) && ($postId > 0)) {
            $query_parentid = "SELECT id FROM bulletin WHERE photocomment_id='$postId'";
            if (DEBUG)
                writelog("profile.class.php :: post_comment_on_profile() : ", $query_parentid, false);

            $result = execute_query($query_parentid, false, "select");
            $parentid = isset($result['id']) && ($result['id']) ? $result['id'] : 0;
            $privacy = user_privacy_settings($userId);

            $visible = isset($privacy) && ($privacy == 'private') ? 'allfriends' : NULL; //allfriends';

            $query_hotpress = "INSERT INTO bulletin(mem_id,subj,body,visible_to,date,parentid,from_id,photo_album_id,msg_alert,photocomment_id,post_via,image_link) VALUES('$userId','','$comment','$visible','$date','$parentid','$from_id','$photoId','Y','$comment_id','1','$photo_mid')";
            $result_hotpress = execute_query($query_hotpress, false, "insert");
            $affected_row_hotpress = isset($result_hotpress['count']) && ($result_hotpress['count']) ? $result_hotpress['count'] : NULL;
            $bullet_id = isset($result_hotpress['last_id']) && ($result_hotpress['last_id']) ? $result_hotpress['last_id'] : NULL;
            $error = error_CRUD($xmlrequest, $affected_row_hotpress);
            if ((isset($error['CommentOnPhoto']['successful_fin'])) && (!$error['CommentOnPhoto']['successful_fin'])) {
                return $error;
            }
        }
        if ((isset($xmlrequest['CommentOnPhoto']['displayAsHotpress'])) && ($xmlrequest['CommentOnPhoto']['displayAsHotpress']) && (!$postId)) {
            $query_hotpress = "INSERT INTO bulletin(mem_id,subj,body,visible_to,date,parentid,from_id,photo_album_id,msg_alert,photocomment_id,post_via,image_link) VALUES('$userId','','$comment','$visible','$date','$parentid','$from_id','$photoId','Y','$comment_id','1','$photo_mid')";
            $result_hotpress = execute_query($query_hotpress, false, "insert");
            $affected_row_hotpress = isset($result_hotpress['count']) && ($result_hotpress['count']) ? $result_hotpress['count'] : NULL;
            $error = error_CRUD($xmlrequest, $affected_row_hotpress);
            //push notification
//            if (isset($get_photo_owner_id) && ($get_photo_owner_id['uploaded_by'] != $userId)) {
//                push_notification('comment_on_photo', $get_photo_owner_id['uploaded_by'], $userId);
//            }
            $bullet_id = isset($result_hotpress['last_id']) && ($result_hotpress['last_id']) ? $result_hotpress['last_id'] : NULL;
            if ((isset($error['CommentOnPhoto']['successful_fin'])) && (!$error['CommentOnPhoto']['successful_fin'])) {
                return $error;
            }
        }
        if ((isset($xmlrequest['CommentOnPhoto']['displayAsHotpress'])) && ($xmlrequest['CommentOnPhoto']['displayAsHotpress'])) {
            $query_hotpress = "UPDATE photo_comments SET bullet_id='$bullet_id' WHERE id='$comment_id'";
            if (DEBUG)
                writelog("profile.class.php :: post_comment_on_profile() : ", $query_testo_id, false);

            $result_profile_tst_id = mysql_query($query_hotpress);
            $affected_row_hotpress = mysql_affected_rows();
            $error = error_CRUD($xmlrequest, $affected_row_hotpress);
            //push notification
//            if (isset($get_photo_owner_id) && ($get_photo_owner_id['uploaded_by'] != $userId)) {
//                push_notification('comment_on_photo', $get_photo_owner_id['uploaded_by'], $userId);
//            }
            if ((isset($error['CommentOnPhoto']['successful_fin'])) && (!$error['CommentOnPhoto']['successful_fin'])) {
                return $error;
            }
        }

        $error['id'] = isset($comment_id) && ($comment_id) ? $comment_id : NULL;

        if (DEBUG) {
            writelog("Profile:post_comment_on_profile", $error, true);
            writelog("Profile:post_comment_on_profile", "End Here", false);
        }
        return $error;
    }

    /*
     * Function:comment_on_photo_valid($xmlrequest)
     * Description : to validate Photo,Album.
     * Parameters: $xmlrequest=>request sent by user
      Return:boolean Array.
     *  */

    function comment_on_photo_valid($xmlrequest) {
        $albumId = mysql_real_escape_string($xmlrequest['CommentOnPhoto']['albumId']);
        $photoId = mysql_real_escape_string($xmlrequest['CommentOnPhoto']['photoId']);
        $query = "SELECT COUNT(*) FROM albums WHERE id='$albumId'";
        if (DEBUG)
            writelog("profile.class.php :: comment_on_photo_valid() :: query:", $query, false);

        $result = execute_query($query, false, "select");
        $query_photo = "SELECT COUNT(*) FROM photo_album WHERE photo_id='$photoId' AND album_id='$albumId'";
        $result_photo = execute_query($query_photo, false, "select");
        $error['successful'] = (isset($result['COUNT(*)'])) && ($result['COUNT(*)']) && (isset($result_photo['COUNT(*)'])) && ($result_photo['COUNT(*)']) ? true : false;
        if (DEBUG)
            writelog("Profile:comment_on_photo_valid", $error, true);
        return $error;
    }

    /*
     * Function:make_profile_photo($xmlrequest)
     * Description : to change the profile pic.
     * Parameters: $xmlrequest=>request by user
      Return:boolean Array to having the flag related to profile Image has been changed or not.
     *  */

    function make_profile_photo($xmlrequest) {
        
        $userId = mysql_real_escape_string($xmlrequest['MakeProfilePhoto']['userId']);
        $albumId = mysql_real_escape_string($xmlrequest['MakeProfilePhoto']['albumId']);
        $photoId = mysql_real_escape_string($xmlrequest['MakeProfilePhoto']['photoId']);
        $date = mysql_real_escape_string($xmlrequest['MakeProfilePhoto']['photoId']);

        $query = "SELECT photo, photo_mid, photo_small FROM photo_album WHERE photo_id='$photoId' AND album_id='$albumId'";
        if (DEBUG)
            writelog("profile.class.php :: make_profile_photo() :: query:", $query, false);
        $row = execute_query($query, false, "select");
        $photo = $row['photo'];
        $photo_mid = $row['photo_mid'];
        $photo_small = $row['photo_small'];

        $query = "UPDATE members SET photo='$photo', photo_thumb='$photo_mid', photo_b_thumb='$photo_small' WHERE mem_id='$userId'";
        if (DEBUG)
            writelog("profile.class.php :: make_profile_photo() :: query:", $query, false);

        $result = execute_query($query, false, "update");
        $result['count'] = isset($result['count']) && ($result['count']) ? $result['count'] : NULL;
        $error = error_CRUD($xmlrequest, $result['count']);
        if ((isset($error['MakeProfilePhoto']['successful_fin'])) && (!$error['MakeProfilePhoto']['successful_fin'])) {
            return $error;
        }
//to get user name.
        $query_name = "SELECT profilenam,fname,lname,profile_type,gender FROM members WHERE mem_id='$userId'";
        $result_name = execute_query($query_name, false, "select");
        $result_name['fname'] = isset($result_name['fname']) && ($result_name['fname']) ? $result_name['fname'] : NULL;
        $result_name['lname'] = isset($result_name['lname']) && ($result_name['lname']) ? $result_name['lname'] : NULL;
        $name = trim($result_name['fname'] . " " . $result_name['lname']);
        $username = isset($result_name['profilenam']) && ($result_name['profilenam']) ? $result_name['profilenam'] : (isset($name) && ($name) ? $name : NULL);
        if (($result_name['profile_type'] == 'C') || ($result_name['profile_type'] == 'c')) {
			$genderType = "its";
		} else if (($result_name['profile_type'] != 'C') && ($result_name['gender'] == 'f')) {
			$genderType = "her";
		} else {
			$genderType = "his";
		}
	$username = $username . " has changed ".$genderType." profile image";

	$userTimezone = new DateTimeZone('America/Chicago');
        $myDateTime = new DateTime("$time");
        $offset = $userTimezone->getOffset($myDateTime);
        $date = $myDateTime->format('U') + $offset;
	
//        $date = time();
        $query_hotpress = "INSERT INTO bulletin(photo_album_id,body,mem_id, date, image_link,post_via)VALUE('$photoId','$username','$userId', '$date', '$photo_small','1')";
        if (DEBUG)
            writelog("profile.class.php :: make_profile_photo() :: query:", $query_hotpress, false);

        $result_hotpress = execute_query($query_hotpress, false, "insert");
        $result_hotpress['count'] = isset($result_hotpress['count']) && ($result_hotpress['count']) ? $result_hotpress['count'] : NULL;
        $error = error_CRUD($xmlrequest, $result_hotpress['count']);
        
        if ((isset($error['MakeProfilePhoto']['successful_fin'])) && (!$error['MakeProfilePhoto']['successful_fin'])) {
            return $error;
        }

        if (DEBUG)
            writelog("Profile:make_profile_photo", $error, true);
        return $error;
    }

    /*
     * Function:make_profile_photo_valid($xmlrequest)
     * Description: to validate whether that photo and respective album.
     * Parameters: $xmlrequest=>request by user
      Return:boolean Array.
     *  */

    function make_profile_photo_valid($xmlrequest) {
        if ((isset($xmlrequest['MakeProfilePhoto']['userId'])) && (isset($xmlrequest['MakeProfilePhoto']['albumId'])) && (isset($xmlrequest['MakeProfilePhoto']['photoId']))) {
            $userId = mysql_real_escape_string($xmlrequest['MakeProfilePhoto']['userId']);
            $albumId = mysql_real_escape_string($xmlrequest['MakeProfilePhoto']['albumId']);
            $photoId = mysql_real_escape_string($xmlrequest['MakeProfilePhoto']['photoId']);
        }
        $query = "SELECT COUNT(*) FROM albums WHERE mem_id='$userId' AND id='$albumId'";
        if (DEBUG)
            writelog("profile.class.php :: make_profile_photo_valid() :: query:", $query, false);

        $result = execute_query($query, false, "select");
        $query_photo = "SELECT COUNT(*) FROM photo_album WHERE photo_id='$photoId' AND album_id='$albumId'";
        if (DEBUG)
            writelog("profile.class.php :: make_profile_photo_valid() :: query:", $query_photo, false);
        $result_photo = execute_query($query_photo, false, "select");
        $error['successful'] = isset($result['COUNT(*)']) && ($result['COUNT(*)']) && isset($result_photo['COUNT(*)']) && ($result_photo['COUNT(*)']) ? true : false;

        if (DEBUG)
            writelog("Profile:make_profile_photo_valid", $error, true);
        
        return $error;
    }

    /*
     * Function:delete_photo($xmlrequest)
     * Description : to delete Photo.
     * Parameters: $xmlrequest=>request by user
      Return:boolean Array.
     *  */

    function delete_photo($xmlrequest) {
        $userId = mysql_real_escape_string($xmlrequest['DeletePhoto']['userId']);
        $albumId = mysql_real_escape_string($xmlrequest['DeletePhoto']['albumId']);
        $photoId = mysql_real_escape_string($xmlrequest['DeletePhoto']['photoId']);
        $error = array();

        $query = "DELETE FROM photo_album WHERE photo_id='$photoId' AND album_id='$albumId'";
        if (DEBUG)
            writelog("Profile:delete_photo", $query, false);

        $result = execute_query($query, false, "delete");
        $result['count'] = isset($result['count']) && ($result['count']) ? $result['count'] : NULL;
        $error = error_CRUD($xmlrequest, $result['count']);
        if ((isset($error['DeletePhoto']['successful_fin'])) && (!$error['DeletePhoto']['successful_fin'])) {
            return $error;
        }
        if (DEBUG)
            writelog("Profile:delete_photo", $error, true);
        return $error;
    }

    /*
     * Function:delete_photo_valid($xmlrequest)
     * Description to validate Photo.
     * Parameters: $xmlrequest=>request sent by user
      Return:boolean Array.
     *  */

    function delete_photo_valid($xmlrequest) {
        $userId = mysql_real_escape_string($xmlrequest['DeletePhoto']['userId']);
        $albumId = mysql_real_escape_string($xmlrequest['DeletePhoto']['albumId']);
        $photoId = mysql_real_escape_string($xmlrequest['DeletePhoto']['photoId']);

        $error = array();
        $query = "SELECT COUNT(*) FROM albums WHERE mem_id='$userId' AND id='$albumId'";

        if (DEBUG)
            writelog("profile.class.php :: delete_photo_valid() :: query:", $query, false);
        $result = execute_query($query, false, "select");
        $query_photo = "SELECT COUNT(*) FROM photo_album WHERE photo_id='$photoId' AND album_id='$albumId'";
        if (DEBUG)
            writelog("profile.class.php :: delete_photo_valid() :: query:", $query_photo, false);

        $result_photo = execute_query($query_photo, false, "select");
        $error['successful'] = isset($result['COUNT(*)']) && ($result['COUNT(*)']) && isset($result_photo['COUNT(*)']) && ($result_photo['COUNT(*)']) ? true : false;

        if (DEBUG)
            writelog("Profile:delete_photo_valid", $error, true);
        return $error;
    }

    /*
     * Function:photo_sub_comments($xmlrequest)
     * Description :to get the List of subcomment on a specific Parent comment which has been posted on Photo.
     * Parameters: $xmlrequest=>request send by user
      Return:all sub-comment Array.
     *  */

    function photo_sub_comments($xmlrequest, $pagenumber, $limit) {
        $postId = mysql_real_escape_string($xmlrequest['CommentsOnPhotosParentComment']['postId']);
        $commentsinfo = array();
        $query = "SELECT members.is_facebook_user,members.profilenam,members.privacy,members.photo_b_thumb,members.gender,members.profile_type,photo_comments.id, photo_comments.mem_id, photo_comments.comment, photo_comments.date,photo_comments.post_via FROM photo_comments LEFT JOIN members ON(photo_comments.mem_id=members.mem_id) WHERE photo_comments.parent_id='$postId' ORDER BY photo_comments.date"; // LIMIT $lowerlimit,$limit
        if (DEBUG)
            writelog("profile.class.php :: display_comment_on_photo() :: query:", $query, false);

        $commentsinfo['Comment'] = execute_query($query, true, "select");
        if (DEBUG)
            writelog("Profile:display_comment_on_photo", $commentsinfo, true);
        return $commentsinfo;
    }

    /*
     * Function:display_comment_on_photo($xmlrequest)
     * Description : to show All comment on Photo.
     * Parameters: $xmlrequest=>request sent by user
      Return:all comment Array corresponding to specific photo.
     *  */

    function display_comment_on_photo($xmlrequest, $pagenumber, $limit) {
        $userId = mysql_real_escape_string($xmlrequest['DisplayCommentOnPhoto']['userId']);
        $photoId = mysql_real_escape_string($xmlrequest['DisplayCommentOnPhoto']['photoId']);
        $commentsinfo = array();
        $lowerLimit = isset($pagenumber) ? ($pagenumber - 1) * $limit : 0;
        $query = "SELECT SQL_CALC_FOUND_ROWS photo_comments.id, photo_comments.mem_id, photo_comments.comment, photo_comments.date,photo_comments.post_via FROM photo_comments WHERE photo_comments.photo_id='$photoId' AND (parent_id=0||parent_id=NULL) ORDER BY photo_comments.date DESC LIMIT $lowerLimit,$limit"; // LIMIT $lowerlimit,$limit

        if (DEBUG)
            writelog("profile.class.php :: display_comment_on_photo() :: query:", $query, false);

        $commentsinfo['Comment'] = execute_query($query, true, "select");
        $count = isset($commentsinfo['Comment']['count']) ? $commentsinfo['Comment']['count'] : 0;

        if (DEBUG)
            writelog("profile.class.php :: display_comment_on_photo() :: query:", "Total Records: ", false, $count);

        for ($i = 0; $i < $count; $i++) {

            $id = isset($commentsinfo['Comment'][$i]['mem_id']) && ($commentsinfo['Comment'][$i]['mem_id']) ? $commentsinfo['Comment'][$i]['mem_id'] : NULL;
            $postId = isset($commentsinfo['Comment'][$i]['id']) && ($commentsinfo['Comment'][$i]['id']) ? $commentsinfo['Comment'][$i]['id'] : NULL;
            if ($postId)
                $commentsinfo['Comment'][$i]['total_comment'] = $this->get_total_comment_count_photo($postId);
            if ($id) {
                $query_userinfo = "SELECT members.is_facebook_user,members.profilenam,members.privacy,members.photo_b_thumb,members.gender,members.profile_type FROM members WHERE members.mem_id='$id'";
                $commentsinfo['Comment'][$i]['User'] = execute_query($query_userinfo, false);
            }
        }
        $total_comment_records = execute_query("SELECT photo_comments.id, photo_comments.mem_id, photo_comments.comment, photo_comments.date,photo_comments.post_via FROM photo_comments WHERE photo_comments.photo_id='$photoId' AND (parent_id=0||parent_id=NULL) ORDER BY photo_comments.date", true, "select"); // LIMIT $lowerlimit,$limit
        $commentsinfo['Comment']['totalrecords'] = (isset($total_comment_records['count'])) ? $total_comment_records['count'] : 0;
        if (DEBUG)
            writelog("Profile:display_comment_on_photo", $commentsinfo, true);
        return $commentsinfo;
    }

    /*
     * Function:get_total_comment_count_photo($photoId)
     * Description :to get comment count on a Photo.
     * Parameters: $photoId=>Id of a photo to which we want to display all comments.
      Return:integer.
     *  */

    function get_total_comment_count_photo($photoId) {
        $query_comment_count = "SELECT COUNT(*) as totalcomment FROM photo_comments WHERE parent_id ='$photoId'";
        $result_comment_count = execute_query($query_comment_count, false, "select");
        $result_comment_count['totalcomment'] = isset($result_comment_count['totalcomment']) && ($result_comment_count['totalcomment']) ? $result_comment_count['totalcomment'] : 0;
        return $result_comment_count['totalcomment'];
    }

    /*
     * Function:display_comment_on_photo_valid($xmlrequest)
     * Description :to validate Photo and User.
     * Parameters: $xmlrequest=>Request sent by User.
      Return:all Boolean Array.
     *  */

    function display_comment_on_photo_valid($xmlrequest) {
        $userId = mysql_real_escape_string($xmlrequest['DisplayCommentOnPhoto']['userId']);
        $photoId = mysql_real_escape_string($xmlrequest['DisplayCommentOnPhoto']['photoId']);

        $query_user = "SELECT COUNT(*) FROM members WHERE mem_id='$userId'";
        if (DEBUG)
            writelog("profile.class.php :: display_comment_on_photo_valid() :: query:", $query_user, false);

        $result_user = execute_query($query_user, false);

        $query_photo = "SELECT COUNT(*) FROM photo_album WHERE photo_id='$photoId'";
        if (DEBUG)
            writelog("profile.class.php :: display_comment_on_photo_valid() :: query:", $query_photo, false);

        $result_photo = execute_query($query_photo, false);
        $error['successful'] = isset($result_user['COUNT(*)']) && isset($result_photo['COUNT(*)']) && ($result_user['COUNT(*)']) && ($result_photo['COUNT(*)']) ? true : false;
        if (DEBUG)
            writelog("Profile:display_comment_on_photo_valid", $error, true);
        return $error;
    }

    /*
     * Function:full_screen_photo($xmlrequest)
     * Description: to Display full size image.
     * Parameters: $xmlrequest=>request sent by user.
      Return:Array.
     *  */

    function full_screen_photo($xmlrequest) {
        $albumId = mysql_real_escape_string($xmlrequest['FullScreenPhoto']['albumId']);
        $photoId = mysql_real_escape_string($xmlrequest['FullScreenPhoto']['photoId']);
        $full_size_photo = array();
        $query_photo = "SELECT photo_album.photo_id, photo_album.photo FROM photo_album WHERE photo_id='$photoId' AND album_id='$albumId'";
        if (DEBUG)
            writelog("profile.class.php :: full_screen_photo() :: query:", $query_photo, false);
        $full_size_photo = execute_query($query_photo, false);
        if (DEBUG)
            writelog("Profile:full_screen_photo", $full_size_photo, true);
        return $full_size_photo;
    }

    /*
     * Function:full_screen_photo_valid($xmlrequest)
     * Description :to vaidate Photo,album,user.
     * Parameters: $xmlrequest=>request sent by user.
      Return:boolean Array.
     *  */

    function full_screen_photo_valid($xmlrequest) {
        $userId = mysql_real_escape_string($xmlrequest['FullScreenPhoto']['userId']);
        $albumId = mysql_real_escape_string($xmlrequest['FullScreenPhoto']['albumId']);
        $photoId = mysql_real_escape_string($xmlrequest['FullScreenPhoto']['photoId']);
        $error = array();
        $query_photo = "SELECT COUNT(*) FROM photo_album WHERE photo_id='$photoId' AND album_id='$albumId'";
        if (DEBUG)
            writelog("profile.class.php :: full_screen_photo_valid() :: query:", $query_photo, false);

        $result_photo = execute_query($query_photo, false);

        $query_user = "SELECT COUNT(*) FROM albums WHERE id='$albumId' AND mem_id='$userId'";
        if (DEBUG)
            writelog("profile.class.php :: full_screen_photo_valid() :: query:", $query_user, false);

        $result_user = execute_query($query_user, false);

        $error['successful'] = isset($result_photo['COUNT(*)']) && isset($result_user['COUNT(*)']) && ($result_photo['COUNT(*)']) && ($result_user['COUNT(*)']) ? true : false;
        if (DEBUG)
            writelog("Profile:full_screen_photo_valid()", $error, true);
        return $error;
    }

    /*
     * Function:tag_photo($xmlrequest)
     * Description :to Tag Entourage on a photo.
     * Parameters: $xmlrequest=>request sent by user.
      Return:boolean Array.
     *  */

    function tag_photo($xmlrequest) {
        $userId = mysql_real_escape_string($xmlrequest['TagPhoto']['userId']);
        $userName = mysql_real_escape_string($xmlrequest['TagPhoto']['userName']);
        $albumId = mysql_real_escape_string($xmlrequest['TagPhoto']['albumId']);
        $photoId = mysql_real_escape_string($xmlrequest['TagPhoto']['photoId']);
        $time = mysql_real_escape_string($xmlrequest['TagPhoto']['time']);
        $tags = array();

        $tags = $xmlrequest['TagPhoto']['Tags'];

        $query_photo = "SELECT photo_id,photo FROM photo_album WHERE photo_id='$photoId' AND album_id='$albumId'";

        $result_photo = execute_query($query_photo, false);
        $annotation_id = explode("/", $result_photo['photo']);
        $path = 'http://www.socialnightlife.com/' . $result_photo['photo'];
        list($width, $height) = getimagesize($path);
        $count = count(array_keys($tags));

	$userTimezone = new DateTimeZone('America/Chicago');
        $myDateTime = new DateTime("$time");
        $offset = $userTimezone->getOffset($myDateTime);
        $date = $myDateTime->format('U') + $offset;
//        $date = time();
        for ($i = 0; $i < $count; $i++) {
            $str = '';
            $str_tagged_photo = '';
            //for storing (x,y)in DB.
            $annotation_boundingbox = "";
            $x1 = (int) ($width * $tags[$i]['x1']) / 100;
            $y1 = (int) ($height * $tags[$i]['y1']) / 100;
            //$x1 = $x1 - 5;
            //$y1 = $y1 - 5;
            //$tags[$i]['x2'] = $x1 + 5;
            //$tags[$i]['y2'] = $y1 + 5;
            $tags[$i]['x2'] = 52;
            $tags[$i]['y2'] = 52;

            $annotation_boundingbox = (ceil($x1) - 26) . "," . (ceil($y1) - 26) . "," . $tags[$i]['x2'] . "," . $tags[$i]['y2'];

            $str = "(" . "'" . $result_photo['photo'] . "'" . "," . "'" . $photoId . "'" . "," . "'" . "'" . "," . "'" . $userId . "'" . "," . "'" . $tags[$i]['entourageId'] . "'" . "," . "'" . $date . "'" . "," . "'" . $date . "'" . "," . "'" . "'" . "," . "'" . $annotation_id[1] . "'" . "," . "'" . $tags[$i]['entourageName'] . "'" . "," . "'" . $userName . "'" . "," . "'" . $annotation_boundingbox . "'" . "," . "'" . "'" . "," . "'" . "'" . ")";
            if (isset($str)) {
                $query = "INSERT INTO fn_annotation_rows(fn_annotation_rows.file, fn_annotation_rows.image_id, fn_annotation_rows.user_id, fn_annotation_rows.mem_id, fn_annotation_rows.frnd_id, fn_annotation_rows.added, fn_annotation_rows.modified,fn_annotation_rows.annotation,fn_annotation_rows.annotation_id,fn_annotation_rows.annotation_title,fn_annotation_rows.annotation_author,fn_annotation_rows.annotation_boundingbox,fn_annotation_rows.annotation_content,fn_annotation_rows.username)VALUE $str ";
                if (DEBUG)
                    writelog("profile.class.php :: tag_photo() :: query:", $query, false);

                $result = execute_query($query, false, "insert");
                $result['count'] = isset($result['count']) && ($result['count']) ? $result['count'] : NULL;
                $error = error_CRUD($xmlrequest, $result['count']);
                if ((isset($error['TagPhoto']['successful_fin'])) && (!$error['TagPhoto']['successful_fin'])) {
                    return $error;
                }
                $annotation_id = $result['last_id'];
                $str_tagged_photo = "(" . "'" . $userId . "'" . "," . "'" . $tags[$i]['entourageId'] . "'" . "," . "'" . $result_photo['photo_id'] . "'" . "," . "'" . $annotation_id . "'" . "," . "'" . "0" . "'" . ")";

                $query_tagged_photo = "INSERT INTO tagged_photos(tagged_by,tagged_to,image,annotation_id,approved)VALUES $str_tagged_photo";
                $result_tagged_photo = execute_query($query_tagged_photo, false, "insert");
//push notification
		$subject = "<b>" . getname($userId) . "</b> tagged you in this photo";
                $body = '<a href="index.php?pg=photos&pid=' . $result_photo['photo_id'] . '&usr=' . $userId . '&s=v"><img src=' . $result_photo['photo'] . ' class="img_border" width="100" height="100"></a>';
                $query_for_message_system = execute_query("INSERT INTO messages_system (mes_id,mem_id,frm_id,subject,body,type,new,folder,messages_system.date,special,messages_system.read,update_date)VALUES('','$userId','" . $tags[$i]['entourageId'] . "','$subject','$body','tagged','','inbox','$date','$annotation_id','','0')", true, "insert");
                push_notification('tag_photo', $userId, $tags[$i]['entourageId']);
                $result_tagged_photo['count'] = isset($result_tagged_photo['count']) && ($result_tagged_photo['count']) ? $result_tagged_photo['count'] : NULL;
                $error = error_CRUD($xmlrequest, $result_tagged_photo['count']);
                if ((isset($error['TagPhoto']['successful_fin'])) && (!$error['TagPhoto']['successful_fin'])) {
                    return $error;
                }
            }
        }
        if (DEBUG)
            writelog("Profile:tag_photo()", $error, true);
        return $error;
    }

    /*
     * Function:tag_photo_valid($xmlrequest)
     * Description: to Validate Photo,user,album.
     * Parameters: $xmlrequest=>request sent by user.
      Return:boolean Array.
     *  */

    function tag_photo_valid($xmlrequest) {
        if ((isset($xmlrequest['TagPhoto']['userId'])) && (isset($xmlrequest['TagPhoto']['albumId'])) && (isset($xmlrequest['TagPhoto']['photoId']))) {
            $userId = mysql_real_escape_string($xmlrequest['TagPhoto']['userId']);
            $albumId = mysql_real_escape_string($xmlrequest['TagPhoto']['albumId']);
            $photoId = mysql_real_escape_string($xmlrequest['TagPhoto']['photoId']);
        }
        if ((isset($xmlrequest['TagsOnPhoto']['userId'])) && (isset($xmlrequest['TagsOnPhoto']['albumId'])) && (isset($xmlrequest['TagsOnPhoto']['photoId']))) {
            $userId = mysql_real_escape_string($xmlrequest['TagsOnPhoto']['userId']);
            $albumId = mysql_real_escape_string($xmlrequest['TagsOnPhoto']['albumId']);
            $photoId = mysql_real_escape_string($xmlrequest['TagsOnPhoto']['photoId']);
        }
        $query_user = "SELECT COUNT(*) FROM members WHERE mem_id='$userId'";
        if (DEBUG)
            writelog("profile.class.php :: tag_photo_valid() :: query:", $query_user, false);
        $result_user = execute_query($query_user, false);
        $query_photo = "SELECT COUNT(*) FROM photo_album WHERE photo_id='$photoId' AND album_id='$albumId'";
        if (DEBUG)
            writelog("profile.class.php :: tag_photo_valid() :: query:", $query_photo, false);
        $result_photo = execute_query($query_photo, false);
        $error['successful'] = isset($result_photo['COUNT(*)']) && isset($result_user['COUNT(*)']) && ($result_photo['COUNT(*)']) && ($result_user['COUNT(*)']) ? true : false;
        if (DEBUG)
            writelog("Profile:tag_photo_valid()", $error, true);
        return $error;
    }


    /* Function:tags_on_photo($xmlrequest)
     * Description :to Dispaly tags on a Photo.
     * Parameters: $xmlrequest=>request sent by user.
      Return:Array containing tags .
     *  */

    function tags_on_photo($xmlrequest) {
        $userId = mysql_real_escape_string($xmlrequest['TagsOnPhoto']['userId']);
        $albumId = mysql_real_escape_string($xmlrequest['TagsOnPhoto']['albumId']);
        $photoId = mysql_real_escape_string($xmlrequest['TagsOnPhoto']['photoId']);
        $result_tag = array();

        $query_photo = "SELECT CONCAT('http://www.socialnightlife.com/',photo_album.photo) as photo FROM photo_album WHERE photo_id='$photoId' AND album_id='$albumId'";
        $result_photo = execute_query($query_photo, false, "select");

        $path = $result_photo['photo'];
        list($width, $height) = getimagesize($path);
//        print_r($width);
//        print_r($height);
        $width_device = 320;
        $height_device = ($height * 320) / $width;
//        echo $query_tag = "select id,annotation_boundingbox,image_id,annotation_title,frnd_id from fn_annotation_rows where image_id = '" . $photoId . "' and id in(select annotation_id from tagged_photos)";
        $query_tag = "SELECT far.id,far.annotation_boundingbox,far.image_id,far.annotation_title,far.frnd_id FROM fn_annotation_rows AS far,tagged_photos AS tp WHERE far.image_id = '" . $photoId . "' AND far.id = tp.annotation_id;";
        if (DEBUG)
            writelog("profile.class.php :: tags_on_photo() :: query:", $query_tag, false);
        $result_tag = execute_query($query_tag, true);
        $count = isset($result_tag['count']) && ( $result_tag['count']) ? $result_tag['count'] : NULL;
        for ($i = 0; $i < $count; $i++) {
            $cords = explode(',', $result_tag[$i]['annotation_boundingbox']);
            $result_tag[$i]['fn_id'] = $result_tag[$i]['id'];
            $result_tag[$i]['x1'] = $cords[0];
            $result_tag[$i]['y1'] = $cords[1];
            $result_tag[$i]['x2'] = $cords[2];
            $result_tag[$i]['y2'] = $cords[3];

            $result_tag[$i]['x1'] = (($width_device) * ($result_tag[$i]['x1'])) / $width; // + 5;
            $result_tag[$i]['y1'] = (($height_device) * ($result_tag[$i]['y1'])) / $height; // + 5;

            $result_tag[$i]['x2'] = 0;
            $result_tag[$i]['y2'] = 0;
        }
        // if (DEBUG)
        //   writelog("Profile:tags_on_photo()", $result_tag, true);
        return $result_tag;
    }

    //remove tag from account photo album
    function remove_tags_from_photos($xmlrequest) {

        $userId = mysql_real_escape_string($xmlrequest['RemoveTag']['userId']);
        $photoId = mysql_real_escape_string($xmlrequest['RemoveTag']['photoId']);
        $tags = $xmlrequest['RemoveTag']['Tags'];
        $tagsRemove = array();
        $query_photo = "SELECT COUNT(*) FROM fn_annotation_rows WHERE image_id='$photoId' AND (mem_id ='$userId' || frnd_id='$userId')";
        $result_photo = execute_query($query_photo, false, "select");

        if (isset($result_photo['COUNT(*)']) && ($result_photo['COUNT(*)'])) {
            $count = count(array_keys($tags));
            for ($i = 0; $i < $count; $i++) {
                $id = $tags[$i]['id'];
                $query_delete_from_db = "DELETE far.*,tp.* FROM fn_annotation_rows AS far,tagged_photos AS tp WHERE far.image_id = '$photoId' AND far.id='$id' AND far.id = tp.id AND far.mem_id = tp.tagged_by AND far.frnd_id = tp.tagged_to";
                $exe_delete_from_db = execute_query($query_delete_from_db, true, "delete");
                $delete_from_mes_sys = execute_query("DELETE FROM messages_system WHERE special='$id'", true, "delete");
                $affected_row_photo_tag = $exe_delete_from_db['count'];
                $error = error_CRUD($xmlrequest, $affected_row_photo_tag);
            }
        }
        if ((isset($error['RemoveTag']['successful_fin'])) && ($error['RemoveTag']['successful_fin'])) {

            return TRUE;
        } else {
            return FALSE;
        }
    }

    /* Function:album_photo_upload($xmlrequest)
     * Description: to upload a photo in album.
     * Parameters: $xmlrequest=>request sent by user.
      Return:Array containing status of uploadedphoto.
     *  */

    function album_photo_upload($xmlrequest) {
        if (DEBUG)
            writelog("profile.class.php :: album_photo_upload() : ", "Start Here ", false);

        $error = array();
        $error = photo_upload($xmlrequest);
        if (DEBUG)
            writelog("profile.class.php :: album_photo_upload() : ", $error, true);
        if (DEBUG)
            writelog("profile.class.php :: album_photo_upload() : ", "End Here ", false);

        return $error;
    }

    /* Function:album_photo_upload_valid($xmlrequest)
     * Description: to validate photo upload.
     * Parameters: $xmlrequest=>request sent by user.
      Return:Array.
     *  */

    function album_photo_upload_valid($xmlrequest) {
        if (DEBUG)
            writelog("profile.class.php :: album_photo_upload_valid() : ", "Start Here ", false);

        $error = array();
        $error = photo_upload_valid($xmlrequest);
        if (DEBUG) {
            writelog("profile.class.php :: album_photo_upload_valid() : ", $error, true);
            writelog("profile.class.php :: album_photo_upload_valid() : ", "End Here ", false);
        }
        return $error;
    }

    /* Function:create_photo_album($xmlrequest)
     * Description: to create new album.
     * Parameters: $xmlrequest=>request sent by user.
      Return:Boolean Array.
     */

    function create_photo_album($xmlrequest) {
        $userId = mysql_real_escape_string($xmlrequest['CreatePhotoAlbum']['userId']);
        $privacySetting = mysql_real_escape_string($xmlrequest['CreatePhotoAlbum']['privacySetting']);
        $title = mysql_real_escape_string($xmlrequest['CreatePhotoAlbum']['title']);
        $time = mysql_real_escape_string($xmlrequest['CreatePhotoAlbum']['time']);

        $error = array();
        if ($privacySetting) {
            $privacySetting = 'public';
        }
	$userTimezone = new DateTimeZone('America/Chicago');
        $myDateTime = new DateTime("$time");
        $offset = $userTimezone->getOffset($myDateTime);
        $date = $myDateTime->format('U') + $offset;
//        $date = time();
        $query = "INSERT INTO albums(mem_id, albums.type, title, albums.desc, album_cover, create_date)VALUE('$userId', '$privacySetting', '$title', '', '', '$date')";
        if (DEBUG)
            writelog("profile.class.php :: create_photo_album() : ", $query, false);

        $result = execute_query($query, false, "insert");
        $result['count'] = isset($result['count']) && ($result['count']) ? $result['count'] : 0;
        $error['last_id'] = isset($result['last_id']) && ($result['last_id']) ? $result['last_id'] : 0;
        $error = error_CRUD($xmlrequest, $result['count']);
        if ((isset($error['CreatePhotoAlbum']['successful_fin'])) && (!$error['CreatePhotoAlbum']['successful_fin'])) {
            return $error;
        }
        if (DEBUG)
            writelog("CreatePhotoAlbum:create_photo_album:", $error, true);
        return $error;
    }

    /* Function:create_photo_album_valid($xmlrequest)
     * Description: to validate user.
     * Parameters: $xmlrequest=>request sent by user.
      Return:Boolean Array.
     *  */

    function create_photo_album_valid($xmlrequest) {
        $userId = mysql_real_escape_string($xmlrequest['CreatePhotoAlbum']['userId']);
        $error = array();
        $query = "SELECT COUNT(*) from members WHERE mem_id='$userId'";
        if (DEBUG)
            writelog("profile.class.php :: create_photo_album_valid() : ", $query, false);
        $result = execute_query($query, false);
        $error['successful'] = isset($result['COUNT(*)']) && ($result['COUNT(*)']) ? true : false;
        return $error;
    }

    /*     * *****************************Response String***************************** */
    /* Function:createAlbum($xmlrequest)
     * Description: to return response string after creation of album.
     * Parameters: $xmlrequest=>request sent by user.
     *              $response_message=>boolean array
      Return:string.
     *  */

    function createAlbum($response_message, $xmlrequest) {
        if (isset($response_message['CreatePhotoAlbum']['SuccessCode']) && ( $response_message['CreatePhotoAlbum']['SuccessCode'] == '000')) {
            $userinfo = array();
            $userinfo = $this->create_photo_album($xmlrequest);
            $last_id = isset($userinfo['last_id']) ? $userinfo['last_id'] : null;
            if ((isset($userinfo['CreatePhotoAlbum']['successful_fin'])) && (!$userinfo['CreatePhotoAlbum']['successful_fin'])) {
                $obj_error = new Error();
                $response_message = $obj_error->error_type("CreatePhotoAlbum", $userinfo);

                $userinfocode = $response_message['CreatePhotoAlbum']['ErrorCode'];
                $userinfodesc = $response_message['CreatePhotoAlbum']['ErrorDesc'];
                $response_mess = $response_mess = get_response_string("CreatePhotoAlbum", $userinfocode, $userinfodesc);
                return getValidJSON($response_mess);
            }
            $userinfocode = $response_message['CreatePhotoAlbum']['SuccessCode'];
            $userinfodesc = $response_message['CreatePhotoAlbum']['SuccessDesc'];
            $response_str = response_repeat_string();
            $response_mess = '{
   ' . $response_str . '
   "CreatePhotoAlbum":{
      "lastId":"' . $last_id . '",
      "errorCode":"' . $userinfocode . '",
      "errorMsg":"' . $userinfodesc . '"

   }
   }
 	';
        } else {
            $userinfocode = $response_message['CreatePhotoAlbum']['ErrorCode'];
            $userinfodesc = $response_message['CreatePhotoAlbum']['ErrorDesc'];
            $response_mess = $response_mess = get_response_string("CreatePhotoAlbum", $userinfocode, $userinfodesc);
        }
        if (DEBUG)
            writelog("Response:hotPressPostComment():", $response_mess, false);
        return getValidJSON($response_mess);
    }

    /* Function:photos($xmlrequest)
     * Description:to return response of album details.
     * Parameters: $xmlrequest=>request sent by user.
     *              $response_message=>boolean array.
      Return:string.
     *  */

    function photos($response_message, $xmlrequest) {
        if (isset($response_message['Photos']['SuccessCode']) && ( $response_message['Photos']['SuccessCode'] == '000')) {
            $photo_info = array();
            $photo_info = $this->profile_photo($xmlrequest);
            $userinfocode = $response_message['Photos']['SuccessCode'];
            $userinfodesc = $response_message['Photos']['SuccessDesc'];

            if (isset($xmlrequest['Photos']['userId']) && (isset($photo_info['count']))) {
                $count = $photo_info['count'];
                $postcount = 0;

                $str = $photo_info['str'];
                /*for ($i = 0; $i < $count; $i++) {
                    $check_set_var = (isset($photo_info[$i]['id']) ? true : "");
                    $photo_info[$i]['title'] = (isset($photo_info[$i]['title']) ? $photo_info[$i]['title'] : "");
                    $photo_info[$i]['desc'] = (isset($photo_info[$i]['desc']) ? $photo_info[$i]['desc'] : "");
                    $photo_info[$i]['album_cover'] = ((isset($photo_info[$i]['album_cover']) && (strlen($photo_info[$i]['album_cover']) > 7)) ? $this->profile_url . $photo_info[$i]['album_cover'] : $this->profile_url . "photos/1273660058.jpg");
                    $photo_info[$i]['tot_photo'] = (isset($photo_info[$i]['tot_photo']) ? $photo_info[$i]['tot_photo'] : "");

                    if (($check_set_var) && ($postcount < 20)) {
                        $str_temp = '{
            "albumId":"' .str_replace('"', '\"',$photo_info[$i]['id']). '",
            "albumTitle":"' .str_replace('"', '\"',ucfirst($photo_info[$i]['title'])). '",
            "albumImageUrl":"' .str_replace('"', '\"',$photo_info[$i]['album_cover']). '",
            "albumDescription":"' .str_replace('"', '\"',$photo_info[$i]['desc']). '",
            "albumPhotoCount":"' .str_replace('"', '\"',$photo_info[$i]['tot_photo']). '",
            "albumCreated":"' . date("j F Y", $photo_info[$i]['create_date']) . '",
            "albumCommentCount":8,
            "albumLikeCount":2
         }';
                        $postcount++;
                        $str = $str . $str_temp;
                        $str = $str . ',';
                    }
                }*/
                $str = substr($str, 0, strlen($str) - 1);
                $response_str = response_repeat_string();
                $response_mess = '
{
  ' . $response_str . '
   "Photos":{
      "errorCode":"' . $userinfocode . '",
      "errorMsg":"' . $userinfodesc . '",
      "pageNumber":1,
      "albumCount":1,
      "PhotoAlbums":[
         ' . $str . '
      ]
   }
}
';
            }
        } else {
            $userinfocode = $response_message['Photos']['ErrorCode'];
            $userinfodesc = $response_message['Photos']['ErrorDesc'];
            $response_mess = get_response_string("Photos", $userinfocode, $userinfodesc);
        }
        if (DEBUG)
            writelog("Response:photos():", $response_mess, false);
        return getValidJSON($response_mess);
    }

    /*  Function:photoAlbumDetails($xmlrequest)
     *  Description: to show all album detals.
     *  Parameters: $xmlrequest=>request sent by user.
     *               $response_message=>boolean array
      Return:string.
     *  */

    function photoAlbumDetails($response_message, $xmlrequest) {
        if (isset($response_message['PhotoAlbumDetails']['SuccessCode']) && ( $response_message['PhotoAlbumDetails']['SuccessCode'] == '000')) {
            $photo_album_details = array();
            $photo_album_details = $this->profile_album_details($xmlrequest);
            $userinfocode = $response_message['PhotoAlbumDetails']['SuccessCode'];
            $userinfodesc = $response_message['PhotoAlbumDetails']['SuccessDesc'];
            $count = isset($photo_album_details['count']) && ($photo_album_details['count']) ? $photo_album_details['count'] : 0;
            $str = '';
            for ($i = 0; $i < $count; $i++) {
                $photo_album_details[$i]['[photo_id'] = isset($photo_album_details[$i]['[photo_id']) && ($photo_album_details[$i]['[photo_id']) ? $photo_album_details[$i]['[photo_id'] : NULL;
                $photo_album_details[$i]['photo_mid'] = ((isset($photo_album_details[$i]['photo_mid'])) && (strlen($photo_album_details[$i]['photo_mid']) > 7)) ? $this->profile_url . $photo_album_details[$i]['photo_mid'] : "";
                $photo_album_details[$i]['photo'] = ((isset($photo_album_details[$i]['photo'])) && (strlen($photo_album_details[$i]['photo']) > 7)) ? $this->profile_url . $photo_album_details[$i]['photo'] : "";
                $photo_album_details['name']['title'] = isset($photo_album_details['name']['title']) && ($photo_album_details['name']['title']) ? $photo_album_details['name']['title'] : NULL;
                $photo_album_details[$i]['commentCount'] = isset($photo_album_details[$i]['commentCount']) && ($photo_album_details[$i]['commentCount'] ) ? $photo_album_details[$i]['commentCount'] : NULL;
                $photo_album_details[$i]['caption'] = isset($photo_album_details[$i]['caption']) && ($photo_album_details[$i]['caption'] ) ? $photo_album_details[$i]['caption'] : NULL;
                $str_temp = '{
            "photoId":"' .str_replace('"', '\"',$photo_album_details[$i]['photo_id']). '",
            "photoAlbumName":"' .str_replace('"', '\"',$photo_album_details['name']['title']). '",
            "commentCount":"' .str_replace('"', '\"',$photo_album_details[$i]['commentCount']). '",
            "caption":"' .str_replace('"', '\"',$photo_album_details[$i]['caption']). '",
            "photoUrl":"' .str_replace('"', '\"',$photo_album_details[$i]['photo']). '",
            "smallURL":"' .str_replace('"', '\"',$photo_album_details[$i]['photo_mid']). '"
         }';
                $str = $str . $str_temp;
                $str = $str . ',';
            }
            $str = substr($str, 0, strlen($str) - 1);
            $response_str = response_repeat_string();
            $response_mess = '
{
   ' . $response_str . '
   "PhotoAlbumDetails":{
     "errorCode":"' . $userinfocode . '",
      "errorMsg":"' . $userinfodesc . '",
      "PhotoAlbums":[
         ' . $str . '
      ]
   }
}
';
        } else {
            $userinfocode = $response_message['PhotoAlbumDetails']['ErrorCode'];
            $userinfodesc = $response_message['PhotoAlbumDetails']['ErrorDesc'];
            $response_mess = get_response_string("PhotoAlbumDetails", $userinfocode, $userinfodesc);
        }
        if (DEBUG)
            writelog("Response:photoAlbumDetails():", $response_mess, false);
        return $response_mess;
    }

    /* Function:commentOnPhoto($xmlrequest)
     * Description: to post comment on photo.
     * Parameters: $xmlrequest=>request sent by user.
      $response_message=>boolean array.
     * Return:string.
     *  */

    function commentOnPhoto($response_message, $xmlrequest) {

        if (isset($response_message['CommentOnPhoto']['SuccessCode']) && ( $response_message['CommentOnPhoto']['SuccessCode'] == '000')) {
            $userinfo = array();
            $userinfo = $this->comment_on_photo($xmlrequest);
            if ((isset($userinfo['CommentOnPhoto']['successful_fin'])) && (!$userinfo['CommentOnPhoto']['successful_fin'])) {
                $obj_error = new Error();
                $response_message = $obj_error->error_type("CommentOnPhoto", $userinfo);
                $userinfocode = $response_message['CommentOnPhoto']['ErrorCode'];
                $userinfodesc = $response_message['CommentOnPhoto']['ErrorDesc'];
                $response_mess = $response_mess = get_response_string("CommentOnPhoto", $userinfocode, $userinfodesc);
                return $response_mess;
            }
            $userinfocode = $response_message['CommentOnPhoto']['SuccessCode'];
            $userinfodesc = $response_message['CommentOnPhoto']['SuccessDesc'];
            $response_str = response_repeat_string();


            $response_mess = '
{
  ' . $response_str . '
   "CommentOnPhoto":{
      "errorCode":"' . $userinfocode . '",
      "errorMsg":"' . $userinfodesc . '"
   }
}
';
        } else {
            $userinfocode = $response_message['CommentOnPhoto']['ErrorCode'];
            $userinfodesc = $response_message['CommentOnPhoto']['ErrorDesc'];
            $response_mess = get_response_string("CommentOnPhoto", $userinfocode, $userinfodesc);
        }
        if (DEBUG)
            writelog("Response:commentOnPhoto():", $response_mess, false);
        return getValidJSON($response_mess);
    }

    /* Function:makeProfilePhoto($xmlrequest)
     * Description :to change profile Photo.
     * Parameters: $xmlrequest=>request sent by user.
     *              $response_message=>boolean array
      Return:string.
     *  */

    function makeProfilePhoto($response_message, $xmlrequest) {
        
        if (isset($response_message['MakeProfilePhoto']['SuccessCode']) && ( $response_message['MakeProfilePhoto']['SuccessCode'] == '000')) {
           
            $userinfo = array();
            $userinfo = $this->make_profile_photo($xmlrequest);
           
            if ((isset($userinfo['MakeProfilePhoto']['successful_fin'])) && (!$userinfo['MakeProfilePhoto']['successful_fin'])) {
                $obj_error = new Error();
                $response_message = $obj_error->error_type("MakeProfilePhoto", $userinfo);

                $userinfocode = $response_message['MakeProfilePhoto']['ErrorCode'];
                $userinfodesc = $response_message['MakeProfilePhoto']['ErrorDesc'];
                $response_mess = $response_mess = get_response_string("MakeProfilePhoto", $userinfocode, $userinfodesc);
                return $response_mess;
            }

            $userinfocode = $response_message['MakeProfilePhoto']['SuccessCode'];
            $userinfodesc = $response_message['MakeProfilePhoto']['SuccessDesc'];
            $response_str = response_repeat_string();
            $response_mess = '
{
  ' . $response_str . '
   "MakeProfilePhoto":{
      "errorCode":"' . $userinfocode . '",
      "errorMsg":"' . $userinfodesc . '"
   }
}
';
        } else {
            $userinfocode = $response_message['MakeProfilePhoto']['ErrorCode'];
            $userinfodesc = $response_message['MakeProfilePhoto']['ErrorDesc'];
            $response_mess = get_response_string("MakeProfilePhoto", $userinfocode, $userinfodesc);
        }
        if (DEBUG)
            writelog("Response:makeProfilePhoto():", $response_mess, false);
        return getValidJSON($response_mess);
    }

    /* Function:deletePhoto($xmlrequest)
     * Description: to delete Photo.
      Parameters: $xmlrequest=>request sent by user.,
     *            $response_message=>boolean array
      Return:string.
     */

    function deletePhoto($response_message, $xmlrequest) {
        if (isset($response_message['DeletePhoto']['SuccessCode']) && ( $response_message['DeletePhoto']['SuccessCode'] == '000')) {
            $userinfo = array();
            $photo_album_details = $this->delete_photo($xmlrequest);

            if ((isset($userinfo['DeletePhoto']['successful_fin'])) && (!$userinfo['DeletePhoto']['successful_fin'])) {
                $obj_error = new Error();
                $response_message = $obj_error->error_type("DeletePhoto", $userinfo);

                $userinfocode = $response_message['DeletePhoto']['ErrorCode'];
                $userinfodesc = $response_message['DeletePhoto']['ErrorDesc'];
                $response_mess = $response_mess = get_response_string("DeletePhoto", $userinfocode, $userinfodesc);
                return getValidJSON($response_mess);
            }
            $userinfocode = $response_message['DeletePhoto']['SuccessCode'];
            $userinfodesc = $response_message['DeletePhoto']['SuccessDesc'];
            $response_str = response_repeat_string();
            $response_mess = '
{
   ' . $response_str . '
   "DeletePhoto":{
       "errorCode":"' . $userinfocode . '",
       "errorMsg":"' . $userinfodesc . '"
   }
}
';
        } else {
            $userinfocode = $response_message['DeletePhoto']['ErrorCode'];
            $userinfodesc = $response_message['DeletePhoto']['ErrorDesc'];
            $response_mess = get_response_string("DeletePhoto", $userinfocode, $userinfodesc);
        }
        if (DEBUG)
            writelog("Response:deletePhoto():", $response_mess, false);
        return getValidJSON($response_mess);
    }

    /* Function:fullScreenPhoto($xmlrequest)
     * Description: to get full screen image.
      Parameters: $xmlrequest=>request sent by user,
     *            $response_message=>containing boolean array
      Return:string.
     */

    function fullScreenPhoto($response_message, $xmlrequest) {
        if (isset($response_message['FullScreenPhoto']['SuccessCode']) && ( $response_message['FullScreenPhoto']['SuccessCode'] == '000')) {
            $userinfo = array();
            $photo_album_details = $this->full_screen_photo($xmlrequest);

            $userinfocode = $response_message['FullScreenPhoto']['SuccessCode'];
            $userinfodesc = $response_message['FullScreenPhoto']['SuccessDesc'];
            $response_str = response_repeat_string();
            $response_mess = '
{
   ' . $response_str . '
   "FullScreenPhoto":{
      "errorCode":"' . $userinfocode . '",
      "errorMsg":"' . $userinfodesc . '",
      "photoId":"' . $photo_album_details['photo_id'] . '"
       "photoUrl":"' . $photo_album_details['photo'] . '"

   }
}
';
        } else {
            $userinfocode = $response_message['FullScreenPhoto']['ErrorCode'];
            $userinfodesc = $response_message['FullScreenPhoto']['ErrorDesc'];
            $response_mess = get_response_string("FullScreenPhoto", $userinfocode, $userinfodesc);
        }
        if (DEBUG)
            writelog("Response:fullScreenPhoto():", $response_mess, false);
        return getValidJSON($response_mess);
    }

    /* Function:tagPhoto($xmlrequest)
     * Description:to put tag on image.
      Parameters: $xmlrequest=>request sent by user,
     *            $response_message=>containing boolean array
      Return:string.
     */

    function tagPhoto($response_message, $xmlrequest) {

        if (isset($response_message['TagPhoto']['SuccessCode']) && ( $response_message['TagPhoto']['SuccessCode'] == '000')) {
            $userinfo = array();
            $userinfo = $this->tag_photo($xmlrequest);
            if ((isset($userinfo['TagPhoto']['successful_fin'])) && (!$userinfo['TagPhoto']['successful_fin'])) {
                $obj_error = new Error();
                $response_message = $obj_error->error_type("TagPhoto", $userinfo);

                $userinfocode = $response_message['TagPhoto']['ErrorCode'];
                $userinfodesc = $response_message['TagPhoto']['ErrorDesc'];
                $response_mess = $response_mess = get_response_string("TagPhoto", $userinfocode, $userinfodesc);
                return $response_mess;
            }

            $userinfocode = $response_message['TagPhoto']['SuccessCode'];
            $userinfodesc = $response_message['TagPhoto']['SuccessDesc'];
            $response_str = response_repeat_string();


            $response_mess = '
{
  ' . $response_str . '
   "TagPhoto":{
      "errorCode":"' . $userinfocode . '",
      "errorMsg":"' . $userinfodesc . '"
   }
}
';
        } else {
            $userinfocode = $response_message['TagPhoto']['ErrorCode'];
            $userinfodesc = $response_message['TagPhoto']['ErrorDesc'];
            $response_mess = get_response_string("TagPhoto", $userinfocode, $userinfodesc);
        }
        if (DEBUG)
            writelog("Response:tagPhoto():", $response_mess, false);
        return getValidJSON($response_mess);
    }

    /* Function:tagsOnPhoto($xmlrequest)
     * Description: to get tags on photo.
      Parameters: $xmlrequest=>request sent by user,
     *            $response_message=>containing boolean array
      Return:string.
     */

//remove tag from photo

    function removeTagsfromPhotos($response_message, $xmlrequest) {
        global $return_codes;
        $tags_on_photo = $this->remove_tags_from_photos($xmlrequest);

        if ($tags_on_photo === TRUE) {
            $response_str = response_repeat_string();
            $response_mess = '
                {
                   ' . $response_str . '
                                "RemoveTag":{
                                "errorCode":"' . $return_codes["RemoveTag"]["SuccessCode"] . '",
                                "errorMsg":"' . $return_codes["RemoveTag"]["SuccessDesc"] . '"
                      }
                }';
        } else {
            $response_mess = '
		{
                   ' . response_repeat_string() . '
                          "RemoveTag":{
                          "errorCode":"' . $return_codes["RemoveTag"]["NoRecordErrorCode"] . '",
                          "errorMsg":"' . $return_codes["RemoveTag"]["NoRecordErrorDesc"] . '"
                   }
            }';
        }
        return getValidJSON($response_mess);
    }

    function tagsOnPhoto($response_message, $xmlrequest) {
//        $mtime = microtime();
//        $mtime = explode(" ", $mtime);
//        $mtime = $mtime[1] + $mtime[0];
//        $starttime = $mtime;

        $tags_on_photo = array();
        global $return_codes;
        $tags_on_photo = $this->tags_on_photo($xmlrequest);
        $count = isset($tags_on_photo['count']) && ($tags_on_photo['count']) ? $tags_on_photo['count'] : NULL;
        if (isset($count) && ($count)) {
            $str = '';
            for ($i = 0; $i < $count; $i++) {
                $tags_on_photo[$i]['x1'] = (isset($tags_on_photo[$i]['x1']) ? $tags_on_photo[$i]['x1'] : "");
                $tags_on_photo[$i]['x2'] = (isset($tags_on_photo[$i]['x2']) ? $tags_on_photo[$i]['x2'] : "");
                $tags_on_photo[$i]['y1'] = (isset($tags_on_photo[$i]['y1']) ? $tags_on_photo[$i]['y1'] : "");
                $tags_on_photo[$i]['y2'] = (isset($tags_on_photo[$i]['y2']) ? $tags_on_photo[$i]['y2'] : "");
                $tags_on_photo[$i]['frnd_id'] = (isset($tags_on_photo[$i]['frnd_id']) ? $tags_on_photo[$i]['frnd_id'] : "");
                $tags_on_photo[$i]['annotation_title'] = (isset($tags_on_photo[$i]['annotation_title']) ? $tags_on_photo[$i]['annotation_title'] : "");
                $check_set_var = (isset($tags_on_photo[$i]['image_id']) ? TRUE : FALSE);
                if ($check_set_var === TRUE) {
                    $photoid = isset($tags_on_photo[$i]['image_id']) && ($tags_on_photo[$i]['image_id']) ? $tags_on_photo[$i]['image_id'] : NULL;
                    $str_temp = '{
                   "id":"' . $tags_on_photo[$i]['fn_id'] . '"
                   "x1":"' . $tags_on_photo[$i]['x1'] . '",
                   "x2":"' . $tags_on_photo[$i]['x2'] . '",
                   "y1":"' . $tags_on_photo[$i]['y1'] . '",
                   "y2":"' . $tags_on_photo[$i]['y2'] . '",
                   "entourageId":"' .str_replace('"', '\"',$tags_on_photo[$i]['frnd_id']). '",
                   "entourageName":"' .str_replace('"', '\"',$tags_on_photo[$i]['annotation_title']). '"
                  
                }'; // "id":"' . $tags_on_photo[$i]['fn_id'] . '"
                    $str = $str . $str_temp;
                    $str = $str . ',';
                } else {
                    unset($check_set_var);
                }
            }
            $str = substr($str, 0, strlen($str) - 1);
            $userinfocode = $response_message['TagsOnPhoto']['SuccessCode'];
            $userinfodesc = $response_message['TagsOnPhoto']['SuccessDesc'];
            $response_str = response_repeat_string();
            $response_mess = '
{
  ' . $response_str . '
   "TagsOnPhoto":{

            "photoId":"' . $photoid . '",
             "Tags":[
                ' . $str . '
                    ],
             "errorCode":"' . $userinfocode . '",
             "errorMsg":"' . $userinfodesc . '"
   }
}
';
        } else {
            if (isset($response_message['TagsOnPhoto']['ErrorCode']) && ($response_message['TagsOnPhoto']['ErrorCode'])) {
                $userinfocode = $response_message['TagsOnPhoto']['ErrorCode'];
                $userinfodesc = $response_message['TagsOnPhoto']['ErrorDesc'];
                $response_mess = get_response_string("TagsOnPhoto", $userinfocode, $userinfodesc);
            } else {
                $response_mess = '
                {
   ' . response_repeat_string() . '
   "TagsOnPhoto":{
      "errorCode":"' . $return_codes["TagsOnPhoto"]["NoRecordErrorCode"] . '",
      "errorMsg":"' . $return_codes["TagsOnPhoto"]["NoRecordErrorDesc"] . '"

   }
	  }';
            }
        }
        if (DEBUG)
            writelog("Response:tagsOnPhoto():", $response_mess, false);
//        $mtime = microtime();
//        $mtime = explode(" ", $mtime);
//        $mtime = $mtime[1] + $mtime[0];
//        $endtime = $mtime;
//        $totaltime = ($endtime - $starttime);
//        echo "This page was created in " . $totaltime . " seconds";
        return getValidJSON($response_mess);
    }

    /* Function:displayCommentOnPhoto($response_message, $xmlrequest)
     * Description: to display comment on photo.
      Parameters: $xmlrequest=>request sent by user,
     *            $response_message=>containing boolean array
      Return:string.
     */

    function displayCommentOnPhoto($response_message, $xmlrequest) {

        if (isset($response_message['DisplayCommentOnPhoto']['SuccessCode']) && ( $response_message['DisplayCommentOnPhoto']['SuccessCode'] == '000')) {
            $commentsinfo = array();
            $pagenumber = $xmlrequest['DisplayCommentOnPhoto']['pageNumber'];
            $commentsinfo = $this->display_comment_on_photo($xmlrequest, $pagenumber, 20);
            $commentsinfo['Comment'] = bubble_sort($commentsinfo['Comment']);
            $userinfocode = $response_message['DisplayCommentOnPhoto']['SuccessCode'];
            $userinfodesc = $response_message['DisplayCommentOnPhoto']['SuccessDesc'];
            $currentcommentcount = isset($commentsinfo['Comment']['count']) && ($commentsinfo['Comment']['count']) ? $commentsinfo['Comment']['count'] : 0;
            $totalcommentcount = isset($commentsinfo['Comment']['totalrecords']) && ($commentsinfo['Comment']['totalrecords']) ? $commentsinfo['Comment']['totalrecords'] : 0;
            //$postcount = 0;
            $str = '';
            for ($i = 0; $i < $currentcommentcount; $i++) {
                $commentsinfo['Comment'][$i]['comment'] = strip_tags($commentsinfo['Comment'][$i]['comment'], "<br />");
                $commentsinfo['Comment'][$i]['comment'] = str_replace(array("\r\n", "\r", "\n", "<br />", "<br/>"), "\\n", $commentsinfo['Comment'][$i]['comment']);
                $commentsinfo['Comment'][$i]['comment'] = str_replace(array("\""), "", $commentsinfo['Comment'][$i]['comment']);
                $commentsinfo['Comment'][$i]['User']['photo_b_thumb'] = ((isset($commentsinfo['Comment'][$i]['User']['is_facebook_user'])) && (strlen($commentsinfo['Comment'][$i]['User']['photo_b_thumb']) > 7) && ($commentsinfo['Comment'][$i]['User']['is_facebook_user'] == 'y' || $commentsinfo['Comment'][$i]['User']['is_facebook_user'] == 'Y')) ? $commentsinfo['Comment'][$i]['User']['photo_b_thumb'] : ((isset($commentsinfo['Comment'][$i]['User']['photo_b_thumb']) && (strlen($commentsinfo['Comment'][$i]['User']['photo_b_thumb']) > 7)) ? $this->profile_url . $commentsinfo['Comment'][$i]['User']['photo_b_thumb'] : $this->profile_url . default_images($commentsinfo['Comment'][$i]['User']['gender'], $commentsinfo['Comment'][$i]['User']['profile_type']));
                $postVia = ((isset($commentsinfo['Comment'][$i]['post_via'])) && ($commentsinfo['Comment'][$i]['post_via'])) ? "iPhone" : "";
                $date = time_difference($commentsinfo['Comment'][$i]['date']); // date("d/m/y : H:i:s", $commentsinfo['Comment'][$i]['date'])
                $str_temp = ' {
            "postId":"' .str_replace('"', '\"',$commentsinfo['Comment'][$i]['id']). '",
            "totalComment":"' .str_replace('"', '\"',$commentsinfo['Comment'][$i]['total_comment']). '",
            "authorID":"' .str_replace('"', '\"',$commentsinfo['Comment'][$i]['mem_id']). '",
            "authorProfileImgURL":"' .str_replace('"', '\"',$commentsinfo['Comment'][$i]['User']['photo_b_thumb']). '",
            "authorName":"' .str_replace('"', '\"',$commentsinfo['Comment'][$i]['User']['profilenam']). '",
            "gender":"' .str_replace('"', '\"',$commentsinfo['Comment'][$i]['User']['gender']). '",
            "authorType":"' . $commentsinfo['Comment'][$i]['User']['profile_type'] . '",
            "postText":"' .str_replace('"', '\"',trim(preg_replace('/\s+/', ' ', $commentsinfo['Comment'][$i]['comment']))). '",
            "postTimestamp":"' . $date . '",
            "postVia":"' .str_replace('"', '\"',$postVia). '"

         }';
                //$postcount++;
                $str = $str . $str_temp;
                $str = $str . ',';
            }
            $str = substr($str, 0, strlen($str) - 1);
            $response_str = response_repeat_string();
            $response_mess = '{
   ' . $response_str . '
   "DisplayCommentOnPhoto":{
      "errorCode":"' . $userinfocode . '",
      "errorMsg":"' . $userinfodesc . '",
       "currentComments":"' . $currentcommentcount . '",
       "totalComments":"' . $totalcommentcount . '",
       "posts":[ ' . $str . ']
   }
}
';
        } else {
            $userinfocode = $response_message['DisplayCommentOnPhoto']['ErrorCode'];
            $userinfodesc = $response_message['DisplayCommentOnPhoto']['ErrorDesc'];
            $response_mess = get_response_string("DisplayCommentOnPhoto", $userinfocode, $userinfodesc);
        }
        return getValidJSON($response_mess);
    }

    /* Function:commentsOnPhotosParentComment($response_message, $xmlrequest)
     * Description: to display comment on photo's Parent comment.
      Parameters: $xmlrequest=>request sent by user,
     *            $response_message=>containing boolean array
      Return:string.
     */

    function commentsOnPhotosParentComment($response_message, $xmlrequest) {

        if (isset($response_message['CommentsOnPhotosParentComment']['SuccessCode']) && ( $response_message['CommentsOnPhotosParentComment']['SuccessCode'] == '000')) {
            $commentsinfo = array();
            $commentsinfo = $this->photo_sub_comments($xmlrequest);
            $commentsinfo['Comment'] = bubble_sort($commentsinfo['Comment']);
            $userinfocode = $response_message['CommentsOnPhotosParentComment']['SuccessCode'];
            $userinfodesc = $response_message['CommentsOnPhotosParentComment']['SuccessDesc'];
            $commentsinfo['Comment']['count'] = isset($commentsinfo['Comment']['count']) && ($commentsinfo['Comment']['count']) ? $commentsinfo['Comment']['count'] : 0;
            $postcount = 0;
            $str = '';
            for ($i = 0; $i < $commentsinfo['Comment']['count']; $i++) {
                $commentsinfo['Comment'][$i]['comment'] = strip_tags($commentsinfo['Comment'][$i]['comment'], "<br />");
                $commentsinfo['Comment'][$i]['comment'] = str_replace(array("\r\n", "\r", "\n", "<br />", "<br/>"), "\\n", $commentsinfo['Comment'][$i]['comment']);
                $commentsinfo['Comment'][$i]['comment'] = str_replace(array("\""), "", $commentsinfo['Comment'][$i]['comment']);
                $commentsinfo['Comment'][$i]['photo_b_thumb'] = ((isset($commentsinfo['Comment'][$i]['is_facebook_user'])) && (strlen($commentsinfo['Comment'][$i]['photo_b_thumb']) > 7) && ($commentsinfo['Comment'][$i]['is_facebook_user'] == 'y' || $commentsinfo['Comment'][$i]['is_facebook_user'] == 'Y')) ? $commentsinfo['Comment'][$i]['photo_b_thumb'] : ((isset($commentsinfo['Comment'][$i]['photo_b_thumb']) && (strlen($commentsinfo['Comment'][$i]['photo_b_thumb']) > 7)) ? $this->profile_url . $commentsinfo['Comment'][$i]['photo_b_thumb'] : $this->profile_url . default_images($commentsinfo['Comment'][$i]['gender'], $commentsinfo['Comment'][$i]['profile_type']));
                $postVia = ((isset($commentsinfo['Comment'][$i]['post_via'])) && ($commentsinfo['Comment'][$i]['post_via'])) ? "iPhone" : "";
                $date = time_difference($commentsinfo['Comment'][$i]['date']); // date("d/m/y : H:i:s", $commentsinfo['Comment'][$i]['date'])
                $str_temp = ' {
            "postId":"' .str_replace('"', '\"',$commentsinfo['Comment'][$i]['id']). '",
            "authorID":"' .str_replace('"', '\"',$commentsinfo['Comment'][$i]['mem_id']). '",
            "authorProfileImgURL":"' .str_replace('"', '\"',$commentsinfo['Comment'][$i]['photo_b_thumb']). '",
            "authorName":"' .str_replace('"', '\"',$commentsinfo['Comment'][$i]['profilenam']). '",
            "gender":"' .str_replace('"', '\"',$commentsinfo['Comment'][$i]['gender']). '",
            "authorType":"' .str_replace('"', '\"',$commentsinfo['Comment'][$i]['profile_type']). '",
            "postText":"' .str_replace('"', '\"',trim(preg_replace('/\s+/', ' ', $commentsinfo['Comment'][$i]['comment']))). '",
            "postTimestamp":"' . $date . '",
            "postVia":"' .str_replace('"', '\"',$postVia). '"

         }';
                $postcount++;
                $str = $str . $str_temp;
                $str = $str . ',';
            }
            $str = substr($str, 0, strlen($str) - 1);
            $response_str = response_repeat_string();
            $response_mess = '{
   ' . $response_str . '
   "CommentsOnPhotosParentComment":{
      "errorCode":"' . $userinfocode . '",
      "errorMsg":"' . $userinfodesc . '",
       "totalComments":"' . $postcount . '",
       "posts":[ ' . $str . '
]

   }
}
';
        } else {
            $userinfocode = $response_message['CommentsOnPhotosParentComment']['ErrorCode'];
            $userinfodesc = $response_message['CommentsOnPhotosParentComment']['ErrorDesc'];
            $response_mess = get_response_string("CommentsOnPhotosParentComment", $userinfocode, $userinfodesc);
        }
        return $response_mess;
    }

    /* Function:delete_photo_comment($xmlrequest)
     * Description: to delete comment on photo.
      Parameters: $xmlrequest=>request sent by user
      Return:Boolean Array.
     */

    function delete_photo_comment($xmlrequest) {
        $userId = mysql_real_escape_string($xmlrequest['DeletePhotoComment']['userId']);
        $commentId = mysql_real_escape_string($xmlrequest['DeletePhotoComment']['commentId']);
        $error = array();
        $query_hotpress = "SELECT bullet_id FROM photo_comments WHERE id='$commentId'";
        if (DEBUG)
            writelog("Profile:delete_photo_comment()", $query_hotpress, false);

        $result_hotpress = execute_query($query_hotpress, false, "select");
        $id = isset($result_hotpress['bullet_id']) && ($result_hotpress['bullet_id']) ? $result_hotpress['bullet_id'] : 0;
        $query = "DELETE FROM photo_comments WHERE (from_id='$userId'|| mem_id='$userId') AND id='$commentId'||(parent_id='$commentId' AND parent_id>0)";
        if (DEBUG)
            writelog("Profile:delete_photo_comment", $query, false);
        $result = execute_query($query, false, "delete");
        $result['count'] = isset($result['count']) && ($result['count']) ? $result['count'] : NULL;
        $error = error_CRUD($xmlrequest, $result['count']);
        if ((isset($error['DeletePhotoComment']['successful_fin'])) && (!$error['DeletePhotoComment']['successful_fin'])) {
            return getValidJSON($error);
        }
        if ($id) {
            $query_hotpress_del = "DELETE FROM bulletin WHERE (id='$id' AND (mem_id='$userId' || from_id='$userId'))||(parentid='$id' AND parentid>0)"; //(parentid='$id') ||
            if (DEBUG)
                writelog("Profile:delete_photo_comment", $query_hotpress_del, false);
            $result_hotpress_del = execute_query($query_hotpress_del, false, "delete");

            $result_hotpress_del['count'] = isset($result_hotpress_del['count']) && ($result_hotpress_del['count']) ? $result_hotpress_del['count'] : 0;
            $error = error_CRUD($xmlrequest, $result_hotpress_del['count']);

            if ((isset($error['DeletePhotoComment']['successful_fin'])) && (!$error['DeletePhotoComment']['successful_fin'])) {
                return getValidJSON($error);
            }
        }
        if (DEBUG)
            writelog("Profile:delete_photo_comment", $error, true);
        return $error;
    }

    /* Function:deletePhotoComment($response_message, $xmlrequest)
     * Description: to delere comment on photo.
      Parameters: $xmlrequest=>request sent by user,
     *            $response_message=>containing boolean array
      Return:string.
     */

    function deletePhotoComment($response_message, $xmlrequest) {
        global $return_codes;
        $userinfo = array();
        $userinfo = $this->delete_photo_comment($xmlrequest);
        if ((isset($userinfo['DeletePhotoComment']['successful_fin'])) && (!$userinfo['DeletePhotoComment']['successful_fin'])) {
            $obj_error = new Error();
            $response_message = $obj_error->error_type("DeletePhotoComment", $userinfo);

            $userinfocode = $response_message['DeletePhotoComment']['ErrorCode'];
            $userinfodesc = $response_message['DeletePhotoComment']['ErrorDesc'];
            $response_mess = $response_mess = get_response_string("DeletePhotoComment", $userinfocode, $userinfodesc);
            return getValidJSON($response_mess);
        }
        if ((isset($userinfo['DeletePhotoComment']['successful_fin'])) && ($userinfo['DeletePhotoComment']['successful_fin'])) {
            $response_mess = '
               {
   ' . response_repeat_string() . '
    "DeletePhotoComment":{
           "errorCode":"' . $return_codes["DeletePhotoComment"]["SuccessCode"] . '",
           "errorMsg":"' . $return_codes["DeletePhotoComment"]["SuccessDesc"] . '"
   }
	  }';
        } else {

            $response_mess = '
                {
   ' . response_repeat_string() . '
   "DeletePhotoComment":{
      "errorCode":"' . $return_codes["DeletePhotoComment"]["NoRecordErrorCode"] . '",
      "errorMsg":"' . $return_codes["DeletePhotoComment"]["NoRecordErrorDesc"] . '"

   }
	  }';
        }
        return getValidJSON($response_mess);
    }

}

?>
