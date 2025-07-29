<?php

namespace Src\Input;

readonly class CommandInput {

    private function __construct(
        private string $command,
        private string $inputStream = "php://stdin",
        private string $destinationStream = "php://stdout",
        private array $options = [],
        private string $publicKeyStream = "",
        private string $privateKeyStream = "",
        private string $environment = "cli",
    ) {
        ///
    }

    public static function fromOpt() : self {
        $shortOpts = "s::d::c:op::pubk::privk::env::";
        $longOpts = ["source:", "destination:", "command:","options::", "public_key_path::", "private_key_path::", "environment::"];

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
        $parsedArguments["source"] ?? $parsedArguments["s"] ?? "php://stdin" ,
                $parsedArguments["destination"] ?? $parsedArguments["e"] ?? "php://stdout",
                $options,
                $publicKeyStream,
            $privateKeyStream,
            $parsedArguments["environment"] ?? $parsedArguments["env"] ?? "cli"
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

    public function getEnvironment(): string {
        return $this->environment;
    }
}
