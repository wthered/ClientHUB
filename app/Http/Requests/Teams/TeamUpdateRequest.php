<?php

	namespace App\Http\Requests\Teams;

	use Illuminate\Foundation\Http\FormRequest;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Validation\Rule;

	class TeamUpdateRequest extends FormRequest {
		/**
		 * Determine if the user is authorized to make this request.
		 */
		public function authorize(): bool {
			return Auth::check() && Auth::user()->can('update teams');
		}

		/**
		 * Get the validation rules that apply to the request.
		 */
		public function rules(): array {
			// Παίρνουμε το ID της ομάδας από το route για το unique rule
			$team = $this->route('team')->id;
//			dd($team);

			return [
				'name'        => [
					'required',
					'string',
					'max:255',
					Rule::unique('teams', 'name')->ignore($team),
				],
				'description' => 'nullable|string|max:1000',
				'company_id'  => 'nullable|exists:companies,id',
				'manager_id'  => 'nullable|exists:users,id',
				'leader_id'   => 'nullable|exists:users,id',
				'is_active'   => 'boolean',
			];
		}

		/**
		 * Custom μηνύματα σφάλματος στα Ελληνικά (ή Αγγλικά, αναλόγως το project).
		 */
		public function messages(): array {
			return [
				'name.required'     => 'Το όνομα της ομάδας είναι υποχρεωτικό.',
				'name.unique'       => 'Αυτό το όνομα ομάδας χρησιμοποιείται ήδη.',
				'company_id.exists' => 'Η επιλεγμένη εταιρεία δεν είναι έγκυρη.',
				'manager_id.exists' => 'Ο επιλεγμένος Manager δεν βρέθηκε στο σύστημα.',
				'leader_id.exists'  => 'Ο επιλεγμένος Leader δεν βρέθηκε στο σύστημα.',
			];
		}

		/**
		 * Προετοιμασία δεδομένων πριν το validation.
		 */
		protected function prepareForValidation(): void {
			$this->merge([
				// Καθαρισμός κενών από το όνομα και μετατροπή σε τίτλο
				'name'      => strip_tags(trim($this->name)),
				'is_active' => $this->has('is_active') && $this->input('is_active'),
			]);
		}

		/**
		 * Ενέργειες αφού το validation πετύχει.
		 */
		protected function passedValidation(): void {
			// Εδώ θα μπορούσες να προσθέσεις επιπλέον logic,
			// π.χ. αν το is_active είναι false, ίσως θες να στείλεις ένα notification
			if (!$this->input('is_active')) {
				// Logic for deactivating team members if needed
			}
		}
	}
