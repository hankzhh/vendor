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

class Salore_Salon_Controller_Router extends Mage_Core_Controller_Varien_Router_Abstract
{
    const MIDDLE    = 0;
    const BEGINNING = 1;
    
    protected $_defaultControllers = array('new', 'login', 'theme', 'search');
    
    /**
     * Get router default request path
     * @return string
     */
    protected function _getDefaultPath() {
        return Mage::getStoreConfig('web/default/front');
    }

    public function match(Zend_Controller_Request_Http $request) {
        if (Mage::app()->getStore()->isAdmin()) 
        {
            return false;
        }
        $path = trim($request->getPathInfo(), '/');
        if ($path) {
            $p = explode('/', $path);
        } else {
            $p = explode('/', $this->_getDefaultPath());
        }
        
        //This case is for http://nailkare.com/admin/
        if( isset($p[0]) && $p[0] == 'admin') {
            $p = explode('/', trim($this->_getDefaultPath(),'/').'/'.$path);
        }
        
        $controller = 'index';
        $action = 'index';
        $params = array();
        //Check for default controller
        if ( isset( $p[1] ) && in_array( trim($p[1]), $this->_defaultControllers ) ) {
            $controller = trim($p[1]);
            
            if(isset( $p[2] ) && !empty( $p[2] )) {
                $action = trim($p[2]);
            }
            
            // set parameters from pathinfo
            for ($i = 3, $l = sizeof($p); $i < $l; $i += 2) {
                $request->setParam($p[$i], isset($p[$i+1]) ? urldecode($p[$i+1]) : '');
            }
        }
        else if( isset( $p[1] ) && !empty( $p[1] ) && $p[0] == 'salon') {
            //Check if salonurl exist
            if (!$existingRealSalon = Mage::registry('currentsalon')) {
                $existingRealSalon = Mage::getModel('salon/salon')->checkSalonUrlExists( strtolower($p[1]) );
                if ($existingRealSalon) {
                    Mage::register('currentsalon', $existingRealSalon);
                }
            }
            
            if ( $existingRealSalon ) {
                if(isset( $p[2] ) && !empty( $p[2] )) {
                    $controller = trim($p[2]);
                }
                else {
                    $controller = 'home';
                }
                if(isset( $p[3] ) && !empty( $p[3] )) {
                    $action = trim($p[3]);
                }
                // set parameters from pathinfo
                for ($i = 4, $l = sizeof($p); $i < $l; $i += 2) {
                    $request->setParam($p[$i], isset($p[$i+1]) ? urldecode($p[$i+1]) : '');
                }
            }
        }
        else if (isset( $p[0] ) && !empty( $p[0]) && $p[0] !== 'salon') {
            //Check if salonurl exist
            $existingRealSalon = Mage::registry('currentsalon');
            if (!$existingRealSalon) {
                $existingRealSalon = Mage::getModel('salon/salon')->checkSalonUrlExists( strtolower($p[0]) );
                if ($existingRealSalon) {
                    Mage::register('currentsalon', $existingRealSalon);
                    Mage::register('currentsalon_url', strtolower($p[0]));
                }
            }
            if ( $existingRealSalon ) {
                if(isset( $p[1] ) && !empty( $p[1] )) {
                    $controller = trim($p[1]);
                }
                else {
                    $controller = 'home';
                }
                if(isset( $p[2] ) && !empty( $p[2] )) {
                    $action = trim($p[2]);
                }
                // set parameters from pathinfo
                for ($i = 3, $l = sizeof($p); $i < $l; $i += 2) {
                    $request->setParam($p[$i], isset($p[$i+1]) ? urldecode($p[$i+1]) : '');
                }
            }
        }
        
        $existingRealSalon = Mage::registry('currentsalon');
        if ( $existingRealSalon && $existingRealSalon->getData('store_id') ) {
            $store = Mage::app()->getStore($existingRealSalon->getData('store_id'));
            Mage::app()->setCurrentStore($store);
        }
        
        
        $realModule = 'Salore_Salon';
        //Improve admin router
        if (strtolower($controller) == 'admin') {
            $controller = 'index';
            $action = 'index';
            
            //loop paths
            $i = 0;
            foreach ($p as $part) {
                if ( strtolower($part) == 'admin' ) {
                    if ( isset( $p[($i+1)] ) )  {
                        $controller = $p[($i+1)];
                        
                        if (isset($p[($i+2)])) {
                            $action = $p[($i+2)];
                        }
                        break;
                    }
                }
                $i++;
            }
            $base = Mage::getModuleDir('controllers', $realModule) . DS . 'admin' . DS . 'BaseController.php';
            require_once $base;
            $file = Mage::getModuleDir('controllers', $realModule) . DS . 'admin' .DS . ucfirst($controller) .'Controller.php';
            require_once $file;
            $controllerClass = $realModule . '_Admin_'.ucfirst($controller).'Controller';
            $request->setModuleName('salon');
            $request->setRouteName('salon');
            $request->setControllerName( strtolower('admin_'.$controller) );
            $request->setActionName( strtolower($action) );
            $request->setControllerModule( $realModule );
            $controllerInstance = new $controllerClass($request, $this->getFront()->getResponse());
            $request->setDispatched(true);
            $controllerInstance->dispatch($action);
        }
        else {
            //Force to load pending action if salon not yet active
            if ($existingRealSalon && $existingRealSalon->getData('approve') < 1) {
                $controller = 'home';
                $action = 'pending';
            }
            $file = Mage::getModuleDir('controllers', $realModule) . DS . ucfirst($controller) .'Controller.php';
            require_once $file;
            $controllerClass = $realModule . '_'.ucfirst($controller).'Controller';
            $request->setModuleName('salon');
            $request->setRouteName('salon');
            $request->setControllerName( strtolower($controller) );
            $request->setActionName( strtolower($action) );
            $request->setControllerModule( $realModule );
            $controllerInstance = new $controllerClass($request, $this->getFront()->getResponse());
            $request->setDispatched(true);
            $controllerInstance->dispatch($action);
        }
        
        return true;
    }
}
