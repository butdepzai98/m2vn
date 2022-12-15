<?php

namespace Cowell\Region\Model\Import\Behavior;

use Magento\ImportExport\Model\Source\Import\AbstractBehavior;

class Region extends AbstractBehavior
{
    const BEHAVIOR_ADD_UPDATE = 'add_update';

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        return [
            self::BEHAVIOR_ADD_UPDATE => __('Add/Update'),
        ];
    }

    /**
     * @inheritDoc
     */
    public function getCode()
    {
        return 'cowellRegion';
    }
}
