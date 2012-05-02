<?php
/**
 * Login controller
 * Previously, this was using Zend_Service_Twitter. It is unsafe and it requires a password.
 * Currently, I am trying to use Zend_Oauth and Zend_Service_Twitter work in this app.
 *
 */
class LoginController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $oauth = new Zend_Session_Namespace('Twitter_Oauth');
        $oauth->twitter_access_token = null;
        $oauth->twitter_request_token = null;

        $config   = new Zend_Config_Ini(APPLICATION_INI, APPLICATION_ENV);
        $consumer = new Zend_Oauth_Consumer($config->oauth);

        if (!isset($oauth->twitter_access_token)) {
            $token = $consumer->getRequestToken();
            $oauth->twitter_request_token = serialize($token);
            $consumer->redirect();
        } else {
            $this->_redirect('/timeline');
        }
    }

	/**
	 * From twitter.com after getting the token
	 * 
	 */ 
    public function callbackAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout()->disableLayout();

        $config   = new Zend_Config_Ini(APPLICATION_INI, APPLICATION_ENV);
        $consumer = new Zend_Oauth_Consumer($config->oauth);

        $oauth = new Zend_Session_Namespace('Twitter_Oauth');

        if (!empty($_GET) && isset($oauth->twitter_request_token)) {
            $token = $consumer->getAccessToken($_GET, unserialize($oauth->twitter_request_token));
            $oauth->twitter_access_token = serialize($token);
            $oauth->twitter_request_token = null;

            $this->_redirect('/timeline');
        } else {
            exit('Invalid callback request. Oops. Sorry.');
        }
    }

    public function clearoauthAction()
    {
        Zend_Session::destroy();
        //$oauth = new Zend_Session_Namespace('Twitter_Oauth');
        //$oauth->twitter_access_token = null;
        //$oauth->twitter_request_token = null;
        $this->_redirect('/');
        //header('Location: ' . URL_ROOT );
    }


    /**
     * Old way to authenticate to Twitter
     *
     */
    public function authenticateAction()
    {
        $config      = new Zend_Config_Ini(APPLICATION_INI, APPLICATION_ENV);
        $twitterAuth = new Zend_Session_Namespace('Twitter_Auth');
        $twitterCredentials = new Zend_Session_Namespace('Twitter_Credentials');
        $twitter     = new Zend_Service_Twitter($_POST['twitter_username'],$_POST['twitter_password']);
        $response    = $twitter->account->verifyCredentials();

        if(isset($response->error)) {
            //throw new Exception($response->error);
            echo 'Twitter API Error: '.$response->error;
            die();
        }

        $iv_size = mcrypt_get_iv_size(MCRYPT_XTEA, MCRYPT_MODE_ECB);
        $iv      = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $key     = $config->encryption->salt;
        $text    = $_POST['twitter_password'];

        $twitterAuth->username   = $_POST['twitter_username'];
        $twitterAuth->password   = mcrypt_encrypt(MCRYPT_XTEA, $key, $text, MCRYPT_MODE_ECB, $iv);
        $twitterAuth->twitter_id = (string)$response->id;
        $twitterAuth->twitter_load = (int)$_POST['twitter_load'];

        $this->_redirect('/timeline');
    }

    /**
     * Old way to log out
     *
     */
    public function logoutAction()
    {
        $twitter     = new Zend_Service_Twitter();
        $twitter->account->endSession();
        Zend_Session::destroy();
        $this->_redirect('/');
    }

}

