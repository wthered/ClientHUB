<?php

	namespace Database\Factories\Invoices;

	use App\Enums\InvoiceStatus;
	use App\Models\Account;
	use App\Models\Invoices\Invoice;
	use App\Models\Opportunities\Opportunity;
	use Carbon\Carbon;
	use Illuminate\Database\Eloquent\Factories\Factory;

	/**
	 * @extends Factory<Invoice>
	 */
	class InvoiceFactory extends Factory {
		protected $model = Invoice::class;

		public function definition(): array {
			$invoiceDate = Carbon::parse($this->faker->dateTimeBetween('1st of January last year', 'yesterday'));
			$totalAmount = $this->faker->randomFloat(2, 100, 15000);
			$taxRate     = 0.24;
			$netAmount   = round($totalAmount / (1 + $taxRate), 2);

			// Τυχαία επιλογή status
			$status = $this->faker->randomElement(InvoiceStatus::cases());

			// Δυναμικός υπολογισμός πληρωμένου ποσού βάσει status
			$paidAmount = match($status) {
				InvoiceStatus::PAID    => $totalAmount,
				InvoiceStatus::PARTIAL => round($totalAmount * rand(10, 66) / 100, 2),
				default                => 0, // Unpaid, Draft, Sent, Overdue, Canceled
			};

			return [
				'account_id'     => Account::factory(),
				'opportunity_id' => Opportunity::factory(),
				'invoice_number' => 'INV-' . $invoiceDate->format('Y') . '-' . $this->faker->unique()->numberBetween(1000, 9999),
				'net_amount'     => $netAmount,
				'tax_amount'     => round($totalAmount - $netAmount, 2),
				'total_amount'   => $totalAmount,
				'paid_amount'    => $paidAmount,
				'currency'       => 'EUR',
				'invoice_date'   => $invoiceDate->toDateString(),
				'due_date'       => $invoiceDate->copy()->modify('+' . mt_rand(16, 48) . ' days'),
				'status'         => $status->value,
				'notes'          => $this->faker->optional()->sentence(),
				'internal_notes' => $this->faker->optional()->sentence(),
			];
		}

		// State για εξοφλημένο
		public function paid(): static {
			return $this->state(fn(array $attributes) => [
				'status'      => InvoiceStatus::PAID->value,
				'paid_amount' => $attributes['total_amount'],
			]);
		}

		// State για ληξιπρόθεσμο (Overdue)
		public function overdue(): static {
			return $this->state(fn(array $attributes) => [
				'status'      => InvoiceStatus::OVERDUE->value,
				'paid_amount' => 0,
				'due_date'    => now()->subDays(rand(1, 30)),
			]);
		}

		// State για μερικώς πληρωμένο
		public function partial(): static {
			return $this->state(fn(array $attributes) => [
				'status'      => InvoiceStatus::PARTIAL->value, // Επιβολή του σωστού Enum
				'paid_amount' => round($attributes['total_amount'] * rand(10, 50) / 100, 2),
			]);
		}
	}