<?php
require_once __DIR__ . '/../models/ExampleModel.php';

$model = new ExampleModel();
$users = [];
try {
    $users = $model->getAllUsers();
} catch (Exception $e) {
    $error = $e->getMessage();
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Home</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <h1>Home</h1>
    <?php if (isset($error)): ?>
        <p>Error: <?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <ul>
    <?php foreach ($users as $u): ?>
        <li><?= htmlspecialchars($u['name'] ?? $u['email'] ?? '–') ?></li>
    <?php endforeach; ?>
    </ul>

    <script src="/assets/js/app.js"></script>
</body>
</html>
