<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\PrediksiController;

class GenerateNaiveDataset extends Command
{
    protected $signature = 'generate:naive-dataset';
    protected $description = 'Generate Naive Bayes dataset (calls PrediksiController::generateDataset)';

    public function handle()
    {
        $this->info('Generating Naive Bayes dataset...');
        $controller = app()->make(PrediksiController::class);
        $result = app()->call([$controller, 'generateDataset']);
        $this->info('Done.');
        if (is_array($result) || (is_object($result) && method_exists($result, 'toArray'))) {
            $this->line(json_encode($result));
        }
        return 0;
    }
}
