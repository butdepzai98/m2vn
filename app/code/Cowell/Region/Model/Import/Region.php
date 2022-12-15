<?php

namespace Cowell\Region\Model\Import;

use Magento\Framework\App\ResourceConnection;
use Magento\ImportExport\Model\Import\Entity\AbstractEntity;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Cowell\Region\Model\Import\Validator\ValidatorInterface;
use Magento\Directory\Model\ResourceModel\Region\CollectionFactory as RegionCollectionFactory;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use Magento\Framework\MessageQueue\PublisherInterface;
use Cowell\Region\Model\Import\Behavior\Region as RegionBehavior;

class Region extends AbstractEntity
{
    const ENTITY_CODE = 'cowell_region';

    /**
     * Table name
     */
    const TABLE = 'directory_country_region';

    const ENTITY_ID_COLUMN = 'region_id';

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    protected $publisher;

    /**
     * If we should check column names
     */
    protected $needColumnCheck = true;

    /**
     * Need to log in import history
     */
    protected $logInHistory = true;

    /**
     * Valid column names
     */
    protected $validColumnNames = [
        'region_id',
        'country_id',
        'code',
        'default_name'
    ];

    /**
     * @var AdapterInterface
     */
    private AdapterInterface $connection;

    /**
     * Permanent entity columns.
     */
    protected $_permanentAttributes = [
        'region_id'
    ];

    /**
     * @var RequestInterface
     */
    private $requestInterface;

    /**
     * @var RegionCollectionFactory
     */
    protected $regionCollection;

    protected $entityIdListFromDb;

    /**
     * @var ResourceConnection
     */
    private $resource;

    public function __construct(
        \Magento\Framework\Json\Helper\Data                   $jsonHelper,
        \Magento\ImportExport\Helper\Data                     $importExportData,
        \Magento\ImportExport\Model\ResourceModel\Import\Data $importData,
        ResourceConnection                                    $resource,
        \Magento\ImportExport\Model\ResourceModel\Helper      $resourceHelper,
        ProcessingErrorAggregatorInterface                    $errorAggregator,
        PublisherInterface                                    $publisher,
        ValidatorInterface                                    $validator,
        RequestInterface                                      $requestInterface,
        RegionCollectionFactory                               $regionCollection
    )
    {
        $this->jsonHelper = $jsonHelper;
        $this->_importExportData = $importExportData;
        $this->_resourceHelper = $resourceHelper;
        $this->_dataSourceModel = $importData;
        $this->resource = $resource;
        $this->connection = $resource->getConnection(ResourceConnection::DEFAULT_CONNECTION);
        $this->errorAggregator = $errorAggregator;
        $this->publisher = $publisher;
        $this->validator = $validator;
        $this->requestInterface = $requestInterface;
        $this->regionCollection = $regionCollection;
        $this->loadRegionIds();
    }

    /**
     * @inheritDoc
     */
    protected function _importData()
    {
        $this->addEntity();
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getEntityTypeCode()
    {
        return self::ENTITY_CODE;
    }

    /**
     * Import behavior getter.
     *
     * @return string
     */
    public function getBehavior()
    {
        return $this->_parameters['behavior'];
    }

    public function addEntity()
    {
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            $dataToCreate = [];
            $dataToUpdate = [];
            $dataToImport = [];

            foreach ($bunch as $rowNum => $rowData) {
                if (!$this->validateRow($rowData, $rowNum)) {
                    continue;
                }
                if ($this->getErrorAggregator()->hasToBeTerminated()) {
                    $this->getErrorAggregator()->addRowToSkip($rowNum);
                    continue;
                }

                // prepare data to count
                if ($rowData['region_id']) {
                    $dataToUpdate[] = 'data update';
                } else {
                    $dataToCreate[] = 'data create';
                }

                $dataToImport[] = $this->prepareDataToImport($rowData);
            }

            // count record created to report
            $dataCreate = $dataToCreate;
            $this->updateItemsCounterStats($dataCreate, $dataToUpdate);
            try {
                $this->connection->beginTransaction();

                if ($this->getBehavior() == RegionBehavior::BEHAVIOR_ADD_UPDATE) {
                    $this->addUpdateRegion($dataToImport);
                }

                $this->connection->commit();
            } catch (Exception $e) {
                $this->connection->rollBack();
                throw $e;
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function validateRow(array $rowData, $rowNum)
    {
        $code = $this->requestInterface->getParam('code') ?? "";
        $country_id = $this->requestInterface->getParam('country_id') ?? "";
        $result = $this->validator->validate($rowData, $rowNum, $code, $country_id, $this->entityIdListFromDb);

        if ($result->isValid()) {
            return true;
        }

        foreach ($result->getErrors() as $error) {
            $this->addRowError($error, $rowNum);
        }

        return false;
    }

    /**
     * Update proceed items counter
     *
     * @param array $created
     * @param array $updated
     * @param array $deleted
     * @return $this
     */
    protected function updateItemsCounterStats(array $created = [], array $updated = [])
    {
        $this->countItemsCreated += count($created);
        $this->countItemsUpdated += count($updated);
        return $this;
    }

    /**
     * load Region Ids from DB
     * @return void
     */
    protected function loadRegionIds()
    {
        $regions = $this->regionCollection->create();
        foreach ($regions->getData() as $item) {
            $this->entityIdListFromDb[] = [
                'country_id' => $item['country_id'],
                'code' => $item['code'],
                'region_id' => $item['region_id']
            ];
        }
    }

    /**
     * prepare Data To Import
     * @param $rowData
     * @return array
     */
    public function prepareDataToImport($rowData): array
    {
        $entityRow = [];
        $entityRow['region_id'] = $rowData['region_id'];
        $entityRow['country_id'] = $rowData['country_id'];
        $entityRow['code'] = $rowData['code'];
        $entityRow['default_name'] = $rowData['default_name'];
        return $entityRow;
    }

    /**
     * @param array $entitiesToUpdate
     * @return $this
     */
    public function addUpdateRegion(array $entitiesToUpdate)
    {
        $this->connection->insertOnDuplicate(self::TABLE, $entitiesToUpdate);
        return $this;
    }
}
