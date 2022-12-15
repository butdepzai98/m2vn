<?php

namespace Cowell\Region\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface RegionRepositoryInterface
{
    /**
     * Save Region
     * @param \Cowell\Region\Api\Data\RegionInterface $region
     * @return \Cowell\Region\Api\Data\RegionInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Cowell\Region\Api\Data\RegionInterface $region
    );

    /**
     * Retrieve Region
     * @param string $regionId
     * @return \Cowell\Region\Api\Data\RegionInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($regionId);

    /**
     * Retrieve Region matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Cowell\Region\Api\Data\RegionSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Region
     * @param \Cowell\Region\Api\Data\RegionInterface $region
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Cowell\Region\Api\Data\RegionInterface $region
    );

    /**
     * Delete Region by ID
     * @param string $regionId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($regionId);
}
