<?php

namespace Cowell\Region\Model\Import\Validator;

interface ValidatorInterface
{
    /**
     * @param array $rowData
     * @param int $rowNumber
     * @param int $code (unique in Country)
     * @param int $country_id (is in DB)
     * @return mixed
     */
    public function validate(array $rowData, int $rowNumber, int $code, int $country_id, array $entityIdListFromDb);
}
