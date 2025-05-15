<?php

use App\Jobs\CalculateDailyNetRevenue;
use App\Jobs\GenerateDailyExpenses;
use App\Models\Gerai;
use Carbon\Carbon;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Jadwalkan job GenerateDailyExpenses setiap hari
Schedule::job(new GenerateDailyExpenses(Carbon::today()->toDateString()))->daily();

// Jadwalkan job CalculateDailyNetRevenue untuk setiap gerai setiap hari
Schedule::call(function () {
    $gerais = Gerai::select('id')->get();
    foreach ($gerais as $gerai) {
        dispatch(new CalculateDailyNetRevenue($gerai->id, Carbon::today()->toDateString()));
    }
})->daily();

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
