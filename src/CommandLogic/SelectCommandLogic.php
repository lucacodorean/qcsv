<?php

namespace Src\CommandLogic;

use Src\Domain\DataTable;
use Src\Domain\DataTableInterface;
use Src\Domain\Row;
use Src\Exceptions\InvalidParametersException;
use Src\Utils\SelectCondition;

readonly class SelectCommandLogic implements Command
{
    /**
     * @param array $columns
     * @param array|SelectCondition[] $conditions
     */
    public function __construct(
        private array $columns = [],
        private array $conditions = [],
    ) {
        ///
    }


    private function filterPass(Row $row): ?bool {
        foreach ($this->conditions as $condition) {
            if(!$row->evaluateCondition($condition)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @throws InvalidParametersException
     */
    public function execute(DataTableInterface $initialData): DataTableInterface {

        $resultData = new DataTable();
        foreach($initialData->getIterator() as $row) {
            if($this->filterPass($row)) {
                $filtered = $row->withColumns($this->columns);
                $resultData->append($filtered);
            }
        }

        return $resultData;
    }

}
