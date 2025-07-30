<?php

namespace Src\Dto;

use Symfony\Component\Validator\Constraints as Assert;


class DataTableSelectRequest
{
    #[Assert\NotBlank(
        message: 'Data Table is missing.',
    )]
    public string $tableData;

    #[Assert\NotBlank(
        message: 'Inserting at least one column is required.',
    )]
    public array $columns;
    public ?array $conditionals = null;
}
