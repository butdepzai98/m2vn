<?php

namespace Cowell\Region\Model\ResourceModel\Region\Grid;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;
use Psr\Log\LoggerInterface as Logger;

class Collection extends SearchResult
{
    /**
     * @var string
     */
    protected $_idFieldName = 'region_id';

    public function __construct(
        EntityFactory $entityFactory,
        Logger        $logger,
        FetchStrategy $fetchStrategy,
        EventManager  $eventManager,
                      $mainTable = 'directory_country_region',
                      $resourceModel = 'Cowell\Region\Model\ResourceModel\Region',
                      $identifierName = null,
                      $connectionName = null
    )
    {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel, $identifierName, $connectionName);
    }

    /**
     * @return Collection|void
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        // Join width "directory_country_region_name" and "directory_country"
        $this->getSelect()
            ->joinLeft(
                ['dcrn' => $this->getConnection()->getTableName('directory_country_region_name')],
                'main_table.region_id = dcrn.region_id',
                ['name']
            )->joinLeft(
                ['dc' => $this->getTable('directory_country')],
                'main_table.country_id = dc.country_id',
                ['iso3_code']);
        $this->addFilterToMap('region_id', 'main_table.region_id');
        $this->addFilterToMap('country_id', 'main_table.country_id');
    }
}
