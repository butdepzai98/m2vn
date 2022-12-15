<?php

namespace Cowell\Region\Model\Import\Validator;

interface ValidatorInterface
{
    /**
     * @param array $rowData
     * @param int $rowNumber
     * @param varchar $code
     * @param varchar $country_id
     * @param array $entityIdListFromDb
     * @return mixed
     */
    public function validate(array $rowData, int $rowNumber, string $code, string $country_id, array $entityIdListFromDb);
}
