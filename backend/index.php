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
  new FunctionDefinition("bakeCake", "Bakes a cake", [])
]);

$conversation = new Conversation();
$conversation->addMessage(new UserMessage("hello! bake a cake for me!"));

$client->performConversation($conversation);

print_r($conversation->toArray());
