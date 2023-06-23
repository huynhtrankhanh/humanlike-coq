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

function JQN($x) {
    return $x * $x + 3;
}

$data = [
    'model' => 'gpt-3.5-turbo-0613',
    'messages' => [
        [
            'role' => 'user',
            'content' => 'What is the result of JQN(35)?'
        ]
    ],
    'functions' => [
        [
            'name' => 'JQN',
            'description' => 'Calculate the result of JQN function',
            'parameters' => [
                'type' => 'object',
                'properties' => [
                    'x' => [
                        'type' => 'integer',
                        'description' => 'The input value for the JQN function'
                    ]
                ],
                'required' => ['x']
            ]
        ]
    ],
    'temperature' => 0.7
];

$response = $client->post('/v1/chat/completions', [
    'json' => $data
]);

$result = json_decode($response->getBody(), true);

if (isset($result['choices'][0]['message']['function_call'])) {
    $function_call = $result['choices'][0]['message']['function_call'];
    if ($function_call['name'] == 'JQN') {
        $arguments = json_decode($function_call['arguments'], true);
        $jqn_result = JQN($arguments['x']);
        $result['choices'][0]['message']['content'] = "The result of JQN({$arguments['x']}) is {$jqn_result}.";
    }
}

print_r($result); // Or use echo json_encode($result, JSON_PRETTY_PRINT);
?>
