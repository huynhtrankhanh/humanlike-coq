<?php

declare(strict_types=1);

class FunctionCall {
    private string $name;
    private string $arguments;

    public function __construct(string $name, string $arguments) {
        $this->name = $name;
        $this->arguments = $arguments;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getArguments(): string {
        return $this->arguments;
    }

    public function toArray(): array {
        return [
            'name' => $this->name,
            'arguments' => $this->arguments,
        ];
    }
}

class Conversation {
    private array $messages;

    public function __construct() {
        $this->messages = [];
    }

    public function addUserMessage(string $content): void {
        $this->messages[] = [
            'type' => 'user',
            'content' => $content,
        ];
    }

    public function addAssistantMessage(?string $content, ?FunctionCall $functionCall = null): void {
        $message = [
            'type' => 'assistant',
            'content' => $content,
        ];

        if ($functionCall !== null) {
            $message['functionCall'] = $functionCall->toArray();
        }

        $this->messages[] = $message;
    }

    public function addFunctionMessage(string $name, string $content): void {
        $this->messages[] = [
            'type' => 'function',
            'name' => $name,
            'content' => $content,
        ];
    }

    public function getMessages(): array {
        return $this->messages;
    }
}
