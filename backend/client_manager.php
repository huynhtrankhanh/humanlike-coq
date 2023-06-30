<?php

class OpenAIClientManager
{
    private $client;

    public function __construct($apiKey)
    {
        $this->client = OpenAI::client($apiKey);
    }

    public function performConversation(Conversation $conversation, string $model = 'gpt-3.5-turbo'): void
    {
        $messages = $conversation->getMessages();

        // Make call to OpenAI chat service
        $response = $this->client->chat()->create([
            'model' => $model,
            'messages' => $messages,
        ]);

        // Select the first response choice
        $result = $response->choices[0];

        // Create a new AssistantMessage
        $assistantMessage = new AssistantMessage(
            $result->message->content, 
            isset($result->message->functionCall) ?
                new FunctionCall(
                    $result->message->functionCall->name,
                    $result->message->functionCall->arguments
                ) : null
        );

        // Add final message to the conversation
        $conversation->addMessage($assistantMessage);
    }
}
