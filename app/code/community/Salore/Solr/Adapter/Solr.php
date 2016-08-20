<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade MongoBridge to newer
 * versions in the future.
 *
 * @category    Salore
 * @package     Salore_Solr
 * @author      Salore team
 * @copyright   Copyright (c) Salore team
 */
class Salore_Solr_Adapter_Solr extends Salore_Solr_Adapter_Abstract {
    public function __construct($config) {
        $this->_config = $config;
    }
    
    public function getUpdateUrl() {
        return trim($this->_config['server'], '/').'/'.$this->_config['solrcore'].'/update/json?wt=json&overwrite=true';
    }
    
    public function getDeleteUrl() {
        return trim($this->_config['server'], '/').'/'.$this->_config['solrcore'].'/update/';
    }
    
    public function getCommitUrl() {
        return trim($this->_config['server'], '/').'/'.$this->_config['solrcore'].'/update/json?wt=json&commit=true&waitFlush=false';
    }
    
    public function getLogUrl() {
        return trim($this->_config['server'], '/').'/admin/info/logging?wt=json&level=ERROR';
    }
    
    public function getTruncateUrl() {
        return trim($this->_config['server'], '/').'/'.$this->_config['solrcore'].'/update?stream.body=<delete><query>*:*</query></delete>&commit=true&waitFlush=false';
    }
    
    public function getQueryUrl() {
        return trim($this->_config['server'], '/').'/'.$this->_config['solrcore'].'/select';
    }
    
    public function getPingUrl() {
        return trim($this->_config['server'], '/').'/'.$this->_config['solrcore'].'/admin/ping?wt=json';
    }
    
    public function getConnection() {
        return $this;
    }
    
    public function ping() {
        $pingUrl = $this->getPingUrl();
        $result = $this->request($pingUrl);
        if (isset($result['status']) && $result['status'] == 'OK') {
            return true;
        }
        return false;
    }
    
    public function saveDocument( array $document ) {
        $updateUrl = $this->getUpdateUrl();
        $json = '{';
        $json .= '"add": '.json_encode(array('doc'=>$document));
        $json .= '}';
        $postFields = array('stream.body'=>$json);
        return $this->request($updateUrl, $postFields);
    }
    
    /**
     * Delete solr document by entity id
     * @param mixed $entityId
     */
    public function deleteDocument($entityId) {
        $updateUrl = $this->getDeleteUrl();
        $updateUrl .= '?stream.body=<delete><query>entity_id:'.$entityId.'</query></delete>';
        return $this->request($updateUrl);
    }
    
    public function loadDocument($id) {
        $loadUrl = $this->getQueryUrl();
        $loadUrl .= '/?q=id:'.$id;
        $response = $this->request($loadUrl, array('wt' => 'json'));
        if ( isset($response['response']['docs'][0]['id']) && $response['response']['docs'][0]['id'] == $id)
        {
            return $response['response']['docs'][0];
        }
        return false;
    }
    
    public function commit() {
        $commitUrl = $this->getCommitUrl();
        return $this->request($commitUrl);
    }
    
    public function getLogs() {
        $logUrl = $this->getLogUrl();
        return $this->request($logUrl);
    }
    
    public function truncate() {
        $commitUrl = $this->getTruncateUrl();
        $this->request($commitUrl);
        $this->commit();
    }
    
    /**
     * Request Solr Server by CURL
     * @param string $url
     * @param mixed $postFields
     * @param string $type
     * @return array
     */
    public function request($url, $postFields = null, $type='array') {
        $sh = curl_init($url);
        curl_setopt($sh, CURLOPT_HEADER, 0);
        if(is_array($postFields)) {
            curl_setopt($sh, CURLOPT_POST, true);
            curl_setopt($sh, CURLOPT_POSTFIELDS, $postFields);
        }
        curl_setopt($sh, CURLOPT_RETURNTRANSFER, 1);
    
        curl_setopt( $sh, CURLOPT_FOLLOWLOCATION, true );
        if ($type == 'json') {
            curl_setopt( $sh, CURLOPT_HEADER, true );
        }
    
        if (isset($_GET['user_agent']) || isset($_SERVER['HTTP_USER_AGENT'])) {
            curl_setopt( $sh, CURLOPT_USERAGENT, isset($_GET['user_agent']) ? $_GET['user_agent'] : $_SERVER['HTTP_USER_AGENT'] );
        }
    
        if ($type == 'json') {
            list( $header, $contents ) = @preg_split( '/([\r\n][\r\n])\\1/', curl_exec($sh), 2 );
            $output = preg_split( '/[\r\n]+/', $contents );
        }else {
            $output = curl_exec($sh);
            $output = json_decode($output,true);
        }
    
        curl_close($sh);
        return $output;
    }
}