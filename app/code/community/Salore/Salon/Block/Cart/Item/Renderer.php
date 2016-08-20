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
class Salore_Salon_Block_Cart_Item_Renderer extends Mage_Checkout_Block_Cart_Item_Renderer {
    /**
     * Get DeleteUrl when click link removeItem on Cart
     * @return string url
     */
    public function getDeleteUrl() {
        if ($this->hasDeleteUrl()) {
            return $this->getData('delete_url');
        }
        return Mage::helper('salon')->getUrl('cart/delete')."id/{$this->getItem()->getId()}/" .  Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED . "/{$this->helper('core/url')->getEncodedUrl()}";
    }
    /**
     * Render Form View Product In order to Update Quantity Item Product
     * @return string url
     */
    public function getConfigureUrl() {
        return Mage::helper('salon')->getUrl('cart/configure')."id/{$this->getItem()->getId()}" ;
        
    }
    
}