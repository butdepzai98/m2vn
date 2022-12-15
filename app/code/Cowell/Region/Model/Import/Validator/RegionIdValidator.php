<?php

namespace Cowell\Region\Model\Import\Validator;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Validation\ValidationResultFactory;
use Cowell\Region\Api\Data\RegionInterface;

class RegionIdValidator implements ValidatorInterface
{
    /**
     * @var ValidationResultFactory
     */
    private $validationResultFactory;

    public function __construct(ValidationResultFactory $validationResultFactory)
    {
        $this->validationResultFactory = $validationResultFactory;
    }

    /**
     * @inheritDoc
     */
    public function validate(array $rowData, int $rowNumber, string $code, string $country_id, array $entityIdListFromDb)
    {
        $errors = [];
        $entityId = $rowData['region_id'];
        if ($entityId && !is_numeric($entityId)) {
            $errors[] = __('region_id  "%id" must be type of numeric, error ', ['id' => $rowData['region_id']]);
        }

        $arrayCountryIds = [];
        foreach ($entityIdListFromDb as $entity) {
            $arrayCountryIds[] = $entity['country_id'];
        }

        if ($entityId && !in_array($entityId, $arrayCountryIds)) {
            $errors[] = __('region_id  "%id" not exits in Database, error ', ['id' => $rowData['region_id']]);
        }

        return $this->validationResultFactory->create(['errors' => $errors]);
    }
}
