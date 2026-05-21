<?php

	namespace App\Http\Controllers\Profile;

	use App\Http\Controllers\Controller;
	use App\Http\Requests\Profile\PreferencesUpdateRequest;
	use App\Http\Requests\Profile\ProfileSecurityUpdateRequest;
	use App\Models\Users\User;
	use Auth;
	use Hash;
	use Illuminate\Http\Request;

	class SettingsController extends Controller {
		public function index() {
			return view('profile.settings', [
				'user'    => Auth::user(),
				'profile' => Auth::user()->profile()->first(),
			]);
		}

		public function updateSecurity(ProfileSecurityUpdateRequest $request) {
			$user = Auth::user();

			// Ενημέρωση κωδικού
			$user->update([
				'password' => $request->validated('password'),
			]);

			return back()->with('success', 'Ο κωδικός πρόσβασης ενημερώθηκε!');
		}

		public function destroy() {
			$user = Auth::user();

			// Εδώ συνήθως κάνουμε soft delete ή απενεργοποίηση
			$user->delete();

			Auth::logout();
			return redirect('/')->with('info', 'Ο λογαριασμός σας διαγράφηκε.');
		}

		public function updatePreferences(PreferencesUpdateRequest $request) {
			$profile = $request->user()->profile()->first();

			$profile->update($request->validated());

			return response()->json([
				'status'    => 'success',
				'message'   => 'Η προτίμηση ενημερώθηκε επιτυχώς!',
				'new_state' => $profile->fresh()->only([
					'notify_on_sales',
					'notify_on_report',
				]),
			]);
		}

		public function update(Request $request) {
			$request->user()->settings()->update([
				'stagnant_report_enabled' => $request->has('stagnant_report_enabled'),
				'daily_pulse_enabled'    => $request->has('daily_pulse_enabled'),
				'notify_on_sales'        => $request->has('notify_on_sales'),
				'language'               => $request->language,
			]);

			return back()->with('success', 'Οι ρυθμίσεις ενημερώθηκαν!');
		}
	}
