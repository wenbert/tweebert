<?php
/**
 * A new Twitter model that I have been trying to work on.
 * I am trying to make Zend_Oauth and Zend_Service_Twitter work
 */

require_once 'Mytwitterapp/Twitteroauth.php';

class Twitter_Model_Twitter
{
    protected $_twitter;
    protected $_username;
    protected $_password;

    public function __construct(Zend_Oauth_Token_Access $token, array $options = null, Zend_Http_Client $http_client=null)
    {
		$this->_twitter = new Mytwitterapp_Twitteroauth($token,$http_client);
        if (is_array($options)) {
            $this->setOptions($options);
        } 
    }

    public function setOptions(array $options)
    {
        $methods = get_class_methods($this);
        foreach ($options as $key => $value) {
            $pieces = explode('_', $key);
            foreach($pieces AS $piece_key => $piece_value) {
                $pieces[$piece_key] = ucfirst($piece_value);
            }
            $name = implode('',$pieces);
            $method = 'set' . $name;
            if (in_array($method, $methods)) {
                $this->$method($value);
            }
        }
        return $this;
    }

    public function setTwitter($obj)
    {
        $this->_twitter = $obj;
        return $this;
    }


    public function verifyCredentials()
    {
        return $this->_twitter->account->verifyCredentials();
    }

    public function userTimeline()
    {
        return $this->_twitter->status->userTimeline();
    }

    public function publicTimeline()
    {
        return $this->_twitter->status->publicTimeline();
    }

    public function friendsTimeline($params)
    {
        return $this->_twitter->status->friendsTimeline($params);
    }

    public function rateLimitStatus()
    {
        return $this->_twitter->account->rateLimitStatus();
    }

    public function show($twitter_id)
    {
        return $this->_twitter->user->show($twitter_id);
    }

    public function update($status)
    {
        return $this->_twitter->status->update($status);
    }

    public function endSession()
    {
        return $this->_twitter->account->endSession();
    }

    public function friends()
    {
        return $this->_twitter->user->friends();
    }
}
?>
