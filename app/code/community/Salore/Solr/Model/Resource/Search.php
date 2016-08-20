<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Salore to newer
 * versions in the future.
 *
 * @category    Salore
 * @package     Salore_Solr
 * @author      Salore team
 * @copyright   Copyright (c) Salore team
 */
class Salore_Solr_Model_Resource_Search extends Salore_Solr_Model_Resource_Abstract {
    public function getSuggestion($queryText) {
        $connection = $this->getConnection();
        $queryUrl = $connection->getQueryUrl();
        $queryUrl = trim($queryUrl,'/').'/?q='.urlencode(strtolower(trim($queryText)));
        $queryUrl .= '&facet=true&facet.field=textSearchStandard&facet.limit=7&rows=-1';
        
        $filterQuery = array('qf' => Mage::helper('solr')->getQueryFields(),
                            'defType' => 'edismax',
                            'json.nl' => 'map',
                            'wt' => 'json'
                        );
        
        $queryArray = explode(' ', $queryText);
        $tempQueryArray = array();
        foreach ($queryArray as $word) {
            $queryUrl .= '&f.textSearchStandard.facet.prefix='.urlencode(strtolower(trim($word)));
        
            if (count($tempQueryArray) > 0) {
                $tempQueryArray[] = $word;
                $queryUrl .= '&f.textSearchStandard.facet.prefix='.urlencode(strtolower(trim(@implode('+',$tempQueryArray))));
            }else{
                $tempQueryArray[] = $word;
            }
        }
        
        $result = $connection->request($queryUrl, $filterQuery);
        
        $suggestions = array('query' => $queryText, 'suggestions' => array());
        
        if (isset($result['facet_counts']['facet_fields']['textSearchStandard']) && is_array($result['facet_counts']['facet_fields']['textSearchStandard']))  {
            foreach ($result['facet_counts']['facet_fields']['textSearchStandard'] as $term => $val) {
                $suggestions['suggestions'][] = array('value' => $term, 'data' => $val);
            }
        }
        return $suggestions;
    }
}