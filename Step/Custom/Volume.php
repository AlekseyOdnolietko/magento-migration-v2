<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Alod\Migration\Step\Custom;

use Migration\App\Step\AbstractVolume;
use Migration\Logger\Logger;
use Migration\Reader\MapFactory;
use Migration\ResourceModel;
use Migration\App\ProgressBar\LogLevelProcessor;
use Migration\ResourceModel\Source;
use Migration\ResourceModel\Destination;

/**
 * Custom Volume
 * Class Volume
 */
class Volume extends AbstractVolume
{
    /**
     * @var Source
     */
    private $source;

    /**
     * @var Destination
     */
    private $destination;

    /**
     * @var Map
     */
    private $map;

    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @var LogLevelProcessor
     */
    private $progress;

    /**
     * Volume constructor.
     * @param Logger $logger
     * @param Source $source
     * @param Destination $destination
     * @param MapFactory $mapFactory
     * @param LogLevelProcessor $progress
     * @param Helper $helper
     */
    public function __construct(
        Logger $logger,
        Source $source,
        Destination $destination,
        MapFactory $mapFactory,
        LogLevelProcessor $progress,
        Helper $helper
    ) {
        $this->source = $source;
        $this->destination = $destination;
        $this->helper = $helper;
        $this->map = $mapFactory->create($this->helper->getMapConfigOption());
        $this->progress = $progress;
        parent::__construct($logger);
    }

    /**
     * @return bool
     */
    public function perform(): bool
    {
        $documentList = $this->helper->getDocumentList();
        $this->progress->start(count($documentList));
        foreach ($documentList as $sourceDocName => $destinationDocName) {
            $sourceCount = $this->source->getRecordsCount($sourceDocName);
            $destinationCount = $this->destination->getRecordsCount($destinationDocName);
            if ($sourceCount != $destinationCount) {
                $this->errors[] = sprintf(
                    'Mismatch of entities in the document: %s Source: %s Destination: %s',
                    $destinationDocName,
                    $sourceCount,
                    $destinationCount
                );
            }
        }
        $this->progress->finish();
        return $this->checkForErrors();
    }

    /**
     * Get iterations count for step
     *
     * @return int
     */
    protected function getIterationsCount(): int
    {
        $migrationDocuments = $this->helper->getDocumentList();
        $documents = [
            array_keys($migrationDocuments),
            array_values($migrationDocuments)
        ];
        return count($documents);
    }
}
