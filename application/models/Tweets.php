<?php
/*
class Twitter_Model_Tweets
{
    protected $_id;
    protected $_twitter_id;
    protected $_tweet_id;
    protected $_tweet_status;
    protected $_mapper;
    
    public function __construct(array $options = null)
    {
        if(is_array($opionts)) {
            $this->setOptions($options);
        }
    }
    
    public function __set($name, $value)
    {
        $pieces = explode('_',$name);
        foreach($pieces AS $key => $row) {
            $pieces[$key] = ucfirst($row);
        }
        $name = implode('',$pieces);
        $method = 'set'.$name;
        if(('mapper'==$name) || !method_exists($this,$method)) {
            throw new Exception('Invalid group property');
        }
        $this->method($value);
    }
    
    public function __get($name)
    {
        $pieces = explode('_',$name);
        foreach($piees AS $key => $row) {
            $pieces[$key] = ucfirst($row);
        }
        $name = implode('',$pieces);
        $method = 'get'.$name;
        if(('mapper'==$name) || !method_exists($this,$method)) {
            throw new Exception('Invalid group property.');
        }
        return $this->$method();
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
            //$method = 'set' . ucfirst($key);
            if (in_array($method, $methods)) {
                $this->$method($value);
            }
        }
        return $this;
    }
    
    public function setMapper($mapper)
    {
        $this->_mapper = $mapper;
        return $this;
    }

    public function getMapper()
    {
        if (null === $this->_mapper) {
            $this->setMapper(new Twitter_Model_TweetsMapper());
        }
        return $this->_mapper;
    }
    
    public function saveTweetIdsInCookie($timeline_obj)
    {
        return $this->getMapper()->saveTweetIdsInCookie($timeline_obj);
    }
    
    public function markTweetAsRead($tweet_id)
    {
        return $this->getMapper()->markTweetAsRead($tweet_id);
    }
    
    public function unmarkTweetAsRead($tweet_id)
    {
        return $this->getMapper()->unmarkTweetAsRead($tweet_id);
    }
    
    public function isTweetRead($tweet_id)
    {
        return $this->getMapper()->isTweetRead($tweet_id);
    }
}
*/