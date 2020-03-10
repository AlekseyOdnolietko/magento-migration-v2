<?php

namespace Alod\Migration\Step\Custom\Split\Main;

use Alod\Migration\Step\Custom\Delta as CustomDelta;
use Migration\Reader\MapInterface;

/**
 * Split Main Delta
 * Class Delta
 */
class Delta extends CustomDelta
{
    /**
     * @var string
     */
    protected $groupName = 'delta_split';

    /**
     * Process deleted records
     *
     * @param string $documentName
     * @param array $idKeys
     * @param string $destinationName
     * @return void
     */
    protected function processDeletedRecords($documentName, $idKeys, $destinationName)
    {
        $this->destination->getAdapter()->setForeignKeyChecks(1);
        $items = $this->source->getDeletedRecords($documentName, $idKeys);
        if ($items) {
            echo('.');
            $this->destination->deleteRecords(
                $this->destination->addDocumentPrefix($destinationName),
                $idKeys,
                $items
            );
            $documentNameDelta = $this->source->getDeltaLogName($documentName);
            $documentNameDelta = $this->source->addDocumentPrefix($documentNameDelta);
        }
        $this->destination->getAdapter()->setForeignKeyChecks(0);
    }

    /**
     * Process changed records
     *
     * @param string $documentName
     * @param array $idKeys
     * @return void
     */
    protected function processChangedRecords($documentName, $idKeys)
    {
        $destinationName = $this->getDocumentMap($documentName, MapInterface::TYPE_SOURCE);
        $sourceDocument = $this->source->getDocument($documentName);
        $destDocument = $this->destination->getDocument($destinationName);
        $recordTransformer = $this->getRecordTransformer($sourceDocument, $destDocument);
        $items = $this->source->getChangedRecords($documentName, $idKeys);
        if ($items) {
            $destinationRecords = $destDocument->getRecords();
            foreach ($items as $data) {
                echo('.');
                $this->transformData(
                    $data,
                    $sourceDocument,
                    $destDocument,
                    $recordTransformer,
                    $destinationRecords
                );
            }
            $fieldsUpdateOnDuplicate = (!empty($this->documentsDuplicateOnUpdate[$destinationName]))
                ? $this->documentsDuplicateOnUpdate[$destinationName]
                : false;
            $this->updateChangedRecords($destinationName, $destinationRecords, $fieldsUpdateOnDuplicate);
            $documentNameDelta = $this->source->getDeltaLogName($documentName);
            $documentNameDelta = $this->source->addDocumentPrefix($documentNameDelta);
        }
    }
}
