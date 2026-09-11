<?php

declare(strict_types=1);

namespace App\Database;

use RuntimeException;

final readonly class DatabaseConfiguration
{
    public function __construct(
        public string $host,
        public int $port,
        public string $database,
        public string $username,
        public string $password,
    ) {}

    public static function fromEnvironment(): self
    {
        $port = filter_var(self::environmentValue('DB_PORT', '3306'), FILTER_VALIDATE_INT);

        if (!is_int($port)) {
            throw new RuntimeException('DB_PORT must be a valid integer.');
        }

        return new self(
            host: self::environmentValue('DB_HOST', 'db'),
            port: $port,
            database: self::environmentValue('DB_DATABASE', 'blog'),
            username: self::environmentValue('DB_USERNAME', 'blog'),
            password: self::environmentValue('DB_PASSWORD', 'secret'),
        );
    }

    private static function environmentValue(string $name, string $default): string
    {
        $value = getenv($name);

        return $value === false ? $default : $value;
    }
}
