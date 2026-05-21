<?php

	namespace App\Console\Commands;

	use App\Models\Opportunities\Opportunity;
	use App\Notifications\StagnantDealAlert;
	use Illuminate\Console\Command;
	use Notification;

	class CheckStagnantDeals extends Command {
		/**
		 * The name and signature of the console command.
		 *
		 * @var string
		 */
		protected $signature = 'app:check-stagnant-deals';

		/**
		 * The console command description.
		 *
		 * @var string
		 */
		protected $description = 'Command description';

		/**
		 * Execute the console command.
		 */
		public function handle(): void {
			$stagnantDeals = Opportunity::query()
				->where('amount', '>', 5000)
				->whereDoesntHave('activities', function ($query) {
					$query->where('created_at', '>=', now()->subDays(5));
				})
				->with('owner') // Ο υπεύθυνος πωλητής
				->get();

			foreach ($stagnantDeals as $deal) {
				// Στέλνουμε ειδοποίηση στον πωλητή ή στον Manager
				// Ελέγχουμε αν ο χρήστης θέλει την ειδοποίηση
				if ($deal->user->settings->stagnant_report_enabled) {
					Notification::send($deal->owner, new StagnantDealAlert($deal));
				}
			}
		}
	}
