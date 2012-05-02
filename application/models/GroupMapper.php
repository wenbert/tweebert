<?php
/**
 * Enter description here...
 *
 */
class Twitter_Model_GroupMapper
{
    protected $_dbTable;

    public function setDbTable($dbTable)
    {
        if (is_string($dbTable)) {
            $dbTable = new $dbTable();
        }
        if (!$dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Exception('Invalid table data gateway provided');
        }
        $this->_dbTable = $dbTable;
        return $this;
    }

    public function getDbTable()
    {
        if (null === $this->_dbTable) {
            $this->setDbTable('Twitter_Model_DbTable_Group');
        }
        return $this->_dbTable;
    }

    /**
     * Save a group
     *
     * @param Twitter_Model_Group $twitter
     * @return unknown
     */
    public function save(Twitter_Model_Group $twitter)
    {
        $data = array(
            'group_name'   => $twitter->getGroupName(),
            'twitter_name' => $twitter->getTwitterName(),
            'twitter_id' => $twitter->getTwitterId(),
            'weight' => $twitter->getWeight()
        );

        if (null === ($id = $twitter->getId())) {
            unset($data['id']);
            if(!$this->getDbTable()->insert($data)) return false;
        } else {
            if(!$this->getDbTable()->update($data, array('id = ?' => $id))) return false;
        }

        return true;
    }

    /**
     * Delete a group
     *
     * @param unknown_type $group_id
     * @return unknown
     */
    public function delete($group_id)
    {
        $where = $this->getDbTable()->getAdapter()->quoteInto('id = ?', $group_id);
        if(!$this->getDbTable()->delete($where)) {
            return false;
        }
        //throw new Exception(print_r($result,true));

        $groupdetails_model = new Twitter_Model_Groupdetails();
        $groupdetails_model->deleteGroupMembers($group_id);

        return true;
    }

    public function lastInsertId()
    {
        $result = $this->getDbTable()->getAdapter()->lastInsertId();
        return $result;
    }

    /*
    public function find($id, Twitter_Model_Group $twitter)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return;
        }
        $row = $result->current();
        $twitter->setId($row->id)
                  ->setGroupName($row->group_name)
                  ->setTwitterUsername($row->twitter_username)
                  ->setWeight($row->weight);
    }
    */

    public function fetchAll()
    {
        $resultSet = $this->getDbTable()->fetchAll();
        $entries   = array();
        foreach ($resultSet as $row) {
            $entry = new Twitter_Model_Group();
            $entry->setId($row->id)
                  ->setGroupName($row->group_name)
                  ->setTwitterName($row->twitter_name)
                  ->setTwitterId($row->twitter_id)
                  ->setWeight($row->weight)
                  ->setMapper($this);
            $entries[] = $entry;
        }

        //throw new Exception(print_r($result,true));
        return $entries;
    }

    /**
     * Fetch a group using a twitter_id
     *
     * @param unknown_type $twitter_id
     * @return unknown
     */
    public function fetchGroup($twitter_id)
    {
        $sql = $this->getDbTable()->select()->where('twitter_id = ?',$twitter_id)->order('weight');
        $result = $this->getDbTable()->fetchAll($sql);
        //throw new Exception(print_r($result,true));
        return $result;
    }

    /**
     * Fetch a group including all members
     *
     * @param unknown_type $twitter_id
     * @return unknown
     */
    public function fetchGroupMembersAndData($twitter_id)
    {
        //$sql = $this->getDbTable()->select()->where('twitter_id = ?',$twitter_id)->order('weight');

        $sql = "SELECT
                g.group_name, g.weight, g.twitter_id ,
                gd.twitter_name, gd.group_id
                FROM groups g
                INNER JOIN group_details gd ON g.id = gd.group_id
                WHERE g.twitter_id = '".$twitter_id."'
                ORDER BY g.weight ASC";
        $result = $this->getDbTable()->getAdapter()->fetchAll($sql);
        //throw new Exception(print_r($result,true));
        return $result;
    }

}