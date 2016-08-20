<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Salore_Salon to newer
 * versions in the future.
 *
 * @category    Salore
 * @package     Salore_Mongo
 * @author      Salore team
 * @copyright   Copyright (c) Salore team
 */
class Salore_Salon_Helper_Data extends Mage_Core_Helper_Abstract {
    /**
     * Get current Page Name in format RouteName_controllerName
     * @return string
     */
    public function getCurrentPageName() {
        $request = Mage::app()->getRequest();
        $routeName = $request->getRouteName();
        $controllerName = $request->getControllerName();
        $actionName = $request->getActionName();
        $pageName = $routeName.'_'.$controllerName;
        return $pageName;
    }
    
    public function getActivePageClass($path) {
        $currentPage = $this->getCurrentPageName();
        $page = str_replace('/', '_', $path);
        if (false !== strpos($currentPage, $page)) {
            return 'class="active"';
        }
        return '';
        
    }
    
    public function getActivePageArrow($path) {
        if ($this->getActivePageClass($path)) {
            return '<span class="fa fa-angle-right pull-right"></span>';
        }
        return '';
    }
    
    public function getCurrentUserName() {
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            // Load the customer's data
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            
            $userName = $customer->getName();
            if (trim($userName) == '') {
                $userName = $customer->getEmail();
            }
        
            return $userName;
        }
        return '';
    }
    
    /**
     * Return search form action Url
     * @return string
     */
    public function  getSearchFormActionUrl() {
        return '/salon/search';
    }

    public function getUrl($routePath = null, $routeParams = null) {
        return Mage::getModel('salon/url')->getUrl($routePath, $routeParams);
    }
    /**
     * get session message when form was submitted 
     * @return array
     */
    public function getSessionMessage() {
        $output = array();
        $smessages = Mage::getSingleton('core/session')->getMessages(true)->getLastAddedMessage();
        if ($smessages) {
            $output['type'] = $smessages->getType();
            $output['message'] = $smessages->getCode();
            Mage::getSingleton('core/session')->unsMessages();
        }
        return $output;
    }
    /**
     * return a array after sorted by date
     * @return array
     */
    public function getContact() {
        $returnData = array();
        $contactMongo = Mage::getModel('salon/contact')->getCollection();
        foreach ($contactMongo as $contact) {
            if($contact->getCreateAt() && !$contact->getStyle()) {
                $returnData[] = $contact->getData();
            }
        }
        return array_slice($this->sortArrayByCreateDate($returnData), 0, 5);
    
    }
    /**
     * @param array $returnData
     * @return array
     */
    public function sortArrayByCreateDate($returnData) {
        $countService = count($returnData);
        for($i = 0 ; $i < $countService - 1 ; $i++ ) {
            $iTimestamp = $returnData[$i]['create_at'];
            for($j = $i + 1 ; $j < $countService; $j++ ) {
                $jTimestamp = $returnData[$j]['create_at'];
                if($iTimestamp < $jTimestamp) {
                    $temp = $returnData[$j];
                    $returnData[$j] = $returnData[$i];
                    $returnData[$i] = $temp;
                }
            }
        }
        return $returnData;
    }
    public function checkContact() {
        $returnData = array();
        $salonId = Mage::registry('currentsalon')->getEntityId();
        $contactMongo = Mage::getModel('salon/contact')->getCollection();
        foreach ($contactMongo as $contact) {
            if($contact->getCreateAt() && !$contact->getStyle()) {
                $returnData[] = $contact->getData();
            }
        }
        return count($returnData);
    }
    /**
     * Format time to show as xx seconds ago , xx minutes ago
     * @param string $date
     * @return string
     */
    public function niceTime($date) {
        if(empty($date)) {
            return "None";
        }
            
        $periods         = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
        $lengths         = array("60","60","24","7","4","12","10");
        $now             = Mage::getModel('core/date')->timestamp(time());
        $unix_timestamp = $date;
        $difftime = ($now - $unix_timestamp);
        $format = $date;
        switch ($difftime) {
            //seconds
            case $difftime < 60 :
                $format = $difftime;
                if ($format >= 1 ) {
                    $format .= Mage::helper('salon')->__(' Seconds ago');
                }
                else {
                    $format .= Mage::helper('salon')->__(' Second ago');
                }
                    
                break;
                // minutes
            case $difftime >= (60)  && $difftime < (60*60) :
                $format = round(($difftime/60));
                if($format > 1) {
                    $format .= Mage::helper('salon')->__(' Minutes ago');
                }
                else {
                    $format .= Mage::helper('salon')->__(' Minute ago');
                }
    
                break;
                // hours
            case  $difftime >= (60*60) && $difftime <= (60*60*24) :
                $format = round(($difftime/(60*60)));
                if($format > 1) {
                    $format .=    Mage::helper('salon')->__(' Hours ago');
                }
                else {
                    $format .=    Mage::helper('salon')->__(' Hour ago');
                }
    
                break;
                // days
            case  $difftime >= (60*60*24) && $difftime < (60*60*24*7):
                $format = round(($difftime/(60*60*24))) ;
                if($format > 1) {
                    $format .=    Mage::helper('salon')->__(' Days ago');
                }
                else {
                    $format .=    Mage::helper('salon')->__(' Day ago');
                }
                break;
                // week
            case $difftime >= (60*60*24*7) && $difftime < (60*60*24*7*4) :
                $format = round(($difftime/(60*60*24*7))) ;
                if($format > 1) {
                    $format .=    Mage::helper('salon')->__(' Weeks ago');
                }
                else {
                    $format .=    Mage::helper('salon')->__(' Week ago');
                }
                break;
                // months
            case $difftime >= (60*60*24*7*4) && $difftime < (60*60*24*7*4*12) :
                $format = round(($difftime/(60*60*24*7*4)));
                if($format > 1) {
                    $format .=    Mage::helper('salon')->__(' Months ago');
                }
                else {
                    $format .=    Mage::helper('salon')->__(' Month ago');
                }
                break;
                // year
            case $difftime >= (60*60*24*7*4*12)  :
                $format = round(($difftime/(60*60*24*7*4*12)));
                if($format > 1) {
                    $format .=    Mage::helper('salon')->__(' Years ago');
                }
                else {
                    $format .=    Mage::helper('salon')->__(' Year ago');
                }
                break;
            default:
                break;
        }
        return $format;
    }
    /**
     * transport a string to a standard string
     * @param string $text
     * @return string|NULL
     */
    public function transportText($text) {
        if($text) {
            $text = str_replace(' ', '', $text);
            $text = $this->vn_str_filter($text);
            return strtolower($text);
        }
        return null;
    }
     public function vn_str_filter ($str){
    
        $unicode = array(
                  'a'=>'Ä‚Â¡|Ä‚Â |Ã¡ÂºÂ£|Ä‚Â£|Ã¡ÂºÂ¡|Ã„Æ’|Ã¡ÂºÂ¯|Ã¡ÂºÂ·|Ã¡ÂºÂ±|Ã¡ÂºÂ³|Ã¡ÂºÂµ|Ä‚Â¢|Ã¡ÂºÂ¥|Ã¡ÂºÂ§|Ã¡ÂºÂ©|Ã¡ÂºÂ«|Ã¡ÂºÂ­',
            'd'=>'Ã„â€˜',
            'e'=>'Ä‚Â©|Ä‚Â¨|Ã¡ÂºÂ»|Ã¡ÂºÂ½|Ã¡ÂºÂ¹|Ä‚Âª|Ã¡ÂºÂ¿|Ã¡Â»ï¿½|Ã¡Â»Æ’|Ã¡Â»â€¦|Ã¡Â»â€¡',
            'i'=>'Ä‚Â­|Ä‚Â¬|Ã¡Â»â€°|Ã„Â©|Ã¡Â»â€¹',
            'o'=>'Ä‚Â³|Ä‚Â²|Ã¡Â»ï¿½|Ä‚Âµ|Ã¡Â»ï¿½|Ä‚Â´|Ã¡Â»â€˜|Ã¡Â»â€œ|Ã¡Â»â€¢|Ã¡Â»â€”|Ã¡Â»â„¢|Ã†Â¡|Ã¡Â»â€º|Ã¡Â»ï¿½|Ã¡Â»Å¸|Ã¡Â»Â¡|Ã¡Â»Â£',
            'u'=>'Ä‚Âº|Ä‚Â¹|Ã¡Â»Â§|Ã…Â©|Ã¡Â»Â¥|Ã†Â°|Ã¡Â»Â©|Ã¡Â»Â«|Ã¡Â»Â­|Ã¡Â»Â¯|Ã¡Â»Â±',
            'y'=>'Ä‚Â½|Ã¡Â»Â³|Ã¡Â»Â·|Ã¡Â»Â¹|Ã¡Â»Âµ',
            'A'=>'Ä‚ï¿½|Ä‚â‚¬|Ã¡ÂºÂ¢|Ä‚Æ’|Ã¡ÂºÂ |Ã„â€š|Ã¡ÂºÂ®|Ã¡ÂºÂ¶|Ã¡ÂºÂ°|Ã¡ÂºÂ²|Ã¡ÂºÂ´|Ä‚â€š|Ã¡ÂºÂ¤|Ã¡ÂºÂ¦|Ã¡ÂºÂ¨|Ã¡ÂºÂª|Ã¡ÂºÂ¬',
            'D'=>'Ã„ï¿½',
            'E'=>'Ä‚â€°|Ä‚Ë†|Ã¡ÂºÂº|Ã¡ÂºÂ¼|Ã¡ÂºÂ¸|Ä‚ï¿½|Ã¡ÂºÂ¾|Ã¡Â»â‚¬|Ã¡Â»â€š|Ã¡Â»â€ž|Ã¡Â»â€ ',
            'I'=>'Ä‚ï¿½|Ä‚Å’|Ã¡Â»Ë†|Ã„Â¨|Ã¡Â»ï¿½',
            'O'=>'Ä‚â€œ|Ä‚â€™|Ã¡Â»ï¿½|Ä‚â€¢|Ã¡Â»Å’|Ä‚â€�|Ã¡Â»ï¿½|Ã¡Â»â€™|Ã¡Â»â€�|Ã¡Â»â€“|Ã¡Â»Ëœ|Ã†Â |Ã¡Â»ï¿½|Ã¡Â»Å“|Ã¡Â»ï¿½|Ã¡Â»Â |Ã¡Â»Â¢',
            'U'=>'Ä‚ï¿½|Ä‚â„¢|Ã¡Â»Â¦|Ã…Â¨|Ã¡Â»Â¤|Ã†Â¯|Ã¡Â»Â¨|Ã¡Â»Âª|Ã¡Â»Â¬|Ã¡Â»Â®|Ã¡Â»Â°',
            'Y'=>'Ä‚ï¿½|Ã¡Â»Â²|Ã¡Â»Â¶|Ã¡Â»Â¸|Ã¡Â»Â´',
        );
        foreach($unicode as $nonUnicode=>$uni){
            $str = preg_replace("/($uni)/i", $nonUnicode, $str);
        }
        return $str;
    } 
    /**
     * create a image
     * @param string $fileName
     * @param string $nameInput
     * @param string $path
     * @return boolean
     */
    public function createImageAfterUpload($fileName, $nameInput, $path) {
        if (isset($fileName)) {
            $fname = $fileName; 
            $uploader = new Varien_File_Uploader($nameInput); 
            $uploader->setAllowedExtensions(array('jpg', 'gif', 'png', 'jpeg'));
            $uploader->setAllowCreateFolders(true); 
            $uploader->setAllowRenameFiles(false); 
            $uploader->setFilesDispersion(false);
            return $uploader->save($path,$fname); 
        }
        return false;
    }
    /**
     * return a path which contain image
     * @param string $serviceId
     * @param string $containFolder
     * @return string
     */
    public function getImageDir($serviceId, $containFolder) {
        return $path = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . 'media/' . 'salore/' .$containFolder. '/' . $serviceId . '/' .$containFolder.'.png' ;
    }
    /**
     * delete images which created
     * @param string $str
     */
    public function deletefile($str) {
        if(is_file($str)) {
            return @unlink($str);
        
        }
        elseif(is_dir($str)) {
                $scan = glob(rtrim($str,'/').'/*');
                foreach($scan as $index=>$path){
                    deletefile($path);
                }
                return @rmdir($str);
        }
    }
    /**
     * Resize image with one Url
     * @param  $path
     * @param  $fileName
     * @param  $width
     * @param  $height
     * @param  $nameAfterResize
     * return image save on Url root
     */
    public function resizeImage($path, $fileName, $width, $height, $nameAfterResize) {
        $imageObj = new Varien_Image($path. $fileName);
        $imageObj->constrainOnly(FALSE);
        $imageObj->keepAspectRatio(TRUE);
        $imageObj->keepFrame(FALSE);
        $imageObj->backgroundColor(array(255,255,255));
        $imageObj->resize($width, $height);
        $imageObj->save($path . $nameAfterResize);
    }
    public function resizeImage2($path, $fileName, $width, $height, $nameAfterResize) {
        $imageObj = new Varien_Image($path. $fileName);
        $imageObj->constrainOnly(FALSE);
        $imageObj->keepAspectRatio(TRUE);
        $imageObj->keepFrame(TRUE);
        $imageObj->keepTransparency(TRUE);
        $imageObj->backgroundColor(array(255,255,255));
        $imageObj->resize($width, $height);
        $imageObj->save($path . $nameAfterResize);
    }
    public function resizeImage1($path, $fileName, $height, $nameAfterResize) {
        $imageObj = new Varien_Image($path. $fileName);
        $imageObj->constrainOnly(FALSE);
        $imageObj->keepAspectRatio(TRUE);
        $imageObj->keepFrame(TRUE);
        $imageObj->keepTransparency(TRUE);
        $imageObj->backgroundColor(array(255,255,255));
        $imageObj->resize( $height);
        $imageObj->save($path . $nameAfterResize);
    }
    /**
     *  Resize image with two Url
     * @param  $path
     * @param  $fileName
     * @param  $width
     * @param  $height
     * @param  $nameAfterResize
     * @param  $pathurl
     * return image save on Url different
     */
    public function resize($path, $fileName, $width, $height, $nameAfterResize , $pathurl) {
        $imageObj = new Varien_Image($path. $fileName);
        $imageObj->constrainOnly(FALSE);
        $imageObj->keepAspectRatio(TRUE);
        $imageObj->keepFrame(FALSE);
        $imageObj->backgroundColor(array(255,255,255));
        $imageObj->resize($width, $height);
        return $imageObj->save($pathurl . $nameAfterResize);
    }
    /**
     * Retrieve random password
     *
     * @param   int $length
     * @return  string
     */
    public function generatePassword() {
        $length = 8;
        $chars = Mage_Core_Helper_Data::CHARS_PASSWORD_LOWERS
        . Mage_Core_Helper_Data::CHARS_PASSWORD_UPPERS
        . Mage_Core_Helper_Data::CHARS_PASSWORD_DIGITS
        . Mage_Core_Helper_Data::CHARS_PASSWORD_SPECIALS;
        return Mage::helper('core')->getRandomString($length, $chars);
    }
    /**
     * create attribute id before creating product
     * @return mixed|unknown
     */
    public function getAttributeSetIdNameDefault() {
        $cachedKey = "salon_service_getAttributeSetIdNameDefault";
        if (false !== ($attributeSetId = Mage::app ()->getCache ()->load ( $cachedKey ))) {
            return $attributeSetId = unserialize ( $attributeSetId );
        } else {
            $entityTypeId = Mage::getModel ( 'eav/entity' )->setType ( 'catalog_product' )->getTypeId ();
            $attributeSetName = 'Default';
            $attributeSetId = Mage::getModel ( 'eav/entity_attribute_set' )->getCollection ()->setEntityTypeFilter ( $entityTypeId )->addFieldToFilter ( 'attribute_set_name', $attributeSetName )->getFirstItem ()->getAttributeSetId ();
            Mage::app ()->getCache ()->save ( serialize ( $attributeSetId ), $cachedKey, array () );
            return $attributeSetId;
        }
    }
    /**
     * return a standard price
     * @param float $price
     */
    public function stringFormatToPrice($price) {
        return Mage::helper('core')->formatPrice($price, true);
    }
    /**
     * cut string with end point
     * @param string $str
     * @param number $length
     * @param number $minword
     * @return string
     */
    public function _substr($str, $length, $minword = 3) {
        $sub = '';
        $len = 0;
        foreach (explode(' ', $str) as $word)
        {
            $part = (($sub != '') ? ' ' : '') . $word;
            $sub .= $part;
            $len += strlen($part);
            if (strlen($word) > $minword && strlen($sub) >= $length)
            {
                break;
            }
        }
        return $sub . (($len < strlen($str)) ? '...' : '');
    }
    /**
     * @param string $url
     * @return Ambigous <multitype:, mixed>
     */
    public function getFaceBookData($url) {
        $faceBookUrl = 'http://api.facebook.com/method/fql.query?query=select%20like_count,comment_count,url%20from%20link_stat%20where%20url%20in%20%28%27'.str_replace(' ', '%27', $url).'%27%29&format=json';
        $data = $this->doRequest($faceBookUrl);
        return $data;
    }
    /**
     * check salon which approved
     * @return string
     */
    public function checkApprove() {
        $flag = 'Approve this Salon';
        if(Mage::registry('salon_data')){
            $data = Mage::registry('salon_data')->getData();
            if(isset( $data['approve'])){
                $approve = $data['approve'];
                if(strlen($approve) <= 0 || (int)$approve === 1)
                    $flag = 'UnApprove this Salon';
        }
        }
        return $flag;
    }
    /**
     * Request Solr Server by CURL
     * @param string $url
     * @param mixed $postFields
     * @param string $type
     * @return array
     */
    public function doRequest($url, $postFields = null, $type='array'){
        $sh = curl_init($url);
        curl_setopt($sh, CURLOPT_HEADER, 0);
        if(is_array($postFields)) {
            curl_setopt($sh, CURLOPT_POST, true);
            curl_setopt($sh, CURLOPT_POSTFIELDS, $postFields);
        }
        curl_setopt($sh, CURLOPT_RETURNTRANSFER, 1);
    
        if ($type == 'json') {
            curl_setopt( $sh, CURLOPT_HEADER, true );
        }
    
    
        if ($type == 'json') {
            list( $header, $contents ) = @preg_split( '/([\r\n][\r\n])\\1/', curl_exec($sh), 2 );
            $output = preg_split( '/[\r\n]+/', $contents );
        }else{
            $output = curl_exec($sh);
            $output = json_decode($output,true);
        }
    
        curl_close($sh);
        return $output;
    }
    /**
     * return time now
     * @return string
     */
    public function getCurrentDate() {
        $currentTimestamp = Mage::app()->getLocale()->storeTimeStamp(Mage::app()->getStore()->getId());
        return date('d-m-Y', $currentTimestamp);
    }
    /**
     * push static menu to array 
     * @return array
     */
    public function getLinksForOption() {
        $defaultLinks = array('service' => 'Service', 'product' => 'Product', 'contact' => 'Contact','gallery' => 'Gallery', 'reservation' => 'Reservation');
        $pages = Mage::getModel('salon/page')->getCollection();
        foreach($pages as $page)
        {
            $defaultLinks['page/view/id/'.$page->getEntityId()] = $page->getTitle();
        }
        return $defaultLinks;
    }
    /**
     * get footer data from mongo and parse to array type
     * @return array
     */
    public function getFooterAfterParseToArr() {
        $returnData = array();
        $footerCollection = Mage::getModel('salon/footer')->getCollection();
        $i = 0;
        foreach($footerCollection as $footer)
        {
            if ($footer->getActive())
            {
                $returnData[$i]['title'] = $footer->getTitle();
                $returnData[$i]['content'] = $footer->getContent();
                $returnData[$i]['position'] = $footer->getPosition();
                $i++;
            }
        }
        return $this->sortFooterArr($returnData);
    }
    /**
     * sort array by position atttribute
     * @param array $dataArr
     * @return array
     */
    public function sortFooterArr($dataArr) {
        usort($dataArr, array(get_class($this), 'attributePositionSort'));
        return $dataArr;
    }
    /**
     * @param array $a
     * @param array $b
     * @return number
     */
    public function attributePositionSort( $a, $b ) {
        return $a['position'] - $b['position'];
    }
    /**
     * @param string $salonUrl
     * @return string|null
     */
    public function getSalonNameBySalonUrl($salonUrl) {
        $salon = Mage::getModel('salon/salon')->load($salonUrl, 'salon_url');
        if($salon->getEntityId())
        {
            return $salon->getSalonName();
        }
        return null;
    }
    /**
     * call to Base class to get params
     * @return multitype:
     */
    public function getParams() {
        return Salore_Base::getParams();
    }
    
    /**
     * 
     * @return number
     */
    public function getLastIdCategory(Salore_Mongo_Model_Abstract $categoryModel) {
        
        $categorytIds = array ();
        
        $categoryCollections = $categoryModel->getCollection ();
        
        if ( (int) $categoryCollections->getSize() <= 0 ){
            
            return ( int ) 0;
        }
        
        foreach ( $categoryCollections as $category ) {
            
            if ( $category->getEntityId () ) {
                $categorytIds [] = $category->getEntityId ();
            }
        }
        
        return ( int ) max ( $categorytIds );
    }
    /**
     * @desc get Category By Name
     * @return string
     */
    public function getCategoryByName($categoryName)
    {
        return Mage::getModel('salon/category')->load($categoryName, 'category_name');
    }
    /**
     * return a string after remove special characters and join with id
     * @param string $salonUrl
     * @param string $salonId
     * @return string
     */
    public function transformCode($salonUrl, $salonId)
    {
        return preg_replace("/^'|[^A-Za-z0-9\']|'$/", '', $salonUrl).uniqid($salonId);
    }
    /**
     * Check if domain is registered or not
     * @param string $domainName
     */
    public function checkIsDomainRegistered($domainName)
    {
        if ( gethostbyname($domainName) != $domainName )
        {
            return true;
        }
        return false;
    }
    
    public function getSort( $direct ){
    
        if( strtoupper( trim( $direct) === 'ASC')  ) {
    
            return 'DESC';
        }
        return 'ASC';
    }
    public function transformTimestampToDate($date) {
        return date('m-d-Y' , $date);
    }
}