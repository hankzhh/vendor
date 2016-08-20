<?php
require_once ('app/Mage.php');
Mage::app();

$queryText = Mage::app()->getRequest()->getParam('query');
$suggestion = Mage::getResourceModel('solr/search')->getSuggestion($queryText);

echo json_encode($suggestion);