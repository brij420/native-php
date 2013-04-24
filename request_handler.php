<?php

$error = array();

switch ($requesttype) {
    case"GetBadges":
        require_once ('classes/profile.class.php');
        $error['successful'] = true;
        $obj_response = new Profile();
        $response = response_string($obj_response, 'getBadges', $error, $xmlrequest, $requesttype);
        break;
		
    case"BadgeDetails":
	    require_once ('classes/profile.class.php');
        $error['successful'] = true;
        $obj_badges_detail = new Profile();
        $response = response_string($obj_badges_detail, 'badgeDetailsInfo', $error, $xmlrequest, $requesttype);
        break;
	
	
    case "UserLogin":
        require_once('classes/login.class.php');
        $obj_login = new Login();
        $error = $obj_login->login_check($xmlrequest);
		
        $response = response_string($obj_login, 'userlogin', $error, $xmlrequest, $requesttype);
        break;
		
    case "profileRegistration":
		require_once('classes/entourage.class.php');
		$obj_pro_reg = new Entourage();
		$error['successful'] = true;
		$response = response_string($obj_pro_reg, 'userProfileUpdate', $error, $xmlrequest, $requesttype);
		break;
	
    case "HotPress":
        require_once('classes/hotpress.class.php');
        $obj_hotpress = new HotPress();
        $error = $obj_hotpress->hotpress_valid($xmlrequest);
        $response = response_string($obj_hotpress, 'hotpressPost', $error, $xmlrequest, $requesttype);
		//writelog("index.php :: sent  response :: ", $xmlrequest, false,0,1);
        break;

    case "CommentsOnHotPressPost":
        require_once('classes/hotpress.class.php');
        $obj_comment = new HotPress();
        $error = $obj_comment->comment_on_hotpress_post_valid($xmlrequest);
        $response = response_string($obj_comment, 'commentsOnHotPressPost', $error, $xmlrequest, $requesttype);
        break;

    case "HotPressPostComment":
        require_once('classes/hotpress.class.php');
        $obj_postComment = new HotPress();
        $error = $obj_postComment->hot_press_post_comment_valid($xmlrequest); //print_r($error);die();
        $response = response_string($obj_postComment, 'hotPressPostComment', $error, $xmlrequest, $requesttype);
        break;

    case "DeletePost":
        require_once('classes/hotpress.class.php');
        $obj_hotpress = new HotPress();
        $error = $obj_hotpress->delete_post_valid($xmlrequest);
        $response = response_string($obj_hotpress, 'deleteHotpressPost', $error, $xmlrequest, $requesttype);
        break;

    case "FriendRequests":
        require_once('classes/entourage.class.php');
        $obj_friendreq = new Entourage();
        $error = $obj_friendreq->friend_request_valid($xmlrequest);
        $response = response_string($obj_friendreq, 'friendRequests', $error, $xmlrequest, $requesttype);
        break;

    case "Profile":
        require_once('classes/profile.class.php');
        $obj_profile = new Profile();
        $error = $obj_profile->profile_valid($xmlrequest);
        $response = response_string($obj_profile, 'profileInfo', $error, $xmlrequest, $requesttype);
        break;

    case "Photos":
        require_once('classes/photos.class.php');
        $obj_photos = new AlbumPhotos();
        $error = $obj_photos->profile_photo_valid($xmlrequest);
        $response = response_string($obj_photos, 'photos', $error, $xmlrequest, $requesttype);
        break;

    case "PhotoAlbumDetails":
        require_once('classes/photos.class.php');
        $obj_album_details = new AlbumPhotos();
        $error = $obj_album_details->profile_album_details_valid($xmlrequest);
        $response = response_string($obj_album_details, 'photoAlbumDetails', $error, $xmlrequest, $requesttype);
        break;

    case "CommentOnPhoto":
        require_once('classes/photos.class.php');
        $obj_comment_on_photo = new AlbumPhotos();
        $error = $obj_comment_on_photo->comment_on_photo_valid($xmlrequest);
        $response = response_string($obj_comment_on_photo, 'commentOnPhoto', $error, $xmlrequest, $requesttype);
        break;

    case "MakeProfilePhoto":
        require_once('classes/photos.class.php');
        $obj_make_profile_photo = new AlbumPhotos();
        $error = $obj_make_profile_photo->make_profile_photo_valid($xmlrequest);
        $response = response_string($obj_make_profile_photo, 'makeProfilePhoto', $error, $xmlrequest, $requesttype);
        break;

    case "DeletePhoto":
        require_once('classes/photos.class.php');
        $obj_delete_photo = new AlbumPhotos();
        $error = $obj_delete_photo->delete_photo_valid($xmlrequest);
        $response = response_string($obj_delete_photo, 'deletePhoto', $error, $xmlrequest, $requesttype);
        break;

    case "PhotoUpload":
        $match = trim($xmlrequest['PhotoUpload']['uploadLocation']);

        $error = array();
        switch ($match) {
            case "Profile":
                require_once('classes/profile.class.php');
                $obj_photo_upload = new Profile();
                $error = $obj_photo_upload->profile_photo_upload_valid($xmlrequest);
                $response = response_string(false, 'photoUpload', $error, $xmlrequest, $requesttype);
                break;

            case "Albums":
                require_once('classes/photos.class.php');
                $obj_photo_upload = new AlbumPhotos();
                $error = $obj_photo_upload->album_photo_upload_valid($xmlrequest);
                $response = response_string(false, 'photoUpload', $error, $xmlrequest, $requesttype);
                break;

            case "Hotpress":
                require_once('classes/hotpress.class.php');
                $obj_photo_upload = new HotPress();
                $error = $obj_photo_upload->hotpress_photo_upload_valid($xmlrequest);
                $response = response_string(false, 'photoUpload', $error, $xmlrequest, $requesttype);
                break;

            case "Events":
                require_once('classes/event.class.php');
                $obj_photo_upload = new Events();
                $error = $obj_photo_upload->event_photo_upload_valid($xmlrequest);
                $response = response_string(false, 'photoUpload', $error, $xmlrequest, $requesttype);
                break;
            case "Appearance":
                require_once('classes/appearance.class.php');
                $obj_photo_upload = new Appearance();
                $error = $obj_photo_upload->appearance_photo_upload_valid($xmlrequest);
                $response = response_string(false, 'photoUpload', $error, $xmlrequest, $requesttype);
                break;
			case "ProfilePhoto":
				require_once('classes/entourage.class.php');
				$obj_photo_upload = new Entourage();
				if (method_exists($obj_photo_upload,'profile_upload_photo')) {
					$error = $obj_photo_upload->profile_upload_photo($xmlrequest);
				}
				$response = response_string(false, 'photoUpload', $error, $xmlrequest, $requesttype);
				break;				
        }

        break;

    case "TagPhoto":
        require_once('classes/photos.class.php');
        $obj_tag_photo = new AlbumPhotos();
        $error = $obj_tag_photo->tag_photo_valid($xmlrequest);
        $response = response_string($obj_tag_photo, 'tagPhoto', $error, $xmlrequest, $requesttype);
        break;

    case "TagsOnPhoto":
        require_once('classes/photos.class.php');
        $obj_tags_on_photo = new AlbumPhotos();
        $error = $obj_tags_on_photo->tag_photo_valid($xmlrequest);
        $response = response_string($obj_tags_on_photo, 'tagsOnPhoto', $error, $xmlrequest, $requesttype);
        break;

    case "RemoveTag":
        require_once ('classes/photos.class.php');
        $obj_event = new AlbumPhotos();
        $error['successful'] = true;
        $response = response_string($obj_event, 'removeTagsFromPhotos', $error, $xmlrequest, $requesttype);
        break;

    case "EntourageList":
        require_once('classes/entourage.class.php');
        $obj_entourage_list = new Entourage();
        $error = $obj_entourage_list->entourage_list_valid($xmlrequest);
        $response = response_string($obj_entourage_list, 'entourageList', $error, $xmlrequest, $requesttype);
        break;

    case "LikePostList":
        require_once('classes/hotpress.class.php');
        $obj_like_post = new HotPress();
        $error = $obj_like_post->like_post_list_valid($xmlrequest); //print_r($error);die();
        $response = response_string($obj_like_post, 'likePostList', $error, $xmlrequest, $requesttype);
        break;

    case "LikePost":
        require_once('classes/hotpress.class.php');
        $obj_like_post = new HotPress();
        $error = $obj_like_post->like_post_list_valid($xmlrequest); //print_r($error);die();
        $response = response_string($obj_like_post, 'likePost', $error, $xmlrequest, $requesttype);
        break;

    case "AllEntourageList":
        require_once('classes/entourage.class.php');
        $obj_mutual_entourage_list = new Entourage();
        $error = $obj_mutual_entourage_list->mutual_entourage_list_valid($xmlrequest);
        $response = response_string($obj_mutual_entourage_list, 'entourageMutualList', $error, $xmlrequest, $requesttype);
        break;

    case "RemoveFriend":
        require_once('classes/entourage.class.php');
        $obj_remove_friend = new Entourage();
        $error['successful'] = true;
        $response = response_string($obj_remove_friend, 'removeFriend', $error, $xmlrequest, $requesttype);
        break;

    case "TakeOverProfile":
        require_once('classes/profile.class.php');
        $obj_response = new Profile();
        $error['successful'] = true;
        $response = response_string($obj_response, 'takeOverProfile', $error, $xmlrequest, $requesttype);
        break;

    case "AddAsFriendRequest":
        require_once('classes/entourage.class.php');
        $obj_add_friend = new Entourage();
        $error = $obj_add_friend->add_friend_request_valid($xmlrequest);
        $response = response_string($obj_add_friend, 'addFriendRequest', $error, $xmlrequest, $requesttype);
        break;

    case"DisplayCommentOnPhoto":
        require_once('classes/photos.class.php');
        $obj_display_comment = new AlbumPhotos();
        $error = $obj_display_comment->display_comment_on_photo_valid($xmlrequest);
        $response = response_string($obj_display_comment, 'displayCommentOnPhoto', $error, $xmlrequest, $requesttype);
        break;

    case "FullScreenPhoto":
        require_once('classes/photos.class.php');
        $obj_full_screen_photo = new AlbumPhotos();
        $error = $obj_full_screen_photo->full_screen_photo_valid($xmlrequest);
        $response = response_string($obj_full_screen_photo, 'fullScreenPhoto', $error, $xmlrequest, $requesttype);
        break;

    case "AdvanceSearch":
        require_once('classes/entourage.class.php');
        $obj_advance_search = new Entourage();
        $error = $obj_advance_search->advance_search_valid($xmlrequest);
        $response = response_string($obj_advance_search, 'advanceSearch', $error, $xmlrequest, $requesttype);
        break;

    case "CreatePhotoAlbum":
        require_once('classes/photos.class.php');
        $obj_create_album = new AlbumPhotos();
        $error = $obj_create_album->create_photo_album_valid($xmlrequest);
        $response = response_string($obj_create_album, 'createAlbum', $error, $xmlrequest, $requesttype);
        break;

    case "UserSignUp":
        require_once('classes/facebook_register.class.php');
        $obj_facebook_signups = new FacebookConnect();
        $error = $obj_facebook_signups->valid_fields($xmlrequest);
        $response = response_string($obj_facebook_signups, 'userSignUp', $error, $xmlrequest, $requesttype);
        break;

    case"FBVerifyUser":
        require_once('classes/facebook_register.class.php');
        $obj_facebook_varify = new FacebookConnect();
        $error = $obj_facebook_varify->valid_fields($xmlrequest);
        $response = response_string($obj_facebook_varify, 'fbVerifyUser', $error, $xmlrequest, $requesttype);
        break;
		
case"FbUserVerification":	
        require_once('classes/facebook_register.class.php');
        $obj_facebook_user_varify = new FacebookConnect();
        $error['successful'] = true;
        $response = response_string($obj_facebook_user_varify, 'FbUserVerify', $error, $xmlrequest, $requesttype);
	break;
	
    case"fbSearchUsers":
	require_once('classes/facebook_register.class.php');
	$obj_facebook_user_search = new FacebookConnect();
	$error['successful'] = true;
	$response = response_string($obj_facebook_user_search, 'fbSearchUserListing', $error, $xmlrequest, $requesttype);
	break;	
    
    case"inviteContacts":
        require_once('classes/facebook_register.class.php');
        $obj_invite_contacts = new FacebookConnect();
        $error['successful'] = true;
        $response = response_string($obj_invite_contacts, 'friendsContactInvitation', $error, $xmlrequest, $requesttype);
	break;
	
    case"FBStates":
	
        require_once('classes/facebook_register.class.php');
        $obj_fblocation = new FacebookConnect();
        $error['successful'] = true;
        $response = response_string($obj_fblocation, 'fbStateList', $error, $xmlrequest, $requesttype);
        break;
    
    case"FBCities":
        require_once('classes/facebook_register.class.php');
        $obj_fbcity = new FacebookConnect();
        $error['successful'] = true;
        $response = response_string($obj_fbcity, 'FBCityList', $error, $xmlrequest, $requesttype);
        break;


    case "ProfileParentComment":
        require_once('classes/profile.class.php');
        $obj_profile_parent_comment = new Profile();
        $error = $obj_profile_parent_comment->profile_showall_comments_valid($xmlrequest);
        $response = response_string($obj_profile_parent_comment, 'profileParentComment', $error, $xmlrequest, $requesttype);

        break;

    case "ProfileSubComments":
        require_once('classes/profile.class.php');
        $obj_profile_sub_comment = new Profile();
        $error = $obj_profile_sub_comment->profile_sub_comments_valid($xmlrequest);
        $response = response_string($obj_profile_sub_comment, 'profileSubComments', $error, $xmlrequest, $requesttype);
        break;

    case"PostCommentOnProfile":
        require_once('classes/profile.class.php');
        $obj_profile_post_comment = new Profile();
        $error = $obj_profile_post_comment->post_comment_on_profile_valid($xmlrequest);
        $response = response_string($obj_profile_post_comment, 'postCommentOnProfile', $error, $xmlrequest, $requesttype);
        break;

    case "DeleteProfileMessage":
        require_once('classes/profile.class.php');
        $obj_profile_delete_comment = new Profile();
        $error = $obj_profile_delete_comment->delete_post_from_profile_valid($xmlrequest);
        $response = response_string($obj_profile_delete_comment, 'deleteProfileMessage', $error, $xmlrequest, $requesttype);
        break;

    case "profileInfo":
        require_once('classes/profile.class.php');
        $obj_profile = new Profile();
        $error = $obj_profile->profile_valid($xmlrequest);
        $response = response_string($obj_profile, 'profileInfoShortDesc', $error, $xmlrequest, $requesttype);
        break;
    case "Events":
        require_once ('classes/event.class.php');
        $obj_event = new Events();
        $error['successful'] = true;
        $response = response_string($obj_event, 'eventList', $error, $xmlrequest, $requesttype);
        break;

    case "SearchEvent":
        require_once ('classes/event.class.php');
        $obj_event = new Events();
        $error['successful'] = true;
        $response = response_string($obj_event, 'searchEvent', $error, $xmlrequest, $requesttype);
        break;

    case "EventDetails":
        require_once ('classes/event.class.php');
        $obj_event = new Events();
        $error['successful'] = true;
        $response = response_string($obj_event, 'eventDetails', $error, $xmlrequest, $requesttype);
        break;
    case "EventCommentList":
        require_once ('classes/event.class.php');
        $obj_event = new Events();
        $error['successful'] = true;
        $response = response_string($obj_event, 'eventCommentList', $error, $xmlrequest, $requesttype);
        break;
    case "EventPostComment":
        require_once ('classes/event.class.php');
        $obj_event = new Events();
        $error['successful'] = true;
        $response = response_string($obj_event, 'eventPostComment', $error, $xmlrequest, $requesttype);
        break;
    case "EventViewGuestList":
        require_once ('classes/event.class.php');
        $obj_event = new Events();
        $error['successful'] = true;
        $response = response_string($obj_event, 'eventViewGuestList', $error, $xmlrequest, $requesttype);
        break;
    case "EventAddGuestList":
        require_once ('classes/event.class.php');
        $obj_event = new Events();
        $error['successful'] = true;
        $response = response_string($obj_event, 'eventAddGuestList', $error, $xmlrequest, $requesttype);
        break;

    case "EventParentChildComment":
        require_once ('classes/event.class.php');
        $obj_event = new Events();
        $error['successful'] = true;
        $response = response_string($obj_event, 'EventParentChildComments', $error, $xmlrequest, $requesttype);
        break;

    case "EventRemoveGuestList":
        require_once ('classes/event.class.php');
        $obj_event = new Events();
        $error['successful'] = true;
        $response = response_string($obj_event, 'eventRemoveGuestList', $error, $xmlrequest, $requesttype);
        break;

    case "EventComments":
        require_once ('classes/event.class.php');
        $obj_event = new Events();
        $error['successful'] = true;
        $response = response_string($obj_event, 'eventCommentList', $error, $xmlrequest, $requesttype);
        break;

    case "EventPostComment":
        require_once ('classes/event.class.php');
        $obj_event = new Events();
        $error['successful'] = true;
        $response = response_string($obj_event, 'eventPostComment', $error, $xmlrequest, $requesttype);
        break;

    case "EventReplyComment":
        require_once ('classes/event.class.php');
        $obj_event = new Events();
        $error['successful'] = true;
        $response = response_string($obj_event, 'eventReplyComment', $error, $xmlrequest, $requesttype);
        break;

    case "EventCommentDelete":
        require_once ('classes/event.class.php');
        $obj_event = new Events();
        $error['successful'] = true;
        $response = response_string($obj_event, 'eventCommentDelete', $error, $xmlrequest, $requesttype);
        break;


    case "Messages":
        require_once ('classes/message.class.php');
        $obj_message = new Message();
        $error['successful'] = true;
        $response = response_string($obj_message, 'messageList', $error, $xmlrequest, $requesttype);
        break;

    case "DeleteMessage":
        require_once ('classes/message.class.php');
        $obj_message = new Message();
        $error['successful'] = true;
        $response = response_string($obj_message, 'deleteMessage', $error, $xmlrequest, $requesttype);
        break;
    
    case "getAllMessageList":
        require_once ('classes/message.class.php');
        $obj_message_all_msg = new Message();
        $error['successful'] = true;
        $response = response_string($obj_message_all_msg, 'getAllMessageList', $error, $xmlrequest, $requesttype);
        break;    

    case "MessageDetails":
        require_once ('classes/message.class.php');
        $obj_message = new Message();
        $error['successful'] = true;
        $response = response_string($obj_message, 'messageDetails', $error, $xmlrequest, $requesttype);
        break;

    case "sendMessage":
        require_once ('classes/message.class.php');
        $obj_message = new Message();
        $error['successful'] = true;
        $response = response_string($obj_message, 'sendMessage', $error, $xmlrequest, $requesttype);
        break;

    case "replyMessage":
        require_once ('classes/message.class.php');
        $obj_message = new Message();
        $error['successful'] = true;
        $response = response_string($obj_message, 'replyMessage', $error, $xmlrequest, $requesttype);
        break;

    case "AppEntourageList":
        require_once ('classes/appearance.class.php');
        $obj_appearance = new Appearance();
        $error['successful'] = true;
        $response = response_string($obj_appearance, 'appearanceList', $error, $xmlrequest, $requesttype);
        break;

    case "AppEntourageStatus":
        require_once ('classes/appearance.class.php');
        $obj_appearance = new Appearance();
        $error['successful'] = true;
        $response = response_string($obj_appearance, 'appearanceEntourageStatus', $error, $xmlrequest, $requesttype);
        break;

    case "AppEntStatusComment":
        require_once ('classes/appearance.class.php');
        $obj_appearance = new Appearance();
        $error['successful'] = true;
        $response = response_string($obj_appearance, 'appearanceEntStatusComment', $error, $xmlrequest, $requesttype);

        break;

    case "AppEntStatusCommentList":
        require_once ('classes/appearance.class.php');
        $obj_appearance = new Appearance();
        $error['successful'] = true;
        $response = response_string($obj_appearance, 'appearanceEntStatusCommentList', $error, $xmlrequest, $requesttype);

        break;

    case "AppearanceVenueList":
        require_once ('classes/appearance.class.php');
        $obj_appearance = new Appearance();
        $error['successful'] = true;
        $response = response_string($obj_appearance, 'appearanceVenueList', $error, $xmlrequest, $requesttype);
        break;

    case "AnnounceArrival":
        require_once ('classes/appearance.class.php');
        $obj_appearance = new Appearance();
        $error['successful'] = true;
        $response = response_string($obj_appearance, 'appVenueSave', $error, $xmlrequest, $requesttype);
        break;

    case "AppVenueDetail":
        require_once ('classes/appearance.class.php');
        $obj_appearance = new Appearance();
        $error['successful'] = true;
        $response = response_string($obj_appearance, 'appVenueDetail', $error, $xmlrequest, $requesttype);
        break;

    case "CurrentVenueStatus":
        require_once ('classes/appearance.class.php');
        $obj_appearance = new Appearance();
        $error['successful'] = true;
        $response = response_string($obj_appearance, 'currentVenueStatus', $error, $xmlrequest, $requesttype);
        break;

    case "AppReward":
        require_once ('classes/appearance.class.php');
        $obj_appearance = new Appearance();
        $error['successful'] = true;
        $response = response_string($obj_appearance, 'appRewards', $error, $xmlrequest, $requesttype);
        break;

    case "AppGetAllEventTag":
        require_once ('classes/appearance.class.php');
        $obj_appearance = new Appearance();
        $error['successful'] = true;
        $response = response_string($obj_appearance, 'appGetEventTag', $error, $xmlrequest, $requesttype);
        break;

    case"BackStageEventList":
        require_once ('classes/backstage.class.php');
        $obj_response = new BackStage();
        $error['successful'] = $obj_response->validate_user($xmlrequest);
        $response = response_string($obj_response, 'backStageEventList', $error, $xmlrequest, $requesttype);
        break;

    case "BSEViewGuestList":
        require_once ('classes/backstage.class.php');
        $obj_response = new BackStage();
        $error['successful'] = true;
        $response = response_string($obj_response, 'bckSEViewGuestList', $error, $xmlrequest, $requesttype);
        break;

    case "BSEGLCheckIn":
        require_once ('classes/backstage.class.php');
        $obj_response = new BackStage();
        $error['successful'] = true;
        $response = response_string($obj_response, 'bSEGLCheckIn', $error, $xmlrequest, $requesttype);

        break;

    case "BSEGLEntourageSearch":
        require_once ('classes/backstage.class.php');
        $obj_response = new BackStage();
        $error['successful'] = true;
        $response = response_string($obj_response, 'bSEGLEntourageSearch', $error, $xmlrequest, $requesttype);
        break;

    case "BSEViewTblReservationList":
        require_once ('classes/backstage.class.php');
        $obj_response = new BackStage();
        $error['successful'] = true;
        $response = response_string($obj_response, 'bSEViewTblReservationList', $error, $xmlrequest, $requesttype);
        break;

    case "BSETRCheckInNotes":
        require_once ('classes/backstage.class.php');
        $obj_response = new BackStage();
        $error['successful'] = true;
        $response = response_string($obj_response, 'bSETRCheckInNotes', $error, $xmlrequest, $requesttype);

        break;

    case "BSETRViewCheckIn":
        require_once ('classes/backstage.class.php');
        $obj_response = new BackStage();
        $error['successful'] = true;
        $response = response_string($obj_response, 'bSETRViewCheckIn', $error, $xmlrequest, $requesttype);

        break;

    case "BSETRConfirmMessageScreen":
        require_once ('classes/backstage.class.php');
        $obj_response = new BackStage();
        $error['successful'] = true;
        $response = response_string($obj_response, 'bSETRConfirmMessageScreen', $error, $xmlrequest, $requesttype);

        break;

    case"eventSharing":
        require_once ('classes/event.class.php');
        $obj_response = new Events();
        $error = $obj_response->event_sharing_valid($xmlrequest);
        $response = response_string($obj_response, 'eventSharing', $error, $xmlrequest, $requesttype);
        break;

    case "Alerts":
        require_once ('classes/alert.class.php');
        $obj_alert = new Alerts();
        $error['successful'] = true;
        $response = response_string($obj_alert, 'alertsList', $error, $xmlrequest, $requesttype);
        break;

    case "AlertsUpdate":
        require_once ('classes/alert.class.php');
        $obj_alerts = new Alerts();
        $error['successful'] = true;
        $response = response_string($obj_alerts, 'alerts_update', $error, $xmlrequest, $requesttype);
        break;

    case "HotPressAlert":
        require_once ('classes/alert.class.php');
        $obj_alerts = new Alerts();
        $error['successful'] = true;
        $response = response_string($obj_alerts, 'hotpress_alert', $error, $xmlrequest, $requesttype);
        break;

    case "CommentAlert":
        require_once ('classes/alert.class.php');
        $obj_alerts = new Alerts();
        $error['successful'] = true;
        $response = response_string($obj_alerts, 'comment_alert', $error, $xmlrequest, $requesttype);
        break;

    case "updateTable":
        require_once ('classes/alert.class.php');
        $obj_alerts = new Alerts();
        $error['successful'] = true;
        $response = response_string($obj_alerts, 'update_table', $error, $xmlrequest, $requesttype);
        break;


    case "UserLogout":
        $error['successful'] = true;
        require_once('classes/login.class.php');
        $obj_login = new Login();
        $error['successful'] = true;
        $response = response_string($obj_login, 'userLogout', $error, $xmlrequest, $requesttype);
        break;


    case "BSEViewNonMemGuestList":
        require_once ('classes/backstage.class.php');
        $error['successful'] = true;
        $obj_response = new BackStage();
        $response = response_string($obj_response, 'bckSEViewNonMemGuestList', $error, $xmlrequest, $requesttype);
        break;

    case "BSENonMemGLCheckIn":
        require_once ('classes/backstage.class.php');
        $error['successful'] = true;
        $obj_response = new BackStage();
        $response = response_string($obj_response, 'bSENonMemGLCheckIn', $error, $xmlrequest, $requesttype);
        break;


    case "BSENonMemGLEntourageSearch":
        require_once ('classes/backstage.class.php');
        $error['successful'] = true;
        $obj_response = new BackStage();
        $response = response_string($obj_response, 'bSENonMemGLEntourageSearch', $error, $xmlrequest, $requesttype);
        break;

    case "AlertsClear":
        require_once ('classes/alert.class.php');
        $error['successful'] = true;
        $obj_response = new Alerts();
        $response = response_string($obj_response, 'alertsClear', $error, $xmlrequest, $requesttype);
        break;


    case"DeletePhotoComment":
        require_once ('classes/photos.class.php');
        $obj_response = new AlbumPhotos();
        $error['successful'] = true;
        $response = response_string($obj_response, 'deletePhotoComment', $error, $xmlrequest, $requesttype);

        break;

    case"DeleteEventComment":
        require_once ('classes/event.class.php');
        $error['successful'] = true;
        $obj_response = new Events();
        $response = response_string($obj_response, 'deleteEventComment', $error, $xmlrequest, $requesttype);
        break;

    case"DeleteAppearanceComment":
        require_once ('classes/appearance.class.php');
        $error['successful'] = true;
        $obj_response = new Appearance();
        $response = response_string($obj_response, 'deleteAppearanceComment', $error, $xmlrequest, $requesttype);

        break;

    case"AllEntourageListByName":
        require_once ('classes/entourage.class.php');
        $error['successful'] = true;
        $obj_response = new Entourage();
        $response = response_string($obj_response, 'allEntourageListByName', $error, $xmlrequest, $requesttype);
        break;

    case"CommentsOnPhotosParentComment":
        require_once ('classes/photos.class.php');
        $error['successful'] = true;
        $obj_response = new AlbumPhotos();
        $response = response_string($obj_response, 'commentsOnPhotosParentComment', $error, $xmlrequest, $requesttype);

        break;

    case"RespondPhotoTagAlerts":
        require_once ('classes/alert.class.php');
        $error['successful'] = true;
        $obj_response = new Alerts();
        $response = response_string($obj_response, 'respondPhotoTagAlerts', $error, $xmlrequest, $requesttype);
        break;

    case"DisplayPhotoTagAlert":
        require_once ('classes/alert.class.php');
        $error['successful'] = true;
        $obj_response = new Alerts();
        $response = response_string($obj_response, 'displayPhotoTagAlert', $error, $xmlrequest, $requesttype);
        break;
    
    
}
writelog("index.php : : handle request :", $error, true);
?>