<?php
/**
 * Determines whether a tweet is new or old.
 * It checks the $_COOKIE from the previous refresh/request
 * True if new, False is old.
 *
 */
class Zend_View_Helper_Readify extends Zend_View_Helper_Abstract
{
    public function Readify($created_at)
    {
        $twitterAuth = new Zend_Session_Namespace('Twitter_Auth');
        if(isset($_COOKIE[$twitterAuth->twitter_id]['timestamp'])) {
            if((int)strtotime($created_at) < (int)$_COOKIE[$twitterAuth->twitter_id]['timestamp']) {
                return true;
            } else {
                return false;
            }
        }
    }
}