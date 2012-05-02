<?php
/*
class Twitter_Model_TweetsMapper
{
    protected $_twitter_id;
    
    public function __construct()
    {
        $twitterAuth = new Zend_Session_Namespace('Twitter_Auth');
        
        if(!isset($twitterAuth->twitter_id)) {
            $this->_twitter_id = 'Test_Twitter_Id';
        } else {
            $this->_twitter_id = $twitterAuth->twitter_id;
        }
    }
    
    public function saveTweetIdsInCookie($timeline_obj)
    {
        //Make sure that $row is the ID of the tweet.
        //Refactor this part as needed.
        
        //Maybe Zend Framework has a way to handle cookies.
        //Use that. To avoid namespace issues, etc.
        
        //Zend_Debug::dump($timeline_obj);
        //die('xxx');
        foreach($timeline_obj->status AS $key=>$row) {
            $_COOKIE[$this->_twitter_id]['tweet_ids'][] = (string)$row->id;
        }
        return $_COOKIE[$this->_twitter_id];
    }
    
    public function markTweetAsRead($tweet_id) 
    {
        //make sure that no duplicate tweet_ids are in the cookie :P
        if(!in_array($tweet_id,$_COOKIE[$this->_twitter_id]['tweet_ids'])) {
            $_COOKIE[$this->_twitter_id]['tweet_ids'][] = $tweet_id;
            return true;
        }
        return false;
    }
    
    public function unmarkTweetAsRead($tweet_id)
    {
        if($key = array_search($tweet_id,$_COOKIE[$this->_twitter_id]['tweet_ids'])) {
            unset($_COOKIE[$this->_twitter_id]['tweet_ids'][$key]);
            return true;
        }
        return false;
    }
    
    public function isTweetRead($tweet_id)
    {
        if(in_array($tweet_id,$_COOKIE[$this->_twitter_id]['tweet_ids'])) {
            return true;
        }
        return false;
    }
}
*/