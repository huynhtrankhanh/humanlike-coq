<?php

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
}

class ParameterDefinition
{
    private string $name;
    private string $type;
    private string $description;

    public function __construct(string $name, string $type, string $description)
    {
        $this->name = $name;
        $this->type = $type;
        $this->description = $description;
    }

    public function getDefinition(): array
    {
        return [
            "name" => $this->name,
            "type" => $this->type,
            "description" => $this->description,
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
                $this->parameters[$parameter->getDefinition()['name']] = $parameter->getDefinition();
            }
        }
    }

    public function getDefinition(): array
    {
        $parameter_definitions = [];
        foreach ($this->parameters as $parameter) {
            unset($parameter['name']);
            $parameter_definitions[] = $parameter;
        }

        return [
            "name" => $this->name,
            "description" => $this->description,
            "parameters" => [
                "type" => "object",
                "properties" => $this->parameters,
                "required" => array_keys($this->parameters),
            ]
        ];
    }
}
