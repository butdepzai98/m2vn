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
    public function validate(array $rowData, int $rowNumber, int $code, int $country_id, array $entityIdListFromDb)
    {
        $errors = [];
        $entityId = $rowData['id'];
        if ($entityId && !is_numeric($entityId)) {
            $errors[] = __('ID  "%id" must be type of numeric, error ', ['id' => $rowData['id']]);
        }
        if ($entityId && !in_array($entityId, $entityIdListFromDb)) {
            $errors[] = __('ID  "%id" not exits in Database, error ', ['id' => $rowData['id']]);
        }

        return $this->validationResultFactory->create(['errors' => $errors]);
    }
}
