<?php

namespace Cowell\Region\Model\Import\Validator;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Validation\ValidationResultFactory;

class ValidatorChain implements ValidatorInterface
{
    /**
     * @var ValidationResultFactory
     */
    private $validationResultFactory;

    /**
     * @var ValidatorInterface[]
     */
    private $validators;

    public function __construct(
        ValidationResultFactory $validationResultFactory,
        array                   $validators = []
    )
    {
        $this->validationResultFactory = $validationResultFactory;

        foreach ($validators as $validator) {
            if (!$validator instanceof ValidatorInterface) {
                throw new LocalizedException(
                    __('Row Validator must implement %interface.', ['interface' => ValidatorInterface::class])
                );
            }
        }
        $this->validators = $validators;
    }

    public function validate(array $rowData, int $rowNumber, int $code, int $country_id, array $entityIdListFromDb)
    {
        /* the inner empty array covers cases when no loops were made */
        $errors = [[]];
        foreach ($this->validators as $validator) {
            $validationResult = $validator->validate($rowData, $rowNumber, $code, $country_id, $entityIdListFromDb);

            if (!$validationResult->isValid()) {
                $errors[] = $validationResult->getErrors();
            }
        }

        return $this->validationResultFactory->create(['errors' => array_merge(...$errors)]);
    }
}
