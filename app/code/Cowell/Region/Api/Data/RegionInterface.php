<?php

namespace Cowell\Region\Api\Data;

interface RegionInterface
{
    const REGION_ID = 'region_id';
    const COUNTRY_ID = 'country_id';
    const CODE = 'code';
    const DEFAULT_NAME = 'default_name';

    public function getRegionId();
    public function setRegionId();

    public function getCountryId();
    public function setCountryId();

    public function getCode();
    public function setCode();

    public function getDefaultName();
    public function setDefaultName();
}
