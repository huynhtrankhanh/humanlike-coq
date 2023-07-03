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

$functions = [
    new FunctionDefinition(
        "displayCongratulatoryDialog",
        "Display a congratulatory message. If none is supplied, just display 'CONGRATULATIONS'.",
        [new ParameterDefinition("message", "string", "the message", false)]
    ),
    new FunctionDefinition("bakeCake", "Bake a cake.", []),
];

$isLastMessageFunctionCall = true;
while ($isLastMessageFunctionCall) {
    $client->performConversation($conversation, $functions, "gpt_4-32k");

    $lastMessageIndex = array_key_last($conversation->toArray());
    $lastMessage = $conversation->toArray()[$lastMessageIndex];

    if ($lastMessage["role"] === "assistant" && isset($lastMessage["function_call"])) {
        $lastMessageFunctionCallName = $lastMessage["function_call"]["name"];
        if ($lastMessageFunctionCallName === "displayCongratulatoryDialog") {
            echo "Triggered congratulations.\n";
            $congratulationsMessage = new FunctionMessage("displayCongratulatoryDialog", 
                                                          "Congratulatory message displayed successfully.");
            $conversation->addMessage($congratulationsMessage);
        } elseif ($lastMessageFunctionCallName === "bakeCake") {
            echo "Triggered bake cake.\n";
            $bakeCakeMessage = new FunctionMessage("bakeCake", 
                                                   "Cake has been baked successfully.");
            $conversation->addMessage($bakeCakeMessage);
        }
    } else {
        $isLastMessageFunctionCall = false;
    }
}

print_r($conversation->toArray());
