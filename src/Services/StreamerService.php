<?php

namespace Src\Services;

use Generator;

interface StreamerService {
    public function stream(string $filepath): Generator;
}