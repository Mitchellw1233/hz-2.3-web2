<?php

namespace Slimfony\ORM\Query;

abstract class AbstractQueryBuilder
{
    /**
     * @var string|null
     */
    protected ?string $sql;

    /**
     * @var array<int, string>
     */
    protected array $statements;

    /**
     * @var array<string, scalar>
     */
    protected array $parameters;

    public function __construct()
    {
        $this->sql = null;
        $this->statements = [];
        $this->parameters = [];
    }

    public function build(): string
    {
        $this->sql = implode(' ', $this->statements) . ';';

        return $this->sql;
    }

    /**
     * @param array<string, scalar> $parameters
     *
     * @return static
     */
    public function setParameters(array $parameters): static
    {
        foreach ($parameters as $name => $value) {
            if (!is_scalar($value)) {
                throw new \InvalidArgumentException(sprintf('Parameter `%s` is non-scalar', $name));
            }
        }
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * @return array<string, scalar>
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

//    protected function parseParameters(string $sql): string
//    {
//        return preg_replace_callback('#(?<=:)[^\s:]+#', function ($matches) {
//            return $this->parameters[$matches[0]] ?? throw new \InvalidArgumentException(sprintf(
//                ':`%s` is not set in parameters', $matches[0]));
//        }, $sql);
//    }
}
