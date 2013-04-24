<?php

class Validation {

    function email_check($email) {
        $regex = "/^[A-z0-9_]+([.][A-z0-9_]+)*[@][A-z0-9_]+([.][A-z0-9_]+)*[.][A-z]{2,4}$/";
        if (preg_match($regex, $email)) {
            return true;
        } else {
            return false;
        }
    }

    function fb_email_check($email) {
//        $regex = "/^[0-9]{1,}[@][A-z0-9_]+([.][A-z0-9_]+)*[.][A-z]{2,4}$/";
        $regex = "/^[A-z0-9_]+([.][A-z0-9_]+)*[@][A-z0-9_]+([.][A-z0-9_]+)*[.][A-z]{2,4}$/";
        if (preg_match($regex, $email)) {
            return true;
        } else {
            return false;
        }
    }

    function alphabet_check($name) {
        $regex = "/^[a-zA-Z\s]+$/";
        if (preg_match($regex, $name)) {
            return true;
        } else {
            return false;
        }
    }

//
    function field_blank_check($str) {
        if (strlen(trim($str)) > 0)
            return true;
        else
            return false;
    }

    function password($password) {
        $pass_length = strlen($password);
        if (($pass_length >= 6)) {
            return true;
        } else {
            return false;
        }
    }

    function digit_check($digit) {
        $regex = "/^-?\d*\.?\d{1,50}$/";
        if ((preg_match($regex, $digit))) {
            return true;
        } else {
            return false;
        }
    }

    function url_check($url) {
        $regex = "#^(http|https|ftp)\:\/\/([A-Z0-9][A-Z0-9_-]*(?:.[A-Z0-9][A-Z0-9_-]*)+):?(d+)\?\/\?\/$#i";

        preg_match($regex, $url, $matches);
        if ($matches) {
            return true;
        } else {
            return false;
        }
    }

    function image_check($image) {
        $regex = "#^http:\/\/(.*)\.(gif|png|jpg)$#i";
        preg_match($regex, $image, $matches);
        if ($matches) {
            return true;
        } else {
            return false;
        }
    }

    function image_type_check($image_type) {
        $regex = "/^([A-Za-z0-9_\s\W])*\.{1}(JPE?G|jpe?g|gif|png)$/";
        preg_match($regex, $image_type, $matches);
        if ($matches) {
            return true;
        } else {
            return false;
        }
    }

}

?>