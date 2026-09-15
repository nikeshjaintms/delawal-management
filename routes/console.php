<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('properties:sync-status', function () {
    $this->info('Synchronizing property statuses from bookings, sales, and rentals...');
    \App\Models\Property::syncAllStatuses();
    $this->info('✓ All property statuses synchronized successfully!');
})->purpose('Synchronize property statuses based on active bookings, sales, and rentals');

