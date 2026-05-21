<?php

	namespace App\Http\Controllers\Profile;

	use App\Http\Controllers\Controller;
	use App\Http\Requests\Profile\ProfileUpdateRequest;
	use DB;
	use Illuminate\Support\Facades\Auth;
	use Storage;

	class ProfileController extends Controller {
		/**
		 * Προβολή του προφίλ του συνδεδεμένου χρήστη.
		 */
		public function show() {
			// Παίρνουμε τον τρέχοντα χρήστη.
			// Στο CRM, το $user έχει ήδη τα στοιχεία από το session.

			return view('profile.show', ['user' => Auth::user()]);
		}

		/**
		 * Εμφάνιση της φόρμας επεξεργασίας.
		 */
		public function edit() {
			$user = Auth::user();

			return view('profile.edit', compact('user'));
		}

		/**
		 * Ενημέρωση των στοιχείων στο database.
		 *
		 * @throws \Throwable
		 */
		public function update(ProfileUpdateRequest $request) {
			$user = Auth::user();
			$data = $request->validated();

			DB::transaction(function () use ($user, $data, $request) {
				// 1. Ενημέρωση στον πίνακα 'users'
				$user->update([
					'email' => $data['email']
				]);

				// 2. Διαχείριση Avatar (ανέβασμα αρχείου)
				if ($request->hasFile('avatar')) {
					// Διαγραφή παλιού avatar αν υπάρχει (προαιρετικά)
					if ($user->profile->avatar && Storage::disk('public')->exists($user->profile->avatar)) {
						Storage::disk('public')->delete($user->profile->avatar);
					}

					$data['avatar'] = $request->file('avatar')->store('avatars', 'public');
				}

				// 3. Ενημέρωση στον πίνακα 'user_profiles'
				// Χρησιμοποιούμε updateOrCreate για ασφάλεια
				$user->profile()->update([
					'first_name' => $data['first_name'],
					'last_name'  => $data['last_name'],
					'phone'      => $data['phone'],
					'position'   => $data['position'],
					'bio'        => $data['bio'],
					'avatar'     => $data['avatar'] ?? $user->profile->avatar,
				]);
			});

			return redirect()->route('profile.show')->with('success', 'Το προφίλ σας ενημερώθηκε επιτυχώς!');
		}
	}
