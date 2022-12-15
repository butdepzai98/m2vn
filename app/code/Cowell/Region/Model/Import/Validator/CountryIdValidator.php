<?php

namespace Cowell\Region\Model\Import\Validator;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Validation\ValidationResultFactory;
use Cowell\Region\Api\Data\RegionInterface;

class CountryIdValidator implements ValidatorInterface
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
        return $this->validationResultFactory->create(['errors' => $errors]);
    }
}
