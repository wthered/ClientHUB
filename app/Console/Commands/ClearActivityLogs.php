<?php

	namespace App\Console\Commands;

	use App\Models\ActivityLog;
	use Carbon\Carbon;
	use Illuminate\Console\Command;

	class ClearActivityLogs extends Command {
		protected $signature   = 'logs:clear {--months=6 : Ο αριθμός των μηνών που θα διατηρηθούν}';
		protected $description = 'Διαγραφή logs παλαιότερα από τον ορισμένο αριθμό μηνών';

		public function handle(): void {
			$months = $this->option('months');
			$date   = Carbon::now()->subMonths($months);

			$count = ActivityLog::where('created_at', '<', $date)->delete();

			$this->info("Διαγράφηκαν ".$count." εγγραφές παλαιότερες από {$date->format('d-m-Y')}.");
		}
	}
