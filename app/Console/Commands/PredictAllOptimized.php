<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\PrediksiController;

class PredictAllOptimized extends Command
{
    protected $signature = 'predict:all-optimized';
    protected $description = 'Run optimized batch prediction for entire Naive Bayes dataset';

    public function handle()
    {
        $this->info('Starting batch prediction (optimized)...');
        $controller = app()->make(PrediksiController::class);
        $response = app()->call([$controller, 'predictAllOptimized']);

        if (is_object($response) && method_exists($response, 'getContent')) {
            $this->line($response->getContent());
        } else {
            $this->line(json_encode($response));
        }

        $this->info('Batch prediction finished.');
        return 0;
    }
}
