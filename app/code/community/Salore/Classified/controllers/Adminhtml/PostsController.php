<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Salore_Classified to newer
 * versions in the future.
 *
 * @category    Salore
 * @package     Salore_Classified
 * @author      Salore team
 * @copyright   Copyright (c) Salore team
 */
class Salore_Classified_Adminhtml_PostsController extends Mage_Adminhtml_Controller_Action {
    protected $editImage = false;
    
    public function _initData() {
        $posts_id = $this->getRequest ()->getParam ( 'id' );
        Mage::register ( 'posts_data', Mage::getModel ( 'classified/posts' ) );
        if (isset ( $posts_id ) && $posts_id) {
            $this->editImage = true;
            Mage::registry ( 'posts_data' )->load ( $posts_id );
        }
    }
    public function indexAction() {
        $this->loadLayout ();
        $this->renderLayout ();
    }
    public function newAction() {
        $this->_forward ( 'edit' );
    }
    public function editAction() {
        $this->_initData ();
        $this->loadLayout ()->_title ( $this->__ ( 'Posts Manager' ) )->_addContent ( $this->getLayout ()->createBlock ( 'classified/adminhtml_posts_edit' ) )->renderLayout ();
    }

    public function saveAction() {
        $flag = true;
        $requestImg = array();

        $this->_initData ();
        
        $data = $this->getRequest ()->getParams ();
        
        if($this->editImage && Mage::registry ( 'posts_data' )->getImages()){
            
            $deletedImg = isset( $data['removeImg'] ) ? $data['removeImg'] : null;
            
            if( count($data['images']) <= 0 ) {
                $requestImg = array();
            } else {
                $requestImg = Mage::registry ( 'posts_data' )->getImages();
            }

            if( !empty( $deletedImg ) ) {
                
                foreach( $deletedImg as $fileId ) {
                    if( isset( $requestImg[ trim( $fileId ) ] ) ) {
                        unset( $requestImg[ trim($fileId) ] );
                    }
                }
            }

            
        }
        

        try {
            if ( (Mage::registry ( 'posts_data' )) && (Mage::registry ( 'posts_data' )->getEntityId ()) ) {
                $id = Mage::registry ( 'posts_data' )->getEntityId ();
                $postsObj = Mage::getModel ( 'classified/posts' )->load ( $id );
                
                if ($postsObj->getIsSpecific () && empty ( $data ['is_specific'] ) ) {
                    $postsObj->setData ( 'is_specific', ( int ) 0 );
                }
            } else {
                
                $postsObj = Mage::getModel ( 'classified/posts' );
                
                $lastEntityId = Mage::helper ( 'salon' )->getLastIdCategory ( $postsObj );
                
                $postsObj->setData ( 'entity_id', ( string ) ($lastEntityId + 1) );
            }

            if (isset ( $_FILES )) {
                if( isset($_POST['images'] ) && count($_POST['images']) )  {

                    $postImg = $_POST['images'];
                    foreach( $_FILES ['file'] ['name'] as $key => $fileName) {
                        if( !isset( $postImg[trim($fileName ) ] ) )  {
                    
                            unset($_FILES['file'] ['name'][$key]);
                            unset($_FILES['file'] ['type'][$key]);
                            unset($_FILES['file'] ['tmp_name'][$key]);
                            unset($_FILES['file'] ['error'][$key]);
                            unset($_FILES['file'] ['size'][$key]);
                        }
                    }
                    
                    $imgCount = count($_FILES ['file'] ['name']);

                    $lar = 'salore' . DS . 'classified' . DS . 'posts' . DS;
                    
                    $path = Mage::getBaseDir ( 'media' ) . DS . $lar;

                    $pathResize = Mage::getBaseDir('media') . DS . $lar . 'small' . DS;

                    for ($i=0; $i < $imgCount ; $i++) {
                        $uploader = new Mage_Core_Model_File_Uploader(
                            array(
                                'name' => $_FILES['file']['name'][$i],
                                'type' => $_FILES['file']['type'][$i],
                                'tmp_name' => $_FILES['file']['tmp_name'][$i],
                                'error' => $_FILES['file']['error'][$i],
                                'size' => $_FILES['file']['size'][$i]
                                ));

                        $fname = $_FILES['file']['name'][$i];

                        if( isset( $postImg[$fname] ) ) {
                            $idImg = $postImg[$fname];
                        }
                        
                        $uploader->setAllowedExtensions(array('jpg', 'gif', 'png', 'jpeg'));
                        $uploader->setAllowCreateFolders(true); 
                        $uploader->setAllowRenameFiles(false); 
                        $uploader->setFilesDispersion(false);
                        $uploader->save($path,$fname);
                        $linkUrl =  Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . 'media/salore/classified/posts/' . $fname;
                        $smallLink = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . 'media/salore/classified/posts/small/' . $fname;
                        Mage::helper('salon')->resize($path, $fname, 280, 160, $fname, $pathResize);
                        $requestImg[ $idImg ] = array(
                            'imgName' => $fname,
                            'smallUrl' => $smallLink,
                            'largerUrl' => $linkUrl, 
                            );

                    }
                }
            }
            
            $postsObj->setData('images', $requestImg);
        
            if( isset( $data['thumbImg'] ) ){
                
                $thumb = $data['thumbImg'];

                if( isset($requestImg[$thumb] ) ) {

                    $postsObj->setData('thumbnail_image', array( 'idImage' =>$thumb, 'url' => $requestImg[ $thumb]['smallUrl'] ) );
                }
            } else {
                $postsObj->setData('thumbnail_image', array() );
            }
            if( isset ($data['baseImg'] ) ){

                $base = $data['baseImg'];

                if( isset($requestImg[$base] ) ) {

                    $postsObj->setData('base_image', array( 'idImage' =>$base, 'url' => $requestImg[ $base]['smallUrl'] ) );
                }
            } else {
                $postsObj->setData('base_image', array() );
            }



            
            if (isset ( $data ['specific'] )) {
                $postsObj->setData ( 'is_specific', ( int ) 1 );
            }
            
            
            
            if (isset ( $data ['title'] ) && $data ['title']) {
                $postsObj->setData ( 'title', $data ['title'] );
            }
            
            if (isset ( $data ['category'] ) && $data ['category']) {
                $postsObj->setData ( 'category', $data ['category'] );
            }
            if (isset ( $data ['description'] ) && $data ['description']) {
                $postsObj->setData ( 'description', $data ['description'] );
            }
            if (isset ( $data ['expired_date'] ) && $data ['expired_date']) {
                $data = $this->_filterDates ( $data, array (
                    'expired_date' 
                    ) );
                $postsObj->setData ( 'expired_date', $data ['expired_date'] );
            }
            
            $postsObj->setData('update_at', Mage::getModel('core/date')->timestamp( time() ) );
            
            $postsObj->save ();
            Mage::getSingleton ( 'core/session' )->addSuccess ( 'This posts have saved succesfully!' );
            $this->_redirect ( '*/*' );
        } catch ( Exception $e ) {
            
            $this->failToSaveData ( $data );
        }
    }



    
    public function failToSaveData($data) {
        Mage:: getSingleton( "adminhtml/session" )->setPostsData($data);
        Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'Please fill all field with value.' ) );
        $this->_redirect ( '*/*/edit' );
    }
    public function updateValuePostsImage(&$postsObj) {
        $imgPath = Mage::registry ( 'posts_data' )->getImgPath ();
        
        $imgPathOriginal = Mage::registry ( 'posts_data' )->getImgPathOriginal ();
        
        $imgPathResize = Mage::registry ( 'posts_data' )->getImgPathResize ();
        
        $imgResize = Mage::registry ( 'posts_data' )->getImgResize ();
        
        $postsObj->setData ( 'img_path', $imgPath );
        
        $postsObj->setData ( 'img_path_resize', $imgPathResize );
        
        $postsObj->setData ( 'img_path_original', $imgPathOriginal );
    }
    public function ajaxdeleteAction() {
        $responses = array ();
        $id = Mage::app ()->getRequest ()->getParam ( 'id' );
        try {
            $categoryModel = Mage::getModel ( 'classified/posts' )->load ( $id, 'entity_id' );
            $imgPathDelete = $categoryModel ['img_path_resize'];
            $this->recursiveDelete ( $imgPathDelete );
            $categoryModel->delete ();
            $responses ['status'] = 'SUCCESS';
            $responses ['message'] = 'This  category have deleted successfully!';
            echo json_encode ( $responses );
        } catch ( Exception $e ) {
            $responses ['status'] = 'ERROR';
            $responses ['message'] = $e->getMessage ();
            echo json_encode ( $responses );
        }
    }
    public function deleteAction() {
        $id = Mage::app ()->getRequest ()->getParam ( 'id' );
        try {
            $postsModel = Mage::getModel ( 'classified/posts' )->load ( $id, 'entity_id' );
            $imgPathDelete = $postsModel ['img_path_resize'];
            $this->recursiveDelete ( $imgPathDelete );
            $postsModel->delete ();
            Mage::getSingleton ( 'core/session' )->addSuccess ( 'This posts have delete succesfully!' );
            $this->_redirect ( '*/*' );
        } catch ( Exception $e ) {
            Mage::getSingleton ( "adminhtml/session" )->setSessionDataExist ( $data );
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( $e->getMessage () ) );
            $this->_redirect ( '*/*/edit' );
        }
    }
    public function recursiveDelete($str) {
        if (is_file ( $str )) {
            return @unlink ( $str );
        } elseif (is_dir ( $str )) {
            $scan = glob ( rtrim ( $str, '/' ) . '/*' );
            foreach ( $scan as $index => $path ) {
                recursiveDelete ( $path );
            }
            return @rmdir ( $str );
        }
    }
    public function ajaxDeleteImgAction() {

        $idImg = $this->getRequest()->getParam('id');
        $srcImg = $this->getRequest()->getParam('src');
        if( isset( $idImg) && $idImg ) {

            echo json_encode( Mage::helper('salon/image')->ajaxDeleteImgAction($idImg, $srcImg, 'classified/admin/posts', Mage::getModel('classified/posts') ) );
            return;

        } else {

            $reponse['status'] = 'ERROR';
            $reponse['message'] ="Image does not exist in the gallery";
            echo json_encode($reponse);
            return ;
        }
    }
    
    public function uploadAction() {

        if ( isset($_FILES) && count($_FILES) > 0 ) {
            echo json_encode( Mage::helper('salon/image')->processImgaUploaded( $_FILES, 'classified/admin/posts' ) );
            return;
        }
        echo json_encode( array( 'error' => $this->__('Cannot find any image Data!')) );
        return ;
    }
    
}