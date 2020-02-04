<?php
namespace MagentoGarage\GSTIndia\Model;

use Magento\Framework\DataObject;

class GST{

	public function configureGST($regionId){
		// @TODO create GST rules based on given region ID 
		$result = new DataObject([
            'is_valid' => true,
            'message' => __('GST configuration yet to set. for ' . $regionId),
        ]);

		return $result;
	}	
}