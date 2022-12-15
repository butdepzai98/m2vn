<?php

namespace Cowell\Region\Model\Import\Validator;

interface ValidatorInterface
{
    /**
     * @param array $rowData
     * @param int $rowNumber
     * @param array $entityIdListFromDb
     * @return mixed
     */
    public function validate(array $rowData, int $rowNumber, array $entityIdListFromDb);
}
