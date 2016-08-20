<?php 
/**
 * My Render
 * @author DELL
 *
 */
class Salore_Salon_Block_Adminhtml_Render_Approve extends  Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
    /**
     * Render a new string after Approve / Unapprove
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row) {
        if ($row->getData('approve') == 0) {
              $data = '<a class="salore-salon-approve"  href="#salore-salon-approve">Approve</a>';
        }
        else {
            $data = '<a class="salore-salon-approve" href="#salore-salon-approve">Unapprove</a>';
        }
        return $data;
    }
}