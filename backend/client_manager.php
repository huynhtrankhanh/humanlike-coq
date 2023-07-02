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

class Parameter
{
    private string $type; // type of the parameter
    private string $description; // description of the parameter

    public function __construct(string $type, string $description)
    {
        $this->type = $type;
        $this->description = $description;
    }

    public function getParameter(): array
    {
        return [
            "type" => $this->type,
            "description" => $this->description,
        ];
    }
}

class FunctionDefinition
{
    private string $name; // Name of the function
    private string $description; // Description of the function
    private array $parameters; // Array of parameters required by function

    public function __construct(
        string $name,
        string $description,
        array $parameters = []
    ) {
        $this->name = $name;
        $this->description = $description;

        $this->parameters = [];
        foreach ($parameters as $parameter) {
            if ($parameter instanceof Parameter) {
                $this->parameters[] = $parameter->getParameter();
            }
        }
    }

    public function getDefinition(): array
    {
        return [
            "name" => $this->name,
            "description" => $this->description,
            // "parameters" => $this->parameters,
        ];
    }
}
