<?php

//include_once 'serversidevalidation.class.php';
//$ob = new Validation();
class Validate {

    function __construct() {
        
    }

    function validate($match, $xmlrequest) {
        $ob = new Validation();
        $counter = false;
        $error = array();
        // $match=trim($match);
        switch ($match) {
            case "UserLogin":
                $email = trim($xmlrequest['UserLogin']['emailId']); //put array variable into isolated php variable explicitly.
                $password = $xmlrequest['UserLogin']['password'];

                if ((isset($email)) || (isset($password))) {

                    if ((!$ob->field_blank_check($email)) && (!$ob->field_blank_check($password))) {
                        $error['blank'] = true;
                        $counter = true;
                    }
//                    $ob->email_check($email);
                    if ((!$ob->email_check($email))) {
                        $error['email'] = true;
                        $counter = true;
                    }
                }
                $error['counter'] = $counter;
                break;

            case "HotPress":
                $userId = trim($xmlrequest['HotPress']['userId']);
                //$bulletinId = trim($xmlrequest['HotPress']['bulletinId']);
                $latestBulletin = trim($xmlrequest['HotPress']['latest']);

                if ((!$ob->digit_check($userId))) {
                    $error['number'] = true;
                    $counter = true;
                }

                if (!$ob->field_blank_check($latestBulletin)) {
                    $error['blank'] = true;
                    $counter = true;
                }
//
                $error['counter'] = $counter;
                break;

			case "profileRegistration":
               $uid = trim($xmlrequest['profileRegistration']['user_id']);
                $fname = isset($xmlrequest['profileRegistration']['fname'])?trim($xmlrequest['profileRegistration']['fname']):NULL;
                $lname = isset($xmlrequest['profileRegistration']['lname'])?trim($xmlrequest['profileRegistration']['lname']):NULL;
                $venue_name = isset($xmlrequest['profileRegistration']['venue_name'])?trim($xmlrequest['profileRegistration']['venue_name']):NULL;
                $security_que = trim($xmlrequest['profileRegistration']['security_que']);
                $security_ans = trim($xmlrequest['profileRegistration']['security_ans']);
                $country = trim($xmlrequest['profileRegistration']['country']);
                $state = trim($xmlrequest['profileRegistration']['state']);
                $city = trim($xmlrequest['profileRegistration']['city']);
                $zip = trim($xmlrequest['profileRegistration']['zip']);

		if ((!$ob->field_blank_check($uid)) || (!$ob->field_blank_check($security_que))
			 || (!$ob->field_blank_check($security_ans)) || (!$ob->field_blank_check($country)) || (!$ob->field_blank_check($state)) || (!$ob->field_blank_check($city)) || (!$ob->field_blank_check($zip))) {
		    $error['blank'] = true;
		    $counter = true;
		}
		
		$error['counter'] = $counter;
		break;	
								
            case "CommentsOnHotPressPost":
                $userId = $xmlrequest['CommentsOnHotPressPost']['userId'];
                $postId = $xmlrequest['CommentsOnHotPressPost']['postId'];
                if ((!$ob->digit_check($userId))) {
                    $error['userid'] = true;
                    $counter = true;
                }

                if ((!$ob->digit_check($postId))) {
                    $error['post'] = true;
                    $counter = true;
                }

                $error['counter'] = $counter;
                break;

            case "HotPressPostComment":
                $fromid = $xmlrequest['HotPressPostComment']['userId'];
                $parentid = $xmlrequest['HotPressPostComment']['postId'];
                $post = $xmlrequest['HotPressPostComment']['commentText'];
                //$visible = $xmlrequest['HotPressPostComment']['displayAsHotPress'];
                if ((!$ob->digit_check($fromid))) {
                    $error['fromid'] = true;
                    $counter = true;
                }
                if ((!$ob->digit_check($parentid))) {
                    $error['parentid'] = true;
                    $counter = true;
                }
                if ((!$ob->field_blank_check($post))) {
                    $error['post'] = true;
                    $counter = true;
                }

                $error['counter'] = $counter;
                break;

            case "DeletePost":
                $userId = trim($xmlrequest['DeletePost']['userId']);
                $authorId = trim($xmlrequest['DeletePost']['authorId']);
                $postId = trim($xmlrequest['DeletePost']['postId']);

                if ((!$ob->digit_check($userId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->digit_check($authorId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->digit_check($postId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                $error['counter'] = $counter;

                break;

            case "FriendRequests":
                $userId = trim($xmlrequest['FriendRequests']['userId']);

                if ((!$ob->digit_check($userId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                $error['counter'] = $counter;
                break;

	    case "FBStates":
                $CountryCode = trim($xmlrequest['FBStates']['countryCode']);

                if ((!$ob->field_blank_check($CountryCode))) {
                    $error['blank'] = true;
                    $counter = true;
                }
                $error['counter'] = $counter;
                break;
		
	    case "FBCities":
                $countryCode = trim($xmlrequest['FBCities']['countryCode']);
                $stateCode = trim($xmlrequest['FBCities']['stateCode']);

                if ((!$ob->field_blank_check($countryCode)) && ((!$ob->field_blank_check($stateCode)))) {
                    $error['blank'] = true;
                    $counter = true;
                }
                $error['counter'] = $counter;
                break;


            case "Photos":
                $userId = trim($xmlrequest['Photos']['userId']);

                if ((!$ob->digit_check($userId))) {
                    $error['number'] = true;
                    $counter = true;
                }

                $error['counter'] = $counter;
                break;

            case "PhotoAlbumDetails":
                $userId = trim($xmlrequest['PhotoAlbumDetails']['userId']);
                $albumId = trim($xmlrequest['PhotoAlbumDetails']['albumId']);

                if ((!$ob->digit_check($userId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->digit_check($albumId))) {
                    $error['number'] = true;
                    $counter = true;
                }

                $error['counter'] = $counter;
                break;

            case "CommentOnPhoto":
                $userId = trim($xmlrequest['CommentOnPhoto']['userId']);
                $albumId = trim($xmlrequest['CommentOnPhoto']['albumId']);
                $photoId = trim($xmlrequest['CommentOnPhoto']['photoId']);
                $comment = trim($xmlrequest['CommentOnPhoto']['comment']);

                if ((!$ob->digit_check($userId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->digit_check($albumId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->digit_check($photoId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->field_blank_check($comment))) {
                    $error['post'] = true;
                    $counter = true;
                }

                $error['counter'] = $counter;

                break;

            case "MakeProfilePhoto":
                $userId = trim($xmlrequest['MakeProfilePhoto']['userId']);
                $albumId = trim($xmlrequest['MakeProfilePhoto']['albumId']);
                $photoId = trim($xmlrequest['MakeProfilePhoto']['photoId']);

                if ((!$ob->digit_check($userId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->digit_check($albumId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->digit_check($photoId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                $error['counter'] = $counter;
                break;

            case "DeletePhoto":
                $userId = trim($xmlrequest['DeletePhoto']['userId']);
                $albumId = trim($xmlrequest['DeletePhoto']['albumId']);
                $photoId = trim($xmlrequest['DeletePhoto']['photoId']);

                if ((!$ob->digit_check($userId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->digit_check($albumId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->digit_check($photoId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                $error['counter'] = $counter;

                break;

            case "FullScreenPhoto":

                $userId = trim($xmlrequest['FullScreenPhoto']['userId']);
                $albumId = trim($xmlrequest['FullScreenPhoto']['albumId']);
                $photoId = trim($xmlrequest['FullScreenPhoto']['photoId']);

                if ((!$ob->digit_check($userId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->digit_check($albumId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->digit_check($photoId))) {
                    $error['number'] = true;
                    $counter = true;
                }

                $error['counter'] = $counter;
                break;


            case "TagPhoto":

                $userId = trim($xmlrequest['TagPhoto']['userId']);
                $albumId = trim($xmlrequest['TagPhoto']['albumId']);
                $photoId = trim($xmlrequest['TagPhoto']['photoId']);


                if ((!$ob->digit_check($userId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->digit_check($albumId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->digit_check($photoId))) {
                    $error['number'] = true;
                    $counter = true;
                }


                $count = count($xmlrequest['TagPhoto']['Tags']);
                for ($i = 0; $i < $count; $i++) {

                    $x1 = trim($xmlrequest['TagPhoto']['Tags'][$i]['x1']);
                    $x2 = trim($xmlrequest['TagPhoto']['Tags'][$i]['x2']);
                    $y1 = trim($xmlrequest['TagPhoto']['Tags'][$i]['y1']);
                    $y2 = trim($xmlrequest['TagPhoto']['Tags'][$i]['y2']);
                    //$width = trim($xmlrequest['TagPhoto']['Tags'][$i]['width']);
                    // $height = trim($xmlrequest['TagPhoto']['Tags'][$i]['height']);
                    $entourageName = trim($xmlrequest['TagPhoto']['Tags'][$i]['entourageName']);

                    if ((!$ob->field_blank_check($x1))) {
                        $error['blank'] = true;
                        $counter = true;
                    }
                    if ((!$ob->field_blank_check($y1))) {
                        $error['blank'] = true;
                        $counter = true;
                    }
                    if ((!$ob->field_blank_check($x2))) {
                        $error['blank'] = true;
                        $counter = true;
                    }
                    if ((!$ob->field_blank_check($y2))) {
                        $error['blank'] = true;
                        $counter = true;
                    }

//                    if ((!$ob->field_blank_check($width))) {
//                        $error['blank'] = true;
//                        $counter = true;
//                    }
//                    if ((!$ob->field_blank_check($height))) {
//                        $error['blank'] = true;
//                        $counter = true;
//                    }
                    if ((!$ob->field_blank_check($entourageName))) {
                        $error['blank'] = true;
                        $counter = true;
                    }
                }

                $error['counter'] = $counter;
                break;

            case "TagsOnPhoto":
                $userId = trim($xmlrequest['TagsOnPhoto']['userId']);
                $albumId = trim($xmlrequest['TagsOnPhoto']['albumId']);
                $photoId = trim($xmlrequest['TagsOnPhoto']['photoId']);


                if ((!$ob->digit_check($userId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->digit_check($albumId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->digit_check($photoId))) {
                    $error['number'] = true;
                    $counter = true;
                }

                $error['counter'] = $counter;
                break;

            case "RemoveTag":
                
                $userId = $xmlrequest['RemoveTag']['userId'];
                $photoId = $xmlrequest['RemoveTag']['photoId'];
//                print_r($userId);
                if ((!$ob->digit_check($userId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->digit_check($photoId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                break;

            case "UserLogout":
                $userId = trim($xmlrequest['UserLogout']['userId']);

                if ((!$ob->digit_check($userId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                $error['counter'] = $counter;
                break;

            case "PhotoUpload":
                $userId = trim($xmlrequest['PhotoUpload']['userId']);
                $userPrivacySetting = trim($xmlrequest['PhotoUpload']['userPrivacySetting']);
                $displayAsHotPress = trim($xmlrequest['PhotoUpload']['displayAsHotPress']);
                $totalImageSize = trim($xmlrequest['PhotoUpload']['totalImageSize']);
                $filename = trim($xmlrequest['PhotoUpload']['filename']);
                $currentChunk = trim($xmlrequest['PhotoUpload']['currentChunk']);
                $totalChunks = trim($xmlrequest['PhotoUpload']['totalChunks']);
                $uploadLocation = trim($xmlrequest['PhotoUpload']['uploadLocation']);
                $entourageId = trim($xmlrequest['PhotoUpload']['entourageId']);

//                if ((!$ob->image_type_check($_FILES['userfile']['name']))) {
//                    $error['match'] = true;
//                    $counter = true;
//                }

                if ((!$ob->digit_check($userId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->digit_check($entourageId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->field_blank_check($uploadLocation))) {
                    $error['blank'] = true;
                    $counter = true;
                }
                if ((!$ob->field_blank_check($userPrivacySetting))) {
                    $error['blank'] = true;
                    $counter = true;
                }
                if ((!$ob->field_blank_check($displayAsHotPress))) {
                    $error['blank'] = true;
                    $counter = true;
                }
                if ((!$ob->field_blank_check($totalImageSize))) {
                    $error['blank'] = true;
                    $counter = true;
                }
                /*  if ((!$ob->field_blank_check($chunkData))) {
                  $error['blank'] = true;
                  $counter = true;
                  } */
                if ((!$ob->field_blank_check($currentChunk))) {
                    $error['blank'] = true;
                    $counter = true;
                }
                if ((!$ob->field_blank_check($totalChunks))) {
                    $error['blank'] = true;
                    $counter = true;
                }


                $error['counter'] = $counter;

                break;

            case "EntourageList":
                $userId = trim($xmlrequest['EntourageList']['userId']);
                $profileType = trim($xmlrequest['EntourageList']['profileType']);


                if ((!$ob->digit_check($userId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->field_blank_check($profileType))) {
                    $error['profile_type'] = true;
                    $counter = true;
                }
                $error['counter'] = $counter;
                break;

            case "AllEntourageListByName":
                $userId = trim($xmlrequest['AllEntourageListByName']['userId']);
                // $typeText = trim($xmlrequest['AllEntourageListByName']['typeText']);


                if ((!$ob->digit_check($userId))) {
                    $error['number'] = true;
                    $counter = true;
                }
//                if ((!$ob->field_blank_check($typeText))) {
//                    $error['typeText'] = true;
//                    $counter = true;
//                }
                $error['counter'] = $counter;
                break;

            case "AllEntourageList":
                $userId = trim($xmlrequest['AllEntourageList']['userId']);
                $entourageId = trim($xmlrequest['AllEntourageList']['entourageId']);


                if ((!$ob->digit_check($userId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->digit_check($entourageId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                $error['counter'] = $counter;

                break;

            case "LikePostList":
                $userId = trim($xmlrequest['LikePostList']['userId']);
                $authorId = trim($xmlrequest['LikePostList']['authorId']);
                $postId = trim($xmlrequest['LikePostList']['postId']);

                if ((!$ob->digit_check($userId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->field_blank_check($authorId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->field_blank_check($postId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                $error['counter'] = $counter;

                break;

            case "LikePost":
                $userId = trim($xmlrequest['LikePost']['userId']);
                $postId = trim($xmlrequest['LikePost']['postId']);

                if ((!$ob->digit_check($userId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->field_blank_check($postId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                $error['counter'] = $counter;
                break;

            case "AddAsFriendRequest":
                $userId = trim($xmlrequest['AddAsFriendRequest']['userId']);
                $friendId = trim($xmlrequest['AddAsFriendRequest']['friendId']);
                $status = trim($xmlrequest['AddAsFriendRequest']['status']);


                if ((!$ob->digit_check($userId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->digit_check($friendId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->field_blank_check($status))) {
                    $error['blank'] = true;
                    $counter = true;
                }

                $error['counter'] = $counter;

                break;

            case"RemoveFriend":
                $userId = trim($xmlrequest['RemoveFriend']['userId']);
                $friendId = trim($xmlrequest['RemoveFriend']['friendId']);

                if ((!$ob->digit_check($userId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->digit_check($friendId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                $error['counter'] = $counter;
                break;

            case"DeletePhotoComment":
                $userId = trim($xmlrequest['DeletePhotoComment']['userId']);
                $commentId = trim($xmlrequest['DeletePhotoComment']['commentId']);

                if ((!$ob->digit_check($userId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->digit_check($commentId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                $error['counter'] = $counter;
                break;

            case "DeleteEventComment":
                $userId = trim($xmlrequest['DeleteEventComment']['userId']);
                $commentId = trim($xmlrequest['DeleteEventComment']['commentId']);

                if ((!$ob->digit_check($userId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->digit_check($commentId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                $error['counter'] = $counter;
                break;

            case "DeleteAppearanceComment":
                $userId = trim($xmlrequest['DeleteAppearanceComment']['userId']);
                $commentId = trim($xmlrequest['DeleteAppearanceComment']['commentId']);

                if ((!$ob->digit_check($userId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->digit_check($commentId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                $error['counter'] = $counter;
                break;

            case"DisplayCommentOnPhoto":
                $userId = trim($xmlrequest['DisplayCommentOnPhoto']['userId']);
                $photoId = trim($xmlrequest['DisplayCommentOnPhoto']['photoId']);

                if ((!$ob->digit_check($userId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->digit_check($photoId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                $error['counter'] = $counter;
                break;

            case "AdvanceSearch":
                $userId = trim($xmlrequest['AdvanceSearch']['userId']);
                $category = trim($xmlrequest['AdvanceSearch']['category']);
                $profileName = trim($xmlrequest['AdvanceSearch']['profileName']);
                $advancedSearch = trim($xmlrequest['AdvanceSearch']['advancedSearch']);
                // $searchType = trim($xmlrequest['AdvanceSearch']['searchType']);
                $fromAge = trim($xmlrequest['AdvanceSearch']['fromAge']);
                $toAge = trim($xmlrequest['AdvanceSearch']['toAge']);
                $searchRadius = trim($xmlrequest['AdvanceSearch']['searchRadius']);
                $gender = trim($xmlrequest['AdvanceSearch']['gender']);

                if ((isset($xmlrequest['AdvanceSearch']['fromAge'])) && (isset($xmlrequest['AdvanceSearch']['toAge'])) && ($fromAge > $toAge) && ($fromAge > 100)) {
                    $error['age_limit'] = true;
                    $counter = true;
                }
                if ((!$ob->digit_check($userId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->digit_check($searchRadius))) {
                    $error['number'] = true;
                    $counter = true;
                }

                if ((!$ob->field_blank_check($advancedSearch))) {
                    $error['blank'] = true;
                    $counter = true;
                }

                $error['counter'] = $counter;

                break;
            case "CreatePhotoAlbum":
                $userId = trim($xmlrequest['CreatePhotoAlbum']['userId']);
                $privacySetting = trim($xmlrequest['CreatePhotoAlbum']['privacySetting']);
                $title = trim($xmlrequest['CreatePhotoAlbum']['title']);


                if ((!$ob->digit_check($userId))) {
                    $error['number'] = true;
                    $counter = true;
                }

                if ((!$ob->field_blank_check($privacySetting))) {
                    $error['blank'] = true;
                    $counter = true;
                }
                if ((!$ob->field_blank_check($title))) {
                    $error['blank'] = true;
                    $counter = true;
                }
                $error['counter'] = $counter;
                break;

            case "UserSignUp":
                $userId = trim($xmlrequest['UserSignUp']['emailId']);
                $password = trim($xmlrequest['UserSignUp']['password']);
                $birthday = trim($xmlrequest['UserSignUp']['birthday']);
                $accttype = trim($xmlrequest['UserSignUp']['accttype']);
                $fb_id = trim($xmlrequest['UserSignUp']['fb_id']);
                $gender = trim($xmlrequest['UserSignUp']['gender']);
                $userName = trim($xmlrequest['UserSignUp']['userName']);
                // $fb_profile_image = trim($xmlrequest['UserSignUp']['fb_profile_image']);

                if ((!$ob->fb_email_check($userId))) {
                    $error['email'] = true;
                    $counter = true;
                }

                if ((!$ob->field_blank_check($password))) {
                    $error['blank'] = true;
                    $counter = true;
                }

                if ((!$ob->digit_check($fb_id))) {
                    $error['number'] = true;
                    $counter = true;
                }


                if ((!$ob->field_blank_check($birthday))) {
                    $error['blank'] = true;
                    $counter = true;
                }
                if ((!$ob->field_blank_check($accttype))) {
                    $error['blank'] = true;
                    $counter = true;
                }
                if ((!$ob->field_blank_check($gender))) {
                    $error['blank'] = true;
                    $counter = true;
                }
                if ((!$ob->alphabet_check($userName))) {
                    $error['character'] = true;
                    $counter = true;
                }
                $error['counter'] = $counter;

                break;

            case"FBVerifyUser":
                $emailId = trim($xmlrequest['FBVerifyUser']['emailId']);
                if ((!$ob->fb_email_check($emailId))) {
                    $error['email'] = true;
                    $counter = true;
                }
                $error['counter'] = $counter;
                break;
				
            case "FbUserVerification":
                $userId = trim($xmlrequest['FbUserVerification']['userId']);
				if ((!$ob->digit_check($userId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                $error['counter'] = $counter;
                break;
				
            case "fbSearchUsers":
                $userId = trim($xmlrequest['fbSearchUsers']['userId']);
				if ((!$ob->digit_check($userId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                $error['counter'] = $counter;
                break;
				
            case "inviteContacts":
                $userId = trim($xmlrequest['inviteContacts']['user_id']);
				if ((!$ob->digit_check($userId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                $error['counter'] = $counter;
                break;
				
            case "ProfileParentComment":
                $userId = trim($xmlrequest['ProfileParentComment']['userId']);
                $postId = trim($xmlrequest['ProfileParentComment']['postId']);
                $latest = trim($xmlrequest['ProfileParentComment']['latest']);

                if ((!$ob->digit_check($userId))) {
                    $error['number'] = true;
                    $counter = true;
                }

                if ((!$ob->digit_check($postId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->digit_check($latest))) {
                    $error['number'] = true;
                    $counter = true;
                }

                $error['counter'] = $counter;
                break;
            case "ProfileSubComments":
                $userId = trim($xmlrequest['ProfileSubComments']['userId']);
                $testimonialId = trim($xmlrequest['ProfileSubComments']['testimonialId']);
                if ((!$ob->digit_check($userId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->digit_check($testimonialId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                $error['counter'] = $counter;
                break;

            case"PostCommentOnProfile":
                $userId = trim($xmlrequest['PostCommentOnProfile']['userId']);
                $profileId = trim($xmlrequest['PostCommentOnProfile']['profileId']);
                $postId = trim($xmlrequest['PostCommentOnProfile']['postId']);
                $displayAsHotPress = trim($xmlrequest['PostCommentOnProfile']['displayAsHotPress']);
                $commentText = trim($xmlrequest['PostCommentOnProfile']['commentText']);
                $publishashotpress = trim($xmlrequest['PostCommentOnProfile']['displayAsHotPress']);
                if ((!$ob->digit_check($userId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->digit_check($profileId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->digit_check($postId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->field_blank_check($commentText))) {
                    $error['blank'] = true;
                    $counter = true;
                }
                if ((!$ob->field_blank_check($displayAsHotPress))) {
                    $error['blank'] = true;
                    $counter = true;
                }
//                 if ((!$ob->field_blank_check($publishashotpress))) {
//                    $error['blank'] = true;
//                    $counter = true;
//                }

                $error['counter'] = $counter;
                break;
            case "Entourage":

                break;

            case "Events":

                $userId = $xmlrequest['Events']['userId'];
                $pageNumber = $xmlrequest['Events']['pageNumber'];
                $eventType = $xmlrequest['Events']['eventType'];
                $latitude = $xmlrequest['Events']['latitude'];
                $longitude = $xmlrequest['Events']['longitude'];
                if (!$ob->digit_check($userId)) {
                    $error['number'] = true;
                    $counter = true;
                }
//                if (!$ob->digit_check($longitude)) {
//                    $error['number'] = true;
//                    $counter = true;
//                }
//                if (!$ob->digit_check($latitude)) {
//                    $error['number'] = true;
//                    $counter = true;
//                }
                if ($xmlrequest['Events']['eventType'] == 'nearby') {
                    if (empty($latitude) || empty($longitude)) {
                        $error['empty'] = true;
                        $counter = true;
                    }
                }
                if ((!$ob->digit_check($pageNumber))) {
                    $error['number'] = true;
                    $counter = true;
                }

                if (!$ob->field_blank_check($eventType)) {
                    $error['blank'] = true;
                    $counter = true;
                }
                $error['counter'] = $counter;

                break;

            case "SearchEvent":
                $userId = $xmlrequest['SearchEvent']['userId'];
                $EventTitle = $xmlrequest['SearchEvent']['searchEventTitle'];
                $EventLocation = $xmlrequest['SearchEvent']['searchEventLocation'];

                if (!$ob->digit_check($userId)) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ($EventTitle == '' && $EventLocation == '') {
                    $error['blank'] = true;
                    $counter = true;
                }
                $error['counter'] = $counter;
                break;



            case "EventDetails":
                $userId = $xmlrequest['EventDetails']['userId'];
                $EventId = $xmlrequest['EventDetails']['eventId'];


                if (!$ob->digit_check($userId)) {
                    $error['number'] = true;
                    $counter = true;
                }

                if (!$ob->digit_check($EventId)) {
                    $error['number'] = true;
                    $counter = true;
                }
                $error['counter'] = $counter;
                break;

            case "EventComments":

                $userId = $xmlrequest['EventComments']['userId'];
                $EventId = $xmlrequest['EventComments']['eventId'];

                if (!$ob->digit_check($userId)) {
                    $error['number'] = true;
                    $counter = false;
                }

                if (!$ob->digit_check($EventId)) {
                    $error['number'] = true;
                    $counter = false;
                }
                $error['counter'] = $counter;
                break;


            case "EventPostComment":
                $userId = trim($xmlrequest['EventPostComment']['userId']);
                $commentText = trim($xmlrequest['EventPostComment']['comment']);
                if (!$ob->digit_check($userId)) {
                    $error['number'] = true;
                    $counter = false;
                }

                if (!$ob->field_blank_check($commentText)) {
                    $error['blank'] = true;
                    $counter = false;
                }
                $error['counter'] = $counter;
                break;

            case "EventReplyComment":

                $userId = $xmlrequest['EventReplyComment']['userId'];
                $commentText = trim($xmlrequest['EventReplyComment']['comment']);
                $eventId = $xmlrequest['EventReplyComment']['eventId'];

                if (!$ob->digit_check($userId)) {
                    $error['number'] = true;
                    $counter = true;
                }

                if (!$ob->digit_check($eventId)) {
                    $error['number'] = true;
                    $counter = true;
                }

                if (!$ob->field_blank_check($commentText)) {
                    $error['blank'] = true;
                    $counter = true;
                }

                $error['counter'] = $counter;
                break;

            case "EventParentChildComment":

                $userId = $xmlrequest['EventParentChildComment']['userId'];
                $eventId = $xmlrequest['EventParentChildComment']['eventId'];
                $commentId = $xmlrequest['EventParentChildComment']['commentId'];

                if (!$ob->digit_check($userId)) {
                    $error['number'] = true;
                    $counter = true;
                }
                if (!$ob->digit_check($commentId)) {
                    $error['number'] = true;
                    $counter = true;
                }
                if (!$ob->digit_check($eventId)) {
                    $error['number'] = true;
                    $counter = true;
                }
                $error['counter'] = $counter;
                break;

            case "EventCommentDelete":

                $userId = trim($xmlrequest['EventCommentDelete']['userId']);
                $commentId = trim($xmlrequest['EventCommentDelete']['commentId']);
                $eventId = trim($xmlrequest['EventCommentDelete']['eventId']);

                if (!$ob->digit_check($userId)) {
                    $error['number'] = true;
                    $counter = true;
                }

                if (!$ob->field_blank_check($commentId)) {
                    $error['blank'] = true;
                    $counter = true;
                }

                if (!$ob->field_blank_check($eventId)) {
                    $error['blank'] = true;
                    $counter = true;
                }
                $error['counter'] = $counter;
                break;

            case "EventViewGuestList":
                $EventId = $xmlrequest['EventViewGuestList']['eventId'];

                if ((!$ob->digit_check($EventId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                $error['counter'] = $counter;
                break;

            case "EventAddGuestList":
                $userId = $xmlrequest['EventAddGuestList']['userId'];
                $noOfGuest = $xmlrequest['EventAddGuestList']['noOfGuest'];
                if ((!$ob->digit_check($userId))) {
                    $error['number'] = true;
                    $counter = true;
                }

                if ((!$ob->digit_check($noOfGuest))) {
                    $error['number'] = true;
                    $counter = true;
                }

                $error['counter'] = $counter;
                break;

            case "Messages":

                $userId = stripslashes($xmlrequest['Messages']['userId']);

                if ((!$ob->digit_check($userId)) && ($counter)) {
                    $error['number'] = true;
                    $counter = true;
                }

                $error['counter'] = $counter;
                //print_r($error['counter']);
                break;

            case "sendMessage":

                $userId = stripslashes($xmlrequest['sendMessage']['userId']);

                if ((!$ob->digit_check($userId)) && ($counter)) {
                    $error['number'] = true;
                    $counter = true;
                }

                $error['counter'] = $counter;

                break;

            case "DeleteMessage":

                $userId = stripslashes($xmlrequest['DeleteMessage']['userId']);

                if ((!$ob->digit_check($userId)) && ($counter)) {
                    $error['number'] = true;
                    $counter = true;
                }

                $error['counter'] = $counter;
                break;

				
            case "getAllMessageList":

                $userId = stripslashes($xmlrequest['getAllMessageList']['userId']);

                if ((!$ob->digit_check($userId)) && ($counter)) {
                    $error['number'] = true;
                    $counter = true;
                }

                $error['counter'] = $counter;
                break;



            case "MessageDetails":

                $userId = stripslashes($xmlrequest['MessageDetails']['userId']);

                if ((!$ob->digit_check($userId)) && ($counter)) {
                    $error['number'] = true;
                    $counter = true;
                }

                $error['counter'] = $counter;
                break;

            case "replyMessage":

                $userId = stripslashes($xmlrequest['replyMessage']['userId']);

                if ((!$ob->digit_check($userId)) && ($counter)) {
                    $error['number'] = true;
                    $counter = true;
                }

                $error['counter'] = $counter;
                break;

            case"profileInfo":
                $userId = trim($xmlrequest['profileInfo']['userId']);
                $entourageId = trim($xmlrequest['profileInfo']['entourageId']);
                if (!$ob->digit_check($userId)) {
                    $error['number'] = true;
                    $counter = true;
                }
                if (!$ob->digit_check($entourageId)) {
                    $error['number'] = true;
                    $counter = true;
                }
                $error['counter'] = $counter;
                break;

            case"BackStageEventList":
                $userId = trim($xmlrequest['BackStageEventList']['userId']);
                $userProfileType = trim($xmlrequest['BackStageEventList']['userProfileType']);
                if (!$ob->digit_check($userId)) {
                    $error['number'] = true;
                    $counter = true;
                }
                if (!$ob->field_blank_check($userProfileType)) {
                    $error['blank'] = true;
                    $counter = true;
                }
                $error['counter'] = $counter;
                break;

            case"eventSharing":
                $userId = trim($xmlrequest['eventSharing']['userId']);
                $eventId = trim($xmlrequest['eventSharing']['eventId']);
                $displayAsHotPress = trim($xmlrequest['eventSharing']['displayAsHotPress']);
                $commentText = trim($xmlrequest['eventSharing']['commentText']);

                if (!$ob->digit_check($userId)) {
                    $error['number'] = true;
                    $counter = true;
                }
                if (!$ob->digit_check($eventId)) {
                    $error['number'] = true;
                    $counter = true;
                }
                if (!$ob->field_blank_check($displayAsHotPress)) {
                    $error['blank'] = true;
                    $counter = true;
                }
//                if (!$ob->field_blank_check($commentText)) {
//                    $error['blank'] = true;
//                    $counter = true;
//                }
                $error['counter'] = $counter;
                break;



            case "Alerts":

                $userId = $xmlrequest['Alerts']['userId'];

                if ((!$ob->digit_check($userId)) && ($counter)) {
                    $error['number'] = true;
                    $counter = true;
                }

                $error['counter'] = $counter;
                break;

            case "AlertsClear":

                $userId = $xmlrequest['AlertsClear']['userId'];

                if ((!$ob->digit_check($userId)) && ($counter)) {
                    $error['number'] = true;
                    $counter = true;
                }

                $error['counter'] = $counter;
                break;

            case "AlertsUpdate":

                $alertsplUpdateId = $xmlrequest['AlertsUpdate']['alertsUpdateId'];
                $alertsplId = $xmlrequest['AlertsUpdate']['alertsplId'];

                if ((!$ob->digit_check($alertsplUpdateId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->digit_check($alertsplId))) {
                    $error['number'] = true;
                    $counter = true;
                }

                $error['counter'] = false;

                break;

            case "Save411":

                break;

            case "AppEntourageList":

                $userId = $xmlrequest['AppEntourageList']['userId'];

                if ((!$ob->digit_check($userId))) {
                    $error['number'] = true;
                    $counter = true;
                }

                $error['counter'] = $counter;
                break;


            case "AppEntourageStatus":

                $checkedInUserId = $xmlrequest['AppEntourageStatus']['checkedInUserId'];
                $venueId = $xmlrequest['AppEntourageStatus']['venueId'];
                $userId = $xmlrequest['AppEntourageStatus']['userId'];
                $announceId = $xmlrequest['AppEntourageStatus']['announceId'];

                if ((!$ob->digit_check($checkedInUserId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->digit_check($venueId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->digit_check($userId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->digit_check($announceId))) {
                    $error['number'] = true;
                    $counter = true;
                }

                $error['counter'] = $counter;
                break;
				
 case "profileRegistration":
               $uid = trim($xmlrequest['profileRegistration']['user_id']);
                $fname = isset($xmlrequest['profileRegistration']['fname'])?trim($xmlrequest['profileRegistration']['fname']):NULL;
                $lname = isset($xmlrequest['profileRegistration']['lname'])?trim($xmlrequest['profileRegistration']['lname']):NULL;
                $venue_name = isset($xmlrequest['profileRegistration']['venue_name'])?trim($xmlrequest['profileRegistration']['venue_name']):NULL;
                $security_que = trim($xmlrequest['profileRegistration']['security_que']);
                $security_ans = trim($xmlrequest['profileRegistration']['security_ans']);
                $country = trim($xmlrequest['profileRegistration']['country']);
                $state = trim($xmlrequest['profileRegistration']['state']);
                $city = trim($xmlrequest['profileRegistration']['city']);
                $zip = trim($xmlrequest['profileRegistration']['zip']);

		if ((!$ob->field_blank_check($uid)) || (!$ob->field_blank_check($security_que))
			 || (!$ob->field_blank_check($security_ans)) || (!$ob->field_blank_check($country)) || (!$ob->field_blank_check($state)) || (!$ob->field_blank_check($city)) || (!$ob->field_blank_check($zip))) {
		    $error['blank'] = true;
		    $counter = true;
		}
		
		$error['counter'] = $counter;
		break;	
		
            case "AppEntStatusComment":

                $userId = $xmlrequest['AppEntStatusComment']['userId'];
                $checkedInUserId = $xmlrequest['AppEntStatusComment']['checkedInUserId'];
                $announceId = $xmlrequest['AppEntStatusComment']['id'];

                if ((!$ob->digit_check($userId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->digit_check($checkedInUserId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->digit_check($announceId))) {
                    $error['number'] = true;
                    $counter = true;
                }

                $error['counter'] = $counter;
                break;

            case "AppearanceVenueList":

                $userId = $xmlrequest['AppearanceVenueList']['userId'];
                $latitude = $xmlrequest['AppearanceVenueList']['latitude'];
                $longitude = $xmlrequest['AppearanceVenueList']['longitude'];

                if ((!$ob->digit_check($userId))) {
                    $error['number'] = true;
                    $counter = true;
                }
//                if ((!$ob->digit_check($latitude))) {
//                    $error['number'] = true;
//                    $counter = true;
//                }
//                if ((!$ob->digit_check($longitude))) {
//                    $error['number'] = true;
//                    $counter = true;
//                }

                $error['counter'] = $counter;
                break;

            case "AppReward":

                $userId = $xmlrequest['AppReward']['userId'];
                $venueId = $xmlrequest['AppReward']['venueId'];

                if ((!$ob->digit_check($userId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->digit_check($venueId))) {
                    $error['number'] = true;
                    $counter = true;
                }

                $error['counter'] = $counter;
                break;

            case "AppVenueDetail":

                $venueId = $xmlrequest['AppVenueDetail']['venueId'];
                $latitude = $xmlrequest['AppVenueDetail']['latitude'];
                $longitude = $xmlrequest['AppVenueDetail']['longitude'];

                if ((!$ob->digit_check($venueId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                /* if ((!$ob->digit_check($latitude))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->digit_check($longitude))) {
                    $error['number'] = true;
                    $counter = true;
                } */

                $error['counter'] = $counter;

                break;

            case "AppearanceVenueList":

                $userId = $xmlrequest['AppearanceVenueList']['userId'];
                $latitude = $xmlrequest['AppearanceVenueList']['latitude'];
                $longitude = $xmlrequest['AppearanceVenueList']['longitude'];

                if ((!$ob->digit_check($userId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->digit_check($latitude))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->digit_check($longitude))) {
                    $error['number'] = true;
                    $counter = true;
                }

                $error['counter'] = $counter;
                break;


            case "AnnounceArrival":

                //$venueId = $xmlrequest['AnnounceArrival']['venueId'];
                $userId = $xmlrequest['AnnounceArrival']['userId'];
                writelog("Validate:validate():", "AnnounceArrival", false);

                if (isset($_FILES['userfile']['name']) && ($_FILES['userfile']['name']) && (!$ob->image_type_check($_FILES['userfile']['name']))) {
                    $error['match'] = true;
                    $counter = true;
                }
                if (isset($_FILES['userfile']['name']) && ($_FILES['userfile']['name'] != '')) {
                    if (($_FILES['userfile']['tmp_name'] == '')) {
                        $error['validImage'] = true;
                        $counter = true;
                    }
                }
//                if ((!$ob->digit_check($venueId))) {
//                    $error['number'] = true;
//                    $counter = true;
//                }
                if ((!$ob->digit_check($userId))) {
                    $error['number'] = true;
                    $counter = true;
                }

                $error['counter'] = $counter;
                break;

            case "CurrentVenueStatus":


                $venueId = $xmlrequest['CurrentVenueStatus']['venueId'];
                $userId = $xmlrequest['CurrentVenueStatus']['userId'];

                if ((!$ob->digit_check($venueId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->digit_check($userId))) {
                    $error['number'] = true;
                    $counter = true;
                }

                $error['counter'] = $counter;
                break;



            case "Save411":

                break;

            case "Messages":

                break;

            case "DeleteMessage":

                break;

            case "MessageDetails":

                break;

            case "SendMessage":

                break;

            case "Profile":

                $userId = trim($xmlrequest['Profile']['userId']);
                $entourageId = trim($xmlrequest['Profile']['entourageId']);
//                $latestBulletin = trim($xmlrequest['Profile']['latest']);
//                if ((!$ob->field_blank_check($latestBulletin))) {
//                    $error['blank'] = true;
//                    $counter = true;
//                }
                if ((!$ob->digit_check($userId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->digit_check($entourageId))) {
                    $error['number'] = true;
                    $counter = true;
                }
// else {
//                    unset($error['number']);
//                }

                $error['counter'] = $counter;
                break;

            case "DeleteProfileMessage":
                $userId = trim($xmlrequest['DeleteProfileMessage']['userId']);
                $postId = trim($xmlrequest['DeleteProfileMessage']['postId']);

                if ((!$ob->digit_check($userId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->digit_check($postId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                $error['counter'] = $counter;
                break;

            case "BSEViewGuestList":
                $userId = trim($xmlrequest['BSEViewGuestList']['userId']);
                $eventId = trim($xmlrequest['BSEViewGuestList']['eventId']);

                if ((!$ob->digit_check($userId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->digit_check($eventId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                $error['counter'] = $counter;
                break;

            case "BSEViewNonMemGuestList":
                $userId = trim($xmlrequest['BSEViewNonMemGuestList']['userId']);
                $eventId = trim($xmlrequest['BSEViewNonMemGuestList']['eventId']);

                if ((!$ob->digit_check($userId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->digit_check($eventId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                $error['counter'] = $counter;
                break;
            case "BSEGLEntourageSearch":
                $userId = trim($xmlrequest['BSEGLEntourageSearch']['userId']);
                $eventId = trim($xmlrequest['BSEGLEntourageSearch']['eventId']);

                if ((!$ob->digit_check($userId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->digit_check($eventId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                $error['counter'] = $counter;
                break;


            case "BSENonMemGLEntourageSearch":
                $userId = trim($xmlrequest['BSENonMemGLEntourageSearch']['userId']);
                $eventId = trim($xmlrequest['BSENonMemGLEntourageSearch']['eventId']);

                if ((!$ob->digit_check($userId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->digit_check($eventId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                $error['counter'] = $counter;
                break;


            case "BSEGLCheckIn":
                $userId = trim($xmlrequest['BSEGLCheckIn']['userId']);
                $rsvpId = trim($xmlrequest['BSEGLCheckIn']['rsvpId']);
                $eventId = trim($xmlrequest['BSEGLCheckIn']['eventId']);
                $host = trim($xmlrequest['BSEGLCheckIn']['host']);
                $entourageCount = trim($xmlrequest['BSEGLCheckIn']['entourageCount']);

                if ((!$ob->digit_check($userId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->digit_check($entourageCount))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->digit_check($eventId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if (!$ob->digit_check($rsvpId)) {
                    $error['number'] = true;
                    $counter = true;
                }
                if (!$ob->field_blank_check($host)) {
                    $error['blank'] = true;
                    $counter = true;
                }
                $error['counter'] = $counter;
                break;

            case "BSENonMemGLCheckIn":
                $non_mem_gl_id = trim($xmlrequest['BSENonMemGLCheckIn']['non_mem_gl_id']);
                $eventId = trim($xmlrequest['BSENonMemGLCheckIn']['eventId']);
                $host = trim($xmlrequest['BSENonMemGLCheckIn']['host']);
                $entourageCount = trim($xmlrequest['BSENonMemGLCheckIn']['entourageCount']);

                if (!$ob->digit_check($non_mem_gl_id)) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->digit_check($entourageCount))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->digit_check($eventId))) {
                    $error['number'] = true;
                    $counter = true;
                }

                if (!$ob->field_blank_check($host)) {
                    $error['blank'] = true;
                    $counter = true;
                }
                $error['counter'] = $counter;
                break;

            case "DisplayPhotoTagAlert":
                $userId = trim($xmlrequest['DisplayPhotoTagAlert']['userId']);
                $alertId = trim($xmlrequest['DisplayPhotoTagAlert']['alertId']);
                $photoId = trim($xmlrequest['DisplayPhotoTagAlert']['photoId']);

                if ((!$ob->digit_check($userId))) {
                    $error['number'] = true;
                    $counter = true;
                }

                if ((!$ob->digit_check($alertId))) {
                    $error['number'] = true;
                    $counter = true;
                }

                if ((!$ob->digit_check($photoId))) {
                    $error['number'] = true;
                    $counter = true;
                }

                $error['counter'] = $counter;
                break;

            case "RespondPhotoTagAlerts":
                $userId = trim($xmlrequest['RespondPhotoTagAlerts']['userId']);
                $alertId = trim($xmlrequest['RespondPhotoTagAlerts']['alertId']);
                $photoId = trim($xmlrequest['RespondPhotoTagAlerts']['photoId']);
                $status = trim($xmlrequest['RespondPhotoTagAlerts']['status']);
                $taggedEntourageId = trim($xmlrequest['RespondPhotoTagAlerts']['taggedEntourageId']);

                if ((!$ob->digit_check($userId))) {
                    $error['number'] = true;
                    $counter = true;
                }

                if ((!$ob->digit_check($alertId))) {
                    $error['number'] = true;
                    $counter = true;
                }

                if ((!$ob->digit_check($photoId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->digit_check($status))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->digit_check($taggedEntourageId))) {
                    $error['number'] = true;
                    $counter = true;
                }


                $error['counter'] = $counter;
                break;
            case "CommentsOnPhotosParentComment":
                $userId = trim($xmlrequest['CommentsOnPhotosParentComment']['userId']);
                $postId = trim($xmlrequest['CommentsOnPhotosParentComment']['postId']);

                if ((!$ob->digit_check($userId))) {
                    $error['number'] = true;
                    $counter = true;
                }

                if ((!$ob->digit_check($postId))) {
                    $error['number'] = true;
                    $counter = true;
                }

                $error['counter'] = $counter;
                break;

            case "BSEViewTblReservationList":
                $userId = trim($xmlrequest['BSEViewTblReservationList']['userId']);
                $eventId = trim($xmlrequest['BSEViewTblReservationList']['eventId']);

                if ((!$ob->digit_check($userId))) {
                    $error['number'] = true;
                    $counter = true;
                }

                if ((!$ob->digit_check($eventId))) {
                    $error['number'] = true;
                    $counter = true;
                }

                $error['counter'] = $counter;
                break;

            case "BSETRCheckInNotes":
                $userId = trim($xmlrequest['BSETRCheckInNotes']['userId']);
                $event_Id = trim($xmlrequest['BSETRCheckInNotes']['event_Id']);
                $table_no = trim($xmlrequest['BSETRCheckInNotes']['table_no']);

                if ((!$ob->digit_check($userId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->digit_check($event_Id))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->digit_check($table_no))) {
                    $error['number'] = true;
                    $counter = true;
                }
                $error['counter'] = $counter;
                break;

            case "GetBadges":
                $userId = trim($xmlrequest['GetBadges']['userId']);
                $profileId = trim($xmlrequest['GetBadges']['profileId']);
                
                if ((!$ob->digit_check($userId)) && ((!$ob->digit_check($profileId)))) {
                    $error['number'] = true;
                    $counter = true;
                }
                $error['counter'] = $counter;
                break;
				
			 case "BadgeDetails":
                $userId = trim($xmlrequest['BadgeDetails']['userId']);
                $badgeId = trim($xmlrequest['BadgeDetails']['badgeId']);
                
                if ((!$ob->digit_check($userId)) && ((!$ob->digit_check($badgeId)))) {
                    $error['number'] = true;
                    $counter = true;
                }
                $error['counter'] = $counter;
                break;	

            case"BSETRViewCheckIn":
                $userId = trim($xmlrequest['BSETRViewCheckIn']['userId']);
                $eventId = trim($xmlrequest['BSETRViewCheckIn']['eventId']);
                $table_no = trim($xmlrequest['BSETRViewCheckIn']['table_no']);

                if ((!$ob->digit_check($userId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->digit_check($eventId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->digit_check($table_no))) {
                    $error['number'] = true;
                    $counter = true;
                }

                $error['counter'] = $counter;
                break;

            case"BSETRConfirmMessageScreen":
                $userId = trim($xmlrequest['BSETRConfirmMessageScreen']['userId']);
                $eventId = trim($xmlrequest['BSETRConfirmMessageScreen']['eventId']);
                $table_no = trim($xmlrequest['BSETRConfirmMessageScreen']['tableListInfo']['table_no']);
                //$recordId = trim($xmlrequest['BSETRConfirmMessageScreen']['tableListInfo']['recordId']);
                $entourageCount = trim($xmlrequest['BSETRConfirmMessageScreen']['tableListInfo']['entourageCount']);
                // $checkedInStatus = trim($xmlrequest['BSETRConfirmMessageScreen']['tableListInfo']['checkedInStatus']);
                $host = trim($xmlrequest['BSETRConfirmMessageScreen']['tableListInfo']['host']);
                $bottle_server = trim($xmlrequest['BSETRConfirmMessageScreen']['tableListInfo']['bottle_server']);
                $bottle_minimum = trim($xmlrequest['BSETRConfirmMessageScreen']['tableListInfo']['bottle_minimum']);
                $minimum_spend = trim($xmlrequest['BSETRConfirmMessageScreen']['tableListInfo']['minimum_spend']);

                if ((!$ob->digit_check($userId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->digit_check($eventId))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->digit_check($table_no))) {
                    $error['number'] = true;
                    $counter = true;
                }
//                if ((!$ob->digit_check($recordId))) {
//                    $error['number'] = true;
//                    $counter = true;
//                }
                if ((!$ob->digit_check($entourageCount))) {
                    $error['number'] = true;
                    $counter = true;
                }
//                if ((!$ob->field_blank_check($checkedInStatus))) {
//                    $error['blank'] = true;
//                    $counter = true;
//                }
                if ((!$ob->field_blank_check($host))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->field_blank_check($bottle_server))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->digit_check($bottle_minimum))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->digit_check($minimum_spend))) {
                    $error['number'] = true;
                    $counter = true;
                }
                $error['counter'] = $counter;
                break;

            case "TakeOverProfile":
                $profilenam = mysql_real_escape_string($xmlrequest['TakeOverProfile']['profileName']);
                $zip = mysql_real_escape_string($xmlrequest['TakeOverProfile']['zip']);
                $country = mysql_real_escape_string($xmlrequest['TakeOverProfile']['country']);
                $photo = mysql_real_escape_string($xmlrequest['TakeOverProfile']['photo']);
                $state = mysql_real_escape_string($xmlrequest['TakeOverProfile']['state']);
                $city = mysql_real_escape_string($xmlrequest['TakeOverProfile']['city']);
                $longitude = mysql_real_escape_string($xmlrequest['TakeOverProfile']['longitude']);
                $latitude = mysql_real_escape_string($xmlrequest['TakeOverProfile']['latitude']);
                $interests = mysql_real_escape_string($xmlrequest['TakeOverProfile']['interests']);
                $gender = mysql_real_escape_string($xmlrequest['TakeOverProfile']['gender']);
                $uniqueId = mysql_real_escape_string($xmlrequest['TakeOverProfile']['uniqueId']);

                if ((!$ob->field_blank_check($zip))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->field_blank_check($longitude))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->field_blank_check($latitude))) {
                    $error['number'] = true;
                    $counter = true;
                }
                if ((!$ob->field_blank_check($uniqueId))) {
                    $error['number'] = true;
                    $counter = true;
                }

                if (!$ob->field_blank_check($gender)) {
                    $error['blank'] = true;
                    $counter = true;
                }
                if (!$ob->field_blank_check($country)) {
                    $error['blank'] = true;
                    $counter = true;
                }
                if (!$ob->field_blank_check($state)) {
                    $error['blank'] = true;
                    $counter = true;
                }
                if (!$ob->field_blank_check($city)) {
                    $error['blank'] = true;
                    $counter = true;
                }
                if (!$ob->field_blank_check($profilenam)) {
                    $error['blank'] = true;
                    $counter = true;
                }
                $error['counter'] = $counter;
                break;
        }
        writelog("Validate:validate():", $error, true);

        return $error;
    }

}

?>
