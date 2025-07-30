<?php

namespace Src\CommandLogic;

use Src\Domain\DataTable;
use Src\Domain\DataTableInterface;
use Ds\Map;

class JoinCommandLogic implements Command
{
    public function __construct(
        private DataTableInterface $secondTable,
        private string $columnInFirstTable,
        private string $columnInSecondTable,
    ) {
        ///
    }

    private function encodeRow(array $row): string {
        $json = json_encode($row);
        $compressed = gzcompress($json);
        return base64_encode($compressed);
    }

    private function decodeRow(string $encoded): array {
        $compressed = base64_decode($encoded);
        $json = gzuncompress($compressed);
        return json_decode($json, true);
    }

    private function convertData(DataTableInterface $dataTable): Map {

        $collectedData = new Map;
        foreach ($dataTable->getIterator() as $row) {
            $rowArr = $row->toArray();
            unset($rowArr[$this->columnInSecondTable]);
            $collectedData->put($row->get($this->columnInSecondTable), $this->encodeRow($rowArr));
        }

        return $collectedData;
    }

    public function execute(DataTableInterface $initialData): DataTableInterface {
        $joinedDataTable = new DataTable;
        $dataFromSecondTable = $this->convertData($this->secondTable);

        foreach($initialData->getIterator() as $row) {
            if($dataFromSecondTable->hasKey($row->get($this->columnInFirstTable))) {
                $joiningRowData = $dataFromSecondTable->get($row->get($this->columnInFirstTable));
                $decoded = $this->decodeRow($joiningRowData);

                foreach ($decoded as $key => $value) {
                    $row->set($key, $value);
                }

                $joinedDataTable->append($row);
            }
        }

        return $joinedDataTable;
    }
}
