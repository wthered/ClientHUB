<?php

	namespace App\Http\Requests\Invoices;

	use Carbon\Carbon;
	use Illuminate\Foundation\Http\FormRequest;
	use Illuminate\Support\Facades\Auth;

	/**
	 * Class InvoiceUpdateRequest
	 * * Διαχειρίζεται την επικύρωση και τον υπολογισμό δεδομένων για την ενημέρωση τιμολογίων.
	 * Αναλαμβάνει αυτόματα τον υπολογισμό ΦΠΑ και συνόλων μετά την επιτυχή επικύρωση.
	 */
	class InvoiceUpdateRequest extends FormRequest {
		/**
		 * Ελέγχει αν ο χρήστης έχει δικαίωμα να εκτελέσει αυτή την ενέργεια.
		 */
		public function authorize(): bool {
			return Auth::check() && $this->user()->can('update invoices');
		}

		/**
		 * Κανόνες επικύρωσης.
		 */
		public function rules(): array {
			return [
				'account_id'          => [
					'required',
					'integer',
					'exists:accounts,id'
				],
				'opportunity_id'      => [
					'nullable',
					'integer',
					'exists:opportunities,id'
				],
				'status'              => [
					'required',
					'string'
				],
				'invoice_date'        => [
					'required',
					'date'
				],
				'due_date'            => [
					'required',
					'date',
					'after_or_equal:invoice_date'
				],
				'notes'               => [
					'nullable',
					'string',
					'max:2000'
				],
				'internal_notes'      => [
					'nullable',
					'string',
					'max:2000'
				],

				// Line Items Validation
				'items'               => [
					'required',
					'array',
					'min:1'
				],
				'items.*.product_id' => 'required|exists:products,id',
				'items.*.description' => [
					'required',
					'string',
					'max:255'
				],
				'items.*.unit_price' => 'required|numeric|min:0',
				'items.*.amount'      => [
					'required',
					'numeric',
					'min:0'
				],
				'items.*.quantity'    => 'required|numeric|min:1',

				// Υπολογιζόμενα πεδία (Placeholders για το validated())
				'net_amount'          => [
					'sometimes',
					'numeric'
				],
				'tax_amount'          => [
					'sometimes',
					'numeric'
				],
				'total_amount'        => [
					'sometimes',
					'numeric'
				],
			];
		}

		/**
		 * Ονόματα πεδίων για τα σφάλματα.
		 */
		public function attributes(): array {
			return [
				'account_id'          => __('invoices.attributes.account'),
				'invoice_date'        => __('invoices.attributes.invoice_date'),
				'due_date'            => __('invoices.attributes.due_date'),
				'items.*.description' => __('invoices.attributes.item_description'),
				'items.*.amount'      => __('invoices.attributes.item_amount'),
			];
		}

		/**
		 * Μηνύματα σφαλμάτων.
		 */
		public function messages(): array {
			return [
				'items.required'          => __('invoices.messages.items_required'),
				'due_date.after_or_equal' => __('invoices.messages.invalid_due_date'),
			];
		}

		/**
		 * Προετοιμασία δεδομένων πριν το Validation.
		 * Μετατρέπει τα IDs σε integers για να αποφευχθούν θέματα τύπων.
		 */
		protected function prepareForValidation(): void {
			$this->merge([
				'account_id'     => $this->filled('account_id') ? intval($this->input('account_id')) : null,
				'opportunity_id' => $this->filled('opportunity_id') ? intval($this->input('opportunity_id')) : null,
			]);
		}

		/**
		 * Ενέργειες μετά την επιτυχή επικύρωση.
		 * Υπολογίζει τα οικονομικά μεγέθη και φορμάρει τις ημερομηνίες για τη βάση.
		 */
		protected function passedValidation(): void {
			// 1. Παίρνουμε τα επικυρωμένα items (πλέον συμπεριλαμβάνεται και το quantity)
			$items = collect($this->validated('items'));

			// 2. Υπολογισμός Net Amount με στρογγυλοποίηση σε κάθε γραμμή
			// Χρησιμοποιούμε reduce για να είμαστε σίγουροι ότι ο server υπολογίζει
			// το σύνολο βάσει Qty * Unit Price και δεν βασίζεται απλώς στο input του χρήστη.
			$net = $items->reduce(function ($carry, $item) {
				$rowTotal = round($item['quantity'] * $item['unit_price'], 2);
				return $carry + $rowTotal;
			}, 0);

			// 3. Υπολογισμός ΦΠΑ και Τελικού Συνόλου
			$tax   = round($net * 0.24, 2);
			$total = round($net + $tax, 2);

			// 4. Προετοιμασία των δεδομένων προς συγχώνευση
			$extraData = [
				'net_amount'   => $net,
				'tax_amount'   => $tax,
				'total_amount' => $total,
				'invoice_date' => Carbon::parse($this->validated('invoice_date'))->format('Y-m-d'),
				'due_date'     => Carbon::parse($this->validated('due_date'))->format('Y-m-d'),
			];

			// 5. Ενημέρωση του request και του validator instance
			$this->merge($extraData);

			$validator = $this->getValidatorInstance();
			$validator->setData(array_merge($validator->getData(), $extraData));
		}
	}