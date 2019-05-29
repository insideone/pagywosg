<?php

namespace App\Validation;

use Opis\JsonSchema\ValidationError;
use Opis\JsonSchema\ValidationResult;

class ErrorFormatter
{
    protected $map = [
        'type' => '{title} should be a {expected}',
        'minLength' => '{title} length must be at least {min}',
        'maxLength' => '{title} length must be at most {max}',
    ];

    public function formatResult(ValidationResult $result)
    {
        $errors = [];
        foreach ($result->getErrors() as $error) {
            $errors[] = $this->format($error);
        }

        return $errors;
    }

    public function format(ValidationError $error): string
    {
        $kw = $error->keyword();
        if (!isset($this->map[$kw])) {
            return 'Unexpected error on keyword: ' . $kw;
        }

        $kw = $this->map[$kw];

        if (is_callable($kw)) {
            return $kw($error, $this);
        }

        $args = $error->keywordArgs() + ['title' => $this->fieldName($error)];

        return $this->replace($kw, $args);
    }

    public function fieldName(ValidationError $error, bool $name = true): ?string
    {
        $schema = $error->schema();

        if (is_object($schema) && isset($schema->title) && is_string($schema->title)) {
            return $schema->title;
        }

        if (!$name) {
            return null;
        }

        $title = $error->dataPointer();
        $title = array_pop($title);

        return (string) $title;
    }

    public function replace(string $str, array $args): string
    {
        return preg_replace_callback('~\{([^}]+)\}~', function ($m) use ($args) {
            return isset($args[$m[1]]) ? $args[$m[1]] : $m[0];
        }, $str);
    }
}
