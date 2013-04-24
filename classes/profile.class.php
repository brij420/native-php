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
  File-name : profile.class.php
  Directory Path  : $/MySNL/Deliverables/Code/MySNL_WebServiceV2/profile.class.php/
  Author    : Brijesh Kumar
  Date    : 12/08/2011
  Modified By   : N/A
  Date : N/A

  Include Files : none
  CSS File(s)   : none

  Functions Used
  PHP     :  profile_info,profile_valid,profile_showall_comments,get_photo_id,get_user_info,get_total_comment_count,profile_showall_comments_valid,profile_sub_comments,profile_sub_comments_valid,post_comment_on_profile,error_CRUD,post_comment_on_profile_valid,delete_post_from_profile,delete_post_from_profile_valid,profile_info_short_desc,take_over_profile,is_take_over_profile,profileInfo,profileParentComment,profileSubComments,postCommentOnProfile,deleteProfileMessage,profileInfoShortDesc,takeOverProfile,profile_photo_upload,profile_photo_upload_valid

  DataBase Table(s)  : members,bulletin,testimonial,club_venue,mytravel,vip_lounge_details,profiles,network,photo_albums

  Global Variable(s)  : LOCAL_FOLDER: Path where all the images save.
  PROFILE_IMAGE_SITEURL:website url

  Description:  File  to Display,Delete,Send comments on Profile.

  Reviwed By  :
  Reviwed Date:
 * ************************************************************************************* */

/*  class Profile
  Purpose:We can send comment on Profile Like Upload Image,Post comment self Profile or any of our Entourage Profile.We can share as a Hotpress.
 *        Display all stuff(Self Profile+comment by Entourage)
 *        User can Delete his own comment as well as if the posted comment is main comment then user can delete all subcomments+User can Delete comment sent by Entourage.
 *        User can reply comment.
 *        Profile module is linked with Hotpress and Photo Module.
 *

 * Returns : None
 */

class Profile {

    var $profile_url = PROFILE_IMAGE_SITEURL;
    var $local_folder = LOCAL_FOLDER;

    /* Function:profile_info($xmlrequest)
      Description: to fetch the profile info related to user or his/her Entourage(411).
     * Parameters: $xmlrequest=>request by user
      Return: Array of Data related to profile.
     *  */

    function profile_info($xmlrequest) {
	if (DEBUG)
	    writelog("profile.class.php :: profile_info() :: query:", "<----Start Here---->", false);

	$userid = mysql_real_escape_string($xmlrequest['Profile']['userId']);
	$entourageId = mysql_real_escape_string($xmlrequest['Profile']['entourageId']);

	$query = "select states.state_name as state,country.country_name as country,club_venue.door_policy,club_venue.parking,IFNULL(club_venue.address,members.saddress) as caddress,club_venue.name as cname,club_venue.event_name as cevent_name,club_venue.name as cvenuename,club_venue.business_phone,club_venue.website as cwebsite,club_venue.club_type,club_venue.description as cdescription,club_venue.music as cmusic,club_venue.club_admission,club_venue.house_of_operation,club_venue.capacity,club_venue.what_to_expect,club_venue.services,club_venue.alcohol,club_venue.age_limit,club_venue.specials,club_venue.purchase_ticket,club_venue.guidelines,club_venue.widget,
        mytravel.whatatrip,mytravel.restaurants,mytravel.topdestinations,mytravel.topgateaways,mytravel.hotclubs,mytravel.besthotel,mytravel.finerestaurants,mytravel.whatnext,mytravel.destinations,mytravel.gataways,mytravel.clubs as club,mytravel.hotel,
        members.gender,members.birthday,members.privacy,members.city,members.ethnicity,members.profile_url,members.profilenam,members.photo,members.is_facebook_user,members.profile_type,members.interests as interest,members.quote,members.latitude,
  members.longitude,vip_lounge_details.paypal_email,vip_lounge_details.charges,vip_lounge_details.instant_access,vip_lounge_details.what_is,vip_lounge_details.res_experience,vip_lounge_details.fantasies,vip_lounge_details.sex_experience,vip_lounge_details.perfect_soul,vip_lounge_details.crush,vip_lounge_details.heart_breken,vip_lounge_details.heart_been_breken,vip_lounge_details.other_sex,vip_lounge_details.favorite,vip_lounge_details.turn_on ,vip_lounge_details.ego,vip_lounge_details.music,vip_lounge_details.bg_image,
        profiles.assotiations,profiles.p_positions,profiles.p_companies,profiles.skills,profiles.here_for,profiles.interests,profiles.hometown,profiles.schools,languages,profiles.website,profiles.books,profiles.music,profiles.movies,profiles.travel,profiles.clubs,profiles.about,profiles.meet_people,profiles.position,profiles.company,profiles.occupation,profiles.specialities,profiles.overview,profiles.style_card,profiles.college,profiles.highschool,profiles.job,profiles.marstat,profiles.religion,profiles.smoker,profiles.drinker,profiles.children,profiles.income,profiles.education,profiles.hobbies,industries.name as industry
       FROM members LEFT JOIN club_venue ON club_venue.mem_id=members.mem_id
        LEFT JOIN mytravel ON mytravel.mem_id=members.mem_id
        LEFT JOIN  `profiles` ON
        profiles.mem_id=members.mem_id
        LEFT JOIN industries ON (industries.ind_id=profiles.industry ) LEFT JOIN vip_lounge_details ON (vip_lounge_details.mem_id=members.mem_id)
		LEFT JOIN country
    ON (members.country = country.country_code or members.country=country.country_name)
  LEFT JOIN states
    ON (country.country_code = states.country_code
        AND members.state = states.state_code)
        where members.mem_id='$entourageId'";
	if (DEBUG)
	    writelog("profile.class.php :: profile_info() :: query:", $query, false);
	$walls = execute_query($query, false, "select");
	if (DEBUG)
	    writelog("Profile:profile_info", $walls, true);

	return $walls;
    }

    /*
     * Function:profile_valid($xmlrequest)
      Description: to validate User.
     * Parameters: $xmlrequest=>request by user
      Return: boolean Array
     *  */

    function profile_valid($xmlrequest) {

	$request_keys = array_keys($xmlrequest);
	$key = $request_keys[1];
	$error = array();
	$userId = mysql_real_escape_string($xmlrequest[$key]['userId']);
	$entourageId = mysql_real_escape_string($xmlrequest[$key]['entourageId']);

	$query = execute_query("SELECT is_take_over_profile FROM members WHERE mem_id='$userId'", false, "select");
	if ($query['is_take_over_profile'] == 'Y')
	    $query = "SELECT COUNT(*) FROM members WHERE mem_id='$userId'"; // and verified='n' and ban = 'n' AND is_take_over_profile='Y'
	else
	    $query = "SELECT COUNT(*) FROM members WHERE mem_id='$userId'"; // and email != '' and verified='y' and ban = 'n'
	if (DEBUG)
	    writelog("profile.class.php :: profile_valid() :: query:", $query, false);
	$result = execute_query($query, false);
	$error['successful'] = isset($result['COUNT(*)']) && ($result['COUNT(*)']) ? true : false;
	if ($error['successful'] == TRUE) {
	    $query = execute_query("SELECT is_take_over_profile FROM members WHERE mem_id='$entourageId'", false, "select");
	    if ($query['is_take_over_profile'] == 'Y')
		$queryEntourage = "SELECT COUNT(*) FROM members WHERE mem_id='$entourageId'"; // and verified='n' and ban = 'n' AND is_take_over_profile='Y'
	    else
		$queryEntourage = "SELECT COUNT(*) FROM members WHERE mem_id='$entourageId'"; // and email != '' and verified='y' and ban = 'n'
	    $resultEntourage = execute_query($queryEntourage, false);
	    $error['successful'] = isset($resultEntourage['COUNT(*)']) && ($resultEntourage['COUNT(*)']) ? true : false;

	    if (DEBUG)
		writelog("Profile:profile_valid", $error, true);
	    return $error;
	}else {
	    if (DEBUG)
		writelog("Profile:profile_valid", $error, true);

	    return $error;
	}
    }

    /* Function:profile_showall_comments($xmlrequest, $pagenumber, $limit)
      Description: to fetch All Parent comments related to user or his/her Entourage.
     * Parameters: $xmlrequest=>request by user,
     *             $pagenumber=>Pagination of Data,
     *             $limit=>Upper bound of data.
      Return: Array of Data having profile comments.
     *  */

    function profile_showall_comments($xmlrequest, $pagenumber, $limit) {
	$userId = mysql_real_escape_string($xmlrequest['ProfileParentComment']['userId']);
	$entourageId = mysql_real_escape_string($xmlrequest['ProfileParentComment']['entourageId']);
	$latest = mysql_real_escape_string($xmlrequest['ProfileParentComment']['latest']);

	if (DEBUG)
	    writelog("profile.class.php :: profile_showall_comments() : ", "Start Here ", false);

	$profile_hotpress = array();
	$lowerlimit = isset($pagenumber) ? (($pagenumber - 1) * $limit) : 0;
	if (($latest) && (isset($xmlrequest['ProfileParentComment']['postId'])) && ($xmlrequest['ProfileParentComment']['postId'] > 0)) {
	    if (DEBUG)
		writelog("profile.class.php :: profile_showall_comments() :: latestBulletin variable set to : ", $latest, false);
//$postId=if we want to fetch all comment after specific Id.Then $postId E I else $postId=0
	    //$latest=for latest comment on Profile set=1.
	    $postId = mysql_real_escape_string($xmlrequest['ProfileParentComment']['postId']);
	    //$query_profile_hotpress = "SELECT bullet_id,link_url,youtubeLink,link_image,image_link,added, tst_id, testimonial,from_id,post_via,photo_album_id FROM testimonials WHERE (tst_id>'$postId')AND(parent_tst_id =0||parent_tst_id =NULL)AND mem_id ='$entourageId' ORDER BY added DESC, tst_id DESC LIMIT $lowerlimit, $limit ";
	   $query_profile_hotpress = "SELECT SQL_CALC_FOUND_ROWS DISTINCT photo_album.photo_id,members.is_facebook_user,members.privacy,members.mem_id, members.profilenam, members.photo_thumb, members.photo_b_thumb,members.gender,members.profile_type,testimonials.bullet_id,testimonials.link_url,testimonials.youtubeLink,testimonials.link_image,testimonials.image_link,testimonials.added, testimonials.tst_id,testimonials. testimonial,testimonials.from_id,testimonials.post_via,testimonials.photo_album_id FROM testimonials LEFT JOIN members ON (members.mem_id=testimonials.from_id) LEFT JOIN photo_album ON (photo_album.photo_id=testimonials.photo_album_id) WHERE (testimonials.parent_tst_id =0 OR testimonials.parent_tst_id IS NULL)AND testimonials.mem_id ='$entourageId' ORDER BY testimonials.added DESC, testimonials.tst_id DESC LIMIT $lowerlimit, $limit ";//(tst_id>'$postId')AND
	    if (DEBUG)
		writelog("profile.class.php :: profile_showall_comments() :: latestBulletin variable set to : ", $query_profile_hotpress, false);
	}
	if (!$latest) {
	    //$query_profile_hotpress = "SELECT bullet_id,link_url,youtubeLink,link_image,image_link,added, tst_id, testimonial, from_id,post_via,photo_album_id FROM testimonials WHERE (parent_tst_id =0||parent_tst_id =NULL)AND  mem_id ='$entourageId' ORDER BY added DESC, tst_id DESC LIMIT $lowerlimit, $limit ";
	    $query_profile_hotpress = "SELECT SQL_CALC_FOUND_ROWS DISTINCT photo_album.photo_id,photo_album.album_id,members.is_facebook_user,members.privacy,members.mem_id, members.profilenam, members.photo_thumb, members.photo_b_thumb,members.gender,members.profile_type,testimonials.bullet_id,testimonials.link_url,testimonials.youtubeLink,testimonials.link_image,testimonials.image_link,testimonials.added, testimonials.tst_id,testimonials. testimonial,testimonials.from_id,testimonials.post_via,testimonials.photo_album_id FROM testimonials LEFT JOIN members ON (members.mem_id=testimonials.from_id) LEFT JOIN photo_album ON (photo_album.photo_id=testimonials.photo_album_id) WHERE (testimonials.parent_tst_id =0||testimonials.parent_tst_id =NULL)AND testimonials.mem_id ='$entourageId' ORDER BY testimonials.added DESC, testimonials.tst_id DESC LIMIT $lowerlimit, $limit ";
	    if (DEBUG)
		writelog("profile.class.php :: profile_showall_comments() :: latestBulletin variable set to : ", $query_profile_hotpress, false);
	}

	$result_profile_hotpress = execute_query_new($query_profile_hotpress);
	$count=0;
	$str="";
	$totalProfileComments = execute_query("SELECT FOUND_ROWS() as TotalRecords ;", false);
	$data['Total'] = $totalProfileComments['TotalRecords'];
	if ((mysql_num_rows($result_profile_hotpress) > 0)) {
	    while ($row = mysql_fetch_array($result_profile_hotpress, MYSQL_ASSOC)) {
		$id = isset($row['tst_id']) && ($row['tst_id']) ? $row['tst_id'] : NULL;
	    $row['tot_comment'] = $this->get_total_comment_count($id);
		$width_link_image = NULL;
		$height_link_image = NULL;
		//to get thumbnail images.
		if (is_readable($this->local_folder . $row['link_image'])) {
				$sizee = getimagesize($this->local_folder . $row['link_image']);
				$width_link_image = $sizee[0];
				$height_link_image = $sizee[1];
				$file_extension = substr($row['link_image'], strrpos($row['link_image'], '.') + 1);
				$arr = explode('.', $row['link_image']);
				$Id = isset($row['photo_album_id']) && ($row['photo_album_id']) ? $row['photo_album_id'] : NULL;
				if (!$Id)
				$Id = isset($row['bullet_id']) && $row['bullet_id'] ? $row['bullet_id'] : NULL;
	
				if ((!file_exists($this->local_folder . $arr[0] . "_" . $Id . "." . $file_extension)) && ($Id) && (preg_match('/^image\/(jp[e]?g|png|gif)$/', $sizee['mime']))) {
				thumbanail_for_image($Id,$row['link_image']);
				}
		    	if (preg_match('/^image\/(jp[e]?g|png|gif)$/', $sizee['mime'])) {
				$row['link_image'] = isset($row['link_image']) && (strlen($row['link_image']) > 7) ? event_image_detail($Id, $row['link_image'], 1) : NULL;
				list($width_link_image, $height_link_image) = (isset($row['link_image']) && (strlen($row['link_image']) > 7)) ? getimagesize($this->local_folder . $row['link_image']) : NULL;
		    	}
			}
			$width_image_link = NULL;
			$height_image_link = NULL;
			if (is_readable($this->local_folder . $row['image_link'])) {
					
					$sizee = getimagesize($this->local_folder . $row['image_link']);
					$width_image_link = $sizee[0];
					$height_image_link = $sizee[1];
					$file_extension = substr($row['image_link'], strrpos($row['image_link'], '.') + 1);
					$arr = explode('.', $row['image_link']);
					$Id = isset($row['photo_album_id']) && ($row['photo_album_id']) ? $row['photo_album_id'] : NULL;
					if (!$Id)
					$Id = isset($row['bullet_id']) && $row['bullet_id'] ? $row['bullet_id'] : NULL;
					if (!file_exists($this->local_folder . $arr[0] . "_" . $Id . "." . $file_extension) && (preg_match('/^image\/(jp[e]?g|png|gif)$/', $sizee['mime']))) {
					thumbanail_for_image($Id, $row['image_link']);
					}
					if (preg_match('/^image\/(jp[e]?g|png|gif)$/', $sizee['mime'])) {
					$row['image_link'] = isset($row['image_link']) && (strlen($row['image_link']) > 7) ? event_image_detail($Id, $row['image_link'], 1) : NULL;
					list($width_image_link, $height_image_link) = (isset($row['image_link']) && (strlen($row['image_link']) > 7)) ? getimagesize($this->local_folder . $row['image_link']) : NULL;
					}
				
			}
			$check = isset($row['mem_id']) && ($row['mem_id']) ? $row['mem_id'] : "";
		$row['profilenam'] = isset($row['profilenam']) && ($row['profilenam']) ? $row['profilenam'] : "";
		$row['gender'] = isset($row['gender']) && ($row['gender']) ? $row['gender'] : "";
		$row['profile_type'] = isset($row['profile_type']) && ($row['profile_type']) ? $row['profile_type'] : "";
		
		if ($check) {
			$row['photo_id'] = isset($row['photo_id']) ? $row['photo_id'] : "";
		    $row['photo_thumb'] = (isset($row['photo_thumb']) && (strlen($row['photo_thumb']) > 7)) ? $this->profile_url . $row['photo_thumb'] : $this->profile_url . default_images1($row['gender'], $row['profile_type']);
			
			 $row['photo_thumb'] = ((isset($row['is_facebook_user'])) && (strlen($row['photo_thumb']) > 7) && ($row['is_facebook_user'] == 'y' || $row['is_facebook_user'] == 'Y')) ? ((strstr($row['photo_thumb'],"photos")!=FALSE && strstr($row['photo_thumb'],"http")==FALSE) ? $this->profile_url.$row['photo_thumb'] : $row['photo_thumb']) : ((isset($row['photo_thumb']) && (strlen($row['photo_thumb']) > 7)) ? $this->profile_url . $row['photo_thumb'] : $this->profile_url . default_images($row['gender'], $row['profile_type']));

		   
		   $row['photo_b_thumb'] = ((isset($row['is_facebook_user'])) && (strlen($row['photo_b_thumb']) > 7) && ($row['is_facebook_user'] == 'y' || $row['is_facebook_user'] == 'Y')) ? ((strstr($row['photo_b_thumb'],"photos")!=FALSE && strstr($row['photo_b_thumb'],"http")==FALSE) ? $this->profile_url.$row['photo_b_thumb'] : $row['photo_b_thumb']) : ((isset($row['photo_b_thumb']) && (strlen($row['photo_b_thumb']) > 7)) ? $this->profile_url . $row['photo_b_thumb'] : $this->profile_url . default_images($row['gender'], $row['profile_type']));

		    $row['link_image'] = isset($row['link_image']) && (strlen($row['link_image']) > 7) ? $this->profile_url . $row['link_image'] : NULL;
		    $row['youtubeLink'] = isset($row['youtubeLink']) ? $row['youtubeLink'] : NULL;
		    $row['link_url'] = isset($row['link_url']) ? $row['link_url'] : NULL;
		    $row['image_link'] = isset($row['image_link']) && (strlen($row['image_link']) > 7) ? $this->profile_url . $row['image_link'] : NULL;
		    $input = $row['testimonial'];
		    $input = str_replace('\\', '', $input);
		    //to get the type data in the Post like url || text
		    if (preg_match(REGEX_URL, $input, $url)) {
			$postType = extract_url($input);
			$postType = strip_tags($postType);
			$postType = str_replace(array("\r\n", "\r", "\n", "<br />", "\""), "\\n", $postType);
		    } else {
			$postType = 'text';
		    }
		    

		    /* Added below line on 25 Nov 2011 :: aarya */
		    $row['testimonial'] = get_organized_comment_data($row['testimonial'], NULL);

		    $row['photo_album_id'] = isset($row['photo_album_id']) && ($row['photo_album_id']) ? $row['photo_album_id'] : NULL;

		    $postVia = ((isset($row['post_via'])) && ($row['post_via'])) ? "iPhone" : "";
		    $date = time_difference($row['added']); 
			
		    $str_temp = '{
                                     "postId":"' . str_replace('"', '\"', trim($row['tst_id'])) . '",
                                     "authorID":"' . str_replace('"', '\"', trim($row['mem_id'])) . '",
                                     "authorProfileImgURL":"' . str_replace('"', '\"', trim($row['photo_b_thumb'])) . '",
                                     "authorName":"' . str_replace('"', '\"', trim(preg_replace('/\s+/', ' ', $row['profilenam']))) . '",
                                     "gender":"' . str_replace('"', '\"', trim($row['gender'])) . '",
                                     "profileType":"' . str_replace('"', '\"', trim($row['profile_type'])) . '",
                                     "postText":"' . trim(preg_replace('/\s+/', ' ', str_replace("\'", "'", htmlspecialchars_decode(str_replace('"', '\"', $row['testimonial']), ENT_QUOTES)))) . '",
                                     "postType":"' . str_replace('"', '\"', trim($postType)) . '",
                                     "photoId":"' . str_replace('"', '\"', trim($row['photo_id'])) . '",
                                     "uploadedImage":"' . str_replace('"', '\"', trim($row['image_link'])) . '",
                                     "width_image_link":"' . str_replace('"', '\"', trim($width_image_link)) . '",
                                     "height_image_link":"' . str_replace('"', '\"', trim($height_image_link)) . '",
                                     "link_url":"' . str_replace('"', '\"', trim($row['link_url'])) . '",
                                     "youtubeLink":"' . str_replace('"', '\"', trim($row['youtubeLink'])) . '",
                                     "link_image":"' . str_replace('"', '\"', trim($row['link_image'])) . '",
                                     "width_link_image":"' . str_replace('"', '\"', trim($width_link_image)) . '",
                                     "height_link_image":"' . str_replace('"', '\"', trim($height_link_image)) . '",
                                     "postTimestamp":"' . str_replace('"', '\"', trim($date)) . '",
                                     "postVia":"' . str_replace('"', '\"', trim($postVia)) . '",
                                     "albumId":"' . str_replace('"', '\"', trim($row['album_id'])) . '",
                                     "commentsCount":"' . str_replace('"', '\"', trim($row['tot_comment'])) . '"
                                 }';
		    $postcount++;
		    $str = $str . $str_temp;
		    $str = $str . ',';
		
		}	
		$count++;
	 	}
	 }
	$count = isset($count) && ($count) ? $count : 0;
	$data['count']=$count;
	$data['str']=$str;
	
	/*for ($i = 0; $i < $count; $i++) {
	    $id = isset($result_profile_hotpress[$i]['tst_id']) && ($result_profile_hotpress[$i]['tst_id']) ? $result_profile_hotpress[$i]['tst_id'] : NULL;
	    $result_profile_hotpress[$i]['tot_comment'] = $this->get_total_comment_count($id);
	}*/

	return $data;
    }

    /* Function:get_total_comment_count($hotpressid)
      Description: to count total sub comment on Parent comments.
     * Parameters: $profileid=>parent comment id
      Return: integer.
     */

    function get_total_comment_count($profileid) {
	if (DEBUG)
	    writelog("profile.class.php :: get_total_comment_count() : ", "Start Here ", false);
	$query_comment_count = "SELECT COUNT(*) FROM testimonials WHERE parent_tst_id='$profileid'";
	$result_comment_count = execute_query($query_comment_count, false, "select");

	$result_comment_count['COUNT(*)'] = isset($result_comment_count['COUNT(*)']) ? $result_comment_count['COUNT(*)'] : NULL;
	if (DEBUG) {
	    writelog("profile.class.php :: get_total_comment_count() : ", $query_comment_count, false);
	    writelog("profile.class.php :: get_total_comment_count() : ", "End Here ", false);
	}
	return $result_comment_count['COUNT(*)'];
    }

    /* Function:profile_showall_comments_valid($xmlrequest)
      Description: to validate login user.
     * Parameters: $xmlrequest=>request by user
      Return: boolean Array.
     *  */

    function profile_showall_comments_valid($xmlrequest) {
	if (DEBUG)
	    writelog("profile.class.php :: profile_showall_comments_valid() : ", "Start Here ", false);
	$userId = mysql_real_escape_string($xmlrequest['ProfileParentComment']['userId']);

	$error = array();
	$query = "SELECT COUNT(*) from members WHERE mem_id='$userId'";
	$result = execute_query($query, false);
	$error['successful'] = isset($result['COUNT(*)']) && ($result['COUNT(*)']) ? true : false;
	if (DEBUG)
	    writelog("profile.class.php :: profile_showall_comments_valid() : ", $query, false);
	if (DEBUG)
	    writelog("profile.class.php :: profile_showall_comments_valid() : ", "End Here ", false);

	return $error;
    }

    /* Function:profile_sub_comments($xmlrequest, $pagenumber, $limit)
      Description:to show all subcomment which has been posted on Parent comment.
     * Parameters: $xmlrequest=>request by user
     *             $pagenumber=>use for pagination,
     *             $limit=>upper bound for page number.
      Return: data Array of subcomments posted on Specific Parent comment.
     *  */

    function profile_sub_comments($xmlrequest, $pagenumber, $limit) {
	$userId = mysql_real_escape_string($xmlrequest['ProfileSubComments']['userId']);
	$testimonialId = mysql_real_escape_string($xmlrequest['ProfileSubComments']['testimonialId']);

	$lowerlimit = ($pagenumber) ? (($pagenumber - 1) * $limit) : 0;
	if (DEBUG)
	    writelog("profile.class.php :: profile_sub_comments() : ", "Start Here ", false);
	$profile_hotpress = array();
	$lowerlimit = ($pagenumber) ? (($pagenumber - 1) * $limit) : 0;
	$count=0;
	$str="";
	//$query_profile_hotpress = "SELECT added, tst_id, testimonial, from_id,post_via FROM testimonials WHERE parent_tst_id ='$testimonialId' ORDER BY added LIMIT $lowerlimit, $limit "; //, tst_id ASC
	$query_profile_hotpress = "SELECT SQL_CALC_FOUND_ROWS members.is_facebook_user,members.privacy,members.mem_id, members.profilenam, members.photo_thumb, members.photo_b_thumb,members.gender,members.profile_type,testimonials.added, testimonials.tst_id, testimonials.testimonial, testimonials.from_id,testimonials.post_via FROM testimonials LEFT JOIN members ON( members.mem_id = testimonials.from_id ) WHERE testimonials.parent_tst_id ='$testimonialId' ORDER BY testimonials.added LIMIT $lowerlimit, $limit ";
	if (DEBUG)
	    writelog("profile.class.php :: profile_sub_comments() : ", $query_profile_hotpress, false);
	//$result_profile_hotpress = execute_query($query_profile_hotpress, true);
	$result = execute_query_new($query_profile_hotpress);
	$total_records = execute_query("SELECT FOUND_ROWS() as TotalRecords ;", true, "select");
	$result_profile_hotpress['totalrecords'] = (isset($total_records[0]['TotalRecords'])) ? $total_records[0]['TotalRecords'] : 0;
	if ((mysql_num_rows($result) > 0)) {
	    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		
		$row['photo_b_thumb'] = ((isset($row['is_facebook_user'])) && (strlen($row['photo_b_thumb']) > 7) && ($row['is_facebook_user'] == 'y' || $row['is_facebook_user'] == 'Y')) ? ((strstr($row['photo_b_thumb'], "photos") != FALSE && strstr($row['photo_b_thumb'], "http") == FALSE) ? $this->profile_url . $row['photo_b_thumb'] : $row['photo_b_thumb']) : ((isset($row['photo_b_thumb']) && (strlen($row['photo_b_thumb']) > 7)) ? $this->profile_url . $row['photo_b_thumb'] : $this->profile_url . default_images($row['gender'], $row['profile_type']));

		$input = $row['testimonial'];
		$input = str_replace('\\', '', $input);
		if (preg_match(REGEX_URL, $input, $url)) {
		    $postType = extract_url($input);
		    $postType = strip_tags($postType);
		    $postType = str_replace(array("\r\n", "\r", "\n", "<br />", "\""), "\\n", $postType);
		} else {
		    $postType = 'text';
		}
		$row['testimonial'] = str_replace('\\', "", $row['testimonial']);

		$row['testimonial'] = str_replace(array("\r\n", "\r", "\n", "<br />", "<br/>"), "\\n", $row['testimonial']);
		$row['testimonial'] = strip_tags($row['testimonial']);
		$row['testimonial'] = str_replace(array("\""), "", $row['testimonial']);
		$postVia = ((isset($row['post_via'])) && ($row['post_via'])) ? "iPhone" : "";
		$date = time_difference($row['added']); //date("d/m/y : H:i:s", $hotpress[$i]['date'])
		$str_temp = '{
                                     "postId":"' . str_replace('"', '\"', $row['tst_id']) . '",
                                     "authorID":"' . str_replace('"', '\"', $row['mem_id']) . '",
                                     "authorProfileImgURL":"' . str_replace('"', '\"', $row['photo_b_thumb']) . '",
                                     "authorName":"' . str_replace('"', '\"', preg_replace('/\s+/', ' ', $row['profilenam'])) . '",
                                     "gender":"' . str_replace('"', '\"', $row['gender']) . '",
                                     "profileType":"' . str_replace('"', '\"', $row['profile_type']) . '",
                                     "postText":"' . trim(preg_replace('/\s+/', ' ', str_replace("\'", "'", htmlspecialchars_decode(str_replace('"', '\"', $row['testimonial']), ENT_QUOTES)))) . '",
                                     "postType":"' . str_replace('"', '\"', $postType) . '",
                                     "postTimestamp":"' . str_replace('"', '\"', $date) . '",
                                     "postVia":"' . str_replace('"', '\"', $postVia) . '"
                                     
                                 }';
						
		//$postcount++;    "commentsCount":"' . $count . '"  for child comments
		$str = $str . $str_temp;
		$str = $str . ',';
	    
			$count++;
		}
	}
	
	$result_profile_hotpress['str']=$str;
	$result_profile_hotpress['count']=$count;
	
	//$count = isset($result_profile_hotpress['count']) && ($result_profile_hotpress['count']) ? $result_profile_hotpress['count'] : 0;
	/*for ($i = 0; $i < $count; $i++) {
	    $from_id = $result_profile_hotpress[$i]['from_id'];
	}*/
	if (DEBUG) {
	    writelog("profile.class.php :: profile_sub_comments() : ", $result_profile_hotpress, true);
	    writelog("profile.class.php :: profile_sub_comments() : ", "End Here ", false);
	}
	return $result_profile_hotpress;
    }

    /* Function:profile_sub_comments_valid($xmlrequest)
      Description:to validate Parent comment.
     * Parameters: $xmlrequest=>request by user
      Return: boolean Array.
     *  */

    function profile_sub_comments_valid($xmlrequest) {
	$testimonialId = mysql_real_escape_string($xmlrequest['ProfileSubComments']['testimonialId']);
	if (DEBUG)
	    writelog("profile.class.php :: profile_sub_comments_valid() : ", "Start Here ", false);
	$error = array();
	$query = "SELECT COUNT(*) from testimonials WHERE tst_id='$testimonialId'";
	$result = execute_query($query, false);
	$error['successful'] = isset($result['COUNT(*)']) && ($result['COUNT(*)']) ? true : false;
	if (DEBUG) {
	    writelog("profile.class.php :: profile_sub_comments_valid() : ", $error, true);
	    writelog("profile.class.php :: profile_sub_comments_valid() : ", "End Here ", false);
	}
	return $error;
    }

    /* Function:post_comment_on_profile($xmlrequest)
      Description:to post comment on a profile(Entourage || user)
     * Parameters: $xmlrequest=>request sent by user
      Return: boolean Array=>to check whether comment has been posted or not.
     *  */

    function post_comment_on_profile($xmlrequest) {
	if (DEBUG)
	    writelog("profile.class.php :: post_comment_on_profile() : ", "Start Here", false);

	$userId = mysql_real_escape_string($xmlrequest['PostCommentOnProfile']['userId']);
	$profileId = mysql_real_escape_string($xmlrequest['PostCommentOnProfile']['profileId']);
	$postId = mysql_real_escape_string($xmlrequest['PostCommentOnProfile']['postId']);
	$publishashotpress = mysql_real_escape_string($xmlrequest['PostCommentOnProfile']['displayAsHotPress']);
	$commentText = mysql_real_escape_string($xmlrequest['PostCommentOnProfile']['commentText']);
	$time = mysql_real_escape_string($xmlrequest['PostCommentOnProfile']['time']);
	//$date = time();
	$error = array();

	$userTimezone = new DateTimeZone('America/Chicago');
	$myDateTime = new DateTime("$time");
	$offset = $userTimezone->getOffset($myDateTime);
	$date = $myDateTime->format('U') + $offset;

	if ((isset($xmlrequest['PostCommentOnProfile']['postId'])) && ($xmlrequest['PostCommentOnProfile']['postId'])) {
	    $query_public = "SELECT publishashotpress,mem_id,bullet_id FROM testimonials WHERE tst_id='$postId'";
	    $result_public = execute_query($query_public, true, "select");
	    $publishashotpress = ((isset($result_public[0]['bullet_id'])) && ($result_public[0]['bullet_id'])) ? 1 : 0;
	}
	if (isset($postId) && ($postId != 0) && (isset($publishashotpress)) && ($publishashotpress == 1)) {
	    //profile comment reply display as hotpress
	    $query_comment = "INSERT INTO testimonials(mem_id, from_id, testimonial, stat, added, parent_tst_id, publishashotpress, photo_album_id, bullet_id,post_via,msg_alert)VALUE('" . $result_public[0]['mem_id'] . "', '$userId', '$commentText', 'a', '$date', '$postId', '', '0', '0',1,'Y')";
	} elseif (isset($postId) && ($postId != 0) && ($publishashotpress == 0)) {
	    //profile comment reply do not display as hotpress
	    $query_comment = "INSERT INTO testimonials(mem_id, from_id, testimonial, stat, added, parent_tst_id, publishashotpress, photo_album_id, bullet_id,post_via,msg_alert)VALUE('" . $result_public[0]['mem_id'] . "', '$userId', '$commentText', 'a', '$date', '$postId', '', '0', '0',1,'Y')";
	} else {
	    //profile parent comment
	    $query_comment = "INSERT INTO testimonials(mem_id, from_id, testimonial, stat, added, parent_tst_id, publishashotpress, photo_album_id, bullet_id,post_via,msg_alert)VALUE('$profileId ', '$userId', '$commentText', 'a', '$date', '0', '0', '0', '0',1,'Y')";
	}
	if (DEBUG)
	    writelog("profile.class.php :: post_comment_on_profile() : ", $query_comment, false);
	$result = execute_query($query_comment, false, "insert");
	$testo_id = isset($result['last_id']) && ($result['last_id']) ? $result['last_id'] : NULL;
	//send email
	$get_user_email_id = execute_query("select profilenam,email,photo_thumb from members where mem_id='$userId'", false, "select");
	$get_profile_user_email_id = execute_query("select profilenam,email from members where mem_id='$profileId'", false, "select");


//getOnline status

	$get_online_status = execute_query("select id FROM user_push_notification Where mem_id='$profileId' AND showonline='y'", true, "select");
	if ((!empty($get_online_status) && ($userId != $profileId) && ($postId == 0)) || (!empty($get_online_status) && ($userId != $profileId) && ($postId != 0))) {
	    //push_notification('post_comment_on_profile', $profileId, $userId);
	    $commentText1 = getname($userId) . ' added a comment to your profile:<br>Please login to view and response.<br><span style="color:#666666">"' . $commentText . '"</span>' . '' . "<a href='http://www.socialnightlife.com/index.php?pg=profile&usr=$profileId&action=delmsg&frmid=$userId&cmmentid=$testo_id' target='_blank'>Login</a>";
	    $matter = email_template($get_user_email_id['profilenam'], 'You have a new profile comment on SocialNightlife.', $commentText1, $userId, $get_user_email_id['photo_thumb']);
	    firemail($get_profile_user_email_id['email'], 'From: socialNightLife <socialnightlife.com>\r\n', 'You have a new profile comment on SocialNightlife.', $matter);

//$mail = mail($get_profile_user_email_id['email'], 'You have a new profile comment on MySNL.', "$commentText", $headers);
	}
//push notification
	if (($postId > 0) && ($userId == $profileId )) {
	    $get_parent_comment = execute_query("SELECT from_id FROM testimonials WHERE tst_id='$postId'", false, "select");
	    if ($userId != $get_parent_comment['from_id']) {
		//push_notification('post_comment_on_profile', $get_parent_comment['from_id'], $userId);
		$commentText1 = getname($userId) . ' added a comment to your profile:<br>Please login to view and response.<br><span style="color:#666666">"' . $commentText . '"</span>' . '' . "<a href='http://www.socialnightlife.com/index.php?pg=profile&usr=$profileId&action=delmsg&frmid=$userId&cmmentid=$testo_id' target='_blank'>Login</a>";
		$matter = email_template($get_user_email_id['profilenam'], 'You have a new profile comment on SocialNightlife.', $commentText1, $userId, $get_user_email_id['photo_thumb']);
		firemail($get_profile_user_email_id['email'], 'From: socialNightLife <socialnightlife.com>\r\n', 'You have a new profile comment on SocialNightlife.', $matter);
		//$mail = mail($get_profile_user_email_id['email'], 'You have a new profile comment on MySNL.', "$commentText", $headers);
	    }
	}
	$affected_row_testimonial = isset($result['count']) && ($result['count']) ? $result['count'] : NULL;
	$error = error_CRUD($xmlrequest, $affected_row_testimonial);
	if ((isset($error['PostCommentOnProfile']['successful_fin'])) && (!$error['PostCommentOnProfile']['successful_fin'])) {
	    return $error;
	}
	//Hotpress=>If user wants to share comment on Hotpress.
	if ((isset($publishashotpress)) && ($publishashotpress) && ($postId > 0)) {
	    $query_parentid = "SELECT id FROM bulletin WHERE testo_id='$postId'";
	    if (DEBUG)
		writelog("profile.class.php :: post_comment_on_profile() : ", $query_parentid, false);

	    $result = execute_query($query_parentid, true, "select");
	    $parentid = isset($result[0]['id']) && ($result[0]['id']) ? $result[0]['id'] : 0;
	    $privacy = user_privacy_settings($userId);
	    $visible = isset($privacy) && ($privacy == 'private') ? 'allfriends' : NULL; //allfriends';
	    $query_hotpress = "INSERT INTO bulletin(mem_id, parentid, from_id, date, body, testo_id, visible_to,post_via,msg_alert)VALUE('$userId', '$parentid', '$profileId', '$date', '$commentText', '0', '$visible',1,'N')";
	    if (DEBUG)
		writelog("profile.class.php :: post_comment_on_profile() : ", $query_hotpress, false);
	    $result_hotpress = execute_query($query_hotpress, false, "insert");
	    $affected_row_hotpress = isset($result_hotpress['count']) && ($result_hotpress['count']) ? $result_hotpress['count'] : NULL;
	    $bullet_id = isset($result_hotpress['last_id']) && ($result_hotpress['last_id']) ? $result_hotpress['last_id'] : NULL;
	    $error = error_CRUD($xmlrequest, $affected_row_hotpress);
	    if ((isset($error['PostCommentOnProfile']['successful_fin'])) && (!$error['PostCommentOnProfile']['successful_fin'])) {
		return $error;
	    }
	}
	if ((isset($publishashotpress)) && ($publishashotpress) && (!$postId)) {
	    $query_hotpress = "INSERT INTO bulletin(mem_id, parentid, from_id, date, body, testo_id, visible_to,post_via,msg_alert)VALUE('$userId', 0, '$profileId', '$date', '$commentText', '$testo_id', '',1,'Y')";
	    if (DEBUG)
		writelog("profile.class.php :: post_comment_on_profile() : ", $query_hotpress, false);
	    $result_hotpress = execute_query($query_hotpress, false, "insert");
	    $affected_row_hotpress = isset($result_hotpress['count']) && ($result_hotpress['count']) ? $result_hotpress['count'] : NULL;
	    $error = error_CRUD($xmlrequest, $affected_row_hotpress);
	    $bullet_id = isset($result_hotpress['last_id']) && ($result_hotpress['last_id']) ? $result_hotpress['last_id'] : NULL;
	    if ((isset($error['PostCommentOnProfile']['successful_fin'])) && (!$error['PostCommentOnProfile']['successful_fin'])) {
		return $error;
	    }
	}
	if ((isset($publishashotpress)) && ($publishashotpress)) {
	    $query_testo_id = "UPDATE testimonials SET bullet_id='$bullet_id' WHERE tst_id='$testo_id'";
	    if (DEBUG)
		writelog("profile.class.php :: post_comment_on_profile() : ", $query_testo_id, false);
	    $result_profile_tst_id = execute_query($query_testo_id, false, "insert");
	    $affected_row_hotpress = isset($result_profile_tst_id['count']) && ($result_profile_tst_id['count']) ? $result_profile_tst_id['count'] : NULL;
	    $error = error_CRUD($xmlrequest, $affected_row_hotpress);
	    if ((isset($error['PostCommentOnProfile']['successful_fin'])) && (!$error['PostCommentOnProfile']['successful_fin'])) {
		return $error;
	    }
	}
	$error['id'] = isset($testo_id) && ($testo_id) ? $testo_id : NULL;
	if (DEBUG) {
	    writelog("Profile:post_comment_on_profile", $error, true);
	    writelog("Profile:post_comment_on_profile", "End Here", false);
	}
	return $error;
    }

    /* Function:post_comment_on_profile($xmlrequest)
      Description:validate user and comment.
     * Parameters: $xmlrequest=>request by user
      Return: boolean Array=>to check whether comment has been posted or not.
     *  */

    function post_comment_on_profile_valid($xmlrequest) {
	$userId = mysql_real_escape_string($xmlrequest['PostCommentOnProfile']['userId']);
	$profileId = mysql_real_escape_string($xmlrequest['PostCommentOnProfile']['profileId']);
	$postId = mysql_real_escape_string($xmlrequest['PostCommentOnProfile']['postId']);
	if (DEBUG)
	    writelog("Profile:post_comment_on_profile_valid", "Start Here", false);

	if (isset($xmlrequest['PostCommentOnProfile']['postId']) && ($xmlrequest['PostCommentOnProfile']['postId'])) {
	    $query_post = "SELECT COUNT(*) FROM testimonials WHERE tst_id='$postId'";
	    if (DEBUG)
		writelog("Profile:post_comment_on_profile_valid", $query_post, false);

	    $result_post = execute_query($query_post, false);
	}
	$error = array();
//        if ($userId != $profileId) {
//          $query = "SELECT COUNT(*) FROM network WHERE mem_id ='$userId' AND frd_id='$profileId' || mem_id ='$profileId' AND frd_id='$userId' ";
//            if (DEBUG)
//                writelog("Profile:post_comment_on_profile_valid", $query, false);
//        }

	if ($userId) {
	    $query = "SELECT COUNT(*) FROM members WHERE mem_id ='$userId'";
	    if (DEBUG)
		writelog("Profile:post_comment_on_profile_valid", $query, false);
	}

	$result = execute_query($query, false);
	$error['successful'] = isset($result['COUNT(*)']) && ($result['COUNT(*)']) ? true : false;

	if (DEBUG) {
	    writelog("Profile:post_comment_on_profile_valid", $error, true);
	    writelog("Profile:post_comment_on_profile_valid", "End Here", false);
	}
	return $error;
    }

    /* Function:delete_post_from_profile($xmlrequest)
      Description:delete comment from a profile(Entourage or user)
     * Parameters: $xmlrequest=>request sent by user
      Return: boolean Array=>to check whether comment has been deleted or not.
     *  */

    function delete_post_from_profile($xmlrequest) {
	if (DEBUG)
	    writelog("Profile:delete_post_from_profile()", "Start Here", false);

	$userId = mysql_real_escape_string($xmlrequest['DeleteProfileMessage']['userId']);
	$entourageId = mysql_real_escape_string($xmlrequest['DeleteProfileMessage']['entourageId']);
	$postId = mysql_real_escape_string($xmlrequest['DeleteProfileMessage']['postId']);
	$error = array();

	$query_hotpress = "SELECT bullet_id FROM testimonials WHERE tst_id='$postId'";
	if (DEBUG)
	    writelog("Profile:delete_post_from_profile()", $query_hotpress, false);
	$result_hotpress = execute_query($query_hotpress, false);
	$result_hotpress['bullet_id'] = isset($result_hotpress['bullet_id']) && ($result_hotpress['bullet_id']) ? $result_hotpress['bullet_id'] : 0;

	if (isset($xmlrequest['DeleteProfileMessage']['entourageId']) && ($xmlrequest['DeleteProfileMessage']['entourageId'])) {
	    $query = "DELETE FROM testimonials WHERE ((from_id='$entourageId'||mem_id='$entourageId') AND tst_id='$postId')||(parent_tst_id='$postId' AND parent_tst_id>0)";
	} else {
	    $query = "DELETE FROM testimonials WHERE ((from_id='$userId'||mem_id='$userId') AND tst_id='$postId')||(parent_tst_id='$postId' AND parent_tst_id>0)";
	}
	if (DEBUG)
	    writelog("Profile:delete_post_from_profile()", $query, false);

	$result = execute_query($query, false, "delete");
	$affected_row = isset($result['count']) && ($result['count']) ? $result['count'] : NULL;
	$error = error_CRUD($xmlrequest, $affected_row);
	if ((isset($error['DeleteProfileMessage']['successful_fin'])) && (!$error['DeleteProfileMessage']['successful_fin'])) {
	    return $error;
	}
//Hotpress=>If comment present on Hotpress.
	if (isset($result_hotpress['bullet_id']) && ($result_hotpress['bullet_id']) && ($affected_row)) {
	    $query_hotpress_del = "DELETE FROM bulletin WHERE (id='" . $result_hotpress['bullet_id'] . "')||(parentid='" . $result_hotpress['bullet_id'] . "' AND parentid>0)"; // AND mem_id='$userId'
	    if (DEBUG)
		writelog("Profile:delete_post_from_profile", $query_hotpress_del, false);
	    $result_hotpress_del = execute_query($query_hotpress_del, false, "delete");
	    $affected_row_hotpress = isset($result_hotpress_del['count']) && ($result_hotpress_del['count']) ? $result_hotpress_del['count'] : NULL;
	    $error = error_CRUD($xmlrequest, $affected_row_hotpress);
	    if ((isset($error['DeleteProfileMessage']['successful_fin'])) && (!$error['DeleteProfileMessage']['successful_fin'])) {
		return $error;
	    }
	    if (DEBUG) {
		writelog("Profile:delete_post_from_profile():", $error, true);
		writelog("Profile:delete_post_from_profile():", "End Here", false);
	    }
	}
	if (DEBUG) {
	    writelog("Profile:delete_post_from_profile():", $error, true);
	    writelog("Profile:delete_post_from_profile():", "End Here", false);
	}
	return $error;
    }

    /* Function:delete_post_from_profile_valid($xmlrequest)
      Description:to validate whether comment exist in profile in respect to corresponding(Entourage or user)
     * Parameters: $xmlrequest=>request by user
      Return: boolean Array
     *  */

    function delete_post_from_profile_valid($xmlrequest) {
	if (DEBUG)
	    writelog("Profile:delete_post_from_profile_valid():", "Start Here", false);

	$userId = mysql_real_escape_string($xmlrequest['DeleteProfileMessage']['userId']);
	$postId = mysql_real_escape_string($xmlrequest['DeleteProfileMessage']['postId']);
	$query = "SELECT COUNT(*) FROM testimonials WHERE (from_id='$userId'||mem_id='$userId') AND tst_id='$postId'";
	if (DEBUG)
	    writelog("Profile:delete_post_from_profile_valid():", $query, false);
	$row = execute_query($query, false);
	$error['successful'] = isset($row['COUNT(*)']) && ($row['COUNT(*)']) ? true : false;
	if (DEBUG) {
	    writelog("Profile:delete_post_from_profile_valid", $error, true);
	    writelog("Profile:delete_post_from_profile_valid():", "End Here", false);
	}
	return $error;
    }

    /* Function:profile_info_short_desc($xmlrequest)
      Description:to get the short information of(Entourage || user)
     * Parameters: $xmlrequest=>request sent by user
      Return:Array containing information related to user and Entourage.
     *  */

    function profile_info_short_desc($xmlrequest) {
	if (DEBUG)
	    writelog("Profile:profile_info_short_desc():", "Start Here", false);

	$userId = mysql_real_escape_string($xmlrequest['profileInfo']['userId']);
	$entourageId = mysql_real_escape_string($xmlrequest['profileInfo']['entourageId']);
	    //info relate to user.
	    $query = "SELECT members.is_facebook_user,members.privacy,members.profilenam,members.photo_b_thumb,members.profile_type,members.gender,members.photo_bb_thumb FROM members WHERE members.mem_id='$entourageId'";
	    if (DEBUG)
		writelog("Profile:profile_info_short_desc():", $query, false);
	$result_check=array();
	if($userId != $entourageId)
	{
		$query_check = "SELECT COUNT(*) FROM messages_system WHERE (mem_id='$entourageId' AND frm_id='$userId') AND type='friend'";
		$result_check = execute_query($query_check, false, "select");
		$query_add = "SELECT COUNT(*) FROM messages_system WHERE (mem_id='$userId' AND frm_id='$entourageId')AND type='friend'";
		$result_add = execute_query($query_add, false, "select");
	}
	$walls['friendRequestSent'] = isset($result_check['COUNT(*)']) && ($result_check['COUNT(*)']) ? true : false;
	$walls['addAsFriend'] = isset($result_add['COUNT(*)']) && ($result_add['COUNT(*)']) ? true : false;
	$walls['Profile_info'] = execute_query($query, false);
	$walls['Profile_info']['friend'] = is_friend($userId, $entourageId);
	$walls['is_badges_available'] = $this->getBadgesInfo($entourageId);

	if (DEBUG) {
	    writelog("Profile:profile_info", $walls, true);
	    writelog("Profile:profile_info():", "End Here", false);
	}

	return $walls;
    }

    /*  get flag if badges are available or NOT */

    private function getBadgesInfo($entourageId) {
	
	    $getEntourageInfo = execute_query("SELECT * FROM members mem where mem.mem_id='$entourageId' AND (mem.profile_type !='C' || mem.profile_type !='c')", true, "select");
//SELECT * FROM members mem INNER JOIN bottel_alert bta ON(mem.mem_id='$entourageId' AND mem.mem_id=bta.mem_id)
//	    WHERE (mem.profile_type !='C' || mem.profile_type !='c') GROUP BY bta.bottel_type ORDER BY bta.createdate DESC
	    if ($getEntourageInfo['count'] > 0) {
		return 'Y';
	    } else {
		return 'N';
	    }
    }

    /* Function:take_over_profile($xmlrequest)
      Description:save user information during appearance in an event if that user doesnot exist in Data base.
     * Parameters: $xmlrequest=>request by user
      Return:boolean Array
     *  */

    function take_over_profile($xmlrequest) {
	$profilenam = mysql_real_escape_string($xmlrequest['TakeOverProfile']['profileName']);
	$zip = mysql_real_escape_string($xmlrequest['TakeOverProfile']['zip']);
	$saddress = mysql_real_escape_string($xmlrequest['TakeOverProfile']['address']);
	$country = isset($xmlrequest['TakeOverProfile']['country']) ? mysql_real_escape_string($xmlrequest['TakeOverProfile']['country']) : 'United States';
	$photo = 'no'; //mysql_real_escape_string($xmlrequest['TakeOverProfile']['photo']);
	$state = mysql_real_escape_string($xmlrequest['TakeOverProfile']['state']);
	$city = mysql_real_escape_string($xmlrequest['TakeOverProfile']['city']);
	$longitude = mysql_real_escape_string($xmlrequest['TakeOverProfile']['longitude']);
	$latitude = mysql_real_escape_string($xmlrequest['TakeOverProfile']['latitude']);
	$interests = mysql_real_escape_string($xmlrequest['TakeOverProfile']['interests']);
	$gender = mysql_real_escape_string($xmlrequest['TakeOverProfile']['gender']);
	$xmlrequest['TakeOverProfile']['uniqueId'] = isset($xmlrequest['TakeOverProfile']['uniqueId']) && ($xmlrequest['TakeOverProfile']['uniqueId']) ? $xmlrequest['TakeOverProfile']['uniqueId'] : NULL;
	$uniqueId = mysql_real_escape_string($xmlrequest['TakeOverProfile']['uniqueId']);
	$birthday = mysql_real_escape_string($xmlrequest['TakeOverProfile']['birthday']);

	$birthday = strtotime($birthday);
	$error = array();
	$ratetime = 1;
	$showonline = 0;
	$showage = 0;
	$showgender = 0;
	$rapzone = "";
	$fname = "";
	$lname = "";
	$secanswer = "";
	$secquestion = "";
	$profile_url = "";
	$f_artist = 'n';
	$play = 'off';
	$pay_stat = "";
	$chk_rate = "0000-00-00 00:00:00";
	$mem_acc = 0;
	$ad_notes = "";
	$tribes = "";
	$ignore_list = '';
	$filter = "any||any";
	$joined = date("Y-m-d");
	$history = NULL;
	$views = NULL;
	$accountNo = NULL;
	$email = NULL;
	$password = NULL;
	$photo_thumb = 'no';
	$photo_b_thumb = 'no';
	$photo_bb_thumb = 'no';
	$verified = 'n';
	$online = 'off';
	$ban = 'n';
	$featured = 1;
	$visitcount = 0;

	$ad_notes = "";
	$mem_stat = "";

	$ethnicity = "";
	$interests;
	$profilebg = "";
	$profilebg_repeat = "";
	$audio = "";
	$video = "";
	$spam_list = "";
	$bgpic = "";
	$profile_type = "C";
	$feedbackScore = 0;
	$positiveScore = 0;
	$negativeScore = 0;
	$neutralScore = 0;
	$privacy = "public";
	$widget = "";
	$quote = "";
	$rating = "";
	$subscribe = '';
	$is_facebook_user = NULL;
	$takeOverActCode = NULL;
	$notifications = '';
	$showloc = 0;
	$currdate = time();
	$rateme = 1;
	$check_id = NULL;
	$check_id = $this->is_take_over_profile($profilenam);
	if ((!$check_id)) {
	    $query = "INSERT INTO members (accountNo, email, password, profile_url, secquestion, secanswer, fname, lname, profilenam, zip, country, showloc, rapzone, showgender, showage, showonline, rateme, notifications, gender, birthday, photo, photo_thumb, photo_b_thumb, photo_bb_thumb, verified, online, ban, featured, visitcount, current, views, history, joined, filter, ignore_list, tribes, ad_notes, mem_stat, mem_acc, pay_stat, chk_rate, play, f_artist, ethnicity, saddress, state, city, interests, profilebg, profilebg_repeat, audio, video, spam_list, bgpic, profile_type, feedbackScore, positiveScore, negativeScore, neutralScore, privacy, widget, quote, rating, subscribe, is_facebook_user, is_take_over_profile, takeOverActCode, is_take_overed, latitude, longitude) VALUES
            ('$uniqueId', '$email', '$password', '$profile_url', '$secquestion', '$secanswer', '$fname', '$lname', '$profilenam', '$zip', '$country', '$showloc', '$rapzone', '$showgender', '$showage', '$showonline', '$rateme', '$notifications', '$gender', '$birthday', '$photo', '$photo_thumb', '$photo_b_thumb', '$photo_bb_thumb', '$verified', '$online', '$ban', '$featured', '$visitcount','$currdate', '$views', '$history', '0000-00-00 00:00:00', '$filter', '$ignore_list', '$tribes', '$ad_notes', '$mem_stat', '$mem_acc', '$pay_stat', '0000-00-00 00:00:00', '$play', '$f_artist', '$ethnicity','$saddress', '$state', '$city', '$interests', '$profilebg', '$profilebg_repeat', '$audio', '$video', '$spam_list', '$bgpic', '$profile_type', '$feedbackScore', '$positiveScore', '$negativeScore', '$neutralScore', '$privacy', '$widget', '$quote', '$rating', '$subscribe', '$is_facebook_user', 'Y', '$takeOverActCode', 'N', '$latitude', '$longitude')";
	    //echo $query;
	    $result = execute_query($query, false, "insert");
	    $affected_row = isset($result['count']) ? $result['count'] : NULL;
	    $error['last_id'] = $result['last_id'];
	} else {
	    $error['last_id'] = $check_id;
	}
	return $error;
    }

    /* Function:is_take_over_profile($profilenam)
      Description:to check whether the user already exist or not in database.
     * Parameters: $xmlrequest=>request by user
      Return:integer Array
     *  */

    function is_take_over_profile($profilenam) {

	$profilenam = isset($profilenam) && ($profilenam) ? trim($profilenam) : NULL;
	$query = "SELECT mem_id FROM members WHERE profilenam LIKE '%$profilenam%'";
	$result = execute_query($query, false, "select");
	$result['mem_id'] = isset($result['mem_id']) && ($result['mem_id']) ? $result['mem_id'] : NULL;

	return $result['mem_id'];
    }

    /*     * ***********************Response Strings*********************************************** */
    /* Function:profileInfo($response_message, $xmlrequest)
      Description:to get user info
     * Parameters: $xmlrequest=>request by user
     *             $response_message=>validation message.
      Return:string
     *  */

    function profileInfo($response_message, $xmlrequest) {

	if (isset($response_message['Profile']['SuccessCode']) && ( $response_message['Profile']['SuccessCode'] == '000')) {
	    $profile_info = array();
	    $profile_info = $this->profile_info($xmlrequest);

	    $userinfocode = $response_message['Profile']['SuccessCode'];
	    $userinfodesc = $response_message['Profile']['SuccessDesc'];
	    $profile_info['country'] = (isset($profile_info['country']) ? $profile_info['country'] : '');
	    $profile_info['state'] = (isset($profile_info['state']) ? $profile_info['state'] : '');
	    $profile_info['city'] = (isset($profile_info['city']) ? $profile_info['city'] : '');
	    $profile_info['profilenam'] = (isset($profile_info['profilenam']) ? $profile_info['profilenam'] : '');
	    $profile_info['profile_type'] = isset($profile_info['profile_type']) ? $profile_info['profile_type'] : '';
	    $profile_info['ethnicity'] = isset($profile_info['ethnicity']) && ($profile_info['ethnicity']) ? $profile_info['ethnicity'] : NULL;
	    $str = '';
	    if (($profile_info['profile_type'] == 'C') || ($profile_info['profile_type'] == 'c')) {
		$str = ' "Type":"' . str_replace('"', '\"', $profile_info['ethnicity']) . '",';
	    } else {
		$str = '"Ethnicity":"' . str_replace('"', '\"', $profile_info['ethnicity']) . '",';
	    }
		
//to get Lifestyle
	    $profile_info['about'] = isset($profile_info['about']) && ($profile_info['about']) ? $profile_info['about'] : (isset($profile_info['interest']) ? $profile_info['interest'] : '');
	    $profile_info['schools'] = (isset($profile_info['schools']) ? $profile_info['schools'] : '');

	    $url = (isset($profile_info['profile_url']) && $profile_info['profile_url'] != "" && !empty($profile_info['profile_url'])) ? (((isset($profile_info['is_facebook_user'])) && ($profile_info['is_facebook_user'] == 'y' || $profile_info['is_facebook_user'] == 'Y')) ? $profile_info['profile_url'] : $this->profile_url . $profile_info['profile_url']) : NULL;
/* if(!isset($url) || empty($url))
{
	    $url = (isset($profile_info['photo']) && $profile_info['photo'] != "" && !empty($profile_info['photo'])) ? (((isset($profile_info['is_facebook_user'])) && ($profile_info['is_facebook_user'] == 'y' || $profile_info['is_facebook_user'] == 'Y')) ? $profile_info['photo'] : $this->profile_url . $profile_info['photo']) : NULL;

} */
	    $alcohol = NULL;
	    //for Nightsites
	    if (($profile_info['profile_type'] == 'C') || ($profile_info['profile_type'] == 'c')) {
		$profile_info['smoker'] = '';
		$profile_info['drinker'] = '';
		$profile_info['marstat'] = '';
		$profile_info['religion'] = '';
		$profile_info['children'] = '';

		$profile_info['event_name'] = (isset($profile_info['event_name']) ? $profile_info['event_name'] : '');
		$profile_info['caddress'] = (isset($profile_info['caddress']) ? $profile_info['caddress'] : '');
		$profile_info['business_phone'] = (isset($profile_info['business_phone']) ? $profile_info['business_phone'] : '');
		$website = (isset($profile_info['cwebsite']) ? $profile_info['cwebsite'] : '');
		$profile_info['club_type'] = (isset($profile_info['club_type']) ? $profile_info['club_type'] : '');
		$profile_info['cdescription'] = (isset($profile_info['cdescription']) ? $profile_info['cdescription'] : '');
		$profile_info['cmusic'] = (isset($profile_info['cmusic']) ? $profile_info['cmusic'] : '');
		$profile_info['club_admission'] = (isset($profile_info['club_admission']) ? $profile_info['club_admission'] : '');
		$profile_info['house_of_operation'] = (isset($profile_info['house_of_operation']) ? $profile_info['house_of_operation'] : '');
		$profile_info['capacity'] = (isset($profile_info['capacity']) ? $profile_info['capacity'] : '');
		$profile_info['what_to_expect'] = (isset($profile_info['what_to_expect']) ? $profile_info['what_to_expect'] : '');
		$profile_info['services'] = (isset($profile_info['services']) ? $profile_info['services'] : '');
		$profile_info['alcohol'] = (isset($profile_info['alcohol']) ? $profile_info['alcohol'] : '');
		$profile_info['age_limit'] = (isset($profile_info['age_limit']) ? str_replace("#", "", $profile_info['age_limit']) : '');
		$Website = (isset($profile_info['specials']) ? $profile_info['specials'] : '');
		$profile_info['purchase_ticket'] = (isset($profile_info['purchase_ticket']) ? $profile_info['purchase_ticket'] : '');
		$profile_info['guidelines'] = (isset($profile_info['guidelines']) ? $profile_info['guidelines'] : '');
		$profile_info['widget'] = (isset($profile_info['widget']) ? $profile_info['widget'] : '');
		$profile_info['door_policy'] = (isset($profile_info['door_policy']) ? $profile_info['door_policy'] : '');
		$profile_info['parking'] = (isset($profile_info['parking']) ? $profile_info['parking'] : '');

		if ($profile_info['alcohol'] == 1) {
		    $alcohol = 'No';
		} elseif ($profile_info['alcohol'] == 2) {
		    $alcohol = 'Yes';
		} else {
		    $alcohol = NULL;
		}
//blank for nightsite

		$profile_info['here_for'] = '';
		$profile_info['interests'] = '';
		$profile_info['hometown'] = '';

		$profile_info['languages'] = '';
		$profile_info['website'] = '';
		$profile_info['books'] = '';
		$profile_info['music'] = '';
		$profile_info['movies'] = '';
		$profile_info['travel'] = '';
		$profile_info['clubs'] = '';
		$profile_info['travel'] = '';
		$profile_info['meet_people'] = '';
		$profile_info['position'] = '';
		$profile_info['company'] = '';
		$profile_info['occupation'] = '';
		$profile_info['industry'] = '';
		$profile_info['specialities'] = '';
		$profile_info['overview'] = '';
		$profile_info['style_card'] = '';
		$profile_info['college'] = '';
		$profile_info['highschool'] = '';
		$profile_info['job'] = '';

		$profile_info['assotiations'] = '';
		$profile_info['p_positions'] = '';
		$profile_info['p_companies'] = '';
		$profile_info['income'] = '';
		$profile_info['education'] = '';
		$profile_info['hobbies'] = '';
		$profile_info['whatatrip'] = '';
		$profile_info['topdestinations'] = '';
		$profile_info['education'] = '';
		$profile_info['topgateaways'] = '';
		$profile_info['hotclubs'] = '';
		$profile_info['besthotel'] = '';
		$profile_info['finerestaurants'] = '';
		$profile_info['whatnext'] = '';
		$profile_info['destinations'] = '';
		$profile_info['gataways'] = '';
		$profile_info['club'] = '';
		$profile_info['hotel'] = '';
		$profile_info['restaurants'] = '';
		$profile_info['skills'] = '';

		$profile_info['paypal_email'] = '';
		$profile_info['charges'] = '';
		$profile_info['instant_access'] = '';
		$profile_info['what_is'] = '';
		$profile_info['res_experience'] = '';
		$profile_info['fantasies'] = '';
		$profile_info['sex_experience'] = '';
		$profile_info['perfect_soul'] = '';
		$profile_info['crush'] = '';
		$profile_info['heart_breken'] = '';
		$profile_info['heart_been_breken'] = '';
		$profile_info['other_sex'] = '';
		$profile_info['favorite'] = '';
		$profile_info['turn_on'] = '';
		$profile_info['ego'] = '';
		$profile_info['music'] = '';
		$profile_info['activated'] = '';
		$profile_info['bg_image'] = '';
		$profile_info['theme'] = '';
	    } else {

		$profile_info['smoker'] = (isset($profile_info['smoker']) ? $profile_info['smoker'] : '');
		$profile_info['drinker'] = (isset($profile_info['drinker']) ? $profile_info['drinker'] : '');
		$profile_info['marstat'] = (isset($profile_info['marstat']) ? $profile_info['marstat'] : '');
		$profile_info['religion'] = (isset($profile_info['religion']) ? $profile_info['religion'] : '');
		$profile_info['children'] = (isset($profile_info['children']) ? $profile_info['children'] : '');

		$profile_info['event_name'] = '';
		$profile_info['caddress'] = '';
		$profile_info['business_phone'] = '';
		$website = '';
		$profile_info['club_type'] = '';
		$profile_info['cdescription'] = '';
		$profile_info['cmusic'] = '';
		$profile_info['club_admission'] = '';
		$profile_info['house_of_operation'] = '';
		$profile_info['capacity'] = '';
		$profile_info['what_to_expect'] = '';
		$profile_info['services'] = '';
		$profile_info['alcohol'] = '';
		$profile_info['age_limit'] = '';
		$Website = '';
		$profile_info['purchase_ticket'] = '';
		$profile_info['guidelines'] = '';
		$profile_info['widget'] = '';
		$profile_info['door_policy'] = '';
		$profile_info['parking'] = '';

//for Other Profiles.
		$profile_info['here_for'] = (isset($profile_info['here_for']) ? $profile_info['here_for'] : '');
		$profile_info['latitude'] = (isset($profile_info['latitude']) ? $profile_info['latitude'] : '');
		$profile_info['longitude'] = (isset($profile_info['longitude']) ? $profile_info['longitude'] : '');

		$profile_info['interests'] = isset($profile_info['interests']) ? $profile_info['interests'] : '';
		$profile_info['hometown'] = (isset($profile_info['hometown']) ? $profile_info['hometown'] : '');

		$profile_info['languages'] = (isset($profile_info['languages']) ? $profile_info['languages'] : '');
		$profile_info['website'] = (isset($profile_info['website']) ? $profile_info['website'] : '');
		$profile_info['books'] = (isset($profile_info['books']) ? $profile_info['books'] : '');
		$profile_info['music'] = (isset($profile_info['music']) ? $profile_info['music'] : '');
		$profile_info['movies'] = (isset($profile_info['movies']) ? $profile_info['movies'] : '');
		$profile_info['travel'] = (isset($profile_info['travel']) ? $profile_info['travel'] : '');
		$profile_info['clubs'] = (isset($profile_info['clubs']) ? $profile_info['clubs'] : '');
		$profile_info['travel'] = (isset($profile_info['travel']) ? $profile_info['travel'] : '');

		$profile_info['meet_people'] = (isset($profile_info['meet_people']) ? $profile_info['meet_people'] : '');
		$profile_info['position'] = (isset($profile_info['position']) ? $profile_info['position'] : '');
		$profile_info['company'] = (isset($profile_info['company']) ? $profile_info['company'] : '');
		$profile_info['occupation'] = (isset($profile_info['occupation']) ? $profile_info['occupation'] : '');
		$profile_info['industry'] = (isset($profile_info['industry']) ? $profile_info['industry'] : '');
		$profile_info['specialities'] = (isset($profile_info['specialities']) ? $profile_info['specialities'] : '');
		$profile_info['overview'] = (isset($profile_info['overview']) ? $profile_info['overview'] : '');
		$profile_info['style_card'] = (isset($profile_info['style_card']) ? $profile_info['style_card'] : '');
		$profile_info['college'] = (isset($profile_info['college']) ? $profile_info['college'] : '');
		$profile_info['highschool'] = (isset($profile_info['highschool']) ? $profile_info['highschool'] : '');
		$profile_info['job'] = (isset($profile_info['job']) ? $profile_info['job'] : '');

		$profile_info['assotiations'] = (isset($profile_info['assotiations']) ? $profile_info['assotiations'] : '');
		$profile_info['p_positions'] = (isset($profile_info['p_positions']) ? $profile_info['p_positions'] : '');
		$profile_info['p_companies'] = (isset($profile_info['p_companies']) ? $profile_info['p_companies'] : '');
		$profile_info['income'] = (isset($profile_info['income']) ? $profile_info['income'] : '');
		$profile_info['education'] = (isset($profile_info['education']) ? $profile_info['education'] : '');
		$profile_info['hobbies'] = (isset($profile_info['hobbies']) ? $profile_info['hobbies'] : '');
		$profile_info['whatatrip'] = (isset($profile_info['whatatrip']) ? $profile_info['whatatrip'] : '');
		$profile_info['topdestinations'] = (isset($profile_info['topdestinations']) ? $profile_info['topdestinations'] : '');
		$profile_info['education'] = (isset($profile_info['education']) ? $profile_info['education'] : '');
		$profile_info['topgateaways'] = (isset($profile_info['topgateaways']) ? $profile_info['topgateaways'] : '');
		$profile_info['hotclubs'] = (isset($profile_info['hotclubs']) ? $profile_info['hotclubs'] : '');
		$profile_info['besthotel'] = (isset($profile_info['besthotel']) ? $profile_info['besthotel'] : '');
		$profile_info['finerestaurants'] = (isset($profile_info['finerestaurants']) ? $profile_info['finerestaurants'] : '');
		$profile_info['whatnext'] = (isset($profile_info['whatnext']) ? $profile_info['whatnext'] : '');
		$profile_info['destinations'] = (isset($profile_info['destinations']) ? $profile_info['destinations'] : '');
		$profile_info['gataways'] = (isset($profile_info['gataways']) ? $profile_info['gataways'] : '');
		$profile_info['club'] = (isset($profile_info['club']) ? $profile_info['club'] : '');
		$profile_info['hotel'] = (isset($profile_info['hotel']) ? $profile_info['hotel'] : '');
		$profile_info['restaurants'] = (isset($profile_info['restaurants']) ? $profile_info['restaurants'] : '');
		$profile_info['skills'] = (isset($profile_info['skills']) ? $profile_info['skills'] : '');


		$profile_info['paypal_email'] = (isset($profile_info['paypal_email']) ? $profile_info['paypal_email'] : '');
		$profile_info['charges'] = (isset($profile_info['charges']) ? $profile_info['charges'] : '');
		$profile_info['instant_access'] = (isset($profile_info['instant_access']) ? $profile_info['instant_access'] : '');
		$profile_info['what_is'] = (isset($profile_info['what_is']) ? $profile_info['what_is'] : '');
		$profile_info['res_experience'] = (isset($profile_info['res_experience']) ? $profile_info['res_experience'] : '');
		$profile_info['fantasies'] = (isset($profile_info['fantasies']) ? $profile_info['fantasies'] : '');
		$profile_info['sex_experience'] = (isset($profile_info['sex_experience']) ? $profile_info['sex_experience'] : '');
		$profile_info['perfect_soul'] = (isset($profile_info['perfect_soul']) ? $profile_info['perfect_soul'] : '');
		$profile_info['crush'] = (isset($profile_info['crush']) ? $profile_info['crush'] : '');
		$profile_info['heart_breken'] = (isset($profile_info['heart_breken']) ? $profile_info['heart_breken'] : '');
		$profile_info['heart_been_breken'] = (isset($profile_info['heart_been_breken']) ? $profile_info['heart_been_breken'] : '');
		$profile_info['other_sex'] = (isset($profile_info['other_sex']) ? $profile_info['other_sex'] : '');
		$profile_info['favorite'] = (isset($profile_info['favorite']) ? $profile_info['favorite'] : '');
		$profile_info['turn_on'] = (isset($profile_info['turn_on']) ? $profile_info['turn_on'] : '');
		$profile_info['ego'] = (isset($profile_info['ego']) ? $profile_info['ego'] : '');
		$profile_info['music'] = (isset($profile_info['music']) ? $profile_info['music'] : '');
		$profile_info['activated'] = (isset($profile_info['activated']) ? $profile_info['activated'] : '');
		$profile_info['bg_image'] = (isset($profile_info['bg_image']) && (strlen($profile_info['bg_image']) > 7) ? $this->profile_url . $profile_info['bg_image'] : '');
		$profile_info['theme'] = (isset($profile_info['theme']) ? $profile_info['theme'] : '');
	    }
	    $profile_info['caddress'] = str_replace(array("\r\n", "\r", "\n", "<br />"), "\\n", $profile_info['caddress']);
	    $profile_info['quote'] = (isset($profile_info['quote']) ? $profile_info['quote'] : '');
	    $profile_info['skills'] = str_replace(array("\r\n", "\r", "\n", "<br />"), "\\n", $profile_info['skills']);
	    $profile_info['quote'] = str_replace(array("\r\n", "\r", "\n", "<br />"), "\\n", $profile_info['quote']);
	    $profile_info['interests'] = str_replace(array("\r\n", "\r", "\n", "<br />"), "\\n", $profile_info['interests']);

	    $profile_info['books'] = str_replace(array("\r\n", "\r", "\n", "<br />"), "\\n", $profile_info['books']);
	    $profile_info['music'] = str_replace(array("\r\n", "\r", "\n", "<br />"), "\\n", $profile_info['music']);
	    $profile_info['movies'] = str_replace(array("\r\n", "\r", "\n", "<br />"), "\\n", $profile_info['movies']);
	    $profile_info['travel'] = str_replace(array("\r\n", "\r", "\n", "<br />"), "\\n", $profile_info['travel']);
	    $profile_info['clubs'] = str_replace(array("\r\n", "\r", "\n", "<br />"), "\\n", $profile_info['clubs']);
	    $profile_info['about'] = str_replace(array("\r\n", "\r", "\n", "<br />", "\""), "\\n", $profile_info['about']);
	    $profile_info['meet_people'] = str_replace(array("\r\n", "\r", "\n", "<br />"), "\\n", $profile_info['meet_people']);
	    $profile_info['position'] = str_replace(array("\r\n", "\r", "\n", "<br />"), "\\n", $profile_info['position']);
	    $profile_info['company'] = str_replace(array("\r\n", "\r", "\n", "<br />"), "\\n", $profile_info['company']);
	    $profile_info['occupation'] = str_replace(array("\r\n", "\r", "\n", "<br />"), "\\n", $profile_info['occupation']);
	    $profile_info['industry'] = str_replace(array("\r\n", "\r", "\n", "<br />"), "\\n", $profile_info['industry']);
	    $profile_info['specialities'] = str_replace(array("\r\n", "\r", "\n", "<br />"), "\\n", $profile_info['specialities']);
	    $profile_info['overview'] = str_replace(array("\r\n", "\r", "\n", "<br />"), "\\n", $profile_info['overview']);
	    $profile_info['style_card'] = str_replace(array("\r\n", "\r", "\n", "<br />"), "\\n", $profile_info['style_card']);
	    $profile_info['college'] = str_replace(array("\r\n", "\r", "\n", "<br />"), "\\n", $profile_info['college']);
	    $profile_info['highschool'] = str_replace(array("\r\n", "\r", "\n", "<br />"), "\\n", $profile_info['highschool']);
	    $profile_info['hobbies'] = str_replace(array("\r\n", "\r", "\n", "<br />"), "\\n", $profile_info['hobbies']);
	    $profile_info['gender'] = isset($profile_info['gender']) ? $profile_info['gender'] : '';

	    $date = isset($profile_info['birthday']) && ($profile_info['birthday']) ? date("F j, Y", $profile_info['birthday']) : NULL;

	    $response_str = response_repeat_string();
	    $response_mess = '
{
  ' . $response_str . '
   "Profile":{
      "errorCode":"' . $userinfocode . '",
      "errorMsg":"' . $userinfodesc . '",
       "Paypal Email":"' . str_replace('"', '\"', $profile_info['paypal_email']) . '",
       "Enter Fee you want to charge":"' . str_replace('"', '\"', $profile_info['charges']) . '",
       "Instant Access":"' . str_replace('"', '\"', $profile_info['instant_access']) . '",
       "Vices":"' . str_replace('"', '\"', $profile_info['what_is']) . '",
       "Recent wild/crazy/Exotic experience/story":"' . str_replace('"', '\"', $profile_info['res_experience']) . '",
       "Fantasies":"' . str_replace('"', '\"', $profile_info['fantasies']) . '",
       "Most Exotic Sexual Experience":"' . str_replace('"', '\"', $profile_info['sex_experience']) . '",
       "Perfect soul mate":"' . str_replace('"', '\"', $profile_info['perfect_soul']) . '",
       "Current crush":"' . str_replace('"', '\"', $profile_info['crush']) . '",
       "How many hearts broken":"' . str_replace('"', '\"', $profile_info['heart_breken']) . '",
       "How many times my heart has been broken":"' . str_replace('"', '\"', $profile_info['heart_been_breken']) . '",
       "If I was the other sex, I would...":"' . str_replace('"', '\"', $profile_info['other_sex']) . '",
       "Favorite Position":"' . str_replace('"', '\"', $profile_info['favorite']) . '",
       "What turns me on":"' . str_replace('"', '\"', $profile_info['turn_on']) . '",
           "Address":"' . str_replace('"', '\"', $profile_info['caddress']) . '",
           "Business Phone":"' . str_replace('"', '\"', $profile_info['business_phone']) . '",
           "Club Type":"' . str_replace('"', '\"', $profile_info['club_type']) . '",
           "Services":"' . $profile_info['services'] . '",
           "Description":"' . str_replace('"', '\"', $profile_info['cdescription']) . '",
           "Dress Code":"' . str_replace('"', '\"', $profile_info['door_policy']) . '",
           "Club Admission":"' . str_replace('"', '\"', $profile_info['club_admission']) . '",
           "Parking":"' . str_replace('"', '\"', $profile_info['parking']) . '",
           "House of Operation":"' . str_replace('"', '\"', $profile_info['house_of_operation']) . '",
           "Capacity":"' . str_replace('"', '\"', $profile_info['capacity']) . '",
           "Purchase Tickets":"' . str_replace('"', '\"', $profile_info['purchase_ticket']) . '",
       "My alter ego":"' . str_replace('"', '\"', $profile_info['ego']) . '",
       "Music":"' . str_replace('"', '\"', $profile_info['cmusic']) . '",
      "website":"' . str_replace('"', '\"', $website) . '",
      "Website":"' . str_replace('"', '\"', $Website) . '",
      "Age Limit":"' . str_replace('"', '\"', $profile_info['age_limit']) . '",
      "Alcohol":"' . str_replace('"', '\"', $alcohol) . '",
      "Gender":"' . str_replace('"', '\"', $profile_info['gender']) . '",
      "Birthday":"' . str_replace('"', '\"', $date) . '",
      "Country":"' . str_replace('"', '\"', $profile_info['country']) . '",
      "State":"' . str_replace('"', '\"', $profile_info['state']) . '",
      "City":"' . str_replace('"', '\"', $profile_info['city']) . '",
       ' . $str . '
      "userName":"' . str_replace('"', '\"', preg_replace('/\s+/', ' ', $profile_info['profilenam'])) . '",
      "Profile Url":"' . str_replace('"', '\"', $url) . '",
      "Profile Type":"' . str_replace('"', '\"', $profile_info['profile_type']) . '",
      "Here for":"' . str_replace('"', '\"', $profile_info['here_for']) . '",
      "Interests":"' . addslashes(strip_tags(stripslashes($profile_info['interests']))) . '",
      "PersonalQuote":"' . addslashes(strip_tags(stripslashes($profile_info['quote']))) . '",
      "Hometown":"' . str_replace('"', '\"', $profile_info['hometown']) . '",
      "Languages":"' . str_replace('"', '\"', $profile_info['languages']) . '",
      "Bio":"' . str_replace('"', '\"', $profile_info['schools']) . '",
      "Skills":"' . str_replace('"', '\"', $profile_info['skills']) . '",
      "Past Positions":"' . str_replace('"', '\"', $profile_info['p_positions']) . '",
      "Past Companies":"' . str_replace('"', '\"', $profile_info['p_positions']) . '",
      "Associations":"' . str_replace('"', '\"', $profile_info['assotiations']) . '",
      "Laungage":"' . str_replace('"', '\"', $profile_info['languages']) . '",
      "Personal Website":"' . str_replace('"', '\"', strip_tags(stripslashes($profile_info['website']))) . '",
      "Favorite Books":"' . str_replace('"', '\"', strip_tags(stripslashes($profile_info['books']))) . '",
      "Favorite Music":"' . str_replace('"', '\"', strip_tags(stripslashes($profile_info['music']))) . '",
      "Favorite Movies/TV":"' . str_replace('"', '\"', strip_tags(stripslashes($profile_info['movies']))) . '",
        "Pet Peeves":"' . str_replace('"', '\"', strip_tags(stripslashes($profile_info['travel']))) . '",
        "Guilty Pleasures":"' . str_replace('"', '\"', strip_tags(stripslashes($profile_info['clubs']))) . '",
        "AboutMe":"' . str_replace('"', '\"', strip_tags(stripslashes($profile_info['about']))) . '",
        "I want to meet people for":"' . str_replace('"', '\"', strip_tags(stripslashes($profile_info['meet_people']))) . '",
        "Position/Title":"' . str_replace('"', '\"', strip_tags(stripslashes($profile_info['position']))) . '",
        "Company":"' . str_replace('"', '\"', strip_tags(stripslashes($profile_info['company']))) . '",
        "Occupation":"' . str_replace('"', '\"', strip_tags(stripslashes($profile_info['occupation']))) . '",
        "Industry":"' . str_replace('"', '\"', strip_tags(stripslashes($profile_info['industry']))) . '",
        "Specialities":"' . str_replace('"', '\"', strip_tags(stripslashes($profile_info['specialities']))) . '",
        "Overview":"' . str_replace('"', '\"', strip_tags(stripslashes($profile_info['overview']))) . '",
        "What a Trip":"' . str_replace('"', '\"', strip_tags(stripslashes($profile_info['style_card']))) . '",
        "College":"' . str_replace('"', '\"', strip_tags(stripslashes($profile_info['college']))) . '",
        "High School":"' . str_replace('"', '\"', strip_tags(stripslashes($profile_info['highschool']))) . '",
        "Dream job I like to get":"' . str_replace('"', '\"', $profile_info['job']) . '",
        "Marital Status":"' . str_replace('"', '\"', $profile_info['marstat']) . '",
        "Religion":"' . str_replace('"', '\"', $profile_info['religion']) . '",
        "Smoker":"' . str_replace('"', '\"', $profile_info['smoker']) . '",
        "Drinker":"' . str_replace('"', '\"', $profile_info['drinker']) . '",
        "Children":"' . str_replace('"', '\"', $profile_info['children']) . '",
        "Income":"' . str_replace('"', '\"', $profile_info['income']) . '",
        "Education":"' . str_replace('"', '\"', $profile_info['education']) . '",
        "Hobbies":"' . str_replace('"', '\"', strip_tags(stripslashes($profile_info['hobbies']))) . '",
        "What a Trip":"' . str_replace('"', '\"', strip_tags(stripslashes($profile_info['whatatrip']))) . '",
        "Top Destinations":"' . str_replace('"', '\"', strip_tags(stripslashes($profile_info['topdestinations']))) . '",
        "Top Getaways":"' . str_replace('"', '\"', strip_tags(stripslashes($profile_info['topgateaways']))) . '",
        "Hottest Clubs/Bars":"' . str_replace('"', '\"', strip_tags(stripslashes($profile_info['hotclubs']))) . '",
        "Best Hotel/Resorts":"' . str_replace('"', '\"', strip_tags(stripslashes($profile_info['besthotel']))) . '",
        "Finest Restaurants":"' . str_replace('"', '\"', strip_tags(stripslashes($profile_info['finerestaurants']))) . '",
        "What I am up to next":"' . str_replace('"', '\"', strip_tags(stripslashes($profile_info['whatnext']))) . '",
        "Destinations":"' . str_replace('"', '\"', strip_tags(stripslashes($profile_info['destinations']))) . '",
        "Getaways":"' . str_replace('"', '\"', strip_tags(stripslashes($profile_info['gataways']))) . '",
        "Clubs/Bars":"' . str_replace('"', '\"', strip_tags(stripslashes($profile_info['club']))) . '",
        "Restaurants":"' . str_replace('"', '\"', strip_tags(stripslashes($profile_info['restaurants']))) . '",
        "Hotel/Resorts":"' . str_replace('"', '\"', strip_tags(stripslashes($profile_info['hotel']))) . '",
        "latitude":"' . str_replace('"', '\"', strip_tags(stripslashes(trim($profile_info['latitude'])))) . '",
        "longitude":"' . str_replace('"', '\"', strip_tags(stripslashes(trim($profile_info['longitude'])))) . '"
   }
}
';
	} else {
	    $userinfocode = $response_message['Profile']['ErrorCode'];
	    $userinfodesc = $response_message['Profile']['ErrorDesc'];
	    $response_mess = get_response_string("Profile", $userinfocode, $userinfodesc);
	}
	if (DEBUG)
	    writelog("Response:profile():", $response_mess, false);
	return getValidJSON($response_mess);
    }

    /* Function:profileParentComment($response_message, $xmlrequest)
      Description:to get all Parent comment on user or Entourage Profile.
     * Parameters: $xmlrequest=>request by user,
     *             $response_message=>validation message.
      Return:string
     *  */

    function profileParentComment($response_message, $xmlrequest) {
	if (isset($response_message['ProfileParentComment']['SuccessCode']) && ( $response_message['ProfileParentComment']['SuccessCode'] == '000')) {
	    $profile_hotpress = array();
	    $pagenumber = $xmlrequest['ProfileParentComment']['pageNumber'];
	    $profile_hotpress = $this->profile_showall_comments($xmlrequest, $pagenumber, 10);
	    $userinfocode = $response_message['ProfileParentComment']['SuccessCode'];
	    $userinfodesc = $response_message['ProfileParentComment']['SuccessDesc'];
		$postcount = 0;
	    $postcount = isset($profile_hotpress['count']) && ($profile_hotpress['count']) ? $profile_hotpress['count'] : 0;
	    $str = '';
		$str = $profile_hotpress['str'];
	    
		$str = substr($str, 0, strlen($str) - 1);
	    $response_str = response_repeat_string();
	    $response_mess = '
{
    ' . $response_str . '
   "ProfileParentComment":{
      "errorCode":" ' . $userinfocode . '",
      "errorMsg":" ' . $userinfodesc . '",
      "postCount":"' . $postcount . '",
      "totalPostCount":"' . $profile_hotpress['Total'] . '",
      "Posts":[
         ' . $str . '
      ]
   }
}';
	} else {
	    $userinfocode = $response_message['ProfileParentComment']['ErrorCode'];
	    $userinfodesc = $response_message['ProfileParentComment']['ErrorDesc'];
	    $response_mess = get_response_string("ProfileParentComment", $userinfocode, $userinfodesc);
	}
	if (DEBUG)
	    writelog("Response:profileParentComment():", $response_mess, false);
	return getValidJSON($response_mess);
    }

    /* Function:profileSubComments($response_message, $xmlrequest)
      Description:to get all sub comment related to particular Parent comment on user or Entourage Profile.
     * Parameters: $xmlrequest=>request by user,
     *             $response_message=>validation message.
      Return:string
     *  */

    function profileSubComments($response_message, $xmlrequest) {

	if (isset($response_message['ProfileSubComments']['SuccessCode']) && ( $response_message['ProfileSubComments']['SuccessCode'] == '000')) {
	    $profile_hotpress = array();
	    $pagenumber = $xmlrequest['ProfileSubComments']['pageNumber'];
	    $profile_hotpress = $this->profile_sub_comments($xmlrequest, $pagenumber, 20);
	    $userinfocode = $response_message['ProfileSubComments']['SuccessCode'];
	    $userinfodesc = $response_message['ProfileSubComments']['SuccessDesc'];
	    $count = isset($profile_hotpress['count']) && ($profile_hotpress['count']) ? $profile_hotpress['count'] : 0;
	    $total_count = isset($profile_hotpress['totalrecords']) && ($profile_hotpress['totalrecords']) ? $profile_hotpress['totalrecords'] : 0;
	    //$postcount = 0;
	    $str = '';
		$str=isset($profile_hotpress['str'])?$profile_hotpress['str']:"";
		
	    /*for ($i = 0; $i < $count; $i++) {
		//print_r($profile_hotpress[$i]);

		$profile_hotpress[$i]['photo_b_thumb'] = ((isset($profile_hotpress[$i]['is_facebook_user'])) && (strlen($profile_hotpress[$i]['photo_b_thumb']) > 7) && ($profile_hotpress[$i]['is_facebook_user'] == 'y' || $profile_hotpress[$i]['is_facebook_user'] == 'Y')) ? ((strstr($profile_hotpress[$i]['photo_b_thumb'], "photos") != FALSE && strstr($profile_hotpress[$i]['photo_b_thumb'], "http") == FALSE) ? $this->profile_url . $profile_hotpress[$i]['photo_b_thumb'] : $profile_hotpress[$i]['photo_b_thumb']) : ((isset($profile_hotpress[$i]['photo_b_thumb']) && (strlen($profile_hotpress[$i]['photo_b_thumb']) > 7)) ? $this->profile_url . $profile_hotpress[$i]['photo_b_thumb'] : $this->profile_url . default_images($profile_hotpress[$i]['gender'], $profile_hotpress[$i]['profile_type']));

		$input = $profile_hotpress[$i]['testimonial'];
		$input = str_replace('\\', '', $input);
		if (preg_match(REGEX_URL, $input, $url)) {
		    $postType = extract_url($input);
		    $postType = strip_tags($postType);
		    $postType = str_replace(array("\r\n", "\r", "\n", "<br />", "\""), "\\n", $postType);
		} else {
		    $postType = 'text';
		}
		$profile_hotpress[$i]['testimonial'] = str_replace('\\', "", $profile_hotpress[$i]['testimonial']);

		$profile_hotpress[$i]['testimonial'] = str_replace(array("\r\n", "\r", "\n", "<br />", "<br/>"), "\\n", $profile_hotpress[$i]['testimonial']);
		$profile_hotpress[$i]['testimonial'] = strip_tags($profile_hotpress[$i]['testimonial']);
		$profile_hotpress[$i]['testimonial'] = str_replace(array("\""), "", $profile_hotpress[$i]['testimonial']);
		$postVia = ((isset($profile_hotpress[$i]['post_via'])) && ($profile_hotpress[$i]['post_via'])) ? "iPhone" : "";
		$date = time_difference($profile_hotpress[$i]['added']); //date("d/m/y : H:i:s", $hotpress[$i]['date'])
		$str_temp = '{
                                     "postId":"' . str_replace('"', '\"', $profile_hotpress[$i]['tst_id']) . '",
                                     "authorID":"' . str_replace('"', '\"', $profile_hotpress[$i]['mem_id']) . '",
                                     "authorProfileImgURL":"' . str_replace('"', '\"', $profile_hotpress[$i]['photo_b_thumb']) . '",
                                     "authorName":"' . str_replace('"', '\"', $profile_hotpress[$i]['profilenam']) . '",
                                     "gender":"' . str_replace('"', '\"', $profile_hotpress[$i]['gender']) . '",
                                     "profileType":"' . str_replace('"', '\"', $profile_hotpress[$i]['profile_type']) . '",
                                     "postText":"' . trim(preg_replace('/\s+/', ' ', str_replace("\'", "'", htmlspecialchars_decode(str_replace('"', '\"', $profile_hotpress[$i]['testimonial']), ENT_QUOTES)))) . '",
                                     "postType":"' . str_replace('"', '\"', $postType) . '",
                                     "postTimestamp":"' . str_replace('"', '\"', $date) . '",
                                     "postVia":"' . str_replace('"', '\"', $postVia) . '"
                                     
                                 }';
		//$postcount++;    "commentsCount":"' . $count . '"  for child comments
		$str = $str . $str_temp;
		$str = $str . ',';
	    }*/
	    $str = substr($str, 0, strlen($str) - 1);
	    $response_str = response_repeat_string();
	    $response_mess = '
{
    ' . $response_str . '
   "ProfileSubComments":{
      "errorCode":"' . $userinfocode . '",
      "errorMsg":"' . $userinfodesc . '",
      "postCount":"' . $count . '",
      "totalPostCount":"' . $total_count . '",
      "Posts":[
         ' . $str . '
      ]
   }
}';
	} else {
	    $userinfocode = $response_message['ProfileSubComments']['ErrorCode'];
	    $userinfodesc = $response_message['ProfileSubComments']['ErrorDesc'];
	    $response_mess = get_response_string("ProfileSubComments", $userinfocode, $userinfodesc);
	}
	if (DEBUG)
	    writelog("Response:profileSubComments():", $response_mess, false);
	return getValidJSON($response_mess);
    }

    /* Function:postCommentOnProfile($response_message, $xmlrequest)
      Description:to send comment on user or Entourage Profile.
     * Parameters: $xmlrequest=>request by user,
     *              $response_message=>validation message.
      Return:string
     *  */

    function postCommentOnProfile($response_message, $xmlrequest) {
	if (isset($response_message['PostCommentOnProfile']['SuccessCode']) && ( $response_message['PostCommentOnProfile']['SuccessCode'] == '000')) {
	    $userinfo = array();
	    $userinfo = $this->post_comment_on_profile($xmlrequest);

	    if ((isset($userinfo['PostCommentOnProfile']['successful_fin'])) && (!$userinfo['PostCommentOnProfile']['successful_fin'])) {
		$obj_error = new Error();
		$response_message = $obj_error->error_type("PostCommentOnProfile", $userinfo);
		$userinfocode = $response_message['PostCommentOnProfile']['ErrorCode'];
		$userinfodesc = $response_message['PostCommentOnProfile']['ErrorDesc'];
		$response_mess = $response_mess = get_response_string("PostCommentOnProfile", $userinfocode, $userinfodesc);
		return getValidJSON($response_mess);
	    }
	    $userinfocode = $response_message['PostCommentOnProfile']['SuccessCode'];
	    $userinfodesc = $response_message['PostCommentOnProfile']['SuccessDesc'];
	    $response_str = response_repeat_string();
	    $str_id = isset($userinfo['id']) ? '"postId":"' . $userinfo['id'] . '",' : NULL;
	    $response_mess = '
{
  ' . $response_str . '
   "PostCommentOnProfile":{
    ' . $str_id . '
      "errorCode":"' . $userinfocode . '",
      "errorMsg":"' . $userinfodesc . '"
   }
}
 	';
	} else {
	    $userinfocode = $response_message['PostCommentOnProfile']['ErrorCode'];
	    $userinfodesc = $response_message['PostCommentOnProfile']['ErrorDesc'];
	    $response_mess = $response_mess = get_response_string("PostCommentOnProfile", $userinfocode, $userinfodesc);
	}
	if (DEBUG)
	    writelog("Response:hotPressPostComment():", $response_mess, false);
	return getValidJSON($response_mess);
    }

    /* Function:deleteProfileMessage($response_message, $xmlrequest)
      Description:to delete comment on user or Entourage Profile.
     * Parameters: $xmlrequest=>request by user,
     *              $response_message=>validation message.
      Return:string
     *  */

    function deleteProfileMessage($response_message, $xmlrequest) {
	if (isset($response_message['DeleteProfileMessage']['SuccessCode']) && ( $response_message['DeleteProfileMessage']['SuccessCode'] == '000')) {
	    $userinfo = array();

	    $userinfo = $this->delete_post_from_profile($xmlrequest);
	    if ((isset($userinfo['DeleteProfileMessage']['successful_fin'])) && (!$userinfo['DeleteProfileMessage']['successful_fin'])) {
		$obj_error = new Error();
		$response_message = $obj_error->error_type("DeleteProfileMessage", $userinfo);

		$userinfocode = $response_message['DeleteProfileMessage']['ErrorCode'];
		$userinfodesc = $response_message['DeleteProfileMessage']['ErrorDesc'];
		$response_mess = $response_mess = get_response_string("DeleteProfileMessage", $userinfocode, $userinfodesc);
		return $response_mess;
	    }
	    $userinfocode = $response_message['DeleteProfileMessage']['SuccessCode'];
	    $userinfodesc = $response_message['DeleteProfileMessage']['SuccessDesc'];
	    $response_str = response_repeat_string();
	    $response_mess = '
{
  ' . $response_str . '
   "DeleteProfileMessage":{
      "errorCode":"' . $userinfocode . '",
      "errorMsg":"' . $userinfodesc . '"
   }
}
 	';
	} else {
	    $userinfocode = $response_message['DeleteProfileMessage']['ErrorCode'];
	    $userinfodesc = $response_message['DeleteProfileMessage']['ErrorDesc'];
	    $response_mess = $response_mess = get_response_string("DeleteProfileMessage", $userinfocode, $userinfodesc);
	}
	if (DEBUG)
	    writelog("Response:deleteProfileMessage():", $response_mess, false);
	return getValidJSON($response_mess);
    }

    /* Function:profileInfoShortDesc($response_message, $xmlrequest)
      Description:to get the information related to user or Entourage Profile in Short.
     * Parameters: $xmlrequest=>request by user,
     *              $response_message=>validation message.
      Return:string
     *  */

    function profileInfoShortDesc($response_message, $xmlrequest) {

	if (isset($response_message['profileInfo']['SuccessCode']) && ( $response_message['profileInfo']['SuccessCode'] == '000')) {
	    $profile_info = array();
	    $profile_info = $this->profile_info_short_desc($xmlrequest);
	    $userinfocode = $response_message['profileInfo']['SuccessCode'];
	    $userinfodesc = $response_message['profileInfo']['SuccessDesc'];
	    $is_friend = '';
	    $is_friend = (isset($profile_info['Profile_info']['friend'])) && ($profile_info['Profile_info']['friend']) ? 'yes' : 'no';


	    $friendRequestSent = isset($profile_info['friendRequestSent']) && ($profile_info['friendRequestSent']) ? 'yes' : 'no';
	    $addAsFriend = isset($profile_info['addAsFriend']) && ($profile_info['addAsFriend']) ? 'yes' : 'no';

	    $profile_info['Profile_info']['gender'] = isset($profile_info['Profile_info']['gender']) && ($profile_info['Profile_info']['gender']) ? $profile_info['Profile_info']['gender'] : NULL;

		
		$profile_info['Profile_info']['photo_b_thumb'] = ((isset($profile_info['Profile_info']['is_facebook_user'])) && (strlen($profile_info['Profile_info']['photo_b_thumb']) > 7) && ($profile_info['Profile_info']['is_facebook_user'] == 'y' || $profile_info['Profile_info']['is_facebook_user'] == 'Y')) ? ((strstr($profile_info['Profile_info']['photo_b_thumb'],"photos")!=FALSE && strstr($profile_info['Profile_info']['photo_b_thumb'],"http")==FALSE) ? $this->profile_url.$profile_info['Profile_info']['photo_b_thumb'] : $profile_info['Profile_info']['photo_b_thumb']) : ((isset($profile_info['Profile_info']['photo_b_thumb']) && (strlen($profile_info['Profile_info']['photo_b_thumb']) > 7)) ? $this->profile_url . $profile_info['Profile_info']['photo_b_thumb'] : $this->profile_url . default_images1($profile_info['Profile_info']['gender'], $profile_info['Profile_info']['profile_type']));
		
//	    $profile_info['Profile_info']['is_badges_available'] = is_bool($profile_info['is_badges_available']) === true ? 'Y' : 'N';
	    $response_str = response_repeat_string();
	    $response_mess = '
{
  ' . $response_str . '
   "profileInfo":{
      "errorCode":"' . $userinfocode . '",
      "errorMsg":"' . str_replace('"', '\"', $userinfodesc) . '",
      "isFriend":"' . str_replace('"', '\"', $is_friend) . '",
      "isFriendRequestSent":"' . str_replace('"', '\"', $friendRequestSent) . '",
      "addAsFriend":"' . str_replace('"', '\"', $addAsFriend) . '",
      "privacySettings":"' . str_replace('"', '\"', $profile_info['Profile_info']['privacy']) . '",
      "userName":"' . str_replace('"', '\"', preg_replace('/\s+/', ' ', $profile_info['Profile_info']['profilenam'])) . '",
      "profileUrl":"' . str_replace('"', '\"', $profile_info['Profile_info']['photo_b_thumb']) . '",
      "profile Type":"' . str_replace('"', '\"', $profile_info['Profile_info']['profile_type']) . '",
      "is_badges_available":"' . str_replace('"', '\"', $profile_info['is_badges_available']) . '"
   }
}
';
	} else {
	    $userinfocode = $response_message['profileInfo']['ErrorCode'];
	    $userinfodesc = $response_message['profileInfo']['ErrorDesc'];
	    $response_mess = get_response_string("profileInfo", $userinfocode, $userinfodesc);
	}
	if (DEBUG)
	    writelog("Response:profileInfo():", $response_mess, false);
	return $response_mess;
    }

    /* Function:takeOverProfile($response_message, $xmlrequest)
      Description:to save the information related to userwho appeared in any event at particular venue
     * Parameters: $xmlrequest=>request by user,
     *             $response_message=>success message
      Return:string
     *  */

    function takeOverProfile($response_message, $xmlrequest) {
	$userinfo = array();

	$userinfo = $this->take_over_profile($xmlrequest);

	if ((isset($userinfo['TakeOverProfile']['successful_fin'])) && (!$userinfo['TakeOverProfile']['successful_fin'])) {
	    $obj_error = new Error();
	    $response_message = $obj_error->error_type("TakeOverProfile", $userinfo);

	    $userinfocode = $response_message['TakeOverProfile']['ErrorCode'];
	    $userinfodesc = $response_message['TakeOverProfile']['ErrorDesc'];
	    $response_mess = $response_mess = get_response_string("TakeOverProfile", $userinfocode, $userinfodesc);
	    return getValidJSON($response_mess);
	}

	if ((isset($userinfo['TakeOverProfile']['successful_fin'])) && ($userinfo['TakeOverProfile']['successful_fin'])) {


	    $response_mess = '
               {
   ' . response_repeat_string() . '
    "TakeOverProfile":{
           "errorCode":"' . $return_codes["TakeOverProfile"]["SuccessCode"] . '",
           "errorMsg":"' . $return_codes["TakeOverProfile"]["SuccessDesc"] . '"
   }
	  }';
	} else {

	    $response_mess = '
                {
   ' . response_repeat_string() . '
   "TakeOverProfile":{
      "errorCode":"' . $return_codes["TakeOverProfile"]["NoRecordErrorCode"] . '",
      "errorMsg":"' . $return_codes["TakeOverProfile"]["NoRecordErrorDesc"] . '"

   }
	  }';
	}
	return getValidJSON($response_mess);
    }

    /* Function:profile_photo_upload($xmlrequest)
      Description:to upload photo on a profile of particular user or his entourage.
     * Parameters: $xmlrequest=>request by user,
     *              $response_message=>success message
      Return:string
     *  */

    function profile_photo_upload($xmlrequest) {
	if (DEBUG)
	    writelog("profile.class.php :: profile_photo_upload() : ", "Start Here ", false);

	$error = array();
	$error = photo_upload($xmlrequest);
	if (DEBUG)
	    writelog("profile.class.php :: profile_photo_upload() : ", $error, true);
	if (DEBUG)
	    writelog("profile.class.php :: profile_photo_upload() : ", "End Here ", false);

	return $error;
    }

    /* Function:profile_photo_upload_valid($xmlrequest)
      Description:to validate photo upload.
     * Parameters: $xmlrequest=>request by user,
     *              $response_message=>success message
      Return:string
     *  */

    function profile_photo_upload_valid($xmlrequest) {
	if (DEBUG)
	    writelog("profile.class.php :: photo_upload_valid() : ", "Start Here ", false);

	$error = array();
	$error = photo_upload_valid($xmlrequest);
	$entourageId = $xmlrequest['PhotoUpload']['entourageId'];
	$userId = $xmlrequest['PhotoUpload']['userId'];
	if ($entourageId != $userId)
	    push_notification('post_comment_on_profile', $entourageId, $userId);
	if (DEBUG) {
	    writelog("profile.class.php :: photo_upload_valid() : ", $error, true);
	    writelog("profile.class.php :: photo_upload_valid() : ", "End Here ", false);
	}
	return $error;
    }

    private function badgeDetails($jsonResponse) {
	// if (DEBUG)
	writelog("profile.class.php :: badgeDetails() :: ", "Starts Here ", false);

	$getBadgesDetailInfo = array();
	$userId = mysql_real_escape_string($jsonResponse['BadgeDetails']['userId']);
	$badgeId = mysql_real_escape_string($jsonResponse['BadgeDetails']['badgeId']);
	$isActive = mysql_real_escape_string($jsonResponse['BadgeDetails']['badgeEarned']);
	if ($isActive == 'yes') {
	    $getBadge = "SELECT ba.id,b.badge_name,b.public_hint_active as badge_description,b.badge_img,b.badge_bottle_img,ba.venue_id,mem.profilenam,ba.date_alert as createdate FROM bottel_alert ba,badges b,members mem WHERE ba.mem_id = '$userId' AND ba.id = '$badgeId' AND ba.badge_type_id = b.badge_id AND ba.venue_id = mem.mem_id";
	    $getBadgesDetailInfo = execute_query($getBadge, FALSE, "select");
	    if (!empty($getBadgesDetailInfo) && (is_array($getBadgesDetailInfo))) {
//	    if (DEBUG)
		/* update bottel_alert table when viewed */
		$getUpdateBadge = "UPDATE bottel_alert SET alert_status='Y' WHERE id='$badgeId';";
		$getUpdateBadgesDetailInfo = execute_query($getUpdateBadge, TRUE, "update");

		writelog("profile.class.php :: badgeDetails() : ", $getBadge, false);
		writelog("profile.class.php :: badgeDetails() : ", $getBadgesDetailInfo, true);
		$getBadgesDetailInfo['badgeEarned'] = 'yes';
		if ($getBadgesDetailInfo['badge_name'] == 'PartyAnimal') {
		    $getAllAmb = execute_query("SELECT venue_id FROM bottel_alert WHERE mem_id='$userId' AND bottel_type='LAST_90_DAY_3_APP'", true, "select");
		    $venueName = '';
		    $venueId = '';
		    foreach ($getAllAmb AS $ii => $ambList) {
			if (is_array($ambList)) {
			    $venueName .= getname($ambList['venue_id']) . ',';
			    $venueId .= $ambList['venue_id'] . ',';
			}
		    }
		    $venueName1 = rtrim($venueName, ',');
		    $venueId1 = rtrim($venueId, ',');
		    $getBadgesDetailInfo['venueId'] = $venueId1;
		    $getBadgesDetailInfo['badgeUnlockedAtVenue'] = $venueName1;
		} else {
		    $getBadgesDetailInfo['venueId'] = $getBadgesDetailInfo['venue_id'];
		    $getBadgesDetailInfo['badgeUnlockedAtVenue'] = getname($getBadgesDetailInfo['venue_id']);
		}
	    } else {
		return FALSE;
	    }
	} else {
	    $getBadge = "select badge_id,badge_name,badge_bw_img,badge_bottle_bw_img,public_hint_inactive as badge_description from badges where badge_id='$badgeId'";
	    $getBadgesDetailInfo = execute_query($getBadge, FALSE, "select");
	    if (!empty($getBadgesDetailInfo) && (is_array($getBadgesDetailInfo))) {
//	    if (DEBUG)
		writelog("profile.class.php :: badgeDetails() : ", $getBadge, false);
		writelog("profile.class.php :: badgeDetails() : ", $getBadgesDetailInfo, true);
		$getBadgesDetailInfo['venueId'] = '';
		$getBadgesDetailInfo['badgeUnlockedAtVenue'] = '';
		$getBadgesDetailInfo['badgeUnlockedAtTime'] = '';
		$getBadgesDetailInfo['badgeEarned'] = 'no';
	    } else {
		return FALSE;
	    }
	}
	// if (DEBUG)
	writelog("profile.class.php :: badgeDetails() :: ", "Ends Here ", false);
	return $getBadgesDetailInfo;
    }

    public function badgeDetailsInfo($response_message, $jsonRequest) {
	global $return_codes;
	$badgeDetailsInfo = array();
	$badgeDetailsInfo = self::badgeDetails($jsonRequest);

	if (is_array($badgeDetailsInfo) && !empty($badgeDetailsInfo)) {
	    //date_default_timezone_set("UTC");
	    $badgeId = isset($badgeDetailsInfo['id']) && ($badgeDetailsInfo['id']) ? $badgeDetailsInfo['id'] : NULL;
	    $badgeDescription = isset($badgeDetailsInfo['badge_description']) && ($badgeDetailsInfo['badge_description']) ? str_replace('"', '\"', $badgeDetailsInfo['badge_description']) : NULL;
	    $bottelType = isset($badgeDetailsInfo['bottel_type']) && ($badgeDetailsInfo['bottel_type']) ? str_replace('"', '\"', $badgeDetailsInfo['bottel_type']) : str_replace('"', '\"', $badgeDetailsInfo['badge_name']);
	    // $createDate = isset($badgeDetailsInfo['createdate']) && ($badgeDetailsInfo['createdate']) ? date("l M. d,Y H:i A", strtotime($badgeDetailsInfo['createdate'])) : NULL;
	    $createDate = isset($badgeDetailsInfo['createdate']) && ($badgeDetailsInfo['createdate']) ? $badgeDetailsInfo['createdate'] : NULL; //date("Y-m-d H:i:s", strtotime($badgeDetailsInfo['createdate']))
	    $badgeEarned = isset($badgeDetailsInfo['badgeEarned']) && ($badgeDetailsInfo['badgeEarned']) ? $badgeDetailsInfo['badgeEarned'] : NULL;
	    $badgeUnlockedAtVenue = isset($badgeDetailsInfo['badgeUnlockedAtVenue']) && ($badgeDetailsInfo['badgeUnlockedAtVenue']) ? $badgeDetailsInfo['badgeUnlockedAtVenue'] : NULL;
	    $badgeThumbImageURL = isset($badgeDetailsInfo['badge_img']) && ($badgeDetailsInfo['badge_img']) ? $badgeDetailsInfo['badge_img'] : $badgeDetailsInfo['badge_bw_img'];
	    $badgeImageURL = isset($badgeDetailsInfo['badge_bottle_img']) && ($badgeDetailsInfo['badge_bottle_img']) ? $badgeDetailsInfo['badge_bottle_img'] : $badgeDetailsInfo['badge_bottle_bw_img'];
	    $venueId = isset($badgeDetailsInfo['venueId']) && ($badgeDetailsInfo['venueId']) ? $badgeDetailsInfo['venueId'] : NULL;
	    if ($bottelType == 'LAST_90_DAY_3_APP')
		$bottelType = 'Ambassador';
	    $str_temp = '{
            "badgeId":"' . str_replace('"', '\"', $badgeId) . '",
            "badgeName":"' . str_replace('"', '\"', $bottelType) . '",
            "badgeDescription":"' . str_replace('"', '\"', trim($badgeDescription)) . '",
            "badgeThumbImageURL":"' . str_replace('"', '\"', ROOT_URL . $badgeThumbImageURL) . '",
            "badgeImageURL":"' . str_replace('"', '\"', ROOT_URL . $badgeImageURL) . '",
            "badgeEarned":"' . str_replace('"', '\"', $badgeEarned) . '",
            "venueId":"' . str_replace('"', '\"', $venueId) . '",
            "badgeUnlockedAtVenue":"' . str_replace('"', '\"', $badgeUnlockedAtVenue) . '",
            "badgeUnlockedAtTime":"' . str_replace('"', '\"', $createDate) . '"
	    }';

	    $str .= $str_temp;
	    $str .=',';

	    $str = rtrim($str, ',');
	    $response_str = response_repeat_string();
	    $response_mess = '
               {
		   ' . response_repeat_string() . '
		    "BadgeDetails":{
			   "errorCode":"' . $return_codes["BadgeDetails"]["SuccessCode"] . '",
			   "errorMsg":"' . $return_codes["BadgeDetails"]["SuccessDesc"] . '",
			   "BadgeList": [' . $str . ']
		   }
	  }';
	}else {
	    $response_mess = '
                {
           ' . response_repeat_string() . '
           "BadgeDetails":{
              "errorCode":"' . $return_codes["BadgeDetails"]["FailedToAddRecordCode"] . '",
              "errorMsg":"' . $return_codes["BadgeDetails"]["FailedToAddRecordDesc"] . '"
           }
        }';
	}
	return getValidJSON($response_mess);
    }

    public function GetBadges($response_date, $jsonResponse) {
	// if (DEBUG)
	writelog("profile.class.php :: GetBadges() :: ", "Starts Here ", false);

	$getBadgesInfo = array();
	$userId = mysql_real_escape_string($jsonResponse['GetBadges']['userId']);
	$profileId = mysql_real_escape_string($jsonResponse['GetBadges']['profileId']);
	//get badges for this user
	if ($userId == $profileId)
	    $uid = $userId;
	else
	    $uid = $profileId;
	$getBadges = "select ba.badge_type_id as badge_id,bdg.public_hint_active,ba.bottel_type,ba.alert_text,ba.date_alert as createdate,bdg.badge_img,bdg.badge_bottle_img,ba.alert_text,ba.venue_id
	from bottel_alert as ba,badges as bdg where ba.mem_id='$uid' and ba.bottel_type=bdg.badge_name group by ba.bottel_type";
	$getBadgesInfo = execute_query($getBadges, true, "select");
	// if (DEBUG){
	writelog("profile.class.php :: GetBadges() :: ", "Starts Here ", false);
	writelog("profile.class.php :: GetBadges() :: ", $getBadgesInfo, true);
	//}

	if (!empty($getBadgesInfo) && is_array($getBadgesInfo) && ($getBadgesInfo['count'] > 0)) {
	    $test = '';
	    foreach ($getBadgesInfo as $kk => $badges) {
		if (is_array($badges)) {
		    if ($badges['bottel_type'] == 'LAST_90_DAY_3_APP') {
			$getAllAmb = execute_query("SELECT venue_id FROM bottel_alert WHERE mem_id='$uid' AND bottel_type='LAST_90_DAY_3_APP'", true, "select");
			$venueName = '';
			$venueId = '';
			foreach ($getAllAmb AS $ii => $ambList) {
			    if (is_array($ambList)) {
				$venueName .= getname($ambList['venue_id']) . ',';
				$venueId .= $ambList['venue_id'] . ',';
			    }
			}
			$venueName1 = rtrim($venueName, ',');
			$venueId1 = rtrim($venueId, ',');
			$test .= "'" . $badges['badge_id'] . "'" . ",";
			$getBadgesInfo[$kk]['badgeEarned'] = 'yes';
			$getBadgesInfo[$kk]['venueId'] = $venueId1;
			$getBadgesInfo[$kk]['badgeUnlockedAtVenue'] = $venueName1;
			$getBadgesInfo[$kk]['badge_description'] = $badges['public_hint_active'];
		    } else {
			$test .= "'" . $badges['badge_id'] . "'" . ",";
			$getBadgesInfo[$kk]['badgeEarned'] = 'yes';
			$getBadgesInfo[$kk]['venueId'] = $badges['venue_id'];
			$getBadgesInfo[$kk]['badgeUnlockedAtVenue'] = getname($badges['venue_id']);
			$getBadgesInfo[$kk]['badge_description'] = $badges['public_hint_active'];
		    }
		}
	    }
	    $test1 = rtrim($test, ',');
	    $getAllBadges = execute_query("select badge_id,badge_name,public_hint_inactive,badge_bw_img,badge_bottle_bw_img from badges where badge_id NOT IN($test1)", TRUE, "select");

	    foreach ($getAllBadges AS $jj => $inactiveBadges) {
		if (is_array($inactiveBadges)) {
		    $getAllBadges[$jj]['alert_text'] = 'no';
		    $getAllBadges[$jj]['createdate'] = '';
		    $getAllBadges[$jj]['badgeEarned'] = 'no';
		    $getAllBadges[$jj]['venueId'] = '';
		    $getAllBadges[$jj]['badgeUnlockedAtVenue'] = '';
		    $getAllBadges[$jj]['badge_description'] = $inactiveBadges['public_hint_inactive'];
		}
	    }
	    $getFinalArray = array_merge($getBadgesInfo, $getAllBadges);
	    $getFinalArray['BadgesEarned'] = $getBadgesInfo['count'];
	    //$getFinalArray['total']=count($getFinalArray);
	    unset($getFinalArray['count']);
	} else {
	    $getFinalArray = execute_query("select badge_id,badge_name,public_hint_inactive,badge_bw_img,badge_bottle_bw_img from badges", TRUE, "select");
	    //$getFinalArray['total']=count($getFinalArray);
	    foreach ($getFinalArray AS $jj => $inactiveBadges) {
		if (is_array($inactiveBadges)) {
		    $getFinalArray[$jj]['alert_text'] = 'no';
		    $getFinalArray[$jj]['createdate'] = '';
		    $getFinalArray[$jj]['badgeEarned'] = 'no';
		    $getFinalArray[$jj]['venueId'] = '';
		    $getFinalArray[$jj]['badgeUnlockedAtVenue'] = '';
		    $getFinalArray[$jj]['badge_description'] = $inactiveBadges['public_hint_inactive'];
		}
	    }
	    $getFinalArray['BadgesEarned'] = '0';
	}
	global $return_codes;
	//date_default_timezone_set("UTC");
	if (!empty($getFinalArray) && (is_array($getFinalArray)) && count($getFinalArray) > 0) {
	    for ($i = 0; $i < 12; $i++) {
		$badgeId = isset($getFinalArray[$i]['badge_id']) && ($getFinalArray[$i]['badge_id']) ? $getFinalArray[$i]['badge_id'] : NULL;
		$badgeDescription = isset($getFinalArray[$i]['badge_description']) && ($getFinalArray[$i]['badge_description']) ? str_replace('"', '\"', $getFinalArray[$i]['badge_description']) : NULL;
		$bottelType = isset($getFinalArray[$i]['bottel_type']) && ($getFinalArray[$i]['bottel_type']) ? str_replace('"', '\"', $getFinalArray[$i]['bottel_type']) : str_replace('"', '\"', $getFinalArray[$i]['badge_name']);
		// $createDate = isset($getFinalArray[$i]['createdate']) && ($getFinalArray[$i]['createdate']) ? date("l M. d,Y H:i A",strtotime($getFinalArray[$i]['createdate'])) : NULL;
		$createDate = isset($getFinalArray[$i]['createdate']) && ($getFinalArray[$i]['createdate']) ? $getFinalArray[$i]['createdate'] : NULL; //date("Y-m-d H:i:s", strtotime($getFinalArray[$i]['createdate']))
		$badgeEarned = isset($getFinalArray[$i]['badgeEarned']) && ($getFinalArray[$i]['badgeEarned']) ? $getFinalArray[$i]['badgeEarned'] : NULL;
		$badgeUnlockedAtVenue = isset($getFinalArray[$i]['badgeUnlockedAtVenue']) && ($getFinalArray[$i]['badgeUnlockedAtVenue']) ? $getFinalArray[$i]['badgeUnlockedAtVenue'] : NULL;
		$badgeThumbImageURL = isset($getFinalArray[$i]['badge_img']) && ($getFinalArray[$i]['badge_img']) ? $getFinalArray[$i]['badge_img'] : $getFinalArray[$i]['badge_bw_img'];
		$badgeImageURL = isset($getFinalArray[$i]['badge_bottle_img']) && ($getFinalArray[$i]['badge_bottle_img']) ? $getFinalArray[$i]['badge_bottle_img'] : $getFinalArray[$i]['badge_bottle_bw_img'];
		$venueId = isset($getFinalArray[$i]['venueId']) && ($getFinalArray[$i]['venueId']) ? $getFinalArray[$i]['venueId'] : NULL;
		if ($bottelType == 'LAST_90_DAY_3_APP')
		    $bottelType = 'Ambassador';
		$str_temp = '{
            "badgeId":"' . str_replace('"', '\"', $badgeId) . '",
            "badgeName":"' . str_replace('"', '\"', $bottelType) . '",
            "badgeDescription":"' . str_replace('"', '\"', trim($badgeDescription)) . '",
            "badgeThumbImageURL":"' . str_replace('"', '\"', ROOT_URL . $badgeThumbImageURL) . '",
            "badgeImageURL":"' . str_replace('"', '\"', ROOT_URL . $badgeImageURL) . '",
            "badgeEarned":"' . str_replace('"', '\"', $badgeEarned) . '",
            "venueId":"' . str_replace('"', '\"', $venueId) . '",
            "badgeUnlockedAtVenue":"' . str_replace('"', '\"', $badgeUnlockedAtVenue) . '",
            "badgeUnlockedAtTime":"' . str_replace('"', '\"', $createDate) . '"
	    }';

		$str .= $str_temp;
		$str .=',';
	    }
	    $str = rtrim($str, ',');
	    $response_str = response_repeat_string();
	    $response_mess = '
               {
		   ' . response_repeat_string() . '
		    "Badges":{
			   "errorCode":"' . $return_codes["GetBadges"]["SuccessCode"] . '",
			   "errorMsg":"' . $return_codes["GetBadges"]["SuccessDesc"] . '",
			   "BadgesEarned":"' . str_replace('"', '\"', $getFinalArray['BadgesEarned']) . '",
			   "TotalBadges":"12",
			   "BadgeList": [' . $str . ']
		   }
	  }';
	} else {
	    $response_mess = '
		    {
		   ' . response_repeat_string() . '
		   "Badges":{
		      "errorCode":"' . $return_codes["GetBadges"]["NoRecordErrorCode"] . '",
		      "errorMsg":"' . $return_codes["GetBadges"]["NoRecordErrorDesc"] . '"
		   }
	  }';
	}
	return getValidJSON($response_mess);
    }

}

?>
