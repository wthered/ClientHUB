<?php

	namespace Database\Factories;

	use App\Enums\InvoiceStatus;
	use App\Models\Invoices\Invoice;
	use App\Models\Payment;
	use Illuminate\Database\Eloquent\Factories\Factory;

	/**
	 * @extends Factory<Payment>
	 */
	class PaymentFactory extends Factory {
		protected $model = Payment::class;

		public function definition(): array {
			return [
				'invoice_id'   => Invoice::factory(),
				'amount'       => 0,
				'currency'     => 'EUR',
				'method'       => $this->faker->randomElement([
					'bank transfer',
					'credit card',
					'cash',
					'stripe'
				]),
				'reference_id' => 'REF-' . strtoupper($this->faker->bothify('??###')),
				'payment_date' => today()->subDays(mt_rand(1, 30)),
				'notes'        => $this->faker->optional()->sentence(),
			];
		}

		/**
		 * Ενημερώνει το Invoice μετά την πληρωμή
		 */
		public function configure(): static {
			return $this->afterCreating(function (Payment $payment) {
				// Χρησιμοποιούμε τη μέθοδο invoice() της σχέσης για να είμαστε σίγουροι
				$invoice = $payment->invoice()->first();

				// ΕΛΕΓΧΟΣ ΑΣΦΑΛΕΙΑΣ: Αν δεν υπάρχει invoice, σταμάτα εδώ
				if (!$invoice) {
					return;
				}

				// Υπολογίζουμε το νέο συνολικό πληρωμένο ποσό
				$totalPaid = $invoice->payments()->sum('amount');

				$status = InvoiceStatus::UNPAID->value;
				if ($totalPaid >= $invoice->total_amount) {
					$status = InvoiceStatus::PAID->value;
				} elseif ($totalPaid > 0) {
					$status = InvoiceStatus::PARTIAL->value;
				}

				$invoice->update(['status' => $status]);
			});
		}
	}
