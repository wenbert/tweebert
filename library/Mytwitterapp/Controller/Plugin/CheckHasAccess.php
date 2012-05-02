<?php
/**
 * Enter description here...
 *
 */
class Mytwitterapp_Controller_Plugin_CheckHasAccess extends Zend_Controller_Plugin_Abstract {
 
    /**
     * Enter description here...
     *
     * @param Zend_Controller_Request_Abstract $request
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request) 
	{
        
		$oauth = new Zend_Session_Namespace('Twitter_Oauth');
		$token = unserialize($oauth->twitter_access_token);
		$moduleName         = $this->getRequest()->getModuleName();
        $controllerName     = $this->getRequest()->getControllerName();
        $actionName         = $this->getRequest()->getActionName();
        $frontController    = Zend_Controller_Front::getInstance();

		if(
			(
				$controllerName == 'timeline' OR
				$controllerName == 'group'
			)
			AND !isset($oauth->twitter_access_token)) {
            $this->getResponse()->setHttpResponseCode(403);
            $request->setControllerName('error');
            $request->setActionName('forbidden');
        }

    }
}