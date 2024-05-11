<?php

namespace Library\Actions\Exceptions;

use RuntimeException;

class MethodNotFoundException extends RuntimeException
{
    /**
     * @param string $message
     * @param string|object $classname
     * @param string $methodName
     * @param ?array $arguments
     */
    public function __construct(
        string                         $message,
        private readonly string|object $classname,
        private readonly string        $methodName,
        private readonly ?array        $arguments = null)
    {
        parent::__construct($message);
    }

    public function getClassname(): object|string
    {
        return $this->classname;
    }

    public function getMethodName(): string
    {
        return $this->methodName;
    }

    public function getArguments(): ?array
    {
        return $this->arguments;
    }
}
