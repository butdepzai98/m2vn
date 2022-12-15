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
    public function validate(array $rowData, int $rowNumber, string $code, string $country_id, array $entityIdListFromDb)
    {
        $errors = [];
        $countryId = $rowData['country_id'];
        $arrayCountryIds = [];
        foreach ($entityIdListFromDb as $entity) {
            $arrayCountryIds[] = $entity['country_id'];
        }

        if($countryId && !in_array($countryId, $arrayCountryIds)) {
            $errors[] = __('CountryId  "%countryId" is incorrect !', ['countryId' => $rowData['country_id']]);
        }
        return $this->validationResultFactory->create(['errors' => $errors]);
    }
}
