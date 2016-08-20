<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Salore_Salon to newer
 * versions in the future.
 *
 * @category    Salore
 * @package     Salore_Salon
 * @author      Salore team
 * @copyright   Copyright (c) Salore team
 */

class Salore_Salon_Admin_MenuController extends Salore_Salon_Admin_BaseController
{
    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }
    public function editAction() {
        $this->loadLayout();
        $this->renderLayout();
    }
    public function newAction() {
        $this->loadLayout();
        $this->renderLayout();
    }
    /**
     * save menu information from form to mongodb   
     */
/**
     * save menu information from form to mongodb   
     */
    public function saveAction() {
        $formData = $this->getRequest()->getParams();
        $menuModel = Mage::getModel('salon/menu');
            try {
                $this->setData($menuModel, $formData);
                $menuModel->save();
                Mage::getSingleton('core/session')->addSuccess($this->__($menuModel->getTitle().' menu have saved successfully!'));
                $this->_redirectUrl(Mage::helper('salon')->getUrl('admin/menu'));
                
            } catch (Exception $e) {
                Mage::getSingleton('core/session')->addError($this->__($e->getMessage()));
            }
    }
    /**
     * set data before saving to mongodb
     * @param object $menuModel
     * @param array $formData
     */
    public function setData(&$menuModel, &$formData) {
        if(isset($formData) && $formData) {
            if (isset($formData['menuid']) && $formData['menuid']) {
                $menuModel->load($formData['menuid'], 'entity_id');
            }
            else {
                $entityId = uniqid();
                $menuModel->setData('entity_id', $entityId);
            }
            if(isset($formData['title']) && $formData['title']) {
                $menuModel->setData('title', $formData['title']);
            }
            if(isset($formData['position']) && is_numeric($formData['position'])  ) {
                $menuModel->setData('position' , $formData['position']);
            }
            if(isset($formData['link']) && $formData['link']) {
                $menuModel->setData('path', $formData['link']);
            }
            if(isset($formData['active']) && $formData['active']) {
                $menuModel->setData('active', $formData['active']);
            }
            $menuModel->setData('system', '0');
        }
        else {
            Mage::getSingleton('core/session')->addError($this->__('Please fill full information!'));
            $this->_redirectReferer();
        }
    
    }
    public function ajaxdeleteAction() {
        $response = array();
        $menuId = Mage::app()->getRequest()->getParam('id');
        $menuMongo = Mage::getModel('salon/menu');
        if (isset($menuId) && $menuId) {
            $menuMongo->load($menuId , 'entity_id');
            $system = $menuMongo->getSystem();
            if (isset($system) && !$system || isset($system) && $system) {
                try {
                    $menuMongo->delete();
                    $response['status'] = 'SUCCESS';
                    $response['message'] = "{$menuMongo->getTitle()} menu was deleted from the your salon!";
                    echo json_encode($response);
                    return ;
                } catch (Exception $e) {
                    $response['status'] = 'ERROR';
                    $response['message'] = $e->getMessage();
                    echo json_encode($response);
                    return ;
                }    
            }
            else {
                $response['status'] = 'ERROR';
                $response['message'] = "{$menuMongo->getTitle()} menu can't deleted because it belongs to system";
                echo json_encode($response);
                return ;
            }
        }
        else {
            $response['status'] = 'ERROR';
            $response['message'] = 'This menu is already exist!';
            echo json_encode($response);
            return ;
        }
    }
    /**
     * enable/disable a menu from mongodb by ajax
     */
    public function ajaxactiveAction() {
        $response = array();
        $menuData = Mage::app()->getRequest()->getParams();
        $menuMongo = Mage::getModel('salon/menu');
        if(isset($menuData['id']) && $menuData['id'] && isset($menuData['active']) && $menuData['active']) {
            $menuMongo = $menuMongo->load($menuData['id'] , 'entity_id');
            $active = $menuMongo->getActive();
            if(isset($active) && $active) {
                $menuMongo->setData('active' ,'0');
                $response['message'] = "{$menuMongo->getTitle()} menu is disabled successfully!";
            }
            else {
                $menuMongo->setData('active' ,'1');
                $response['message'] = "{$menuMongo->getTitle()} menu is enabled successfully!";
            }
            try {
                $menuMongo->save();
                $response['status'] = 'SUCCESS';
                echo json_encode($response);
                return ;
            } catch (Exception $e) {
                $response['status'] = 'ERROR';
                $response['message'] = $e->getMessage();
                echo json_encode($response);
                return ;
            }
        }
        else {
            $response['status'] = 'ERROR';
            $response['message'] = 'This menu item is not exist';
            echo json_encode($response);
            return ;
        }
    }
    public function updatePositionAction() {
        $response = array();
        $menuId = Mage::app()->getRequest()->getParam('id');
        $positionValue = Mage::app()->getRequest()->getParam('value');
        $menuMongo = Mage::getModel('salon/menu');
        if (isset($menuId) && $menuId) {
            $menuMongo->load($menuId , 'entity_id');
            $menuMongo->setData('position' , $positionValue);
            try {
                $menuMongo->save();
                $response['status'] = 'SUCCESS';
                echo json_encode($response);
                return ;
            } catch (Exception $e) {
                $response['status'] = 'ERROR';
                $response['message'] = $e->getMessage();
                echo json_encode($response);
                return ;
            }
                
        }
        else {
            $response['status'] = 'ERROR';
            $response['message'] = 'This position menu is already exist!';
            echo json_encode($response);
            return ;
        }
    }
}