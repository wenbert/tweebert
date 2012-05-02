<?php

require_once ('../application/models/Twitter.php');

class Model_TwitterTest extends ControllerTestCase
{
    /**
     * @var Model_Twitter
     */
    protected $_twitter;

    protected $_oauth_token_stub;
    protected $_client_stub;
    
    protected $_twitter_stub;
    
    public function setUp()
    {
        
    }
    /*
    public function testVerifyCredentials() 
    {
        $stub = $this->getMock('Zend_Service_Twitter', array('verifyCredentials'),array(),'',FALSE);
        $stub->expects($this->once())
              ->method('verifyCredentials');

        
        //    4) testVerifyCredentials(Model_TwitterTest)
        //    Expectation failed for method name is equal to <string:getAccessToken> when invoked 1 time(s).
        //    Method was expected to be called 1 times, actually called 0 times.
        
        $oauth_token = $this->getMock('Zend_Oauth_Token_Access', array('getAccessToken'),array(),'',FALSE);
        $oauth_token->expects($this->once())
              ->method('getAccessToken');
        
        $http_client = $this->getMock('Zend_Http_Client', array('getHttpClient'));
        $http_client->expects($this->once())
              ->method('getHttpClient');
        
        $model = new Twitter_Model_Twitter($oauth_token,NULL,$http_client);
        $model->setOptions(array('twitter'=>$stub));

        $posts = $model->verifyCredentials();
    }
    */
    protected function _getCleanMock($className, $testClassName) {
        $class = new ReflectionClass($className);
        $methods = $class->getMethods();
        $stubMethods = array();
        foreach ($methods as $method) {
            if ($method->isPublic() || ($method->isProtected()
            && $method->isAbstract())) {
                $stubMethods[] = $method->getName();
            }
        }
        $mocked = $this->getMock(
            $className,
            $stubMethods,
            array(),
            $className . '_' . $testClassName . '_' . uniqid(),
            false
        );
        return $mocked;
    }
    
    public function testuserTimeline() 
    {
        $stub = $this->getMock('Zend_Service_Twitter', array('userTimeline'),array(),'',FALSE);
        $stub->expects($this->once())
              ->method('userTimeline');

        //$oauth_token_stub = $this->getMock('Zend_Oauth', array('getAccessToken'),array(),'',FALSE);
        //$oauth_token_stub->expects($this->once())
        //      ->method('getAccessToken');
        $oauth_token_stub = $this->_getCleanMock('Zend_Oauth_Token_Access','Zend_Oauth_Token_Access');

        $http_client_stub = $this->getMock('Zend_Http_Client', array('getHttpClient'));
        $http_client_stub->expects($this->once())
              ->method('getHttpClient');


        $model = new Twitter_Model_Twitter($oauth_token_stub,NULL,$http_client_stub);

        $model->setOptions(array('twitter'=>$stub));

        $posts = $model->userTimeline();
    }
    
    public function testpublicTimeline() 
    {
        $stub = $this->getMock('Zend_Service_Twitter', array('publicTimeline'),array(),'',FALSE);
        $stub->expects($this->once())
              ->method('publicTimeline');

        $model = new Twitter_Model_Twitter();
        $model->setOptions(array('twitter'=>$stub));

        $posts = $model->publicTimeline();
    }
    
    public function testfriendsTimeline() 
    {
        $stub = $this->getMock('Zend_Service_Twitter', array('friendsTimeline'),array(),'',FALSE);
        $stub->expects($this->once())
              ->method('friendsTimeline');

        $model = new Twitter_Model_Twitter();
        $model->setOptions(array('twitter'=>$stub));

        $posts = $model->friendsTimeline();
    }
    
    public function testrateLimitStatus() 
    {
        $stub = $this->getMock('Zend_Service_Twitter', array('rateLimitStatus'),array(),'',FALSE);
        $stub->expects($this->once())
              ->method('rateLimitStatus');

        $model = new Twitter_Model_Twitter();
        $model->setOptions(array('twitter'=>$stub));

        $posts = $model->rateLimitStatus();
    }
    
    public function testShow() 
    {
        $stub = $this->getMock('Zend_Service_Twitter', array('show'),array(),'',FALSE);
        $stub->expects($this->once())
              ->method('show');

        $model = new Twitter_Model_Twitter();
        $model->setOptions(array('twitter'=>$stub));

        $posts = $model->show('1');
    }
    
    public function testUpdate() 
    {
        $stub = $this->getMock('Zend_Service_Twitter', array('update'),array(),'',FALSE);
        $stub->expects($this->once())
              ->method('update');

        $model = new Twitter_Model_Twitter();
        $model->setOptions(array('twitter'=>$stub));

        $posts = $model->update('1');
    }
    
    public function testEndSession() 
    {
        $stub = $this->getMock('Zend_Service_Twitter', array('endSession'),array(),'',FALSE);
        $stub->expects($this->once())
              ->method('endSession');

        $model = new Twitter_Model_Twitter();
        $model->setOptions(array('twitter'=>$stub));

        $posts = $model->endSession();
    }
    
    public function testFriends() 
    {
        $stub = $this->getMock('Zend_Service_Twitter', array('friends'),array(),'',FALSE);
        $stub->expects($this->once())
              ->method('friends');

        $model = new Twitter_Model_Twitter();
        $model->setOptions(array('twitter'=>$stub));

        $posts = $model->friends();
    }
    

    /*
    public function setUp()
    {
        parent::setUp();
        $this->_twitter = $this->getMock('Twitter_Model_Twitter');
    }
    public function testCanVerifyCredentials()
    {
        $this->_twitter->expects($this->once())
                       ->method('verifyCredentislas'); 
        
    }
    */  
}
