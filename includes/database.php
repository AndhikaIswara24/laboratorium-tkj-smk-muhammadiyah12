<?php
// Basic database connection loader. Reads .env at project root and returns PDO via getPDO().

function load_env($path)
{
    if (!file_exists($path)) {
        return [];
    }
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $data = [];
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '#') === 0) {
            continue;
        }
        if (strpos($line, '=') === false) {
            continue;
        }
        list($key, $value) = explode('=', $line, 2);
        $data[trim($key)] = trim($value, " \t\n\r\0\x0B\"'");
    }
    return $data;
}

$env = load_env(__DIR__ . '/../.env');

$host = $env['DB_HOST'] ?? '127.0.0.1';
$db   = $env['DB_NAME'] ?? 'database';
$user = $env['DB_USER'] ?? 'root';
$pass = $env['DB_PASS'] ?? '';
$charset = $env['DB_CHARSET'] ?? 'utf8mb4';

$dsn = "mysql:host={$host};dbname={$db};charset={$charset}";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    throw new RuntimeException('Database connection failed: ' . $e->getMessage());
}

function getPDO()
{
    global $pdo;
    return $pdo;
}
