<?php
declare(strict_types=1);

interface IFunction
{
    public function handle(string $args = ""): string;
}

class DisplayCongratulatoryDialogHandler implements IFunction
{
    public function handle(string $args = ""): string
    {
        $argsArray = json_decode($args, true);
        $message = $argsArray["message"] ?? "CONGRATULATIONS";
        echo "Triggered congratulations with message: $message\n";
        return "Displayed message: '$message'.";
    }
}

class BakeCakeHandler implements IFunction
{
    public function handle(string $args = ""): string
    {
        echo "Triggered bake cake.\n";
        return "Cake has been baked successfully.";
    }
}

class FunctionHandler
{
    private array $supportedFunctions;

    public function __construct()
    {
        $this->supportedFunctions = [
            "displayCongratulatoryDialog" => new DisplayCongratulatoryDialogHandler(),
            "bakeCake" => new BakeCakeHandler(),
        ];
    }

    public function addFunction($name, IFunction $function)
    {
        $this->supportedFunctions[$name] = $function;
    }

    public function handleFunction(FunctionCall $functionCall): string
    {
        $name = $functionCall->name;
        // checks if function exists
        if (!isset($this->supportedFunctions[$name])) {
            throw new Exception(
                "The function does not exist. Please check and correct this issue."
            );
        }
        $handler = $this->supportedFunctions[$name];
        $args = $functionCall->arguments;
        // checks if function arguments are correct
        foreach (
            $functionCall->paramsDefinition["properties"]
            as $param => $paramDefinition
        ) {
            if (!isset($args[$param]) && $paramDefinition["isRequired"]) {
                throw new Exception(
                    "This function exists but you are calling with wrong arguments. Please check and correct this issue."
                );
            }
        }
        return $handler->handle(json_encode($args));
    }
}
