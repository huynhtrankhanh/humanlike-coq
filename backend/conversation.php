<?php
declare(strict_types=1);

abstract class Message {
    abstract function toArray(): array;
}

class UserMessage extends Message {
    private string $content;

    public function __construct(string $content) {
        $this->content = $content;
    }

    public function getContent(): string {
        return $this->content;
    }

    public function toArray(): array {
        return [
            'role' => 'user',
            'content' => $this->getContent()
        ];
    }
}

class AssistantMessage extends Message {
    private ?string $content;
    private ?FunctionCall $functionCall;

    public function __construct(?string $content, ?FunctionCall $functionCall = null) {
        $this->content = $content;
        $this->functionCall = $functionCall;
    }

    public function getContent(): ?string {
        return $this->content;
    }

    public function getFunctionCall(): ?FunctionCall {
        return $this->functionCall;
    }

    public function toArray(): array {
        $arr = [ 'role' => 'assistant', 'content' => $this->getContent() ];
        
        if ($this->getFunctionCall() !== null) {
            $arr['function_call'] = $this->getFunctionCall()->toArray();
        }

        return $arr;
    }
}

class FunctionMessage extends Message {
    private string $name;
    private string $content;

    public function __construct(string $name, string $content) {
        $this->name = $name;
        $this->content = $content;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getContent(): string {
        return $this->content;
    }

    public function toArray(): array {
        return [
            'role' => 'function',
            'name' => $this->getName(),
            'content' => $this->getContent()
        ];
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
        return array_map(function ($message) {
            return $message->toArray();
        }, $this->messages);
    }
}

class FunctionCall {
    private string $name;
    private string $arguments;

    public function __construct(string $name, string $arguments) {
        $this->name = $name;
        $this->arguments = $arguments;
    }

    public function toArray(): array {
        return [
            'name' => $this->name,
            'arguments' => $this->arguments
        ];
    }
}
