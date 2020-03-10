<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Alod\Migration\Step\Custom;

use Migration\App\Step\StageInterface;
use Migration\Reader\MapFactory;
use Migration\ResourceModel\Destination;
use Migration\ResourceModel\RecordFactory;
use Migration\RecordTransformerFactory;
use Migration\Logger\Manager as LogManager;
use Migration\Logger\Logger;
use Migration\App\ProgressBar\LogLevelProcessor;
use Migration\ResourceModel\Source;

/**
 * Custom Data
 * Class Data
 */
class Data implements StageInterface
{
    /**
     * @var Source
     */
    protected $source;

    /**
     * @var AdapterInterface
     */
    protected $sourceAdapter;

    /**
     * @var Map
     */
    private $map;

    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @var Destination
     */
    protected $destination;

    /**
     * @var LogLevelProcessor
     */
    protected $progress;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var RecordFactory
     */
    protected $recordFactory;

    /**
     * @var RecordTransformerFactory
     */
    protected $transformerFactory;

    /**
     * Data constructor.
     * @param LogLevelProcessor $progress
     * @param Source $source
     * @param Destination $destination
     * @param RecordFactory $recordFactory
     * @param Logger $logger
     * @param MapFactory $mapFactory
     * @param RecordTransformerFactory $transformerFactory
     * @param Helper $helper
     */
    public function __construct(
        LogLevelProcessor $progress,
        Source $source,
        Destination $destination,
        RecordFactory $recordFactory,
        Logger $logger,
        MapFactory $mapFactory,
        RecordTransformerFactory $transformerFactory,
        Helper $helper
    ) {
        $this->source = $source;
        $this->sourceAdapter = $this->source->getAdapter();
        $this->destination = $destination;
        $this->progress = $progress;
        $this->recordFactory = $recordFactory;
        $this->logger = $logger;
        $this->helper = $helper;
        $this->map = $mapFactory->create($this->helper->getMapConfigOption());
        $this->transformerFactory = $transformerFactory;
    }

    /**
     * @return bool
     */
    public function perform(): bool
    {
        $documentList = $this->helper->getDocumentList();
        foreach ($documentList as $sourceDocName => $destinationDocName) {
            $sourceDocument = $this->source->getDocument($sourceDocName);
            $destinationDocument = $this->destination->getDocument($destinationDocName);
            /** @var RecordTransformer $recordTransformer */
            $recordTransformer = $this->transformerFactory->create(
                [
                    'sourceDocument' => $sourceDocument,
                    'destDocument' => $destinationDocument,
                    'mapReader' => $this->map
                ]
            );
            $recordTransformer->init();
            $pageNumber = 0;
            $this->logger->debug('migrating', ['table' => $sourceDocName]);
            $this->progress->start($this->source->getRecordsCount($sourceDocName), LogManager::LOG_LEVEL_INFO);
            while (!empty($records = $this->source->getRecords($sourceDocName, $pageNumber++))) {
                $destCollection = $destinationDocument->getRecords();
                foreach ($records as $record) {
                    $this->progress->advance(LogManager::LOG_LEVEL_INFO);
                    $sourceRecord = $this->recordFactory->create(['document' => $sourceDocument, 'data' => $record]);
                    $destinationRecord = $this->recordFactory->create(['document' => $destinationDocument]);
                    $recordTransformer->transform($sourceRecord, $destinationRecord);
                    $destCollection->addRecord($destinationRecord);
                }
                $this->destination->saveRecords($destinationDocName, $destCollection, true);
            }
            $this->progress->finish(LogManager::LOG_LEVEL_INFO);
        }
        return true;
    }

    /**
     * Get iterations count
     *
     * @return int
     */
    protected function getIterationsCount(): int
    {
        $iterations = 0;
        foreach (array_keys($this->helper->getDocumentList()) as $document) {
            $iterations += $this->source->getRecordsCount($document);
        }
        return $iterations;
    }
}
