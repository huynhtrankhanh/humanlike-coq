<?php

declare(strict_types=1);

abstract class Message {
    private string $content;

    public function __construct(string $content) {
        $this->content = $content;
    }

    public function getContent(): string {
        return $this->content;
    }
}

class UserMessage extends Message {}

class AssistantMessage extends Message {
    private ?FunctionCall $functionCall;

    public function __construct(string $content, ?FunctionCall $functionCall = null) {
        parent::__construct($content);
        $this->functionCall = $functionCall;
    }

    public function getFunctionCall(): ?FunctionCall {
        return $this->functionCall;
    }
}

class FunctionMessage extends Message {
    private string $name;

    public function __construct(string $name, string $content) {
        parent::__construct($content);
        $this->name = $name;
    }

    public function getName(): string {
        return $this->name;
    }
}

class Conversation {
    private array $messages;

    public function __construct() {
        $this->messages = [];
    }

    public function addMessage(Message $message): void {
        $this->messages[] = $message;
    }

    public function getMessages(): array {
        return $this->messages;
    }
}
