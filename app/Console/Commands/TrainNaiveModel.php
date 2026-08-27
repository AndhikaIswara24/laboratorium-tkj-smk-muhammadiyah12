<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\PrediksiController;

class TrainNaiveModel extends Command
{
    protected $signature = 'train:naive-model';
    protected $description = 'Trigger training on Flask Naive Bayes service via PrediksiController::trainModel';

    public function handle()
    {
        $this->info('Triggering train on Flask service...');
        $controller = app()->make(PrediksiController::class);
        $response = app()->call([$controller, 'trainModel']);
        $this->info('Response:');
        if (is_object($response) && method_exists($response, 'getContent')) {
            $this->line($response->getContent());
        } else {
            $this->line(json_encode($response));
        }
        return 0;
    }
}
