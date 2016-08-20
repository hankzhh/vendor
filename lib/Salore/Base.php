<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade MongoBridge to newer
 * versions in the future.
 *
 * @category    SolrBridge
 * @package     SolrBridge_Solrsearch
 * @author      Hau Danh
 * @copyright   Copyright (c) 2011-2014 Solr Bridge (http://www.solrbridge.com)
 */

class Salore_Base {

	/**
	 * Get parameters value
	 * @return array
	 */
	static public function getParams() {

		$httpRequest = new Zend_Controller_Request_Http();

		$params = $httpRequest->getParams();
		if ( !is_array($params) )
		{
			$params = array();
		}

		return $params;
	}

	/**
	 * Get parameter value
	 * @param $key
	 * @return mixed
	 */
	static public function getParam($key) {
		$params = self::getParams();
		$returnValue = '';
		if (!empty($key) && isset($params[$key]) && !empty($params[$key])) {
			$returnValue = $params[$key];
		}
		return self::escapeHtml($returnValue);
	}

	static public function escapeHtml($data, $allowedTags = null)
	{
		if (is_array($data)) {
			$result = array();
			foreach ($data as $item) {
				$result[] = self::escapeHtml($item);
			}
		} else {
			// process single item
			if (strlen($data)) {
				if (is_array($allowedTags) && !empty($allowedTags)) {
					$allowed = implode('|', $allowedTags);
					$result = preg_replace('/<([\/\s\r\n]*)(' . $allowed . ')([\/\s\r\n]*)>/si', '##$1$2$3##', $data);
					$result = htmlspecialchars($result, ENT_COMPAT, 'UTF-8', false);
					$result = preg_replace('/##([\/\s\r\n]*)(' . $allowed . ')([\/\s\r\n]*)##/si', '<$1$2$3>', $result);
				} else {
					$result = htmlspecialchars($data, ENT_COMPAT, 'UTF-8', false);
				}
			} else {
				$result = $data;
			}
		}
		return $result;
	}
}