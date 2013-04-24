<?php

//include_once('login.class.php');
//include_once('hotpress.class.php');

class DatabaseValidation {

    function db_valid($match, $xmlrequest) {

        $error = array();
        switch ($match) {
            case "UserLogin":
                $obj_login = new Login();
                $error = $obj_login->login_check($xmlrequest);
                break;

            case "HotPress":
                $obj_hotpress = new HotPress();
                $error = $obj_hotpress->hotpress_valid($xmlrequest);
                break;

            case "CommentsOnHotPressPost":
                $obj_comment = new HotPress();
                $error = $obj_comment->comment_on_hotpress_post_valid($xmlrequest);
                break;

            case "HotPressPostComment":
                $obj_postComment = new HotPress();
                $error = $obj_postComment->hot_press_post_comment_valid($xmlrequest); //print_r($error);die();
                break;

            case "MostPopularOnHotpress":
                $obj_hotpress = new HotPress();
                $error = $obj_hotpress->hotpress_valid($xmlrequest);
                break;

            case "DeletePost":
                $obj_hotpress = new HotPress();
                $error = $obj_hotpress->delete_post_valid($xmlrequest);
                break;

            case "FriendRequests":
                $obj_friendreq = new Entourage();
                $error = $obj_friendreq->friend_request_valid($xmlrequest);
                break;

            case "Profile":
                $obj_profile = new Profile();
                $error = $obj_profile->profile_valid($xmlrequest);
                break;

            case "Photos":
                $obj_photos = new Profile();
                $error = $obj_photos->profile_photo_valid($xmlrequest);
                break;

            case "PhotoAlbumDetails":
                $obj_album_details = new Profile();
                $error = $obj_album_details->profile_album_details_valid($xmlrequest);
                break;

            case "CommentOnPhoto":
                $obj_comment_on_photo = new Profile();
                $error = $obj_comment_on_photo->comment_on_photo_valid($xmlrequest);

                break;

            case "MakeProfilePhoto":
                $obj_make_profile_photo = new Profile();
                $error = $obj_make_profile_photo->make_profile_photo_valid($xmlrequest);
                break;

            case "DeletePhoto":
                $obj_delete_photo = new Profile();
                $error = $obj_delete_photo->delete_photo_valid($xmlrequest);
                break;

            case "TagPhoto":
                $obj_tag_photo = new Profile();
                $error = $obj_tag_photo->tag_photo_valid($xmlrequest);
                break;

            case "TagsOnPhoto":
                $obj_tags_on_photo = new Profile();
                $error = $obj_tags_on_photo->tag_photo_valid($xmlrequest);
                break;

            case "PhotoUpload":
                $obj_photo_upload = new Profile();
                $error = $obj_photo_upload->profile_photo_upload_valid($xmlrequest);
                break;

            case "EntourageList":
                $obj_entourage_list = new Entourage();
                $error = $obj_entourage_list->entourage_list_valid($xmlrequest);
                break;

            case "LikePost":
                $obj_like_post = new HotPress();
                $error = $obj_like_post->like_post_valid($xmlrequest); //print_r($error);die();
                break;

            case "AllEntourageList":
                $obj_mutual_entourage_list = new Entourage();
                $error = $obj_mutual_entourage_list->mutual_entourage_list_valid($xmlrequest);
                break;

            case "AddAsFriendRequest":
                $obj_add_friend = new Entourage();
                $error = $obj_add_friend->add_friend_request_valid($xmlrequest);
                break;

            case"DisplayCommentOnPhoto":
                $obj_display_comment = new Profile();
                $error = $obj_display_comment->display_comment_on_photo_valid($xmlrequest);
                break;

            case "FullScreenPhoto":
                $obj_full_screen_photo = new Profile();
                $error = $obj_full_screen_photo->full_screen_photo_valid($xmlrequest);
                break;
            case "UserSignUp":
                //require_once('classes/facebook_register.class.php');
                $obj_facebook_signups = new FacebookConnect();
                $error = $obj_facebook_signups->valid_fields($xmlrequest);
                var_dump($error);
                print_r($error);
                die();
                break;

//              case "RemoveFriendRequest":
//                $obj_mutual_entourage_list = new Entourage();
//                $error = $obj_mutual_entourage_list->mutual_entourage_list_valid($xmlrequest);
//                break;
//
//            case "Entourage":
//                 $response_json=$obj_->entourage($response_message);
//                break;
//
//            case "Events":
//                 $response_json=$obj_->events($response_message);
//                break;
//
//            case "SearchEvent":
//                 $response_json=$obj_->searchEvent($response_message);
//                break;
//
//            case "EventDetails":
//                 $response_json=$obj_->eventDetails($response_message);
//                break;
//
//            case "Save411":
//                 $response_json=$obj_->save411($response_message);
//                break;
//
//            case "Messages":
//                 $response_json=$obj_->messages($response_message);
//                break;
//
//            case "DeleteMessage":
//                 $response_json=$obj_->deleteMessage($response_message);
//                break;
//
//            case "MessageDetails":
//                 $response_json=$obj_->messageDetails($response_message);
//                break;
//
//            case "SendMessage":
//                 $response_json=$obj_->sendMessage($response_message);
//                break;
//
//            case "Alerts":
//                 $response_json=$obj_->alerts($response_message);
//                break;
//
//
//            case "Appearances":
//                 $response_json=$obj_->appearances($response_message);
//                break;
//
//            case "Venues":
//                 $response_json=$obj_->venues($response_message);
//                break;
//
//            case "VenueDetails":
//                 $response_json=$obj_->venueDetails($response_message);
//                break;
//
//            case "FlagVenue":
//                 $response_json=$obj_->flagVenue($response_message);
//                break;
//
//            case "AnnounceArrival":
//                 $response_json=$obj_->announceArrival($response_message);
//                break;
//
//            case "LikeComment":
//                 $response_json=$obj_->likeComment($response_message);
//                break;
        }
        writelog("DatabaseValidation:db_valid():", $error, true);
        return $error;
    }

}

?>