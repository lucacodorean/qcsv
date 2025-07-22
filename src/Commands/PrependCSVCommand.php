<?php

namespace Src\Commands;

use Src\Services\StreamerService;

readonly class PrependCSVCommand implements Command
{
    public function __construct(
        private StreamerService $streamerService
    ) {
        //
    }

    public function execute(string $filePath, array $options = []): void
    {
        foreach ($this->streamerService->stream($filePath) as $line) {

        }
    }
}