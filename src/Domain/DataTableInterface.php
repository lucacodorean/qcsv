<?php

namespace Src\Domain;

use Generator;
use Ds\Vector;


interface DataTableInterface {
    public function getRows(): Vector|Generator;
}