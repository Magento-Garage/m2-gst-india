<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Adminhtml Configure GST validation block
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace MagentoGarage\GSTIndia\Block\Adminhtml\System\Config;

class ConfigureGST extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * Merchant State Field Name
     *
     * @var string
     */
    protected $_merchantRegion = 'general_store_information_region_id';

    /**
     * Validate GST Button Label
     *
     * @var string
     */
    protected $_vatButtonLabel = 'Configure GST';

    /**
     * Set Merchant Country Field Name
     *
     * @param string $regionField
     * @return \Magento\Customer\Block\Adminhtml\System\Config\Validatevat
     */
    public function setMerchantRegionField($regionField)
    {
        $this->_merchantRegion = $regionField;
        return $this;
    }

    /**
     * Get Merchant Region Field Name
     *
     * @return string
     */
    public function getMerchantRegionField()
    {
        return $this->_merchantRegion;
    }

    /**
     * Set Validate VAT Button Label
     *
     * @param string $vatButtonLabel
     * @return \Magento\Customer\Block\Adminhtml\System\Config\Validatevat
     */
    public function setVatButtonLabel($vatButtonLabel)
    {
        $this->_vatButtonLabel = $vatButtonLabel;
        return $this;
    }

    /**
     * Set template to itself
     *
     * @return \Magento\Customer\Block\Adminhtml\System\Config\Validatevat
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (!$this->getTemplate()) {
            $this->setTemplate('MagentoGarage_GSTIndia::system/config/configure-gst.phtml');
        }
        return $this;
    }

    /**
     * Unset some non-related element parameters
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * Get the button and scripts contents
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $originalData = $element->getOriginalData();
        $buttonLabel = !empty($originalData['button_label']) ? $originalData['button_label'] : $this->_vatButtonLabel;
        $this->addData(
            [
                'button_label' => __($buttonLabel),
                'html_id' => $element->getHtmlId(),
                'ajax_url' => $this->_urlBuilder->getUrl('gst/configure/index'),
            ]
        );

        return $this->_toHtml();
    }
}
