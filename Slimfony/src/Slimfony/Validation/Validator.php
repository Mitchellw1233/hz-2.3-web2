<?php

namespace Slimfony\Validation;

use Slimfony\Validation\Exception\ValidationException;

class Validator
{
    /**
     * @param array<string, mixed> $data
     * @param array<string, Constraint> $schema
     *
     * @throws ValidationException
     *
     * @return array<string, mixed> data returned and transformed if needed
     */
    public static function validate(array $data, array $schema): array
    {
        $errors = [];
        foreach ($schema as $key => $constraint) {
            $errors = [...$errors, ...self::validateRecursive($key, $data, $constraint, $key)];
        }

        if (!empty($errors)) {
            throw new ValidationException(implode('<br>', $errors));
        }

        return $data;
    }

    protected static function validateRecursive($key, array &$data, Constraint $constraint, string $identifier): array
    {
        $errors = [];

        // If not set and nullable, stop validating
        if ($constraint->nullable && !isset($data[$key])) {
            return $errors;
        }

        // If not set and not nullable, error
        if (!$constraint->nullable && !isset($data[$key])) {
            $errors[] = sprintf('%s cannot be null', $identifier);
            return $errors;
        }

        // If empty and cannot be empty, error
        if (!$constraint->empty && empty($data[$key])) {
            $errors[] = sprintf('%s cannot be empty', $identifier);
            return $errors;
        }

        // Validate type
        if (!is_array($data[$key]) && !self::validateType($constraint->type, $data[$key])) {
            $errors[] = sprintf('%s is not a type of %s', $identifier, $constraint->type);
        }

        // Validate recursively
        if (is_array($data[$key])) {
            foreach ($data[$key] as $k => $value) {
                $errors = [...$errors, ...self::validateRecursive($k, $data[$key], $constraint,
                    sprintf('%s.%s', $identifier, $k))];
            }
            return $errors;
        }

        // Transform to right type
        self::transformType($constraint->type, $data[$key]);

        return $errors;
    }

    protected static function validateType(string $type, mixed &$value): bool
    {
        if (!is_scalar($value)) {
            return false;
        }

        return match ($type) {
            'string' => is_string($value),
            'integer' => strval((integer) $value) === strval($value),
            'float' => strval((float) $value) === strval($value),
            'boolean' => strval((bool) $value) === strval($value),
            default => throw new \InvalidArgumentException($type . ' is not a valid type'),
        };
    }

    protected static function transformType(string $type, mixed $value): mixed
    {
        return match ($type) {
            'string' => $value,
            'integer' => (integer) $value,
            'float' => (float) $value,
            'boolean' => (boolean) $value,
            default => throw new \InvalidArgumentException($type . ' cannot be transformed'),
        };
    }
}