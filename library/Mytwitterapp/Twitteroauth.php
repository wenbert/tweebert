<?php

/**
 * I'm trying to extend Zend_Service_Twitter to use Oauth instead
 */

require_once 'Zend/Service/Twitter.php';

class Mytwitterapp_Twitteroauth extends Zend_Service_Twitter
{
	/**
	 * Oauth
	 * @var Zend_Oauth_Token_Access
	 */
	protected $_token;
	
	/**
	 * Config Array for Zend_Oauth_Consumer (i think)
	 * @var Zend_Config_Ini
	 */
	protected $_config;
	
	/**
	 * @param object Zend_Oauth_Token_Access $token
	 * @return void
	 */
	public function __construct(Zend_Oauth_Token_Access $token, Zend_Http_Client $http_client=null,array $oauth_config = null)
	{
		$config = array();
		if(!isset($oauth_config)) {
			$config = new Zend_Config_Ini(APPLICATION_INI, APPLICATION_ENV);
			$this->_config = $config->oauth->toArray();
		} else {
			$this->_config = $oauth_config;
		}
		
		$this->_token = $token;

        $this->setUri('http://twitter.com');
		
		if(!isset($http_client)) {
			self::setHttpClient($this->_token->getHttpClient($this->_config));
		} else {
			self::setHttpClient($http_client);
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
	
	
}