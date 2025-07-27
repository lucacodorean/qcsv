<?php

namespace Src\Input;

readonly class CommandInput {

    private function __construct(
        private string $command,
        private string $inputStream,
        private string $destinationStream,
        private array $options = [],
        private string $publicKeyStream = "",
        private string $privateKeyStream = "",
    ) {
        ///
    }

    public static function fromOpt() : self {
        $shortOpts = "s:d:c:op::pubk::privk::";
        $longOpts = ["source:", "destination:", "command:","options::", "public_key_path::", "private_key_path::"];

        $parsedArguments = getopt($shortOpts, $longOpts);

        $options = [];
        if(isset($parsedArguments['options'])) $options[] = $parsedArguments['options'];
        if(isset($parsedArguments['o'])) $options[] = $parsedArguments['o'];

        $publicKeyStream = "";
        if(isset($parsedArguments['public_key_path'])) $publicKeyStream = $parsedArguments['public_key_path'];
        else if(isset($parsedArguments['pubk'])) $publicKeyStream = $parsedArguments['pubk'];

        $privateKeyStream = "";
        if(isset($parsedArguments['private_key_path'])) $privateKeyStream = $parsedArguments['private_key_path'];
        else if(isset($parsedArguments['privk'])) $privateKeyStream = $parsedArguments['privk'];

        return new self(
            $parsedArguments["command"] ?? $parsedArguments["c"],
        $parsedArguments["source"] ?? $parsedArguments["s"],
                $parsedArguments["destination"] ?? $parsedArguments["e"],
                $options,
                $publicKeyStream,
            $privateKeyStream
        );
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

    public function getPublicKeyStream(): string {
        return $this->publicKeyStream;
    }

    public function getPrivateKeyStream(): string
    {
        return $this->privateKeyStream;
    }
}