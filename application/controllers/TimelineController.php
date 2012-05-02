<?php

/**
 * Timeline thingies
 * This is where the use will see the friendsTimeline and the groups
 */
class TimelineController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        $this->_flash_messenger = $this->_helper->FlashMessenger;
    }

    public function indexAction()
    {
		$oauth = new Zend_Session_Namespace('Twitter_Oauth');
		$token = unserialize($oauth->twitter_access_token);
		
        $twitter_model 		= new Twitter_Model_Twitter($token);
        $group_model   	    = new Twitter_Model_Group();
        $groupdetails_model = new Twitter_Model_Groupdetails();

        $this->view->account = $twitter_model->show($token->user_id);

        $this->view->rateLimitStatus = $twitter_model->rateLimitStatus();
        
        $this->view->friendsTimeline = $twitter_model->friendsTimeline(array('count'=>100));

        $this->view->end             = $twitter_model->endSession();
        
        $this->view->groups          = $group_model->fetchGroup($token->user_id);
        
        $this->view->groupData       = $group_model->fetchGroupMembersAndData($token->user_id);
        $this->view->messages        = $this->_flash_messenger->getMessages();
    }

    public function updateAction()
    {
		$oauth = new Zend_Session_Namespace('Twitter_Oauth');
		$token = unserialize($oauth->twitter_access_token);
        $twitter_model = new Twitter_Model_Twitter($token);

        //Yup, unsanitized data :P
        $twitter_model->update($_POST['status']);

        $this->_redirect('/timeline');
    }

}