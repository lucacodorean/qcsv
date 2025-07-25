<?php

namespace Src\Commands;


use Src\Domain\DataTableInterface;
use Src\Domain\Row;
use Src\Exceptions\InvalidParametersException;

readonly class PrependCommand implements Command
{
    public function __construct(
        private array $newHeader,
    ) {
        //
    }

    public function execute(DataTableInterface $initialData): DataTableInterface
    {
        try {
            $initialData->prepend(new Row($this->newHeader, $this->newHeader));
            return $initialData;
        } catch (InvalidParametersException $e) {
            echo $e->getMessage();
            return new DataTableInterface();
        }
    }
}