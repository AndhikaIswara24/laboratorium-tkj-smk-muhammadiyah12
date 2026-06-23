<?php
require_once __DIR__ . '/../includes/database.php';

class ExampleModel
{
    protected $pdo;

    public function __construct()
    {
        $this->pdo = getPDO();
    }

    public function getAllUsers()
    {
        $stmt = $this->pdo->query('SELECT * FROM users');
        return $stmt->fetchAll();
    }
}
