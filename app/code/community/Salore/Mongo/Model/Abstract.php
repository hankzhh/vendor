<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Salore_Mongo to newer
 * versions in the future.
 *
 * @category    Salore
 * @package     Salore_Mongo
 * @author      Salore team
 * @copyright   Copyright (c) Salore team
 */

class Salore_Mongo_Model_Abstract extends Mage_Core_Model_Abstract {
    /**
     * Save object data
     *
     * @return Mage_Core_Model_Abstract
     */
    public function save() {
        /**
         * Direct deleted items to delete method
         */
        if ($this->isDeleted()) {
            return $this->delete();
        }
        if (!$this->_hasModelChanged()) {
            return $this;
        }
        $this->_getResource()->beginTransaction();
        $dataCommited = false;
        try {
            $this->_beforeSave();
            if ($this->_dataSaveAllowed) {
                $this->_getResource()->save($this);
                $this->_afterSave();
            }
            /**
            * Temporary comment this, will be implement it later
            * $this->_getResource()->addCommitCallback(array($this, 'afterCommitCallback'))
            * ->commit();
            */
            $this->_hasDataChanges = false;
            $dataCommited = true;
        } catch (Exception $e) {
            //$this->_getResource()->rollBack();
            $this->_hasDataChanges = true;
            throw $e;
        }
        if ($dataCommited) {
            $this->_afterSaveCommit();
        }
        return $this;
    }
    public function delete() {
        $this->_getResource()->beginTransaction();
        try {
            $this->_beforeDelete();
            $this->_getResource()->delete($this);
            $this->_afterDelete();
    
            $this->_getResource()->commit();
            $this->_afterDeleteCommit();
        }
        catch (Exception $e){
            $this->_getResource()->rollBack();
            throw $e;
        }
        return $this;
    }
    public function truncate($filter = array()) {
        $this->_getResource()->truncate($filter);
    }
    /**
     * Load object data
     *
     * @param   integer $id
     * @return  Mage_Core_Model_Abstract
     */
    public function load($id, $field=null) {
        $this->_beforeLoad($id, $field);

        $this->_getResource()->load($this, $id, $field);
        $this->_afterLoad();
        $this->setOrigData();
        $this->_hasDataChanges = false;
        return $this;
    }
    
    public function loadId($id, $field=null) {
        $this->_beforeLoad($id, $field);
        $this->_getResource()->loadId($this, $id, $field);
        $this->_afterLoad();
        $this->setOrigData();
        $this->_hasDataChanges = false;
        return $this;
    }
}