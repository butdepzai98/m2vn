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

class Region extends AbstractEntity
{
    const ENTITY_CODE = 'directory_country_region';

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * Table name
     */
    const TABLE = 'directory_country_region';

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
     * @var RequestInterface
     */
    private $requestInterface;

    /**
     * @var RegionCollectionFactory
     */
    protected $regionCollection;

    protected $entityIdListFromDb;

    public function __construct(
        \Magento\Framework\Json\Helper\Data                   $jsonHelper,
        \Magento\ImportExport\Helper\Data                     $importExportData,
        \Magento\ImportExport\Model\ResourceModel\Import\Data $importData,
        \Magento\Eav\Model\Config                             $config,
        ResourceConnection                                    $resource,
        \Magento\ImportExport\Model\ResourceModel\Helper      $resourceHelper,
        \Magento\Framework\Stdlib\StringUtils                 $string,
        ProcessingErrorAggregatorInterface                    $errorAggregator,
        PublisherInterface                                    $publisher,
        ValidatorInterface                                    $validator,
        RequestInterface                                      $requestInterface,
        RegionCollectionFactory                               $regionCollection
    )
    {
        parent::__construct(
            $jsonHelper,
            $importExportData,
            $importData,
            $config,
            $resource,
            $resourceHelper,
            $string,
            $errorAggregator
        );
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
            $standardShippingDatesToCreate = [];
            $standardShippingDatesToUpdate = [];
            $standardShippingDatesData = [];

            foreach ($bunch as $rowNum => $rowData) {
                if (!$this->validateRow($rowData, $rowNum)) {
                    continue;
                }
                if ($this->getErrorAggregator()->hasToBeTerminated()) {
                    $this->getErrorAggregator()->addRowToSkip($rowNum);
                    continue;
                }

                // prepare data to count
                if ($rowData['id']) {
                    $standardShippingDatesToUpdate[] = 'data update';
                } else {
                    $standardShippingDatesToCreate[] = 'data create';
                }

                $standardShippingDatesData[] = $this->prepareProductStandardShippingDateData($rowData);
            }

            // count record created to report
            $dataCreate = $standardShippingDatesToCreate;
            $this->updateItemsCounterStats($dataCreate, $standardShippingDatesToUpdate);
            try {
                $this->connection->beginTransaction();

                if ($this->getBehavior() == ProductStandardShippingDateBehavior::BEHAVIOR_ADD_UPDATE) {
                    $this->addUpdateStandardShippingDate($standardShippingDatesData);
                }

                $this->connection->commit();
            } catch (Exception $e) {
                $this->connection->rollBack();
                throw $e;
            }
        }

//        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
//            $standardShippingDatesToCreate = [];
//            $standardShippingDatesToUpdate = [];
//            $standardShippingDatesData = [];
//
//            foreach ($bunch as $rowNum => $rowData) {
//                if (!$this->validateRow($rowData, $rowNum)) {
//                    continue;
//                }
//                if ($this->getErrorAggregator()->hasToBeTerminated()) {
//                    $this->getErrorAggregator()->addRowToSkip($rowNum);
//                    continue;
//                }
//
//                // prepare data to count
//                if ($rowData['id']) {
//                    $standardShippingDatesToUpdate[] = 'data update';
//                } else {
//                    $standardShippingDatesToCreate[] = 'data create';
//                }
//
//                $standardShippingDatesData[] = $this->prepareProductStandardShippingDateData($rowData);
//            }
//
//            // count record created to report
//            $dataCreate = $standardShippingDatesToCreate;
//            $this->updateItemsCounterStats($dataCreate, $standardShippingDatesToUpdate);
//            try {
//                $this->connection->beginTransaction();
//
//                if ($this->getBehavior() == ProductStandardShippingDateBehavior::BEHAVIOR_ADD_UPDATE) {
//                    $this->addUpdateStandardShippingDate($standardShippingDatesData);
//                }
//
//                $this->connection->commit();
//            } catch (Exception $e) {
//                $this->connection->rollBack();
//                throw $e;
//            }
//        }
    }

    /**
     * @inheritDoc
     */
    public function validateRow(array $rowData, $rowNum)
    {
        $code = $this->requestInterface->getParam('code');;
        $country_id = $this->requestInterface->getParam('country_id');;
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
            $this->entityIdListFromDb[] = $item['region_id'];
        }
    }
}
