<?php

namespace Cowell\Region\Model\Config\Source;

class Region implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Retrieve Custom Option array
     *
     * @return array
     */

    protected $collectionFactory;
    public function __construct(
        \Cowell\Region\Model\ResourceModel\Region\CollectionFactory $collectionFactory,
    ) {
        $this->collectionFactory = $collectionFactory;
    }
    public function toOptionArray()
    {
        $countryId = $this->collectionFactory->create()->addFieldToSelect('country_id')->getItems();
        $countryIds = [];
        $checkDuplicate = '';
        foreach ($countryId as $item) {
            if ($checkDuplicate!=$item) {
                $countryIds[] = ['label' => __($item['country_id']), 'value' => $item['country_id']];
            }
            $checkDuplicate = $item;
        }
        return $countryIds;
    }
}
