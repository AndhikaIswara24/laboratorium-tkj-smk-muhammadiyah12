<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$db = $app->make('db');
// Subquery: latest id_prediksi per asset
$sub = $db->table('t_hasil_prediksi')
    ->selectRaw('MAX(id_prediksi) as max_id')
    ->groupBy('id_aset');

$latestIds = $sub->pluck('max_id')->toArray();

$counts = $db->table('t_hasil_prediksi')
    ->whereIn('id_prediksi', $latestIds)
    ->select('hasil_prediksi', $db->raw('COUNT(*) as cnt'))
    ->groupBy('hasil_prediksi')
    ->pluck('cnt', 'hasil_prediksi')
    ->toArray();

$totalAssets = $db->table('t_naive_bayes_dataset')->count();

$result = [
    'total_assets' => $totalAssets,
    'counts' => $counts,
];

echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL;
