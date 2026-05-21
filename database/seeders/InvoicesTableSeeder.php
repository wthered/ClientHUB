<?php

	namespace Database\Seeders;

	use App\Enums\InvoiceStatus;
	use App\Models\Account;
	use App\Models\Invoices\Invoice;
	use App\Models\Opportunities\Opportunity;
	use Illuminate\Database\Seeder;

	class InvoicesTableSeeder extends Seeder {
		public function run(): void {
			$accounts = Account::all();
			$opportunities = Opportunity::all();

			if ($accounts->isEmpty()) {
				$this->command->error('❌ No accounts found. Run AccountsTableSeeder first.');
				return;
			}

			// 1. Δημιουργία τυχαίων τιμολογίων ανά Account
			foreach ($accounts as $account) {
				$accountOpportunities = $opportunities->where('account_id', $account->id);

				Invoice::factory()->count(rand(1, 4))->recycle($account)->create([
					'opportunity_id' => $accountOpportunities->isNotEmpty() ? $accountOpportunities->random()->id : null,
				]);
			}

			// 2. Δημιουργία των 32 Overdue τιμολογίων
			Invoice::factory()->count(32)->recycle($accounts)->recycle($opportunities)->overdue()->create();

			// 3. Δημιουργία των 64 Partial τιμολογίων
			Invoice::factory()->count(64)->recycle($accounts)->recycle($opportunities)->partial()->create();

			// --- ΣΤΑΤΙΣΤΙΚΑ ---
			$totalCount = Invoice::query()->count();
			$totalAmount = Invoice::query()->sum('total_amount');
			$totalPaid = Invoice::query()->sum('paid_amount');
			$totalPending = $totalAmount - $totalPaid;

			// Status Counts
			$paidCount = Invoice::query()->where('status', InvoiceStatus::PAID->value)->count();
			$partialCount = Invoice::query()->where('status', InvoiceStatus::PARTIAL->value)->count();
			$overdueCount = Invoice::query()->where('status', InvoiceStatus::OVERDUE->value)->count();

			$this->command->info("");
			$this->command->info("   ┌─────────────────────────────────────────────────┐");
			$this->command->info(sprintf("   │ %-35s %10d │", "Total Invoices Created:", $totalCount));
			$this->command->info(sprintf("   │ %-35s %10s │", "Total Invoiced Value:", number_format($totalAmount, 2) . "€"));
			$this->command->info("   ├─────────────────────────────────────────────────┤");
			$this->command->info(sprintf("   │ %-35s %10d │", "Paid (Full):", $paidCount));
			$this->command->info(sprintf("   │ %-35s %10d │", "Partially Paid:", $partialCount));
			$this->command->info(sprintf("   │ %-35s %10d │", "Overdue:", $overdueCount));
			$this->command->info("   ├─────────────────────────────────────────────────┤");
			$this->command->info(sprintf("   │ %-35s %10s │", "Collected Revenue:", number_format($totalPaid, 2) . "€"));
			$this->command->info(sprintf("   │ %-35s %10s │", "Outstanding Balance:", number_format($totalPending, 2) . "€"));
			$this->command->info("   └─────────────────────────────────────────────────┘");
			$this->command->info('✅ Invoices seeded successfully!');
		}
	}
