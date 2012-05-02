<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
        $this->view->twitterAuth = new Zend_Session_Namespace('Twitter_Auth');
        //$oauth = new Zend_Session_Namespace('Twitter_Oauth');
        //Zend_Debug::dump($oauth->twitter_access_token);
    }
    
    

}

