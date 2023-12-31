<?php
declare(strict_types=1);

class OpenAIClientManager
{
    private $client;

    public function __construct(string $apiKey)
    {
        $this->client = OpenAI::client($apiKey);
    }

    public function performConversation(
        Conversation $conversation,
        array $functions = [],
        string $model = "gpt-3.5-turbo"
    ): void {
        $messages = $conversation->toArray();

        $functionList = [];
        foreach ($functions as $function) {
            if ($function instanceof FunctionDefinition) {
                $functionList[] = $function->getDefinition();
            }
        }

        $payload = [
            "model" => $model,
            "messages" => $messages,
        ];

        if (!empty($functionList)) {
            $payload["functions"] = $functionList;
        }

        $response = $this->client->chat()->create($payload);

        $result = $response->choices[0];

        $assistantMessage = new AssistantMessage(
            $result->message->content,
            isset($result->message->functionCall)
                ? new FunctionCall(
                    $result->message->functionCall->name,
                    $result->message->functionCall->arguments
                )
                : null
        );

        $conversation->addMessage($assistantMessage);
    }

    public function getFunctionDefinition($functionName, $functions): array
    {
        foreach ($functions as $function) {
            if ($function->getDefinition()["name"] == $functionName) {
                return $function->getDefinition()["parameters"];
            }
        }
        return [];
    }
}

class ParameterDefinition
{
    private string $name;
    private string $type;
    private string $description;
    private bool $isRequired;

    public function __construct(
        string $name,
        string $type,
        string $description,
        bool $isRequired
    ) {
        $this->name = $name;
        $this->type = $type;
        $this->description = $description;
        $this->isRequired = $isRequired;
    }

    public function getDefinition(): array
    {
        return [
            "name" => $this->name,
            "type" => $this->type,
            "description" => $this->description,
            "isRequired" => $this->isRequired,
        ];
    }
}

class FunctionDefinition
{
    private string $name;
    private string $description;
    private array $parameters;

    public function __construct(
        string $name,
        string $description,
        array $parameters = []
    ) {
        $this->name = $name;
        $this->description = $description;
        $this->parameters = [];

        foreach ($parameters as $parameter) {
            if ($parameter instanceof ParameterDefinition) {
                $this->parameters[
                    $parameter->getDefinition()["name"]
                ] = $parameter->getDefinition();
            }
        }
    }

    public function getDefinition(): array
    {
        $parameter_definitions = [];
        foreach ($this->parameters as $parameter) {
            unset($parameter["name"]);
            $parameter_definitions[] = $parameter;
        }

        $requiredParameters = array_filter($this->parameters, function (
            $parameter
        ) {
            return $parameter["isRequired"];
        });

        return [
            "name" => $this->name,
            "description" => $this->description,
            "parameters" => [
                "type" => "object",
                "properties" => (object) $this->parameters,
                "required" => array_keys($requiredParameters),
            ],
        ];
    }
}
