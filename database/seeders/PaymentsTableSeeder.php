<?php

    namespace Database\Seeders;

    use App\Models\Invoices\Invoice;
    use App\Models\Payment;
    use Illuminate\Database\Seeder;

    class PaymentsTableSeeder extends Seeder {
	    public function run(): void {
		    // Παίρνουμε μόνο τα Invoices που δεν είναι 'draft' ή 'cancelled'
		    $invoices = Invoice::query()->whereNotIn('status', ['draft', 'cancelled'])->get();

		    if ($invoices->isEmpty()) {
			    $this->command->warn('⚠️ No active invoices found to attach payments.');
			    return;
		    }

		    $this->command->info('💰 Processing payments for invoices...');

		    foreach ($invoices as $invoice) {
			    // Πληρώνουμε το 70% των τιμολογίων που βρήκαμε (για ρεαλισμό)
			    if (rand(1, 100) <= 70) {

				    // Απόφαση: Πλήρης εξόφληση ή μερική;
				    $isFullPayment = rand(1, 100) <= 80; // 80% chance για πλήρη εξόφληση

				    $amount = $isFullPayment ? $invoice->total_amount : round($invoice->total_amount * (rand(30, 60) / 100), 2);

				    Payment::factory()->create([
					    'invoice_id'   => $invoice->id,
					    'amount'       => $amount,
					    'payment_date' => $invoice->invoice_date->addDays(rand(1, 15)),
				    ]);
			    }
		    }

		    $this->command->info('✅ Payments seeded and Invoice statuses updated!');
	    }
    }
