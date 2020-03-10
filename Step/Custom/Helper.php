<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Alod\Migration\Step\Custom;

use Migration\ResourceModel\Destination;

/**
 * Custom Helper
 * Class Helper
 */
class Helper
{
    /**
     * @var Destination
     */
    protected $destination;

    /**
     * @var string
     */
    protected $sourceDocName;

    /**
     * @var string
     */
    protected $destinationDocName;

    /**
     * @var string
     */
    protected $mapConfigOption;

    /**
     * @param Destination $destination
     * @param string $sourceDocName
     * @param string $destinationDocName
     * @param string $mapConfigOption
     */
    public function __construct(
        Destination $destination,
        $sourceDocName,
        $destinationDocName,
        $mapConfigOption
    ) {
        $this->destination = $destination;
        $this->sourceDocName = $sourceDocName;
        $this->destinationDocName = $destinationDocName;
        $this->mapConfigOption = $mapConfigOption;
    }

    /**
     * Get document list
     *
     * @return array
     */
    public function getDocumentList(): array
    {
        return [
            $this->sourceDocName => $this->destinationDocName
        ];
    }

    /**
     * @return string
     */
    public function getMapConfigOption(): string
    {
        return $this->mapConfigOption;
    }
}
