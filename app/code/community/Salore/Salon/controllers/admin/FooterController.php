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

class Salore_Salon_Admin_FooterController extends Salore_Salon_Admin_BaseController
{
    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }
    public function newAction() {
        $this->loadLayout();
        $this->renderLayout();
    }
    public function editAction() {
        $this->loadLayout();
        $this->renderLayout();
    }
    /**
     * save footer information to mongodb from form data
     */
    public function saveAction() {
        $formData = $this->getRequest()->getParam('block');
        $footerModel = Mage::getModel('salon/footer');
        if (isset($formData) && isset($formData['title']) && $formData['title'] && isset($formData['position']) && $formData['position'] )  {
            if (isset($formData['id']) && $formData['id'])  {
                $footerModel->load($formData['id'], 'entity_id');
            }
            if (!$footerModel->getEntityId()) {
                $footerModel->setData('entity_id', uniqid());
            }
            try {
                $footerModel->setData('active', 1);
                $footerModel->setData('title', $formData['title']);
                $footerModel->setData('position', $formData['position']);
                if (isset($formData['content']) && $formData['content']) {
                    $footerModel->setData('content', $formData['content']);
                }
                $footerModel->save();
                Mage::getSingleton('core/session')->addSuccess($this->__('The block have saved successfully!'));
                $url = Mage::helper('salon')->getUrl('admin/footer');
                $this->_redirectUrl($url);
            } catch (Exception $e) {
                Mage::getSingleton('core/session')->setFormData($formData);
                Mage::getSingleton('core/session')->addError($e->getMessage());
                $this->_redirectReferer();
            }
        }
        else 
        {
            Mage::getSingleton('core/session')->setFormData($formData);
            Mage::getSingleton('core/session')->addError($this->__('Please fill full information!'));
            $this->_redirectReferer();
        }
    }
    /**
     * enable/disable a footer block in mongodb by ajax
     */
    public function ajaxactiveAction() {
        $response = array();
        $footerData = Mage::app()->getRequest()->getParams();
        $footerMongo = Mage::getModel('salon/footer');
        if(isset($footerData['id']) && $footerData['id'] && isset($footerData['active']) && $footerData['active']) {
            $footerMongo = $footerMongo->load($footerData['id'] , 'entity_id');
            $active = $footerMongo->getActive();
            if(isset($active) && $active) {
                $footerMongo->setData('active' ,'0');
                $response['message'] = "{$footerMongo->getTitle()} column is disabled successfully!";
            }
            else {
                $footerMongo->setData('active' ,'1');
                $response['message'] = "{$footerMongo->getTitle()} column is enabled successfully!";
            }
            try {
                $footerMongo->save();
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
            $response['message'] = 'This item is not exist';
            echo json_encode($response);
            return ;
        }
    }
    /**
     * delete a record from footer table from mongodb by ajax
     */
    public function ajaxdeleteAction() {
        $response = array();
        $footerId = Mage::app()->getRequest()->getParam('id');
        $footerMongo = Mage::getModel('salon/footer');
        if (isset($footerId) && $footerId) {
            $footerMongo->load($footerId , 'entity_id');
            if ($footerMongo->getEntityId()) {
                try {
                    $footerMongo->delete();
                    $response['status'] = 'SUCCESS';
                    $response['message'] = "{$footerMongo->getTitle()} item was deleted from the your salon!";
                    echo json_encode($response);
                    return ;
                } catch (Exception $e) {
                    $response['status'] = 'ERROR';
                    $response['message'] = $e->getMessage();
                    echo json_encode($response);
                    return ;
                }
            }
        }
        else {
            $response['status'] = 'ERROR';
            $response['message'] = 'This item is already exist!';
            echo json_encode($response);
            return ;
        }
    }
}