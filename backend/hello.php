<?php

require 'vendor/autoload.php';

use GuzzleHttp\Client;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$openai_api_key = $_ENV['OPENAI_API_KEY'];

$client = new Client([
    'base_uri' => 'https://api.openai.com',
    'headers' => [
        'Content-Type' => 'application/json',
        'Authorization' => 'Bearer ' . $openai_api_key
    ]
]);

$data = [
    'model' => 'gpt-3.5-turbo',
    'messages' => [
        [
            'role' => 'user',
            'content' => 'Say this is a test!'
        ]
    ],
    'temperature' => 0.7
];

$response = $client->post('/v1/chat/completions', [
    'json' => $data
]);

$result = json_decode($response->getBody(), true);

print_r($result); // Or use echo json_encode($result, JSON_PRETTY_PRINT);
?>
