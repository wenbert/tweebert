<?php
require_once ('../application/models/Group.php');

class Model_GroupTest extends ControllerTestCase 
{
        
        /**
         * @var Model_Stats
         */
        protected $_group;
        
        public function setUp()
        {
                parent::setUp();                
                $this->_group = new Twitter_Model_Group();
        }
        
        public function testSetOptions()
        {
            $group_a = new Twitter_Model_Group(array(
                            'twitter_name'=>'wenbert',
                            'group_name'=>'my_test_group',
                            'weight'=>'1',
                            'twitter_id' => '12345',
                            'id'=>'1'
                        ));
            $this->assertEquals($group_a->getTwitterName(),'wenbert');
            $this->assertEquals($group_a->getGroupName(),'my_test_group');
            $this->assertEquals($group_a->getWeight(),'1');
            $this->assertEquals($group_a->getTwitterId(),'12345');
            $this->assertEquals($group_a->getId(),'1');
        }
        
        public function testSetterMagicThrowsException()
        {
            try {
                $this->_group->setSomethingthatdoesntexist = 'hell';
                $this->assertTrue(false); //failed
            } catch (Exception $e) {
                if($e->getMessage() == 'Invalid group property')
                    $this->assertTrue(true);
            }
        }
        
        public function testGetterMagicThrowsException()
        {
            try {
                $this->_group->getSomethingthatdoesntexist;
                $this->assertTrue(false); //failed
            } catch (Exception $e) {
                $this->assertTrue(true);
            }
        }
        
        public function testGroupMapperInvalidDataGatewayThrowsException()
        {
            try {
                $this->_group->getMapper()->setDbTable(new stdClass());
            } catch (Exception $e) {
                if($e->getMessage() == 'Invalid table data gateway provided')
                    $this->assertTrue(true);
            }
        }
        
        
        public function testCanAddUpdateDeleteGroup()
        {
            //add group
            $test_group = "Unit Test Group";
            $this->_group->group_name = $test_group;
            $this->_group->twitter_id = '123456';
            $this->_group->twitter_name = 'wenbert';
            $this->_group->weight = '1';
            $this->assertTrue($this->_group->save());
            
            //then delete the added group
            $last_id = $this->_group->lastInsertId();
            $this->_group->id = $last_id;
            $this->_group->twitter_id = '654321';
            $this->_group->twitter_name = 'wenbert2';
            $this->_group->weight = '2';
            $this->assertTrue($this->_group->save());
            
            //then delete the group that was added/updated
            $this->assertTrue($this->_group->delete($last_id));
        }
        
        public function testdeleteNotFound()
        {
            $this->assertFalse($this->_group->delete('999999999'));
        }
        
        public function testCanFetchAllGroups()
        { 
            $test_group = "Unit Test Group";
            $this->_group->group_name = $test_group;
            $this->_group->twitter_id = '123456';
            $this->_group->twitter_name = 'wenbert';
            $this->_group->weight = '1';
            $this->assertTrue($this->_group->save());
            $last_id = $this->_group->lastInsertId();
            $this->assertFalse(is_null($this->_group->fetchAll()));
            $this->assertTrue($this->_group->delete($last_id));
        }
        
        public function testWhenGroupIsNotFound()
        {
            $this->assertFalse((bool)($this->_group->fetchGroup('1')->count())); //should not be found. therefore null
        }
        
        public function testWhenGroupIsFound()
        {
            $test_group = "Unit Test Group";
            $this->_group->group_name = $test_group;
            $this->_group->twitter_id = '123456';
            $this->_group->twitter_name = 'wenbert';
            $this->_group->weight = '1';
            $this->assertTrue($this->_group->save());

            $last_id = $this->_group->lastInsertId();
            
            $this->assertTrue((bool)($this->_group->fetchGroup('123456')->count())); //should be found 
            $this->assertTrue($this->_group->delete($last_id));
        }
        
        public function testFetchGroupMembersAndData()
        {
            $test_group = "Unit Test Group";
            $this->_group->group_name = $test_group;
            $this->_group->twitter_id = '123456';
            $this->_group->twitter_name = 'wenbert';
            $this->_group->weight = '1';
            $this->assertTrue($this->_group->save());

            $last_id = $this->_group->lastInsertId();
            
            $groupdetails =  new Twitter_Model_Groupdetails(); 
            $groupdetails->group_id = $last_id;
            $groupdetails->twitter_name = 'test_twitter_name';
            $this->assertTrue($groupdetails->save());
            $this->_last_id = $groupdetails->lastInsertId();
            

            $this->assertTrue((bool)count(($this->_group->fetchGroupMembersAndData('123456'))));
            $this->assertTrue($this->_group->delete($last_id));
        }
        
        
        
        /*
        public function testCanAddCountry()
        {
                $testCountry = "Canada";
                $this->stats->AddCountry($testCountry);
                $countries = $this->stats->GetCountries();
                foreach ($countries as $country)
                {
                        if ($testCountry == $country)
                        {
                                $this->assertEquals($country , $testCountry);                   
                                break;
                        }
                                
                }
        }
        */
        


}
