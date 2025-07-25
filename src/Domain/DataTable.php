<?php

namespace Src\Domain;

use Ds\Vector;

class DataTable implements 
{
    private Vector $rows;
    public function __construct(){
        $this->rows = new Vector;
    }

    public function append(Row $row): void {
        $this->rows->push($row);
    }

    public function getRows(): Vector {
        return $this->rows;
    }

    public function prepend(Row $row): void {
        $this->rows->unshift($row);
    }

    public function print(): void {
        foreach($this->rows as $row){
            $row->printKeys();
            echo PHP_EOL;
        }
    }
}