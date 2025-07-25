<?php

namespace Src\Input;

readonly class CommandInput {

    private function __construct(
        private string $command,
        private string $inputStream,
        private string $destinationStream,
        private array $options = []
    ) {
        ///
    }

    public static function fromOpt() : self {
        $shortOpts = "s:d:c:op::";
        $longOpts = ["source:", "destination:", "command:","options::"];

        $parsedArguments = getopt($shortOpts, $longOpts);

        $options = [];
        if(isset($parsedArguments['options'])) $options[] = $parsedArguments['options'];
        if(isset($parsedArguments['o'])) $options[] = $parsedArguments['o'];

        return new self(
            $parsedArguments["command"] ?? $parsedArguments["c"],
        $parsedArguments["source"] ?? $parsedArguments["s"],
                $parsedArguments["destination"] ?? $parsedArguments["e"],
                $options
        );
    }

    public static function fromCascade(string $command, string $inputStream, string $outputStream, array $options = []): self {
        return new self($command, $inputStream, $outputStream, $options);
    }

    public function getInputStream(): string {
        return $this->inputStream;
    }

    public function getDestinationStream(): string {
        return $this->destinationStream;
    }

    public function getOptions(): array {
        return $this->options;
    }

    public function getCommand(): string {
        return $this->command;
    }
}