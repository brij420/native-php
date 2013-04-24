<?php

error_reporting(E_ALL);
/* all included files */
date_default_timezone_set('America/Chicago');
//date_default_timezone_set('America/Los_Angeles');
require_once('config/config.php');
require_once('config/constants.php');
require_once('config/db.php');
require_once('function/functions.php');
require_once('function/bottelstatusemail.php');
require_once('classes/error_check.class.php');
require_once('classes/validate.class.php');
require_once('classes/serversidevalidation.class.php');

$obj_error = new Error();
//if (isset($_REQUEST['xmlrequest'])) {

if (isset($_SERVER['HTTP_XML_ADDITIONAL_INFO'])) {
    $xmlrequest = ($_SERVER['HTTP_XML_ADDITIONAL_INFO']);
} else {
    $xmlrequest = trim($_REQUEST['xmlrequest']);
}
//if (DEBUG)
writelog("index.php :: Received XML Request :: ", $xmlrequest, false);
//writelog("index.php :: Received XML Request :: ", $xmlrequest, false,0,1);

//writelog("index.php ::", $_FILES['userfile'], true);

if (isset($xmlrequest) && ($xmlrequest)) {

    $xmlrequest = str_replace('\n', 'nlbr', $xmlrequest);
    $xmlrequest = trim($xmlrequest);
    $xmlrequest = stripslashes($xmlrequest);
    $xmlrequest = preg_replace('/\s+/', ' ', ($xmlrequest));
    //if (DEBUG)
    writelog("index.php :: -----------------------------------------------:: ", "start", false);
    //if (DEBUG)
    writelog("index.php :: Received XML Request :: ", $xmlrequest, false);
	
	$xmlrequest=getActualJson($xmlrequest);
	$xmlrequest=utf8_encode($xmlrequest);
	//print_r($xmlrequest);
    $xmlrequest = json_decode($xmlrequest, true);
	//print_r($xmlrequest);
	
    //if (DEBUG)
    writelog("index.php json_decode ::", $xmlrequest, true);
    // if (DEBUG)
    writelog("index.php json_decode ::", $xmlrequest, false);
		
    if ((isset($xmlrequest)) && ($xmlrequest != "")) {

        $requesttype = get_request_type($xmlrequest); //Determine type of request.
        writelog("index.php :: -----------------------------------------------:: ", "start 3", false);

        $error = array();
        $obj_validate = new Validate();

        $error = $obj_validate->validate($requesttype, $xmlrequest); //validation


        $response_message = array();
        if ((isset($error['counter'])) && ($error['counter'])) {

            // if (DEBUG)
            writelog("index.php :: Error Response : ", $error['counter'], false);
            $response = $obj_error->error_type($requesttype, $error);
        } //End of  if ((isset($error['counter'])) && ($error['counter']))
        else {
//            header('Content-Type: application/json');
            require_once("request_handler.php");
        }
        if (is_array($response) && isset($response[$requesttype]['ErrorCode']) && ($response[$requesttype]['ErrorCode'])) {

            $response_str = response_repeat_string();
            $response = '
                            {
                             ' . $response_str . '
                                     "' . $requesttype . '":{
                              "errorCode":"' . $response[$requesttype]['ErrorCode'] . '",
                              "errorMsg":"' . $response[$requesttype]['ErrorDesc'] . '"
                                     }
                            }';
        }
//        header('Content-Type: application/json');
        header('Content-type: application/json');
        echo $response;
		
		// $push = json_decode($response,true);
	// if(isset($push['AnnounceArrival'])){

	    // /* get badges future */
	    // badgesFeature($push['AnnounceArrival']['userId'],$push['AnnounceArrival']['venueId'],$push['AnnounceArrival']['time'],$push['AnnounceArrival']['announceId']);
	// }
    } //End of if ((isset($xmlrequest)) && ($xmlrequest))
    else {
        $requesttype = trim(get_request_type($xmlrequest));
        $response_str = response_repeat_string();
        $response = '{
                             "GenInfo":{
                                 "appname":"SNL",
                                 "appvtagersion":"1.0.2",
                                 "type":"Response"
                                        },
                              "JsonError":{
                                    "errorCode":"001",
                                     "errorMsg":"Json Error"
                                          }
                            }';
        //if (DEBUG)
        writelog("index.php :: Server Var : ", $_SERVER['HTTP_XML_ADDITIONAL_INFO'], false);
        //if (DEBUG)
        writelog("index.php :: Server Var : ", $xmlrequest, false);
        //if (DEBUG)
        writelog("index.php :: Server Var : ", "json request is not in proper format", false);
        header('Content-type: application/json');
        echo $response;
    }
    //if (DEBUG)
    writelog("index.php :: -----------------------------------------------:: ", "end", false);
//    }  //End of if (isset($_REQUEST['xmlrequest']))
//    else {
//        $responseHeaderStr = responseRepeatString();
//        $response = '{
//                       ' . $responseHeaderStr . '
//                         "JsonError":{
//                             "errorCode":"001",
//                             "errorMsg":"Json Error"
//                              }
//                     }';
//
//        header('Content-type: application/json');
//        echo $response;
//
//        if (LOG)
//            writelog("index.php :: Server Var : ", "json request is not in proper format", false);
//    }
    if (LOG)
        writelog("index.php :: -----------------------------------------------:: ", "End", false);
}
else {
    $error['nodata'] = true;
    //if (DEBUG)
    writelog("index.php :: -----------------------------------------------:: ", "end", false);

    $responseMessage = $obj_error->error_type("noData", $error);
    $errorResponse = '"errorCode":"' . $responseMessage['noData']['NoRequestElementCode'] . '","errorMsg":"' . $responseMessage['noData']['NoRequestElementDesc'] . '"';
    $responseHeaderStr = response_repeat_string();
    $response = $responseHeaderStr . '
                         "Error":{
                          ' . $errorResponse . '
                          }
                     }';
    header('Content-type: application/json');
    echo $response;
    if (LOG)
        writelog("index.php :: Server Var : ", "Input request is empty", false);
    if (LOG)
        writelog("index.php :: -----------------------------------------------:: ", "End", false);
}  //End of else for if (isset($_REQUEST['xmlrequest']))
?>