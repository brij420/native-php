<?php

class Error {

    function error_type($match, $error) {
        if (DEBUG)
            writelog("error_check.class.php :: error_type() : ", "Start Here ", false);
        $response_message = array();
        // var_dump( $error );
        switch ($match) {
            case "UserLogin":
                if ((isset($error['successful'])) && ($error['successful'])) {
                    $response_message['UserLogin']['SuccessCode'] = '000';
                    $response_message['UserLogin']['SuccessDesc'] = 'You have log-in successfully';
                }
                if ((isset($error['blank'])) && ($error['blank'])) {
                    $response_message['UserLogin']['ErrorCode'] = '001';
                    $response_message['UserLogin']['ErrorDesc'] = 'Username and password field should not be left blank.';
                }

                if ((isset($error['email'])) && ($error['email'])) {
                    $response_message['UserLogin']['ErrorCode'] = '002';
                    $response_message['UserLogin']['ErrorDesc'] = 'Please enter valid Email address.';
                }
                if ((isset($error['successful'])) && (!$error['successful'])) {
                    $response_message['UserLogin']['ErrorCode'] = '003';
                    $response_message['UserLogin']['ErrorDesc'] = 'Please enter a valid Username and Password.';
                }
		if ((isset($error['facebookLoginError'])) && ($error['facebookLoginError'])) {
                    $response_message['UserLogin']['ErrorCode'] = '004';
                    $response_message['UserLogin']['ErrorDesc'] = 'Please enter valid Username and Password. If you registered using Facebook Connect you can log into your account using Facebook Connect.';
                }
			if ((isset($error['verified'])) && (!$error['verified'])) {
		    		$response_message['UserLogin']['ErrorCode'] = '006';
		    		$response_message['UserLogin']['ErrorDesc'] = 'Please verify your email address & complete the registration  process to activate your SocialNightlife Account.';
				}
                break;
		
	    case "profileRegistration":
		if ((isset($error['successful'])) && ($error['successful'])) {
		    $response_message['profileRegistration']['SuccessCode'] = '000';
		    $response_message['profileRegistration']['SuccessDesc'] = 'Profile updated successfully.';
		}
		if ((isset($error['blank'])) && ($error['blank'])) {
		    $response_message['profileRegistration']['ErrorCode'] = '001';
		    $response_message['profileRegistration']['ErrorDesc'] = 'Userld should not be left blank.';
		}

		if ((isset($error['successful'])) && (!$error['successful'])) {
		    $response_message['profileRegistration']['ErrorCode'] = '003';
		    $response_message['profileRegistration']['ErrorDesc'] = 'Please Enter Complete Information.';
		}
		break;
		
            case "HotPress":
                if ((isset($error['successful'])) && ($error['successful'])) {
                    $response_message['HotPress']['SuccessCode'] = '000';
                    $response_message['HotPress']['SuccessDesc'] = 'List of Posts.';
                }
                if ((isset($error['successful'])) && (!$error['successful'])) {
                    $response_message['HotPress']['ErrorCode'] = '000';
                    $response_message['HotPress']['ErrorDesc'] = 'Id does not exist in our database.';
                }
                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['HotPress']['ErrorCode'] = '001';
                    $response_message['HotPress']['ErrorDesc'] = 'Id should contains only numbers';
                }
                if ((isset($error['blank'])) && ($error['blank'])) {
                    $response_message['HotPress']['ErrorCode'] = '002';
                    $response_message['HotPress']['ErrorDesc'] = 'Field should not be blank.';
                }

                break;

            case "CommentsOnHotPressPost":

                if ((isset($error['successful'])) && ($error['successful'])) {
                    $response_message['CommentsOnHotPressPost']['SuccessCode'] = '000';
                    $response_message['CommentsOnHotPressPost']['SuccessDesc'] = 'Comments List.';
                }
                if ((isset($error['userid'])) && ($error['userid'])) {
                    $response_message['CommentsOnHotPressPost']['ErrorCode'] = '001';
                    $response_message['CommentsOnHotPressPost']['ErrorDesc'] = 'user-Id should contains only numbers';
                }

                if ((isset($error['post'])) && ($error['post'])) {
                    $response_message['CommentsOnHotPressPost']['ErrorCode'] = '002';
                    $response_message['CommentsOnHotPressPost']['ErrorDesc'] = 'post-Id should contains only numbers.';
                }
                break;
		
            case "GetBadges":

                if ((isset($error['successful'])) && ($error['successful'])) {
                    $response_message['GetBadges']['SuccessCode'] = '000';
                    $response_message['GetBadges']['SuccessDesc'] = 'badges List.';
                }
                if ((isset($error['userid'])) && ($error['userid'])) {
                    $response_message['GetBadges']['ErrorCode'] = '001';
                    $response_message['GetBadges']['ErrorDesc'] = 'user-Id should contains only numbers';
                }
                if ((isset($error['successful'])) && (!$error['successful'])) {
                    $response_message['GetBadges']['ErrorCode'] = '002';
                    $response_message['GetBadges']['ErrorDesc'] = 'unable to load badges.';
                }
                break;
				
		case "BadgeDetails":

		if ((isset($error['successful'])) && ($error['successful'])) {
		    $response_message['badgeDetails']['SuccessCode'] = '000';
		    $response_message['badgeDetails']['SuccessDesc'] = 'badges Details Listed successfully.';
		}
		if ((isset($error['number'])) && ($error['number'])) {
		    $response_message['GetBadges']['ErrorCode'] = '001';
		    $response_message['GetBadges']['ErrorDesc'] = 'user-Id should contains only numbers';
		}
		if ((isset($error['successful'])) && (!$error['successful'])) {
		    $response_message['GetBadges']['ErrorCode'] = '002';
		    $response_message['GetBadges']['ErrorDesc'] = 'unable to load bottle details.';
		}
		break;	

            case "HotPressPostComment":
                if ((isset($error['successful'])) && ($error['successful'])) {
                    $response_message['HotPressPostComment']['SuccessCode'] = '000';
                    $response_message['HotPressPostComment']['SuccessDesc'] = 'Comment Posted successfully.';
                }
                if ((isset($error['successful'])) && (!$error['successful'])) {
                    $response_message['HotPressPostComment']['ErrorCode'] = '004';
                    $response_message['HotPressPostComment']['ErrorDesc'] = 'Top comment has been deleted or not present.';
                }

                if ((isset($error['HotPressPostComment']['successful_fin'])) && (!$error['HotPressPostComment']['successful_fin'])) {
                    $response_message['HotPressPostComment']['ErrorCode'] = '005';
                    $response_message['HotPressPostComment']['ErrorDesc'] = 'Your comment has not been posted.';
                }
                if ((isset($error['fromid'])) && ($error['fromid'])) {
                    $response_message['HotPressPostComment']['ErrorCode'] = '001';
                    $response_message['HotPressPostComment']['ErrorDesc'] = 'from-Id should contains only numbers';
                }
                if ((isset($error['parentid'])) && ($error['parentid'])) {
                    $response_message['HotPressPostComment']['ErrorCode'] = '002';
                    $response_message['HotPressPostComment']['ErrorDesc'] = 'Post-Id should contains only numbers';
                }
                if ((isset($error['post'])) && ($error['post'])) {
                    $response_message['HotPressPostComment']['ErrorCode'] = '003';
                    $response_message['HotPressPostComment']['ErrorDesc'] = 'comment should not be blank.';
                }
                break;

            case "DisplayPhotoTag":
                if ((isset($error['successful'])) && ($error['successful'])) {
                    $response_message['DisplayPhotoTag']['SuccessCode'] = '000';
                    $response_message['DisplayPhotoTag']['SuccessDesc'] = 'Post deleted successfully.';
                }
                if ((isset($error['successful'])) && (!$error['successful'])) {
                    $response_message['DisplayPhotoTag']['ErrorCode'] = '002';
                    $response_message['DisplayPhotoTag']['ErrorDesc'] = 'Post does not exist in our Database';
                }
                if ((isset($error['different_user'])) && (!$error['different_user'])) {
                    $response_message['DisplayPhotoTag']['ErrorCode'] = '004';
                    $response_message['DisplayPhotoTag']['ErrorDesc'] = 'You can not delete this post';
                }
                if ((isset($error['DisplayPhotoTag']['successful_fin'])) && (!$error['DisplayPhotoTag']['successful_fin'])) {
                    $response_message['DisplayPhotoTag']['ErrorCode'] = '003';
                    $response_message['DisplayPhotoTag']['ErrorDesc'] = 'Post has not been deleted.';
                }
                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['DisplayPhotoTag']['ErrorCode'] = '001';
                    $response_message['DisplayPhotoTag']['ErrorDesc'] = 'Id should contains only numbers';
                }
                break;

            case "FriendRequests":
                if ((isset($error['successful'])) && ($error['successful'])) {
                    $response_message['FriendRequests']['SuccessCode'] = '000';
                    $response_message['FriendRequests']['SuccessDesc'] = 'Total friend requests:';
                }
                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['FriendRequests']['ErrorCode'] = '001';
                    $response_message['FriendRequests']['ErrorDesc'] = 'Id should contains only numbers';
                }
                break;


            case "Profile":
                if ((isset($error['successful'])) && ($error['successful'])) {
                    $response_message['Profile']['SuccessCode'] = '000';
                    $response_message['Profile']['SuccessDesc'] = 'profile info is:';
                }
                if ((isset($error['successful'])) && (!$error['successful'])) {
                    $response_message['Profile']['ErrorCode'] = '003';
                    $response_message['Profile']['ErrorDesc'] = 'Id does not exist in our data base.';
                }
                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['Profile']['ErrorCode'] = '001';
                    $response_message['Profile']['ErrorDesc'] = 'Id should contains only numbers';
                }
                if ((isset($error['blank'])) && ($error['blank'])) {
                    $response_message['Profile']['ErrorCode'] = '002';
                    $response_message['Profile']['ErrorDesc'] = 'Field should be blank.';
                }
                break;

            case "PhotoUpload":

                if ((isset($error['match'])) && ($error['match'])) {
                    $response_message['PhotoUpload']['ErrorCode'] = '005';
                    $response_message['PhotoUpload']['ErrorDesc'] = 'Only jpg|jpeg|png image.';
                }

                if ((isset($error['successful'])) && ($error['successful'])) {
                    $response_message['PhotoUpload']['SuccessCode'] = '000';
                    $response_message['PhotoUpload']['SuccessDesc'] = 'Photo Uploaded successfully.';
                }

                if ((isset($error['successful'])) && (!$error['successful'])) {
                    $response_message['PhotoUpload']['ErrorCode'] = '003';
                    $response_message['PhotoUpload']['ErrorDesc'] = 'Problem occurring in photo uploading.';
                }
                if ((isset($error['write'])) && ($error['write'])) {
                    $response_message['PhotoUpload']['ErrorCode'] = '004';
                    $response_message['PhotoUpload']['ErrorDesc'] = $error['write'];
                }
                if ((isset($error['write_succesfully'])) && ($error['write_succesfully'])) {
                    $response_message['PhotoUpload']['ErrorCode'] = '000';
                    $response_message['PhotoUpload']['ErrorDesc'] = $error['write_succesfully'];
                }

                if ((isset($error['PhotoUpload']['successful_fin'])) && (!$error['PhotoUpload']['successful_fin'])) {
                    $response_message['PhotoUpload']['ErrorCode'] = '999';
                    $response_message['PhotoUpload']['ErrorDesc'] = 'Problem occurring in photo uploading.';
                }


                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['PhotoUpload']['ErrorCode'] = '001';
                    $response_message['PhotoUpload']['ErrorDesc'] = 'Id should contains only numbers';
                }
                if ((isset($error['blank'])) && ($error['blank'])) {
                    $response_message['PhotoUpload']['ErrorCode'] = '002';
                    $response_message['PhotoUpload']['ErrorDesc'] = 'Field should be blank.';
                }

                break;

            case "Photos":
                if ((isset($error['successful'])) && ($error['successful'])) {
                    $response_message['Photos']['SuccessCode'] = '000';
                    $response_message['Photos']['SuccessDesc'] = 'List of Photo Albums';
                }
                if ((isset($error['successful'])) && (!$error['successful'])) {
                    $response_message['Photos']['ErrorCode'] = '002';
                    $response_message['Photos']['ErrorDesc'] = 'Photo Album does not exist in our Database';
                }
                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['Photos']['ErrorCode'] = '001';
                    $response_message['Photos']['ErrorDesc'] = 'Id should contains only numbers';
                }
                break;

            case "PhotoAlbumDetails":
                if ((isset($error['successful'])) && ($error['successful'])) {
                    $response_message['PhotoAlbumDetails']['SuccessCode'] = '000';
                    $response_message['PhotoAlbumDetails']['SuccessDesc'] = 'Photo Album Details';
                }
                if ((isset($error['successful'])) && (!$error['successful'])) {
                    $response_message['PhotoAlbumDetails']['ErrorCode'] = '002';
                    $response_message['PhotoAlbumDetails']['ErrorDesc'] = 'Photo Album does not exist in our Database';
                }
                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['PhotoAlbumDetails']['ErrorCode'] = '001';
                    $response_message['PhotoAlbumDetails']['ErrorDesc'] = 'Id should contains only numbers';
                }
                break;

            case "CommentOnPhoto":
                if ((isset($error['successful'])) && ($error['successful'])) {
                    $response_message['CommentOnPhoto']['SuccessCode'] = '000';
                    $response_message['CommentOnPhoto']['SuccessDesc'] = 'Comment Posted successfully.';
                }
                if ((isset($error['successful'])) && (!$error['successful'])) {
                    $response_message['CommentOnPhoto']['ErrorCode'] = '002';
                    $response_message['CommentOnPhoto']['ErrorDesc'] = 'Comment has not been posted.';
                }
                if ((isset($error['CommentOnPhoto']['successful_fin'])) && (!$error['CommentOnPhoto']['successful_fin'])) {
                    $response_message['CommentOnPhoto']['ErrorCode'] = '004';
                    $response_message['CommentOnPhoto']['ErrorDesc'] = 'Your comment has not been posted.';
                }
                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['CommentOnPhoto']['ErrorCode'] = '001';
                    $response_message['CommentOnPhoto']['ErrorDesc'] = 'Id should contains only numbers';
                }

                if ((isset($error['post'])) && ($error['post'])) {
                    $response_message['CommentOnPhoto']['ErrorCode'] = '003';
                    $response_message['CommentOnPhoto']['ErrorDesc'] = 'comment should not be blank.';
                }
                break;

            case "MakeProfilePhoto":
                if ((isset($error['successful'])) && ($error['successful'])) {
                    $response_message['MakeProfilePhoto']['SuccessCode'] = '000';
                    $response_message['MakeProfilePhoto']['SuccessDesc'] = 'Profile photo changed successfully.';
                }
                if ((isset($error['successful'])) && (!$error['successful'])) {
                    $response_message['MakeProfilePhoto']['ErrorCode'] = '002';
                    $response_message['MakeProfilePhoto']['ErrorDesc'] = 'Photo Album does not exist in our Database';
                }
                if ((isset($error['MakeProfilePhoto']['successful_fin'])) && (!$error['MakeProfilePhoto']['successful_fin'])) {
                    $response_message['MakeProfilePhoto']['ErrorCode'] = '003';
                    $response_message['MakeProfilePhoto']['ErrorDesc'] = 'Your profile image has not changed.';
                }
                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['MakeProfilePhoto']['ErrorCode'] = '001';
                    $response_message['MakeProfilePhoto']['ErrorDesc'] = 'Id should contains only numbers';
                }
                break;

            case "DeletePhoto":
                if ((isset($error['successful'])) && ($error['successful'])) {
                    $response_message['DeletePhoto']['SuccessCode'] = '000';
                    $response_message['DeletePhoto']['SuccessDesc'] = 'Photo deleted successfully.';
                }
                if ((isset($error['successful'])) && (!$error['successful'])) {
                    $response_message['DeletePhoto']['ErrorCode'] = '002';
                    $response_message['DeletePhoto']['ErrorDesc'] = 'Photo Album does not exist in our Database';
                }
                if ((isset($error['DeletePhoto']['successful_fin'])) && (!$error['DeletePhoto']['successful_fin'])) {
                    $response_message['DeletePhoto']['ErrorCode'] = '003';
                    $response_message['DeletePhoto']['ErrorDesc'] = 'Image has not been deleted.';
                }
                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['DeletePhoto']['ErrorCode'] = '001';
                    $response_message['DeletePhoto']['ErrorDesc'] = 'Id should contains only numbers';
                }
                break;

            case "EntourageList":
                if ((isset($error['successful'])) && ($error['successful'])) {
                    $response_message['EntourageList']['SuccessCode'] = '000';
                    $response_message['EntourageList']['SuccessDesc'] = 'Entourage List:';
                }
                if ((isset($error['successful'])) && (!$error['successful'])) {
                    $response_message['EntourageList']['ErrorCode'] = '002';
                    $response_message['EntourageList']['ErrorDesc'] = 'User-id does not exist in our Database';
                }
                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['EntourageList']['ErrorCode'] = '001';
                    $response_message['EntourageList']['ErrorDesc'] = 'Id should contains only numbers';
                }
                break;

            case "AllEntourageListByName":
                if ((isset($error['successful'])) && ($error['successful'])) {
                    $response_message['AllEntourageListByName']['SuccessCode'] = '000';
                    $response_message['AllEntourageListByName']['SuccessDesc'] = 'Entourage List:';
                }
                if ((isset($error['successful'])) && (!$error['successful'])) {
                    $response_message['AllEntourageListByName']['ErrorCode'] = '002';
                    $response_message['AllEntourageListByName']['ErrorDesc'] = 'User-id does not exist in our Database';
                }
                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['AllEntourageListByName']['ErrorCode'] = '001';
                    $response_message['AllEntourageListByName']['ErrorDesc'] = 'Id should contains only numbers';
                }
                break;


            case "AllEntourageList":
                if ((isset($error['successful'])) && ($error['successful'])) {
                    $response_message['AllEntourageList']['SuccessCode'] = '000';
                    $response_message['AllEntourageList']['SuccessDesc'] = 'Entourage List:';
                }
                if ((isset($error['successful'])) && (!$error['successful'])) {
                    $response_message['AllEntourageList']['ErrorCode'] = '002';
                    $response_message['AllEntourageList']['ErrorDesc'] = 'User-id does not exist in our Database';
                }
                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['AllEntourageList']['ErrorCode'] = '001';
                    $response_message['AllEntourageList']['ErrorDesc'] = 'Id should contains only numbers';
                }
                break;

            case "LikePostList":
                if ((isset($error['successful'])) && ($error['successful'])) {
                    $response_message['LikePostList']['SuccessCode'] = '000';
                    $response_message['LikePostList']['SuccessDesc'] = 'Post liked.';
                }
                if ((isset($error['successful'])) && (!$error['successful'])) {
                    $response_message['LikePostList']['ErrorCode'] = '002';
                    $response_message['LikePostList']['ErrorDesc'] = 'Post does not exist in our Database';
                }
                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['LikePostList']['ErrorCode'] = '001';
                    $response_message['LikePostList']['ErrorDesc'] = 'Id should contains only numbers';
                }
                break;

            case "LikePost":
                if ((isset($error['successful'])) && ($error['successful'])) {
                    $response_message['LikePost']['SuccessCode'] = '000';
                    $response_message['LikePost']['SuccessDesc'] = 'Post liked.';
                }
                if ((isset($error['successful'])) && (!$error['successful'])) {
                    $response_message['LikePost']['ErrorCode'] = '002';
                    $response_message['LikePost']['ErrorDesc'] = 'Post alresdy liked or does not exist in our Database';
                }
                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['LikePost']['ErrorCode'] = '001';
                    $response_message['LikePost']['ErrorDesc'] = 'Id should contains only numbers';
                }
                if ((isset($error['LikePost']['successful_fin'])) && (!$error['LikePost']['successful_fin'])) {
                    $response_message['LikePost']['ErrorCode'] = '003';
                    $response_message['LikePost']['ErrorDesc'] = 'error occured while liking a Post.';
                }
                break;

            case "AddAsFriendRequest":
                if ((isset($error['successful'])) && ($error['successful'])) {
                    $response_message['AddAsFriendRequest']['SuccessCode'] = '000';
                }

                if ((isset($error['successful'])) && (!$error['successful'])) {
                    $response_message['AddAsFriendRequest']['ErrorCode'] = '002';
                    $response_message['AddAsFriendRequest']['ErrorDesc'] = 'Friend request has not been posted.';
                }
                if ((isset($error['AddAsFriendRequest']['successful_fin'])) && (!$error['AddAsFriendRequest']['successful_fin'])) {
                    $response_message['AddAsFriendRequest']['ErrorCode'] = '004';
                    $response_message['AddAsFriendRequest']['ErrorDesc'] = 'Friend request already sent.!!!.';
                }
                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['AddAsFriendRequest']['ErrorCode'] = '001';
                    $response_message['AddAsFriendRequest']['ErrorDesc'] = 'Id should contains only numbers';
                }
                if ((isset($error['blank'])) && ($error['blank'])) {
                    $response_message['AddAsFriendRequest']['ErrorCode'] = '003';
                    $response_message['AddAsFriendRequest']['ErrorDesc'] = 'Status should not be blank.';
                }

                break;

            case"TakeOverProfile":
                if ((isset($error['successful'])) && ($error['successful'])) {
                    $response_message['TakeOverProfile']['SuccessCode'] = '000';
                    $response_message['TakeOverProfile']['SuccessDesc'] = 'Friend removed successfully.';
                }
                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['TakeOverProfile']['ErrorCode'] = '001';
                    $response_message['TakeOverProfile']['ErrorDesc'] = 'Id should contains only numbers';
                }
                if ((isset($error['blank'])) && ($error['blank'])) {
                    $response_message['TakeOverProfile']['ErrorCode'] = '002';
                    $response_message['TakeOverProfile']['ErrorDesc'] = 'Id should contains only numbers';
                }
                if ((isset($error['TakeOverProfile']['successful_fin'])) && (!$error['TakeOverProfile']['successful_fin'])) {
                    $response_message['TakeOverProfile']['ErrorCode'] = '003';
                    $response_message['TakeOverProfile']['ErrorDesc'] = 'Take over profile has not been saved.';
                }
                break;

            case"RemoveFriend":
                if ((isset($error['successful'])) && ($error['successful'])) {
                    $response_message['RemoveFriend']['SuccessCode'] = '000';
                    $response_message['RemoveFriend']['SuccessDesc'] = 'Friend removed successfully.';
                }
                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['RemoveFriend']['ErrorCode'] = '001';
                    $response_message['RemoveFriend']['ErrorDesc'] = 'Id should contains only numbers';
                }
                if ((isset($error['RemoveFriend']['successful_fin'])) && (!$error['RemoveFriend']['successful_fin'])) {
                    $response_message['RemoveFriend']['ErrorCode'] = '002';
                    $response_message['RemoveFriend']['ErrorDesc'] = 'Friend has not been removed.';
                }
                break;

            case"DeletePhotoComment":
                if ((isset($error['successful'])) && ($error['successful'])) {
                    $response_message['DeletePhotoComment']['SuccessCode'] = '000';
                    $response_message['DeletePhotoComment']['SuccessDesc'] = 'Post has been deleted successfully.';
                }
                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['DeletePhotoComment']['ErrorCode'] = '001';
                    $response_message['DeletePhotoComment']['ErrorDesc'] = 'Id should contains only numbers';
                }
                if ((isset($error['DeletePhotoComment']['successful_fin'])) && (!$error['DeletePhotoComment']['successful_fin'])) {
                    $response_message['DeletePhotoComment']['ErrorCode'] = '002';
                    $response_message['DeletePhotoComment']['ErrorDesc'] = 'Post has not been removed.';
                }
                break;

            case"DeleteEventComment":
                if ((isset($error['successful'])) && ($error['successful'])) {
                    $response_message['DeleteEventComment']['SuccessCode'] = '000';
                    $response_message['DeleteEventComment']['SuccessDesc'] = 'Post has been deleted successfully.';
                }
                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['DeleteEventComment']['ErrorCode'] = '001';
                    $response_message['DeleteEventComment']['ErrorDesc'] = 'Id should contains only numbers';
                }
                if ((isset($error['DeleteEventComment']['successful_fin'])) && (!$error['DeleteEventComment']['successful_fin'])) {
                    $response_message['DeleteEventComment']['ErrorCode'] = '002';
                    $response_message['DeleteEventComment']['ErrorDesc'] = 'Post has not been removed.';
                }
                break;

            case"DeleteAppearanceComment":
                if ((isset($error['successful'])) && ($error['successful'])) {
                    $response_message['DeleteAppearanceComment']['SuccessCode'] = '000';
                    $response_message['DeleteAppearanceComment']['SuccessDesc'] = 'Post has been deleted successfully.';
                }
                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['DeleteAppearanceComment']['ErrorCode'] = '001';
                    $response_message['DeleteAppearanceComment']['ErrorDesc'] = 'Id should contains only numbers';
                }
                if ((isset($error['DeleteAppearanceComment']['successful_fin'])) && (!$error['DeleteAppearanceComment']['successful_fin'])) {
                    $response_message['DeleteAppearanceComment']['ErrorCode'] = '002';
                    $response_message['DeleteAppearanceComment']['ErrorDesc'] = 'Post has not been removed.';
                }
                break;

            case"DisplayCommentOnPhoto":
                if ((isset($error['successful'])) && ($error['successful'])) {
                    $response_message['DisplayCommentOnPhoto']['SuccessCode'] = '000';
                    $response_message['DisplayCommentOnPhoto']['SuccessDesc'] = 'Comments on photos are.';
                }

                if ((isset($error['successful'])) && (!$error['successful'])) {
                    $response_message['DisplayCommentOnPhoto']['ErrorCode'] = '002';
                    $response_message['DisplayCommentOnPhoto']['ErrorDesc'] = 'Id is not present in database';
                }
                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['DisplayCommentOnPhoto']['ErrorCode'] = '001';
                    $response_message['DisplayCommentOnPhoto']['ErrorDesc'] = 'Id should contains only numbers';
                }
                break;

            case "FullScreenPhoto":
                if ((isset($error['successful'])) && ($error['successful'])) {
                    $response_message['FullScreenPhoto']['SuccessCode'] = '000';
                    $response_message['FullScreenPhoto']['SuccessDesc'] = 'Image sent successfully.';
                }

                if ((isset($error['successful'])) && (!$error['successful'])) {
                    $response_message['FullScreenPhoto']['ErrorCode'] = '002';
                    $response_message['FullScreenPhoto']['ErrorDesc'] = 'Id is not present in database';
                }
                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['FullScreenPhoto']['ErrorCode'] = '001';
                    $response_message['FullScreenPhoto']['ErrorDesc'] = 'Id should contains only numbers';
                }
                break;

            case "TagPhoto":
                if ((isset($error['successful'])) && ($error['successful'])) {
                    $response_message['TagPhoto']['SuccessCode'] = '000';
                    $response_message['TagPhoto']['SuccessDesc'] = 'Photo has been tagged successfully.';
                }
                if ((isset($error['successful'])) && (!$error['successful'])) {
                    $response_message['TagPhoto']['ErrorCode'] = '002';
                    $response_message['TagPhoto']['ErrorDesc'] = 'Photo does not exist in our Database';
                }
                if ((isset($error['TagPhoto']['successful_fin'])) && (!$error['TagPhoto']['successful_fin'])) {
                    $response_message['TagPhoto']['ErrorCode'] = '003';
                    $response_message['TagPhoto']['ErrorDesc'] = 'Image has not been tagged.';
                }
                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['TagPhoto']['ErrorCode'] = '001';
                    $response_message['TagPhoto']['ErrorDesc'] = 'Id should contains only numbers';
                }
                if ((isset($error['blank'])) && ($error['blank'])) {
                    $response_message['TagPhoto']['ErrorCode'] = '004';
                    $response_message['TagPhoto']['ErrorDesc'] = 'Field should not be blank.';
                }

                break;

            case "TagsOnPhoto":
                if ((isset($error['successful'])) && ($error['successful'])) {
                    $response_message['TagsOnPhoto']['SuccessCode'] = '000';
                    $response_message['TagsOnPhoto']['SuccessDesc'] = 'Tags on photo.';
                }
                if ((isset($error['successful'])) && (!$error['successful'])) {
                    $response_message['TagsOnPhoto']['ErrorCode'] = '002';
                    $response_message['TagsOnPhoto']['ErrorDesc'] = 'Photo does not exist in our Database';
                }

                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['TagsOnPhoto']['ErrorCode'] = '001';
                    $response_message['TagsOnPhoto']['ErrorDesc'] = 'Id should contains only numbers';
                }

                break;

            case"RemoveTag":
                if ((isset($error['successful'])) && ($error['successful'])) {
                    $response_message['RemoveTag']['SuccessCode'] = '000';
                    $response_message['RemoveTag']['SuccessDesc'] = 'Tag Removed Successfully.';
                }
                if ((isset($error['successful'])) && (!$error['successful'])) {
                    $response_message['RemoveTag']['SuccessCode'] = '001';
                    $response_message['RemoveTag']['SuccessDesc'] = 'Tag Can Not Be Removed.';
                }
                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['RemoveTag']['ErrorCode'] = '002';
                    $response_message['RemoveTag']['ErrorDesc'] = 'Id should contains only numbers';
                }
                break;

            case "UserLogout":
                if ((isset($error['successful'])) && ($error['successful'])) {
                    $response_message['UserLogout']['SuccessCode'] = '000';
                    $response_message['UserLogout']['SuccessDesc'] = 'Logout is successfully.';
                }
                if ((isset($error['UserLogout']['successful_fin'])) && (!$error['UserLogout']['successful_fin'])) {
                    $response_message['UserLogout']['ErrorCode'] = '003';
                    $response_message['UserLogout']['ErrorDesc'] = 'Logout is unsuccessful.';
                }

                if ((isset($error['successful'])) && (!$error['successful'])) {
                    $response_message['UserLogout']['ErrorCode'] = '002';
                    $response_message['UserLogout']['ErrorDesc'] = 'Logout is unsuccessful.';
                }

                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['UserLogout']['ErrorCode'] = '001';
                    $response_message['UserLogout']['ErrorDesc'] = 'Id should contains only numbers';
                }

                break;


            case "AdvanceSearch":
                if ((isset($error['successful'])) && ($error['successful'])) {
                    $response_message['AdvanceSearch']['SuccessCode'] = '000';
                    $response_message['AdvanceSearch']['SuccessDesc'] = 'List of Friends.';
                }
                if ((isset($error['successful'])) && (!$error['successful'])) {
                    $response_message['AdvanceSearch']['ErrorCode'] = '002';
                    $response_message['AdvanceSearch']['ErrorDesc'] = 'Id does not exist in our Database';
                }

                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['AdvanceSearch']['ErrorCode'] = '001';
                    $response_message['AdvanceSearch']['ErrorDesc'] = 'Id should contains only numbers';
                }
                if ((isset($error['blank'])) && ($error['blank'])) {
                    $response_message['AdvanceSearch']['ErrorCode'] = '003';
                    $response_message['AdvanceSearch']['ErrorDesc'] = 'Field should not be blank.';
                }

                if ((isset($error['age_limit'])) && ( $error['age_limit'] )) {
                    $response_message['AdvanceSearch']['ErrorCode'] = '004';
                    $response_message['AdvanceSearch']['ErrorDesc'] = 'Please choose a valid age and should be between 100.';
                }

                break;

            case "CreatePhotoAlbum":
                if ((isset($error['successful'])) && ($error['successful'])) {
                    $response_message['CreatePhotoAlbum']['SuccessCode'] = '000';
                    $response_message['CreatePhotoAlbum']['SuccessDesc'] = 'Photo Album has been created.';
                }
                if ((isset($error['successful'])) && (!$error['successful'])) {
                    $response_message['CreatePhotoAlbum']['ErrorCode'] = '003';
                    $response_message['CreatePhotoAlbum']['ErrorDesc'] = 'Id is not present in our Data Base.';
                }

                if ((isset($error['CreatePhotoAlbum']['successful_fin'])) && (!$error['CreatePhotoAlbum']['successful_fin'])) {
                    $response_message['CreatePhotoAlbum']['ErrorCode'] = '004';
                    $response_message['CreatePhotoAlbum']['ErrorDesc'] = 'Album has not been created.';
                }


                if ((isset($error['blank'])) && ($error['blank'])) {
                    $response_message['CreatePhotoAlbum']['ErrorCode'] = '001';
                    $response_message['CreatePhotoAlbum']['ErrorDesc'] = 'field should not be blank.';
                }
                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['CreatePhotoAlbum']['ErrorCode'] = '002';
                    $response_message['CreatePhotoAlbum']['ErrorDesc'] = 'Id should be in digit.';
                }
                break;

            case "UserSignUp":
                if ((isset($error['successful'])) && (!$error['successful'])) {
                    $response_message['UserSignUp']['ErrorCode'] = '005';
                    $response_message['UserSignUp']['ErrorDesc'] = 'This Email address is already exists in System,Please use any other Email address';
                }
                if ((isset($error['successful'])) && ($error['successful'])) {
                    $response_message['UserSignUp']['SuccessCode'] = '000';
                    $response_message['UserSignUp']['SuccessDesc'] = 'Registration successfully.';
                }

                if ((isset($error['UserSignUp']['successful_fin'])) && (!$error['UserSignUp']['successful_fin'])) {
                    $response_message['UserSignUp']['ErrorCode'] = '006';
                    $response_message['UserSignUp']['ErrorDesc'] = 'Registration has not been completed please try again.';
                }
				
				if ($error['UserSignUp']['successful_fin'] == 'InvalidIntCode' && (is_string($error['UserSignUp']['successful_fin']))) {
					$response_message['UserSignUp']['ErrorCode'] = '006';
					$response_message['UserSignUp']['ErrorDesc'] = 'Please Enter Your Valid invitation Code.';
				}

                if ((isset($error['blank'])) && ($error['blank'])) {
                    $response_message['UserSignUp']['ErrorCode'] = '001';
                    $response_message['UserSignUp']['ErrorDesc'] = 'field should not be blank.';
                }
                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['UserSignUp']['ErrorCode'] = '002';
                    $response_message['UserSignUp']['ErrorDesc'] = 'Id should contain only digit.';
                }
                if ((isset($error['character'])) && ($error['character'])) {
                    $response_message['UserSignUp']['ErrorCode'] = '003';
                    $response_message['UserSignUp']['ErrorDesc'] = 'Name should contain only character.';
                }
                if ((isset($error['email'])) && ( $error['email'])) {
                    $response_message['UserSignUp']['ErrorCode'] = '004';
                    $response_message['UserSignUp']['ErrorDesc'] = 'Please enter valid email.';
                }

                break;

            case"FBVerifyUser":
                if ((isset($error['successful'])) && (!$error['successful'])) {
                    $response_message['FBVerifyUser']['ErrorCode'] = '002';
                    $response_message['FBVerifyUser']['ErrorDesc'] = 'No Email present in our Data Base.';
                }

                if ((isset($error['FBVerifyUser']['successful_fin'])) && (!$error['FBVerifyUser']['successful_fin'])) {
                    $response_message['FBVerifyUser']['ErrorCode'] = '003';
                    $response_message['FBVerifyUser']['ErrorDesc'] = 'Latitude and Longitude has not been updated.';
                }

                if ((isset($error['successful'])) && ($error['successful'])) {
                    $response_message['FBVerifyUser']['SuccessCode'] = '000';
                    $response_message['FBVerifyUser']['SuccessDesc'] = 'Login successful.';
                }

                if ((isset($error['email'])) && ( $error['email'])) {
                    $response_message['FBVerifyUser']['ErrorCode'] = '001';
                    $response_message['FBVerifyUser']['ErrorDesc'] = 'Please enter valid email.';
                }

                break;
				
case"FbUserVerification":
		if ((isset($error['number'])) && ( $error['number'])) {
		    $response_message['FbUserVerification']['ErrorCode'] = '002';
		    $response_message['FbUserVerification']['ErrorDesc'] = 'userId should contain only digit.';
		}
		break;
		
	    case"fbSearchUsers":
		if ((isset($error['number'])) && ( $error['number'])) {
		    $response_message['fbSearchUsers']['ErrorCode'] = '001';
		    $response_message['fbSearchUsers']['ErrorDesc'] = 'userId should contain only digit.';
		}
		break;
		
	    case"inviteContacts":
		if ((isset($error['number'])) && ( $error['number'])) {
		    $response_message['inviteContacts']['ErrorCode'] = '001';
		    $response_message['inviteContacts']['ErrorDesc'] = 'userId should contain only digit.';
		}
		break;		
            case "ProfileParentComment":
                if ((isset($error['successful'])) && ($error['successful'])) {
                    $response_message['ProfileParentComment']['SuccessCode'] = '000';
                    $response_message['ProfileParentComment']['SuccessDesc'] = 'List of Posts.';
                }
                if ((isset($error['successful'])) && (!$error['successful'])) {
                    $response_message['ProfileParentComment']['ErrorCode'] = '002';
                    $response_message['ProfileParentComment']['ErrorDesc'] = 'Id doesn not present in our database.';
                }
                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['ProfileParentComment']['ErrorCode'] = '001';
                    $response_message['ProfileParentComment']['ErrorDesc'] = 'Id should contain only digit.';
                }
                break;

            case "ProfileSubComments":
                if ((isset($error['successful'])) && ($error['successful'])) {
                    $response_message['ProfileSubComments']['SuccessCode'] = '000';
                    $response_message['ProfileSubComments']['SuccessDesc'] = 'List of Posts.';
                }
                if ((isset($error['successful'])) && (!$error['successful'])) {
                    $response_message['ProfileSubComments']['ErrorCode'] = '002';
                    $response_message['ProfileSubComments']['ErrorDesc'] = 'Main comment has been deleted from our database.';
                }
                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['ProfileSubComments']['ErrorCode'] = '001';
                    $response_message['ProfileSubComments']['ErrorDesc'] = 'Id should contain only digit.';
                }
                break;

            case"PostCommentOnProfile":
                if ((isset($error['successful'])) && ($error['successful'])) {
                    $response_message['PostCommentOnProfile']['SuccessCode'] = '000';
                    $response_message['PostCommentOnProfile']['SuccessDesc'] = 'Comment posted successfully.';
                }
                if ((isset($error['successful'])) && (!$error['successful'])) {
                    $response_message['PostCommentOnProfile']['ErrorCode'] = '003';
                    $response_message['PostCommentOnProfile']['ErrorDesc'] = 'Main comment has been deleted from our database or user is not in the network.';
                }
                if ((isset($error['PostCommentOnProfile']['successful_fin'])) && (!$error['PostCommentOnProfile']['successful_fin'])) {
                    $response_message['PostCommentOnProfile']['ErrorCode'] = '004';
                    $response_message['PostCommentOnProfile']['ErrorDesc'] = 'Your comment has not been posted.';
                }
                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['PostCommentOnProfile']['ErrorCode'] = '001';
                    $response_message['PostCommentOnProfile']['ErrorDesc'] = 'Id should contain only digit.';
                }
                if ((isset($error['blank'])) && ($error['blank'])) {
                    $response_message['PostCommentOnProfile']['ErrorCode'] = '002';
                    $response_message['PostCommentOnProfile']['ErrorDesc'] = 'Field should not be blank.';
                }
                break;
            case "DeleteProfileMessage":
                if ((isset($error['successful'])) && ($error['successful'])) {
                    $response_message['DeleteProfileMessage']['SuccessCode'] = '000';
                    $response_message['DeleteProfileMessage']['SuccessDesc'] = 'Post deleted successfully.';
                }
                if ((isset($error['successful'])) && (!$error['successful'])) {
                    $response_message['DeleteProfileMessage']['ErrorCode'] = '002';
                    $response_message['DeleteProfileMessage']['ErrorDesc'] = 'Post does not exist in our Database';
                }

                if ((isset($error['DeleteProfileMessage']['successful_fin'])) && (!$error['DeleteProfileMessage']['successful_fin'])) {
                    $response_message['DeleteProfileMessage']['ErrorCode'] = '003';
                    $response_message['DeleteProfileMessage']['ErrorDesc'] = 'Post has not been deleted.';
                }
                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['DeleteProfileMessage']['ErrorCode'] = '001';
                    $response_message['DeleteProfileMessage']['ErrorDesc'] = 'Id should contains only numbers';
                }

                break;

            case "Entourage":

                break;

            case "Events":
                if ((isset($error['successful'])) && ($error['successful'])) {
                    $response_message['Events']['SuccessCode'] = '000';
                    $response_message['Events']['SuccessDesc'] = 'List of Events.';
                }
                if ((isset($error['successful']) && (!$error['successful']))) {
                    $response_message['Events']['ErrorCode'] = '001';
                    $response_message['Events']['ErrorDesc'] = 'No Events Found.';
                }
                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['Events']['ErrorCode'] = '002';
                    $response_message['Events']['ErrorDesc'] = 'Id should be numeric value';
                }

                if ((isset($error['blank'])) && ($error['blank'])) {
                    $response_message['Events']['ErrorCode'] = '003';
                    $response_message['Events']['ErrorDesc'] = 'Field should not be blank.';
                }
                if ((isset($error['empty'])) && ($error['empty'])) {
                    $response_message['Events']['ErrorCode'] = '004';
                    $response_message['Events']['ErrorDesc'] = 'GPS unavailable !';
                }
                break;

            case "SearchEvent":
                if ((isset($error['successful'])) && ($error['successful'])) {
                    $response_message['SearchEvent']['SuccessCode'] = '000';
                    $response_message['SearchEvent']['SuccessDesc'] = 'Search Event Resultset.';
                }

                if ((isset($error['successful']) && (!$error['successful']))) {
                    $response_message['SearchEvent']['ErrorCode'] = '001';
                    $response_message['SearchEvent']['ErrorDesc'] = 'No Events Found.';
                }

                if ((isset($error['blank']) && ($error['blank']))) {
                    $response_message['SearchEvent']['ErrorCode'] = '003';
                    $response_message['SearchEvent']['ErrorDesc'] = 'No Search Criteria given.';
                }

                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['SearchEvent']['ErrorCode'] = '002';
                    $response_message['SearchEvent']['ErrorDesc'] = 'Id should be numeric value';
                }

            case "EventDetails":
                if ((isset($error['successful'])) && ($error['successful'])) {
                    $response_message['EventDetails']['SuccessCode'] = '000';
                    $response_message['EventDetails']['SuccessDesc'] = 'Events Details.';
                }

                if ((isset($error['successful']) && (!$error['successful']))) {
                    $response_message['EventDetails']['ErrorCode'] = '001';
                    $response_message['EventDetails']['ErrorDesc'] = 'No Event Detail Found.';
                }
                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['EventDetails']['ErrorCode'] = '002';
                    $response_message['EventDetails']['ErrorDesc'] = 'Id should be numeric value';
                }
                break;

	case "FBStates":
                if ((isset($error['successful'])) && ($error['successful'])) {
                    $response_message['FBStates']['SuccessCode'] = '000';
                    $response_message['FBStates']['SuccessDesc'] = 'states listed successfully.';
                }

                if ((isset($error['successful']) && (!$error['successful']))) {
                    $response_message['FBStates']['ErrorCode'] = '001';
                    $response_message['FBStates']['ErrorDesc'] = 'No state found for this country Code';
                }
                if ((isset($error['blank'])) && ($error['blank'])) {
                    $response_message['FBStates']['ErrorCode'] = '002';
                    $response_message['FBStates']['ErrorDesc'] = 'field should not be left blank.';
                }
                break;
		
	case "FBCities":
                if ((isset($error['successful'])) && ($error['successful'])) {
                    $response_message['FBCities']['SuccessCode'] = '000';
                    $response_message['FBCities']['SuccessDesc'] = 'cities listed successfully.';
                }

                if ((isset($error['successful']) && (!$error['successful']))) {
                    $response_message['FBCities']['ErrorCode'] = '001';
                    $response_message['FBCities']['ErrorDesc'] = 'No city found for this state Code';
                }
                if ((isset($error['blank'])) && ($error['blank'])) {
                    $response_message['FBCities']['ErrorCode'] = '002';
                    $response_message['FBCities']['ErrorDesc'] = 'field should not be left blank.';
                }
                break;

            case "EventCommentList":

                if ((isset($error['successful'])) && ($error['successful'])) {
                    $response_message['EventCommentList']['SuccessCode'] = '000';
                    $response_message['EventCommentList']['SuccessDesc'] = 'List of Event comments.';
                }

                if ((isset($error['successful']) && (!$error['successful']))) {
                    $response_message['EventCommentList']['ErrorCode'] = '001';
                    $response_message['EventCommentList']['ErrorDesc'] = 'No comments found';
                }

                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['EventCommentList']['ErrorCode'] = '002';
                    $response_message['EventCommentList']['ErrorDesc'] = 'Id should be numeric value';
                }
                break;

            case "EventPostComment":

                if ((isset($error['successful'])) && ($error['successful'])) {
                    $response_message['EventPostComment']['SuccessCode'] = '000';
                    $response_message['EventPostComment']['SuccessDesc'] = 'Comment posted successfully.';
                }

                if ((isset($error['successful']) && (!$error['successful']))) {
                    $response_message['EventPostComment']['ErrorCode'] = '001';
                    $response_message['EventPostComment']['ErrorDesc'] = 'Failed to post comment';
                }

                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['EventPostComment']['ErrorCode'] = '002';
                    $response_message['EventPostComment']['ErrorDesc'] = 'Id should be numeric value';
                }

                if ((isset($error['blank'])) && ($error['blank'])) {
                    $response_message['EventPostComment']['ErrorCode'] = '003';
                    $response_message['EventPostComment']['ErrorDesc'] = 'Field should not be blank.';
                }
                break;

            case "EventReplyComment":

                if ((isset($error['successful'])) && ($error['successful'])) {
                    $response_message['EventReplyComment']['SuccessCode'] = '000';
                    $response_message['EventReplyComment']['SuccessDesc'] = 'Comment posted successfully.';
                }

                if ((isset($error['successful']) && (!$error['successful']))) {
                    $response_message['EventReplyComment']['ErrorCode'] = '001';
                    $response_message['EventReplyComment']['ErrorDesc'] = 'Failed to post comment';
                }

                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['EventReplyComment']['ErrorCode'] = '002';
                    $response_message['EventReplyComment']['ErrorDesc'] = 'Id should be numeric value';
                }

                if ((isset($error['blank'])) && ($error['blank'])) {
                    $response_message['EventReplyComment']['ErrorCode'] = '003';
                    $response_message['EventReplyComment']['ErrorDesc'] = 'Field should not be blank.';
                }
                break;

            case "EventParentChildComment":

                if ((isset($error['successful'])) && ($error['successful'])) {
                    $response_message['EventParentChildComment']['SuccessCode'] = '000';
                    $response_message['EventParentChildComment']['SuccessDesc'] = 'Comment posted successfully.';
                }

                if ((isset($error['successful']) && (!$error['successful']))) {
                    $response_message['EventParentChildComment']['ErrorCode'] = '001';
                    $response_message['EventParentChildComment']['ErrorDesc'] = 'Failed to post comment';
                }

                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['EventParentChildComment']['ErrorCode'] = '002';
                    $response_message['EventParentChildComment']['ErrorDesc'] = 'Id should be numeric value';
                }

                if ((isset($error['blank'])) && ($error['blank'])) {
                    $response_message['EventParentChildComment']['ErrorCode'] = '003';
                    $response_message['EventParentChildComment']['ErrorDesc'] = 'Field should not be blank.';
                }
                break;

            case "EventViewGuestList":

                if ((isset($error['successful'])) && ($error['successful'])) {
                    $response_message['EventViewGuestList']['SuccessCode'] = '000';
                    $response_message['EventViewGuestList']['SuccessDesc'] = 'List of Guests.';
                }

                if ((isset($error['successful']) && (!$error['successful']))) {
                    $response_message['EventViewGuestList']['ErrorCode'] = '001';
                    $response_message['EventViewGuestList']['ErrorDesc'] = 'No Guest List Found.';
                }
                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['EventViewGuestList']['ErrorCode'] = '002';
                    $response_message['EventViewGuestList']['ErrorDesc'] = 'Id should be numeric value.';
                }

                break;

            case "EventAddGuestList":

                if ((isset($error['successful'])) && ($error['successful'])) {
                    $response_message['EventAddGuestList']['SuccessCode'] = '000';
                    $response_message['EventAddGuestList']['SuccessDesc'] = 'List of Events.';
                }

                if ((isset($error['successful']) && (!$error['successful']))) {
                    $response_message['EventAddGuestList']['ErrorCode'] = '001';
                    $response_message['EventAddGuestList']['ErrorDesc'] = 'Failed to Add Guest List.';
                }

                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['EventAddGuestList']['ErrorCode'] = '002';
                    $response_message['EventAddGuestList']['ErrorDesc'] = 'Id should be numeric value.';
                }

                break;

            case "Messages":

                if ((isset($error['successful'])) && ($error['successful'])) {
                    $response_message['Messages']['SuccessCode'] = '000';
                    $response_message['Messages']['SuccessDesc'] = 'successful';
                }

                if ((isset($error['successful']) && (!$error['successful']))) {
                    $response_message['Messages']['ErrorCode'] = '001';
                    $response_message['Messages']['ErrorDesc'] = 'no user found';
                }

                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['Messages']['ErrorCode'] = '002';
                    $response_message['Messages']['ErrorDesc'] = 'Id should contains only numbers';
                }
                break;


            case "DeleteMessage":

                if ((isset($error['successful'])) && ($error['successful'])) {
                    $response_message['DeleteMessage']['SuccessCode'] = '000';
                    $response_message['DeleteMessage']['SuccessDesc'] = 'Message deleted successfully.';
                }

                if ((isset($error['successful']) && (!$error['successful']))) {
                    $response_message['DeleteMessage']['ErrorCode'] = '001';
                    $response_message['DeleteMessage']['ErrorDesc'] = 'no record found';
                }

                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['DeleteMessage']['ErrorCode'] = '002';
                    $response_message['DeleteMessage']['ErrorDesc'] = 'Id should contains only numbers';
                }
                break;
				
            case "getAllMessageList":

                if ((isset($error['successful'])) && ($error['successful'])) {
                    $response_message['getAllMessageList']['SuccessCode'] = '000';
                    $response_message['getAllMessageList']['SuccessDesc'] = 'Message viewed.';
                }

                if ((isset($error['successful']) && (!$error['successful']))) {
                    $response_message['getAllMessageList']['ErrorCode'] = '001';
                    $response_message['getAllMessageList']['ErrorDesc'] = 'no message found';
                }

                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['getAllMessageList']['ErrorCode'] = '002';
                    $response_message['getAllMessageList']['ErrorDesc'] = 'Id should contains only numbers';
                }
                break;
		
            case "MessageDetails":

                if ((isset($error['successful'])) && ($error['successful'])) {
                    $response_message['MessageDetails']['SuccessCode'] = '000';
                    $response_message['MessageDetails']['SuccessDesc'] = 'Message viewed.';
                }

                if ((isset($error['successful']) && (!$error['successful']))) {
                    $response_message['MessageDetails']['ErrorCode'] = '001';
                    $response_message['MessageDetails']['ErrorDesc'] = 'no message found';
                }

                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['MessageDetails']['ErrorCode'] = '002';
                    $response_message['MessageDetails']['ErrorDesc'] = 'Id should contains only numbers';
                }
                break;


            case "sendMessage":

                if ((isset($error['successful'])) && ($error['successful'])) {
                    $response_message['sendMessage']['SuccessCode'] = '000';
                    $response_message['SendMessage']['SuccessDesc'] = 'Message sent.';
                }

                if ((isset($error['successful']) && (!$error['successful']))) {
                    $response_message['SendMessage']['ErrorCode'] = '001';
                    $response_message['SendMessage']['ErrorDesc'] = 'message can not be send';
                }

                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['SendMessage']['ErrorCode'] = '002';
                    $response_message['SendMessage']['ErrorDesc'] = 'Id should contains only numbers';
                }
                break;

            case "replyMessage":

                if ((isset($error['successful'])) && ($error['successful'])) {
                    $response_message['replyMessage']['SuccessCode'] = '000';
                    $response_message['replyMessage']['SuccessDesc'] = 'Message replyed.';
                }

                if ((isset($error['successful']) && (!$error['successful']))) {
                    $response_message['replyMessage']['ErrorCode'] = '001';
                    $response_message['replyMessage']['ErrorDesc'] = 'message can not be replyed';
                }

                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['replyMessage']['ErrorCode'] = '002';
                    $response_message['replyMessage']['ErrorDesc'] = 'Id should contains only numbers';
                }
                break;

            case "DeletePost":
                if ((isset($error['successful'])) && ($error['successful'])) {
                    $response_message['DeletePost']['SuccessCode'] = '000';
                    $response_message['DeletePost']['SuccessDesc'] = 'Post deleted successfully.';
                }
                if ((isset($error['successful'])) && (!$error['successful'])) {
                    $response_message['DeletePost']['ErrorCode'] = '002';
                    $response_message['DeletePost']['ErrorDesc'] = 'Post does not exist in our Database';
                }
                if ((isset($error['different_user'])) && (!$error['different_user'])) {
                    $response_message['DeletePost']['ErrorCode'] = '004';
                    $response_message['DeletePost']['ErrorDesc'] = 'You can not delete this post';
                }
                if ((isset($error['DeletePost']['successful_fin'])) && (!$error['DeletePost']['successful_fin'])) {
                    $response_message['DeletePost']['ErrorCode'] = '003';
                    $response_message['DeletePost']['ErrorDesc'] = 'Post has not been deleted.';
                }
                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['DeletePost']['ErrorCode'] = '001';
                    $response_message['DeletePost']['ErrorDesc'] = 'Id should contains only numbers';
                }
                break;

            case "fowrdMessage":

                if ((isset($error['successful'])) && ($error['successful'])) {
                    $response_message['fowrdMessage']['SuccessCode'] = '000';
                    $response_message['fowrdMessage']['SuccessDesc'] = 'Message forwarded.';
                }

                if ((isset($error['successful']) && (!$error['successful']))) {
                    $response_message['fowrdMessage']['ErrorCode'] = '001';
                    $response_message['fowrdMessage']['ErrorDesc'] = 'message can not be forward';
                }

                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['fowrdMessage']['ErrorCode'] = '002';
                    $response_message['fowrdMessage']['ErrorDesc'] = 'Id should contains only numbers';
                }
                break;

            case "profileInfo":
                if ((isset($error['successful'])) && ($error['successful'])) {
                    $response_message['profileInfo']['SuccessCode'] = '000';
                    $response_message['profileInfo']['SuccessDesc'] = 'Info of a user is:';
                }
                if ((isset($error['successful'])) && (!$error['successful'])) {
                    $response_message['profileInfo']['ErrorCode'] = '002';
                    $response_message['profileInfo']['ErrorDesc'] = 'User does not exist.';
                }
                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['profileInfo']['ErrorCode'] = '001';
                    $response_message['profileInfo']['ErrorDesc'] = 'Id should contains only numbers';
                }

                break;

            case"BackStageEventList":
                if ((isset($error['successful'])) && ($error['successful'])) {
                    $response_message['BackStageEventList']['SuccessCode'] = '000';
                    $response_message['BackStageEventList']['SuccessDesc'] = 'List of events.';
                }
                if ((isset($error['successful'])) && (!$error['successful'])) {
                    $response_message['BackStageEventList']['ErrorCode'] = '003';
                    $response_message['BackStageEventList']['ErrorDesc'] = 'Id does not exist in our data base.';
                }
                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['BackStageEventList']['ErrorCode'] = '001';
                    $response_message['BackStageEventList']['ErrorDesc'] = 'Id should contains only numbers';
                }
                if ((isset($error['blank'])) && ($error['blank'])) {
                    $response_message['BackStageEventList']['ErrorCode'] = '002';
                    $response_message['BackStageEventList']['ErrorDesc'] = 'Field should not be blank.';
                }
                break;

            case"eventSharing":
                if ((isset($error['successful'])) && ($error['successful'])) {
                    $response_message['eventSharing']['SuccessCode'] = '000';
                    $response_message['eventSharing']['SuccessDesc'] = 'Event shared successfully.';
                }
                if ((isset($error['successful'])) && (!$error['successful'])) {
                    $response_message['eventSharing']['ErrorCode'] = '002';
                    $response_message['eventSharing']['ErrorDesc'] = 'Event does not exist in database.';
                }
                if ((isset($error['successful'])) && (!$error['successful'])) {
                    $response_message['eventSharing']['ErrorCode'] = '002';
                    $response_message['eventSharing']['ErrorDesc'] = 'Event does not exist in database.';
                }
                if ((isset($error['eventSharing']['event_share_out_of_bound'])) && ($error['eventSharing']['event_share_out_of_bound'])) {
                    $response_message['eventSharing']['ErrorCodeOutOfBound'] = '006';
                    $response_message['eventSharing']['ErrorDescOutOfBound'] = 'You are only allowed to post this event as HotPress three time. This is to prevent Spam.';
                }
                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['eventSharing']['ErrorCode'] = '001';
                    $response_message['eventSharing']['ErrorDesc'] = 'Id should contains only numbers';
                }

                if ((isset($error['blank'])) && ($error['blank'])) {
                    $response_message['eventSharing']['ErrorCode'] = '003';
                    $response_message['eventSharing']['ErrorDesc'] = 'field should not be blank.';
                }
                if ((isset($error['calender'])) && ($error['calender'])) {
                    $response_message['eventSharing']['ErrorCode'] = '005';
                    $response_message['eventSharing']['ErrorDesc'] = 'event has not been added to your calender.';
                }
                break;

            case "Save411":

                break;

//            case "Messages":
//
//                break;
//
//            case "DeleteMessage":
//
//                break;
//
//            case "MessageDetails":
//
//                break;
//
//            case "SendMessage":
//
//                break;

            case "Alerts":

                if ((isset($error['successful'])) && ($error['successful'])) {
                    $response_message['Alerts']['SuccessCode'] = '000';
                    $response_message['Alerts']['SuccessDesc'] = 'List of Alerts.';
                }

                if ((isset($error['successful']) && (!$error['successful']))) {
                    $response_message['Alerts']['ErrorCode'] = '001';
                    $response_message['Alerts']['ErrorDesc'] = 'no alert found';
                }

                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['Alerts']['ErrorCode'] = '002';
                    $response_message['Alerts']['ErrorDesc'] = 'Id should contains only numbers';
                }

                break;

            case "AlertsUpdate":

                if ((isset($error['successful'])) && ($error['successful'])) {
                    $response_message['AlertsUpdate']['SuccessCode'] = '000';
                    $response_message['AlertsUpdate']['SuccessDesc'] = 'List of Alerts.';
                }

                if ((isset($error['successful']) && (!$error['successful']))) {
                    $response_message['AlertsUpdate']['ErrorCode'] = '001';
                    $response_message['AlertsUpdate']['ErrorDesc'] = 'no alert found';
                }

                break;

            case"BSEViewGuestList":
                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['BSEViewGuestList']['ErrorCode'] = '001';
                    $response_message['BSEViewGuestList']['ErrorDesc'] = 'Id should contains only numbers';
                }
                break;

            case"BSEViewNonMemGuestList":
                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['BSEViewNonMemGuestList']['ErrorCode'] = '001';
                    $response_message['BSEViewNonMemGuestList']['ErrorDesc'] = 'Id should contains only numbers';
                }
                break;

            case"BSEGLEntourageSearch":
                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['BSEGLEntourageSearch']['ErrorCode'] = '001';
                    $response_message['BSEGLEntourageSearch']['ErrorDesc'] = 'Id should contains only numbers';
                }
                break;

            case"BSENonMemGLEntourageSearch":
                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['BSENonMemGLEntourageSearch']['ErrorCode'] = '001';
                    $response_message['BSENonMemGLEntourageSearch']['ErrorDesc'] = 'Id should contains only numbers';
                }
                break;

            case"BSEGLCheckIn":
                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['BSEGLCheckIn']['ErrorCode'] = '001';
                    $response_message['BSEGLCheckIn']['ErrorDesc'] = 'Id should contains only numbers';
                }
                if ((isset($error['blank'])) && ($error['blank'])) {
                    $response_message['BSEGLCheckIn']['ErrorCode'] = '002';
                    $response_message['BSEGLCheckIn']['ErrorDesc'] = 'Fields should not be blank.';
                }

                if ((isset($error['BSEGLCheckIn']['successful_fin'])) && (!$error['BSEGLCheckIn']['successful_fin'])) {
                    $response_message['BSEGLCheckIn']['ErrorCode'] = '003';
                    $response_message['BSEGLCheckIn']['ErrorDesc'] = 'Occur some error during Check-In.';
                }
                break;

            case"BSENonMemGLCheckIn":
                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['BSENonMemGLCheckIn']['ErrorCode'] = '001';
                    $response_message['BSENonMemGLCheckIn']['ErrorDesc'] = 'Id should contains only numbers';
                }
                if ((isset($error['blank'])) && ($error['blank'])) {
                    $response_message['BSENonMemGLCheckIn']['ErrorCode'] = '002';
                    $response_message['BSENonMemGLCheckIn']['ErrorDesc'] = 'Fields should not be blank.';
                }

                break;

            case"BSEViewTblReservationList":
                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['BSEViewTblReservationList']['ErrorCode'] = '001';
                    $response_message['BSEViewTblReservationList']['ErrorDesc'] = 'Id should contains only numbers';
                }
                break;

            case"BSETRCheckInNotes":
                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['BSETRCheckInNotes']['ErrorCode'] = '001';
                    $response_message['BSETRCheckInNotes']['ErrorDesc'] = 'Id should contains only numbers';
                }
                break;

            case"BSETRViewCheckIn":
                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['BSETRViewCheckIn']['ErrorCode'] = '001';
                    $response_message['BSETRViewCheckIn']['ErrorDesc'] = 'Id should contains only numbers';
                }
                break;

            case"BSETRConfirmMessageScreen":
                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['BSETRConfirmMessageScreen']['ErrorCode'] = '001';
                    $response_message['BSETRConfirmMessageScreen']['ErrorDesc'] = 'Id should contains only numbers';
                }
                if ((isset($error['blank'])) && ($error['blank'])) {
                    $response_message['BSETRConfirmMessageScreen']['ErrorCode'] = '002';
                    $response_message['BSETRConfirmMessageScreen']['ErrorDesc'] = 'Field should not be blank.';
                }

                if ((isset($error['BSETRConfirmMessageScreen']['successful_fin'])) && (!$error['BSETRConfirmMessageScreen']['successful_fin'])) {
                    $response_message['BSETRConfirmMessageScreen']['ErrorCode'] = '003';
                    $response_message['BSETRConfirmMessageScreen']['ErrorDesc'] = 'Occur some error during Checked-In.';
                }
                break;


            case "Appearances":

                break;

            case "Venues":

                break;

            case "VenueDetails":

                break;

            case "FlagVenue":

                break;

            case "AppEntourageList":

                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['AppEntourageList']['ErrorCode'] = '001';
                    $response_message['AppEntourageList']['ErrorDesc'] = 'Id should contains only numbers';
                }
                break;


            case "AppEntourageStatus":

                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['AppEntourageStatus']['ErrorCode'] = '001';
                    $response_message['AppEntourageStatus']['ErrorDesc'] = 'Id should contains only numbers';
                }
                break;

            case "AppEntStatusComment":

                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['AppEntStatusComment']['ErrorCode'] = '001';
                    $response_message['AppEntStatusComment']['ErrorDesc'] = 'Id should contains only numbers';
                }
                break;

            case "AppearanceVenueList":

                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['AppearanceVenueList']['ErrorCode'] = '001';
                    $response_message['AppearanceVenueList']['ErrorDesc'] = 'Id should contains only numbers';
                }
                break;

            case "AppReward":

                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['AppReward']['ErrorCode'] = '001';
                    $response_message['AppReward']['ErrorDesc'] = 'Id should contains only numbers';
                }
                break;

            case "AppVenueDetail":

                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['AppVenueDetail']['ErrorCode'] = '001';
                    $response_message['AppVenueDetail']['ErrorDesc'] = 'Id should contains only numbers';
                }
                break;

            case "AppearanceVenueList":

                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['AppearanceVenueList']['ErrorCode'] = '001';
                    $response_message['AppearanceVenueList']['ErrorDesc'] = 'Id should contains only numbers';
                }
                break;

            case "AnnounceArrival":

                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['AnnounceArrival']['ErrorCode'] = '001';
                    $response_message['AnnounceArrival']['ErrorDesc'] = 'Id should contains only numbers';
                }
                if ((isset($error['match'])) && ($error['match'])) {
                    $response_message['AnnounceArrival']['ErrorCode'] = '002';
                    $response_message['AnnounceArrival']['ErrorDesc'] = 'Image must of .jpg or .jpeg type';
                }
                if ((isset($error['validImage'])) && ($error['validImage'])) {
                    $response_message['AnnounceArrival']['ErrorCode'] = '003';
                    $response_message['AnnounceArrival']['ErrorDesc'] = 'Image not in proper format';
                }
                break;


            case "DisplayPhotoTagAlert":
                if ((isset($error['successful'])) && ($error['successful'])) {
                    $response_message['DisplayPhotoTagAlert']['SuccessCode'] = '000';
                    $response_message['DisplayPhotoTagAlert']['SuccessDesc'] = 'Photo tags are.';
                }

                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['DisplayPhotoTagAlert']['ErrorCode'] = '001';
                    $response_message['DisplayPhotoTagAlert']['ErrorDesc'] = 'Id should contains only numbers';
                }
                break;

            case "RespondPhotoTagAlerts":

                if ((isset($error['successful'])) && ($error['successful'])) {
                    $response_message['RespondPhotoTagAlerts']['SuccessCode'] = '000';
                    $response_message['RespondPhotoTagAlerts']['SuccessDesc'] = 'request has been processed successfully.';
                }
                if ((isset($error['successful'])) && (!$error['successful'])) {
                    $response_message['RespondPhotoTagAlerts']['ErrorCode'] = '002';
                    $response_message['RespondPhotoTagAlerts']['ErrorDesc'] = 'Post does not exist in our Database';
                }

                if (isset($error['RespondPhotoTagAlerts']['successful_fin']) && (!$error['RespondPhotoTagAlerts']['successful_fin'])) {
                    $response_message['RespondPhotoTagAlerts']['ErrorCode'] = '003';
                    $response_message['RespondPhotoTagAlerts']['ErrorDesc'] = 'Error occurred.';
                }
                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['RespondPhotoTagAlerts']['ErrorCode'] = '001';
                    $response_message['RespondPhotoTagAlerts']['ErrorDesc'] = 'Id should contains only numbers';
                }
                break;

            case "CommentsOnPhotosParentComment":

                if ((isset($error['successful'])) && ($error['successful'])) {
                    $response_message['CommentsOnPhotosParentComment']['SuccessCode'] = '000';
                    $response_message['CommentsOnPhotosParentComment']['SuccessDesc'] = 'List of Comments.';
                }
                if ((isset($error['successful'])) && (!$error['successful'])) {
                    $response_message['CommentsOnPhotosParentComment']['ErrorCode'] = '002';
                    $response_message['CommentsOnPhotosParentComment']['ErrorDesc'] = 'Post does not exist in our Database';
                }

                if ((isset($error['number'])) && ($error['number'])) {
                    $response_message['CommentsOnPhotosParentComment']['ErrorCode'] = '001';
                    $response_message['CommentsOnPhotosParentComment']['ErrorDesc'] = 'Id should contains only numbers';
                }
                break;

            case "noData":
                if ((isset($error['nodata'])) && ($error['nodata'])) {
                    $response_message['noData']['NoRequestElementCode'] = '999';
                    $response_message['noData']['NoRequestElementDesc'] = 'No Request Parameter Provided';
                }
                break;
        }
        unset($error);
        writelog("Error:error_type():", $response_message, true);
        writelog("error_check.class.php :: error_type() : ", "End Here ", false);
        return $response_message;
    }

}

?>