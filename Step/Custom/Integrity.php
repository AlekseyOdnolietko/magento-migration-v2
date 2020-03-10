<?php

namespace Alod\Migration\Step\Custom;

use Migration\Config;
use Migration\Reader\GroupsFactory;
use Migration\Reader\MapFactory;
use Migration\Reader\MapInterface;
use Migration\ResourceModel\Source;
use Migration\ResourceModel\Destination;
use Migration\Logger\Logger;
use Migration\App\ProgressBar\LogLevelProcessor;
use Migration\App\Step\AbstractIntegrity;

/**
 * Custom Integrity
 * Class Integrity
 */
class Integrity extends AbstractIntegrity
{
    /**
     * @var Helper
     */
    protected $helper;

    /**
     * Integrity constructor.
     * @param LogLevelProcessor $progress
     * @param Logger $logger
     * @param Config $config
     * @param Source $source
     * @param Destination $destination
     * @param MapFactory $mapFactory
     * @param Helper $helper
     */
    public function __construct(
        LogLevelProcessor $progress,
        Logger $logger,
        Config $config,
        Source $source,
        Destination $destination,
        MapFactory $mapFactory,
        Helper $helper
    ) {
        $this->helper = $helper;
        $mapConfigOption = $helper->getMapConfigOption();
        parent::__construct($progress, $logger, $config, $source, $destination, $mapFactory, $mapConfigOption);
    }

    /**
     * @return bool
     */
    public function perform(): bool
    {
        $this->progress->start($this->getIterationsCount());
        $documentList = $this->helper->getDocumentList();
        $sourceDocNames = $destinationDocNames = [];
        foreach ($documentList as $sourceDocName => $destinationDocName) {
            $sourceDocNames[] = $sourceDocName;
            $destinationDocNames[] = $destinationDocName;
        }
        $this->check($sourceDocNames, MapInterface::TYPE_SOURCE);
        $this->check($destinationDocNames, MapInterface::TYPE_DEST);
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
        return count($this->helper->getDocumentList());
    }
}
