<?php
/**
 * Enter description here...
 *
 */
class Twitter_Model_GroupdetailsMapper
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
            $this->setDbTable('Twitter_Model_DbTable_Groupdetails');
        }
        return $this->_dbTable;
    }

    /**
     * Saves a member for a group
     *
     * @param Twitter_Model_Groupdetails $twitter
     * @return unknown
     */
    public function save(Twitter_Model_Groupdetails $twitter)
    {
        $data = array(
            'group_id'   => $twitter->getGroupId(),
            'twitter_name' => $twitter->getTwitterName()
        );

        if (null === ($id = $twitter->getId())) {
            unset($data['id']);
            $this->getDbTable()->insert($data);
        } else {
            $this->getDbTable()->update($data, array('id = ?' => $id));
        }
        return true;
    }

    public function lastInsertId()
    {
        $result = $this->getDbTable()->getAdapter()->lastInsertId();
        return $result;
    }

    /**
     * Delete based on primary id
     *
     * @param unknown_type $groupdetails_id
     * @return unknown
     */
    public function delete($groupdetails_id)
    {
        $where = $this->getDbTable()->getAdapter()->quoteInto('id = ?', $groupdetails_id);
        return $this->getDbTable()->delete($where);
    }

    /**
     * Delete based on group_id
     *
     * @param unknown_type $group_id
     * @return unknown
     */
    public function deleteGroupMembers($group_id)
    {
        $where = $this->getDbTable()->getAdapter()->quoteInto('group_id = ?', $group_id);
        return $this->getDbTable()->delete($where);
    }


    /**
     * Fetch group members using a twitter_id
     *
     * @param unknown_type $twitter_id
     * @return unknown
     */
    public function getGroupMembers($twitter_id)
    {
        //get groups
        $group_model = new Twitter_Model_Group();
        $groups = $group_model->fetchGroup($twitter_id);
        //get the members of the group
        $data = array();
        $i=0;
        foreach($groups AS $group) {
            $members = $this->getMembers($group->id);
            $data[$i]['id'] = $group->id;
            $data[$i]['group_name'] = $group->group_name;
            $data[$i]['weight'] = $group->weight;
            $data[$i]['members'] = $members;
            $i++;
        }
        return $data;
    }


    public function getMembers($group_id)
    {
        //$quoted = $this->getDbTable()->getAdapter()->quote($group_id);
        $sql = $this->getDbTable()->select()->where('group_id = ?',$group_id);
        $result = $this->getDbTable()->fetchAll($sql);
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

    public function userExistsInGroup($group_id,$twitter_name)
    {
        $sql = $this->getDbTable()->select()->where('group_id = ?', $group_id)->where('twitter_name = ?', $twitter_name);

        $result = $this->getDbTable()->fetchRow($sql);

        if(0==count($result)) {
            return false;
        } else {
            return true;
        }
    }
}
