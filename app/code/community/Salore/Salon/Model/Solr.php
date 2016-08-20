<?php
/**
 * @category SolrBridge
 * @package WebMods_Solrsearch
 * @author    Hau Danh
 * @copyright    Copyright (c) 2011-2013 Solr Bridge (http://www.solrbridge.com)
 *
 */
class Salore_Salon_Model_Solr extends Mage_Core_Model_Abstract {
    /**
     *  get data from solr server
     * @return json
     */
    public function getTopCities() {
        $solrServerUrl = $this->getSolrServerUrl();
        $url = $solrServerUrl.'select/?q=*:*&facet=true&facet.field=city_varchar&wt=json&json.nl=map&facet.mincount=1&rows=-1';
        $jsonData = $this->doRequest($url);
        return $jsonData;
    }
    public function getSalonResultAfterSearch($textSearch) {
        $solr = new SolrBridge_Solr();
        $solr->setQueryText($textSearch);
        return $solr->execute();
    }
    /**
     * @return string
     */
    public function getSolrServerUrl() {
        $solrServerUrl ='http://localhost:8983/solr/english/';
        return $solrServerUrl;
    }

    /**
     * Request Solr Server by CURL
     * @param string $url
     * @param mixed $postFields
     * @param string $type
     * @return array
     */
    public function doRequest($url, $postFields = null, $type='array') {
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
        }else{
            $output = curl_exec($sh);
            $output = json_decode($output,true);
        }

        curl_close($sh);
        return $output;
    }
}