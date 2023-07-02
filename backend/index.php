<?php
declare(strict_types=1);

require "vendor/autoload.php";
require_once "conversation.php";
require_once "client_manager.php";

use GuzzleHttp\Client;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$openai_api_key = $_ENV["OPENAI_API_KEY"];

$client = new OpenAIClientManager(
    $openai_api_key
);

$conversation = new Conversation();
$conversation->addMessage(new UserMessage("whats the weather today in bermuda"));

$client->performConversation($conversation
,	    [
 new FunctionDefinition(
    "get_current_weather",
    "Get the current weather in a given location",
    [
        new ParameterDefinition(
            "location",
            "string",
            "The city and state, e.g. San Francisco, CA"
        ),
        new ParameterDefinition(
            "unit",
            "string",
            "The unit of measurement for temperature, either 'celsius' or 'fahrenheit'"
        )
    ]
)    ],
    "gpt-4-32k"

);

print_r($conversation->toArray());
