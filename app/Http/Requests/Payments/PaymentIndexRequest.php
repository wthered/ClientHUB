<?php

	namespace App\Http\Requests\Payments;

	use Carbon\Carbon;
	use Illuminate\Foundation\Http\FormRequest;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Support\Str;
	use Illuminate\Validation\Rule;

	class PaymentIndexRequest extends FormRequest {
		public function authorize(): bool {
			return Auth::check() && $this->user()->can('view-payments');
		}

		public function rules(): array {
			return [
				'invoice_id' => [
					'nullable',
					'integer',
					Rule::exists('invoices', 'id')
				],
				'date_from'  => [
					'nullable',
					'date'
				],
				'date_to'    => [
					'nullable',
					'date',
					'after_or_equal:date_from'
				],
				'method'     => [
					'nullable',
					'string',
					Rule::in([
						'cash',
						'bank transfer',
						'stripe',
						'credit card'
					])
				],
			];
		}

		public function messages(): array {
			return [
				'invoice_id.exists' => 'Το παραστατικό δεν βρέθηκε.',
				'date_to.after_or_equal' => 'Η τελική ημερομηνία πρέπει να είναι ίδια ή μετά την αρχική.',
				'method.in' => 'Η μέθοδος πληρωμής δεν είναι έγκυρη.',
			];
		}

		protected function prepareForValidation(): void {
			// Χρησιμοποιούμε το trim για να αποφύγουμε κενά και Str::lower για το validation rule
			$this->merge([
				'invoice_id' => $this->filled('invoice_id') ? (int) $this->input('invoice_id') : null,
				'method'     => $this->filled('method') ? Str::lower(trim($this->input('method'))) : null,
			]);
		}

		/**
		 * Αντί για merge στην passedValidation, μπορούμε να προετοιμάσουμε
		 * τα δεδομένα ώστε ο Controller να τα πάρει έτοιμα.
		 */
		protected function passedValidation(): void {
			// Μετατροπή των ημερομηνιών σε standard Y-m-d format για το query
			// Χρησιμοποιούμε το offsetSet για να ενημερώσουμε το validated data array
			if ($this->filled('date_from')) {
				$this->merge(['date_from' => Carbon::parse($this->validated('date_from'))->format('Y-m-d')
				]);
			}

			if ($this->filled('date_to')) {
				$this->merge(['date_to' => Carbon::parse($this->validated('date_to'))->format('Y-m-d')
				]);
			}
		}
	}