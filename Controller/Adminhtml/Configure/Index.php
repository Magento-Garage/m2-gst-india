<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MagentoGarage\GSTIndia\Controller\Adminhtml\Configure;

use Magento\Framework\Controller\Result\JsonFactory;

/**
 * GST configuration controller
 *
 * @author     Aman Srivastava <credevator@outlook.com>
 */
class Index extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_Customer::manage';

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * Check whether vat is valid
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $result = $this->_validate();

        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData([
            'valid' => (int)$result->getIsValid(),
            'message' => $result->getMessage(),
        ]);
    }

    /**
     * Perform customer VAT ID validation
     *
     * @return \Magento\Framework\DataObject
     */
    protected function _validate()
    {
        return $this->_objectManager->get(\MagentoGarage\GSTIndia\Model\GST::class)
            ->configureGST(
                $this->getRequest()->getParam('region')
            );
    }
}
