<?php
define('COMMENT_DELIMITER', '#SNL_COMMENT_SEPERATOR#');
define('REGEX_URL', '/[http\:\/\/||www\.][a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/');
define("EVENT_IMAGE_SITEURL", "http://www.socialnightlife.com/");
//define("EVENT_IMAGE_SITEURL", 'http://192.168.0.119/development/');
define("PROFILE_IMAGE_SITEURL", "http://www.socialnightlife.com/");
//define("PROFILE_IMAGE_SITEURL", 'http://192.168.0.119/development/');
define("LOCAL_FOLDER", $_SERVER['DOCUMENT_ROOT'].'/');
define('ROOT_URL','http://www.socialnightlife.com/MySNL_WebServiceV3Live/');
//define('ROOT_URL','http://192.168.0.119/MySNL_WebServicev2/');

$special_char = array("\"", "\'", "\!", "\Â", "\£", "\$", "\%", "\^", "\&", "\*", "\(", "\)", "\}", "\{", "\@", "\:", "\'", "\#", "\~", "\/", "\?", "\>", "\<", "\.", "\,", "\/", "\:", "\@", "\|", "\-", "\=", "\-", "\_", "\+", "\-", "\Â", "\¬", "\`", "\~");

global $return_codes;
$return_codes = array();

$return_codes['HotPress']['SuccessCode'] = '000';
$return_codes['HotPress']['SuccessDesc'] = 'List of Posts.';

$return_codes['HotPress']['ErrorCode'] = '001';
$return_codes['HotPress']['ErrorDesc'] = 'No post found.';

$return_codes['profileRegistration']['SuccessCode'] = '000';
$return_codes['profileRegistration']['SuccessDesc'] = 'Profile updated successfully.';

$return_codes['profileRegistration']['errorCode'] = '006';
$return_codes['profileRegistration']['errorDesc'] = 'Unable to update profile at this time.Please Try later.';

$return_codes["Events"]["SuccessCode"] = "000";
$return_codes["Events"]["SuccessDesc"] = "Event Listing Retrived.";

$return_codes["Events"]["NoRecordErrorCode"] = "700";
$return_codes["Events"]["NoRecordErrorDesc"] = "No Event Listing Found";

$return_codes["EventDetails"]["SuccessCode"] = "000";
$return_codes["EventDetails"]["SuccessDesc"] = "Event Details Retrieved.";

$return_codes["EventDetails"]["NoRecordErrorCode"] = "701";
$return_codes["EventDetails"]["NoRecordErrorDesc"] = "No Event Details Found.";

$return_codes["SearchEvent"]["NoRecordErrorCode"] = "702";
$return_codes["SearchEvent"]["NoRecordErrorDesc"] = "No Event Records Found for Given Search Criteria.";

$return_codes["SearchEvent"]["SuccessCode"] = "000";
$return_codes["SearchEvent"]["SuccessDesc"] = "Search Event Resultset.";

$return_codes["EventViewGuestList"]["SuccessCode"] = "000";
$return_codes["EventViewGuestList"]["SuccessDesc"] = "Event Guest List Retrieved.";

$return_codes["EventViewGuestList"]["NoRecordErrorCode"] = "705";
$return_codes["EventViewGuestList"]["NoRecordErrorDesc"] = "No Guest List Found.";

$return_codes["EventAddGuestList"]["SuccessCode"] = "000";
$return_codes["EventAddGuestList"]["SuccessDesc"] = "You have been added to the guest-list.  Read event description or contact event host for guest-list details. If you have added yourself to the guest-list multiple times only your last submission will be accepted.";

$return_codes["EventAddGuestList"]["FailedToAddRecordCode"] = "706";
$return_codes["EventAddGuestList"]["FailedToAddRecordDesc"] = "Failed to add guest list.";

$return_codes["EventAddGuestList"]["FailedToAddRecordCodeNo"] = "001";
$return_codes["EventAddGuestList"]["FailedToAddRecordDescNo"] = "Not Added To GuestList.";

$return_codes["EventAddGuestList"]["FailedToAddRecordCodeMayBe"] = "002";
$return_codes["EventAddGuestList"]["FailedToAddRecordDescMayBe"] = "May be Added To GuestList.";

$return_codes["EventAddGuestList"]["FailedToAddRecordCode"] = "706";
$return_codes["EventAddGuestList"]["FailedToAddRecordDesc"] = "Failed to add guest list.";

$return_codes["EventRemoveGuestList"]["SuccessCode"] = "000";
$return_codes["EventRemoveGuestList"]["SuccessDesc"] = "Guest List Deleted Successfully.";

$return_codes["EventRemoveGuestList"]["FailedToAddRecordCode"] = "710";
$return_codes["EventRemoveGuestList"]["FailedToAddRecordDesc"] = "Failed to delete guest list.";

$return_codes["EventComments"]["SuccessCode"] = "000";
$return_codes["EventComments"]["SuccessDesc"] = "List of Comments.";

$return_codes["EventComments"]["FailedToAddRecordCode"] = "707";
$return_codes["EventComments"]["FailedToAddRecordDesc"] = "No comments found.";

$return_codes["EventPostComment"]["SuccessCode"] = "000";
$return_codes["EventPostComment"]["SuccessDesc"] = "Commented successfully.";

$return_codes["EventPostComment"]["FailedToAddRecordCode"] = "708";
$return_codes["EventPostComment"]["FailedToAddRecordDesc"] = "can not be commented.";

$return_codes["EventParentChildComment"]["SuccessCode"] = "000";
$return_codes["EventParentChildComment"]["SuccessDesc"] = "Comments Listed successfully.";

$return_codes["EventParentChildComment"]["FailedToAddRecordCode"] = "711";
$return_codes["EventParentChildComment"]["FailedToAddRecordDesc"] = "Comment can not be Listed.";

$return_codes["EventReplyComment"]["SuccessCode"] = "000";
$return_codes["EventReplyComment"]["SuccessDesc"] = "Commented successfully.";

$return_codes["EventReplyComment"]["FailedToAddRecordCode"] = "710";
$return_codes["EventReplyComment"]["FailedToAddRecordDesc"] = "can not be replied.";

$return_codes["EventCommentDelete"]["SuccessCode"] = "000";
$return_codes["EventCommentDelete"]["SuccessDesc"] = "Deleted successfully.";

$return_codes["EventCommentDelete"]["FailedToAddRecordCode"] = "709";
$return_codes["EventCommentDelete"]["FailedToAddRecordDesc"] = "No comment found.";

$return_codes["Messages"]["SuccessCode"] = "000";
$return_codes["Messages"]["SuccessDesc"] = "Messages Listed successfully.";

$return_codes["Messages"]["FailedToAddRecordCode"] = "600";
$return_codes["Messages"]["FailedToAddRecordDesc"] = "No Message found.";

$return_codes["getAllMessageList"]["SuccessCode"] = "000";
$return_codes["getAllMessageList"]["SuccessDesc"] = "Messages Listed successfully.";

$return_codes["getAllMessageList"]["FailedToAddRecordCode"] = "600";
$return_codes["getAllMessageList"]["FailedToAddRecordDesc"] = "No Message found.";

$return_codes["MessageDetails"]["SuccessCode"] = "000";
$return_codes["MessageDetails"]["SuccessDesc"] = "Message Detail Retrieved.";

$return_codes["MessageDetails"]["FailedToAddRecordCode"] = "601";
$return_codes["MessageDetails"]["FailedToAddRecordDesc"] = "No Message Detail Found.";

$return_codes["sendMessage"]["SuccessCode"] = "000";
$return_codes["sendMessage"]["SuccessDesc"] = "Message Sent successfully.";

$return_codes["sendMessage"]["FailedToAddRecordCode"] = "602";
$return_codes["sendMessage"]["FailedToAddRecordDesc"] = "Message can not be sent.";

$return_codes["replyMessage"]["SuccessCode"] = "000";
$return_codes["replyMessage"]["SuccessDesc"] = "Message Replied successfully.";

$return_codes["replyMessage"]["FailedToAddRecordCode"] = "603";
$return_codes["replyMessage"]["FailedToAddRecordDesc"] = "Message can not be replied.";

$return_codes["DeleteMessage"]["SuccessCode"] = "000";
$return_codes["DeleteMessage"]["SuccessDesc"] = "Message Deleted successfully.";

$return_codes["DeleteMessage"]["FailedToAddRecordCode"] = "604";
$return_codes["DeleteMessage"]["FailedToAddRecordDesc"] = "Message can not be Deleted.";

$return_codes["Alerts"]["SuccessCode"] = "000";
$return_codes["Alerts"]["SuccessDesc"] = "Alert Listed successfully.";

$return_codes["Alerts"]["FailedToAddRecordCode"] = "501";
$return_codes["Alerts"]["FailedToAddRecordDesc"] = "No Alert Found.";

$return_codes["AlertsUpdate"]["SuccessCode"] = "000";
$return_codes["AlertsUpdate"]["SuccessDesc"] = "Alert Updated successfully.";

$return_codes["AlertsUpdate"]["FailedToAddRecordCode"] = "502";
$return_codes["AlertsUpdate"]["FailedToAddRecordDesc"] = "Alert Can Not be Updated.";

$return_codes['AlertsClear']['SuccessCode'] = "000";
$return_codes["AlertsClear"]["SuccessDesc"] = "Alert Cleared successfully.";

$return_codes["AlertsClear"]["FailedToAddRecordCode"] = "503";
$return_codes["AlertsClear"]["FailedToAddRecordDesc"] = "Alert Can Not be Cleared.";

$return_codes["HotPressAlert"]["SuccessCode"] = "000";
$return_codes["HotPressAlert"]["SuccessDesc"] = "Hotpress Alert Listed successfully.";

$return_codes["HotPressAlert"]["FailedToAddRecordCode"] = "904";
$return_codes["HotPressAlert"]["FailedToAddRecordDesc"] = "No Hotpress Alert Found.";

$return_codes["CommentAlert"]["SuccessCode"] = "000";
$return_codes["CommentAlert"]["SuccessDesc"] = "Comment Alert Listed successfully.";

$return_codes["CommentAlert"]["FailedToAddRecordCode"] = "905";
$return_codes["CommentAlert"]["FailedToAddRecordDesc"] = "No Comment Alert Found.";

$return_codes["AppEntourageList"]["SuccessCode"] = "000";
$return_codes["AppEntourageList"]["SuccessDesc"] = "Appearance Entourage list Listed successfully.";

$return_codes["AppEntourageList"]["FailedToAddRecordCode"] = "800";
$return_codes["AppEntourageList"]["FailedToAddRecordDesc"] = "No Appearance Found.";

$return_codes["AppEntourageStatus"]["SuccessCode"] = "000";
$return_codes["AppEntourageStatus"]["SuccessDesc"] = "Appearance Entourage Status Listed successfully.";

$return_codes["AppEntourageStatus"]["FailedToAddRecordCode"] = "806";
$return_codes["AppEntourageStatus"]["FailedToAddRecordDesc"] = "No Appearance Post found.";

$return_codes["AppEntStatusComment"]["SuccessCode"] = "000";
$return_codes["AppEntStatusComment"]["SuccessDesc"] = "Appearance Entourage Status commented successfully.";

$return_codes["AppEntStatusComment"]["FailedToAddRecordCode"] = "807";
$return_codes["AppEntStatusComment"]["FailedToAddRecordDesc"] = "Appearance Status could not be commented.";

$return_codes["AppEntStatusCommentList"]["SuccessCode"] = "000";
$return_codes["AppEntStatusCommentList"]["SuccessDesc"] = "Appearance Entourage Status comments Listed successfully.";

$return_codes["AppEntStatusCommentList"]["FailedToAddRecordCode"] = "808";
$return_codes["AppEntStatusCommentList"]["FailedToAddRecordDesc"] = "Appearance Status Comments could not be Listed.";

$return_codes["AppGetAllEventTag"]["SuccessCode"] = "000";
$return_codes["AppGetAllEventTag"]["SuccessDesc"] = "Appearance Tag Events Listed successfully.";

$return_codes["AppGetAllEventTag"]["FailedToAddRecordCode"] = "809";
$return_codes["AppGetAllEventTag"]["FailedToAddRecordDesc"] = "No events found";


$return_codes["AppearanceVenueList"]["SuccessCode"] = "000";
$return_codes["AppearanceVenueList"]["SuccessDesc"] = "Appearance Venues Listed successfully.";

$return_codes["AppearanceVenueList"]["FailedToAddRecordCode"] = "801";
$return_codes["AppearanceVenueList"]["FailedToAddRecordDesc"] = "No Appearance Venues Found.";

$return_codes["AnnounceArrival"]["SuccessCode"] = "000";
$return_codes["AnnounceArrival"]["SuccessDesc"] = "Appearance Announce Status Saved Successfully.";

$return_codes["AnnounceArrival"]["FailedToAddRecordCode"] = "802";
$return_codes["AnnounceArrival"]["FailedToAddRecordDesc"] = "Appearance Venue could not be inserted.";

$return_codes["CurrentVenueStatus"]["SuccessCode"] = "000";
$return_codes["CurrentVenueStatus"]["SuccessDesc"] = "Appearance Announce Status Listed Successfully.";

$return_codes["CurrentVenueStatus"]["FailedToAddRecordCode"] = "805";
$return_codes["CurrentVenueStatus"]["FailedToAddRecordDesc"] = "Appearance Announce Status could not be Listed.";


$return_codes["AppVenueDetail"]["SuccessCode"] = "000";
$return_codes["AppVenueDetail"]["SuccessDesc"] = "Appearance Ambassador Listed successfully.";

$return_codes["AppVenueDetail"]["FailedToAddRecordCode"] = "803";
$return_codes["AppVenueDetail"]["FailedToAddRecordDesc"] = "Appearance Venue Detail not found.";

$return_codes["AppReward"]["SuccessCode"] = "000";
$return_codes["AppReward"]["SuccessDesc"] = "Appearance Reward Listed successfully.";

$return_codes["AppReward"]["FailedToAddRecordCode"] = "804";
$return_codes["AppReward"]["FailedToAddRecordDesc"] = "No rewards available. Please contact venue so they can participate and reward their guests";


$return_codes["BSEViewGuestList"]["SuccessCode"] = "000";
$return_codes["BSEViewGuestList"]["SuccessDesc"] = "List of Guests.";

$return_codes["BSEViewGuestList"]["NoRecordErrorCode"] = "700";
$return_codes["BSEViewGuestList"]["NoRecordErrorDesc"] = "No Guest List Found";


$return_codes["BSEViewNonMemGuestList"]["SuccessCode"] = "000";
$return_codes["BSEViewNonMemGuestList"]["SuccessDesc"] = "List of Non member Guests.";

$return_codes["BSEViewNonMemGuestList"]["NoRecordErrorCode"] = "700";
$return_codes["BSEViewNonMemGuestList"]["NoRecordErrorDesc"] = "No Non member Guest List Found";

$return_codes["BSEGLEntourageSearch"]["SuccessCode"] = "000";
$return_codes["BSEGLEntourageSearch"]["SuccessDesc"] = "List of Guests.";

$return_codes["BSEGLEntourageSearch"]["NoRecordErrorCode"] = "700";
$return_codes["BSEGLEntourageSearch"]["NoRecordErrorDesc"] = "No Guest List Found";

$return_codes["BSENonMemGLEntourageSearch"]["SuccessCode"] = "000";
$return_codes["BSENonMemGLEntourageSearch"]["SuccessDesc"] = "List of Guests.";

$return_codes["BSENonMemGLEntourageSearch"]["NoRecordErrorCode"] = "700";
$return_codes["BSENonMemGLEntourageSearch"]["NoRecordErrorDesc"] = "No Guest List Found";

$return_codes["BSEGLCheckIn"]["SuccessCode"] = "000";
$return_codes["BSEGLCheckIn"]["SuccessDesc"] = "Checked-In successfully.";

$return_codes["BSEGLCheckIn"]["NoRecordErrorCode"] = "700";
$return_codes["BSEGLCheckIn"]["NoRecordErrorDesc"] = "No Records Found";

$return_codes["BSENonMemGLCheckIn"]["SuccessCode"] = "000";
$return_codes["BSENonMemGLCheckIn"]["SuccessDesc"] = "Checked-In successfully.";

$return_codes["BSENonMemGLCheckIn"]["NoRecordErrorCode"] = "700";
$return_codes["BSENonMemGLCheckIn"]["NoRecordErrorDesc"] = "No Records Found";


$return_codes["BSEViewTblReservationList"]["SuccessCode"] = "000";
$return_codes["BSEViewTblReservationList"]["SuccessDesc"] = "List of Tables.";

$return_codes["BSEViewTblReservationList"]["NoRecordErrorCode"] = "700";
$return_codes["BSEViewTblReservationList"]["NoRecordErrorDesc"] = "No Records Found";

$return_codes["BSETRCheckInNotes"]["SuccessCode"] = "000";
$return_codes["BSETRCheckInNotes"]["SuccessDesc"] = "Checked-In successfully.";

$return_codes["BSETRCheckInNotes"]["NoRecordErrorCode"] = "700";
$return_codes["BSETRCheckInNotes"]["NoRecordErrorDesc"] = "No checkin notes is available.";

$return_codes["BSETRViewCheckIn"]["SuccessCode"] = "000";
$return_codes["BSETRViewCheckIn"]["SuccessDesc"] = "CheckIn Details.";

$return_codes["BSETRViewCheckIn"]["NoRecordErrorCode"] = "700";
$return_codes["BSETRViewCheckIn"]["NoRecordErrorDesc"] = "No Records Found";

$return_codes["BSETRConfirmMessageScreen"]["SuccessCode"] = "000";
$return_codes["BSETRConfirmMessageScreen"]["SuccessDesc"] = "Checked In successfully.";

$return_codes["BSETRConfirmMessageScreen"]["NoRecordErrorCode"] = "700";
$return_codes["BSETRConfirmMessageScreen"]["NoRecordErrorDesc"] = "No Records Found";

$return_codes["RemoveFriend"]["SuccessCode"] = "000";
$return_codes["RemoveFriend"]["SuccessDesc"] = "Friend removed successfully.";

$return_codes["RemoveFriend"]["NoRecordErrorCode"] = "700";
$return_codes["RemoveFriend"]["NoRecordErrorDesc"] = "No Records Found";

$return_codes["DeletePhotoComment"]["SuccessCode"] = "000";
$return_codes["DeletePhotoComment"]["SuccessDesc"] = "Post has been deleted successfully.";

$return_codes["DeletePhotoComment"]["NoRecordErrorCode"] = "700";
$return_codes["DeletePhotoComment"]["NoRecordErrorDesc"] = "No Records Found";

$return_codes["DeleteEventComment"]["SuccessCode"] = "000";
$return_codes["DeleteEventComment"]["SuccessDesc"] = "Post has been deleted successfully.";

$return_codes["DeleteEventComment"]["NoRecordErrorCode"] = "700";
$return_codes["DeleteEventComment"]["NoRecordErrorDesc"] = "No Records Found";

$return_codes["TakeOverProfile"]["SuccessCode"] = "000";
$return_codes["TakeOverProfile"]["SuccessDesc"] = "Take Over profile has been saved successfully.";

$return_codes["TakeOverProfile"]["NoRecordErrorCode"] = "700";
$return_codes["TakeOverProfile"]["NoRecordErrorDesc"] = "No Records Found";

$return_codes["TagsOnPhoto"]["NoRecordErrorCode"] = "700";
$return_codes["TagsOnPhoto"]["NoRecordErrorDesc"] = "No Records Found";

$return_codes["TagPhoto"]["NoRecordErrorCode"] = "700";
$return_codes["TagPhoto"]["NoRecordErrorDesc"] = "No Records Found";


$return_codes["RemoveTag"]["SuccessCode"] = "000";
$return_codes["RemoveTag"]["SuccessDesc"] = "Tags are removed successfully.";

$return_codes["RemoveTag"]["NoRecordErrorCode"] = "900";
$return_codes["RemoveTag"]["NoRecordErrorDesc"] = "Tags can not be removed.";


$return_codes["UserLogout"]["NoRecordErrorCode"] = "700";
$return_codes["UserLogout"]["NoRecordErrorDesc"] = "Logout has not been done.";

$return_codes["DeleteAppearanceComment"]["SuccessCode"] = "000";
$return_codes["DeleteAppearanceComment"]["SuccessDesc"] = "Post has been deleted successfully.";

$return_codes["DeleteAppearanceComment"]["NoRecordErrorCode"] = "700";
$return_codes["DeleteAppearanceComment"]["NoRecordErrorDesc"] = "No Records Found";

$return_codes["DisplayPhotoTagAlert"]["NoRecordErrorCode"] = "700";
$return_codes["DisplayPhotoTagAlert"]["NoRecordErrorDesc"] = "No Records Found";

$return_codes["CommentsOnPhotosParentComment"]["NoRecordErrorCode"] = "700";
$return_codes["CommentsOnPhotosParentComment"]["NoRecordErrorDesc"] = "No Records Found";

//$return_codes["RespondPhotoTagAlerts"]["SuccessCode"] = "000";
//$return_codes["RespondPhotoTagAlerts"]["SuccessDesc"] = "Post has been deleted successfully.";

$return_codes["RespondPhotoTagAlerts"]["NoRecordErrorCode"] = "700";
$return_codes["RespondPhotoTagAlerts"]["NoRecordErrorDesc"] = "No Records Found";

$return_codes["AllEntourageList"]["SuccessCode"] = "000";
$return_codes["AllEntourageList"]["SuccessDesc"] = "Entourage Listed Successfully.";

$return_codes["AllEntourageList"]["FailedToAddRecordCode"] = "003";
$return_codes["AllEntourageList"]["FailedToAddRecordDesc"] = "No Mutual Friends Found.";

$return_codes["GetBadges"]["SuccessCode"] = "000";
$return_codes["GetBadges"]["SuccessDesc"] = "badges listed successfully.";

$return_codes["GetBadges"]["NoRecordErrorCode"] = "003";
$return_codes["GetBadges"]["NoRecordErrorDesc"] = "unable to load badges.";

$return_codes["BadgeDetails"]["SuccessCode"]='000';
$return_codes["BadgeDetails"]["SuccessDesc"]='Bottle Detail Listed successfully.';

$return_codes["BadgeDetails"]["FailedToAddRecordCode"]='001';
$return_codes["BadgeDetails"]["FailedToAddRecordDesc"]='No detail found for this Bottle.';


$return_codes["FBStates"]["SuccessCode"] = "000";
$return_codes["FBStates"]["SuccessDesc"] = "states listed successfully.";

$return_codes["FBStates"]["NoRecordErrorCode"] = "001";
$return_codes["FBStates"]["NoRecordErrorDesc"] = "No state found for this country Code.";

$return_codes["FBCities"]["SuccessCode"] = "000";
$return_codes["FBCities"]["SuccessDesc"] = "cities listed successfully.";

$return_codes["FBCities"]["NoRecordErrorCode"] = "001";
$return_codes["FBCities"]["NoRecordErrorDesc"] = "No city found for this state Code.";


$return_codes["FbUserVerification"]["SuccessCode"] = "000";
$return_codes["FbUserVerification"]["SuccessDesc"] = "facebook user verification listed successfully.";

$return_codes["FbUserVerification"]['error']["NoRecordCode"] = "002";
$return_codes["FbUserVerification"]['error']["NoRecordDesc"] = "Please login with given email-id";

$return_codes["FbUserVerification"]["NoRecordErrorCode"] = "001";
$return_codes["FbUserVerification"]["NoRecordErrorDesc"] = "Unable to verify facebook users.";

$return_codes["fbSearchUsers"]["SuccessCode"] = "000";
$return_codes["fbSearchUsers"]["SuccessDesc"] = "facebook searched users listed successfully.";

$return_codes["fbSearchUsers"]["NoRecordErrorCode"] = "001";
$return_codes["fbSearchUsers"]["NoRecordErrorDesc"] = "Unable to find facebook users.";

$return_codes["inviteContacts"]["SuccessCode"] = "000";
$return_codes["inviteContacts"]["SuccessDesc"] = "Invitations saved successfully.";

$return_codes["inviteContacts"]['no_contacts']["NoRecordErrorCode"] = "001";
$return_codes["inviteContacts"]['no_contacts']["NoRecordErrorDesc"] = "No contact list found.";

$return_codes["inviteContacts"]['already_added']["NoRecordErrorCode"] = "002";
$return_codes["inviteContacts"]['already_added']["NoRecordErrorDesc"] = "Friends already invited.";

$return_codes["inviteContacts"]['do_no_import']["NoRecordErrorCode"] = "003";
$return_codes["inviteContacts"]['do_no_import']["NoRecordErrorDesc"] = "Unable to import contacts.";

?>