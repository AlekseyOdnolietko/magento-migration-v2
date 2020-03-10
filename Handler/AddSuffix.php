<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Alod\Migration\Handler;

use Migration\ResourceModel\Record;
use Migration\Handler\AbstractHandler;
use Migration\Handler\HandlerInterface;

/**
 * Handler to set constant value to the field
 * Class AddSuffix
 */
class AddSuffix extends AbstractHandler implements HandlerInterface
{
    /**
     * @var string
     */
    protected $suffix;

    /**
     * AddSuffix constructor.
     * @param string $suffix
     */
    public function __construct($suffix)
    {
        $this->suffix = $suffix;
    }

    /**
     * @param Record $recordToHandle
     * @param Record $oppositeRecord
     * @throws \Migration\Exception
     * @return void
     */
    public function handle(Record $recordToHandle, Record $oppositeRecord): void
    {
        $this->validate($recordToHandle);
        $recordToHandle->setValue($this->field, $recordToHandle->getValue($this->field) . $this->suffix);
    }
}
