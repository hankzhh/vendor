<?php
class Salore_Salon_Block_Admin_Image_Uploader extends Salore_Salon_Block_Admin_Gallery_Gallery {
    protected $data = array();
    
    public function __construct(){
        
        parent::__construct();
            
    }
    
    public function getValue( $key ) {

        $this->data = $this->getData('collection');
        $rs = null;
        
        if( isset( $this->data[ trim( $key) ] ) ) {
            $rs = $this->data[ trim($key) ];
            
        }
        
        return $rs;
    }
    
}