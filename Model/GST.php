<?php
namespace MagentoGarage\GSTIndia\Model;

use Magento\Framework\DataObject;

class GST{

	const COUNTRY_ID = "IN";
    /**
     * @var \Magento\Tax\Api\TaxClassRepositoryInterface
     */
    protected $taxClassRepository;

    /**
     * @var \Magento\Tax\Api\Data\TaxClassInterfaceFactory
     */
    protected $taxClassDataObjectFactory;

    /**
     * @var \Magento\Tax\Api\TaxRateRepositoryInterface
     */
    protected $taxRateRepository;

    /**	
     * @var \Magento\Tax\Model\Calculation\Rate\Converter
     */
    protected $taxRateConverter;

    protected $regionRepository;

    /**
     * @var \Magento\Tax\Api\TaxRuleRepositoryInterface
     */
    protected $ruleService;

    /**
     * @var \Magento\Tax\Api\Data\TaxRuleInterfaceFactory
     */
    protected $taxRuleDataObjectFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Tax\Api\TaxClassRepositoryInterface $taxClassService
     * @param \Magento\Tax\Api\Data\TaxClassInterfaceFactory $taxClassDataObjectFactory
     * @param \Magento\Tax\Api\TaxRateRepositoryInterface $taxRateRepository
     * @param \Magento\Tax\Model\Calculation\Rate\Converter $taxRateConverter
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     * @param \Magento\Tax\Api\TaxRuleRepositoryInterface $ruleService
     * @param \Magento\Tax\Api\Data\TaxRuleInterfaceFactory $taxRuleDataObjectFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Tax\Api\TaxClassRepositoryInterface $taxClassService,
        \Magento\Tax\Api\Data\TaxClassInterfaceFactory $taxClassDataObjectFactory,
        \Magento\Tax\Api\TaxRateRepositoryInterface $taxRateRepository,
        \Magento\Tax\Model\Calculation\Rate\Converter $taxRateConverter,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Tax\Api\TaxRuleRepositoryInterface $ruleService,
        \Magento\Tax\Api\Data\TaxRuleInterfaceFactory $taxRuleDataObjectFactory
    ) {
        $this->taxClassRepository = $taxClassService;
        $this->taxClassDataObjectFactory = $taxClassDataObjectFactory;
        $this->taxRateRepository = $taxRateRepository;
        $this->taxRateConverter = $taxRateConverter;
        $this->regionRepository = $regionFactory;
        $this->ruleService = $ruleService;
        $this->taxRuleDataObjectFactory = $taxRuleDataObjectFactory;

    }

    protected $_taxClasses = [
    	5 => "GST-5",
    	12 => "GST-12",
    	18 => "GST-18",
    	28 => "GST-28"
    ];

	public function configureGST($regionId){
		if($regionId == "" || $regionId == 0){
			$result = new DataObject([
	            'is_valid' => false,
	            'message' => __('Please select a region first.'),
	        ]);
			return $result; 
		}
		//$taxClasses = [5 => "GST-5",12 => "GST-12",18 => "GST-18", 28 => "GST-28"];
		$regions = $this->_getRegions();
		foreach($this->_taxClasses as $rate => $class){
	        $taxClass = $this->taxClassDataObjectFactory->create()
	            ->setClassType(\Magento\Tax\Model\ClassModel::TAX_CLASS_TYPE_PRODUCT)
	            ->setClassName($class);
	        $taxClassId = $this->taxClassRepository->save($taxClass);
			$ratePost = [
				"tax_country_id" => self::COUNTRY_ID,
				"tax_postcode" => "*",
				"tax_region_id" => $regionId,
				"rate" => $rate
				];
				$taxRates = [];

			foreach($regions as $region){
				if($region->getId() != $regionId){
					$ratePost["code"] = "IGST-" . $rate . "-" . $region->getCode();
					$ratePost["tax_region_id"] = $region->getId();
					$taxData = $this->taxRateConverter->populateTaxRateData($ratePost);
			        $taxRate = $this->taxRateRepository->save($taxData);
			        $taxRates[] = $taxRate->getId();			
				}
				else{
					$ratePost["code"] = "CGST-" . $rate . "-" . $region->getCode();
					$ratePost["rate"] = $rate/2;
					$taxData = $this->taxRateConverter->populateTaxRateData($ratePost);
			        $taxRate = $this->taxRateRepository->save($taxData);
			        $cgstRate = $taxRate->getId();

			        $ratePost["code"] = "SGST-" . $rate . "-" . $region->getCode();
					$ratePost["rate"] = $rate/2;
					$taxData = $this->taxRateConverter->populateTaxRateData($ratePost);
			        $taxRate = $this->taxRateRepository->save($taxData);	
			        $sgstRate = $taxRate->getId();
				}
			}

	        $this->_createTaxRule("IGST-".$rate, $taxRates, [$taxClassId]);
	        $this->_createTaxRule("CGST-".$rate, [$cgstRate], [$taxClassId]);
	        $this->_createTaxRule("SGST-".$rate, [$sgstRate], [$taxClassId]);

		}
		$result = new DataObject([
            'is_valid' => true,
            'message' => __('GST configured successfully.'),
        ]);
		return $result;
	}

	protected function _getRegions($countryId = self::COUNTRY_ID){
		return $this->regionRepository->create()->getCollection()->addCountryFilter($countryId)->load();
	}

	protected function _createTaxRule($code, array $taxRates, array $taxClassId){
	        $taxRule = $this->taxRuleDataObjectFactory->create();
	        $taxRule->setCode($code);
	        $taxRule->setTaxRateIds($taxRates);
	        $taxRule->setCustomerTaxClassIds([3]);
	        $taxRule->setProductTaxClassIds($taxClassId);
	        $taxRule->setCalculateSubtotal(0);
	        $taxRule->setPriority(0);
	        $taxRule->setPosition(0);
	        $taxRule->save();

	}

	public function resetGST(){
		// @TODO delete all tax classes and rates
	}
}