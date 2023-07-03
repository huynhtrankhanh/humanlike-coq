<?php
declare(strict_types=1);

require "vendor/autoload.php";
require_once "conversation.php";
require_once "client_manager.php";

use GuzzleHttp\Client;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$openai_api_key = $_ENV["OPENAI_API_KEY"];

$client = new OpenAIClientManager($openai_api_key);

$conversation = new Conversation();
$conversation->addMessage(
    new UserMessage("bake me a cake and display a congratulatory message")
);

$client->performConversation(
    $conversation,
    [
        new FunctionDefinition(
            "displayCongratulatoryDialog",
            "Display a congratulatory message. If none is supplied, just display CONGRATULATIONS",
            [new ParameterDefinition("message", "string", "the message", false)]
        ),
        new FunctionDefinition("bakeCake", "Bake a cake", []),
    ],
    "gpt-4-32k"
);

print_r($conversation->toArray());
