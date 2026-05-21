<?php

	use Illuminate\Foundation\Inspiring;
	use Illuminate\Support\Facades\Artisan;
	use Illuminate\Support\Facades\Schedule;

	Artisan::command('inspire', function () {
		$this->comment(Inspiring::quote());
	})->purpose('Display an inspiring quote');

	// Κάθε Δευτέρα στις 08:00 το πρωί
	Schedule::command('app:send-weekly-report')->weeklyOn(1, '08:00')->timezone(config('app.timezone', 'Europe/Athens'));
	// Schedule::command('app:send-weekly-report')->dailyAt('08:00')->timezone(config('app.timezone', 'Europe/Athens'));