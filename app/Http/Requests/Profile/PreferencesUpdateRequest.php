<?php

	namespace App\Http\Requests\Profile;

	use Illuminate\Foundation\Http\FormRequest;
	use Illuminate\Support\Collection;
	use Illuminate\Support\Facades\Auth;

	class PreferencesUpdateRequest extends FormRequest {
		public function authorize(): bool {
			return Auth::check();
		}

		public function rules(): array {
			return [
				// Χρησιμοποιούμε 'sometimes' γιατί το AJAX στέλνει μόνο το ένα από τα δύο
				'notif_sales'  => 'sometimes|boolean',
				'notif_report' => 'sometimes|boolean',
			];
		}

		/**
		 * Επεξεργασία των δεδομένων ΜΕΤΑ το επιτυχημένο validation.
		 */
		protected function passedValidation(): void {
			// Δημιουργούμε το mapped array
			$mapped = [
				'notif_sales'  => $this->boolean('notif_sales'),
				'notif_report' => $this->boolean('notif_report'),
			];

			// Αντί για replace, κάνουμε merge και καθαρίζουμε τα παλιά
			$this->merge($mapped);
		}

		public function validated($key = null, $default = null): array {
			$validated = parent::validated($key, $default);
			return [
				'notify_on_sales' => $validated['notif_sales'],
				'notify_on_report' => $validated['notif_report'],
			];
		}
	}
