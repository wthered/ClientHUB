<?php

	namespace App\Http\Requests\Activities;

	use Illuminate\Contracts\Validation\ValidationRule;
	use Illuminate\Foundation\Http\FormRequest;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Support\Str;

	class ActivityFilterRequest extends FormRequest {
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
		 *
		 * @return array<string, ValidationRule|array|string>
		 */
		public function rules(): array {
			return [
				'search'    => ['nullable', 'string', 'max:100'],
				'user_id'   => ['nullable', 'exists:users,id'],
				'model'     => ['nullable', 'string'],
				'date_from' => ['nullable', 'date', 'before_or_equal:today'],
				'date_to'   => [
					'nullable',
					'date',
					'after_or_equal:date_from',
					'before_or_equal:today'
				],
			];
		}

		/**
		 * Ορίζουμε "ανθρώπινα" ονόματα για τα πεδία.
		 * Αντί για "The date from must be..." θα λέει "Το πεδίο Ημερομηνία Από πρέπει να..."
		 */
		public function attributes(): array {
			return [
				'search'    => 'Αναζήτηση',
				'user_id'   => 'Χρήστης',
				'model'     => 'Οντότητα',
				'date_from' => 'Ημερομηνία Από',
				'date_to'   => 'Ημερομηνία Έως',
			];
		}

		public function messages(): array {
			return [
				'date_to.after_or_equal' => 'Η :attribute δεν μπορεί να είναι προγενέστερη από την Ημερομηνία Από.',
				'date_from.before_or_equal' => 'Η :attribute δεν μπορεί να είναι στο μέλλον.',
				'date_to.before_or_equal'   => 'Η :attribute δεν μπορεί να είναι στο μέλλον.',
			];
		}

		/**
		 * Εδώ μπορούμε να προετοιμάσουμε τα δεδομένα μετά την επιτυχή επικύρωση.
		 */
		protected function passedValidation(): void {
			if ($this->filled('search')) {
				$this->merge([
					'search' => Str::lower(Str::trim($this->validated('search')))
				]);
			}
		}
	}
