<?php

	namespace App\Listeners\Authentication;

	use App\Models\Activities\ActivityLog;
	use Carbon\Carbon;
	use Illuminate\Auth\Events\Failed;
	use Illuminate\Auth\Events\Login;
	use Illuminate\Auth\Events\Logout;

	class UserAuthHistoryListener {
		public function handle(object $event): void {
			// ΕΠΙΤΥΧΗΜΕΝΟ LOGIN
			if ($event instanceof Login) {
				$event->user->update([
					'last_login_at'         => Carbon::now(),
					'last_login_ip'         => request()->ip(),
					'failed_login_attempts' => 0,
				]);

				ActivityLog::log($event->user, 'login', 'Επιτυχής σύνδεση στο σύστημα.');
			}

			// ΑΠΟΤΥΧΗΜΕΝΗ ΠΡΟΣΠΑΘΕΙΑ
			if ($event instanceof Failed) {
				if ($event->user) {
					$event->user->increment('failed_login_attempts');

					if ($event->user->failed_login_attempts >= 5) {
						$event->user->update([
							'is_locked'   => true,
							'lock_reason' => 'Πολλαπλές αποτυχημένες προσπάθειες σύνδεσης.'
						]);
						ActivityLog::log($event->user, 'account_locked', 'Ο λογαριασμός κλειδώθηκε αυτόματα.');
					}
				}
			}

			// LOGOUT
			if ($event instanceof Logout && $event->user) {
				ActivityLog::log($event->user, 'logout', 'Αποσύνδεση από το σύστημα.');
			}
		}
	}
