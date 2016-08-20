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

class Salore_Salon_Admin_PageController extends Salore_Salon_Admin_BaseController
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
     * save information of a page to mongodb from form data 
     */
    public function saveAction() {
        $formData = $this->getRequest()->getParams();
        $pageId = $this->getRequest()->getParam('id');
        $pageModel = Mage::getModel('salon/page');
        if (isset($formData['title']) && $formData['title'] && isset($formData['content']) && $formData['content'])  {
            if (isset($pageId) && $pageId) {
                $pageModel = $pageModel->load($pageId, 'entity_id');
            }
            else {
                $pageModel->setData('entity_id', uniqid());
                $pageModel->setData('active', 1);
            }
            try {
                $pageModel->setData('title', $formData['title']);
                $pageModel->setData('content', $formData['content']);
                $pageModel->save();
                Mage::getSingleton('core/session')->addSuccess($this->__($formData['title']. ' page have saved successfully!'));
                $this->_redirectUrl(Mage::helper('salon')->getUrl('admin/page'));
            } catch (Exception $e) {
                Mage::getSingleton('core/session')->addError($e->getMessage());
                $this->_redirectReferer();
            }
        }
        else {
            Mage::getSingleton('core/session')->addError($this->__('Please fill full information!'));
            $this->_redirectReferer();
        }
    }
    /**
     * delete a record of page table from mongdb by ajax
     */
    public function ajaxDeleteAction() {
        $response = array();
        $pageId = Mage::app()->getRequest()->getParam('id');
        $pageMongo = Mage::getModel('salon/page');
        if (isset($pageId) && $pageId) {
            $pageMongo->load($pageId , 'entity_id');
            if ($pageMongo->getEntityId()) {
                try {
                    $pageMongo->delete();
                    $response['status'] = 'SUCCESS';
                    $response['message'] = "{$pageMongo->getTitle()} page was deleted from the your salon!";
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
        $response['status'] = 'ERROR';
        $response['message'] = 'This page is already exist!';
        echo json_encode($response);
        return ;
    }
    /**
     * enable/disable a page in mongodb  by ajax
     */
    public function ajaxactiveAction() {
        $response = array();
        $pageId = $this->getRequest()->getParam('id');
        $pageMongo = Mage::getModel('salon/page');
        if(isset($pageId) && $pageId ) {
            $pageMongo = $pageMongo->load($pageId, 'entity_id');
            $active = $pageMongo->getActive();
            if(isset($active) && $active) {
                $pageMongo->setData('active' ,'0');
                $response['message'] = "{$pageMongo->getTitle()} page is disabled successfully!";
            }
            else {
                $pageMongo->setData('active' ,'1');
                $response['message'] = "{$pageMongo->getTitle()} page is enabled successfully!";
            }
            try {
                $pageMongo->save();
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
            $response['message'] = 'This page item is not exist';
            echo json_encode($response);
            return ;
        }
    }
}