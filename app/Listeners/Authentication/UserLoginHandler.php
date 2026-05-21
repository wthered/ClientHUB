<?php

	namespace App\Listeners\Authentication;

	use App\Models\Activities\ActivityLog;
	use Carbon\Carbon;
	use Illuminate\Auth\Events\Failed;
	use Illuminate\Auth\Events\Login;
	use Illuminate\Auth\Events\Logout;

	class UserLoginHandler {
		/**
		 * Χειρίζεται όλα τα Auth Events
		 */
		public function handle(object $event): void {
			if ($event instanceof Login) {
				// Reset μετά από επιτυχία
				$event->user->update([
					'last_login_at'         => Carbon::now(),
					'last_login_ip'         => request()->ip(),
					'failed_login_attempts' => 0,
				]);

				// Καταγραφή στο ActivityLog που φτιάξαμε!
				ActivityLog::log($event->user, 'login', 'User logged in successfully');
			}

			if ($event instanceof Failed) {
				if ($event->user) {
					$event->user->increment('failed_login_attempts');

					if ($event->user->failed_login_attempts >= 5) {
						$event->user->update(['is_locked' => true]);
					}
				}
			}

			if ($event instanceof Logout) {
				ActivityLog::log($event->user, 'logout', 'User logged out');
			}
		}
	}
