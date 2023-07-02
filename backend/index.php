<?php
declare(strict_types=1);

require "vendor/autoload.php";
require_once "conversation.php";
require_once "client_manager.php";

use GuzzleHttp\Client;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$openai_api_key = $_ENV["OPENAI_API_KEY"];

$client = new OpenAIClientManager($openai_api_key, [
    new FunctionDefinition("displayCongratulationsAnimation", "Displays an eye catching congratulations animation to the user", []),
], "gpt-4-32k");

$conversation = new Conversation();
$conversation->addMessage(new UserMessage("display congrats animation please"));

$client->performConversation($conversation);

print_r($conversation->toArray());
