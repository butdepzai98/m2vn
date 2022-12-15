<?php

namespace Cowell\Region\Model;

use Magento\Framework\Model\AbstractExtensibleModel;

class Region extends AbstractExtensibleModel
{
    protected function _construct()
    {
        $this->_init('Cowell\Region\Model\ResourceModel\Region');
    }
}
