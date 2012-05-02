<?php
/**
 * Group details are the twitter screen names that are members of the group
 *
 */
class Twitter_Model_Groupdetails
{
    /**
     * Group Id
     *
     * @var int
     */
    protected $_group_id;

    /**
     * Twitter name
     *
     * @var string
     */
    protected $_twitter_name;

    /**
     * Id
     *
     * @var int
     */
    protected $_id;

    /**
     * Table
     *
     * @var Zend_Db_Table_Abstract
     */
    protected $_mapper;

    public function __construct(array $options = null)
    {
        if (is_array($options)) {
            $this->setOptions($options);
        }
    }

    public function __set($name, $value)
    {
        //we camelCase the $name
        //method will be something like setTwitterName()

        $pieces = explode('_', $name);
        foreach($pieces AS $key => $row) {
            $pieces[$key] = ucfirst($row);
        }
        $name = implode('',$pieces);

        //$method = 'set' . ucfirst($name);
        $method = 'set'.$name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid group property');
        }
        $this->$method($value);
    }

    public function __get($name)
    {
        //we camelCase the $name
        //method will be something like getTwitterName()
        $pieces = explode('_', $name);
        foreach($pieces AS $key => $row) {
            $pieces[$key] = ucfirst($row);
        }
        $name = implode('',$pieces);
        //$method = 'get' . ucfirst($name);
        $method = 'get' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid group property');
        }
        return $this->$method();
    }

    public function setOptions(array $options)
    {
        $methods = get_class_methods($this);
        foreach ($options as $key => $value) {
            $pieces = explode('_', $key);
            foreach($pieces AS $piece_key => $piece_value) {
                $pieces[$piece_key] = ucfirst($piece_value);
            }

            $name = implode('',$pieces);
            $method = 'set' . $name;
            //$method = 'set' . ucfirst($key);
            if (in_array($method, $methods)) {
                $this->$method($value);
            }
        }
        return $this;
    }

    public function setId($id)
    {
        $this->_id = (int) $id;
        return $this;
    }

    public function getId()
    {
        return $this->_id;
    }

    public function setGroupId($group_id)
    {
        $this->_group_id = (int) $group_id;
        return $this;
    }

    public function getGroupId()
    {
        return $this->_group_id;
    }

    public function setTwitterName($twitter_name)
    {
        $this->_twitter_name = (string) $twitter_name;
        return $this;
    }

    public function getTwitterName()
    {
        return $this->_twitter_name;
    }

    public function setMapper($mapper)
    {
        $this->_mapper = $mapper;
        return $this;
    }

    public function getMapper()
    {
        if (null === $this->_mapper) {
            $this->setMapper(new Twitter_Model_GroupdetailsMapper());
        }
        return $this->_mapper;
    }

    public function save()
    {
        if(!$this->getMapper()->userExistsInGroup($this->_group_id,$this->_twitter_name)) {
            return $this->getMapper()->save($this);
        } else {
            return false;
        }
    }

    public function lastInsertId()
    {
        $this->id = $this->getMapper()->lastInsertId();
        return $this->id;
    }

    public function delete($groupdetails_id)
    {
        return $this->getMapper()->delete($groupdetails_id);
    }

    public function deleteGroupMembers($group_id)
    {
        return $this->getMapper()->deleteGroupMembers($group_id);
    }

    /*
    public function getGroupData($twitter_id,$timeline_obj)
    {
        return $this->getMapper()->getGroupData($twitter_id,$timeline_obj);
    }
    */


    public function getGroupMembers($twitter_id)
    {
        return $this->getMapper()->getGroupMembers($twitter_id);
    }


    public function getMembers($group_id)
    {
        return $this->getMapper()->getMembers($group_id);
    }

    public function userExistsInGroup($group_id,$twitter_name)
    {
        return $this->getMapper()->userExistsInGroup($group_id,$twitter_name);
    }
}
