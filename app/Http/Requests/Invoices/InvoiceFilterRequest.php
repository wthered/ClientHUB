<?php

	namespace App\Http\Requests\Invoices;

	use Auth;
	use Illuminate\Foundation\Http\FormRequest;

	class InvoiceFilterRequest extends FormRequest {
		/**
		 * Determine if the user is authorized to make this request.
		 */
		public function authorize(): bool {
			return Auth::check() && $this->user()->can('view invoices');
		}

		/**
		 * Get the validation rules that apply to the request.
		 */
		public function rules(): array {
			return [
				'search'    => [
					'nullable',
					'string',
					'max:100'
				],
				'status'    => [
					'nullable',
					'string',
					'in:draft,sent,unpaid,paid,overdue,cancelled'
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
		 * Get custom attributes for validator errors.
		 */
		public function attributes(): array {
			return [
				'search'    => 'Αναζήτηση',
				'status'    => 'Κατάσταση',
				'date_from' => 'Από ημερομηνία',
				'date_to'   => 'Έως ημερομηνία',
			];
		}

		/**
		 * Handle a passed validation attempt.
		 * Σκοπός: Καθαρισμός των δεδομένων (π.χ. trim) ή μετατροπή format πριν φτάσουν στον Controller.
		 */
		protected function passedValidation(): void {
			$this->merge([
				'search' => $this->filled('search') ? trim($this->validated('search')) : null,
			]);
		}
	}
