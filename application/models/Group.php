<?php

class Twitter_Model_Group
{
    /**
     * Group Name
     *
     * @var string
     */
    protected $_group_name;

    /**
     * Twitter Id
     *
     * @var string
     */
    protected $_twitter_id;

    /**
     * Twitter Name
     *
     * @var string
     */
    protected $_twitter_name;

    /**
     * Weight. Think gravity. The big numbers at the bottom. Smaller numbers float to the top.
     *
     * @var weight
     */
    protected $_weight;

    /**
     * Id
     *
     * @var int
     */
    protected $_id;

    /**
     * Enter description here...
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
        //method will be something like getTwitterId()
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

    public function setGroupName($text)
    {
        $this->_group_name = (string) $text;
        return $this;
    }

    public function getGroupName()
    {
        return $this->_group_name;
    }

    public function setTwitterName($text)
    {
        $this->_twitter_name = (string) $text;
        return $this;
    }

    public function getTwitterName()
    {
        return $this->_twitter_name;
    }

    public function setTwitterId($twitter_id)
    {
        $this->_twitter_id = (string) $twitter_id;
        return $this;
    }

    public function getTwitterId()
    {
        return $this->_twitter_id;
    }

    public function setWeight($weight)
    {
        $this->_weight = (int) $weight;
        return $this;
    }

    public function getWeight()
    {
        return $this->_weight;
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

    public function setMapper($mapper)
    {
        $this->_mapper = $mapper;
        return $this;
    }

    public function getMapper()
    {
        if (null === $this->_mapper) {
            $this->setMapper(new Twitter_Model_GroupMapper());
        }
        return $this->_mapper;
    }

    public function save()
    {
        return $this->getMapper()->save($this);
    }

    public function delete($group_id)
    {
        return $this->getMapper()->delete($group_id);
    }

    public function lastInsertId()
    {
        $this->id = $this->getMapper()->lastInsertId();
        return $this->id;
    }

    public function fetchAll()
    {
        return $this->getMapper()->fetchAll();
    }

    public function fetchGroup($twitter_id)
    {
        return $this->getMapper()->fetchGroup($twitter_id);
    }

    public function fetchGroupMembersAndData($twitter_id)
    {
        return $this->getMapper()->fetchGroupMembersAndData($twitter_id);
    }
}
