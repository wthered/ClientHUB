<?php

	namespace App\Http\Requests\Teams;

	use App\Models\Users\User;
	use Illuminate\Foundation\Http\FormRequest;
	use Illuminate\Validation\Rule;

	class RemoveUserRequest extends FormRequest {
		/**
		 * Ελέγχουμε αν ο χρήστης έχει δικαίωμα να ενημερώσει τη συγκεκριμένη ομάδα.
		 */
		public function authorize(): bool {
			// Το $this->user() επιστρέφει null αν δεν είναι συνδεδεμένος,
			// οπότε το check καλύπτεται από το null-safe navigation ή το policy.
			return (bool) $this->user()?->can('update', $this->route('team'));
		}

		/**
		 * Κανόνες επικύρωσης.
		 */
		public function rules(): array {
			return [
				'user_id' => [
					'required',
					Rule::exists('team_user', 'user_id')
						->where(fn($q) => $q->where('team_id', $this->route('team')?->id)),
				],
			];
		}

		/**
		 * Προσαρμοσμένα μηνύματα σφάλματος.
		 */
		public function messages(): array {
			return [
				'user_id.exists' => 'The selected user does not belong to this team.',
			];
		}

		/**
		 * Προετοιμασία των δεδομένων για validation.
		 * Τραβάμε το ID από το Route Parameter {user}.
		 */
		protected function prepareForValidation(): void {
			$user = $this->route('user');

			$this->merge([
				'user_id' => is_object($user) ? $user->id : $user,
			]);
		}

		/**
		 * Αυτή η μέθοδος εκτελείται ΜΟΝΟ αν το validation πετύχει.
		 */
		protected function passedValidation(): void {
			// Μετατρέπουμε το user_id στο αντίστοιχο User Model
			$this->merge([
				'user_model' => User::query()->find($this->input('user_id')),
			]);
		}
	}
