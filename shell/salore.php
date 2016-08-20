<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade MongoBridge to newer
 * versions in the future.
 *
 * @category    MongoBridge
 * @package     MongoBridge_Core
 * @author      Hau Danh
 * @copyright   Copyright (c) 2011-2014 Solr Bridge (http://www.solrbridge.com)
 */

require_once 'abstract.php';

class MongoBridge_Nailkare_Shell extends Mage_Shell_Abstract {
    protected $messages;
    public function __construct() {
        parent::__construct();
    }
    /*
     * Run script
    */
    public function run() {
        ini_set('memory_limit', '2040M');
        if ($this->getArg('import')) {
            //Get which solrcore tobe reindex/update
            $importFile = $this->getArg('import');
            echo $importFile."\n";
            $csv = new Varien_File_Csv();
            $csv->setLineLength(10000);
            $data = $csv->getData($importFile);
            $columns = $data[0];
            $customers = array();
            for($i=1; $i<count($data); $i++) {
                $item = $data[$i];
                $customer = array();
                foreach ($item as $key => $value) {
                    $customer[$columns[$key]] = $value;
                }
                $customers[] = $customer;
                $this->saveCustomer($customer);
                $this->calculateMemoryUsage();
            }
            echo 'script ended....'."\n";
            return ;
        }
        else if ($this->getArg('solrindex')) {
            $this->truncateSolrCore('english');
        	$mongoCustomerCollection = Mage::helper('mongobridge_salon')->getSalonCollection();
        	$jsonData = $this->parseJsonData($mongoCustomerCollection);
        }
        else if ($this->getArg('truncate')) {
        	$this->truncateSalore();
        }
        echo 'No thing to execute....'."\n";
        return ;
    }
    public function parseJsonData($customerCollection) {
    	$documents = "{";
    	$index = 1;
    	foreach ($customerCollection as $nailkareCustomer) {
    		$docData = $this->getDocumentData($nailkareCustomer);

    		$documents .= '"add": '.json_encode(array('doc'=>$docData)).",";
    		$index++;
    	}
    	$jsonData = trim($documents,",").'}';
    	$updateurl = 'http://localhost:8983/solr/english/update/json?wt=json';
    	echo Mage::helper('mongobridge_salon')->__('Started posting json of %s products to Solr', $index ) ."\n";
    	$this->postJsonData($jsonData, $updateurl, 'english');
    	echo "Post json done\n";
    	return $jsonData;
    }

    public function getDocumentData($salon) {
		$nextFix = '_varchar';
    	$documentData = array();
    	$defaultStore = Mage::app()->getWebsite(true)->getDefaultGroup()->getDefaultStore();

    	$documentData['unique_id'] = $defaultStore->getId() . '_' . $salon->getEntityId();
    	$documentData['salon_id'] = $salon->getEntityId();
    	$documentData['store_id'] = $defaultStore->getId();
    	$documentData['website_id'] = $defaultStore->getWebsiteId();
    	$documentData['salon_status'] = 1;

    	$searchKeywords = array();
    	if (!is_null($salon->getFirstname())) {
    		$firstname = $salon->getFirstname();
    		$documentData["firstname{$nextFix}"] = $firstname;
    	}
    	if (!is_null($salon->getLastname())) {
    		$lastname = $salon->getLastname();
    		$documentData["lastname{$nextFix}"] = $lastname;
    	}
    	if (!is_null($salon->getEmail())) {
    		$email = $salon->getEmail();
    		$documentData["email{$nextFix}"] = $email;
    	}
    	if (!is_null($salon->getPostcode())) {
    		$postCode = $salon->getPostcode();
    		$searchKeywords[] = $postCode;
    		$documentData["zipcode{$nextFix}"] = $postCode;
    	}
    	if (!is_null($salon->getStreet_1()) || !is_null($salon->getStreet_2())) {
    		$address = "{$salon->getStreet_1()}, {$salon->getStreet_2()}";
    		$searchKeywords[] = $address;
    		$documentData["address{$nextFix}"] = $address;
    	}
    	if (!is_null($salon->getState())) {
    		$state = $salon->getState();
    		$searchKeywords[] = $state;
    		$searchKeywords[] = $this->getRegionFromState($state);
    		$documentData["state{$nextFix}"] = $state;
    	}
    	if (!is_null($salon->getCity())) {
    		$city = $salon->getCity();
    		$searchKeywords[] = $city;
			$documentData["city{$nextFix}"] = $city;
    	}
    	if (!is_null($salon->getLng())) {
    		$lng = $salon->getLng();
    		$documentData["lng{$nextFix}"] = $lng;
    	}
    	if (!is_null($salon->getLat())) {
    		$lat = $salon->getLat();
    		$documentData["lat{$nextFix}"] = $lat;
    	}
    	if (!is_null($salon->getImgRepresent())) {
    		$imgRepresent = $salon->getImgRepresent();
    		$documentData["img_represent{$nextFix}"] = $imgRepresent;
    	}
    	if (!is_null($salon->getSalonDescription())) {
    		$salonDescription = $salon->getSalonDescription();
    		$documentData["salon_description{$nextFix}"] = $salonDescription;
    	}
    	if (!is_null($salon->getSalonName())) {
    		$salonName = $salon->getSalonName();
    		$searchKeywords[] = $salonName;
    		$documentData["salon_name{$nextFix}"] = $salonName;
    	}
    	if (!is_null($salon->getTelephone())) {
    		$telephone = $salon->getTelephone();
    		$documentData["telephone{$nextFix}"] = $telephone;
    	}
    	$documentData['textSearch'] = $searchKeywords;
    	$documentData['textSearchText'] = $searchKeywords;
    	$documentData['textSearchStandard'] = $searchKeywords;
    	$documentData['textSearchGeneral'] = $searchKeywords;

    	return $documentData;
    }
	public function getRegionFromState($state) {
		$regionCode = Mage::getModel('directory/region')->load($state, 'default_name');
		return $regionCode->getCode();
	}
    protected function saveCustomer($customer) {
        try {
            $customerModel = Mage::getModel('customer/customer');
            $customerModel->setWebsiteId(Mage::app()->getWebsite()->getId());
            $customerModel->loadByEmail($customer['agent_email']);

            $data = array(
                'firstname' => $customer['firstname'],
                'lastname' => $customer['lastname'],
            );
            $customerModel->setFirstname($customer['firstname']);
            $customerModel->setLastname($customer['lastname']);
            if (!$customerModel->getId()) {
                $customerModel->setEmail($customer['agent_email']);
                $customerModel->setPassword('qaz123456789');
            }
            $customerModel->save();
            $regionModel = Mage::getModel('directory/region')->loadByCode($customer['agent_state'], $customer['agent_country']);
            $_customer_address = array (
                'firstname' => $customer['firstname'],
                'lastname' => $customer['lastname'],
                'street' => array (
                    '0' => $customer['agent_address_1'],
                    '1' => $customer['agent_address_2'],
                ),
                'city' => $customer['agent_city'],
                'region_id' => $regionModel->getId(),
                'region' => $customer['agent_state'],
                'postcode' => $customer['agent_postal_cd'],
                'country_id' => $customer['agent_country'],
                'telephone' => '0038531555444',
            );
            $customAddress = Mage::getModel('customer/address');
            $addressCollection = $customAddress->getCollection()->setCustomerFilter($customerModel);
            foreach ($addressCollection as $addressItem)
            {
                $addressItem->delete();
            }

            $customAddress->setData($_customer_address)
            ->setCustomerId($customerModel->getId())
            ->setIsDefaultBilling('1')
            ->setIsDefaultShipping('1')
            ->setSaveInAddressBook('1');

            $customAddress->save();

        } catch (Exception $e) {
            echo $e->getMessage()."\n";
        }
    }

    /**
     * Calculate script memory usage
     */
    public function calculateMemoryUsage() {
        $memoryUsage = memory_get_usage();
        $memoryUsage = $memoryUsage/1024/1024;

        $currentMemoryLimitStr = '';
        if (ini_get('memory_limit')){
            $currentMemoryLimitStr = ini_get('memory_limit');
        }

        $currentMemoryLimit = 2048;
        $phpIniSetAllow = true;
        if (ini_set('memory_limit', '2048M') === false) {
            $this->messages[] = Mage::helper('mongobridge_nailkare')->__('Warning: The current script was not allowed to set memory_limit, please check your php ini ...');
            $phpIniSetAllow = false;
        }

        if ( -1 !== ($position = strpos($currentMemoryLimitStr, 'M')) ) {
            $currentMemoryLimit = substr($currentMemoryLimitStr, 0, $position);
        }
        elseif ( -1 !== ($position = strpos($currentMemoryLimitStr, 'G')) ) {
            $currentMemoryLimit = substr($currentMemoryLimitStr, 0, $position);
            $currentMemoryLimit = $currentMemoryLimit / 1024;
        }

        if ($currentMemoryLimit > 0 && ($currentMemoryLimit - $memoryUsage) < 100 && $phpIniSetAllow) {
            $currentMemoryLimit = $currentMemoryLimit + 100;
            ini_set('memory_limit', $currentMemoryLimit);
        }

        if ($phpIniSetAllow) {
            ini_set('max_execution_time', 18000);
        }

        echo Mage::helper('mongobridge_nailkare')->__('System memory limit: %sM', $currentMemoryLimit)."\n";
        echo Mage::helper('mongobridge_nailkare')->__('Current memory used: %sM', number_format($memoryUsage))."\n";
    }

    /**
     * Truncate solr index by core
     * @param string $solrcore
     */
    public function truncateSolrCore($solrcore = 'english') {
        $solrServerUrl = Mage::getModel('mongobridge_salon/solr')->getSolrServerUrl();

        $clearnSolrIndexUrl = trim($solrServerUrl,'/').'/update?stream.body=<delete><query>*:*</query></delete>&commit=true';

        Mage::getModel('mongobridge_salon/solr')->doRequest($clearnSolrIndexUrl);
    }
    /**
    * Post json data to Solr Server for Indexing
    * @param string $jsonData
    * @param string $updateurl
    * @param string $solrcore
    * @return number
    */
    public function postJsonData($jsonData, $updateurl, $solrcore) {

    	if (!function_exists('curl_init')){
    		echo 'CURL have not installed yet in this server, this caused the Solr index data out of date.';
    		return ;
    	} else {
    		if(!isset($jsonData) && empty($jsonData)) {
    			return 0;
    		}

    		$postFields = array('stream.body'=>$jsonData);

    		$output = Mage::getModel('mongobridge_salon/solr')->doRequest($updateurl, $postFields);

    		if (isset($output['responseHeader']['QTime']) && intval($output['responseHeader']['QTime']) > 0) {
    			return 100;
    		} else {
    			return 0;
    		}
    	}
    }
    public function truncateSalore() {
    	//get load all salon in mongodb
    	$salonCollection = Mage::getModel('salon/salon')->getCollection();
    	foreach ($salonCollection as $salon) {
    		Mage::register('currentsalon', $salon);
    		try {
    			//delete customer
    			$email = $salon->getEmail();
    			$customer = Mage::getModel('customer/customer')
    							->setWebsiteId($salon->getWebsiteId())
    							->loadByEmail($email);
    			
    			$customer->cleanAllAddresses();
    			echo "Customer #{$customer->getId()} have deleted successfully! \n";
    			$customer->delete();
    			
    			//delele website
    			if ($model = Mage::getModel('core/website')->load($salon->getWebsiteId())) {
    				if ($model->isCanDelete()) {
    					$model->delete();
    					echo "websiteId of salon {$salon->getSalonName()} have deleted successfully! \n";
    				}
    			}
    			
    			//delete salon in mongo
    			echo "Salon #{$salon->getSalonName()} have deleted successfully! \n";
    			$salon->delete();
    		} catch (Exception $e) {
    			echo $e->getMessage();
    		}
    		
    		//delete menu from mongo
    		$menuMongo = Mage::getModel('salon/menu');
    		$menuMongo->truncate();
    		
    		//delete banner from mongo
    	    $bannerMongo = Mage::getModel('salon/banner');
    		$bannerMongo->truncate(); 
    		
    		//delete myreservation from mongo
    		$myreservationMongo = Mage::getModel('salon/myreservation');
    		$myreservationMongo->truncate();
    		
    		//delete gallery from mongo
    		$galleryMongo = Mage::getModel('salon/gallery');
    		$galleryMongo->truncate();
    		
    		//delete product and service
    		$productCollection = Mage::getModel('catalog/product');
    		$serviceMongo = Mage::getModel('salon/service')->getCollection();
    		echo "-------------------PRODUCT DELETING---------------------\n";
    		Mage::register('isSecureArea', true);
    		foreach ($serviceMongo as $service) {
    			if( 0 < $service->getEntityId() ) {
    				try {
    					$productCollection->load($service->getEntityId())->delete();
    					echo "Product {$service->getServiceName()} have deleted in magento \n";
    		
    					$service->delete();
    					echo "Service {$service->getServiceName()} have deleted in magento \n";
    		
    				} catch (Exception $e) {
    					echo $e->getMessage();
    				}
    			}
			}
			Mage::unregister('isSecureArea');
			$salonKey = $salon->getSalonUrl();
			if ( !empty($salonKey) ) {
				$adapter = Mage::getSingleton('core/resource')->getConnection('mongo_read');
				$mongoDb = $adapter->getDatabase();
				$databaseName = $adapter->getDatabaseName();
				$collectionNames = $mongoDb->getCollectionNames();
				foreach ($collectionNames as $collectionName) {
					if ( strpos($collectionName, $salonKey.'_') !== false ) {
						$collection = $adapter->getConnection()->selectCollection($databaseName, $collectionName);
						$collection->drop();
					}
				}
			}
    	}
    	echo "TRUNCATE HAVE FINISHED";
    	/*
    	$galleryObj = Mage::getModel('mongobridge_nailkare/gallery');
    	$galleryCollection = Mage::getModel('mongobridge_nailkare/gallery')->getCollection();
    	
    	echo "-------------------GALLERY DELETING---------------------\n";
    	foreach( $galleryCollection as $gallery )
    	{
    		try {
    			$galleryObj->load($gallery->getEntityId())->delete();
    			echo "{$gallery->getEntityId()} have deleted in mongodb \n";
    		} catch (Exception $e) {
    			echo $e->getMessage();
    			return ;
    		}
    		
    	}
    	$cityImgCollection = Mage::getModel('mongobridge_nailkare/city')->getCollection();
    	$cityObj = Mage::getModel('mongobridge_nailkare/city');
    	echo "-------------------CITY IMAGE DELETING---------------------\n";
    	foreach($cityImgCollection as $city)
    	{
    		try {
    			$cityObj->load($city->getEntityId())->delete();
    			echo "{$city->getEntityId()} have deleted in mongodb \n";
    		} catch (Exception $e) {
    			echo $e->getMessage();
    			return ;
    		}
    	}
    	echo "-------------------SOLR DATA DELETING---------------------\n";
    	$this->truncateSolrCore('english');	
    	echo "TRUNCATE HAVE FINISHED";*/
    }
}
$shell = new MongoBridge_Nailkare_Shell();
$shell->run();
$shell->truncateSalore();
