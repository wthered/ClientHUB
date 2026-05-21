<?php

	namespace Database\Seeders;

	use App\Models\Invoices\Invoice;
	use App\Models\Invoices\InvoiceItem;
	use Illuminate\Database\Seeder;

	class InvoiceItemsTableSeeder extends Seeder {
		public function run(): void {
			$invoices = Invoice::all();

			foreach ($invoices as $invoice) {
				// 1. Δημιουργούμε 4-8 γραμμές χρησιμοποιώντας το Factory για το InvoiceItem
				$items = InvoiceItem::factory()->count(mt_rand(4, 8))->create([
					'invoice_id' => $invoice->id,
				]);

				// 2. ΕΝΗΜΕΡΩΝΟΥΜΕ το Invoice για να συμφωνεί με τις γραμμές που μόλις φτιάξαμε
				$netAmount = $items->sum('amount');
				$taxAmount = $netAmount * 0.24;

				$invoice->update([
					'net_amount'   => $netAmount,
					'tax_amount'   => $taxAmount,
					'total_amount' => $netAmount + $taxAmount,
				]);
			}
		}
	}
