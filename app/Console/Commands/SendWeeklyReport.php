<?php

	namespace App\Console\Commands;

	use App\Models\Account;
	use App\Models\Contact;
	use App\Models\Users\User;
	use Illuminate\Console\Command;
	use Illuminate\Support\Facades\Mail;
	use App\Mail\WeeklyReportMail;
	use Spatie\Permission\Models\Role;
	use Log;

	class SendWeeklyReport extends Command {
		/**
		 * The name and signature of the console command.
		 *
		 * @var string
		 */
		protected $signature = 'app:send-weekly-report';

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
			// 1. Μαζεύουμε τα στατιστικά της τελευταίας εβδομάδας
			$stats = [
				'new_contacts'   => Contact::query()->where('created_at', '>=', now()->subDays(7))->count(),
				'new_accounts'   => Account::query()->where('created_at', '>=', now()->subDays(7))->count(),
				'total_contacts' => Contact::count(),
				'total_accounts' => Account::count(),
			];

			// 2. Βρίσκουμε τους χρήστες με ρόλο Admin ή Super-Admin
			$adminRole = Role::where('name', 'admin')->first();
			$superAdminRole = Role::where('name', 'super-admin')->first();

			$users = User::query()
				->whereHas('profile', function ($query) {
					$query->where('notify_on_report', true);
				})
				->where(function ($query) use ($adminRole, $superAdminRole) {
					if ($adminRole) {
						$query->orWhereHas('roles', function ($q) use ($adminRole) {
							$q->where('role_id', $adminRole->id);
						});
					}
					if ($superAdminRole) {
						$query->orWhereHas('roles', function ($q) use ($superAdminRole) {
							$q->where('role_id', $superAdminRole->id);
						});
					}
				})
				->get();

			if ($users->isEmpty()) {
				Log::info('Weekly Report: No eligible users found (Admin/Super-Admin with notify_on_report enabled).');
				$this->info('No eligible users found.');
				return;
			}

			Log::info("Weekly Report: Sending to {$users->count()} users.");

			foreach ($users as $user) {
				Mail::to($user->email)->send(new WeeklyReportMail($stats, $user));
				$this->info("Sent weekly report to: {$user->email}");
			}

			$this->info('Weekly report emails sent successfully.');
		}
	}
