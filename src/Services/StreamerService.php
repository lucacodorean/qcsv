<?php

namespace Src\Services;

interface StreamerService {
    public function stream(string $filepath): Generator;
}