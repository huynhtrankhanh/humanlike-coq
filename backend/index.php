<?php
declare(strict_types=1);

require "vendor/autoload.php";
require_once "conversation.php";
require_once "client_manager.php";
require_once "function_handler.php";

use GuzzleHttp\Client;
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$openai_api_key = $_ENV["OPENAI_API_KEY"];
$client = new OpenAIClientManager($openai_api_key);
$functionHandler = new FunctionHandler();

$conversation = new Conversation();
$conversation->addMessage(
    new UserMessage("bake me a cake and display a congratulatory message")
);
$functions = [
    new FunctionDefinition(
        "displayCongratulatoryDialog",
        "Display a congratulatory message.",
        [new ParameterDefinition("message", "string", "the message", true)]
    ),
    new FunctionDefinition("bakeCake", "Bake a cake.", []),
];

$isLastMessageFunctionCall = true;

while ($isLastMessageFunctionCall) {
    $client->performConversation($conversation, $functions, "gpt-4-32k");
    $lastMessageIndex = array_key_last($conversation->toArray());
    $lastMessage = $conversation->toArray()[$lastMessageIndex];
    if (
        $lastMessage["role"] === "assistant" &&
        isset($lastMessage["function_call"])
    ) {
        $name = $lastMessage["function_call"]["name"];
        $arguments = $lastMessage["function_call"]["arguments"];
        $paramsDefinition = $client->getFunctionDefinition($name, $functions);
        $lastMessageFunctionCall = new FunctionCall($name, $arguments, $paramsDefinition);
        try {
            $responseMessage = $functionHandler->handleFunction(
                $lastMessageFunctionCall
            );
            $completionMessage = new FunctionMessage(
                $lastMessageFunctionCall->name,
                $responseMessage
            );
        } catch (\Exception $e) {
            $completionMessage = new FunctionMessage(
                $name,
                $e->getMessage()
            );
        }   
        $conversation->addMessage($completionMessage);
    } else {
        $isLastMessageFunctionCall = false;
    }
}

print_r($conversation->toArray());
