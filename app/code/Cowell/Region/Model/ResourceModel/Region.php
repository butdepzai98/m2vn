<?php

namespace Cowell\Region\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Region extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('directory_country_region', 'region_id');
    }

    public function saveRegionName($data)
    {
        $datafetch = [
            'locale' => $data[0],
            'region_id' => $data[1],
            'name' => $data[2]
        ];
        $this->getConnection()->insertOnDuplicate('directory_country_region_name', $datafetch,['locale','region_id','name']);
    }

    public function getRegionName($regionId)
    {
        $select = $this->getConnection()->select()->from(
            ['directory_country_region_name']
        )->where('region_id = ' . $regionId);
        $regionName = $this->getConnection()->fetchAll($select);
        return $regionName;
    }
}
