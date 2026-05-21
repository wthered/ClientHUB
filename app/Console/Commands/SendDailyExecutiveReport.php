<?php

	namespace App\Console\Commands;

	use App\Mail\DailyExecutiveReport;
	use App\Models\Users\User;
	use Illuminate\Console\Command;
	use Illuminate\Support\Facades\Mail;

	class SendDailyExecutiveReport extends Command {
		/**
		 * The name and signature of the console command.
		 *
		 * @var string
		 */
		protected $signature = 'app:send-daily-report';

		/**
		 * The console command description.
		 *
		 * @var string
		 */
		protected $description = 'Στέλνει το ημερήσιο report στους διαχειριστές';

		/**
		 * Execute the console command.
		 */
		public function handle(): void {
			// 1. Βρίσκουμε τους παραλήπτες βάσει των settings τους
			$usersToNotify = User::query()->whereHas('settings', function($query) {
				$query->where('daily_pulse_enabled', true);
			})->get();

			// 2. Αν δεν βρεθεί κανένας, σταματάμε εδώ
			if ($usersToNotify->isEmpty()) {
				$this->info('Κανένας χρήστης δεν έχει ενεργοποιημένο το Daily Pulse.');
				return;
			}

			// 3. Στέλνουμε το mail στον καθένα ξεχωριστά
			foreach ($usersToNotify as $user) {
				// Χρησιμοποιούμε το email του κάθε χρήστη από τη βάση
				Mail::to($user->email)->send(new DailyExecutiveReport());
			}

			$this->info('Το report στάλθηκε σε ' . $usersToNotify->count() . ' χρήστες!');
		}
	}
