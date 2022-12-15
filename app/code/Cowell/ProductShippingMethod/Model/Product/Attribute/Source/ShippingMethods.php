<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Cowell\ProductShippingMethod\Model\Product\Attribute\Source;

class ShippingMethods extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    protected $scopeConfig;
    protected $shipconfig;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Shipping\Model\Config $shipconfig
    ) {
        $this->shipconfig = $shipconfig;
        $this->scopeConfig = $scopeConfig;
    }

    public function getAllOptions()
    {
        $activeCarriers = $this->shipconfig->getActiveCarriers();

        if (!$this->_options) {
            foreach ($activeCarriers as $carrierCode => $carrierModel) {
                $options = array();
                if ($carrierMethods = $carrierModel->getAllowedMethods()) {
                    foreach ($carrierMethods as $methodCode => $method) {
                        $code = $methodCode;
                        $options[] = array('value' => $code, 'label' => $method);
                    }
                    $carrierTitle = $this->scopeConfig
                        ->getValue('carriers/'.$carrierCode.'/title');
                    $this->_options[] = array('value' => $options, 'label' => $carrierTitle);
                }
            }
        }
        return $this->_options;
    }
}
