<?php

	namespace App\Http\Requests\Teams;

	use Illuminate\Contracts\Validation\ValidationRule;
	use Illuminate\Foundation\Http\FormRequest;
	use Illuminate\Support\Str;

	class TeamStoreRequest extends FormRequest {
		/**
		 * Determine if the user is authorized to make this request.
		 */
		public function authorize(): bool {
			// Ελέγχουμε αν ο συνδεδεμένος χρήστης έχει τους κατάλληλους ρόλους
			return $this->user() && $this->user()->hasAnyRole(['admin', 'super-admin']);
		}

		/**
		 * Προετοιμασία των δεδομένων πριν το validation.
		 * Καθαρίζουμε το όνομα και διασφαλίζουμε το boolean status.
		 */
		protected function prepareForValidation(): void {
			$this->merge([
				'name' => Str::title(trim($this->name)),
				// Αν το checkbox 'is_active' λείπει από το request, το ορίζουμε ως false
				'is_active' => $this->has('is_active'),
			]);
		}

		/**
		 * Get the validation rules that apply to the request.
		 *
		 * @return array<string, ValidationRule|array|string>
		 */
		public function rules(): array {
			return [
				'name' => [
					'required',
					'string',
					'max:255',
					'unique:teams,name',
				],
				'company_id' => [
					'nullable',
					'integer',
					'exists:companies,id',
				],
				'manager_id' => [
					'nullable',
					'integer',
					'exists:users,id',
				],
				'leader_id' => [
					'nullable',
					'integer',
					'exists:users,id',
				],
				'description' => [
					'nullable',
					'string',
					'max:1000',
				],
				'is_active' => [
					'boolean',
				],
			];
		}

		/**
		 * Ενέργειες που εκτελούνται αφού το validation πετύχει.
		 */
		protected function passedValidation(): void {
			// Εδώ μπορείς να προσθέσεις επιπλέον logic, π.χ. logging
			// ή να κάνεις merge κάποιο extra πεδίο που προκύπτει από τα έγκυρα δεδομένα.
		}

		/**
		 * Προσαρμοσμένα μηνύματα σφάλματος.
		 */
		public function messages(): array {
			return [
				'name.required' => 'Το όνομα της ομάδας είναι υποχρεωτικό.',
				'name.unique' => 'Υπάρχει ήδη ομάδα με αυτό το όνομα.',
				'company_id.exists' => 'Η επιλεγμένη εταιρεία δεν είναι έγκυρη.',
				'manager_id.exists' => 'Ο επιλεγμένος Manager δεν βρέθηκε στο σύστημα.',
				'leader_id.exists' => 'Ο επιλεγμένος Leader δεν βρέθηκε στο σύστημα.',
			];
		}

		/**
		 * Προσαρμοσμένα ονόματα attributes για καθαρότερα σφάλματα.
		 */
		public function attributes(): array {
			return [
				'name' => 'Team Name',
				'company_id' => 'Company',
				'manager_id' => 'Manager',
				'leader_id' => 'Leader',
			];
		}
	}