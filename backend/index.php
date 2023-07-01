<?php
declare(strict_types=1);

require "vendor/autoload.php";
require_once "conversation.php";
require_once "client_manager.php";

use GuzzleHttp\Client;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$openai_api_key = $_ENV["OPENAI_API_KEY"];

$client = OpenAI::client($openai_api_key);

$response = $client->chat()->create([
    "model" => "gpt-3.5-turbo-16k-0613",
    "messages" => [
        ["role" => "user", "content" => 'What\'s the weather like in Boston?'],
    ],
    "functions" => [
        [
            "name" => "get_current_weather",
            "description" => "Get the current weather in a given location",
            "parameters" => [
                "type" => "object",
                "properties" => [
                    "location" => [
                        "type" => "string",
                        "description" =>
                            "The city and state, e.g. San Francisco, CA",
                    ],
                    "unit" => [
                        "type" => "string",
                        "enum" => ["celsius", "fahrenheit"],
                    ],
                ],
                "required" => ["location"],
            ],
        ],
    ],
]);

$response->id; // 'chatcmpl-6pMyfj1HF4QXnfvjtfzvufZSQq6Eq'
$response->object; // 'chat.completion'
$response->created; // 1677701073
$response->model; // 'gpt-3.5-turbo-0613'

foreach ($response->choices as $result) {
    $result->index; // 0
    $result->message->role; // 'assistant'
    $result->message->content; // null
    $result->message->functionCall->name; // 'get_current_weather'
    $result->message->functionCall->arguments; // "{\n  \"location\": \"Boston, MA\"\n}"
    $result->finishReason; // 'function_call'
    print_r($result);
}

$response->usage->promptTokens; // 82,
$response->usage->completionTokens; // 18,
$response->usage->totalTokens; // 100
