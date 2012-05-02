<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Oauth
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Token.php 10526 2008-07-29 11:07:19Z padraic $
 */

/** Zend_Oauth_Http_Utility */
require_once 'Zend/Oauth/Http/Utility.php';

/**
 * @category   Zend
 * @package    Zend_Oauth
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Oauth_Token
{

    const TOKEN_PARAM_KEY = 'oauth_token';

    const TOKEN_SECRET_PARAM_KEY = 'oauth_token_secret';

    protected $_params = array();

    protected $_response = null;

    protected $_httpUtility = null;

    /**
     * Constructor; basic setup for any Token subclass.
     *
     * @param Zend_Http_Response $response
     * @param Zend_Oauth_Http_Utility $utility
     * @return void
     */
    public function __construct(Zend_Http_Response $response = null,
        Zend_Oauth_Http_Utility $utility = null)
    {
        if (!is_null($response)) {
            $this->_response = $response;
            $params = $this->_parseParameters($response);
            if (count($params) > 0) {
                $this->setParams($params);
            }
        }
        if (!is_null($utility)) {
            $this->_httpUtility = $utility;
        } else {
            $this->_httpUtility = new Zend_Oauth_Http_Utility;
        }
    }

    /**
     * Attempts to validate the Token parsed from the HTTP response - really
     * it's just very basic existence checks which are minimal.
     *
     * @return bool
     */
    public function isValid()
    {
        if (isset($this->_params[self::TOKEN_PARAM_KEY])
            && !empty($this->_params[self::TOKEN_PARAM_KEY])
            && isset($this->_params[self::TOKEN_SECRET_PARAM_KEY])) {
            return true;
        }
        return false;
    }

    /**
     * Return the HTTP response object used to initialise this instance.
     *
     * @return Zend_Http_Response
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * Sets the value for the this Token's secret which may be used when signing
     * requests with this Token.
     *
     * @param string $secret
     * @return void
     */
    public function setTokenSecret($secret)
    {
        $this->setParam(self::TOKEN_SECRET_PARAM_KEY, $secret);
    }

    /**
     * Retrieve this Token's secret which may be used when signing
     * requests with this Token.
     *
     * @return string
     */
    public function getTokenSecret()
    {
        return $this->getParam(self::TOKEN_SECRET_PARAM_KEY);
    }

    /**
     * Sets the value for a parameter (e.g. token secret or other) and run
     * a simple filter to remove any trailing newlines.
     *
     * @param string $key
     * @param string $value
     * @return void
     */
    public function setParam($key, $value)
    {
        $this->_params[$key] = trim($value, "\n");
    }

    /**
     * Sets the value for some parameters (e.g. token secret or other) and run
     * a simple filter to remove any trailing newlines.
     *
     * @param array $params
     * @return void
     */
    public function setParams(array $params)
    {
        foreach ($params as $key=>$value) {
            $this->setParam($key, $value);
        }
    }

    /**
     * Get the value for a parameter (e.g. token secret or other).
     *
     * @param string $key
     * @return mixed
     */
    public function getParam($key)
    {
        if (isset($this->_params[$key])) {
            return $this->_params[$key];
        }
        return null;
    }

    /**
     * Sets the value for a Token.
     *
     * @param string $token
     * @return void
     */
    public function setToken($token)
    {
        $this->setParam(self::TOKEN_PARAM_KEY, $token);
    }

    /**
     * Gets the value for a Token.
     *
     * @return string
     */
    public function getToken()
    {
        return $this->getParam(self::TOKEN_PARAM_KEY);
    }

    /**
     * Generic accessor to enable access as public properties.
     *
     * @return string
     */
    public function __get($key)
    {
        return $this->getParam($key);
    }

    /**
     * Generic mutator to enable access as public properties.
     *
     * @param string $key
     * @param string $value
     * @return void
     */
    public function __set($key, $value)
    {
        $this->setParam($key, $value);
    }

    /**
     * Convert Token to a string, specifically a raw encoded query string.
     *
     * @return string
     */
    public function toString()
    {
        return $this->_httpUtility->toEncodedQueryString($this->_params);
    }
    
    /**
     * Convert Token to a string, specifically a raw encoded query string.
     * Aliases to self::toString()
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * Parse a HTTP response body and collect returned parameters
     * as raw url decoded key-value pairs in an associative array.
     *
     * @param Zend_Http_Response $response
     * @return array
     */
    protected function _parseParameters(Zend_Http_Response $response)
    {
        $params = array();
        $body = $response->getBody();
        if (empty($body)) {
            return;
        }
        // validate body based on acceptable characters...todo
        $parts = explode('&', $body);
        foreach ($parts as $kvpair) {
            $pair = explode('=', $kvpair);
            $params[rawurldecode($pair[0])] = rawurldecode($pair[1]);
        }
        return $params;
    }

}