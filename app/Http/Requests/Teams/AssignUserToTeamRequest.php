<?php

	namespace App\Http\Requests\Teams;

	use App\Enums\TeamRole;
	use Illuminate\Contracts\Validation\ValidationRule;
	use Illuminate\Foundation\Http\FormRequest;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Validation\Rule;

	class AssignUserToTeamRequest extends FormRequest {
		/**
		 * Determine if the user is authorized to make this request.
		 */
		public function authorize(): bool {
			// Εφόσον έχεις middleware 'role:admin',
			return Auth::check();
		}

		/**
		 * Get the validation rules that apply to the request.
		 *
		 * @return array<string, ValidationRule|array|string>
		 */
		public function rules(): array {
			return [
				'user_id' => [
					'required',
					'exists:users,id',
					// Ελέγχουμε αν ο χρήστης είναι ήδη στην ομάδα για να αποφύγουμε duplicate entries
					Rule::unique('team_user', 'user_id')->where(function ($query) {
						return $query->where('team_id', $this->route('team')->id);
					}),
				],
				'role'    => [
					'nullable',
					'string',
					'in:member,leader,admin,viewer',
					Rule::in(TeamRole::cases())
				],
			];
		}

		/**
		 * Προσαρμοσμένα μηνύματα σφάλματος.
		 */
		public function messages(): array {
			return [
				'user_id.required' => 'Πρέπει να επιλέξετε έναν χρήστη.',
				'user_id.exists'   => 'Ο επιλεγμένος χρήστης δεν είναι έγκυρος.',
				'user_id.unique'   => 'Αυτός ο χρήστης ανήκει ήδη στην ομάδα.',
				'role.in'          => 'Ο επιλεγμένος ρόλος δεν είναι αποδεκτός.',
			];
		}
	}
