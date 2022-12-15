<?php

namespace Cowell\Region\Model\Import\Validator;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Validation\ValidationResultFactory;
use Cowell\Region\Api\Data\RegionInterface;

class CodeValidator implements ValidatorInterface
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
    public function validate(array $rowData, int $rowNumber, array $entityIdListFromDb)
    {
        $errors = [];
        $arrayCodes = [];
        $code = $rowData['code'];
        foreach ($entityIdListFromDb as $entity) {
            $arrayCodes[] = $entity['code'];
        }

        if($code && in_array($code, $arrayCodes)) {
            $errors[] = __('Code  "%code" must be unique !', ['code' => $rowData['code']]);
        }

        return $this->validationResultFactory->create(['errors' => $errors]);
    }
}
