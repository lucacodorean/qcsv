<?php

namespace Src\Commands;

use Src\Domain\DataTable;
use Src\Domain\DataTableInterface;
use Ds\Map;
use Src\Domain\Row;
use Src\Exceptions\InvalidParametersException;

readonly class ReorderCommand implements Command {

    public function __construct(
        private array $newOrder
    ) {
        //
    }

    public function execute(DataTableInterface $initialData): DataTableInterface
    {
        if(count($this->newOrder) != count($initialData->getHeader())) {
            throw new InvalidParametersException("The number of rows do not match");
        }

        try {
            $newTable = new DataTable();
            foreach ($initialData->getIterator() as $row) {
                $orderedRow = new Map;

                foreach ($this->newOrder as $key) {
                    $orderedRow->put($key, $row->get($key));
                }

                $newTable->append(Row::fromMap($orderedRow));
            }
            return $newTable;
        } catch (InvalidParametersException $e) {
            echo $e->getMessage();
            return $initialData;
        }
    }
}
