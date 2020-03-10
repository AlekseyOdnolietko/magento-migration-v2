<?php

namespace Alod\Migration\Step\Custom;

use Migration\App\Step\AbstractDelta;
use Migration\Logger\Logger;
use Migration\Logger\Manager as LogManager;
use Migration\Reader\GroupsFactory;
use Migration\Reader\MapInterface;
use Migration\ResourceModel\Source;
use Migration\ResourceModel\Destination;
use Migration\ResourceModel\RecordFactory;
use Migration\Reader\MapFactory;
use Migration\ResourceModel;
use Migration\RecordTransformerFactory;
use Migration\Exception;

/**
 * Custom Delta
 * Class Delta
 */
class Delta extends AbstractDelta
{
    /**
     * @var string
     */
    protected $mapConfigOption;

    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @var Data
     */
    protected $data;

    /**
     * @param Source $source
     * @param MapFactory $mapFactory
     * @param GroupsFactory $groupsFactory
     * @param Logger $logger
     * @param Destination $destination
     * @param RecordFactory $recordFactory
     * @param RecordTransformerFactory $recordTransformerFactory
     * @param Helper $helper
     * @param Data $data
     */
    public function __construct(
        Source $source,
        MapFactory $mapFactory,
        GroupsFactory $groupsFactory,
        Logger $logger,
        Destination $destination,
        RecordFactory $recordFactory,
        RecordTransformerFactory $recordTransformerFactory,
        Helper $helper,
        Data $data
    ) {
        $this->helper = $helper;
        $this->mapConfigOption = $this->helper->getMapConfigOption();
        $this->data = $data;
        parent::__construct(
            $source,
            $mapFactory,
            $groupsFactory,
            $logger,
            $destination,
            $recordFactory,
            $recordTransformerFactory
        );
    }
}
