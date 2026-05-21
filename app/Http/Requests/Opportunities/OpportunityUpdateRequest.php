<?php

	namespace App\Http\Requests\Opportunities;

	use App\Models\Opportunities\Stage;
	use Illuminate\Foundation\Http\FormRequest;
	use Illuminate\Support\Str;

	class OpportunityUpdateRequest extends FormRequest {
		/**
		 * Determine if the user is authorized to make this request.
		 */
		public function authorize(): bool {
			// Αν το route σου είναι π.χ. Route::put('/opportunities/{opportunity}', ...)
			return $this->user()->can('update', $this->route('opportunity'));
		}

		/**
		 * Get the validation rules that apply to the request.
		 */
		public function rules(): array {
			// Φέρνουμε όλα τα IDs των σταδίων που έχουν status 'lost'
			$lostStageIds = Stage::query()->where('status', 'lost')->pluck('id')->toArray();

			return [
				'name'         => ['required', 'string', 'max:255'],
				'account_id'   => ['required', 'exists:accounts,id'],
				'contact_id'   => ['nullable', 'exists:contacts,id'],
				'stage_id'     => ['required', 'exists:stages,id'],
				'owner_id'     => ['required', 'exists:users,id'],
				'amount'       => ['nullable', 'numeric', 'min:0'],
				'currency'     => ['required', 'string', 'size:3'],
				'probability'  => ['required', 'integer', 'min:0', 'max:100'],
				'close_date'   => ['nullable', 'date'],
				'notes'        => ['nullable', 'string', 'max:1000'],

				// Τώρα το rule είναι δυναμικό!
				'loss_reason'  => ["required_if:stage_id,".implode(',', $lostStageIds), 'nullable', 'string', 'max:255'],

				'tags'         => ['nullable', 'array'],
				'tags.*'       => ['exists:tags,id'],
			];
		}

		/**
		 * Get custom attributes for validator errors.
		 * Με αυτό γλιτώνεις το γράψιμο πολλών μηνυμάτων στο messages().
		 */
		public function attributes(): array {
			return [
				'name'         => __('opportunities.deal_name'),
				'account_id'   => __('opportunities.account_id'),
				'contact_id'   => __('opportunities.contact_id'),
				'stage_id'     => __('opportunities.stage_id'),
				'amount'       => __('opportunities.amount'),
				'currency'     => __('opportunities.currency'),
				'probability'  => __('opportunities.probability'),
				'close_date'   => __('opportunities.close_date'),
				'tags'         => __('opportunities.tags'),
			];
		}

		/**
		 * Προσαρμοσμένα μηνύματα σφάλματος.
		 */
		public function messages(): array {
			return [
				'required' => 'Το πεδίο :attribute είναι υποχρεωτικό.',
				'exists'   => 'Η επιλογή στο πεδίο :attribute δεν είναι έγκυρη.',
				'date'     => 'Η :attribute δεν είναι έγκυρη ημερομηνία.',
			];
		}

		/**
		 * Prepare the data for validation.
		 * Χρήσιμο για το "πρώτο πέρασμα" ώστε να αποφύγουμε σφάλματα τύπου.
		 */
		protected function prepareForValidation(): void {
			$this->merge([
				// Αν το amount έρχεται ως string με κόμμα (π.χ. 1.250,50), το μετατρέπουμε σε numeric
				'amount' => $this->input('amount') ? (float) Str::replace(['.', ','], ['', '.'], $this->input('amount')) : null,

				// Διασφαλίζουμε ότι αν δεν επιλεγούν tags, θα έχουμε ένα άδειο array αντί για null
				'tags' => $this->input('tags') ?? [],

				// Καθαρίζουμε τυχόν κενά γύρω από το όνομα
				'name' => trim($this->input('name')),
			]);
		}
	}