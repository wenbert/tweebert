<?php
require_once ('../application/models/Twitter.php');
require_once ('../application/models/Group.php');
require_once ('../application/models/Groupdetails.php');

class Model_GroupdetailsTest extends ControllerTestCase 
{
        
        /**
         * @var Model_Stats
         */
        protected $_groupdetails;
        protected $_last_id;
        
        public function setUp()
        {
                parent::setUp();                
                $this->_groupdetails = new Twitter_Model_Groupdetails();
        }
        
        public function testSetOptions()
        {
            $group_a = new Twitter_Model_Groupdetails(array(
                            'group_id'=>'9999999',
                            'twitter_name'=>'invalid_twitter_name',
                            'id'=>'1'
                        ));
            $this->assertEquals($group_a->getGroupId(),'9999999');
            $this->assertEquals($group_a->getTwitterName(),'invalid_twitter_name');
            $this->assertEquals($group_a->getId(),'1');
        }
        
        public function testSetterMagicThrowsException()
        {
            try {
                $this->_groupdetails->setSomething = '1';
            } catch (Exception $e) {
                if($e->getMessage() == 'Invalid group property')
                    $this->assertTrue(true);
            }
        }
        
        public function testGetterMagicThrowsException()
        {
            try {
                $this->_groupdetails->getSomething;
            } catch (Exception $e) {
                if($e->getMessage() == 'Invalid group property')
                    $this->assertTrue(true);
            }
        }
        
        public function testGroupDetailsMapperInvalidDataGatewayThrowsException()
        {
            try {
                $this->_groupdetails->getMapper()->setDbTable(new stdClass());
            } catch (Exception $e) {
                if($e->getMessage() == 'Invalid table data gateway provided')
                    $this->assertTrue(true);
            }
        }
        
        public function testCanSaveAndDeleteGroupDetail()
        {
            $this->_groupdetails->group_id = '999';
            $this->_groupdetails->twitter_name = 'test_twitter_name';
            $this->assertTrue($this->_groupdetails->save());
            $this->_last_id = $this->_groupdetails->lastInsertId();

            $this->assertTrue((bool)$this->_groupdetails->delete($this->_last_id));
        }
        
        public function testCanSaveOnlyOnce()
        {
            //save one record
            $this->_groupdetails->group_id = '999';
            $this->_groupdetails->twitter_name = 'test_twitter_name';
            $this->assertTrue($this->_groupdetails->save());
            $this->_last_id = $this->_groupdetails->lastInsertId();

            //then try to save the same data
            $this->_groupdetails->group_id = '999';
            $this->_groupdetails->twitter_name = 'test_twitter_name';
            $this->assertFalse($this->_groupdetails->save());
            
            //delete the saved record
            $this->assertTrue((bool)$this->_groupdetails->delete($this->_last_id));
        }
        
        public function testCanUpdateGroupdetails()
        {
            $this->_groupdetails->group_id = '999';
            $this->_groupdetails->twitter_name = 'test_twitter_name';
            $this->assertTrue($this->_groupdetails->save());
            $this->_last_id = $this->_groupdetails->lastInsertId();
            
            $this->_groupdetails->id = $this->_last_id;
            $this->_groupdetails->group_id = '99999';
            $this->_groupdetails->twitter_name = 'test_twitter_name';
            $this->assertTrue($this->_groupdetails->save());
            
            $this->assertTrue((bool)$this->_groupdetails->delete($this->_last_id));
        }
        
        public function testCanGetGroupMembers()
        {
            //add a group
            $group = new Twitter_Model_Group();
            $group->group_name = 'mock_test_group';
            $group->twitter_id = '123456';
            $group->twitter_name = 'wenbert';
            $group->weight = '1';
            $this->assertTrue($group->save());
            $group_last_id = $group->lastInsertId();
            
            $this->assertTrue((bool)count($this->_groupdetails->getGroupMembers('123456')));
            
            $this->_groupdetails->group_id = $group_last_id;
            $this->_groupdetails->twitter_name = 'test_twitter_name';
            $this->assertTrue($this->_groupdetails->save());
            $this->_last_id = $this->_groupdetails->lastInsertId();
            
            $this->assertTrue((bool)$this->_groupdetails->delete($this->_last_id));
            $this->assertTrue($group->delete($group_last_id));
        }
        
        
        public function testCanGetMembers()
        {

            $this->_groupdetails->group_id = '999';
            $this->_groupdetails->twitter_name = 'test_twitter_name';
            $this->assertTrue($this->_groupdetails->save());
            $this->_last_id = $this->_groupdetails->lastInsertId();
            
            
            $this->assertTrue((bool)count(($this->_groupdetails->getMembers('999'))));
            $this->assertTrue((bool)$this->_groupdetails->delete($this->_last_id));
        }
        
        public function testCanDeleteGroupMembers()
        {
            $this->_groupdetails->group_id = '999';
            $this->_groupdetails->twitter_name = 'test_twitter_name';
            $this->assertTrue($this->_groupdetails->save());
            $this->_last_id = $this->_groupdetails->lastInsertId();
            
            $this->assertTrue((bool)$this->_groupdetails->deleteGroupMembers('999'));
        }
        
        public function testUserExistsInGroupIsNotFound()
        {
            //var_dump($this->_groupdetails->userExistsInGroup('99999','unknown_twitter_name'));
            $this->assertFalse($this->_groupdetails->userExistsInGroup('99999','unknown_twitter_name'));
        }
        
        /*
        public function testUserExistsInGroupIsFound()
        {
            $this->_groupdetails->group_id = '999';
            $this->_groupdetails->twitter_name = 'test_twitter_name';
            $this->assertTrue($this->_groupdetails->save());
            $this->_last_id = $this->_groupdetails->lastInsertId();
            
            $this->assertTrue((bool)count(($this->_groupdetails->userExistsInGroup('999','test_twitter_name'))));
            $this->assertTrue((bool)$this->_groupdetails->delete($this->_last_id));
        }
        */
        
        /*
        public function testCanGetGroupData()
        {
            //i provided a serialized object
            //this will authenticate to twitter i think:
            //username: tweebert_test
            //password: twitteriscool
            $twitter_model = unserialize(base64_decode('TzoyMToiVHdpdHRlcl9Nb2RlbF9Ud2l0dGVyIjoxOntzOjMxOiIAVHdpdHRlcl9Nb2RlbF9Ud2l0dGVyAF90d2l0dGVyIjtPOjIwOiJaZW5kX1NlcnZpY2VfVHdpdHRlciI6OTp7czoxOToiACoAX2F1dGhJbml0aWFsaXplZCI7YjowO3M6MTM6IgAqAF9jb29raWVKYXIiO047czoxNDoiACoAX2RhdGVGb3JtYXQiO3M6MTY6IkQsIGQgTSBZIEg6aTpzIFQiO3M6MTI6IgAqAF91c2VybmFtZSI7czoxMzoidHdlZWJlcnRfdGVzdCI7czoxMjoiACoAX3Bhc3N3b3JkIjtzOjE2OiJ0d2l0dGVyaXNjb29sAAAAIjtzOjE0OiIAKgBfbWV0aG9kVHlwZSI7TjtzOjE1OiIAKgBfbWV0aG9kVHlwZXMiO2E6Njp7aTowO3M6Njoic3RhdHVzIjtpOjE7czo0OiJ1c2VyIjtpOjI7czoxMzoiZGlyZWN0TWVzc2FnZSI7aTozO3M6MTA6ImZyaWVuZHNoaXAiO2k6NDtzOjc6ImFjY291bnQiO2k6NTtzOjg6ImZhdm9yaXRlIjt9czo4OiIAKgBfZGF0YSI7YTowOnt9czo3OiIAKgBfdXJpIjtPOjEzOiJaZW5kX1VyaV9IdHRwIjo5OntzOjEyOiIAKgBfdXNlcm5hbWUiO3M6MDoiIjtzOjEyOiIAKgBfcGFzc3dvcmQiO3M6MDoiIjtzOjg6IgAqAF9ob3N0IjtzOjExOiJ0d2l0dGVyLmNvbSI7czo4OiIAKgBfcG9ydCI7czowOiIiO3M6ODoiACoAX3BhdGgiO3M6MDoiIjtzOjk6IgAqAF9xdWVyeSI7czowOiIiO3M6MTI6IgAqAF9mcmFnbWVudCI7czowOiIiO3M6OToiACoAX3JlZ2V4IjthOjU6e3M6NzoiZXNjYXBlZCI7czoxNjoiJVtbOnhkaWdpdDpdXXsyfSI7czoxMDoidW5yZXNlcnZlZCI7czoyNDoiW0EtWmEtejAtOS1fLiF+KicoKVxbXF1dIjtzOjc6InNlZ21lbnQiO3M6NTQ6Iig/OiVbWzp4ZGlnaXQ6XV17Mn18W0EtWmEtejAtOS1fLiF+KicoKVxbXF06QCY9KyQsO10pKiI7czo0OiJwYXRoIjtzOjY2OiIoPzpcLyg/Oig/OiVbWzp4ZGlnaXQ6XV17Mn18W0EtWmEtejAtOS1fLiF+KicoKVxbXF06QCY9KyQsO10pKik/KSsiO3M6NDoidXJpYyI7czo1NjoiKD86JVtbOnhkaWdpdDpdXXsyfXxbQS1aYS16MC05LV8uIX4qJygpXFtcXTtcLz86QCY9KyQsXSkiO31zOjEwOiIAKgBfc2NoZW1lIjtzOjQ6Imh0dHAiO319fQ=='));
            
            $timeline = $twitter_model->friendsTimeline(array('count'=>20));
            
            //add group
            $group = new Twitter_Model_Group();
            $group->group_name = 'mock_test_group';
            $group->twitter_id = '123456';
            $group->twitter_name = 'wenbert';
            $group->weight = '1';
            $this->assertTrue($group->save());
            $group_last_id = $group->lastInsertId();
            
            //add group member
            $this->_groupdetails =  new Twitter_Model_Groupdetails(); 
            $this->_groupdetails->group_id = $group_last_id;
            $this->_groupdetails->twitter_name = 'wenbert'; //test will look for this name
            $this->assertTrue($this->_groupdetails->save());
            $groupdetails_last_id = $this->_groupdetails->lastInsertId();
            
            $this->assertFalse(is_null($this->_groupdetails->getGroupData('123456',$timeline)));
            
            $this->assertTrue($group->delete($group_last_id));
        }
        
        public function testCanGetGroupDataWithMockObject()
        {
            $timeline_mock = $this->getMock('Twitter_Model_Twitter');
            $timeline_mock->expects($this->once())->method('friendsTimeline');
            
            $this->assertFalse(is_null($this->_groupdetails->getGroupData('123456',$timeline_mock)));
        }
        */
        
}
