<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\PrediksiController;
use App\Models\NaiveBayesDataset;

class PredictFirstDataset extends Command
{
    protected $signature = 'predict:first-dataset';
    protected $description = 'Predict first Naive Bayes dataset row via PrediksiController::predictDatasetItem';

    public function handle()
    {
        $this->info('Finding first dataset row...');
        $row = NaiveBayesDataset::first();
        if (!$row) {
            $this->error('No dataset rows found. Run generate:naive-dataset first.');
            return 1;
        }
        $this->info('Predicting id_dataset=' . $row->id_dataset);
        $controller = app()->make(PrediksiController::class);
        $response = app()->call([$controller, 'predictDatasetItem'], ['id' => $row->id_dataset]);
        if (is_object($response) && method_exists($response, 'getContent')) {
            $this->line($response->getContent());
        } else {
            $this->line(json_encode($response));
        }
        return 0;
    }
}
