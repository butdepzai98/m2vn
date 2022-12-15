<?php

namespace Cowell\Region\Api\Data;

interface RegionSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface

{
    /**
     * Get Region list.
     * @return \Cowell\Region\Api\Data\RegionInterface[]
     */
    public function getItems();

    /**
     * Set region_code list.
     * @param \Cowell\Region\Api\Data\RegionInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
