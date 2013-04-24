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
  File-name : hotpress.class.php
  Directory Path  : $/MySNL/Deliverables/Code/MySNL_WebServiceV2/hotpress.class.php
  Author    : Brijesh Kumar
  Date    : 12/08/2011
  Modified By   : N/A
  Date : N/A

  Include Files : none
  CSS File(s)   : none

  Functions Used
  Javascript   :  none
  PHP     :  hotpress_post(),sort_todaystop(),get_user_like(),get_total_comment_count(),get_total_like_count(),hot_press_post_comment(),comment_on_hotpress_post(),hotpress_valid(),hot_press_post_comment_valid(),comment_on_hotpress_post_valid(),like_post_list(),like_post_list_valid(),like_post(),delete_post(),delete_post_valid(),hotpress_photo_upload(),hotpress_photo_upload_valid(),hotpressPost(),commentsOnHotPressPost(),hotPressPostComment(),deleteHotpressPost(),likePostList(),likePost(),

  DataBase Table(s)  : bulletin,members,likehot,network,photo_album,app_entourage_comments,announce_arrival,event_list,events_comments,testimonials,photo_comments

  Global Variable(s)  : LOCAL_FOLDER: Path where all the images save.
  PROFILE_IMAGE_SITEURL:website url

  Description: These Variables are use to store logical path of website.

  Reviwed By  :
  Reviwed Date:
 * ************************************************************************************* */

/*  class HotPress
  Purpose:We can send comment on Hotpress Like share events,Upload Image,Post comment anywhere Profile,Events,Appearances,Photo Albums which must be shared as Hotpress.
 *        Display all stuff(Event Sharing,Photo Upload,Shared as Hotpress comment of all modules.)
 *        User can Delete his own comment as well as if the posted comment is main comment then user can delete all subcomments.
 *        User can Like any comment and reply comment.
 *        Hotpress module is linked with all modules whatever we change in other modules(Option =Display as Hotpress) then changes will reflect in this mudule.
 *

 * Returns : None
 */

class HotPress {

    var $profile_url = PROFILE_IMAGE_SITEURL;
    var $local_folder = LOCAL_FOLDER;

    /*
     * Function:hotpress_post($xmlrequest, $pagenumber, $limit)
     * Description:to display all hotpress Post and its details(which user has liked them,total comment on them,commented user,link to the modules Like Profile,Events,Appearances)
     * Parameters: $xmlrequest=>request by user,
     * $pagenumber=>pagination,
     * $limit=>max limit of data to be shown
      Return: Array of all comments.
     *  */

    function hotpress_post($xmlrequest, $pagenumber, $limit) {
        if (DEBUG)
            writelog("hotPress.class.php :: hotpress_post() : ", "Start Here ", false);

        //$error = array();
        $hotpress = array();
        $queryClause = '';
        $userid = (isset($xmlrequest['HotPress']['userId']) && ($xmlrequest['HotPress']['userId'])) ? mysql_real_escape_string($xmlrequest['HotPress']['userId']) : NULL;
        $latestBulletin = (isset($xmlrequest['HotPress']['latest']) && ($xmlrequest['HotPress']['latest'])) ? mysql_real_escape_string($xmlrequest['HotPress']['latest']) : NULL;
        $lowerlimit = isset($pagenumber) ? (($pagenumber - 1) * $limit) : 0;

        $todaystop = isset($xmlrequest['HotPress']['HotPressToday']) && ($xmlrequest['HotPress']['HotPressToday']) ? $xmlrequest['HotPress']['HotPressToday'] : NULL;

//$todaystop(value=1)=>to get last 24 hrs Post.
//$latestBulletin=>to get post after specific Id suppose Id=1313 then query will return Post after Id>1313 Hotpress comments else send $latestBulletin=0.

        if ($latestBulletin == NULL) {
            if (isset($todaystop) && ($todaystop != '0')) {
                $query_hotpress_like = "SELECT hotpressid AS parentid FROM likehot WHERE hotpressid IN (SELECT id FROM bulletin WHERE  DATE  >= '" . (time() - (24 * 60 * 60)) . "') GROUP BY hotpressid ORDER BY COUNT( hotpressid ) DESC";
                //$temp_like1 = array();
                $exe_hotpress_like = mysql_query($query_hotpress_like);
                if ((mysql_num_rows($exe_hotpress_like) > 0)) {
                    while ($rowId = mysql_fetch_array($exe_hotpress_like, MYSQL_ASSOC)) {
                        $temp_like = multi_implode(',', $rowId);
                        $query_hotpress = "SELECT appearance_id,bulletin.id,photo_album_id,eventcmmnt_id,link_url,youtubeLink,link_image,id, mem_id, subj, body, bulletin.date,parentid, from_id, visible_to,image_link,post_via,auto_genrate_text FROM bulletin WHERE id IN ($temp_like) ORDER BY FIELD(id,$temp_like ) LIMIT 5";

                        $hotpressQuery = mysql_query($query_hotpress);
                        if ((mysql_num_rows($hotpressQuery) > 0)) {
                            $str = $this->getHotpressResponse($hotpressQuery,$userid);
                        } else {
                            $str = '';
                        }
                    }
                } else {
                    $str = '';
                }

                if (DEBUG)
                    writelog("hotpress.class.php :: hotpress_post() :: query: ", $query_hotpress, false);
            } else {

                if (DEBUG)
                    writelog("hotPress.class.php :: hotpress_post() :: todaystop variable set to  : ", $todaystop, false);

                $check_profile_type = "SELECT `profile_type` FROM `members` WHERE `mem_id`=$userid";
                $check_profile_result = execute_query($check_profile_type, false, "select");

                $query = "SELECT DISTINCT b.appearance_id,b.photo_album_id,b.eventcmmnt_id,b.id, b.mem_id, b.subj, b.body,
			b.date, b.parentid, b.from_id, b.visible_to,b.image_link,b.post_via,b.youtubeLink,b.link_image,b.link_url,b.auto_genrate_text,badges.badge_img,badges.badge_bottle_img";

                if ($check_profile_result['profile_type'] == "C" || $check_profile_result['profile_type'] == "c") {
                    $queryClause .= " FROM bulletin b
			INNER JOIN network n ON (n.frd_id = b.mem_id)
                        LEFT JOIN badges ON(b.bottle_id=badges.badge_id)			
                        WHERE b.parentid = '0'
			AND ('$userid' IN (n.mem_id, b.mem_id))
			GROUP BY b.id
			ORDER BY b.date DESC
			LIMIT $lowerlimit,$limit";
                } else {
                    $queryClause .= " FROM bulletin b
			INNER JOIN network n ON (n.mem_id = b.mem_id)
                        LEFT JOIN badges ON(b.bottle_id=badges.badge_id)
                        WHERE b.parentid = '0'
			AND ('$userid' IN (n.frd_id, b.mem_id))
			GROUP BY b.id
			ORDER BY b.date DESC
			LIMIT $lowerlimit,$limit";
                }

                 $query_hotpress = $query . $queryClause;
                $hotpressQuery = mysql_query($query_hotpress);
                if ((mysql_num_rows($hotpressQuery) > 0)) {
                    $str = $this->getHotpressResponse($hotpressQuery,$userid);
                } else {
                    $str = array();
                }
                writelog("hotpress.class.php :: hotpress_post() :: query:todaystop set to '0'", $query_hotpress, false);
            }
        }

        if (($latestBulletin) && (isset($xmlrequest['HotPress']['bulletinId'])) && ($xmlrequest['HotPress']['bulletinId'] > 0)) {
            if (DEBUG)
                writelog("hotPress.class.php :: hotpress_post() :: latestBulletin variable set to  : ", $latestBulletin, false);

            $bulletinId = mysql_real_escape_string($xmlrequest['HotPress']['bulletinId']);
            $query_hotpress = "SELECT appearance_id,photo_album_id,eventcmmnt_id,link_url,youtubeLink,link_image,id,mem_id,subj,body,date,parentid,from_id,visible_to,image_link,post_via,photo_album_id FROM bulletin WHERE (id>'" . $bulletinId . "')AND((parentid =0)AND (mem_id IN (SELECT mem_id FROM network WHERE frd_id ='$userid')|| mem_id ='$userid') ) ORDER BY date DESC LIMIT $lowerlimit,$limit"; //AND(from_id =0)
            $hotpressQuery = mysql_query($query_hotpress);
            if ((mysql_num_rows($hotpressQuery) > 0)) {
                $str = $this->getHotpressResponse($hotpressQuery,$userid);
            } else {
                $str = '';
            }

            if (DEBUG)
                writelog("hotpress.class.php :: hotpress_post() :: query:latest bulletin is set", $query_hotpress, false);
        }

        if (DEBUG)
            writelog("hotPress.class.php :: hotpress_post() :: HotPress Query : ", $query_hotpress, false);

        $hotpress['count'] = isset($hotpress) ? count($hotpress) : NULL;

        if (DEBUG) {
            writelog("hotPress.class.php :: hotpress_post() :: HotPress Query : ", $query_hotpress, false, $hotpress['count']);
            writelog("hotPress.class.php :: hotpress_post() :: Total HotPress Count : ", $hotpress['count'], false);
            writelog("HotPress:hotpress_post:", $hotpress, true);
            writelog("hotPress.class.php :: hotpress_post() :: ", "End Here ", false);
        }
//event share
//for todays top

        if (!empty($str) && $str['postcount'] > 0) {
            return $str;
        } else {
            return false;
        }
    }

    function getHotpressResponse($hotpressQuery,$userid) {
        $postcount = 0;
        $postType = '';
        $app_comment = '';
        $i = 0;
        $hotpress = array();

        while ($appResult = mysql_fetch_array($hotpressQuery, MYSQL_ASSOC)) {
            //$hotpress[$i]['appearance_id'] = isset($appResult['appearance_id']) && ($appResult['appearance_id']) ? $appResult['appearance_id'] : NULL;
            if ($appResult['appearance_id'])
                $hotpress[$i]['appearance_id'] = $this->get_appearance_id($appResult['appearance_id']);
//print_r($appResult);
            $hotpress[$i]['id'] = isset($appResult['id']) && ($appResult['id']) ? $appResult['id'] : NULL;
            $hotpress[$i]['mem_id'] = isset($appResult['mem_id']) && ($appResult['mem_id']) ? $appResult['mem_id'] : NULL;
            $hotpress[$i]['user_like'] = $this->get_user_like($userid, $hotpress[$i]['id']);
            $hotpress[$i]['like'] = $this->get_total_like_count($hotpress[$i]['id']);
            $hotpress[$i]['tot_comment'] = $this->get_total_comment_count($hotpress[$i]['id']);
            $hotpress[$i]['popularity_count'] = $hotpress[$i]['like'];
            //$hotpress[$i]['eventcmmnt_id'] = isset($appResult['eventcmmnt_id']) && ($appResult['eventcmmnt_id']) ? $appResult['eventcmmnt_id'] : NULL;
            if ($appResult['eventcmmnt_id'])
                $hotpress[$i]['event_info'] = $this->get_event_info($appResult['eventcmmnt_id']);
            $hotpress[$i]['photo_id'] = isset($appResult['photo_album_id']) && ($appResult['photo_album_id']) ? $appResult['photo_album_id'] : NULL;
            $hotpress[$i]['album_id'] = $this->get_album_id($hotpress[$i]['photo_id']);
            $hotpress[$i]['link_url'] = isset($appResult['link_url']) && ($appResult['link_url']) ? $appResult['link_url'] : NULL;
            $hotpress[$i]['youtubeLink'] = isset($appResult['youtubeLink']) && ($appResult['youtubeLink']) ? $appResult['youtubeLink'] : NULL;
            //$hotpress[$i]['link_image'] = isset($appResult['link_image']) && ($appResult['link_image']) ? $appResult['link_image'] : NULL;

            $tmp_userinfor_array = $this->get_user_briefprofile_info($appResult['mem_id']);

            $hotpress[$i]['name'] = $tmp_userinfor_array['name'];
            //$hotpress[$i]['profileImageUrl'] = $tmp_userinfor_array['profileImageUrl'];
            $hotpress[$i]['is_facebook_user'] = $tmp_userinfor_array['is_facebook_user'];
            $hotpress[$i]['gender'] = $tmp_userinfor_array['gender'];
            $hotpress[$i]['profile_type'] = $tmp_userinfor_array['profile_type'];
            $hotpress[$i]['profileImageUrl'] = isset($tmp_userinfor_array['profileImageUrl']) && (strlen($tmp_userinfor_array['profileImageUrl']) > 7) && ($tmp_userinfor_array['is_facebook_user'] == 'y' || $tmp_userinfor_array['is_facebook_user'] == 'Y') ? ((strstr($tmp_userinfor_array['profileImageUrl'],"photos")!=FALSE && strstr($tmp_userinfor_array['profileImageUrl'],"http")==FALSE) ? $this->profile_url.$tmp_userinfor_array['profileImageUrl'] : $tmp_userinfor_array['profileImageUrl'])  : ((isset($tmp_userinfor_array['profileImageUrl']) && (strlen($tmp_userinfor_array['profileImageUrl']) > 7)) ? $this->profile_url . $tmp_userinfor_array['profileImageUrl'] : $this->profile_url . default_images($tmp_userinfor_array['gender'], $tmp_userinfor_array['profile_type']));

            $hotpress[$i]['subj'] = isset($appResult['subj']) && ($appResult['subj']) ? $appResult['subj'] : NULL;
            $hotpress[$i]['subj'] = str_replace('\\', "", $hotpress[$i]['subj']);
            //commented on 24 nov 2011 :: aarya :: $hotpress[$i]['body'] = str_replace('\\', "", $hotpress[$i]['body']);

            $hotpress[$i]['subj'] = str_replace(array("\r\n", "\r", "\n", "<br />", "<br/>", "<br>"), "\\n", $hotpress[$i]['subj']);
            //commented on 24 nov 2011 :: aarya ::$hotpress[$i]['body'] = str_replace(array("\r\n", "\r", "\n", "<br />", "<br/>", "<br>"), "\\n", $hotpress[$i]['body']);

            $hotpress[$i]['subj'] = strip_tags($hotpress[$i]['subj']);
            //commented on 24 nov 2011 :: aarya :: $hotpress[$i]['body'] = strip_tags($hotpress[$i]['body']);

            $hotpress[$i]['subj'] = str_replace(array("\""), "", $hotpress[$i]['subj']);
            //commented on 24 nov 2011 :: aarya :: $hotpress[$i]['body'] = str_replace(array("\""), "", $hotpress[$i]['body']);

            $hotpress[$i]['subj'] = subanchor($hotpress[$i]['subj']);
            $hotpress[$i]['body'] = isset($appResult['body']) && ($appResult['body']) ? $appResult['body'] : NULL;
            $hotpress[$i]['date'] = isset($appResult['date']) && ($appResult['date']) ? $appResult['date'] : NULL;
            //$hotpress[$i]['parentid'] = isset($appResult['gender']) && ($appResult['gender']) ? $appResult['gender'] : NULL;
            $hotpress[$i]['from_id'] = isset($appResult['from_id']) && ($appResult['from_id']) ? $appResult['from_id'] : NULL;
            if ((isset($appResult['from_id'])) && ($appResult['from_id'] > 0))
                $hotpress[$i]['user_profile'] = $this->get_user_briefprofile_info($appResult['from_id']);
            $hotpress[$i]['visible_to'] = isset($appResult['visible_to']) && ($appResult['visible_to']) ? $appResult['visible_to'] : NULL;
            
            
            $hotpress[$i]['post_via'] = isset($appResult['post_via']) && ($appResult['post_via']) ? $appResult['post_via'] : NULL;
            $hotpress[$i]['auto_genrate_text'] = isset($appResult['auto_genrate_text']) && ($appResult['auto_genrate_text']) ? $appResult['auto_genrate_text'] : NULL;

            //if ($hotpress[$i]['auto_genrate_text'] == 'Y' && $appResult['bottle_id'] != 0) {
            $hotpress[$i]['appearance_id']['badge_image'] = isset($appResult['badge_img']) && ($appResult['badge_img']) ? $appResult['badge_img'] : NULL;
            $hotpress[$i]['appearance_id']['bottel_image'] = isset($appResult['badge_bottle_img']) && ($appResult['badge_bottle_img']) ? $appResult['badge_bottle_img'] : NULL;
            // }
            //$eventId = NULL;
			//print_r($appResult);
			//print_r($hotpress[$i]);
            $width_profileImageUrl = NULL;
            $height_profileImageUrl = NULL;
            //to get thumbnail image.
            if (is_readable($this->local_folder . $hotpress[$i]['profileImageUrl']))
                list($width_profileImageUrl, $height_profileImageUrl) = (isset($hotpress[$i]['profileImageUrl']) && (strlen($hotpress[$i]['profileImageUrl']) > 7)) ? getimagesize($this->local_folder . $hotpress[$i]['profileImageUrl']) : NULL;

            $width_image_link = NULL;
            $height_image_link = NULL;
            $type = NULL;
			$hotpress[$i]['image_link'] = (isset($appResult['image_link']) && (strlen($appResult['image_link']) > 7)) ? $this->profile_url . $appResult['image_link'] : "";
            if (isset($appResult['image_link']) && is_readable($this->local_folder . $appResult['image_link'])) {
                $sizee = getimagesize($this->local_folder . $appResult['image_link']);
                $width_image_link = $sizee[0];
                $height_image_link = $sizee[1];
                $file_extension = substr($appResult['image_link'], strrpos($appResult['image_link'], '.') + 1);
                $arr = explode('.', $appResult['image_link']);
                $Id = isset($hotpress[$i]['photo_album_id']) && ($hotpress[$i]['photo_album_id']) ? $hotpress[$i]['photo_album_id'] : NULL;
                if (!$Id)
                    $Id = isset($hotpress[$i]['id']) && $hotpress[$i]['id'] ? $hotpress[$i]['id'] : NULL;
                if ((!file_exists($this->local_folder . $arr[0] . "_" . $Id . "." . $file_extension)) && ($Id) && (preg_match('/^image\/(jp[e]?g|png|gif)$/', $sizee['mime']))) {
                    thumbanail_for_image($Id, $appResult['image_link']);
                }
                if (preg_match('/^image\/(jp[e]?g|png|gif)$/', $sizee['mime']) && strpos($appResult['image_link'],'event') !== false) {
                    $hotpress[$i]['image_link'] = isset($appResult['image_link']) && (strlen($appResult['image_link']) > 7) ? event_image_detail($Id, $hotpress[$i]['image_link'], 1) : NULL;
					list($width_image_link, $height_image_link, $type) = (isset($hotpress[$i]['image_link']) && (strlen($hotpress[$i]['image_link']) > 7)) ? getimagesize($this->local_folder . $hotpress[$i]['image_link']) : NULL;
                }
            }
			 $hotpress[$i]['link_image'] = isset($appResult['link_image']) && (strlen($appResult['link_image']) > 7) ? $this->profile_url . str_replace("../","",$appResult['link_image']) : "";
            $width_link_image = NULL;
            $height_link_image = NULL;
            //to get thumbnail image.
            if (is_readable($this->local_folder . str_replace("../","",$appResult['link_image']))) {
                $sizee = getimagesize($this->local_folder . str_replace("../","",$appResult['link_image']));
                $width_link_image = $sizee[0];
                $height_link_image = $sizee[1];
                $file_extension = substr($appResult['link_image'], strrpos($appResult['link_image'], '.') + 1);
                $arr = explode('.', $appResult['link_image']);
                $Id = NULL;
                if (preg_match('/^index.php\?pg=events\&s\=view\&eve_id\=/', $hotpress[$i]['link_url'])) {
                    $Id = preg_replace('/^index.php\?pg=events\&s\=view\&eve_id\=/', '', $hotpress[$i]['link_url']);
                } else {
                    $Id = isset($hotpress[$i]['photo_album_id']) && ($hotpress[$i]['photo_album_id']) ? $hotpress[$i]['photo_album_id'] : NULL;
                }
                if (!$Id)
                    $Id = isset($hotpress[$i]['id']) && $hotpress[$i]['id'] ? $hotpress[$i]['id'] : NULL;

                if (!file_exists($this->local_folder . $arr[0] . "_" . $Id . "." . $file_extension) && (preg_match('/^image\/(jp[e]?g|png|gif)$/', $sizee['mime']))) {
                    thumbanail_for_image($Id, $appResult['link_image']);
                }
                if (preg_match('/^image\/(jp[e]?g|png|gif)$/', $sizee['mime']) && strpos($appResult['link_image'],'event') !== false) {
                    $hotpress[$i]['link_image'] = isset($appResult['link_image']) && (strlen($appResult['link_image']) > 7) ? event_image_detail($Id, $hotpress[$i]['link_image'], 1) : NULL;
                    list($width_link_image, $height_link_image) = (isset($appResult['link_image']) && (strlen($appResult['link_image']) > 7)) ? getimagesize($this->local_folder .$appResult['link_image']) : NULL;
                }
            }
				//echo $hotpress[$i]['link_image'];
			//	echo $hotpress[$i]['image_link'];
				
            $check_set_var = (isset($hotpress[$i]['id']) ? true : false);

            if ($check_set_var)
                $check_set_var = (isset($hotpress[$i]['mem_id']) ? true : false);

//------user profile information-----
            if ((isset($hotpress[$i]['user_profile']['profileImageUrl'])) && ($hotpress[$i]['user_profile']['profileImageUrl']))
                $hotpress[$i]['user_profile']['profileImageUrl1'] = ((isset($hotpress[$i]['user_profile']['is_facebook_user'])) && (strlen($hotpress[$i]['user_profile']['profileImageUrl']) > 7) && ($hotpress[$i]['user_profile']['is_facebook_user'] == 'y' || $hotpress[$i]['user_profile']['is_facebook_user'] == 'Y')) ? $hotpress[$i]['user_profile']['profileImageUrl'] : ((isset($hotpress[$i]['user_profile']['profileImageUrl']) && (strlen($hotpress[$i]['user_profile']['profileImageUrl']) > 7)) ? $this->profile_url . $hotpress[$i]['user_profile']['profileImageUrl'] : $this->profile_url . default_images($hotpress[$i]['user_profile']['gender'], $hotpress[$i]['user_profile']['profile_type']));

// $hotpress[$i]['user_profile']['profileImageUrl'] = (isset($hotpress[$i]['user_profile']['profileImageUrl'])) ? $hotpress[$i]['user_profile']['profileImageUrl'] : 0;
            $hotpress[$i]['user_profile']['name'] = (isset($hotpress[$i]['user_profile']['name'])) ? $hotpress[$i]['user_profile']['name'] : "";
            $hotpress[$i]['user_profile']['gender'] = (isset($hotpress[$i]['user_profile']['gender'])) ? $hotpress[$i]['user_profile']['gender'] : "";
            $hotpress[$i]['user_profile']['profile_type'] = (isset($hotpress[$i]['user_profile']['profile_type'])) ? $hotpress[$i]['user_profile']['profile_type'] : "";

//to show events details
            $hotpress[$i]['event_info']['even_id'] = isset($hotpress[$i]['event_info']['even_id']) && ($hotpress[$i]['event_info']['even_id']) ? $hotpress[$i]['event_info']['even_id'] : NULL;
            $hotpress[$i]['event_info']['even_title'] = isset($hotpress[$i]['event_info']['even_title']) && ($hotpress[$i]['event_info']['even_title']) ? $hotpress[$i]['event_info']['even_title'] : NULL;

            if (!empty($hotpress[$i]['appearance_id'])) {
                $hotpress[$i]['appearance_id']['venue_id'] = (!empty($hotpress[$i]['appearance_id']['venue_id']) && $hotpress[$i]['appearance_id']['venue_id'] != 0) ? $hotpress[$i]['appearance_id']['venue_id'] : "0";
                $hotpress[$i]['appearance_id']['announce_arrival_id'] = (!empty($hotpress[$i]['appearance_id']['announce_arrival_id']) && $hotpress[$i]['appearance_id']['announce_arrival_id'] != 0) ? $hotpress[$i]['appearance_id']['announce_arrival_id'] : "0";
                $hotpress[$i]['appearance_id']['badge_image'] = (!empty($hotpress[$i]['appearance_id']['badge_image'])) ? ROOT_URL . $hotpress[$i]['appearance_id']['badge_image'] : "0";
                $hotpress[$i]['appearance_id']['bottel_image'] = (!empty($hotpress[$i]['appearance_id']['bottel_image'])) ? ROOT_URL . $hotpress[$i]['appearance_id']['bottel_image'] : "0";
            } else {
                $hotpress[$i]['appearance_id']['venue_id'] = "0";
                $hotpress[$i]['appearance_id']['announce_arrival_id'] = "0";
                $hotpress[$i]['appearance_id']['badge_image'] = "0";
                $hotpress[$i]['appearance_id']['bottel_image'] = "0";
            }

            $date = time_difference($hotpress[$i]['date']);

            if ($check_set_var) {
                $input = $hotpress[$i]['subj'] . $hotpress[$i]['body'];
                $input = str_replace('\\', '', $input);
                if (preg_match(REGEX_URL, $input, $url)) {
                    //$postType = extract_url($input);
                    //$postType = strip_tags(extract_url($input));
                    $postType = str_replace(array("\r\n", "\r", "\n", "<br />", "\"", "\<a"), "\\n", strip_tags(extract_url($input)));
                } else {
                    $postType = 'text';
                }
                $hotpress_body = get_organized_comment_data($hotpress[$i]['body'], $hotpress[$i]['name']);

                //$hotpress_body = htmlspecialchars_decode($hotpress_body,ENT_QUOTES);
                $hotpress_body = str_replace("'", "\'", $hotpress_body);

                $app_comment = '';
                if ((isset($hotpress[$i]['from_id'])) && ($hotpress[$i]['from_id'] > 0)) {//$hotpress[$i]['user_profile']['mem_id']
                    $profile_info = '"profileID":"' . str_replace('"', '\"', $hotpress[$i]['from_id']) . '",
                    "profileImgURL":"' . str_replace('"', '\"', $hotpress[$i]['user_profile']['profileImageUrl1']) . '",
                    "height":"' . str_replace('"', '\"', $height_profileImageUrl) . '",
                    "width":"' . str_replace('"', '\"', $width_profileImageUrl) . '",
                    "profileName":"' . trim(preg_replace('/\s+/', ' ', str_replace("\'", "'", htmlspecialchars_decode(str_replace('"', '\"', $hotpress[$i]['user_profile']['name']), ENT_QUOTES)))) . '",
                    "profileGender":"' . str_replace('"', '\"', $hotpress[$i]['user_profile']['gender']) . '",
                    "profileType":"' . str_replace('"', '\"', $hotpress[$i]['user_profile']['profile_type']) . '",';
                } else {
                    $profile_info = '';
                }

                $postVia = ((isset($hotpress[$i]['post_via'])) && ($hotpress[$i]['post_via'])) ? "iPhone" : "";
                $hotpress[$i]['appearance_id']['venue_name'] = isset($hotpress[$i]['appearance_id']['venue_name']) ? $hotpress[$i]['appearance_id']['venue_name'] : NULL;
                $str_temp = '{
                        ' . $profile_info . '
            "postId":"' . $hotpress[$i]['id'] . '",
            "appearanceId":"' . $hotpress[$i]['appearance_id']['announce_arrival_id'] . '",
            "venueId":"' . $hotpress[$i]['appearance_id']['venue_id'] . '",
            "venueName":"' . trim(preg_replace('/\s+/', ' ', str_replace("\'", "'", htmlspecialchars_decode(str_replace('"', '\"', $hotpress[$i]['appearance_id']['venue_name']), ENT_QUOTES)))) . '",
            "badgeImage":"' . $hotpress[$i]['appearance_id']['badge_image'] . '",
            "bottleImage":"' . $hotpress[$i]['appearance_id']['bottel_image'] . '",
			"postLikeByUser":"' . str_replace('"', '\"', $hotpress[$i]['user_like']) . '",
            "authorID":"' . str_replace('"', '\"', $hotpress[$i]['mem_id']) . '",
            "authorProfileImgURL":"' . str_replace('"', '\"', $hotpress[$i]['profileImageUrl']) . '",
            "width":"' . str_replace('"', '\"', $width_profileImageUrl) . '",
            "height":"' . str_replace('"', '\"', $height_profileImageUrl) . '",
            "authorName":"' . trim(preg_replace('/\s+/', ' ', str_replace("\'", "'", htmlspecialchars_decode(str_replace('"', '\"', $hotpress[$i]['name']), ENT_QUOTES)))) . '",
            "authorGender":"' . $hotpress[$i]['gender'] . '",
            "profileType":"' . str_replace('"', '\"', $hotpress[$i]['profile_type']) . '",
            "postSubj":"' . trim(preg_replace('/\s+/', ' ', str_replace("\'", "'", htmlspecialchars_decode(str_replace('"', '\"', $hotpress[$i]['subj']), ENT_QUOTES)))) . '",
            "postBody":"' . trim(preg_replace('/\s+/', ' ', str_replace("\'", "'", htmlspecialchars_decode(str_replace('"', '\"', $hotpress_body), ENT_QUOTES)))) . '",
            "postType":"' . trim(preg_replace('/\s+/', ' ', str_replace("\'", "'", htmlspecialchars_decode(str_replace('"', '\"', $postType), ENT_QUOTES)))) . '",
            "photoId":"' . trim(preg_replace('/\s+/', ' ', str_replace("\'", "'", htmlspecialchars_decode(str_replace('"', '\"', $hotpress[$i]['photo_id']), ENT_QUOTES)))) . '",
            "albumId":"' . str_replace('"', '\"', $hotpress[$i]['album_id']) . '",
            "uploadedImage":"' . str_replace('"', '\"', $hotpress[$i]['image_link']) . '",
            "width_image_link":"' . str_replace('"', '\"', $width_image_link) . '",
            "height_image_link":"' . str_replace('"', '\"', $height_image_link) . '",
            "youtubeLink":"' . str_replace('"', '\"', $hotpress[$i]['youtubeLink']) . '",
            "link_image":"' . str_replace('"', '\"', $hotpress[$i]['link_image']) . '",
            "width_link_image":"' . str_replace('"', '\"', $width_link_image) . '",
            "height_link_image":"' . str_replace('"', '\"', $height_link_image) . '",
            "link_url":"' . str_replace('"', '\"', $hotpress[$i]['link_url']) . '",
            "postTimestamp":"' . str_replace('"', '\"', $date) . '",
            "postVia":"' . str_replace('"', '\"', $postVia) . '",
            "eventId":"' . $hotpress[$i]['event_info']['even_id'] . '",
            "eventTitle":"' . trim(preg_replace('/\s+/', ' ', str_replace("\'", "'", htmlspecialchars_decode(str_replace('"', '\"', $hotpress[$i]['event_info']['even_title']), ENT_QUOTES)))) . '",
            "commentsCount":"' . str_replace('"', '\"', $hotpress[$i]['tot_comment']) . '",
            ' . $app_comment . '
            "likeCount":"' . str_replace('"', '\"', $hotpress[$i]['like']) . '",
            "autoGenrateText":"' . trim(preg_replace('/\s+/', ' ', str_replace("\'", "'", htmlspecialchars_decode(str_replace('"', '\"', $hotpress[$i]['auto_genrate_text']), ENT_QUOTES)))) . '"
		}';

                $postcount++;
                $str .= $str_temp;
                $str .= ',';
            }
            $i++;
        }

        $str = substr($str, 0, strlen($str) - 1);
        //}
        $strForResponse['postcount'] = isset($postcount) ? $postcount : NULL;
        $strForResponse['responseString'] = isset($str) ? $str : NULL;
        return $strForResponse;
    }

    /*
     * Function:sort_todaystop($arr)
     * Description: Sorting todays Top Hotpress array.
     * Parameters: $arr=>array of all comments of Todays Top.
      Return: Array of all comments
     *  */

    function sort_todaystop($arr) {

        $hotpress = array();
        $count = isset($arr['count']) ? $arr['count'] : 0;
        for ($i = ($count - 1); $i >= 0; $i--) {
            $hotpress[] = $arr[$i];
        }
        $hotpress['count'] = isset($arr['count']) ? $arr['count'] : 0;
        return $hotpress;

        // $count = isset($arr['count']) && ($arr['count']) ? $arr['count'] : 0;
        //$key = 'date';
        /* for ($i = 0; $i < $count; $i++) {
          for ($j = $i; $j < $count; $j++) {
          $like1 = (int) $arr[$i]['like'];
          $like2 = (int) $arr[$j]['like'];
          if ($like1 < $like2) {
          $temp = $arr[$i];
          $arr[$i] = $arr[$j];
          $arr[$j] = $temp;
          }
          }
          } */
        //  return $arr;
    }

    /*
     * Function:get_album_id($photo_id)
     * Description:to get album Id.
     * Parameters: $photo_id=>Photo Id.
      Return: Integer as an album Id Array
     *  */

    function get_album_id($photo_id) {
        if (DEBUG)
            writelog("hotPress.class.php :: get_album_id() for album_id : " . $photo_id . " :: ", "Start Here ", false);
        $query_album_id = "SELECT album_id FROM photo_album WHERE photo_id='$photo_id'";
        if (DEBUG)
            writelog("hotpress.class.php :: get_album_id() :: query:", $query_album_id, false);
        $result_album_id = execute_query($query_album_id, false);
        if (DEBUG)
            writelog("hotPress.class.php :: get_photo_id() for album_id : " . $photo_id . " :: ", "End Here ", false);
        $album_id = isset($result_album_id['album_id']) && ($result_album_id['album_id']) ? $result_album_id['album_id'] : NULL;
        return $album_id;
    }

    /*
     * Function:get_album_id($photo_id)
     * Description:to get venue id.
     * Parameters: $Id=>appearance id from appearance comments table.
      Return: Integer as a venue id.
     *  

      function get_appearance_id($Id) {
      $appearance_id = array();
      $query = "SELECT app_entourage_comments.announce_arrival_id,announce_arrival.venue_id,members.profilenam FROM app_entourage_comments LEFT JOIN announce_arrival ON (announce_arrival.id=app_entourage_comments.announce_arrival_id)  INNER JOIN members ON (announce_arrival.venue_id=members.mem_id) WHERE app_entourage_comments.announce_arrival_id='$Id'";


      $result = execute_query($query, false, "select");
      if (!empty($result)) {
      $appearance_id['announce_arrival_id'] = isset($result['announce_arrival_id']) && ($result['announce_arrival_id']) ? $result['announce_arrival_id'] : NULL;
      $appearance_id['venue_id'] = isset($result['venue_id']) && ($result['venue_id']) ? $result['venue_id'] : NULL;
      $appearance_id['venue_name'] = isset($result['profilenam']) && ($result['profilenam']) ? $result['profilenam'] : NULL;
      $appearance_id['badge_image1'] = isset($result['badge_img']) && ($result['badge_img']) ? $result['badge_img'] : NULL;
      $appearance_id['bottel_image1'] = isset($result['badge_bottle_img']) && ($result['badge_bottle_img']) ? $result['badge_bottle_img'] : NULL;
      } else {
      echo "SELECT announce_arrival.venue_id, members.profilenam,bottel_alert.badge_type_id,badges.badge_img,badges.badge_bottle_img FROM members,announce_arrival LEFT JOIN bottel_alert ON announce_arrival.id = bottel_alert.app_id LEFT JOIN badges ON bottel_alert.badge_type_id=badges.badge_id WHERE announce_arrival.id = '$Id' and announce_arrival.venue_id = members.mem_id";
      /* $query1 = execute_query("SELECT
      announce_arrival.venue_id,
      members.profilenam,bottel_alert.badge_type_id,badges.badge_img,badges.badge_bottle_img
      FROM announce_arrival
      INNER JOIN (members,
      bottel_alert,badges)
      ON (announce_arrival.user_id = members.mem_id)
      WHERE announce_arrival.id = '$Id'
      AND bottel_alert.app_id = '$Id' AND bottel_alert.badge_type_id=badges.badge_id", false, "select");

      $query1 = execute_query("SELECT announce_arrival.venue_id, members.profilenam,bottel_alert.badge_type_id,badges.badge_img,badges.badge_bottle_img FROM members,announce_arrival LEFT JOIN bottel_alert ON announce_arrival.id = bottel_alert.app_id LEFT JOIN badges ON bottel_alert.badge_type_id=badges.badge_id WHERE announce_arrival.id = '$Id' and announce_arrival.venue_id = members.mem_id", false, "select");
      /* 	echo "SELECT announce_arrival.venue_id, members.profilenam,bottel_alert.badge_type_id,badges.badge_img,badges.badge_bottle_img FROM members,announce_arrival LEFT JOIN bottel_alert ON announce_arrival.id = bottel_alert.app_id LEFT JOIN badges ON bottel_alert.badge_type_id=badges.badge_id WHERE announce_arrival.id = '106'  and announce_arrival.venue_id = members.mem_id";

      $appearance_id['announce_arrival_id'] = isset($query1) && ($query1) ? $Id : NULL;
      $appearance_id['venue_id'] = isset($query1['venue_id']) && ($query1['venue_id']) ? $query1['venue_id'] : NULL;
      $appearance_id['venue_name'] = isset($query1['profilenam']) && ($query1['profilenam']) ? $query1['profilenam'] : NULL;
      $appearance_id['badge_image1'] = isset($query1['badge_img']) && ($query1['badge_img']) ? $query1['badge_img'] : NULL;
      $appearance_id['bottel_image1'] = isset($query1['badge_bottle_img']) && ($query1['badge_bottle_img']) ? $query1['badge_bottle_img'] : NULL;
      }

      return $appearance_id;
      }
     */

    function get_appearance_id($Id) {
        $appearance_id = array();
        /* echo $query = "SELECT app_entourage_comments.announce_arrival_id,announce_arrival.venue_id FROM app_entourage_comments LEFT JOIN announce_arrival ON (announce_arrival.id=app_entourage_comments.announce_arrival_id) WHERE app_entourage_comments.announce_arrival_id='$Id'";
        $result = execute_query($query, false, "select");
        if (!empty($result)) {
            $appearance_id['announce_arrival_id'] = isset($result['announce_arrival_id']) && ($result['announce_arrival_id']) ? $result['announce_arrival_id'] : NULL;
            $appearance_id['venue_id'] = isset($result['venue_id']) && ($result['venue_id']) ? $result['venue_id'] : NULL;
        } else { */
//echo "SELECT aa.venue_id,mem.profilenam FROM announce_arrival AS aa,members AS mem WHERE aa.id='$Id' AND aa.venue_id=mem.mem_id";
		$query1 = execute_query("SELECT aa.venue_id,mem.profilenam FROM announce_arrival AS aa,members AS mem WHERE aa.id='$Id' AND aa.venue_id=mem.mem_id", false, "select");
            $appearance_id['announce_arrival_id'] = isset($query1) && ($query1) ? $Id : NULL;
            $appearance_id['venue_id'] = isset($query1['venue_id']) && ($query1['venue_id']) ? $query1['venue_id'] : NULL;
            $appearance_id['venue_name'] = isset($query1['venue_id']) && ($query1['venue_id']) ? getname($query1['venue_id']) : NULL;
        /* } */
		return $appearance_id;
    }

    /*
     * Function:get_event_info($eventcmmnt_id)
     * Description:to get event details.
     * Parameters: $eventcmmnt_id=>primary key of events comments.
      Return: event details(event id,event name).
     *  */

    function get_event_info($eventcmmnt_id) {
        if (DEBUG)
            writelog("hotPress.class.php :: get_event_info() for eventcmmnt_id : " . $eventcmmnt_id . " :: ", "Start Here ", false);

        $query = "SELECT even_id,even_title FROM event_list WHERE even_id IN (SELECT even_id FROM events_comments WHERE id='$eventcmmnt_id')";
        $result = execute_query($query, false, "select");
        if (DEBUG)
            writelog("hotPress.class.php :: :: get_event_info() for eventcmmnt_id", $query, false);
        if (DEBUG)
            writelog("hotPress.class.php :: get_event_info()", "END HERE ", false);

        return $result;
    }

    /*
     * Function:get_user_briefprofile_info($userid)
     * Description:to get User Information.
     * Parameters: $userid=>member Id which has been generated at the time of registration.
      Return: Array having user details.
     *  */

    function get_user_briefprofile_info($userid) {
        if (DEBUG)
            writelog("hotPress.class.php :: get_user_briefprofile_info() for UserId : " . $userid . " :: ", "Start Here ", false);
        $userinfo = array();
        $query_userinfo = "SELECT is_facebook_user,mem_id,profilenam,photo_b_thumb,gender,profile_type  FROM members WHERE mem_id='$userid'";
        if (DEBUG)
            writelog("hotpress.class.php :: get_user_briefprofile_info() :: query:", $query_userinfo, false);
        $result_userinfo = mysql_query($query_userinfo);
        if ((mysql_num_rows($result_userinfo) > 0)) {
            $row_userinfo = mysql_fetch_array($result_userinfo, MYSQL_ASSOC);
        }

        $userinfo['name'] = (isset($row_userinfo['profilenam'])) ? $row_userinfo['profilenam'] : 0;
        $userinfo['profileImageUrl'] = (isset($row_userinfo['photo_b_thumb'])) ? $row_userinfo['photo_b_thumb'] : 0;
        $userinfo['gender'] = (isset($row_userinfo['gender'])) ? $row_userinfo['gender'] : 0;
        $userinfo['profile_type'] = (isset($row_userinfo['profile_type'])) ? $row_userinfo['profile_type'] : 0;
        $userinfo['is_facebook_user'] = (isset($row_userinfo['is_facebook_user'])) ? $row_userinfo['is_facebook_user'] : NULL;
        if (DEBUG)
            writelog("hotPress.class.php :: get_user_briefprofile_info() for UserId : " . $userid . " :: ", "End Here ", false);
//        print_r($userinfo);
        return $userinfo;
    }

    /*
     * Function:get_user_like($userid, $hotpressid)
     * Description: Give the details whether comment has been liked by user or not.
     * Parameters: $userid=>Login user Id, $hotpressid=>Primary key of a comment
      Return: integer.
     *  */

    function get_user_like($userid, $hotpressid) {
        if (DEBUG)
            writelog("hotPress.class.php :: get_user_like() for UserId : " . $userid . " :: ", "Start Here ", false);
        $query_user_like = "SELECT COUNT(*) as user_like_cnt FROM likehot WHERE hotpressid='$hotpressid' AND user_id='$userid'";
        if (DEBUG)
            writelog("hotpress.class.php :: get_user_like() :: query:", $query_user_like, false);
        $like = execute_query($query_user_like, false);
        $like['user_like_cnt'] = isset($like['user_like_cnt']) && ($like['user_like_cnt']) ? $like['user_like_cnt'] : NULL;
        if (DEBUG)
            writelog("hotPress.class.php :: get_user_like() for UserId : " . $userid . " :: ", "End Here ", false, $like['user_like_cnt']);
        return $like['user_like_cnt'];
    }

    /*
     * Function:get_total_comment_count($hotpressid)
     * Description: to get total comment count on Parent or Root comment.
     * Parameters: $hotpressid=>Primary key of a comment
      Return: integer.
     *  */

    function get_total_comment_count($hotpressid) {
        if (DEBUG)
            writelog("hotPress.class.php :: get_total_comment_count() for HotpressId : " . $hotpressid . " :: ", "Start Here ", false);
        $query_comment_count = "SELECT COUNT(*) as totalcomment FROM bulletin WHERE parentid='$hotpressid'";
        if (DEBUG)
            writelog("hotpress.class.php :: get_total_comment_count() :: query:", $query_comment_count, false);
        $result_comment_count = execute_query($query_comment_count, false, "select");
        $result_comment_count['totalcomment'] = (isset($result_comment_count['totalcomment'])) ? $result_comment_count['totalcomment'] : 0;
        if (DEBUG)
            writelog("hotPress.class.php :: get_total_comment_count() for HotpressId : " . $hotpressid . " :: ", "End Here ", false, $result_comment_count['totalcomment']);
        return $result_comment_count['totalcomment'];
    }

    /*
     * Function:get_total_like_count($hotpressid)
     * Description: to get total count How many user has Liked that Post.
     * Parameters: $hotpressid=>Id of a Parent comment
      Return: integer.
     *  */

    function get_total_like_count($hotpressid) {
        if (DEBUG)
            writelog("hotPress.class.php :: get_total_like_count() for HotPressId : " . $hotpressid . " :: ", "Start Here ", false);
        $result_like = execute_query("SELECT COUNT(*) as total_like FROM likehot WHERE hotpressid='$hotpressid'", false, "select");
        $result_like['total_like'] = isset($result_like['total_like']) && ($result_like['total_like']) ? $result_like['total_like'] : NULL;
        if (DEBUG)
            writelog("hotPress.class.php :: get_total_like_count() for HotPressId : " . $hotpressid . " :: ", "End Here ", false, $result_like['total_like']);
        return $result_like['total_like'];
    }

    /*
     * Function:hot_press_post_comment($xmlrequest)
     * Description: This function is used to post new comment on Hotpress.
     * Parameters: $xmlrequest=>Request in which has been sent by user(comment text)
      Return: boolean array=>to show status that it has been posted or not.
     *  */

    function hot_press_post_comment($xmlrequest) {
        $mem_id = mysql_real_escape_string($xmlrequest['HotPressPostComment']['userId']);
        $parentid = mysql_real_escape_string($xmlrequest['HotPressPostComment']['postId']);
        $post = mysql_real_escape_string($xmlrequest['HotPressPostComment']['commentText']);
        $visible = mysql_real_escape_string($xmlrequest['HotPressPostComment']['displayAsHotPress']);
        $time = mysql_real_escape_string($xmlrequest['HotPressPostComment']['time']);

        // $userTimezone = new DateTimeZone('America/Chicago');
        // $myDateTime = new DateTime("$time");
        // $offset = $userTimezone->getOffset($myDateTime);
        // $date = $myDateTime->format('U') + $offset;
        // $cDate = date('Y-m-d',$date);
        // $cTime = date('H:i:s',$date);
//	$date = time();
// date_default_timezone_set('America/Chicago');
// $date = strtotime(gmdate('Y-m-d H:i:s',time())); 
        $date = gmmktime();
        $fromid = 0;
        $error = array();
        if (isset($parentid) && ($parentid > 0)) {
            $row = execute_query("SELECT mem_id,from_id,photocomment_id,testo_id,eventcmmnt_id,appearance_id FROM bulletin WHERE id='$parentid' AND parentid=0", false, "select");
            $fromid = $row['mem_id'];
        }

        $privacy = user_privacy_settings($mem_id);
        $visible = isset($privacy) && ($privacy == 'private') ? 'allfriends' : '';

        if (isset($parentid) && ($parentid > 0)) {
            $getAllPosts = "SELECT id,mem_id FROM bulletin WHERE parentid ='$parentid' ORDER BY id DESC LIMIT 0,1";
            $getAllPostsResult = execute_query($getAllPosts, false, "select");
            if ($fromid == $mem_id)
                $fromid1 = $getAllPostsResult['mem_id'];
            else
                $fromid1 = $fromid;
            $result = execute_query("INSERT INTO bulletin(mem_id,subj,body,visible_to,date,parentid,from_id,testo_id,post_via,msg_alert)VALUE('$mem_id','','$post','$visible', '$date','$parentid','$fromid1',0,1,'Y')", false, "insert");
            $last_id = $result['last_id'];
        } else {

            $result = execute_query("INSERT INTO bulletin(mem_id,subj,body,visible_to,date,parentid,from_id,testo_id,post_via,msg_alert)VALUE('$mem_id','','$post','$visible', '$date','0','$fromid',0,1,'Y')", false, "insert");
            $last_id = $result['last_id'];
        }

        //Added by anusha: inorder to manage alert notification using function insertShow_Alert
        insertShow_Alert($mem_id, $last_id, $parentid);

        //send email
        $get_user_email_id = execute_query("select profilenam,email,photo_thumb from members where mem_id='$mem_id'", false, "select");
        $get_profile_user_email_id = execute_query("select profilenam,email from members where mem_id='$fromid'", false, "select");
        $testimonialId = execute_query("select testo_id from bulletin where id='$parentid'", false, "select");

//push notification for hotpress post/reply  

        if (isset($parentid) && ($parentid > 0)) {
            $get_parent_comment_info = execute_query("SELECT from_id FROM bulletin WHERE parentid ='$parentid'", false, "select");
            if (!empty($get_parent_comment_info) && ($get_parent_comment_info['from_id'] != $mem_id)) {


                //push_notification('post_comment_on_hotpress', $get_parent_comment_info['from_id'], $mem_id);
                $body = $get_user_email_id['profilenam'] . ' has replied to your HotPress submission.<br>Please login to view.<br><span style="color:#666666">"' . $post . '"</span>' . "<a href='http://www.socialnightlife.com/index.php?pg=profile&usr=$fromid&gotohtpreepage=1' target='_blank'>Login</a>";
                $title = $get_user_email_id['profilenam'] . ' has replied to your HotPress';
                $matter = email_template($get_user_email_id['profilenam'], $title, $body, $mem_id, $get_user_email_id['photo_thumb']);
                firemail($get_profile_user_email_id['email'], 'From: socialNightLife <socialnightlife.com>\r\n', $title, $matter);
            }
            /*             * Added by anusha inoredr to sen d mails to all the people in the chain. */
            $sql = "select * from bulletin where (parentid='" . $parentid . "') and mem_id !=" . $mem_id . " group by mem_id";

            $bulletin_data = mysql_query($sql);

            while ($row = mysql_fetch_assoc($bulletin_data)) {

                if (!empty($row['mem_id']) && ($get_parent_comment_info['from_id'] != $row['mem_id'])) {

                    $get_reciever_email_id = execute_query("select profilenam,email,photo_thumb from members where mem_id=" . $row['mem_id'], false, "select");
                    //push_notification('post_comment_on_hotpress', $row['mem_id'], $mem_id);
                    $body = $get_user_email_id['profilenam'] . ' has replied to ' . $get_profile_user_email_id['profilenam'] . '\'s HotPress submission.<br>Please login to view.<br><span style="color:#666666">"' . $post . '"</span>' . "<a href='http://www.socialnightlife.com/index.php?pg=profile&usr=$fromid&gotohtpreepage=1' target='_blank'>Login</a>";
                    $title = $get_user_email_id['profilenam'] . ' has replied to ' . $get_profile_user_email_id['profilenam'] . '\'s HotPress';
                    $matter = email_template($get_user_email_id['profilenam'], $title, $body, $mem_id, $get_user_email_id['photo_thumb']);
                    firemail($get_reciever_email_id['email'], 'From: socialNightLife <socialnightlife.com>\r\n', $title, $matter);
                }
            }
        } else {
//        $get_parent_comment_get_parent = execute_query("SELECT parentid FROM bulletin WHERE bulletin.mem_id = bulletin.from_id AND bulletin.mem_id = '$mem_id' AND bulletin.id='$last_id'", false, "select");
            $get_parent_comment_get_parent_result = execute_query("SELECT from_id FROM bulletin WHERE id ='$last_id'", false, "select");
            if (!empty($get_parent_comment_get_parent_result) && ($get_parent_comment_info['from_id'] != $mem_id)) {
                //push_notification('post_comment_on_hotpress', $get_parent_comment_get_parent_result['from_id'], $mem_id);
            }
        }
//        $get_online_status = execute_query("select id FROM user_push_notification Where mem_id='$mem_id' AND showonline='y'", true, "select");
//last inserted id in bulletin.
        $last_id = isset($result['last_id']) && ($result['last_id']) ? $result['last_id'] : 0;
        $result['count'] = isset($result['count']) && ($result['count']) ? $result['count'] : NULL;

        $error = error_CRUD($xmlrequest, $result['count']);
        if ((isset($error['HotPressPostComment']['successful_fin'])) && (!$error['HotPressPostComment']['successful_fin'])) {
            return $error;
        }
        $error['last_id'] = $last_id;
        //profile=>if that comment has parent or root comment which has already posted on someone's Profile then for same reflection we have to post it there.

        if (isset($row['testo_id']) && ($row['testo_id']) && isset($parentid) && ($parentid > 0)) {
            $publishashotpress = 1;
            $row['testo_id'] = isset($row['testo_id']) && ($row['testo_id']) ? $row['testo_id'] : NULL;
            $row['from_id'] = isset($row['from_id']) && ($row['from_id']) ? $row['from_id'] : NULL;
//echo "INSERT INTO testimonials(mem_id, from_id, testimonial, stat, added, parent_tst_id, publishashotpress, photo_album_id, bullet_id,post_via,msg_alert)VALUE('" . $row['from_id'] . "', '$mem_id', '$post', 'a', '$date', '" . $row['testo_id'] . "', '0', '0', '$last_id',1,'Y'";
//die();
            $query_comment = "INSERT INTO testimonials(mem_id, from_id, testimonial, stat, added, parent_tst_id, publishashotpress, photo_album_id, bullet_id,post_via,msg_alert)VALUE('" . $row['from_id'] . "', '$mem_id', '$post', 'a', '$date', '" . $row['testo_id'] . "', '', '0', '0',1,'Y')";
            $result_comment = execute_query($query_comment, true, "insert"); //$last_id
            $result_comment_last_id = $result_comment['last_id'];

            $query_for_hotPress = execute_query("update bulletin SET testo_id='$result_comment_last_id',msg_alert='N' WHERE id='$last_id'", true, "update");
            $result_comment['count'] = isset($result_comment['count']) && ($result_comment['count']) ? $result_comment['count'] : NULL;
            $error = error_CRUD($xmlrequest, $result_comment['count']);

            if ((isset($error['HotPressPostComment']['successful_fin'])) && (!$error['HotPressPostComment']['successful_fin'])) {
                return $error;
            }
        }

        //events=>if that comment has parent or root comment which has already posted on Events then for same reflection we have to post it there.
        if (isset($row['eventcmmnt_id']) && ($row['eventcmmnt_id'])) {
            $query_event_id = "SELECT even_id FROM events_comments WHERE id='" . $row['eventcmmnt_id'] . "'";
            $result_event_id = execute_query($query_event_id, false, "select");
            $result_event_id['even_id'] = isset($result_event_id['even_id']) && ($result_event_id['even_id']) ? $result_event_id['even_id'] : NULL;
            $event_data = execute_query("INSERT into events_comments(parent_id,even_id,mem_id,from_id,comment,date,msg_alert,post_via,bullet_id) values ('" . $row['eventcmmnt_id'] . "','" . $result_event_id['even_id'] . "','" . $row['from_id'] . "', '$mem_id','$post','$date','Y','1', '$last_id')", true, "insert");

            $event_data['count'] = isset($event_data['count']) && ($event_data['count']) ? $event_data['count'] : NULL;
            $error = error_CRUD($xmlrequest, $event_data['count']);

            if ((isset($error['HotPressPostComment']['successful_fin'])) && (!$error['HotPressPostComment']['successful_fin'])) {
                return $error;
            }
        }

        //appearance=>if that comment has parent or root comment which has already posted on Appearance then for same reflection we have to post it there.
        if (isset($row['appearance_id']) && ($row['appearance_id'])) {
            $query_appearance_id = "SELECT announce_arrival_id FROM app_entourage_comments WHERE id='" . $row['appearance_id'] . "'";
            $result_appearance_id = execute_query($query_appearance_id, false, "select");
            $result_appearance_id['announce_arrival_id'] = isset($result_appearance_id['announce_arrival_id']) && ($result_appearance_id['announce_arrival_id']) ? $result_appearance_id['announce_arrival_id'] : NULL;
            $appearance_data = execute_query("INSERT INTO app_entourage_comments(parent_id,announce_arrival_id,comment_by_id,comment,date,time,post_via,bullet_id)VALUES('" . $row['appearance_id'] . "','" . $row['appearance_id'] . "','$mem_id','$post','$cDate','$cTime','1','$last_id')", true, "insert");

            $appearance_data['count'] = isset($appearance_data['count']) && ($appearance_data['count']) ? $appearance_data['count'] : NULL;
            $error = error_CRUD($xmlrequest, $appearance_data['count']);

            if ((isset($error['HotPressPostComment']['successful_fin'])) && (!$error['HotPressPostComment']['successful_fin'])) {
                return $error;
            }
        }
//Photos=>if that comment has parent or root comment which has already posted on Photos then for same reflection we have to post it there.
        if (isset($row['photocomment_id']) && ($row['photocomment_id'])) {
            $query_photo_id = "SELECT photo_id FROM photo_comments  WHERE id='" . $row['photocomment_id'] . "'";
            $result_photo_id = execute_query($query_photo_id, false, "select");
            $result_photo_id['photo_id'] = isset($result_photo_id['photo_id']) && ($result_photo_id['photo_id']) ? $result_photo_id['photo_id'] : NULL;
            //$event_data = execute_query("INSERT into events_comments(parent_id,even_id,mem_id,from_id,comment,date,msg_alert,post_via,bullet_id) values ('" . $row['photocomment_id'] . "','" . $result_event_id['photo_id'] . "','" . $row['from_id'] . "', '$mem_id','$post','$date','Y','1', '$last_id')", true, "insert");
            $query_photo = execute_query("INSERT INTO photo_comments(parent_id,photo_id, mem_id,from_id,comment, date, msg_alert,post_via,bullet_id)VALUE('" . $row['photocomment_id'] . "','" . $result_photo_id['photo_id'] . "', '$mem_id','" . $row['from_id'] . "','$post', '$date', 'Y',1,'$last_id')", true, "insert");
            $query_photo['count'] = isset($query_photo['count']) && ($query_photo['count']) ? $query_photo['count'] : NULL;
            $photo_comment_last_id = isset($query_photo['last_id']) && ($query_photo['last_id']) ? $query_photo['last_id'] : NULL;
            $update_photo_id = execute_query("UPDATE bulletin SET photocomment_id='$photo_comment_last_id' WHERE id='$last_id'", true, "update");
            $error = error_CRUD($xmlrequest, $query_photo['count']);

            if ((isset($error['HotPressPostComment']['successful_fin'])) && (!$error['HotPressPostComment']['successful_fin'])) {
                return $error;
            }
        }

        if (DEBUG)
            writelog("HotPress:hot_press_post_comment:", $error, true);
        return $error;
    }

    /* Function:comment_on_hotpress_post($xmlrequest)
     * Description: This function is used to display all post which has been posted on Parent or Root comment (Like who has sent.,Whre they have posted etc.)
     * Parameters: $xmlrequest=>Request in which user send necessary details of Parent comment.
      Return: array of subcomment
     *  */

    function comment_on_hotpress_post($xmlrequest, $pageNumber, $limit) {

        $postId = mysql_real_escape_string($xmlrequest['CommentsOnHotPressPost']['postId']);
        $lowerLimit = isset($pageNumber) ? ($pageNumber - 1) * $limit : 0;
        $postinfo = array();
        $query = "SELECT SQL_CALC_FOUND_ROWS DISTINCT members.is_facebook_user,members.mem_id,members.profilenam,members.photo_b_thumb,members.photo,members.gender,members.profile_type,bulletin.id,bulletin.mem_id,bulletin.from_id,bulletin.subj,bulletin.body,bulletin.date,bulletin.post_via FROM bulletin LEFT JOIN members ON (bulletin.mem_id=members.mem_id) WHERE bulletin.parentid='$postId' ORDER BY bulletin.date LIMIT $lowerLimit,$limit";
        if (DEBUG)
            writelog("hotpress.class.php :: comment_on_hotpress_post() :: query:", $query, false);
        $postinfo = execute_query($query, true, "select");
        $total_reply_count = execute_query("SELECT FOUND_ROWS() as TotalRecords ;", true, "select");
        $postinfo['totalrecords'] = (isset($total_reply_count[0]['TotalRecords'])) ? $total_reply_count[0]['TotalRecords'] : 0;
        if (DEBUG)
            writelog("HotPress:comment_on_hotpress_post:", $postinfo, true);
        return $postinfo;
    }

    /*
     * Function:hotpress_valid($xmlrequest)
     * Description: This function is used to validate user.
     * Parameters: $xmlrequest=>Request which is sent by user.
      Return: boolean array.
     *  */

    function hotpress_valid($xmlrequest) {
        $error = array();
        $userId = mysql_real_escape_string($xmlrequest['HotPress']['userId']);
        $query = "SELECT COUNT(*) FROM members WHERE mem_id='$userId'";
        if (DEBUG)
            writelog("hotpress.class.php :: hotpress_valid() :: query:", $query, false);
        $result = execute_query($query, false);
        $error['successful'] = isset($result['COUNT(*)']) && ($result['COUNT(*)']) ? true : false;
        if (DEBUG)
            writelog("HotPress:hotpress_valid:", $error, true);
        return $error;
    }

    /*
     * Function:hot_press_post_comment_valid($xmlrequest)
     * Description: This function is used to validate hotpress Root post and User.
     * Parameters: $xmlrequest=>Request which is sent by user.
      Return: boolean array.
     *  */

    function hot_press_post_comment_valid($xmlrequest) {
        $fromid = mysql_real_escape_string($xmlrequest['HotPressPostComment']['userId']);
        $parentid = mysql_real_escape_string($xmlrequest['HotPressPostComment']['postId']);
        $error = array();
        if (isset($parentid) && ($parentid > 0)) {
            $query_parent = "SELECT COUNT(*) FROM bulletin WHERE id='$parentid' AND parentid=0";
            if (DEBUG)
                writelog("hotpress.class.php :: hot_press_post_comment_valid() :: query:", $query_parent, false);
            $result_parent = mysql_query($query_parent);
            if (!mysql_num_rows($result_parent)) {
                $row = mysql_fetch_array($result_parent, MYSQL_ASSOC);
                if (!$row['COUNT(*)']) {
                    $error['successful'] = false;
                    if (DEBUG)
                        writelog("HotPress:hot_press_post_comment_valid:", $error, true);
                    return $error;
                }
            }
        }

        $query = "SELECT COUNT(*) FROM members WHERE mem_id='$fromid'";
        if (DEBUG)
            writelog("hotpress.class.php :: hot_press_post_comment_valid() :: query:", $query, false);
        $result = execute_query($query, false);
        $error['successful'] = isset($result['COUNT(*)']) && ($result['COUNT(*)']) ? true : false;

        return $error;
    }

    /* Function:comment_on_hotpress_post_valid($xmlrequest)
     * Description: This function is used to validate hotpress post user.
     * Parameters: $xmlrequest=>Request which is sent by user.
      Return: boolean array.
     *  */

    function comment_on_hotpress_post_valid($xmlrequest) {
        $userId = mysql_real_escape_string($xmlrequest['CommentsOnHotPressPost']['userId']);
        $postId = mysql_real_escape_string($xmlrequest['CommentsOnHotPressPost']['postId']);

        $error = array();
        $userId = mysql_real_escape_string($xmlrequest['CommentsOnHotPressPost']['userId']);
        $query = "SELECT COUNT(*) FROM members WHERE mem_id='$userId'";
        if (DEBUG)
            writelog("hotpress.class.php :: comment_on_hotpress_post_valid() :: query:", $query, false);
        $result = execute_query($query, false);
        $error['successful'] = isset($result['COUNT(*)']) && ($result['COUNT(*)']) ? true : false;
        if (DEBUG)
            writelog("HotPress:comment_on_hotpress_post_valid:", $error, true);
        return $error;
    }

    /* Function:like_post_list($xmlrequest)
     * Description: This function is used to dispaly the information regarding the list of user who has liked specific post.
     * Parameters: $xmlrequest=>Request which is sent by user.
      Return: array of user info.
     *  */

    function like_post_list($xmlrequest) {
        $postId = mysql_real_escape_string($xmlrequest['LikePostList']['postId']);
        $query = "SELECT members.is_facebook_user,members.mem_id,members.photo_thumb,members.profilenam,members.gender,members.profile_type,members.privacy FROM members,likehot WHERE members.mem_id=likehot.user_id AND (likehot.hotpressid='$postId') ORDER BY likehot.rated_time DESC ";
        if (DEBUG)
            writelog("hotpress.class.php :: like_post_list() :: query:", $query, false);
        $user_info = execute_query($query, true, "select");
        if (DEBUG)
            writelog("HotPress:like_post_list", $user_info, true);
        return $user_info;
    }

    /* Function:like_post_list_valid($xmlrequest) {
     * Description: validation of liked Post & LikePostList for user and post both.
     * Parameters: $xmlrequest=>Request which is sent by user.
      Return: boolean array.
     */

    function like_post_list_valid($xmlrequest) {
        $error = array();
        if (isset($xmlrequest['LikePostList']['userId'])) {
            $userId = mysql_real_escape_string($xmlrequest['LikePostList']['userId']);
            $authorId = mysql_real_escape_string($xmlrequest['LikePostList']['authorId']);
            $postId = mysql_real_escape_string($xmlrequest['LikePostList']['postId']);
            $result_duplication['COUNT(*)'] = 0;
            $query = "SELECT COUNT(*) FROM bulletin WHERE id ='$postId' AND mem_id='$authorId'";
            if (DEBUG)
                writelog("hotpress.class.php :: like_post_list_valid() :: query:", $query, false);
            $result = execute_query($query, false, "select");
            $error['successful'] = isset($result['COUNT(*)']) && ($result['COUNT(*)']) ? true : false;
        }

        if (isset($xmlrequest['LikePost']['userId'])) {
            $userId = mysql_real_escape_string($xmlrequest['LikePost']['userId']);
            $postId = mysql_real_escape_string($xmlrequest['LikePost']['postId']);
            $query = "SELECT COUNT(*) FROM bulletin WHERE id ='$postId'";
            if (DEBUG)
                writelog("hotpress.class.php :: like_post_list_valid() :: query:", $query, false);
            $query_duplication = "SELECT COUNT(*) FROM likehot WHERE hotpressid ='$postId' AND user_id='$userId'";
            if (DEBUG)
                writelog("hotpress.class.php :: like_post_list_valid() :: query:", $query_duplication, false);
            $result_duplication = execute_query($query_duplication, false, "select");
            $error['successful'] = isset($result_duplication['COUNT(*)']) && (!$result_duplication['COUNT(*)']) ? true : false;
            if (DEBUG)
                writelog("HotPress:like_post_list_valid", $error, true);
        }
        return $error;
    }

    /* Function:like_post($xmlrequest)
     * Description: If user likes any post then count to be inserted in Data Base
     * Parameters: $xmlrequest=>Request which is sent by user.
      Return: boolean array.
     */

    function like_post($xmlrequest) {
        $userId = mysql_real_escape_string($xmlrequest['LikePost']['userId']);
        $postId = mysql_real_escape_string($xmlrequest['LikePost']['postId']);
        $time = mysql_real_escape_string($xmlrequest['LikePost']['time']);

        $userTimezone = new DateTimeZone('America/Chicago');
        $myDateTime = new DateTime("$time");
        $offset = $userTimezone->getOffset($myDateTime);
        $date = $myDateTime->format('U') + $offset;

//	$date = time();
        $error = array();
        $query = "INSERT INTO likehot(hotpressid,user_id,rated_time)VALUES('$postId','$userId','$date')";
        if (DEBUG)
            writelog("hotpress.class.php :: like_post() :: query:", $query, false);
        $result = execute_query($query, false, "insert");
        $error['LikePost']['successful_fin'] = (isset($result['count'])) && ($result['count']) ? true : false;
        if (DEBUG)
            writelog("HotPress:like_post", $error, true);
        return $error;
    }

    /* Function:delete_post($xmlrequest)
     * Description: If user wants to delete any post then he sends request and post would be deleted from database.
     * Parameters: $xmlrequest=>Request which has been  sent by user.
      Return: boolean array for satus check whether comment has deleted or not.
     */

    function delete_post($xmlrequest) {
        $userId = mysql_real_escape_string($xmlrequest['DeletePost']['userId']);
        $authorId = mysql_real_escape_string($xmlrequest['DeletePost']['authorId']);
        $postId = mysql_real_escape_string($xmlrequest['DeletePost']['postId']);
        $error = array();
        $query_parent_author = "SELECT bulletin.eventcmmnt_id, bulletin.photocomment_id, bulletin.appearance_id, bulletin.testo_id, bulletin1.id, bulletin1.mem_id FROM bulletin LEFT JOIN bulletin AS bulletin1 ON ( bulletin.id = bulletin1.parentid ) WHERE bulletin.id ='$postId'";
        $result_parent_author = execute_query($query_parent_author, false, "select");
        $parent_comment_author = isset($result_parent_author['mem_id']) && ($result_parent_author['mem_id']) ? $result_parent_author['mem_id'] : NULL;
//$parent_comment_author=>main or Parent comment author.
        if (($parent_comment_author) && ($parent_comment_author == $userId)) {
            $last_id = NULL;
            //Added by anusha
            $delfromcomment_alert_notification = "DELETE FROM `comment_alert_notification` WHERE `bulletin_id` ='" . $postId . "'";

            $affected_row_1 = execute_query($delfromcomment_alert_notification, false, "delete");

            $query_parent_comment = "DELETE FROM bulletin WHERE (id='$postId')||(parentid='$postId' AND parentid>0)";
            $affected_row = execute_query($query_parent_comment, false, "delete");
            $affected_row['count'] = isset($affected_row['count']) && ($affected_row['count']) ? $affected_row['count'] : NULL;
            $error = error_CRUD($xmlrequest, $affected_row['count']);
            if ((isset($error['DeletePost']['successful_fin'])) && (!$error['DeletePost']['successful_fin'])) {
                return $error;
            }
        }
        //Added by anusha
        $delfromcomment_alert_notification = "DELETE FROM `comment_alert_notification` WHERE `bulletin_id` ='" . $postId . "'";

        $affected_row_1 = execute_query($delfromcomment_alert_notification, false, "delete");

        $query = "DELETE FROM bulletin WHERE (id='$postId' AND mem_id='$userId')||(parentid='$postId' AND parentid>0)"; //parentid='$postId' ||
        if (DEBUG)
            writelog("HotPress:delete_post", $query, false);
        $result = execute_query($query, false, "delete");
        $result['count'] = isset($result['count']) && ($result['count']) ? $result['count'] : NULL;
        $error = error_CRUD($xmlrequest, $result['count']);
        if ((isset($error['DeletePost']['successful_fin'])) && (!$error['DeletePost']['successful_fin'])) {
            return $error;
        }

        //profile=> That comment present on some one's Profile then it has been deleted from there as well for reflection.

        if (isset($result_parent_author['testo_id']) && ($result_parent_author['testo_id'])) {
            $publishashotpress = 1;
            $result_parent_author['testo_id'] = isset($result_parent_author['testo_id']) && ($result_parent_author['testo_id']) ? $result_parent_author['testo_id'] : NULL;
            $result_parent_author['from_id'] = isset($result_parent_author['from_id']) && ($result_parent_author['from_id']) ? $result_parent_author['from_id'] : NULL;
            $query_comment = "DELETE FROM testimonials WHERE ((from_id='$userId'||mem_id='$userId') AND tst_id='" . $result_parent_author['testo_id'] . "')||(parent_tst_id='" . $result_parent_author['testo_id'] . "' AND parent_tst_id>0)";
            $result_comment = execute_query($query_comment, false, "delete");
            $result_comment['count'] = isset($result_comment['count']) && ($result_comment['count']) ? $result_comment['count'] : NULL;
            $error = error_CRUD($xmlrequest, $result_comment['count']);
            if ((isset($error['HotPressPostComment']['successful_fin'])) && (!$error['HotPressPostComment']['successful_fin'])) {
                return $error;
            }
        }

        //events=> That comment present on Events then it has been deleted from there as well for reflection.
        if (isset($result_parent_author['eventcmmnt_id']) && ($result_parent_author['eventcmmnt_id'])) {
            $event_data = execute_query("DELETE FROM events_comments WHERE ((from_id='$userId'||mem_id='$userId') AND id='" . $result_parent_author['eventcmmnt_id'] . "')||(parent_id='" . $result_parent_author['eventcmmnt_id'] . "' AND parent_id>0)", true, "delete");
            $event_data['count'] = isset($event_data['count']) && ($event_data['count']) ? $event_data['count'] : NULL;
            $error = error_CRUD($xmlrequest, $event_data['count']);

            if ((isset($error['HotPressPostComment']['successful_fin'])) && (!$error['HotPressPostComment']['successful_fin'])) {
                return $error;
            }
        }
//photos=> That comment present on photos then it has been deleted from there as well for reflection.
        if (isset($result_parent_author['photocomment_id']) && ($result_parent_author['photocomment_id'])) {
            $query_photo = execute_query("DELETE FROM photo_comments WHERE (from_id='$userId'||mem_id='$userId') AND id='" . $result_parent_author['photocomment_id'] . "'||(parent_id='" . $result_parent_author['photocomment_id'] . "' AND parent_id>0)", false, "delete");
            $query_photo['count'] = isset($query_photo['count']) && ($query_photo['count']) ? $query_photo['count'] : NULL;
            $error = error_CRUD($xmlrequest, $query_photo['count']);

            if ((isset($error['HotPressPostComment']['successful_fin'])) && (!$error['HotPressPostComment']['successful_fin'])) {
                return $error;
            }
        }

        if (DEBUG)
            writelog("HotPress:delete_post", $error, true);
        return $error;
    }

    /* Function:delete_post_valid($xmlrequest)
     * Description: validate whether the post exist.
     * Parameters: $xmlrequest=>Request which is sent by user.
      Return: boolean array.
     */

    function delete_post_valid($xmlrequest) {
        $postId = mysql_real_escape_string($xmlrequest['DeletePost']['postId']);
        $error = array();
        $query = "SELECT COUNT(*) FROM bulletin WHERE id='$postId'"; //mem_id='$authorId' AND
        if (DEBUG)
            writelog("hotpress.class.php :: delete_post_valid() :: query:", $query, false);
        $row = execute_query($query, false, "select");
        $error['successful'] = isset($row['COUNT(*)']) && ($row['COUNT(*)']) ? true : false;

        if (DEBUG)
            writelog("HotPress:delete_post_valid", $error, true);
        return $error;
    }

    /* Function:hotpress_photo_upload($xmlrequest)
     * Description: to Upload photo on Hotpress.
     * Parameters: $xmlrequest=>Request which is sent by user.
      Return: boolean array.
     */

    function hotpress_photo_upload($xmlrequest) {
        if (DEBUG)
            writelog("hotpress.class.php :: hotpress_photo_upload() : ", "Start Here ", false);
        $error = array();
        $error = photo_upload($xmlrequest);
        if (DEBUG)
            writelog("hotpress.class.php :: hotpress_photo_upload() : ", $error, true);
        if (DEBUG)
            writelog("hotpress.class.php :: profile_photo_upload() : ", "End Here ", false);
        return $error;
    }

    /* Function:hotpress_photo_upload_valid($xmlrequest)
     * Description: used to validate user,image data.
     * Parameters: $xmlrequest=>Request which is sent by user.
      Return: boolean array.
     */

    function hotpress_photo_upload_valid($xmlrequest) {
        if (DEBUG)
            writelog("hotpress.class.php :: photo_upload_valid() : ", "Start Here ", false);
        $error = array();
        $error = photo_upload_valid($xmlrequest);
        if (DEBUG) {
            writelog("hotpress.class.php :: photo_upload_valid() : ", $error, true);
            writelog("hotpress.class.php :: photo_upload_valid() : ", "End Here ", false);
        }
        return $error;
    }

    /* ---------------------To get Response string---------------------------------------------- */

    /* Function:hotpressPost($response_message, $xmlrequest)
     * Description: used to convert all data array into JSON string
     * Parameters: $xmlrequest=>Request which is sent by user
     *             $response_message=>boolean array
      Return: response string.
     */

    function hotpressPost($response_message, $xmlrequest) {

//        if (isset($response_message['HotPress']['SuccessCode']) && ( $response_message['HotPress']['SuccessCode'] == '000')) {
        //$hotpress = array();
        global $return_codes;
        $pagenumber = $xmlrequest['HotPress']['pageNumber'];
        $hotpress = $this->hotpress_post($xmlrequest, $pagenumber, 20);
		
        $userinfocode = $return_codes['HotPress']['SuccessCode'];
        $userinfodesc = $return_codes['HotPress']['SuccessDesc'];
        /* if (!empty($hotpress) && (is_array($hotpress))) {

          $count = isset($hotpress['count']) && ($hotpress['count']) ? $hotpress['count'] : NULL;
          $postcount = 0;
          $str = '';
          $app_comment = '';
          for ($i = 0; $i < $count; $i++) {
          $eventId = NULL;
          $width_profileImageUrl = NULL;
          $height_profileImageUrl = NULL;
          //to get thumbnail immage.
          if (is_readable($this->local_folder . $hotpress[$i]['profileImageUrl']))
          list($width_profileImageUrl, $height_profileImageUrl) = (isset($hotpress[$i]['profileImageUrl']) && (strlen($hotpress[$i]['profileImageUrl']) > 7)) ? getimagesize($this->local_folder . $hotpress[$i]['profileImageUrl']) : NULL;

          $width_image_link = NULL;
          $height_image_link = NULL;
          if (is_readable($this->local_folder . $hotpress[$i]['image_link'])) {
          $sizee = getimagesize($this->local_folder . $hotpress[$i]['image_link']);
          $width_image_link = $sizee[0];
          $height_image_link = $sizee[1];
          $file_extension = substr($hotpress[$i]['image_link'], strrpos($hotpress[$i]['image_link'], '.') + 1);
          $arr = explode('.', $hotpress[$i]['image_link']);
          $Id = isset($hotpress[$i]['photo_album_id']) && ($hotpress[$i]['photo_album_id']) ? $hotpress[$i]['photo_album_id'] : NULL;
          if (!$Id)
          $Id = isset($hotpress[$i]['id']) && $hotpress[$i]['id'] ? $hotpress[$i]['id'] : NULL;
          if ((!file_exists($this->local_folder . $arr[0] . "_" . $Id . "." . $file_extension)) && ($Id) && (preg_match('/^image\/(jp[e]?g|png|gif)$/', $sizee['mime']))) {
          thumbanail_for_image($Id, $hotpress[$i]['image_link']);
          }
          if (preg_match('/^image\/(jp[e]?g|png|gif)$/', $sizee['mime'])) {
          $hotpress[$i]['image_link'] = isset($hotpress[$i]['image_link']) && (strlen($hotpress[$i]['image_link']) > 7) ? event_image_detail($Id, $hotpress[$i]['image_link'], 1) : NULL;

          list($width_image_link, $height_image_link, $type) = (isset($hotpress[$i]['image_link']) && (strlen($hotpress[$i]['image_link']) > 7)) ? getimagesize($this->local_folder . $hotpress[$i]['image_link']) : NULL;
          }
          }

          $width_link_image = NULL;
          $height_link_image = NULL;
          //to get thumbnail image.
          if (is_readable($this->local_folder . $hotpress[$i]['link_image'])) {
          $sizee = getimagesize($this->local_folder . $hotpress[$i]['link_image']);
          $width_link_image = $sizee[0];
          $height_link_image = $sizee[1];
          $file_extension = substr($hotpress[$i]['link_image'], strrpos($hotpress[$i]['link_image'], '.') + 1);
          $arr = explode('.', $hotpress[$i]['link_image']);
          $Id = NULL;
          if (preg_match('/^index.php\?pg=events\&s\=view\&eve_id\=/', $hotpress[$i]['link_url'])) {
          $Id = preg_replace('/^index.php\?pg=events\&s\=view\&eve_id\=/', '', $hotpress[$i]['link_url']);
          } else {
          $Id = isset($hotpress[$i]['photo_album_id']) && ($hotpress[$i]['photo_album_id']) ? $hotpress[$i]['photo_album_id'] : NULL;
          }
          if (!$Id)
          $Id = isset($hotpress[$i]['id']) && $hotpress[$i]['id'] ? $hotpress[$i]['id'] : NULL;

          if (!file_exists($this->local_folder . $arr[0] . "_" . $Id . "." . $file_extension) && (preg_match('/^image\/(jp[e]?g|png|gif)$/', $sizee['mime']))) {
          thumbanail_for_image($Id, $hotpress[$i]['link_image']);
          }
          if (preg_match('/^image\/(jp[e]?g|png|gif)$/', $sizee['mime'])) {
          $hotpress[$i]['link_image'] = isset($hotpress[$i]['link_image']) && (strlen($hotpress[$i]['link_image']) > 7) ? event_image_detail($Id, $hotpress[$i]['link_image'], 1) : NULL;

          list($width_link_image, $height_link_image) = (isset($hotpress[$i]['link_image']) && (strlen($hotpress[$i]['link_image']) > 7)) ? getimagesize($this->local_folder . $hotpress[$i]['link_image']) : NULL;
          }
          }
          $hotpress[$i]['image_link'] = (isset($hotpress[$i]['image_link']) && (strlen($hotpress[$i]['image_link']) > 7)) ? $this->profile_url . $hotpress[$i]['image_link'] : "";
          $hotpress[$i]['link_image'] = isset($hotpress[$i]['link_image']) && (strlen($hotpress[$i]['link_image']) > 7) ? $this->profile_url . $hotpress[$i]['link_image'] : "";
          $check_set_var = (isset($hotpress[$i]['id']) ? true : false);
          if ($check_set_var)
          $check_set_var = (isset($hotpress[$i]['mem_id']) ? true : false);
          $hotpress[$i]['gender'] = (isset($hotpress[$i]['gender']) ? $hotpress[$i]['gender'] : "");
          $hotpress[$i]['profile_type'] = (isset($hotpress[$i]['profile_type']) ? $hotpress[$i]['profile_type'] : "");
          //                print_r($hotpress[$i]);
          $hotpress[$i]['profileImageUrl'] = isset($hotpress[$i]['is_facebook_user']) && (strlen($hotpress[$i]['profileImageUrl']) > 7) && ($hotpress[$i]['is_facebook_user'] == 'y' || $hotpress[$i]['is_facebook_user'] == 'Y') ? $hotpress[$i]['profileImageUrl'] : ((isset($hotpress[$i]['profileImageUrl']) && (strlen($hotpress[$i]['profileImageUrl']) > 7)) ? $this->profile_url . $hotpress[$i]['profileImageUrl'] : $this->profile_url . default_images($hotpress[$i]['gender'], $hotpress[$i]['profile_type']));
          //                print_r($hotpress[$i]['profileImageUrl']);
          //------user profile information-----
          if ((isset($hotpress[$i]['user_profile']['profileImageUrl'])) && ($hotpress[$i]['user_profile']['profileImageUrl']))
          $hotpress[$i]['user_profile']['profileImageUrl1'] = ((isset($hotpress[$i]['user_profile']['is_facebook_user'])) && (strlen($hotpress[$i]['user_profile']['profileImageUrl']) > 7) && ($hotpress[$i]['user_profile']['is_facebook_user'] == 'y' || $hotpress[$i]['user_profile']['is_facebook_user'] == 'Y')) ? $hotpress[$i]['user_profile']['profileImageUrl'] : ((isset($hotpress[$i]['user_profile']['profileImageUrl']) && (strlen($hotpress[$i]['user_profile']['profileImageUrl']) > 7)) ? $this->profile_url . $hotpress[$i]['user_profile']['profileImageUrl'] : $this->profile_url . default_images($hotpress[$i]['user_profile']['gender'], $hotpress[$i]['user_profile']['profile_type']));

          // $hotpress[$i]['user_profile']['profileImageUrl'] = (isset($hotpress[$i]['user_profile']['profileImageUrl'])) ? $hotpress[$i]['user_profile']['profileImageUrl'] : 0;
          $hotpress[$i]['user_profile']['name'] = (isset($hotpress[$i]['user_profile']['name'])) ? $hotpress[$i]['user_profile']['name'] : "";
          $hotpress[$i]['user_profile']['gender'] = (isset($hotpress[$i]['user_profile']['gender'])) ? $hotpress[$i]['user_profile']['gender'] : "";
          $hotpress[$i]['user_profile']['profile_type'] = (isset($hotpress[$i]['user_profile']['profile_type'])) ? $hotpress[$i]['user_profile']['profile_type'] : "";
          //print_r($hotpress[$i]['user_profile']);
          //to show events details
          $hotpress[$i]['event_info']['even_id'] = isset($hotpress[$i]['event_info']['even_id']) && ($hotpress[$i]['event_info']['even_id']) ? $hotpress[$i]['event_info']['even_id'] : NULL;
          $hotpress[$i]['event_info']['even_title'] = isset($hotpress[$i]['event_info']['even_title']) && ($hotpress[$i]['event_info']['even_title']) ? $hotpress[$i]['event_info']['even_title'] : NULL;

          if(!empty($hotpress[$i]['appearance_id'])){
          $hotpress[$i]['appearance_id']['venue_id'] = (!empty($hotpress[$i]['appearance_id']['venue_id']) && $hotpress[$i]['appearance_id']['venue_id'] != 0) ? $hotpress[$i]['appearance_id']['venue_id'] : "0";
          $hotpress[$i]['appearance_id']['announce_arrival_id'] = (!empty($hotpress[$i]['appearance_id']['announce_arrival_id']) && $hotpress[$i]['appearance_id']['announce_arrival_id'] != 0) ? $hotpress[$i]['appearance_id']['announce_arrival_id'] : "0";
          $hotpress[$i]['appearance_id']['badge_image'] =  (!empty($hotpress[$i]['appearance_id']['badge_image'])) ?ROOT_URL . $hotpress[$i]['appearance_id']['badge_image']:"0" ;
          $hotpress[$i]['appearance_id']['bottel_image'] = (!empty($hotpress[$i]['appearance_id']['bottel_image']))?ROOT_URL . $hotpress[$i]['appearance_id']['bottel_image'] :"0";
          }else{
          $hotpress[$i]['appearance_id']['venue_id'] = "0";
          $hotpress[$i]['appearance_id']['announce_arrival_id'] = "0";
          $hotpress[$i]['appearance_id']['badge_image'] = "0";
          $hotpress[$i]['appearance_id']['bottel_image'] = "0";

          }
          $hotpress[$i]['name'] = (isset($hotpress[$i]['name']) ? $hotpress[$i]['name'] : "");
          $hotpress[$i]['subj'] = (isset($hotpress[$i]['subj']) ? $hotpress[$i]['subj'] : "");
          $hotpress[$i]['body'] = (isset($hotpress[$i]['body']) ? $hotpress[$i]['body'] : "");
          $hotpress[$i]['messageType'] = (isset($hotpress[$i]['messageType']) ? $hotpress[$i]['messageType'] : "");
          $hotpress[$i]['date'] = (isset($hotpress[$i]['date']) ? $hotpress[$i]['date'] : "");
          $hotpress[$i]['tot_comment'] = (isset($hotpress[$i]['tot_comment']) ? $hotpress[$i]['tot_comment'] : "");
          $hotpress[$i]['like'] = (isset($hotpress[$i]['like']) ? $hotpress[$i]['like'] : "");
          $hotpress[$i]['user_like'] = (isset($hotpress[$i]['user_like']) ? $hotpress[$i]['user_like'] : "");
          $hotpress[$i]['link_url'] = (isset($hotpress[$i]['link_url']) ? $hotpress[$i]['link_url'] : "");
          $hotpress[$i]['youtubeLink'] = (isset($hotpress[$i]['youtubeLink']) ? $hotpress[$i]['youtubeLink'] : "");

          $hotpress[$i]['photo_id'] = (isset($hotpress[$i]['photo_id']) ? $hotpress[$i]['photo_id'] : "");
          $hotpress[$i]['photo_album_id'] = (isset($hotpress[$i]['photo_album_id']) ? $hotpress[$i]['photo_album_id'] : "");
          $hotpress[$i]['auto_genrate_text'] = isset($hotpress[$i]['auto_genrate_text']) ? $hotpress[$i]['auto_genrate_text'] : "";

          $date = time_difference($hotpress[$i]['date']);
          if ($check_set_var) {
          $input = $hotpress[$i]['subj'] . $hotpress[$i]['body'];
          $input = str_replace('\\', '', $input);
          if (preg_match(REGEX_URL, $input, $url)) {
          $postType = extract_url($input);
          $postType = strip_tags($postType);
          $postType = str_replace(array("\r\n", "\r", "\n", "<br />", "\"", "\<a"), "\\n", $postType);
          } else {
          $postType = 'text';
          }

          $hotpress[$i]['subj'] = str_replace('\\', "", $hotpress[$i]['subj']);
          //commented on 24 nov 2011 :: aarya :: $hotpress[$i]['body'] = str_replace('\\', "", $hotpress[$i]['body']);

          $hotpress[$i]['subj'] = str_replace(array("\r\n", "\r", "\n", "<br />", "<br/>", "<br>"), "\\n", $hotpress[$i]['subj']);
          //commented on 24 nov 2011 :: aarya ::$hotpress[$i]['body'] = str_replace(array("\r\n", "\r", "\n", "<br />", "<br/>", "<br>"), "\\n", $hotpress[$i]['body']);

          $hotpress[$i]['subj'] = strip_tags($hotpress[$i]['subj']);
          //commented on 24 nov 2011 :: aarya :: $hotpress[$i]['body'] = strip_tags($hotpress[$i]['body']);

          $hotpress[$i]['subj'] = str_replace(array("\""), "", $hotpress[$i]['subj']);
          //commented on 24 nov 2011 :: aarya :: $hotpress[$i]['body'] = str_replace(array("\""), "", $hotpress[$i]['body']);

          $hotpress[$i]['subj'] = subanchor($hotpress[$i]['subj']);
          //commented on 24 nov 2011 :: aarya :: $hotpress[$i]['body'] = subanchor($hotpress[$i]['body']);
          /* commented on 24 nov 2011 :: aarya :: if ($hotpress[$i]['appearance_id']['announce_arrival_id'] != 0) {
          $hotpress_body = substr(str_replace($hotpress[$i]['name'], '', $hotpress[$i]['body']), 0, (strpos(str_replace($hotpress[$i]['name'], '', $hotpress[$i]['body']), '@')) + 1);
          $app_comment = ' "appUserComment":"' . substr($hotpress[$i]['body'], (strpos($hotpress[$i]['body'], '\n\n'))) . '",';
          } else {
          $hotpress_body = $hotpress[$i]['body'];
          $app_comment = '';
          } */

        /* Added below line on 24 Nov 2011 :: aarya 

          $hotpress_body = get_organized_comment_data($hotpress[$i]['body'], $hotpress[$i]['name']);

          //$hotpress_body = htmlspecialchars_decode($hotpress_body,ENT_QUOTES);
          $hotpress_body = str_replace("'","\'",$hotpress_body);
          //print_r($hotpress_body);
          $app_comment = '';
          if ((isset($hotpress[$i]['from_id'])) && ($hotpress[$i]['from_id'] > 0)) {//$hotpress[$i]['user_profile']['mem_id']
          $profile_info = '"profileID":"' .str_replace('"', '\"',$hotpress[$i]['from_id']). '",
          "profileImgURL":"' .str_replace('"', '\"',$hotpress[$i]['user_profile']['profileImageUrl1']). '",
          "height":"' .str_replace('"', '\"',$height_profileImageUrl). '",
          "width":"' .str_replace('"', '\"',$width_profileImageUrl). '",
          "profileName":"'.trim(preg_replace('/\s+/', ' ', str_replace("\'","'",htmlspecialchars_decode(str_replace('"','\"',$hotpress[$i]['user_profile']['name']),ENT_QUOTES)))).'",
          "profileGender":"' .str_replace('"', '\"',$hotpress[$i]['user_profile']['gender']). '",
          "profileType":"' .str_replace('"', '\"',$hotpress[$i]['user_profile']['profile_type']). '",';
          } else {
          $profile_info = '';
          }
          $postVia = ((isset($hotpress[$i]['post_via'])) && ($hotpress[$i]['post_via'])) ? "iPhone" : "";
          //htmlspecialchars_decode($hotpress_body,ENT_QUOTES)
          $str_temp = '{
          ' . $profile_info . '
          "postId":"' . $hotpress[$i]['id'] . '",
          "appearanceId":"' .$hotpress[$i]['appearance_id']['announce_arrival_id'] . '",
          "venueId":"' . $hotpress[$i]['appearance_id']['venue_id'] . '",
          "venueName":"'.trim(preg_replace('/\s+/', ' ', str_replace("\'","'",htmlspecialchars_decode(str_replace('"','\"',$hotpress[$i]['appearance_id']['venue_name']),ENT_QUOTES)))).'",
          "venueName":"' . $hotpress[$i]['appearance_id']['venue_name'] . '",
          "badgeImage":"' . $hotpress[$i]['appearance_id']['badge_image'] . '",
          "bottleImage":"' . $hotpress[$i]['appearance_id']['bottel_image'] . '",
          "postLikeByUser":"' .str_replace('"', '\"',$hotpress[$i]['user_like']). '",
          "authorID":"' .str_replace('"', '\"',$hotpress[$i]['mem_id']). '",
          "authorProfileImgURL":"' .str_replace('"', '\"',$hotpress[$i]['profileImageUrl']). '",
          "width":"' .str_replace('"', '\"',$width_profileImageUrl). '",
          "height":"' .str_replace('"', '\"',$height_profileImageUrl) . '",
          "authorName":"' .trim(preg_replace('/\s+/', ' ', str_replace("\'","'",htmlspecialchars_decode(str_replace('"','\"',$hotpress[$i]['name']),ENT_QUOTES)))) . '",
          "authorGender":"' . $hotpress[$i]['gender'] . '",
          "profileType":"' .str_replace('"', '\"',$hotpress[$i]['profile_type']). '",
          "postSubj":"' .trim(preg_replace('/\s+/', ' ', str_replace("\'","'",htmlspecialchars_decode(str_replace('"','\"',$hotpress[$i]['subj']),ENT_QUOTES)))) . '",
          "postBody":"' .trim(preg_replace('/\s+/', ' ', str_replace("\'","'",htmlspecialchars_decode(str_replace('"','\"',$hotpress_body),ENT_QUOTES)))) . '",
          "postType":"' .trim(preg_replace('/\s+/', ' ', str_replace("\'","'",htmlspecialchars_decode(str_replace('"','\"',$postType),ENT_QUOTES)))).'",
          "photoId":"' .trim(preg_replace('/\s+/', ' ', str_replace("\'","'",htmlspecialchars_decode(str_replace('"','\"',$hotpress[$i]['photo_id']),ENT_QUOTES)))).'",
          "albumId":"' .str_replace('"', '\"',$hotpress[$i]['album_id']). '",
          "uploadedImage":"' .str_replace('"', '\"',$hotpress[$i]['image_link']). '",
          "width_image_link":"' .str_replace('"', '\"',$width_image_link). '",
          "height_image_link":"' .str_replace('"', '\"',$height_image_link). '",
          "youtubeLink":"' .str_replace('"', '\"',$hotpress[$i]['youtubeLink']). '",
          "link_image":"' .str_replace('"', '\"',$hotpress[$i]['link_image']). '",
          "width_link_image":"' .str_replace('"', '\"',$width_link_image). '",
          "height_link_image":"' .str_replace('"', '\"',$height_link_image). '",
          "link_url":"' .str_replace('"', '\"',$hotpress[$i]['link_url']). '",
          "postTimestamp":"' .str_replace('"', '\"',$date). '",
          "postVia":"' .str_replace('"', '\"',$postVia). '",
          "eventId":"' . $hotpress[$i]['event_info']['even_id'] . '",
          "eventTitle":"' .trim(preg_replace('/\s+/', ' ', str_replace("\'","'",htmlspecialchars_decode(str_replace('"','\"',$hotpress[$i]['event_info']['even_title']),ENT_QUOTES)))).'",
          "commentsCount":"' .str_replace('"', '\"',$hotpress[$i]['tot_comment']). '",
          ' . $app_comment . '
          "likeCount":"' .str_replace('"', '\"',$hotpress[$i]['like']). '",
          "autoGenrateText":"' .trim(preg_replace('/\s+/', ' ', str_replace("\'","'",htmlspecialchars_decode(str_replace('"','\"',$hotpress[$i]['auto_genrate_text']),ENT_QUOTES)))).'"
          }';
          $postcount++;
          $str = $str . $str_temp;
          $str = $str . ',';
          }
          }
          $str = substr($str, 0, strlen($str) - 1); */
        $response_str = response_repeat_string();
        if (!empty($hotpress['postcount']) && $hotpress['postcount'] > 0) {
            $response_mess = '
	    {
	       ' . $response_str . '
	       "HotPress":{
		  "errorCode":"' . $userinfocode . '",
		  "errorMsg":"' . $userinfodesc . '",
		  "postCount":"' . $hotpress['postcount'] . '",
		  "Posts":[
		     ' . $hotpress['responseString'] . '
		  ]
	       }
	    }';
        } else {
            $userinfocode = $return_codes['HotPress']['ErrorCode'];
            $userinfodesc = $return_codes['HotPress']['ErrorDesc'];
            $response_mess = get_response_string("HotPress", $userinfocode, $userinfodesc);
        }
        if (DEBUG)
            writelog("Response:hotpress():", $response_mess, false);

        return getValidJSON($response_mess);
    }

    /* Function:commentsOnHotPressPost($response_message, $xmlrequest)
     * Description: used to convert all data array into JSON string of sub comments.
     * Parameters: $xmlrequest=>Request which is sent by user.
     *             $response_message=>boolean array
      Return: response string.
     */

    function commentsOnHotPressPost($response_message, $xmlrequest) {
        if (isset($response_message['CommentsOnHotPressPost']['SuccessCode']) && ( $response_message['CommentsOnHotPressPost']['SuccessCode'] == '000')) {
            $postinfo = array();
            $pageNumber = $xmlrequest['CommentsOnHotPressPost']['pageNumber'];
            $postinfo = $this->comment_on_hotpress_post($xmlrequest, $pageNumber, 20);

            $userinfocode = $response_message['CommentsOnHotPressPost']['SuccessCode'];
            $userinfodesc = $response_message['CommentsOnHotPressPost']['SuccessDesc'];
            $count = isset($postinfo['count']) && ($postinfo['count']) ? $postinfo['count'] : 0;
            $postcount = 0;
            $str = '';
            for ($i = 0; $i < $count; $i++) {
                $postinfo[$i]['id'] = isset($postinfo[$i]['id']) && ($postinfo[$i]['id']) ? $postinfo[$i]['id'] : NULL;
                $postinfo[$i]['from_id'] = isset($postinfo[$i]['from_id']) && ($postinfo[$i]['from_id']) ? $postinfo[$i]['from_id'] : NULL;
                $postinfo[$i]['profilenam'] = isset($postinfo[$i]['profilenam']) && ($postinfo[$i]['profilenam']) ? $postinfo[$i]['profilenam'] : NULL;
                $postinfo[$i]['date'] = isset($postinfo[$i]['date']) && ($postinfo[$i]['date']) ? $postinfo[$i]['date'] : NULL;
                if (isset($postinfo[$i]['id']) && isset($postinfo[$i]['profilenam']) && isset($postinfo[$i]['date'])) {
                    $postinfo[$i]['subj'] = isset($postinfo[$i]['subj']) && ($postinfo[$i]['subj']) ? $postinfo[$i]['subj'] : NULL;

                    $input = $postinfo[$i]['subj'] . $postinfo[$i]['body'];
                    $input = str_replace('\\', '', $input);
                    if (preg_match(REGEX_URL, $input, $url)) {
                        $commenHotPresstType = extract_url($input);
                        $commenHotPresstType = str_replace(array("\r\n", "\r", "\n", "<br />", "\""), "\\n", $commenHotPresstType);
                    } else {
                        $commentType = 'text';
                    }
                    $postinfo[$i]['subj'] = str_replace('\\', "", $postinfo[$i]['subj']);
                    $postinfo[$i]['body'] = str_replace('\\', "", $postinfo[$i]['body']);
                    $postinfo[$i]['subj'] = str_replace(array("\r\n", "\r", "\n", "<br />", "<br/>"), "\\n", $postinfo[$i]['subj']);
                    $postinfo[$i]['body'] = str_replace(array("\r\n", "\r", "\n", "<br />", "<br/>"), "\\n", $postinfo[$i]['body']);
                    $postinfo[$i]['subj'] = strip_tags($postinfo[$i]['subj']);
                    $postinfo[$i]['body'] = strip_tags($postinfo[$i]['body']);
                    $postinfo[$i]['subj'] = str_replace(array("\""), "", $postinfo[$i]['subj']);
                    $postinfo[$i]['body'] = str_replace(array("\""), "", $postinfo[$i]['body']);
                    $postinfo[$i]['subj'] = subanchor($postinfo[$i]['subj']);
                    $postinfo[$i]['body'] = subanchor($postinfo[$i]['body']);
                    $postinfo[$i]['photo_b_thumb'] = ((isset($postinfo[$i]['is_facebook_user'])) && (strlen($postinfo[$i]['photo_b_thumb']) > 7) && ($postinfo[$i]['is_facebook_user'] == 'y' || $postinfo[$i]['is_facebook_user'] == 'Y')) ? $postinfo[$i]['photo_b_thumb'] : ((isset($postinfo[$i]['photo_b_thumb']) && (strlen($postinfo[$i]['photo_b_thumb']) > 7)) ? $this->profile_url . $postinfo[$i]['photo_b_thumb'] : $this->profile_url . default_images($postinfo[$i]['gender'], $postinfo[$i]['profile_type']));

                    $date = time_difference($postinfo[$i]['date']);
                    $postVia = ((isset($postinfo[$i]['post_via'])) && ($postinfo[$i]['post_via'])) ? "iPhone" : "";

                    $str_temp = '{
            "commentId":"' . str_replace('"', '\"', $postinfo[$i]['id']) . '",
            "authorID":"' . str_replace('"', '\"', $postinfo[$i]['mem_id']) . '",
            "authorProfileImgURL":"' . str_replace('"', '\"', $postinfo[$i]['photo_b_thumb']) . '",
            "authorGender":"' . str_replace('"', '\"', $postinfo[$i]['gender']) . '",
            "profileType":"' . str_replace('"', '\"', $postinfo[$i]['profile_type']) . '",
            "authorName":"' . str_replace('"', '\"', $postinfo[$i]['profilenam']) . '",
            "commentSubj":"' . trim(preg_replace('/\s+/', ' ', str_replace("\'", "'", htmlspecialchars_decode(str_replace('"', '\"', $postinfo[$i]['subj']), ENT_QUOTES)))) . '",
            "commentBody":"' . trim(preg_replace('/\s+/', ' ', str_replace("\'", "'", htmlspecialchars_decode(str_replace('"', '\"', $postinfo[$i]['body']), ENT_QUOTES)))) . '",
            "commentType":"' . str_replace('"', '\"', $commentType) . '",
            "commentTimestamp":"' . str_replace('"', '\"', $date) . '",
            "postVia":"' . str_replace('"', '\"', $postVia) . '"
         }';
                    $postcount++;
                    $str = $str . $str_temp;
                    $str = $str . ',';
                }
            }
            $str = substr($str, 0, strlen($str) - 1);
            $response_str = response_repeat_string();
            $response_mess = '
{
   ' . $response_str . '
   "CommentsOnHotPressPost":{
      "errorCode":"' . $userinfocode . '",
      "errorMsg":"' . $userinfodesc . '",
      "commentsCount":"' . $postcount . '",
      "totalCommentsCount":"' . $postinfo['totalrecords'] . '",
      "Comments":[
         ' . $str . '
      ]
   }
   }';
        } else {
            $userinfocode = $response_message['CommentsOnHotPressPost']['ErrorCode'];
            $userinfodesc = $response_message['CommentsOnHotPressPost']['ErrorDesc'];
            $response_mess = get_response_string("CommentsOnHotPressPost", $userinfocode, $userinfodesc);
        }
        if (DEBUG)
            writelog("Response:commentsOnHotPressPost():", $response_mess, false);
        return getValidJSON($response_mess);
    }

    /* Function:hotPressPostComment($response_message, $xmlrequest)
     * Description: used to convert all data array into JSON string related to whether comment has been posted or not on Hotpress
     * Parameters: $xmlrequest=>Request which is sent by user
     *             $response_message=>boolean array
      Return: response string.
     */

    function hotPressPostComment($response_message, $xmlrequest) {
        if (isset($response_message['HotPressPostComment']['SuccessCode']) && ( $response_message['HotPressPostComment']['SuccessCode'] == '000')) {
            $userinfo = array();
            $userinfo = $this->hot_press_post_comment($xmlrequest);

            $str_id = isset($userinfo['last_id']) && ($userinfo['last_id']) ? ' "postId":"' . $userinfo['last_id'] . '",' : NULL;

            if ((isset($userinfo['HotPressPostComment']['successful_fin'])) && (!$userinfo['HotPressPostComment']['successful_fin'])) {
                $obj_error = new Error();
                $response_message = $obj_error->error_type("HotPressPostComment", $userinfo);

                $userinfocode = $response_message['HotPressPostComment']['ErrorCode'];
                $userinfodesc = $response_message['HotPressPostComment']['ErrorDesc'];
                $response_mess = $response_mess = get_response_string("HotPressPostComment", $userinfocode, $userinfodesc);
                return getValidJSON($response_mess);
            }
            $userinfocode = $response_message['HotPressPostComment']['SuccessCode'];
            $userinfodesc = $response_message['HotPressPostComment']['SuccessDesc'];
            $response_str = response_repeat_string();
            $response_mess = '
{
  ' . $response_str . '
   "HotPressPostComment":{
   ' . $str_id . '
      "errorCode":"' . $userinfocode . '",
      "errorMsg":"' . $userinfodesc . '"
   }
}
 	';
        } else {
            $userinfocode = $response_message['HotPressPostComment']['ErrorCode'];
            $userinfodesc = $response_message['HotPressPostComment']['ErrorDesc'];
            $response_mess = $response_mess = get_response_string("HotPressPostComment", $userinfocode, $userinfodesc);
        }
        if (DEBUG)
            writelog("Response:hotPressPostComment():", $response_mess, false);
        return getValidJSON($response_mess);
    }

    /* Function:deleteHotpressPost($response_message, $xmlrequest)
     * Description: returns JSON response for Hotpress deleted, which has been posted by login user
     * Parameters: $xmlrequest=>Request which is sent by user
     *             $response_message=>boolean array after validation
      Return: response string.
     */

    function deleteHotpressPost($response_message, $xmlrequest) {
        if (isset($response_message['DeletePost']['SuccessCode']) && ( $response_message['DeletePost']['SuccessCode'] == '000')) {
            $userinfo = array();
            $post_details = $this->delete_post($xmlrequest);
            if ((isset($userinfo['DeletePost']['successful_fin'])) && (!$userinfo['DeletePost']['successful_fin'])) {
                $obj_error = new Error();
                $response_message = $obj_error->error_type("DeletePost", $userinfo);

                $userinfocode = $response_message['DeletePost']['ErrorCode'];
                $userinfodesc = $response_message['DeletePost']['ErrorDesc'];
                $response_mess = $response_mess = get_response_string("DeletePost", $userinfocode, $userinfodesc);
                return getValidJSON($response_mess);
            }
            $userinfocode = $response_message['DeletePost']['SuccessCode'];
            $userinfodesc = $response_message['DeletePost']['SuccessDesc'];
            $response_str = response_repeat_string();
            $response_mess = '
{
   ' . $response_str . '
   "DeletePost":{
       "errorCode":"' . $userinfocode . '",
       "errorMsg":"' . $userinfodesc . '"
   }
}
';
        } else {
            $userinfocode = $response_message['DeletePost']['ErrorCode'];
            $userinfodesc = $response_message['DeletePost']['ErrorDesc'];
            $response_mess = get_response_string("DeletePost", $userinfocode, $userinfodesc);
        }
        if (DEBUG)
            writelog("Response:deleteHotpressPost():", $response_mess, false);
        return getValidJSON($response_mess);
    }

    /* Function:likePostList($response_message, $xmlrequest)
     * Description: used to Display List of user associated with specific Post.
     * Parameters: $xmlrequest=>Request which is sent by user
     *              $response_message=>boolean array
      Return: response string.
     */

    function likePostList($response_message, $xmlrequest) {
        if (isset($response_message['LikePostList']['SuccessCode']) && ( $response_message['LikePostList']['SuccessCode'] == '000')) {
            $user_list = array();
            $user_list = $this->like_post_list($xmlrequest);
            $userinfocode = $response_message['LikePostList']['SuccessCode'];
            $userinfodesc = $response_message['LikePostList']['SuccessDesc'];

            $count = isset($user_list['count']) && ( $user_list['count']) ? $user_list['count'] : 0;
            $likecount = 0;
            $str = '';
            for ($i = 0; $i < $count; $i++) {

                $user_list[$i]['photo_thumb'] = ((isset($user_list[$i]['is_facebook_user'])) && (strlen($user_list[$i]['photo_thumb']) > 7) && ($user_list[$i]['is_facebook_user'] == 'y' || $user_list[$i]['is_facebook_user'] == 'Y')) ? $user_list[$i]['photo_thumb'] : ((isset($user_list[$i]['photo_thumb']) && (strlen($user_list[$i]['photo_thumb']) > 7)) ? $this->profile_url . $user_list[$i]['photo_thumb'] : $this->profile_url . default_images($user_list[$i]['gender'], $user_list[$i]['profile_type']));
                $str_temp = '{

            "userId":"' . $user_list[$i]['mem_id'] . '",
            "userPrivacySetting":"' . str_replace('"', '\"', $user_list[$i]['privacy']) . '",
            "userProfileImgURL":"' . str_replace('"', '\"', $user_list[$i]['photo_thumb']) . '",
            "userGender":"' . str_replace('"', '\"', $user_list[$i]['gender']) . '",
            "profileType":"' . str_replace('"', '\"', $user_list[$i]['profile_type']) . '",
            "userName":"' . str_replace('"', '\"', $user_list[$i]['profilenam']) . '"
         }';
                $likecount++;
                $str = $str . $str_temp;
                $str = $str . ',';
            }
            $str = substr($str, 0, strlen($str) - 1);
            $response_str = response_repeat_string();
            $response_mess = '
{
   ' . $response_str . '
   "LikePostList":{
      "errorCode":"' . $userinfocode . '",
      "errorMsg":"' . $userinfodesc . '",
      "likeCount":"' . $likecount . '",
      "LikeList":[
        ' . $str . '
      ]
   }
}
';
        } else {
            $userinfocode = $response_message['LikePostList']['ErrorCode'];
            $userinfodesc = $response_message['LikePostList']['ErrorDesc'];
            $response_mess = get_response_string("LikePostList", $userinfocode, $userinfodesc);
        }
        return getValidJSON($response_mess);
    }

    /* Function:likePost($response_message, $xmlrequest)
     * Description: used to display JSON response for whether post has been liked by user or not.
     * Parameters: $xmlrequest=>Request which is sent by user.
     *              $response_message=>boolean array
      Return: response string.
     */

    function likePost($response_message, $xmlrequest) {
        if (isset($response_message['LikePost']['SuccessCode']) && ( $response_message['LikePost']['SuccessCode'] == '000')) {
            $userinfo = array();
            $userinfo = $this->like_post($xmlrequest);

            if ((isset($userinfo['successful_fin'])) && (!$userinfo['successful_fin'])) {
                $obj_error = new Error();
                $response_message = $obj_error->error_type("LikePost", $userinfo);

                $userinfocode = $response_message['LikePost']['ErrorCode'];
                $userinfodesc = $response_message['LikePost']['ErrorDesc'];
                $response_mess = $response_mess = get_response_string("LikePost", $userinfocode, $userinfodesc);
                return getValidJSON($response_mess);
            }
            $userinfocode = $response_message['LikePost']['SuccessCode'];
            $userinfodesc = $response_message['LikePost']['SuccessDesc'];
            $response_str = response_repeat_string();
            $response_mess = '
{
  ' . $response_str . '
   "LikePost":{
      "errorCode":"' . $userinfocode . '",
      "errorMsg":"' . $userinfodesc . '"
   }
}
';
        } else {
            $userinfocode = $response_message['LikePost']['ErrorCode'];
            $userinfodesc = $response_message['LikePost']['ErrorDesc'];
            $response_mess = get_response_string("LikePost", $userinfocode, $userinfodesc);
        }
        return getValidJSON($response_mess);
    }

}

?>
