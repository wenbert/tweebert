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
 * @version    $Id: Consumer.php 17262 2009-07-28 20:01:02Z padraic $
 */

/** Zend_Oauth */
require_once 'Zend/Oauth.php';

/** Zend_Uri */
require_once 'Zend/Uri.php';

/** Zend_Oauth_Http_RequestToken */
require_once 'Zend/Oauth/Http/RequestToken.php';

/** Zend_Oauth_Http_UserAuthorisation */
require_once 'Zend/Oauth/Http/UserAuthorisation.php';

/** Zend_Oauth_Http_AccessToken */
require_once 'Zend/Oauth/Http/AccessToken.php';

/** Zend_Oauth_Token_AuthorisedRequest */
require_once 'Zend/Oauth/Token/AuthorisedRequest.php';

/** Zend_Oauth_Config */
require_once 'Zend/Oauth/Config.php';

/**
 * @category   Zend
 * @package    Zend_Oauth
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Oauth_Consumer extends Zend_Oauth
{

    public $switcheroo = false; // replace later when this works

    /**
     * Request Token retrieved from OAuth Provider
     *
     * @var Zend_Oauth_Token_Request
     */
    protected $_requestToken = null;

    /**
     * Access token retrieved from OAuth Provider
     *
     * @var Zend_Oauth_Token_Access
     */
    protected $_accessToken = null;

    /**
     * Signature method used when signing all parameters for an HTTP request
     *
     * @var string
     */
    protected $_config = null;

    /**
     * Constructor; create a new object with an optional array|Zend_Config
     * instance containing initialising options.
     *
     * @param array|Zend_Config $options
     * @return void
     */
    public function __construct($options = null)
    {
        $this->_config = new Zend_Oauth_Config;
        if (!is_null($options)) {
            if ($options instanceof Zend_Config) {
                $options = $options->toArray();
            }
            $this->_config->setOptions($options);
        }
    }

    /**
     * Attempts to retrieve a Request Token from an OAuth Provider which is
     * later exchanged for an authorised Access Token used to access the
     * protected resources exposed by a web service API.
     *
     * @param array $customServiceParameters Non-OAuth Provider-specified parameters
     * @param string $httpMethod
     * @param Zend_Oauth_Http_RequestToken $request
     * @return Zend_Oauth_Token_Request
     */
    public function getRequestToken(array $customServiceParameters = null,
        $httpMethod = null,
        Zend_Oauth_Http_RequestToken $request = null)
    {
        if (is_null($request)) {
            $request = new Zend_Oauth_Http_RequestToken($this, $customServiceParameters);
        } elseif(!is_null($customServiceParameters)) {
            $request->setParameters($customServiceParameters);
        }
        if (!is_null($httpMethod)) {
            $request->setMethod($httpMethod);
        } else {
            $request->setMethod($this->getRequestMethod());
        }
        $this->_requestToken = $request->execute();
        return $this->_requestToken;
    }

    /**
     * After a Request Token is retrieved, the user may be redirected to the
     * OAuth Provider to authorise the application's access to their
     * protected resources - the redirect URL being provided by this method.
     * Once the user has authorised the application for access, they are
     * redirected back to the application which can now exchange the previous
     * Request Token for a fully authorised Access Token.
     *
     * @param array $customServiceParameters
     * @param Zend_Oauth_Token_Request $token
     * @param Zend_OAuth_Http_UserAuthorisation $redirect
     * @return string
     */
    public function getRedirectUrl(array $customServiceParameters = null,
        Zend_Oauth_Token_Request $token = null,
        Zend_Oauth_Http_UserAuthorisation $redirect = null)
    {
        if (is_null($redirect)) {
            $redirect = new Zend_Oauth_Http_UserAuthorisation($this, $customServiceParameters);
        } elseif(!is_null($customServiceParameters)) {
            $redirect->setParameters($customServiceParameters);
        }
        if (!is_null($token)) {
            $this->_requestToken = $token;
        }
        return $redirect->getUrl();
    }

    /**
     * Rather than retrieve a redirect URL for use, e.g. from a controller,
     * one may perform an immediate redirect.
     *
     * @param array $customServiceParameters
     * @param Zend_Oauth_Http_UserAuthorisation $request
     * @return void
     */
    public function redirect(array $customServiceParameters = null,
        Zend_Oauth_Http_UserAuthorisation $request = null)
    {
        $redirectUrl = $this->getRedirectUrl($customServiceParameters, $request);
        header('Location: ' . $redirectUrl);
    }

    /**
     * Retrieve an Access Token in exchange for a previously received/authorised
     * Request Token.
     *
     * @param array $queryData GET data returned in user's redirect from Provider
     * @param Zend_Oauth_Token_Request Request Token information
     * @param string $httpMethod
     * @param Zend_Oauth_Http_AccessToken $request
     * @return Zend_Oauth_Token_Access
     */
    public function getAccessToken($queryData, Zend_Oauth_Token_Request $token,
        $httpMethod = null, Zend_Oauth_Http_AccessToken $request = null)
    {
        $authorisedToken = new Zend_Oauth_Token_AuthorisedRequest($queryData);
        if (!$authorisedToken->isValid()) {
            require_once 'Zend/Oauth/Exception.php';
            throw new Zend_Oauth_Exception(
                'Response from Service Provider is not a valid authorised request token');
        }
        if (is_null($request)) {
            $request = new Zend_Oauth_Http_AccessToken($this);
        }
        if (!is_null($httpMethod)) {
            $request->setMethod($httpMethod);
        } else {
            $request->setMethod($this->getRequestMethod());
        }
        if (isset($token)) {
            if ($authorisedToken->getToken() !== $token->getToken()) {
                require_once 'Zend/Oauth/Exception.php';
                throw new Zend_Oauth_Exception(
                    'Authorised token from Service Provider does not match
                    supplied Request Token details'
                );
            }
        } else {
            require_once 'Zend/Oauth/Exception.php';
            throw new Zend_Oauth_Exception('Request token must be passed to method');
        }
        $this->_requestToken = $token;
        $this->_accessToken = $request->execute();
        return $this->_accessToken;
    }

    /**
     * Return whatever the last Request Token retrieved was while using the
     * current Consumer instance.
     *
     * @return Zend_Oauth_Token_Request
     */
    public function getLastRequestToken()
    {
        return $this->_requestToken;
    }

    /**
     * Return whatever the last Access Token retrieved was while using the
     * current Consumer instance.
     *
     * @return Zend_Oauth_Token_Access
     */
    public function getLastAccessToken()
    {
        return $this->_accessToken;
    }

    /**
     * Alias to self::getLastAccessToken()
     *
     * @return Zend_Oauth_Token_Access
     */
    public function getToken()
    {
        return $this->_accessToken;
    }

    /**
     * Simple Proxy to the current Zend_Oauth_Config method. It's that instance
     * which holds all configuration methods and values this object also presents
     * as it's API.
     *
     * @param Zend_Http_Client $httpClient
     * @return void
     */
    public function __call($method, array $args)
    {
        if (method_exists($this->_config, $method)) {
            return call_user_func_array(array($this->_config,$method), $args);
        }
        require_once 'Zend/Oauth/Exception.php';
        throw new Zend_Oauth_Exception('Method does not exist: '.$method);
    }

}
