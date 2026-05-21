<?php

	namespace App\Http\Middleware\Authentication;

	use Carbon\Carbon;
	use Closure;
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\Auth;

	class UpdateUserStatus {
		public function handle(Request $request, Closure $next) {
			if (Auth::check()) {
				$user = Auth::user();

				// 1. Έλεγχος αν ο λογαριασμός κλειδώθηκε ενδιάμεσα
				if ($user->is_locked) {
					Auth::logout();
					return redirect()->route('login')->withErrors([
						'email' => 'Ο λογαριασμός σας έχει κλειδωθεί. Επικοινωνήστε με τον Admin.'
					]);
				}

				// 2. Ενημέρωση Last Seen (μόνο αν έχει περάσει 1 λεπτό από την τελευταία φορά για performance)
				if ($user->last_active_at < Carbon::now()->subMinute()) {
					$user->update(['last_active_at' => Carbon::now()]);
				}
			}

			return $next($request);
		}
	}
