<?php

namespace Src\Utils;

use Src\Exceptions\InvalidParametersException;

class ArgsParser
{
    private static string $shortOpts = "s:d:c:op::";

    private static array $longOpts = ["source:", "destination:", "command:","options::"];

    public static function parseArgs(): array {
        $parsedArguments = getopt(static::$shortOpts, static::$longOpts);
        $formattedArguments = [
            "sourcePath"      => $parsedArguments["source"] ?? $parsedArguments["s"],
            "destinationPath" => $parsedArguments["destination"] ?? $parsedArguments["d"],
            "command"         => $parsedArguments["command"] ?? $parsedArguments["c"],
            "options"         => (array)$parsedArguments["options"] ?? (array)$parsedArguments["op"] ?? "",
        ];

        if(empty($formattedArguments["sourcePath"]) ||
            empty($formattedArguments["destinationPath"]) ||
            empty($formattedArguments["command"])) {
            throw new InvalidParametersException("Mandatory parameters are missing.");
        }

        return $formattedArguments;
    }
}