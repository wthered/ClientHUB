<?php

	namespace App\Http\Requests\Profile;

	use Auth;
	use Illuminate\Foundation\Http\FormRequest;
	use Illuminate\Support\Facades\Hash;
	use Illuminate\Validation\Rules\Password;

	class ProfileSecurityUpdateRequest extends FormRequest {
		/**
		 * Επιτρέπουμε την πρόσβαση μόνο σε συνδεδεμένους χρήστες.
		 */
		public function authorize(): bool {
			return Auth::check();
		}

		/**
		 * Οι κανόνες validation για την αλλαγή κωδικού.
		 */
		public function rules(): array {
			return [
				'current_password' => [
					'required',
					'string',
					function ($attribute, $value, $fail) {
						if (!Hash::check($value, $this->user()->password)) {
							$fail('Ο τρέχων κωδικός πρόσβασης είναι λανθασμένος.');
						}
					},
				],
				'password'         => [
					'required',
					'string',
					'confirmed',
					Password::min(8)
						->max(12)
						->letters()
						->mixedCase()
						->numbers()
						->uncompromised(),
				],
			];
		}

		/**
		 * Custom μηνύματα σφάλματος στα Ελληνικά.
		 */
		public function messages(): array {
			return [
				'current_password.required' => 'Πρέπει να εισάγετε τον τρέχοντα κωδικό σας.',
				'password.required'         => 'Ο νέος κωδικός είναι υποχρεωτικός.',
				'password.confirmed'        => 'Η επιβεβαίωση κωδικού δεν ταιριάζει.',
				'password.min'              => 'Ο νέος κωδικός πρέπει να είναι τουλάχιστον :min χαρακτήρες.',
			];
		}

		/**
		 * Καθαρά ονόματα πεδίων για τα μηνύματα.
		 */
		public function attributes(): array {
			return [
				'current_password' => 'Τρέχων Κωδικός',
				'password'         => 'Νέος Κωδικός',
			];
		}
	}
