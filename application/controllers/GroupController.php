<?php
/**
 * Handles group requests
 * Grouping of users, etc.
 *
 */

class GroupController extends Zend_Controller_Action
{
    /**
     *
     *
     * @var unknown_type
     */
    protected $_flashMessenger = null;

	protected $_user_id;
	protected $_screen_name;

    public function init()
    {
        /* Initialize action controller here */
        $this->_flash_messenger = $this->_helper->FlashMessenger;
		$oauth = new Zend_Session_Namespace('Twitter_Oauth');
		if(isset($oauth->twitter_access_token)) {
			$token = unserialize($oauth->twitter_access_token);
			$this->_user_id = $token->user_id;
			$this->_screen_name = $token->screen_name;
		} else {
			throw new Exception('Twitter_Oauth is not set in GroupController.php');
		}

    }

    public function indexAction()
    {
        // action body
    }

    /**
     * Performs friends()
     * Minus 2 in API Calls
     */
    public function addAction()
    {
        $this->view->messages = $this->_flash_messenger->getMessages();
    }

    /**
     * Delete a group
     *
     */
    public function deleteAction()
    {
        $group_model = new Twitter_Model_Group();
        $group_model->delete($this->getRequest()->getPost('group_id'));
        $this->_flash_messenger->addMessage('You have deleted group: <b>'.$this->getRequest()->getPost('group_name').'</b>');
        $this->_redirect('/group/edit');
    }

    /**
     * Edit a group
     *
     */
    public function editAction()
    {
        $groupdetails_model   = new Twitter_Model_Groupdetails();
        $this->view->groupData = $groupdetails_model->getGroupMembers($this->_user_id);
        //Zend_Debug::dump($this->view->groupData);
        $this->view->messages = $this->_flash_messenger->getMessages();
    }

    /**
     * Saves a group after editing
     *
     */
    public function saveAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout()->disableLayout();

        $group_model = new Twitter_Model_Group();

        $group_model->twitter_id= $this->_user_id;
        $group_model->twitter_name= $this->_screen_name;
        $group_model->group_name = $this->getRequest()->getPost('group_name');
        $group_model->weight = $this->getRequest()->getPost('weight');
        $group_model->save();

        $this->_flash_messenger->addMessage('You have added group: <b>'.$this->getRequest()->getPost('group_name').'</b>');
        $this->_redirect('/group/add');
    }

    /**
     * Save friends for a group
     *
     */
    public function savefriendsAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout()->disableLayout();

        $group_model = new Twitter_Model_Group();
        $groupdetails_model = new Twitter_Model_Groupdetails();

        $group_id = $this->getRequest()->getPost('group_id');
        $redirect = '';

        if(!$this->getRequest()->getPost('twitter_names')) {
            throw new Exception('You did not select a user.');
        }

        //from the textarea
        if(!is_array($this->getRequest()->getPost('twitter_names'))) {
            $twitter_names = explode(',',$this->getRequest()->getPost('twitter_names'));
            foreach($twitter_names AS $key=>$value) {
                $twitter_names[$key] = trim($value);
                if($twitter_names[$key]=='') {
                    unset($twitter_names[$key]);
                }
            }
            $twitter_names = array_unique($twitter_names);

            if(isset($_POST['group_id'])) {
                $group_model->id = $this->getRequest()->getPost('group_id');
                $group_model->twitter_id= $this->_user_id;
                $group_model->twitter_name= $this->_user_name;
                $group_model->group_name = $this->getRequest()->getPost('group_name');
                $group_model->weight = $this->getRequest()->getPost('weight');
                $group_model->save();
            }

            //delete members who are not in the $twitter_names array
            $current_members = $groupdetails_model->getMembers($group_id);
            foreach($current_members AS $member) {
                if(!in_array($member['twitter_name'],$twitter_names)) {
                    $groupdetails_model->delete($member['id']);
                }
            }
            $redirect = 'group/edit';
            $this->_flash_messenger->addMessage('Successfully edited group: <b>'.$this->getRequest()->getPost('group_name').'</b>');

        //from the timeline
        } else {
            $twitter_names = array_unique($this->getRequest()->getPost('twitter_names'));
            $redirect = 'timeline';
            $this->_flash_messenger->addMessage('You have added member(s) to a group.');
        }

        if(count($twitter_names)<=0) {
            $twitter_names = array();
            //throw new Exception('You did not select people to add to a group. Please go back and try again.');
        }

        //save new members who are in the $twitter_names array
        reset($twitter_names);
        foreach($twitter_names AS $key => $value) {
            $groupdetails_model->group_id = $group_id;
            $groupdetails_model->twitter_name = $value;
            $groupdetails_model->save();
        }

        $this->_redirect('/'.$redirect);
    }
}