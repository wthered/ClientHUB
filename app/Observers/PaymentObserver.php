<?php

	namespace App\Observers;

	use App\Enums\InvoiceStatus;
	use App\Models\Invoices\Invoice;
	use App\Models\Payment;
	use Illuminate\Support\Str;

	class PaymentObserver {
		/**
		 * Πριν δημιουργηθεί η πληρωμή, παράγουμε το reference
		 */
		public function creating(Payment $payment): void {
			if (empty($payment->reference_id)) {
				$payment->reference_id = 'REF-' . strtoupper(Str::random(2)) . rand(100, 999);
			}
		}

		/**
		 * Μετά τη δημιουργία, ενημερώνουμε το status
		 */
		public function created(Payment $payment): void {
			$this->updateInvoiceStatus($payment->invoice);
		}

		/**
		 * Κεντρικό logic για το status του τιμολογίου
		 */
		protected function updateInvoiceStatus(Invoice $invoice): void {
			$totalPaid = $invoice->payments()->sum('amount');

			if ($totalPaid >= $invoice->total_amount) {
				$invoice->update(['status' => InvoiceStatus::PAID]);
			} elseif ($totalPaid > 0) {
				$invoice->update(['status' => InvoiceStatus::PARTIAL]);
			} else {
				$invoice->update(['status' => InvoiceStatus::UNPAID]);
			}
		}

		/**
		 * Μετά τη διαγραφή, ξανα-ενημερώνουμε το status
		 */
		public function deleted(Payment $payment): void {
			// Χρησιμοποιούμε optional() ή null check αν το invoice έχει ήδη διαγραφεί (soft deletes κλπ)
			if ($payment->invoice) {
				$this->updateInvoiceStatus($payment->invoice);
			}
		}
	}
