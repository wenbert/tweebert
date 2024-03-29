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
 * @version    $Id: Client.php 17748 2009-08-22 12:46:10Z padraic $
 */

/** Zend_Oauth */
require_once 'Zend/Oauth.php';

/** Zend_Http_Client */
require_once 'Zend/Http/Client.php';

/** Zend_Oauth_Http_Utility */
require_once 'Zend/Oauth/Http/Utility.php';

/** Zend_Oauth_Config */
require_once 'Zend/Oauth/Config.php';

/**
 * @category   Zend
 * @package    Zend_Oauth
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Oauth_Client extends Zend_Http_Client
{

    /**
     * Holds the current OAuth Configuration set encapsulated in an instance
     * of Zend_Oauth_Config; it's not a Zend_Config instance since that level
     * of abstraction is unnecessary and doesn't let me escape the accessors
     * and mutators anyway!
     *
     * @var Zend_Oauth_Config
     */
    protected $_config = null;

    /**
     * Constructor; creates a new HTTP Client instance which itself is
     * just a typical Zend_Http_Client subclass with some OAuth icing to
     * assist in automating OAuth parameter generation, addition and
     * cryptographioc signing of requests.
     *
     * @param array $oauthOptions
     * @param string $uri
     * @param array|Zend_Config $config
     * @return void
     */
    public function __construct(array $oauthOptions, $uri = null, $config = null)
    {
        parent::__construct($uri, $config);
        $this->_config = new Zend_Oauth_Config;
        if (!is_null($oauthOptions)) {
            if ($oauthOptions instanceof Zend_Config) {
                $oauthOptions = $oauthOptions->toArray();
            }
            $this->_config->setOptions($oauthOptions);
        }
    }

    /**
     * Same as Zend_Http_Client::setMethod() except it also creates an
     * Oauth specific reference to the method type.
     * Might be defunct and removed in a later iteration.
     *
     * @param string $method
     * @return Zend_Http_Client
     */
    public function setMethod($method = self::GET)
    {
        if ($method == self::GET) {
            $this->setRequestMethod(self::GET);
        } elseif($method == self::POST) {
            $this->setRequestMethod(self::POST);
        }
        return parent::setMethod($method);
    }

    /**
     * Same as Zend_Http_Client::request() except just before the request is
     * executed, we automatically append any necessary OAuth parameters and
     * sign the request using the relevant signature method.
     *
     * @param string $method
     * @return Zend_Http_Response
     */
    public function request($method = null)
    {
        if (!is_null($method)) {
            $this->setMethod($method);
        }
        $this->prepareOauth();
        //var_dump($this); exit;
        return parent::request();
    }

    /**
     * Performs OAuth preparation on the request before sending.
     * This primarily means taking a request, correctly encoding and signing
     * all parameters, and applying the correct OAuth scheme to the method
     * being used.
     *
     * @return void
     */
    public function prepareOauth()
    {
        $requestScheme = $this->getRequestScheme();
        $requestMethod = $this->getRequestMethod();
        $query = null;
        if ($requestScheme == Zend_Oauth::REQUEST_SCHEME_HEADER) {
            $params = array();
            if (!empty($this->paramsGet)) {
                $params = array_merge($params, $this->paramsGet);
                $query = $this->getToken()->toQueryString(
                    $this->getUri(true), $this->_config, $params
                );
            }
            if (!empty($this->paramsPost)) {
                $params = array_merge($params, $this->paramsPost);
                $query = $this->getToken()->toQueryString(
                    $this->getUri(true), $this->_config, $params
                );
            }
            $oauthHeaderValue = $this->getToken()->toHeader(
                $this->getUri(true), $this->_config, $params
            );
            $this->setHeaders('Authorization', $oauthHeaderValue);
        } elseif ($requestScheme == Zend_Oauth::REQUEST_SCHEME_POSTBODY) {
            if ($requestMethod == self::GET) {
                require_once 'Zend/Oauth/Exception.php';
                throw new Zend_Oauth_Exception('The client is configured to'
                . 'pass OAuth parameters through a POST body but request method'
                . 'is set to GET');
            }
            $raw = $this->getToken()->toQueryString(
                $this->getUri(true), $this->_config, $this->paramsPost
            );
            $this->setRawData($raw);
            $this->paramsPost = array();
        } elseif ($requestScheme == Zend_Oauth::REQUEST_SCHEME_QUERYSTRING) {
            $this->getUri()->setQuery('');
            $query = $this->getToken()->toQueryString(
                $this->getUri(true), $this->_config, $this->paramsGet
            );
            $this->getUri()->setQuery($query);
            $this->paramsGet = array();
        } else {
            require_once 'Zend/Oauth/Exception.php';
            throw new Zend_Oauth_Exception('Invalid request scheme: '
            . $requestScheme);
        }
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
