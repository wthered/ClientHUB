<?php

	namespace App\Http\Requests\Tasks;

	use Auth;
	use Illuminate\Foundation\Http\FormRequest;

	class TaskFilterRequest extends FormRequest {
		/**
		 * Determine if the user is authorized to make this request.
		 */
		public function authorize(): bool {
			return Auth::check() && $this->user()->hasAnyRole([
				'admin',
				'super-admin'
			]);
		}

		/**
		 * Get the validation rules that apply to the request.
		 */
		public function rules(): array {
			return [
				'status'    => [
					'nullable',
					'string'
				],
				'priority'  => [
					'nullable',
					'string'
				],
				'search'    => [
					'nullable',
					'string',
					'max:100'
				],
				'date_from' => [
					'nullable',
					'date'
				],
				'date_to'   => [
					'nullable',
					'date',
					'after_or_equal:date_from'
				],
			];
		}

		/**
		 * Custom μηνύματα σφάλματος.
		 */
		public function messages(): array {
			return [
				'date_to.after_or_equal' => 'Η ημερομηνία "Έως" δεν μπορεί να είναι προγενέστερη από την ημερομηνία "Από".',
				'search.max'             => 'Η αναζήτηση δεν μπορεί να υπερβαίνει τους 100 χαρακτήρες.',
				'date_from.date'         => 'Η ημερομηνία έναρξης δεν είναι έγκυρη.',
				'date_to.date'           => 'Η ημερομηνία λήξης δεν είναι έγκυρη.',
			];
		}

		/**
		 * Προετοιμασία των δεδομένων πριν το validation.
		 */
		protected function prepareForValidation(): void {
			// Αν το status ή το priority είναι "null" ως string ή κενά, τα κάνουμε null
			$this->merge([
				// Καθαρισμός από τυχόν HTML tags
				'search' => strip_tags($this->input('search')),
			]);
		}

		/**
		 * Ενέργειες αφού περάσει επιτυχώς το validation.
		 */
		protected function passedValidation(): void {
			// Εδώ θα μπορούσαμε π.χ. να καταγράψουμε ότι ο χρήστης έκανε ένα συγκεκριμένο φιλτράρισμα
			// ή να τροποποιήσουμε κάποιο format αν χρειαζόταν.
		}
	}
